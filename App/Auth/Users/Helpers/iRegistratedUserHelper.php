<?php
namespace App\Auth\Users\Helpers;
use App\Auth\Users\RegistratedUser;
/**
 * Description of iUserDataProvider
 *
 * @author olga
 * @TODO разделить на iUserListener и iUserDataProvider как в вариантах
 */
interface iRegistratedUserHelper{
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    function get(RegistratedUser $user, $field);
    /**
     * предупреждение, что данные для указанных Users попали в кеш данных
     */
    function onLoad(RegistratedUser $user);

    function prepare(array $ids);
    
    function preCreate(&$params, &$errors, $hash);
    /**
     * событие на создание нового User
     */
    function onCreate($id, $hash);
     /**
     * событие после изменения User
     * @param \App\Auth\Users\RegistratedUser $user
     * @param array $old_data
     * @param array $additional_data
     */
    function onUpdate(RegistratedUser $user, $old_data, $segment_id);
     /**
     * событие перед изменением
     */
    function preUpdate(RegistratedUser $user, &$params, $segment_id, &$errors);
    /**
     * cобытие на удаление user
     * @param int $user_id
     */
    function onDelete($user_id);

    /**
     * Событие перед поиском, возвращает список id, в которых нужно искать
     * @param array $params
     * @param array $order
     * @param bool $use_in_search флаг, сообщает о том, что данный хелпер участвует в поиске (на случай пустого результата)
     * @return array ids пользователей
     */
    function preSearch(array &$params, &$order, &$use_in_search = FALSE);
    function cleanup();
}

?>