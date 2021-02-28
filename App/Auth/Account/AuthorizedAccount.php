<?php
/**
 * Класс аккаунта, авторизованного в системе
 *
 * @author olga
 */
namespace App\Auth\Account;

abstract class AuthorizedAccount extends Someone{
    protected $user = null;
    /**
     * Используется только в классе \App\Auth\Controller
     * @param \App\Auth\Controller $controller
     * @param \App\Auth\Users\RegistratedUser $user
     * @param string $userClass
     * @return \App\Auth\Account\AuthorizedAccount 
     */
    public static function _makeAccount(\App\Auth\Controller $controller, $user, $userClass){
        return new $userClass($user);
    }
    public function __construct(\App\Auth\Users\RegistratedUser $user = NULL){
        parent::__construct();
        if (empty($user)){
            throw new \LogicException('User is NULL');
        }
        $this->user = $user;
    }
    /**
     * Получить объект зарегистрированного пользователя
     * @return \App\Auth\Users\RegistratedUser
     */
    public function getUser(){
        return $this->user;
    }
    /**
     * Получить роль пользователя
     * @return string
     */
    public function getRole(){
        return $this->user['role'];
    }
    /**
     * Проверяет, может ли пользователь пользоваться какими-то привелегиями
     * @return boolean
     */
    public function isActive(){
        return $this->getUser()->isActive();
    }
//	/**
//	 * Количество избранных предложений.
//	 */
//	public function getFavoriteData($catalog_key){
//		return $this->getUser()->getFavoriteData($catalog_key);
//	}
//	/**
//	 * Записать готовые данные в базу
//	 */
//	public function setFavorite($catalog_key, array $entity_ids, array $comments){
//		return $this->getUser()->setFavorite($entity_ids, $comments);
//	}
}
?>
