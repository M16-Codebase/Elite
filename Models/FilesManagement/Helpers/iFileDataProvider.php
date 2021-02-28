<?php
namespace Models\FilesManagement\Helpers;
use Models\FilesManagement\File;
/**
 * Description of iFileDataProvider
 *
 * @author olya
 */
interface iFileDataProvider {
    public static function factory();
    /**
     * Возвращает список дополнительных полей, к которым организует доступ провайдер данных
     */
    public function fieldsList();
    /**
     * возвращает значение дополнительного поля
     */
    public function get(File $file, $field, $segment_id = NULL);
    /**
     * предупреждение, что данные для указанных Files попали в кеш данных
     */
    public function onLoad(File $file);
	/**
	 * Событие до создания
	 * @param string $title
	 * @param \Symfony\Component\HttpFoundation\File\UploadedFile $FILE
     * @param string $error
	 */
	public function preCreate($title, $FILE, $error);
    /**
     * событие на создание нового File
     */
    public function onCreate($file_id, $title, $FILE);
     /**
     * событие перед изменением
     */
    public function preUpdate(File $file, &$params);
     /**
     * событие после изменения File
     */
    public function onUpdate(File $file);
    public function onCoverUpload($FILE, $new_cover, $error);
    /**
     *
     * @param array $ids
     * @param array $error_ids
     */
    public function onDelete($ids, $error_ids);
    public function onReload(File $f, $FILE);
}
