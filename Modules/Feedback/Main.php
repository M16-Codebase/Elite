<?php
/**
 * Формы оратной связи
 *
 * @author olga
 */
namespace Modules\Feedback;
use App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use LPS\Config;
use Models\CatalogManagement\Positions\Feedback;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Rules\RuleAggregator;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class Main extends \LPS\AdminModule{
	const LOGS_PAGE_SIZE = 50;

    private $allow_filter_params = array('author', 'email', 'phone');
    private $search_like = array('author', 'email', 'phone');

    public function index(){
        $this->logsList(TRUE);
    }
    public function logsList($inner = FALSE){
        if (!$inner){
            $this->setJsonAns();
        }
        $page = $this->request->query->get('page', 1);
        $rules = $this->getFilterParams($this->request->query->all());
        $result = CatalogSearch::factory(CatalogConfig::FEEDBACK_KEY)
            ->setRules($rules)
            ->searchItems(($page-1)*self::LOGS_PAGE_SIZE, self::LOGS_PAGE_SIZE);
        $this->getAns()->add('logs', $result)
            ->add('count', $result->getTotalCount())
            ->add('pageSize', self::LOGS_PAGE_SIZE)
            ->add('pageNum', $page)
            ->add('types', $this->getTypeList());//типы писем
    }

    /**
     * Список типов обращений с сайта
     * @return \Models\CatalogManagement\Type[]
     */
    protected function getTypeList(){
        $catalog = Type::getByKey(CatalogConfig::FEEDBACK_KEY);
        $types = Type::search(array('parent_id' => $catalog['id'], 'not_key' => array(FeedbackConfig::TYPE_VACANCY, FeedbackConfig::TYPE_APPLICANT)));
        $filter_list = array();
        foreach($types as $t) {
            $filter_list[$t['key']] = $t['title'];
            if ($t['key'] == FeedbackConfig::TYPE_APART_REQUEST) {
                $filter_list[FeedbackConfig::TYPE_APART_REQUEST_PRIMARY] = 'Заявка/вопрос по первичной недвижимости';
                $filter_list[FeedbackConfig::TYPE_APART_REQUEST_RESALE] = 'Заявка/вопрос по вторичной недвижимости';
            }
        }
        return $filter_list;
    }

    /**
     * Парсим параметры поиска и создаем список рулов для поиска обращений по каталогу
     * @param $post_data
     * @return array
     */
    protected function getFilterParams($post_data){
        $rules = array();
        foreach($this->allow_filter_params as $param){
            $value = $this->request->query->get($param);
            if (!empty($value)){

                $rule = Rule::make($param)->setValue($value);
                if (in_array($param, $this->search_like)){
                    $rule->setSearchType(Rule::SEARCH_LIKE);
                }
                $rules[$param] = $rule;
            }
        }
        $date_start = $this->request->query->get('date_start');
        $date_end = $this->request->query->get('date_end');
        $time_rule = Rule::make('time')->setOrder(empty($post_data['order']['time']));
        if (!empty($post_data['date_start']) || !empty($post_data['date_end'])){
            if (!empty($date_start)){
                $time_rule->setMin(date('Y-m-d 00:00:00', strtotime($post_data['date_start'])));
            }
            if (!empty($date_end)){
                $time_rule->setMax(date('Y-m-d 23:59:59', strtotime($post_data['date_end'])));
            }
        }
        $rules['time'] = $time_rule;
        if (isset($post_data['number']) && strlen($post_data['number'])){
            $type_id = NULL;
            $number = $post_data['number'];
            $catalog = Type::getByKey(CatalogConfig::FEEDBACK_KEY);
            $types = Type::search(array('parent_id' => $catalog['id'], 'not_key' => array(FeedbackConfig::TYPE_VACANCY, FeedbackConfig::TYPE_APPLICANT)));
            foreach($types as $t){
                if (!isset($t['number_prefix'])){
                    throw new \Exception('Не задан префикс обращения ' . json_encode($t, JSON_UNESCAPED_UNICODE));
                }
                $prefix = mb_strtoupper($t['number_prefix']);
                $number_prefix = mb_strtoupper(mb_substr($number, 0, mb_strlen($prefix)));
                if ($number_prefix == $prefix){
                    $type_id = $t['id'];
                    $number = intval(mb_substr($number, mb_strlen($prefix)));
                    break;
                }
            }
            if (!empty($type_id)) {
                $rules['type'] = Rule::make('type_id')->setValue($type_id);
            }
            $number = intval($number);
            $rules['number'] = Rule::make(FeedbackConfig::KEY_FEEDBACK_NUMBER)->setValue(!empty($number) ? $number : -1);
        }
        if (!empty($post_data['type'])) {
            $catalog = Type::getByKey(CatalogConfig::FEEDBACK_KEY);
            $type_key = in_array($post_data['type'], array(FeedbackConfig::TYPE_APART_REQUEST_RESALE, FeedbackConfig::TYPE_APART_REQUEST_PRIMARY))
                ? FeedbackConfig::TYPE_APART_REQUEST
                : $post_data['type'];
            $type = Type::getByKey($type_key, $catalog['id']);
            if (!empty($type)) {
                $rules['type_id'] = Rule::make('type_id')->setValue($type['id']);
            }
            if (in_array($post_data['type'], array(FeedbackConfig::TYPE_APART_REQUEST_RESALE, FeedbackConfig::TYPE_APART_REQUEST_PRIMARY))) {
                if ($post_data['type'] == FeedbackConfig::TYPE_APART_REQUEST_RESALE) {
                    $rules[FeedbackConfig::KEY_REQUEST_APARTMENT_RESALE] = Rule::make(FeedbackConfig::KEY_REQUEST_APARTMENT_RESALE)->setExists();
                } else {
                    $rules = array(
                        RuleAggregator::make(RuleAggregator::LOGIC_AND, $rules),
                        RuleAggregator::make(RuleAggregator::LOGIC_OR, array(
                            Rule::make(FeedbackConfig::KEY_REQUEST_COMPLEX)->setExists(),
                            Rule::make(FeedbackConfig::KEY_REQUEST_APARTMENT)->setExists()
                        ))
                    );
                }
            }
        }
        return $rules;
    }

    public function setStatus(){
        $id = $this->request->request->get('id');
        $status = $this->request->request->get('status');
        $status = !empty($status) ? 1 : 0;
        $ans = $this->setJsonAns()->setEmptyContent();
        if (empty($id)){
            $ans->addErrorByKey('id', 'empty');
        } else {
            $id = is_array($id) ? $id : array($id);
            $items = CatalogSearch::factory(CatalogConfig::FEEDBACK_KEY)
                ->setRules(array(Rule::make('id')->setValue($id)))
                ->setPublicOnly(false)
                ->searchItems()
                ->getSearch();
            if (empty($items)){
                $ans->addErrorByKey('id', 'empty');
            } else {
                foreach($items as $i){
                    /** @var \Models\CatalogManagement\Positions\Feedback $i */
                    $i->setStatus($status);
                }
                $ans->setStatus('OK');
            }
        }
    }

    public function viewRequest(){
            $this->setAjaxResponse();
            $id = $this->request->request->get('id', $this->request->query->get('id'));
            $result = Feedback::getById($id);
//                Feedback::searchLog(array('id' => $id), 0, 1, $count);
            $this->getAns()->add('log', $result)
            ->add('types', $this->getTypeList())
            ->add('properties', $result->getType()->getProperties());
    }
    public function sended(){
        $catalog_module = $this->getModule('Catalog\Main');
        if (!in_array($this->routeTail, $this->sended_statuses)){
            return $this->notFound();
        }
        $this->getAns()->add('send_action', $this->routeTail);
        $catalog_module->getLeftMenu();
    }
    /**
     * Обработчик форм обратной связи
     */
    public function makeRequest(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $errors = array();
        $check_string = $this->request->request->get('check_string') . Config::HASH_SOLT_STRING;
        if ($this->request->request->get('hash_string') == md5($check_string)){
            $fields = $this->request->request->all();
            foreach(array('check_string', 'hash_string', 'feedbackType', 'vacancy_id') as $field){
                unset($fields[$field]);
            }
            $fields[\App\Configs\FeedbackConfig::KEY_FEEDBACK_REFERRER_URL] = $this->request->server->get('HTTP_REFERER');
            \Models\CatalogManagement\Positions\Feedback::make(
                $this->request->request->get('feedbackType'),
                $fields,
                $this->request->files->all(),
                $errors
            );
//            $feedback = Feedback::factory($this->request->request->get('feedbackType'));
//            if (!empty($feedback)){
//                $feedback->make($this->request->request->all(), $errors);
//            } else {
//                $errors['feedbackType'] = 'wrong';
//            }
        }else{
            $errors['check_sum'] = 'wrong';
        }
        if (!empty($errors)){
            $ans->setErrors($errors);
        }
    }
    public function deleteRequest(){
        $ans = $this->setJsonAns();
        $id = $this->request->query->get('request_id');
        if (empty($id)){
            $ans->addErrorByKey('exception', 'не передан id айтема')->setEmptyContent();
            return;
        }
        $item = \Models\CatalogManagement\Item::getById($id);
        if (empty($item)){
            $ans->addErrorByKey('exception', 'айтем с id ' . $id . ' не найден')->setEmptyContent();
            return;
        }
        $type_id = $item['type_id'];
        $item->delete($errors, \LPS\Config::DELETE_ENTITIES_FROM_DB);
        if (empty($errors)){
            // listItems получает id типа через get-параметр id, без этого не сможем вернуть список айтемов типа
            $this->request->query->set('id', $type_id);
            $ans->setStatus('ok');
            return $this->run('logsList');
        } else {
            $ans->setErrors($errors);
        }
    }
}