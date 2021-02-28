<?php
/**
 * Description of PostImageCollection
 *
 * @author olga
 */
namespace Models\ContentManagement;
use Models\ImageManagement\Collection;
use Models\ContentManagement\Comment;
use Models\ImageManagement\CollectionImage;
class CommentImageCollection extends Collection{
    const DEFAULT_IMAGE_ID = 1;
    /**
     * 
     * @param int $id collection id
     * @return boolean
     */
    public static function delete($id){
        $result = parent::delete($id);
        if ($result === true){
            $db = \App\Builder::getInstance()->getDB();
            $db->query('UPDATE `'.  Comment::TABLE .'` SET `collection_id` = NULL WHERE `collection_id` = ?d', $id);
            return true;
        }else{
            return $result;
        }
    }
}

?>
