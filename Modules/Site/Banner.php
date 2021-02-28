<?php
/**
 * Description of Banner
 *
 * @author olga
 */
namespace Modules\Site;
use App\Configs\CatalogConfig;
use App\Configs\RealEstateConfig;
use Models\Banner AS B;
class Banner extends \LPS\AdminModule{
    /**
     * Список урлов, на которых будут баннеры
     * @var array
     */
    private static $pageUrls = array(
        '/' => 'Главная страница',
        '/company/' => 'О компании',
        '/real-estate/' => 'Первичная недвижимость',
        '/resale/' => 'Вторичная недвижимость'
    );

    public function index(){
        $this->getAns()->add('pageUrls', self::$pageUrls);
        $this->banners(true);
    }

    public function banners($inner = false){
        if (!$inner){
            $this->setJsonAns();
        }
        $url_id = $this->request->query->get('url_id');
        if (!empty($url_id)){
            $url = ''; //@TODO что-то с урлами нужно будет придумать
        }
        if (empty($url)){
            $url = $this->request->query->get('url', $this->request->request->get('url', '/'));
        }
        $url = is_array($url) ? reset($url) : $url;
        $segment_id = null;
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE) {
            $segment_id = $this->request->query->get('segment_id', $this->request->request->get('segment_id', \App\Segment::getInstance()->getDefault()['id']));
        }
        if (!$inner){
            $this->getAns()->addData('url_deb', $url);
        }
        $this->getAns()->add('page_url', $url);
        $banner_id = $this->request->request->get('id');
        $new_position = $this->request->request->get('position');
        $sortable = $this->request->request->get('sortable');
        if ($banner_id !== NULL && $new_position !== NULL){
            $banner = B::getById($banner_id, $url);
            if (!empty($banner)) {
                $banner->move($new_position);
            }
        }
        if ($banner_id !== NULL && $sortable !== NULL){
            $banner = B::getById($banner_id, $url);
            if (!empty($banner)) {
                // $banner->move($new_position);
            }
        }
//        $segment_id = isset($banner_id) ? $banner['segment_id'] : $this->request->query->get('s', 1);
        $banners = B::search(array('url'=>$url, 'date_filter' => FALSE, 'order' => 'admin', 'segment_id' => $segment_id));
        $this->getAns()
            ->add('banners', $banners)
            ->add('segment_id', $segment_id)
        ;
        if ($this->request->request->get('cat')){
            $this->getAns()->add('catalog_banner', 1);
        }
    }

    public function bannerFields(){
        $this->setJsonAns();
        $banner_id = $this->request->request->get('id');
        $url = $this->request->request->get('url', '/');
        $segment_id = null;
        if (\LPS\Config::SEGMENT_MODE == \LPS\Config::SEGMENT_MODE_LANGUAGE) {
            if (strpos($url, '|') !== false) {
                $url = explode('|', $url);
                $segment_id = !empty($url[1]) ? $url[1] : \App\Segment::getInstance()->getDefault()['id'];
                $url = $url[0];
            } else {
                $segment_id = $this->request->query->get('segment_id', $this->request->request->get('segment_id', \App\Segment::getInstance()->getDefault()['id']));
            }
        }
        if (empty($banner_id)) {
            $this->getAns()
                ->add('url', $url)
                ->setFormData(array(
                    'url[]' => array($url),
                    'segment_id' => $segment_id
                ));
        } else {
            $banner = B::getById($banner_id, $url);
            if (!empty($banner)){
                $this->getAns()
//                    ->add('banner_uri', is_array($uri) ? reset($uri) : $uri)
                    ->add('banner', $banner)
                    ->setFormData(array(
                        'id' => $banner['id'],
                        'destination' => $banner['destination'],
                        'url[]' => $banner['url'],
                        'title' => $banner['title'],
                        'description' => $banner['description'],
                        'date_start' => !empty($banner['date_start']) ? date('d.m.Y', strtotime($banner['date_start'])) : NULL,
                        'date_end' => !empty($banner['date_end']) ? date('d.m.Y', strtotime($banner['date_end'])) : NULL,
                        'seconds' => $banner['seconds'],
                        'active' => $banner['active']
                    ));
            }
        }
    }
    public function edit(){
        $errors = array();
        $s_id = $this->request->query->get('s', \LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE ? 1 : 0);
        $ans = $this->setJsonAns();
        $banner_id = $this->request->request->get('id');
        $params['url'] = $this->request->request->get('url');
        $params['image'] = $this->request->files->get('image');
        $destination = $this->request->request->get('destination');
        $params['seconds'] = $this->request->request->get('seconds');
        $params['segment_id'] = $this->request->request->get('segment_id');
        $active = $this->request->request->get('active');
        if (!is_null($active)){
            $params['active'] = !empty($active) ? 1 : 0;
        }
        if(!empty($destination)) {
            $destination = B::checkUrls(array($destination));
            $params['destination'] = is_array($destination) ? $destination[0] : $destination;
        }
        if (!empty($params['url'])){
            foreach ($params['url'] as $num => $url){
                if (empty($url)){
                    unset($params['url'][$num]);
                }
            }
        }
        if (empty($params['url'])){
            $params['url'] = array('/');
        }
        if (!empty($banner_id)){
            $banner = B::getById($banner_id, $this->request->request->get('url'));
        } elseif (!empty($params['image'])){
            $banner = B::create($params['image'], $params['url'], NULL, $errors);
        } else {
            $banner = NULL;
            $errors['image'] = 'empty';
        }
        if (empty($errors)){
            $date_start = $this->request->request->get('date_start');
            $date_end = $this->request->request->get('date_end');
            $params['date_start'] = !empty($date_start) ? strtotime($date_start) : NULL;
            $params['date_end'] = !empty($date_end) ? strtotime($date_end) : NULL;
            $banner->update($params, $errors);
        }
        if (!empty($errors)){
            $ans->setErrors($errors)->setEmptyContent();
        } else {
            B::clearRegistry();
            return $this->run('banners');
        }
    }
    /**
     * @ajax
     */
    public function delete(){
        $id = $this->request->request->get('id');
        $errors = array();
        if (empty($id)) {
            $errors['id'] = \Models\Validator::ERR_MSG_EMPTY;
        } else {
        $banner = B::getById($id);
            if (empty($banner)) {
                $errors['id'] = \Models\Validator::ERR_MSG_NOT_FOUND;
            } else {
                if (!$this->request->request->get('segment_id')) {
                    $this->request->request->set('segment_id', $banner['segment_id']);
                }
                B::delete($id, $error);
                if (!empty($error)) {
                    $errors['id'] = $error;
                }
            }
        }
        if (empty($errors) ){
            return $this->run('banners');
        }else{
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }
    /**
     * @ajax
     */
    public function activate(){
        $ans = $this->setJsonAns()->setEmptyContent();
        $active = $this->request->request->get('active');
        $id = $this->request->request->get('id');
        $banner = B::getById($id, $this->request->request->get('url', '/'));
        $result = $banner->update(array('active' => $active), $errors);
        if (!empty($errors)){
            $ans->setErrors($errors);
        } else {
            $ans->setStatus('ok');
        }
    }
    
    public function switchVisibility(){
        $errors = array();
        $id = $this->request->request->get('id');
        $banner = B::getById($id, $this->request->request->get('url'));
        if (is_null($banner)) {
            $errors[] = 'Баннер не найден';
        }
        $active = ($banner['active'] == 0) ? 1 : 0;
        $banner->update(array('active' => $active));
        $banner->save();
        if (empty($errors)) {
            return $this->run('banners');
        } else {
            $this->setJsonAns()->setEmptyContent()
                ->setErrors($errors);
        }
    }
    
    public function switchSortMode(){
        $errors = array();
        $id = $this->request->request->get('id');
        $banner = B::getById($id, $this->request->request->get('url'));
        if (is_null($banner)) {
            $errors[] = 'Баннер не найден';
        }
        $top = ($banner['top'] == 0) ? 1 : 0;
        $banner->update(array('top' => $top));
        $banner->save();
        if (empty($errors)) {
            return $this->run('banners');
        } else {
            $this->setJsonAns()->setEmptyContent()
                ->setErrors($errors);
        }
    }
}
