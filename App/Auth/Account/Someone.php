<?php
/**
 * Класс пользователя, зашедшего на сайт
 *
 * @author olga
 */
namespace App\Auth\Account;

use App\Configs\CatalogConfig;

abstract class Someone implements iSomeone, iFavoriteVariants{
	protected $favorites = array();

    public function __construct(\App\Auth\Users\RegistratedUser $user = NULL){
		$favorites = \App\Builder::getInstance()->getRequest()->cookies->get('favorites');
		$this->favorites = !empty($favorites) ? json_decode($favorites, true) : array();
    }
    /**
     * Проверяет, разрешено ли пользователю запрашивать страницу модуля $moduleUrl и метод $action
     * @param string $moduleUrl
     * @param string $action
     * @return bool
     */
    public function isPermission($moduleUrl, $action = \LPS\Router\Web::DEFAULT_ACTION){
        $role = $this->getRole();
        $moduleUrl = trim($moduleUrl, '/');
        $permissions = \Models\UserPermission::getInstance()->search($role);
        if (!empty($permissions[$moduleUrl][$action])){//если есть запись о доступе, берем значение из базы
            return $permissions[$moduleUrl][$action] == \Models\UserPermission::STATUS_ENABLE ? TRUE : FALSE;
        }else{//если нет, берем значение по умолчанию (значение у объекта роли)
            $action = \Models\Action::getInstance()->search(array('moduleUrl' => $moduleUrl, 'action' => $action));
            $action = reset($action);
            $roleData = \Models\Roles::getInstance()->get($role);
            if (empty($roleData)){
                throw new \Exception('Не задана роль "' . $role . '"');
            }
            if (empty($action)){
                $default_permissions = $roleData['default_permission'] == \Models\Roles::STATUS_ENABLE ? TRUE : FALSE;
                return $default_permissions;
            } else {
                $permissions = \Models\UserPermission::getInstance()->setActionPermissions($action);
                return $permissions[$role];
            }
        }
    }

	/**
	 * Избранные предложения
	 * @param null $segment_id
	 * @return array
	 */
	public function getFavorites($catalog_key, $segment_id = NULL){
		$data = $this->getFavoriteData($catalog_key);
		$dates = !empty($data['dates']) ? $data['dates'] : array();
		arsort($dates);
		$result = array('items' => array(), 'comments' => array('title' => '', 'text' => ''), 'counts' => array('items' => 0, 'variants' => 0));
		$catalog = \Models\CatalogManagement\Type::getByKey($catalog_key, \Models\CatalogManagement\Type::DEFAULT_TYPE_ID, $segment_id);
		if (!empty($data['entity_ids'])){
			$entity_ids = array_keys($dates);
			$entity_ids = array_merge($entity_ids, array_diff($data['entity_ids'], $entity_ids));
			$entities = $catalog['only_items']
				? \Models\CatalogManagement\Item::factory($entity_ids, $segment_id)
				: \Models\CatalogManagement\Variant::factory($entity_ids, $segment_id);
			$count_items = 0;
			if ($catalog['only_items']) {
				$result['items'] = $entities;
				$result['count']['items'] = count($entities);
			} else {
				foreach ($entities as $v_id => $v){
					if (empty($v)){
						unset($entities[$v_id]);
						continue;
					}
					if (empty($result['items'][$v['item_id']])){
						$result['items'][$v['item_id']] = array('item' => $v->getItem(), 'variants' => array(), 'comment' => !empty($data['comments'][$v['id']]) ? $data['comments'][$v['id']] : '');
						$count_items++;
					}
					$result['items'][$v['item_id']]['variants'][$v['id']] = $v;
				}
				$result['counts']['items'] = $count_items;
				$result['counts']['variants'] = count($entities);
			}
			$result['comments']['title'] = !empty($data['comments']['title']) ? $data['comments']['title'] : '';
			$result['comments']['text'] = !empty($data['commnets']['text']) ? $data['comments']['text'] : '';
		}
		return $result;
	}

	/**
	 * Количество избранных предложений.
	 * @param string $catalog_key
	 * @return int
	 */
	public function getFavoriteCount($catalog_key){
		$data = $this->getFavoriteData($catalog_key);
		return count($data['entity_ids']);
	}

	/**
	 * Добавить в избранное
	 * @param string $catalog_key
	 * @param int $entity_id
	 * @return bool|\Symfony\Component\HttpFoundation\Cookie
	 */
	public function addFavorite($catalog_key, $entity_id){
		$data = $this->getFavoriteData($catalog_key);
		if (!in_array($entity_id, $data['entity_ids'])){
			$dates = !empty($data['dates']) ? $data['dates'] : array();
			if (count($data['entity_ids']) >= CatalogConfig::FAVORITES_LIST_SIZE) {
				asort($dates);
				reset($dates);
				$delete_id = key($dates);
				unset($dates[$delete_id]);
				$key = array_search($delete_id, $data['endity_ids']);
				if ($key !== false) {
					unset($data['entity_ids'][$key]);
				}
			}
			$dates[$entity_id] = time();
			arsort($dates);
			return $this->setFavorite($catalog_key, array_merge($data['entity_ids'], array($entity_id)), $dates, $data['comments']);
		} else {
			$dates = !empty($data['dates']) ? $data['dates'] : array();
			$dates[$entity_id] = time();
			arsort($dates);
			return $this->setFavorite($catalog_key, array_merge($data['entity_ids']), $dates, $data['comments']);
		}
	}
	/**
	 * Удалить из избранного
	 * @return bool|\Symfony\Component\HttpFoundation\Cookie
	 */
	public function removeFavorite($catalog_key, $entity_id){
		$data = $this->getFavoriteData($catalog_key);
		if (in_array($entity_id, $data['entity_ids'])){
			$key = array_search($entity_id, $data['entity_ids']);
			unset($data['entity_ids'][$key]);
			$dates = !empty($data['dates']) ? $data['dates'] : array();
			unset($dates[$entity_id]);
			if (!empty($data['comments'][$entity_id])){
				unset($data['comments'][$entity_id]);
			}
			return $this->setFavorite($catalog_key, $data['entity_ids'], $dates, $data['comments']);
		}
		return FALSE;
	}

	/**
	 * Сохранить толпу комментариев
	 * @param string $catalog_key
	 * @param array $comments
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function setFavoriteComments($catalog_key, array $comments){
		$data = $this->getFavoriteData($catalog_key);
		return $this->setFavorite($catalog_key, $data['variant_ids'], $data['dates'], $comments);
	}

	/**
	 * Созранить один комментарий
	 * @param string $catalog_key
	 * @param string $type
	 * @param string $comment
	 * @param int $item_id
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 * @throws \Exception
	 */
	public function saveFavoriteComment($catalog_key, $type, $comment, $item_id = NULL){
		if ($type == 'item' && empty($item_id)){
			throw new \Exception('Не передан id объекта, к которому написан коментарий');
		}
		$data = $this->getFavoriteData($catalog_key);
		if ($type == 'title'){
			$data['comments']['title'] = $comment;
			return $this->setFavoriteComments($catalog_key, $data['comments']);
		}elseif($type == 'text'){
			$data['comments']['text'] = $comment;
			return $this->setFavoriteComments($catalog_key, $data['comments']);
		}elseif($type == 'item'){
			$data['comments'][$item_id] = $comment;
			return $this->setFavoriteComments($catalog_key, $data['comments']);
		}else{
			throw new \Exception('Неверно задан тип комментария');
		}
	}

	/**
	 * Получить данные из сессии
	 * @param string $catalog_key
	 * @return array
	 */
	public function getFavoriteData($catalog_key){
		return !empty($this->favorites[$catalog_key]) ? $this->favorites[$catalog_key] : array('entity_ids' => array(), 'comments' => array(), 'dates' => array());
	}

	/**
	 * Записать готовые данные в куки
	 * @param string $catalog_key
	 * @param int[] $entity_ids
	 * @param int[] $dates
	 * @param array $comments
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	public function setFavorite($catalog_key, array $entity_ids, array $dates, array $comments){
		$this->favorites[$catalog_key] = array('entity_ids' => $entity_ids, 'dates' => $dates, 'comments' => $comments);
		return new \Symfony\Component\HttpFoundation\Cookie(
			'favorites',
			json_encode($this->favorites),
			time() + \LPS\Config::COOKIE_LIFE_TIME,
			\LPS\Config::COOKIE_PATH,
			\LPS\Config::getParametr('site', 'domain')
		);
	}
}
?>