<?php
namespace Modules\Posts;
use Models\ContentManagement\Post;
use Models\ContentManagement\Theme;
use Models\Logger;

class PagesTheme extends Pages{
    const POSTS_TYPE = 'pages';
    const MIN_TITLE_CHARS = 3;
    const MIN_TEXT_CHARS = 10;
    const PAGE_SIZE = 10;
    public function index(){
        if ($this->request->request->has('ajax')){
            $ans = $this->setJsonAns();
            if ($redirect = $this->request->request->get('redirect')){
                $ans->addData('url', $redirect);
            }
        }
        $segment_id = $this->request->query->get('s');
        if (empty($segment_id)){
            $default_segment = \App\Segment::getInstance()->getDefault(true);
            $segment_id = $default_segment['id'];
        }
        $current_theme_id = $this->request->request->get('theme', $this->request->query->get('theme'));
        $theme_manager = Theme::getInstance();
        $search_params = array('post_type' => static::POSTS_TYPE);
        if (empty($current_theme_id)){
            $search_params['empty_parent'] = true;
        }else{
            $current_theme = $theme_manager->getById($current_theme_id);
            if (empty($current_theme)){
                return $this->notFound();
            } else{
                $this->getAns()
                    ->add('current_theme', $current_theme);
                if ($current_theme['count']){
                    $page = $this->request->query->get('page', 1);
                    if ($page < 1){
                        return $this->redirect($this->getModuleUrl());
                    }
                    $params = array(
                        'type' => static::POSTS_TYPE,
                        'theme_id' => $current_theme_id,
                        'status' => array(Post::STATUS_CLOSE, Post::STATUS_NEW, Post::STATUS_PUBLIC, Post::STATUS_HIDDEN)
                    );
                    $posts = Post::search($params, $count, ($page-1)*static::PAGE_SIZE, static::PAGE_SIZE, 'num');
                    $this->getAns()
                        ->add('posts', $posts)
                        ->add('count', $count)
                        ->add('current_theme_id', $current_theme_id)
                        ->add('pageSize', static::PAGE_SIZE)
                        ->add('pageNum', $page);
                }
            }
            $search_params['parent_id'] = $current_theme_id;
        }
        $themes = $theme_manager->search($search_params);
        $themes_level = \Models\SiteConfigManager::getInstance()->get(\App\Configs\Settings::KEY_THEMES_LEVEL_COUNT);
        if (!empty($themes_level)){
            $this->getAns()->add('parent_themes', $theme_manager->search(array('post_type' => static::POSTS_TYPE, 'top_level' => TRUE)));
        }
        $this->getAns()
            ->add('themes', $themes)
            ->add('themes_level',$themes_level)
            ->add('only_posts_create', !empty($current_theme) && $current_theme['child_level'] >= $themes_level);
    }
    public function postsList($inner = false){
        $this->request->request->set('ajax', 1);
        return $this->run('index');
    }

    public function createTheme(){
        $errors = array();
        $title = $this->request->request->get('title');
        $parent_id = $this->request->request->get('parent_id');
        $theme_manager = Theme::getInstance();
        if (!empty($parent_id)){
            $parent_theme = $theme_manager->getById($parent_id);
            if (empty($parent_theme)){
                $errors['parent_id'] = 'not_found';
            } elseif ($parent_theme['count']){
                $errors['parent_theme'] = 'has_posts';
            }
        }
        if (empty($errors)){
            $theme_id = $theme_manager->create($title, !empty($parent_id) ? $parent_id : NULL, static::POSTS_TYPE, $errors);
        }
        if (empty($errors)){
            $this->request->request->set('ajax', 1);
            $this->request->request->set('theme', (!empty($parent_id) ? $parent_id : NULL));
            return $this->run('index');
        } else {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
    }

    public function editTheme(){
        $themeManager = Theme::getInstance();
        $theme = $themeManager->getById($this->request->query->get('id'));
        if (empty($theme))
            return $this->notFound();
        $post_types = $themeManager->getPostTypes();
        $data = $this->request->request->all();
        $fields=array('parent_id', 'title', 'hide', 'show', 'keyword');
        $list_fields =  array('hide', 'show');
        $segment = \App\Segment::getInstance()->getDefault(true);
        if (!empty($data)){			//сохраняем
            $theme_manager = Theme::getInstance();
            if (!empty($data['parent_id'])){
                $parent_theme = $theme_manager->getById($data['parent_id']);
                if (empty($parent_theme)){
                    $errors['parent_id'] = 'not_found';
                } elseif ($parent_theme['count']){
                    $errors['parent_theme'] = 'has_posts';
                }
            }
            if (empty($data['keyword']) && empty($theme['keyword']))
                $data['keyword'] = \LPS\Components\Translit::Supertag(is_array($data['title']) ? (isset($data['title'][$segment['id']]) ? $data['title'][$segment['id']] : reset($data['title'])) : $data['title']);
            $data['show'] = '.'.static::POSTS_TYPE.'.';
            foreach ($data as $k => $v){
                if (!in_array($k, $fields)){
                    unset($data[$k]);
                }
            }
            $themeManager->edit($theme['id'], $data, $errors);
            if (empty($errors)){
                $main_title = is_array($theme['title']) ? (isset($theme['title'][$segment['id']]) ? $theme['title'][$segment['id']] : reset($theme['title'])) : $theme['title'];
                foreach ($fields as $field){
                    if ($field == 'title' && \LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
                        continue;
                    }
                    if (isset($data[$field]) && $theme[$field] != $data[$field]){
                        Logger::add(array(
                            'type' => Logger::LOG_TYPE_EDIT,
                            'entity_type' => 'post_theme',
                            'entity_id' => $theme['id'],
                            'attr_id' => $field,
                            'comment' => $data[$field],
                            'additional_data' => array('title' => $main_title, 'type' => static::POSTS_TYPE)
                        ));
                    }
                }
                if ($field == 'title' && \LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE){
                    foreach($data['title'] as $s_id => $title){
                        if (empty($theme['title'][$s_id]) && !empty($title) || $title != $theme['title'][$s_id]){
                            Logger::add(array(
                                'type' => Logger::LOG_TYPE_EDIT,
                                'entity_type' => 'post_theme',
                                'entity_id' => $theme['id'],
                                'attr_id' => "title[$s_id]",
                                'comment' => $data['title'][$s_id],
                                'additional_data' => array('title' => $main_title, 'type' => static::POSTS_TYPE)
                            ));
                        }
                    }
                }
                $this->request->request->set('theme', (!empty($parent_theme) ? '?theme=' . $parent_theme['id'] : ''));
                $this->request->request->set('ajax', 1);
                return $this->run('index');
            } else {
                $this->setJsonAns()->setEmptyContent()->setErrors($errors);
            }
        } else {		//выводим
            foreach ($theme as $k => $v){ // "Заполняем" формочку
                if (!isset($_POST[$k])) {
                    if (in_array($k, $list_fields)){
                        $theme[$k]=explode('.',trim($theme[$k],'.'));
                    }
                    $_POST[$k]=$theme[$k];
                }
            }
            $themes_level = \Models\SiteConfigManager::getInstance()->get(\App\Configs\Settings::KEY_THEMES_LEVEL_COUNT);
            $this->setJsonAns()
                ->add('theme_data',$theme)
                ->add('post_types',$post_types)
                ->add('themes_level',$themes_level);
//                ->setFormData($theme);
            if (!empty($themes_level)){
                $this->getAns()->add('parent_themes', Theme::getInstance()->search(array('not_id' => $theme['id'], 'not_children' => $theme['id'], 'post_type' => static::POSTS_TYPE, 'empty_posts' => TRUE, 'max_level' => $themes_level)));
            }
        }
    }

    public function moveTheme(){
//        $this->setJsonAns()->setTemplate('Modules/Posts/PagesTheme/theme_list_element.tpl');
        $theme_id = $this->request->request->get('id');
        $new_position = $this->request->request->get('position');
        if ($theme_id !== NULL && $new_position !== NULL){
            $themeManager = Theme::getInstance();
            $themeManager->move($theme_id, $new_position);
            $theme = $themeManager->getById($theme_id);
            Logger::add(array(
                'type' => Logger::LOG_TYPE_EDIT,
                'entity_type' => 'post_theme',
                'entity_id' => $theme_id,
                'attr_id' => 'position',
                'comment' => $new_position,
                'additional_data' => array('title' => $theme['title'], 'type' => static::POSTS_TYPE)
            ));
            $this->request->request->set('theme', $theme['parent_id']);
            $this->request->request->set('ajax', 1);
            return $this->run('index');
        } else {
            $errors = array();
            if (empty($theme_id)){
                $errors['id'] = 'empty';
            }
            if (empty($new_position)){
                $errors['position'] = 'empty';
            }
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        }
//        $themes = Theme::getInstance()->search(array('post_type' => static::POSTS_TYPE), TRUE);
//        $this->getAns()
//            ->add('themes_list', $themes[0])
//            ->add('moduleUrl', $this->getModuleUrl())
//        ;
    }

    public function createPostFields(){
        $ans = $this->setJsonAns();
        $theme_id = $this->request->query->get('theme');
        if (empty($theme_id)){
            $ans->setEmptyContent()->addErrorByKey('theme', 'empty');
        } else {
            $theme = Theme::getInstance()->getById($theme_id);
            if (empty($theme)){
                $ans->setEmptyContent()->addErrorByKey('theme', 'not_found');
            } else {
                $ans->add('current_theme', $theme)
                    ->add('current_theme_id', $theme_id);
                parent::createPostFields();
            }
        }
    }

    public function createThemeFields(){
        $ans = $this->setJsonAns();
        $theme_id = $this->request->query->get('theme');
        if (!empty($theme_id)){
            $theme = Theme::getInstance()->getById($theme_id);
            if (empty($theme)){
                $ans->setEmptyContent()->addErrorByKey('theme', 'not_found');
            } else {
                $ans->add('current_theme', $theme)
                    ->add('current_theme_id', $theme_id);
            }
        }
    }

    public function delTheme(){
        $errors = array();
        $theme_manager = Theme::getInstance();
        $theme_id = $this->request->request->get('id', $this->request->query->get('id'));
        if (empty($theme_id)){
            $errors['theme_id'] = 'empty';
        } else {
            $theme = $theme_manager->getById($theme_id);
            if (empty($theme)){
                $errors['theme_id'] = 'not_found';
            } elseif (!empty($theme['theme_count']) || !empty($theme['count'])){
                $errors['theme'] = 'not_empty';
            } else {
                $theme_manager->delete($theme_id);
            }
        }
        if (!empty($errors)){
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            $this->request->request->set('ajax', 1);
            $this->request->request->set('theme', (!empty($theme['parent_id']) ? $theme['parent_id'] : NULL));
            return $this->run('index');
        }
    }

    public function editPost($inner = FALSE){
        $this->getAns()
            ->add('themes', Theme::getInstance()->search(array('post_type' => static::POSTS_TYPE, 'empty_child_themes' => TRUE)));
        return parent::editPost($inner);
    }

    /**
     * @param string $action
     * @param Post|\Models\ContentManagement\SegmentPost|null $post
     * @return string
     */
    protected function getRedirectUrl($action, $post = NULL){
        if ($action == 'del' && !empty($post)){
            $this->request->request->set('ajax', 1);
            $this->request->query->set('theme', $post['theme_id']);
            return $this->getModuleUrl().'?theme=' . $post['theme_id'] . (\LPS\Config::SEGMENT_MODE != \LPS\Config::SEGMENT_MODE_NONE && !empty($post['segment_id']) ? '&s='.$post['segment_id'] : '');
        }
        return parent::getRedirectUrl($action, $post);
    }
}