<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 26.09.14
 * Time: 20:34
 */

namespace Models\SphinxManagement;


use App\Configs\SphinxConfig;
use Models\CronTask;
use Models\SiteConfigManager;
use MysqlSimple\Logger;

class SphinxSearch {
    const TABLE = 'sphinx_index_last_update';
    const NEED_RECREATE_INDEX_CONF = 'sphinx_need_recreate';
    const DELTA_SUFFIX = '_delta';
    const DEV_SUFFIX = '_dev';
    private static $i = array();

    private $config = NULL;
    private $sphinx = NULL;
    private $db = NULL;
    private $segment_id = NULL;
    private $type_ids = array();
    private $key = NULL;
    private $group = NULL;
    private $offset = NULL;
    private $limit = NULL;

    private $sphinx_api_client = null;

    private $weights = NULL;


    /**
     * замена ключа для дева
     * @param string $key
     * @param $config
     * @return string|NULL
     */
    public static function checkKey($key, &$config = NULL){
        $config = SphinxConfig::getIndexConfig($key);
        if ($config){
            return (\LPS\Config::isDev()) ? $key.self::DEV_SUFFIX : $key;
        }
        return NULL;
    }

    public function needRebuild(){
        return \Models\TechnicalConfig::getInstance()->get(self::NEED_RECREATE_INDEX_CONF . '_' . $this->key);
    }

    /**
     * @param $key
     * @param null $segment_id
     * @return SphinxSearch
     */
    public static function factory($key, $segment_id = NULL){
        $key = self::checkKey($key, $config);
        if (empty($key)){
            return NULL;
        }
        if (empty(self::$i[$key])){
            self::$i[$key] = new self($key, $config, $segment_id);
        }
        return self::$i[$key];
    }

    /**
     * Устанавливает веса для ранкера
     * @param array $fields array(<field> => <weigth>,....)
     * @return $this
     */
    public function setWeight($fields){
        $weights = '';
        foreach($fields as $k => $v){
            $weights .= (!empty($weights) ? ', ' : '') . $k.'='.$v;
        }
        $this->weights = $weights;
        return $this;
    }

    private function __construct($key, $config, $segment_id){
        $this->sphinx = \App\Builder::getInstance()->getSphinx();
        $this->db = \App\Builder::getInstance()->getDB();
        $this->key = $key;
        $this->config = $config;
        $this->segment_id = $segment_id;
    }

    /**
     * @param int[] $ids
     * @return $this
     */
    public function setTypeIds($ids){
        $this->type_ids = is_array($ids) ? $ids : array($ids);
        return $this;
    }

    public function forceRecreateIndex(){
        \Models\TechnicalConfig::getInstance()->set(self::NEED_RECREATE_INDEX_CONF . '_' . $this->key, 'sphinx', 1, 'требуется перегенерация sphinx-индекса "' . $this->key . '"');
    }

    /**
     * Обновление дельта-индекса
     * @return $this
     */
    public function updateDeltaIndex(){
        if (isset($this->config['has_delta_index']) && !$this->config['has_delta_index']){
            return $this; // если не используется дельта-индекс ничего не делаем
        }
        $wordforms_builder = WordForms::getInstance();
        if ($wordforms_builder->isChanged() || $this->needRebuild()){
            // обновляем словарь синонимов
            $this->rebuildIndex();
        }
        exec('sudo indexer --rotate ' . $this->key . self::DELTA_SUFFIX);
        return $this;
    }

    /**
     * Слияние дельта-индекса с основным
     * @return $this
     */
    public function mergeDeltaIndex(){
        $wordforms_builder = WordForms::getInstance();
        if ($wordforms_builder->isChanged() || $this->needRebuild()){
            $this->rebuildIndex();
        } elseif (isset($this->config['has_delta_index']) && !$this->config['has_delta_index']){
            // если не используется дельта-индекс и для индекса не вызвано обновление ничего не делаем
            return $this;
        } else {
            exec('sudo indexer --rotate --merge ' . $this->key . ' ' . $this->key . self::DELTA_SUFFIX, $output);
        }
        $this->db->query('REPLACE INTO `' . self::TABLE . '` SET `last_update` = NOW(), `index_name` = ?s', $this->key); // Корректируем исходную дату дельта-индекса
//        var_dump($output);
        return $this;
    }

    private function rebuildIndex(){
        $task_id = CronTask::add(array(
                'type' => \App\Configs\CronTaskConfig::TASK_REBUILD_SPHINX_INDEX,
                'status' => CronTask::STATUS_NEW,
                'time_create' => date('Y-m-d H:i:s'),
                'data' => array(
                    'index_key' => $this->key
                )
            )
        );
        $task = CronTask::getById($task_id);
        // обновляем словарь синонимов
        WordForms::getInstance()->generateList();
        $task->update(array(
            'time_start' => date('Y-m-d H:i:s'),
            'status' => CronTask::STATUS_PROCESS,
            'percent' => 0
        ));
        exec('sudo indexer --rotate ' . $this->key); // @TODO сделать парсинг ошибок
        \Models\TechnicalConfig::getInstance()->set(self::NEED_RECREATE_INDEX_CONF, 'sphinx', 0, 'требуется перегенерация индекса sphinx');
        $this->db->query('REPLACE INTO `' . self::TABLE . '` SET `last_update` = NOW(), `index_name` = ?s', $this->key);
        $task->update(array(
            'status' => CronTask::STATUS_COMPLETE,
            'percent' => 100,
            'time_end' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Выбор столбца для группировки
     * @param string|NULL $field_name имя столбца или NULL для отсутствия группировки
     * @return $this
     */
    public function setGroup($field_name){
        $this->group = $field_name;
        return $this;
    }

    /**
     * @param int|NULL $offset
     * @param int|NULL $limit
     * @return $this
     */
    public function setLimit($offset, $limit){
        $this->offset = $offset >= 0 ? $offset : NULL;
        $this->limit = $limit > 0 ? $limit : NULL;
        return $this;
    }

    /**
     * @param string $fields
     * @param string $search_string
     * @return FALSE|int|\MysqlSimple\Result
     */
    public function select($fields, $search_string){
//        $this->sphinx->setLogger(Logger::factory());
        return $this->sphinx->query('SELECT ' . $fields . ' FROM `' . ((isset($this->config['has_delta_index']) && !$this->config['has_delta_index']) ? '' : $this->key.self::DELTA_SUFFIX . '`, `') . $this->key . '` WHERE MATCH(' . '\'' . $this->EscapeSphinxQL($search_string) . '\'' . ')'
            . '{ AND `segment_id` = ?d}'
            . '{ AND `type_id` IN (?i)}'
            . (!empty($this->group) ? ' GROUP BY `' . $this->group . '`' : '')
            . '{ LIMIT ?d, ?d}'
            . (!empty($this->weights) ? ' OPTION field_weights=('.$this->weights.')' : (!empty($this->config['field_weights']) ? ' OPTION field_weights=(' . $this->config['field_weights'] . ')' : '')),
            (!empty($this->segment_id) && SphinxConfig::ENABLE_SEGMENTS) ? $this->segment_id : $this->sphinx->skipIt(),
            !empty($this->type_ids) ? $this->type_ids : $this->sphinx->skipIt(),
            !empty($this->offset) ? $this->offset : 0,
            !empty($this->limit) ? $this->limit : $this->sphinx->skipIt()
        );
    }
    private function EscapeSphinxQL ( $string )
    {
        $from = array ( '\\', '(',')','|','-','!','@','~','"','&', '/', '^', '$', '=', "'", "\x00", "\n", "\r", "\x1a" );
        $to   = array ( '\\\\\\\\', '\\\(','\\\)','\\\|','\\\-','\\\!','\\\@','\\\~','\\\"', '\\\&', '\\\/', '\\\^', '\\\$', '\\\=', "\\'", "\\x00", "\\n", "\\r", "\\x1a" );
        return str_replace ( $from, $to, $string );
    }

    private function generateStopList(){
        if (!empty($this->config['enable_stop_words'])){
            exec('sudo indexer ' . $this->key . ' --buildstops ' . \LPS\Config::getRealDocumentRoot().'/data/stoplist_' . $this->key . '.txt ' . (!empty($this->config['stop_words_count']) ? $this->config['stop_words_count'] : SphinxConfig::DEFAULT_STOP_LIST_SIZE));
        }
    }

    /**
     * @return \SphinxClient
     */
    private function getApiClient(){
        if (empty($this->sphinx_api_client)){
            $this->sphinx_api_client = new \SphinxClient();
            $this->sphinx_api_client->SetServer('localhost', 9312);
        }
        return $this->sphinx_api_client;
    }

    /**
     * Возвращает совпадающие блоки поиска
     * @param array $docs
     * @param string $search_string
     * @return array|bool
     */
    public function getMatchBlocks($docs, $search_string, &$error = NULL, $opts = NULL){
        $sphinx = $this->getApiClient();
        $result = $sphinx->BuildExcerpts($docs, $this->key, $search_string, SphinxConfig::getOpts(SphinxConfig::SEO_MATCH_BLOCK_OPTS));
        if ($result === FALSE) {
            $error = $sphinx->GetLastError();
            return FALSE;
        } else {
            $ids = array_keys($docs);
            return array_combine($ids, $result);
        }
    }

    public function buildKeywords($search){
        $sphinx = $this->getApiClient();
        var_dump($sphinx->BuildKeywords($search, 'santech_catalog', true));
//        $sphinx->
    }
} 