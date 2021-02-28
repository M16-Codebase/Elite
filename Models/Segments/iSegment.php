<?php
/**
 *
 * @author olya
 */
namespace Models\Segments;
interface iSegment {
    /**
     * 
     * @param bool $onSite
     * @return iSegment
     */
    public static function getDefault($onSite = false);
    /**
     * 
     * @param int $segment_id
     * @return iSegment
     */
	public static function getById($segment_id);
    /**
     *
     * @return iSegment[]
     */
    public static function getAll();
    /**
     * 
     * @param string $key
     * @return iSegment
     */
    public static function getByKey($key);

//    /**
//     * @param array $params
//     * @param int $count
//     * @param int $start
//     * @param int $limit
//     * @return iSegment[]
//     */
//    public static function search($params = array(), &$count = 0, $start = 0, $limit = 1000000);
    /**
     * Возвращает посты к страницам для указанного uri
     * @param string $uri - uri страницы, для которой нужны посты
     * @return \Models\ContentManagement\SegmentPost[]
     */
    public function getPagePosts($uri);
    /**
     * @return string
     */
    public function getUrlPrefix();

    /**
     * @return array
     */
    public function asArray();

    /**
     * @param string $key
     * @param string $title
     * @param array $errors
     * @return int
     */
    public static function create($key, $title = NULL, &$errors = array());
    /**
     * Редактирование
     * @param array $params
     * @param array $errors
     * @return boolean
     */
    public function update(array $params, &$errors = array());
    /**
     * Удаляем объект
     * @param int $id
     * @param string $error
     * @return bool
     */
    public static function delete($id, &$error = NULL);
}