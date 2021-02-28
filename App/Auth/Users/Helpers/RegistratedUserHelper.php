<?php
namespace App\Auth\Users\Helpers;
use App\Auth\Users\RegistratedUser;
/**
 * Description of RegistratedUserHelper
 *
 * @author mac-proger
 */
abstract class RegistratedUserHelper implements iRegistratedUserHelper{
    protected static $fields_list = array();
    protected static $i = NULL;
    
    public static function factory(){
        if (empty(static::$i)){
            static::$i = new static();
        }
        return static::$i;
    }
    
    protected function __construct() {
        RegistratedUser::addDataProvider($this);
    }
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList(){
        return static::$fields_list;
    }
    /**
     * возвращает значение дополнительного поля
     */
    public function get(RegistratedUser $user, $field){}
    /**
     * предупреждение, что данные для указанных Users попали в кеш данных
     */
    public function onLoad(RegistratedUser $user){}

    public function prepare(array $ids){}

    public function preCreate(&$params, &$errors, $hash){}
    /**
     * событие на создание нового User
     */
    public function onCreate($id, $hash){}
     /**
     * событие после изменения User
     * @param \App\Auth\Users\RegistratedUser $user
     * @param array $old_data
     * @param int $segment_id
     */
    public function onUpdate(RegistratedUser $user, $old_data, $segment_id){}
     /**
      * событие перед изменением
      * @return bool
      */
    public function preUpdate(RegistratedUser $user, &$params, $segment_id, &$errors){return FALSE;}
    /**
     * событие на удаление user
     * @param int $user_id
     */
    public function onDelete($user_id){}

    /**
     * Событие перед поиском, возвращает список id, в которых нужно искать
     * @param array $params
     * @param array $order
     * @param bool $use_in_search флаг, сообщает о том, что данный хелпер участвует в поиске (на случай пустого результата)
     * @return array ids пользователей
     */
    public function preSearch(array &$params, &$order, &$use_in_search = FALSE){
        $use_in_search = FALSE;
        return array();
    }
    public function cleanup(){}
}
