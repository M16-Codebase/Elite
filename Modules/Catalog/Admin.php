<?php
/**
 * Description of Admin
 *
 * @author olga
 *
 */
namespace Modules\Catalog;
use Models\CatalogManagement\Search\CatalogSearch;
use App\Configs\CatalogConfig;
use Models\CatalogManagement\Tags;
class Admin extends \LPS\AdminModule{
    const GOODS_PAGE_SIZE = 100;
    const REVIEW_PAGE_SIZE = 20;
    const PAGE_SEARCH_SIZE = 10;

    public function index(){}
    
    public function convert(){
        
    }
    /**
     * @ajax
     */
    public function convertNum(){
        $numbers = $this->request->query->get('numbers', $this->request->request->get('numbers'));
        $numbers = explode(',', str_replace(', ', ',', trim($numbers)));
        $result_temp = array();
        $result = array();
        if (!empty($numbers)){
            $rule = \Models\CatalogManagement\Rule::make(CatalogConfig::KEY_VARIANT_JDE);
            $rule->setValue($numbers);
            \Models\CatalogManagement\CatalogHelpers\Type\Code::factory();
            \Models\CatalogManagement\CatalogHelpers\Variant\Code::factory();
            $variants = CatalogSearch::factory()->setRules(array($rule))->searchVariants();
            if (!empty($variants)){
                foreach ($variants as $v){
                    if (!empty($v[CatalogConfig::KEY_VARIANT_JDE])){
                        $result_temp[$v[CatalogConfig::KEY_VARIANT_JDE]] = array('code' => $v['code'], 'title' => $v[CatalogConfig::KEY_VARIANT_TITLE]);
                    }
                }
                foreach ($numbers as $num){
                    $result[$num] = !empty($result_temp[$num]) ? $result_temp[$num] : NULL;
                }
            }
        }
        return json_encode($result);
    }

    /* отзывы */
    public function reviews(){
        $page = $this->request->query->get('page', 1);
        $reviews = \Models\CatalogManagement\Review::search(
            array('status' => !empty($_GET['status']) ? $_GET['status'] : NULL),
            $count,
            ($page-1)*self::REVIEW_PAGE_SIZE,
            self::REVIEW_PAGE_SIZE);
        $this->getAns()->add('reviews', $reviews)
            ->add('pageNum', $page)
            ->add('pageSize', self::REVIEW_PAGE_SIZE)
            ->add('count', $count)
            ->add('page', $page);
    }
    /**
     * @ajax
     */
    public function changeReviewStatus(){
        $this->getAns()->setTemplate('Modules/Catalog/Admin/review_row.tpl');
        $r_id = $this->request->request->get('id');
        $status = $this->request->request->get('change_status');
        if (!empty($r_id) && !empty($status)){
            $review = \Models\CatalogManagement\Review::getById($r_id);
            $review->setStatus($status);
            $this->getAns()->add('review', $review);
        }else{
            return json_encode(array('error' => 'empty id OR status'));
        }
    }

    public function reviewPopup(){
        $this->setAjaxResponse();
        $reviewId = $this->request->request->get('id');
        $review = \Models\CatalogManagement\Review::getById($reviewId);
        $this->getAns()->add('review', $review);
    }
}