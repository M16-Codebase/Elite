<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.10.14
 * Time: 15:48
 */

namespace Modules\Seo;

use App\Configs\SeoConfig;
use \Models\Seo\SiteMap;

class SiteMapAdmin extends \LPS\AdminModule{

    public function index(){
        if ($this->getModule('Site\Config')->index(\Modules\Site\Config::PARAMS_KEY_SEO) !== NULL){
            return $this->redirect($this->getModuleUrl());
        }
        $this->getAns()
            ->add('allow_changefreq', SeoConfig::getParam('allow_changefreq'));
    }

    public function additionalRules(){
        $this->rulesList(TRUE);
    }

    public function rulesList($inner = FALSE){
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $ans->add('rules', SiteMap::getInstance()->getAdditionalUrlRules());
    }

    public function addRule(){
        SiteMap::getInstance()->addUrlRule($this->request->request->get('url'), $this->request->request->get('type'), $errors);
        if (empty($errors)){
            return $this->run('rulesList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function editRuleFields(){
        $ans = $this->setJsonAns();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $ans->setEmptyContent()->addErrorByKey('id', 'empty');
        } else {
            $rule = SiteMap::getInstance()->getUrlRuleById($id);
            if (empty($rule)){
                $ans->setEmptyContent()->addErrorByKey('id', 'not_found');
            } else {
                $ans->setFormData($rule)
                    ->add('rule', $rule);
            }
        }
    }

    public function editRule(){
        SiteMap::getInstance()->editUrlRule($this->request->request->get('id'), $this->request->request->get('url'), $this->request->request->get('type'), $errors);
        if (empty($errors)){
            return $this->run('rulesList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function deleteRule(){
        if (SiteMap::getInstance()->deleteUrlRule($this->request->request->get('ids'))) {
            return $this->run('rulesList');
        } else {
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('ids', \Models\Validator::ERR_MSG_EMPTY);
        }
    }

    public function allowUrls(){
        $this->getAns()
            ->add('priority_list', SeoConfig::getParam('priority_list'))
            ->add('default_priority', SeoConfig::CUSTOM_URLS_DEFAULT_PRIORITY);
        $this->allowUrlsList(true);
    }

    public function allowUrlsList($inner = false){
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $url_list = SiteMap::getInstance()->getUrlList();
        foreach($url_list as &$url){
            $url['timestamp'] = strtotime($url['last_modification']);
        }
        $ans->add('url_list', $url_list);
    }

    public function addAllowUrls(){
        $urls = array_filter(preg_split('/\s+/', $this->request->request->get('urls')));
        $form_data = \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
            'priority' => array('type' => 'checkString'),
            'date' => array('type' => 'checkString'),
            'time' => array('type' => 'checkString')
        ), $errors);
        if (empty($urls)){
            $errors['urls'] = 'empty';
        }
        if (empty($errors)){
            $last_modification = date('Y-m-d H:i:s', strtotime("${form_data['date']} ${form_data['time']}"));
            SiteMap::getInstance()->addUrls($urls, $form_data['priority'], $last_modification, $errors);
        }
        if (empty($errors)){
            return $this->run('allowUrlsList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function editAllowUrlFields(){
        $ans = $this->setJsonAns();
        $id = $this->request->request->get('id');
        if (empty($id)){
            $ans->setEmptyContent()->addErrorByKey('id', 'empty');
        } else {
            $url_data = SiteMap::getInstance()->getUrlById($id);
            if (empty($url_data)) {
                $ans->setEmptyContent()->addErrorByKey('id', 'not_found');
            } else {
                $ts = strtotime($url_data['last_modification']);
                $url_data['date'] = date('d.m.Y', $ts);
                $url_data['time'] = date('H:i:s', $ts);
                $ans->setFormData($url_data)
                    ->add('url', $url_data)
                    ->add('priority_list', SeoConfig::getParam('priority_list'));
            }
        }
    }

    public function editAllowUrl(){
        $form_data = \Models\Validator::getInstance($this->request)->checkFewResponseValues(array(
            'id' => array('type' => 'checkInt'),
            'url' => array('type' => 'checkString'),
            'priority' => array('type' => 'checkString'),
            'date' => array('type' => 'checkString'),
            'time' => array('type' => 'checkString')
        ), $errors);
        if (empty($errors)){
            $last_modification = date('Y-m-d H:i:s', strtotime("${form_data['date']} ${form_data['time']}"));
            SiteMap::getInstance()->editUrl(
                $form_data['id'],
                $form_data['url'],
                $form_data['priority'],
                $last_modification,
                $errors
            );
        }
        if (empty($errors)){
            return $this->run('allowUrlsList');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function deleteAllowUrls(){
        if (SiteMap::getInstance()->deleteUrls($this->request->request->get('ids'))){
            return $this->run('allowUrlsList');
        } else {
            $this->setJsonAns()->setEmptyContent()->addErrorByKey('ids', \Models\Validator::ERR_MSG_EMPTY);
        }
    }
} 