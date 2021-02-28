<?php
/**
 * Коллекция картинок по умолчанию. 
 * Т.е. при установке системы уже есть картинки с id, 
 * прописанные в классах, где требуются картинки для объектов, 
 * если у самих объектов картинок нет.
 *
 * @author olga
 */
namespace Models\ImageManagement;
class DefaultImageCollection extends Collection{
    const COLLECTION_ID = 1;
}

?>
