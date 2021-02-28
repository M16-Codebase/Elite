<?php
/**
 * Класс гостя
 *
 * @author olga
 */
namespace App\Auth\Account;
class Guest extends Someone{
    const ROLE_TITLE = 'Guest';
    /**
     * Возвращает объект зарегестированного пользователя, 
     * т.к. гость - не зарегистрированный пользователь, возвращаем NULL
     * @return \App\Auth\Users\RegistratedUser|NULL
     */
    public function getUser(){
        return NULL;
    }
    /**
     * Получить роль пользователя
     * @return string
     */
    public function getRole(){
        //константой у неавторизованных пользователей, у остальных роль берется у RegistratedUser, который соответствует данному аккаунту
        return self::ROLE_TITLE;
    }
    /**
     * Проверяет, может ли пользователь пользоваться какими-то привелегиями
     * @return boolean
     */
    public function isActive(){
        return FALSE;
    }

//	/**
//	 * Получить данные из сессии
//	 * @param string $catalog_key
//	 * @return array
//	 */
//	public function getFavoriteData($catalog_key){
//		$session = \App\Builder::getInstance()->getCurrentSession();
//		$favor_data = $session->get('favorite');
//		$favor_data = !empty($favor_data[$catalog_key]) ? $favor_data[$catalog_key] : array();
//		return array(
//			'entity_ids' => !empty($favor_data['entity_ids']) ? explode(',', $favor_data['entity_ids']) : array(),
//			'comments' => !empty($favor_data['comments']) ? json_decode($favor_data['comments'], TRUE) : array()
//		);
//	}
//	/**
//	 * Записать готовые данные в сессию
//	 */
//	public function setFavorite($catalog_key, array $entity_ids, array $comments){
//		$session = \App\Builder::getInstance()->getCurrentSession();
//		$favor_data = $session->get('favorite');
//		$favor_data = !empty($favor_data) ? $favor_data : array();
//		$data = array('entity_ids' => implode(',', $entity_ids), 'comments' => json_encode($comments));
//		$favor_data[$catalog_key] = $data;
//		$session->set('favorite', $favor_data);
//	}
}
