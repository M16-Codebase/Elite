<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 16.10.14
 * Time: 16:08
 */

namespace Models\DatabaseManagement;


use MysqlSimple\Exceptions\MySQLQueryException;

class Migrations {
    const TABLE = 'database_migrations';
    const MIGRATIONS_DIR = 'migrations';

    private static $i = NULL;
    private $db = NULL;
    /**
     * Список примененных миграций
     * @var array
     */
    private $loaded_migrations = array();
    /**
     * Список всех миграций со статусами - array(array('name' => <file_name>, 'loaded' => bool), ...)
     * @var array
     */
    private $migrations_list = array();
    /**
     * Список непримененных миграций
     * @var array
     */
    private $new_migrations = array();
	/**
	 * 
	 * @return static
	 */
    public static function getInstance(){
        if (empty(self::$i)){
            self::$i = new self;
        }
        return self::$i;
    }

    private function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
    }

    /**
     * Загружаем списки миграций (примененные, все, новые)
     * @param bool $force_refresh принудительное обновление
     */
    private function loadMigrationsData($force_refresh = FALSE){
        if ($force_refresh || empty($this->loaded_migrations)){
            $this->loaded_migrations = $this->db->query('SELECT `name` FROM `' . self::TABLE . '` ORDER BY `name` DESC')->getCol('name', 'name');
            $dirname = \LPS\Config::getRealDocumentRoot() . self::MIGRATIONS_DIR;
            if (file_exists($dirname)){
                $file_list = scandir(\LPS\Config::getRealDocumentRoot() . self::MIGRATIONS_DIR);//, SCANDIR_SORT_DESCENDING); sorting_order в php >= 5.4
            } else {
                $file_list = array();
            }
            $this->migrations_list = array();
            $this->new_migrations = array();
            if (!empty($file_list)){
                foreach($file_list as $file_name){
                    if (substr($file_name, -4) == '.sql'){
                        $loaded = isset($this->loaded_migrations[$file_name]);
                        $this->migrations_list[$file_name] = array(
                            'name' => $file_name,
                            'loaded' => $loaded
                        );
                        if (!$loaded){
                            $this->new_migrations[$file_name] = $file_name;
                        }
                    }
                }
                // Список для показа в админке сортируем в обратном хронологическом порядке
                krsort($this->migrations_list);
                // Список новых миграций в прямом, т.к. миграции должны применяться в том порядке, в котором создавались
                ksort($this->new_migrations);
            }

        }
    }

    /**
     * Возвращает список примененных миграций
     * @return array
     */
    private function getLoadedMigrations(){
        if (empty($this->loaded_migrations)){
            $this->loaded_migrations = $this->db->query('SELECT `name` FROM `' . self::TABLE . '` ORDER BY `name` DESC')->getCol('name', 'name');
        }
        return $this->loaded_migrations;
    }

    public function getMigrationsList(){
        $this->loadMigrationsData();
        return $this->migrations_list;
    }

    /**
     * Создание новой миграции
     * @param string $sql
     * @param string $user_id
     * @return bool
     */
    public function addMigration($sql, $user_id){
        if (empty($sql)){
            return FALSE;
        }
        if (!file_exists(\LPS\Config::getRealDocumentRoot() . self::MIGRATIONS_DIR)){
            mkdir(\LPS\Config::getRealDocumentRoot() . self::MIGRATIONS_DIR, 0755);
        }
        // file name format - YYYYMMDDHHMMSS_userEmail.sql
        $file_name = date('YmdHis') . '_' . $user_id . '.sql';
        $fp = fopen(\LPS\Config::getRealDocumentRoot() . self::MIGRATIONS_DIR . '/' . $file_name, 'w');
        fwrite($fp, $sql);
        fclose($fp);
        $this->db->query('INSERT IGNORE `' . self::TABLE . '` SET `name` = ?s', $file_name);
        $this->loaded_migrations = array();
        return TRUE;
    }

    /**
     * Применяет новые миграции
     * @param bool $show_report выводить прогресс выполнения
     * @return int количество примененных миграций
     */
    public function applyMigrations($show_report = FALSE){
        $this->loadMigrationsData(TRUE);
        $applied_migrations_count = 0;
        if (!empty($this->new_migrations)){
            $failed_migrations_count = 0;
            foreach($this->new_migrations as $migration_file_name){
                if ($this->db->multi_query(file_get_contents(\LPS\Config::getRealDocumentRoot() . self::MIGRATIONS_DIR . '/' . $migration_file_name))){
                    $this->db->query('INSERT IGNORE `' . self::TABLE . '` SET `name` = ?s', $migration_file_name);
                    $applied_migrations_count ++;
                    if ($show_report){
                        echo 'Applying migration ' . $migration_file_name . PHP_EOL;
                    }
                } else {
                    $failed_migrations_count ++;
                    if ($show_report){
                        echo 'Error in migration ' . $migration_file_name . PHP_EOL;
                    }
                }
            }
            $this->loaded_migrations = array();
            if ($show_report){
                echo 'Migrations applied: ' . $applied_migrations_count . ' of ' . count($this->new_migrations) . ($failed_migrations_count ? '. Failed migrations count: ' . $failed_migrations_count : '') . PHP_EOL;
            }
        }
        return $applied_migrations_count;
    }

    public function setDataFromFiles(){
        echo 'Загрузка структуры базы данных' . "\n";
        $structure_file = \LPS\Config::getRealDocumentRoot() . 'structure.sql';
        if (!file_exists($structure_file)){
            echo 'Файл структуры БД не найден';
            return;
        }
        $this->db->multi_query(file_get_contents($structure_file));
        echo 'Загрузка начальных данных в базу данных' . "\n";
        $base_data_file = \LPS\Config::getRealDocumentRoot() . 'base_data.sql';
        if (!file_exists($base_data_file)){
            echo 'Файл начальных данных БД не найден' . "\n";
            return;
        }
        $this->db->multi_query(file_get_contents($base_data_file));
    }

} 