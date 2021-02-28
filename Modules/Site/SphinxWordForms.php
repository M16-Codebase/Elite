<?php
/**
 * Sphinx word forms editor
 * User: Charles Manson
 * Date: 03.10.14
 * Time: 12:32
 */

namespace Modules\Site;


use App\Configs\SphinxConfig;
use Models\SiteConfigManager;
use Models\SphinxManagement\SphinxSearch;
use Models\SphinxManagement\WordForms;
use Models\Validator;

class SphinxWordForms extends \LPS\AdminModule{
    const PAGE_SIZE = 100000000;
    const AUTOCOMPLETE_SIZE = 20;

    public function index(){
        $this->wordformList(TRUE);
    }

    public function wordformList($inner = FALSE){
        $ans = $inner ? $this->getAns() : $this->setAjaxResponse();
        $page = $this->request->query->get('page', 1);
        $params = array('sort' => $this->request->query->get('sort', $this->request->request->get('sort')));
        $search = $this->request->query->get('search');
        if (!empty($search)){
            $params['search'] = $search;
        }
        $wordforms = WordForms::getInstance()->search($params, ($page - 1) * self::PAGE_SIZE, self::PAGE_SIZE, $count);
        $ans->add('wordforms', $wordforms)
            ->add('count', $count)
            ->add('pageNum', $page)
            ->add('pageSize', self::PAGE_SIZE);
    }

    public function autocomplete(){
        $term = $this->request->query->get('term');
        $ans = array();
        if (!empty($term)){
            $db = \App\Builder::getInstance()->getDB();
            $result = $db->query('SELECT `src_form` FROM `' . WordForms::TABLE . '` WHERE `src_form` LIKE ?s LIMIT ?d', $term.'%', self::AUTOCOMPLETE_SIZE)->getCol('src_form', 'src_form');
            if (count($result) < self::AUTOCOMPLETE_SIZE){
                $result = array_merge(
                    $result,
                    $db->query('SELECT `dst_form` FROM `' . WordForms::TABLE . '` WHERE `dst_form` LIKE ?s LIMIT ?d', $term.'%', self::AUTOCOMPLETE_SIZE - count($result))->getCol('dst_form', 'dst_form')
                );
            }
            foreach($result as $word){
                $ans[] = array(
                    'value' => $word,
                    'label' => $word
                );
            }
        }
        return json_encode($ans);
    }

    public function rebuild(){
        $this->getAns()
            ->add('need_rebuild', WordForms::getInstance()->isChanged() || SphinxSearch::factory(SphinxConfig::CATALOG_KEY)->needRebuild());
    }

    public function add(){
        $params = Validator::getInstance($this->request)->checkFewResponseValues(array(
//            'src_form' => array('type' => 'checkString'),
            'dst_form' => array('type' => 'checkString')
        ), $errors);
        $src_form = $this->request->request->get('src_form');
        $params['src_form'] = is_array($src_form) ? $src_form : explode(',', $src_form);
        if (empty($src_form)){
            $errors['src_form'] = Validator::ERR_MSG_EMPTY;
        }
        $params = empty($errors) ? $this->paramsToLower($params, $errors) : $params;
        if (empty($errors)){
            WordForms::getInstance()->add($params['src_form'], $params['dst_form'], $errors);
            if (empty($errors)){
                $this->getAns()->add('added_wordform', $params['dst_form']);
                return $this->run('wordformList');
            }
        }
        return json_encode(array('errors' => $errors));
    }

    public function edit(){
        $params = Validator::getInstance($this->request)->checkFewResponseValues(array(
            'old_dst_form' => array('type' => 'checkString'),
            //'src_form' => array('type' => 'checkString'),
            'dst_form' => array('type' => 'checkString')
        ), $errors);
        $src_form = $this->request->request->get('src_form');
        $params['src_form'] = is_array($src_form) ? $src_form : explode(',', $src_form);
        if (empty($src_form)){
            $errors['src_form'] = Validator::ERR_MSG_EMPTY;
        }
        $params = empty($errors) ? $this->paramsToLower($params, $errors) : $params;
        if (empty($errors)){
            WordForms::getInstance()->update($params['old_dst_form'], $params['src_form'], $params['dst_form'], $errors);
            if (empty($errors)){
                $wordform = WordForms::getInstance()->getByDstForm($params['dst_form']);
                $this->setAjaxResponse()->setTemplate('Modules/Site/SphinxWordForms/wordformGroup.tpl')
                    ->add('wordform', $wordform);
            }
        }
        if (!empty($errors)){
            return json_encode(array('errors' => $errors));
        }

    }

    public function delete(){
        WordForms::getInstance()->delete($this->request->request->get('dst_form'));
//        return $this->run('wordformList');
        return json_encode(array('errors' => NULL));
    }

    /**
     * Редактор стопслов, при изменении файла указывает при очередном обновлении индекса
     * делать полное перестроение вместо слияния
     */
    public function stopwords(){
        $stop_words_file = \LPS\Config::getRealDocumentRoot() . SphinxConfig::STOPWORDS_FILE;
        if (!file_exists($stop_words_file)){
            touch($stop_words_file);
        }
        if (!file_exists($stop_words_file) || !is_writable($stop_words_file)){
            $this->getAns()->add('error', 1);
        }else{
            if (isset($_POST['text'])){
                $ans = array('errors' => NULL, 'data' => array());
                $result = file_put_contents($stop_words_file, $_POST['text']);
                if ($result === FALSE){
                    $ans['errors']['file'] = 'write_error';
                    // стоп слов может не быть, поэтому пустой файл сохранить можно
//                } elseif ($result == 0) {
//                    $ans['errors']['text'] = 'empty';
                } else {
                    \Models\TechnicalConfig::getInstance()->set(WordForms::IS_WORDFORMS_MODIFIED, 'sphinx', 1, 'Синонимы сфинкса изменены');
                    $ans['data']['status'] = 'OK';
                }
                return json_encode($ans);
            }
            $text = file_get_contents($stop_words_file);
            $this->getAns()
                ->add('stopwords', preg_split('/\s+/', $text));
//                ->addFormValue('text', $text);
        }
    }

    /**
     * Устанавливает флаг полной перегенерации индекса для cron-задачи
     * @return string
     */
    public function rebuildIndex(){
        SphinxSearch::factory(SphinxConfig::CATALOG_KEY)->forceRecreateIndex();
        return json_encode(array('errors' => NULL));
    }

    /**
     * переводит строки в нижний регистр и проверяет на недопустимые символы (>)
     * @param $params
     * @param array $errors
     * @return mixed
     */
    private function paramsToLower($params, &$errors = array()){
        foreach($params as $key=>$value){
            if (is_array($value)){
                foreach($value as $k=>$v){
                    if (strpos($v, '>') !== FALSE){
                        $errors[$key] = Validator::ERR_MSG_INCORRECT_FORMAT;
                    }
                    $value[$k] = mb_strtolower($v);
                }
                $params[$key] = $value;
            } else {
                if (strpos($value, '>') !== FALSE){
                    $errors[$key] = Validator::ERR_MSG_INCORRECT_FORMAT;
                }
                $params[$key] = mb_strtolower($value);
            }

        }
        return $params;
    }
}