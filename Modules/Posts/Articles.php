<?php
/**
 * Description of Articles
 *
 * @author olga
 */
namespace Modules\Posts;
use Models\InternalLinkManager AS ILM;
use Models\CatalogManagement\Type;
class Articles extends ViewTheme{
    const POSTS_TYPE = 'article';

	public function post(){
//        \Models\ContentManagement\PostHelpers\Images::factory();
//		$post = \Models\ContentManagement\Post::getById($this->request->query->get('id'));
//		$l = ILM::getInstance();
//        //ищем все типы, к которым привязана данная статья
//		$attaches = $l->search(array(), NULL, NULL, true, array(ILM::OBJECT_TYPE_ARTICLE => $post['id']), ILM::TARGET_TYPE_TYPE);
//		$types = array();
//        $articles = array();
//		if (!empty($attaches[ILM::TARGET_TYPE_TYPE])){
//            $type_ids = array_keys($attaches[ILM::TARGET_TYPE_TYPE]);
//			$types = Type::factory($type_ids);
//            //ищем все статьи, привязанные к типам
//            $attaches = $l->search(array(ILM::TARGET_TYPE_TYPE => $type_ids), ILM::OBJECT_TYPE_ARTICLE);
//            unset($attaches[ILM::OBJECT_TYPE_ARTICLE][$post['id']]);//нам не нужен сам пост в списке статей
//            if (!empty($attaches[ILM::OBJECT_TYPE_ARTICLE])){
//                $articles = \Models\ContentManagement\Post::factory(array_keys($attaches[ILM::OBJECT_TYPE_ARTICLE]));
//            }
//		}
//        $this->getAns()->add('types', $types)
//                ->add('articles', $articles);
		return parent::post();
	}
}

?>
