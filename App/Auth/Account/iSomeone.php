<?php
/**
 * Интерфейс любого пользователя системы
 *
 * @author olga
 */
namespace App\Auth\Account;
interface iSomeone {
    /**
     * Возвращает объект зарегистрированного пользователя, 
     * если не зарегистрированный пользователь, возвращаем NULL
     * @return \App\Auth\Users\RegistratedUser|NULL
     */
    public function getUser();
    /**
     * Получить роль пользователя
     * @return string
     */
    public function getRole();
    /**
     * Проверяет, может ли пользователь пользоваться какими-то привелегиями
     * @return boolean
     */
    public function isActive();
}

?>
