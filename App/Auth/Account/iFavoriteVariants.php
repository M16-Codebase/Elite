<?php
/**
 * Интерфейс избранных предложений (проект maris. Внесены изменения в Account\Someone, Account\AuthorizedAccount, Users\RegistratedUser, Account\Guest)
 * Все функции одноразовые, только для одного пользователя, поэтому не требуется кэширования данных
 * @author pochepochka
 */
namespace App\Auth\Account;
interface iFavoriteVariants {
	/**
	 * Избранные предложения
	 * @param string $catalog_key
	 * @param int|null $segment_id
	 * @return
	 */
	public function getFavorites($catalog_key, $segment_id = null);

	/**
	 * Данные из базы
	 * @param string $catalog_key
	 * @return array
	 */
	public function getFavoriteData($catalog_key);

	/**
	 * Количество избранных предложений.
	 * @param string $catalog_key
	 * @return int
	 */
	public function getFavoriteCount($catalog_key);

	/**
	 * Добавить в избранное
	 * @param string $catalog_key
	 * @param int $entity_id
	 * @return
	 */
	public function addFavorite($catalog_key, $entity_id);

	/**
	 * Удалить из избранного
	 * @param string $catalog_key
	 * @param int $entity_id
	 * @return
	 */
	public function removeFavorite($catalog_key, $entity_id);

	/**
	 * Записать готовые данные в базу
	 * @param string $catalog_key
	 * @param int[] $entity_ids
	 * @param int[] $dates
	 * @param string[] $comments
	 * @return
	 */
	public function setFavorite($catalog_key, array $entity_ids, array $dates, array $comments);
}
