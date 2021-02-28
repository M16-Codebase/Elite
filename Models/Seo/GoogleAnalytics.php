<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 22.04.15
 * Time: 15:53
 */

namespace Models\Seo;


class GoogleAnalytics extends SeoCounters{
    const PARAMS_KEY = 'seo_google_params';
    const CODE_KEY = 'seo_google_code';
    protected static $i = NULL;
    private $cache = array();

    private $google_targets = NULL;

    protected function __construct(){
        parent::__construct();
        $this->google_targets = \App\Configs\SeoConfig::getParam('google_analytics_targets');
        $this->public_params = array_merge(array(self::CODE_KEY), array_keys($this->google_targets));
    }

    protected function validate($params, &$errors){
        if (!empty($params['enable'])){
            if (empty($params['mode'])){
                $errors['mode'] = 'empty';
            } elseif (!in_array($params['mode'], array('analytics', 'tagmanager'))){
                $errors['mode'] = \Models\Validator::ERR_MSG_INCORRECT_FORMAT;
            } elseif (empty($params[($params['mode'] == 'analytics' ? 'analytics_id' : 'tag_manager_id')])) {
                $errors[($params['mode'] == 'analytics' ? 'analytics_id' : 'tag_manager_id')] = 'empty';
            }
        }
        return empty($errors);
    }

    protected function compile($params){
        if (empty($params['enable'])){
            return array(self::CODE_KEY => '');
        } else {
            if ($params['mode'] == 'analytics'){
                $code = "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create','{$params['analytics_id']}','auto');ga('send', 'pageview');</script>".PHP_EOL;
            } else {
                $code = "<!-- Google Tag Manager --><noscript><iframe src=\"//www.googletagmanager.com/ns.html?id={$params['tag_manager_id']}\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript><script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$params['tag_manager_id']}');</script><!-- End Google Tag Manager -->".PHP_EOL;
            }
            return array(self::CODE_KEY => $code);
        }
    }

    /**
     * @param $offset
     * @return string
     * @throws \LogicException
     */
    public function getData($offset){
        if ($this->counter_params['enable'] == 0) {
            return null;
        }
        if (isset($this->google_targets[$offset])){
            if (!isset($this->cache[$offset])){
                $this->cache[$offset] = (!empty($this->counter_params['enable']) && !empty($this->counter_params[$offset]))
                    ? $this->google_targets[$offset]['code']
                    : '';
            }
            return $this->cache[$offset];
        } elseif ($offset == 'mode') {
            return $this->counter_params[$offset];
        } else {
            return parent::getData($offset);
        }

    }
}