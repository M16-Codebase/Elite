<?php
namespace Modules\Seo;
use App\Configs\AccessConfig;
use App\Configs\SeoConfig;
use Models\Seo\GoogleAnalytics;
use Models\Seo\SeoLinks;
use Models\Seo\YandexMetrika;

class SuperAdmin extends \LPS\AdminModule {
    const ROBOTS_FILE = '/robots.txt';
    const SITEMAP_FILE = '/sitemap.xml';

    protected static $module_custom_permissions = array(
        AccessConfig::ROLE_SUPER_ADMIN => true,
        AccessConfig::ROLE_SEO_ADMIN => true
    );

    const DEFAULT_ACCESS = AccessConfig::ACCESS_DISALLOW_ALL;
    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        return $this->account instanceof \App\Auth\Account\SuperAdmin;
    }
    
    const PAGE_SIZE = 50;

    public function index() {
        $_SESSION['back_url'] = urlencode($_SERVER['REQUEST_URI']);
        

        if (!empty($_GET['del'])) {
            $this->db->query('DELETE FROM `seo` WHERE `id` = ?d', $_GET['del']);
            return $this->run('metatagList');
        }
        if (isset($_GET['enabled']) && !empty($_GET['id'])){
            $this->changeHide();
            return $this->run('metatagList');
        }
        $this->metatagList(true);
    }

    public function metatagList($inner = false){
        if (!$inner){
            $this->setJsonAns();
            $referer = $this->request->server->get('HTTP_REFERER');
            if (!empty($referer)){
                $parts = parse_url($referer);
                if (!empty($parts['query'])){
                    parse_str($parts['query'], $query);
                    if (!empty($query)){
                        foreach($query as $k => $v){
                            $this->request->query->set($k, $v);
                        }
                    }
                }
            }
        }
        $sort_fields = array('id', 'page_uid');
        $sort = array('field' => 'page_uid', 'desc' => 0);
        if ($this->request->query->get('sort')){
            $sort = $this->request->query->get('sort');
            $keys = array_keys($sort);
            $key = reset($keys);
            if (array_search($key, $sort_fields)!==false){
                $sort = array('field' => $key, 'desc' => (!empty($sort[$key]) ? 1 : 0));
            }
        }else{
        }
        $this->getAns()->add('sort', array($sort['field'] => $sort['desc']));
        $page = $this->request->query->get('page', 1);
        if (empty($page) || $page < 1 || intval($page) != $page){
            $page = 1;
        }
        $params = array(
            'enabled' => 'any',
            'sort' => $sort,
            'page_uid_find' => $this->request->query->get('page_uid_find')
        );
        $items = \Models\Seo\PagePersister::getInstance()->search($params, FALSE, $count, ($page-1) * self::PAGE_SIZE, self::PAGE_SIZE);
        $this->getAns()->add('page', $page);
        $this->getAns()->add('pageSize', self::PAGE_SIZE);
        $this->getAns()->add('seoItems', $items);
        $this->getAns()->add('seoItemsCount', $count);
        if (!empty($_POST['page_uid'])) {
            // отрезаем хост и GET-аттрибуты, слэш на конце тоже отрезаем
            $page_uid = rtrim(preg_replace(array('~^http://([^/\\\\]*)~i', '~[/\\\\]?(\?.*)?$~i'), '', $_POST['page_uid']), '/');
            if (empty($page_uid)){
                $page_uid = '/';
            }
            $item = $this->db->query('SELECT * FROM `seo` WHERE `page_uid` = ?', $page_uid)->getRow();
            if (empty ($item)){
                $itemId = $this->db->query('INSERT INTO `seo` SET `page_uid` = ?', $page_uid);
            }else{
                $itemId = $item['id'];
            }
            $this->setJsonAns()->setEmptyContent()->addData('url', '/seo/edit?id=' . $itemId);
        }
    }

    public function edit() {
        $meta_tags_constructor = \Models\Seo\PagePersister::getInstance();
        $this->getAns()->add('back_url', !empty($_SESSION['back_url']) ? urldecode($_SESSION['back_url']) : '');
        $fields = array('page_uid', 'title', 'keywords', 'description', 'enabled', 'text', 'canonical');
//        $item = isset($_GET['id']) ? $this->db->query('SELECT * FROM `seo` WHERE `id` = ?', $_GET['id'])->getRow() : null;
        $id = $this->request->query->get('id');
        $item = $meta_tags_constructor->getById($id);
        if (empty($item)) {
            return $this->redirect('/seo/');
        }
        if (!empty($_POST)) {
            $data = array();
            foreach ($fields as $k) {
                $value = $this->request->request->get($k);
                if ($k == 'page_uid' && !empty($value)){
                    $value = $value != '/' ? rtrim($value, '/') : '/';
                }
                $data[$k] = !empty($value) ? $value : '';
                if ($k == 'enabled' && $data[$k] === ''){
                    $data[$k] = 1;
                }
            }
            $meta_tags_constructor->updateRule($id, $data);
            return $this->redirect('/seo/');
        }
        $this->getAns()->add('item', $item)
            ->add('meta_tag_variables', SeoConfig::getMetaTagVariables())
            ->setFormData($item);
    }
    
    public function changeHide(){
        if (isset($_GET['enabled']) && !empty($_GET['id'])){
            $this->db->query('UPDATE `seo` SET `enabled` = ?d WHERE `id`=?d', $_GET['enabled'], $_GET['id']);
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function viewRedirects(){
        $this->redirectList(true);
    }

    public function redirectList($inner = false){
        if (!$inner){
            $this->setJsonAns();
        }
        $from = $this->request->query->get('from');
        $to = $this->request->query->get('to');

        $filter_auto = $this->request->query->get('show_mode');
        if (!in_array($filter_auto, array(0, 1, 'all'))) $filter_auto = 0;
        $filter_auto = ($filter_auto != 'all') ? $filter_auto : NULL;

        $urls = \Models\Seo\PageRedirect::getInstance()->get(null, $from, $to, $filter_auto);
        $this->getAns()->add('urls', $urls);
    }
    
    public function createRedirect(){
        $from = $this->request->request->get('fr');
        $to = $this->request->request->get('to');
        $pr = \Models\Seo\PageRedirect::getInstance();
        $pr->clearOldRedirects();
        $result = $pr->create($from, $to, $errors);
        if (empty($errors)) {
            return $this->run('redirectList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
    
    public function editRedirect(){
        $from = $this->request->request->get('fr');
        $to = $this->request->request->get('to');
        $edit = $this->request->request->has('edit');
        $pr = \Models\Seo\PageRedirect::getInstance();
        if ($edit){
            $pr->clearOldRedirects();
            $errors = array();
            if (empty($from)){
                $errors['fr'] = 'empty';
            }
            if (empty($to)){
                $errors['to'] = 'empty';
            }
            if (empty($errors)) {
                $pr->edit($from, $to, $errors);
            }
            if (!empty($errors)){
                $this->setJsonAns()->setEmptyContent()->setErrors($errors);
            } else {
                return $this->run('redirectList');
            }
        } else {
            $ans = $this->setJsonAns();
            $redirects = $pr->get($from);
            $redirect = reset($redirects);
            if (empty($redirect)){
                $ans->setEmptyContent()->addErrorByKey('from', 'empty');
            } else {
                $ans->setFormData($redirect);
            }
        }
    }
    
    public function deleteRedirect(){
        $from = $this->request->request->get('from');
        \Models\Seo\PageRedirect::getInstance()->delete($from);
        return $this->run('redirectList');
    }
    
    public function uploadRedirects(){
        $pr = \Models\Seo\PageRedirect::getInstance();
        $pr->clearOldRedirects();
        $urls = $pr->get();
        $uploaded_file_data = $this->request->files->get('redirects');
        $file_path = $uploaded_file_data->getRealPath();
        $inner = file_get_contents($file_path);
        $inner = explode("\n", $inner);
        array_shift($inner);//заголовки убираем
        $i = 0;
        $edit_urls = array();
        $error_redirects = array();
        foreach($inner as $redirect){
            if (!empty($redirect)){
                if (preg_match("~\t~", $redirect)){
                   $redirect = explode("\t", $redirect);
                }else{
                   $redirect = explode(",", $redirect); 
                }
                $from = $redirect[0];
                $from = preg_replace('~(http(s)?:\/\/[^\/]*)~', '', trim(rtrim($from, '/')));
                $to = preg_replace('~(http(s)?:\/\/[^\/]*)~', '', $redirect[1]);
                if (!isset($urls[$from])){
                    $err = array();
                    $pr->create($from, $to, $err);
                    if (!empty($err)){
                        $error_redirects[] = array('from' => $from, 'to' => $to);
                    }
                }elseif($urls[$from]['to'] != $to){
                    $edit_urls[$i]['to'] = $to;
                    $edit_urls[$i]['from'] = $from;
                }
                $i++;
            }
        }
        if (!empty($edit_urls)){
            foreach($edit_urls as $url_data) {
                $err = array();
                $pr->edit($url_data['from'], $url_data['to'], $err);
                if (!empty($err)){
                    $error_redirects[] = $url_data;
                }
            }
        }
        return $this->redirect($this->getModuleUrl() . 'viewRedirects/');
    }
    
    public function content(){
        $this->getAns()->add('seoPage', true);
        return $this->getModule('Site\Config')->run('seo');
    }
    
    public function editRobots(){
        $doc_root = \App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . self::ROBOTS_FILE;
        if (!file_exists($doc_root) || !is_writable($doc_root)){
            $this->getAns()->add('error', 1);
        }else{
            if (isset($_POST['text'])){
                file_put_contents($doc_root, $_POST['text']);
                return $this->redirect($this->getModuleUrl() . 'editRobots/');
            }
            $text = file_get_contents($doc_root);
            $this->getAns()->addFormValue('text', $text);
        }
    }

    public function editSitemap(){
        $sitemaps = array();
        $doc_root = \App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . self::SITEMAP_FILE;
        if (file_exists($doc_root)){
            $sitemaps[] = self::SITEMAP_FILE;
        }
        if (file_exists(\App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . '/data/sitemap')){
            $files_list = scandir(\App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . '/data/sitemap');
            sort($files_list);
            foreach($files_list as $file){
                if (substr($file, 0, 7) == 'sitemap') {
                    $sitemaps[] = '/data/sitemap/' . $file;
                }
            }
        }
        $this->getAns()
            ->add('sitemaps', $sitemaps);
//        if (file_exists($doc_root)){
//            $files_list = array
//        }
//        if (!file_exists($doc_root) || !is_writable($doc_root)){
//            $this->getAns()->add('error', 1);
//        }else{
//            if (isset($_POST['text'])){
//                file_put_contents($doc_root, $_POST['text']);
//                return $this->redirect($this->getModuleUrl() . 'editSitemap/');
//            }
//            $text = file_get_contents($doc_root);
//            $this->getAns()->addFormValue('text', $text);
//        }
    }

    /***************  Новый интерфейс счетчиков  **************/

    public function counters(){
        $yandex_params = $this->request->request->get('seo_yandex');
        $google_params = $this->request->request->get('seo_google');
        if (!empty($yandex_params) || !empty($google_params)){
            $errors = array();
            if (!empty($yandex_params)){
                YandexMetrika::getInstance()->update($yandex_params, $ya_errors);
                if (!empty($ya_errors)){
                    YandexMetrika::prepareErrors('seo_yandex', $ya_errors, $errors);
                }
            }
            if (!empty($google_params)){
                GoogleAnalytics::getInstance()->update($google_params, $google_errors);
                if (!empty($google_errors)){
                    GoogleAnalytics::prepareErrors('seo_yandex', $google_errors, $errors);
                }
            }
            if (!empty($errors)){
                $this->setJsonAns()->setEmptyContent()->setErrors($errors);
            } else {
                $this->setJsonAns()->setEmptyContent()->setStatus('OK');
            }
        }
        $seo_yandex = YandexMetrika::getInstance()->getParams();
        $seo_google = GoogleAnalytics::getInstance()->getParams();
        $this->getAns()->add('google_targets', \App\Configs\SeoConfig::getParam('google_analytics_targets'))
            ->add('yandex_targets', \App\Configs\SeoConfig::getParam('yandex_metrika_targets'))
            ->add('seo_yandex', $seo_yandex)
            ->add('seo_google', $seo_google)
            ->setFormData(array(
                'seo_yandex' => $seo_yandex,
                'seo_google' => $seo_google
                )
            );
    }

    /*********************  Перелинковка  *********************/
    /**************    wow such links much seo   **************/

    public function links(){
        $this->linksList(true);
    }

    public function linksList($inner = false){
        if (!$inner){
            $this->setJsonAns();
        }
        $this->getAns()
            ->add('links_list', SeoLinks::getInstance()->search());
    }

    public function addLink(){
        $ans = $this->setJsonAns('Modules/Seo/SuperAdmin/linksListInner.tpl');
        $errors = array();
        $params = \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
            'phrase' => array('type' => 'checkString'),
            'url' => array('type' => 'checkUrl'),
            'page_limit' => array('type' => 'checkInt')
        ), $errors);
        if (empty($errors)){
            $url = preg_replace('~^[^/]+~', '', $params['url']);
            $seo_links = SeoLinks::getInstance();
            $link_id = $seo_links->addLink($params['phrase'], $url, $params['page_limit']);
            if (!empty($link_id)){
                $ans->add('new_link', $seo_links->getLinkById($link_id))
                    ->addData('new_link_id', $link_id);
                $this->linksList(true);
            } else {
                $errors['create'] = 'error';
            }
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        }
    }

    public function editLinkFields(){
        $ans = $this->setJsonAns();
        $errors = array();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $errors['id'] = 'empty';
        } else {
            $link = SeoLinks::getInstance()->getLinkById($id);
            if (!empty($link)){
                $ans->setFormData($link);
            } else {
                $errors['link'] = 'not_found';
            }
        }
        if (!empty($errors)){
            $ans->setEmptyContent()->setErrors($errors);
        }
    }

    public function editLink(){
        $ans = $this->setJsonAns('Modules/Seo/SuperAdmin/linksListInner.tpl');
        $seo_links = SeoLinks::getInstance();
        $errors = array();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $errors['id'] = 'empty';
        } else {
            $link = $seo_links->getLinkById($id);
            if (empty($link)){
                $errors['link'] = 'not_found';
            }
        }
        $params = \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
            'phrase' => array('type' => 'checkString'),
            'url' => array('type' => 'checkUrl'),
            'page_limit' => array('type' => 'checkInt')
        ), $errors);
        if (empty($errors)){
            $url = preg_replace('~^[^/]+~', '', $params['url']);
            $seo_links->editLink($id, $params['phrase'], $url, $params['page_limit']);
            $this->linksList(true);
        } else {
            $ans->setEmptyContent()->setErrors($errors);
        }
    }

    public function deleteLink(){
        $ans = $this->setJsonAns('Modules/Seo/SuperAdmin/linksListInner.tpl');
        $id = $this->request->request->get('id');
        if (empty($id)){
            $ans->addErrorByKey('id', 'empty')->setEmptyContent();
        } else {
            SeoLinks::getInstance()->deleteLink($id);
            $this->linksList(true);
        }
    }
}