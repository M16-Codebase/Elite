<?php
/**
 * Класс аккаунта разработчика
 *
 * @author olga
 */
namespace App\Auth\Account;
class SuperAdmin extends Admin{
    protected $access_roles = array('SuperAdmin');
}

?>
