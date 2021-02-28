<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 22.04.15
 * Time: 15:50
 */

namespace Models\Seo;


class YandexMetrika extends SeoCounters{
    const PARAMS_KEY = 'seo_yandex_params';
    const CODE_KEY = 'seo_yandex_code';
    const INFORMER_KEY = 'seo_yandex_informer';
    protected static $i = NULL;
    private $cache = array();

    private $yandex_targets = NULL;

    protected function __construct(){
        parent::__construct();
        $this->public_params = array(self::CODE_KEY, self::INFORMER_KEY);
    }

    protected function validate($params, &$errors){
        if (!empty($params['enable'])){
            if (empty($params['id'])){
                $errors['id'] = 'empty';
            }
        }
        return empty($errors);
    }
    
    protected function compile($params){

        if (empty($params['enable'])){
            array(self::CODE_KEY => '', self::INFORMER_KEY => '');
            return '';
        }
        $ya_counter = "yaCounter{$params['id']} = new Ya.Metrika2({id:{$params['id']}"
            .(!empty($params['webvisor']) ? ', webvisor:true' : '')
            .(!empty($params['click_map']) ? ', clickmap:true' : '')
            .(!empty($params['track_links']) ? ', trackLinks:true' : '')
            .(!empty($params['denial']) ? ', accurateTrackBounce:true' : '')
            .(!empty($params['url_hash']) ? ', trackHash:true' : '')
            .(!empty($params['noindex']) ? ', ut:"noindex"' : '') .'});';
        $code = !empty($params['async'])
            ? '<!-- Yandex.Metrika counter --><script type="text/javascript">(function(d,w,c){(w[c]=w[c]||[]).push(function(){try{w.'
            : '<!-- Yandex.Metrika counter --><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript"></script><script type="text/javascript">try{var ';
        $code .= $ya_counter;
        $code .= !empty($params['async'])
            ? '}catch(e){}});var n=d.getElementsByTagName("script")[0],s=d.createElement("script"),f=function(){n.parentNode.insertBefore(s,n);};s.type="text/javascript";s.async=true;s.src="https://mc.yandex.ru/metrika/tag.js"";if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",f,false);}else{f();}})(document,window,"yandex_metrika_callbacks2");</script>'
            : '}catch(e){}</script>';
        $code .= (empty($params['xml_site']) ? '<noscript><div><img src="//mc.yandex.ru/watch/' . $params['id'] . '?ut=noindex" style="position:absolute; left:-9999px;" alt="" /></div></noscript>' : '');
        $code .= '<!-- /Yandex.Metrika counter -->'.PHP_EOL;
        $informer = !empty($params['informer'])
            ? "<!-- Yandex.Metrika informer --><a href=\"https://metrika.yandex.ru/stat/?id={$params['id']}&amp;from=informer\" target=\"_blank\" rel=\"nofollow\"><img src=\"//bs.yandex.ru/informer/{$params['id']}/3_1_FFFFFFFF_EFEFEFFF_0_pageviews\" style=\"width:88px; height:31px; border:0;\" alt=\"Яндекс.Метрика\" title=\"Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)\" onclick=\"try{Ya.Metrika.informer({i:this,id:{$params['id']},lang:'ru'});return false}catch(e){}\"/></a><!-- /Yandex.Metrika informer -->".PHP_EOL
            : '';
        //dump($code);
        return array(self::CODE_KEY => $code, self::INFORMER_KEY => $informer);
    }
/*
    protected function compile($params){
        if (empty($params['enable'])){
            array(self::CODE_KEY => '', self::INFORMER_KEY => '');
            return '';
        }
        $ya_counter = "yaCounter{$params['id']} = new Ya.Metrika({id:{$params['id']}"
            .(!empty($params['webvisor']) ? ', webvisor:true' : '')
            .(!empty($params['click_map']) ? ', clickmap:true' : '')
            .(!empty($params['track_links']) ? ', trackLinks:true' : '')
            .(!empty($params['denial']) ? ', accurateTrackBounce:true' : '')
            .(!empty($params['url_hash']) ? ', trackHash:true' : '')
            .(!empty($params['noindex']) ? ', ut:"noindex"' : '') .'});';
        $code = !empty($params['async'])
            ? '<!-- Yandex.Metrika counter --><script type="text/javascript">(function(d,w,c){(w[c]=w[c]||[]).push(function(){try{w.'
            : '<!-- Yandex.Metrika counter --><script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript"></script><script type="text/javascript">try{var ';
        $code .= $ya_counter;
        $code .= !empty($params['async'])
            ? '}catch(e){}});var n=d.getElementsByTagName("script")[0],s=d.createElement("script"),f=function(){n.parentNode.insertBefore(s,n);};s.type="text/javascript";s.async=true;s.src=(d.location.protocol=="https:"?"https:":"http:")+"//mc.yandex.ru/metrika/watch.js";if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",f,false);}else{f();}})(document,window,"yandex_metrika_callbacks");</script>'
            : '}catch(e){}</script>';
        $code .= (empty($params['xml_site']) ? '<noscript><div><img src="//mc.yandex.ru/watch/' . $params['id'] . '?ut=noindex" style="position:absolute; left:-9999px;" alt="" /></div></noscript>' : '');
        $code .= '<!-- /Yandex.Metrika counter -->'.PHP_EOL;
        $informer = !empty($params['informer'])
            ? "<!-- Yandex.Metrika informer --><a href=\"https://metrika.yandex.ru/stat/?id={$params['id']}&amp;from=informer\" target=\"_blank\" rel=\"nofollow\"><img src=\"//bs.yandex.ru/informer/{$params['id']}/3_1_FFFFFFFF_EFEFEFFF_0_pageviews\" style=\"width:88px; height:31px; border:0;\" alt=\"Яндекс.Метрика\" title=\"Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)\" onclick=\"try{Ya.Metrika.informer({i:this,id:{$params['id']},lang:'ru'});return false}catch(e){}\"/></a><!-- /Yandex.Metrika informer -->".PHP_EOL
            : '';
        return array(self::CODE_KEY => $code, self::INFORMER_KEY => $informer);
    }
*/
    public function getInformer(){
        return $this->config->get(self::INFORMER_KEY);
    }

    public function getData($offset){
        if (isset($this->yandex_targets[$offset])){
            if (!isset($this->cache[$offset])){
                $this->cache[$offset] = (!empty($this->counter_params['id']) && !empty($this->counter_params['enable']) && !empty($this->counter_params[$offset]))
                    ? str_replace('{id}', $this->counter_params['id'], $this->yandex_targets[$offset]['code'])
                    : '';
            }
            return $this->cache[$offset];
        } elseif ($offset == self::INFORMER_KEY){
            return $this->getInformer();
        } else {
            return parent::getData($offset);
        }
    }
}