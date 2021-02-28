<?php
namespace Models\CatalogManagement\Exchange\Import;
/**
 *
 * @author olya
 */
interface iImport {
    /**
     * Создание задачи на основе параметров и/или файла
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     */
    public static function createTask($params = array(), $FILE = NULL);
    /**
     * Основной метод, который запускает импорт
     * @throws \Exception
     */
    public function getData();
}
