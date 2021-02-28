<?php
/**
 * Коллекция методов в модулях
 *
 * @author olga
 */
namespace Models;

class Action {
    const TABLE = 'actions';
    /** @var Action */
    private static $instance = null;
    private static $load_fields = array('id', 'module_class', 'module_url', 'action', 'title', 'admin');
    /**
     *
     * @return Action
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new Action();
        }
        return self::$instance;
    }
    /**
     * Поиск конкретных методов
     * @param array $params
     *  Возможный значение:
     *      order => порядок вывода
     *      moduleUrl => урл модуля
     *      action => название метода
     * @return array
     * @throws \LogicException
     */
    public function search($params = array()){
        $db = \App\Builder::getInstance()->getDB();
        $order_part = '';
        if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $key => $desc){
                    $order[] = $key . (!empty($desc) ? ' DESC ' : ' ');
                }
                $order_part = implode(', ', $order);
            }else{
                throw new \LogicException('Order param must be an array("key" => "desc")');
            }
        }
        $actions = $db->query('
            SELECT `'.implode('`, `', self::$load_fields).'` 
            FROM `'.self::TABLE.'` 
            WHERE 1{ AND `module_url` = ?s}{ AND `action` = ?s}{ AND `admin` = ?d}
            ' . (!empty($order_part) ? ('ORDER BY ' . $order_part) : '') . '
        ', !empty($params['moduleUrl']) ? $params['moduleUrl'] : $db->skipIt(),
            !empty($params['action']) ? $params['action'] : $db->skipIt(),
            (isset($params['admin']) && in_array($params['admin'], array(0, 1))) ? $params['admin'] : $db->skipIt()
        )->select('id');
        return $actions;
    }

    /**
     * Регистрация новых методов
     * @param \LPS\Module|string $module при вызове из \App\Stages передается объект модуля, при импорте прав доступа из админки - имя класса
     * @param string $module_url
     * @param string $action
     * @param int|null $admin при вызове из Stages игнорируется, при импорте из админки нужно указывать значение 1/0
     * @param string $title
     * @throws \Exception
     * @throws \MysqlSimple\Exceptions\InvalidArgumentException
     * @return boolean
     */
    public function registrate($module, $module_url, $action, $admin = NULL, $title = NULL){
        if (in_array($action, array('deny', 'notFound', 'underConstruction'))){
            return false;
        }
        if (is_string($module) && !in_array($admin, array(1, 0))){
            throw new \Exception('Не указан тип модуля (AdminModule => 1, WebModule => 0)');
        }
        $module_class = is_string($module) ? $module : $module->getName();
        $db = \App\Builder::getInstance()->getDB();
        $module_url = trim($module_url, '/');
        $exists = $db->query('SELECT `id` FROM `'.self::TABLE.'` WHERE `module_url` = ?s AND `action` = ?s', $module_url, $action)->getCell();
        if (empty($exists)){
            return $db->query('INSERT INTO `'.self::TABLE.'` SET `module_class` = ?s, `module_url` = ?s, `action` = ?s, `admin` = ?d, `title` = ?',
                $module_class,
                $module_url,
                $action,
                is_string($module) ? $admin : $module instanceof \LPS\AdminModule ? 1 : 0,
                $title);
        } else {
            return $exists;
        }
    }
}

?>
