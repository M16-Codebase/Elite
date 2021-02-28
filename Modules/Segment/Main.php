<?php
/**
 * Description of Index
 *
 * @author olga
 */
namespace Modules\Segment;
use Symfony\Component\HttpFoundation\Cookie;
class Main extends \LPS\WebModule{
    public function index(){
        return $this->notFound();
    }
    /**
     * проверяем, установлен ли регион и правильный ли урл для установленного региона
     * @ajax
     */
    public function check(){
        $default_id = NULL;
        $default = \Models\Segment::getCookieSegment();
        if (!empty($default)){
            $default_id = $default['id'];
        }
        $segment_router_id = $this->request->request->get('reg_id', $this->request->query->get('reg_id'));
        if (empty($default_id) || (!empty($segment_router_id) && $default_id != $segment_router_id)){
            $geoIp = new \SxGeo(\LPS\Config::getRealDocumentRoot() . '/includes/SypexGeo/SxGeoCity.dat');
            $ip = $this->request->getClientIp();
            $city = $geoIp->getCity($ip);
            $geoDef = !empty($city['city']) ? \Models\Segment::getByTitle($city['city']) : NULL;
            return json_encode(array('status' => 'error', 'cookie_id' => $default_id, 'url_id' => $segment_router_id, 'def_id' => !empty($geoDef) ? $geoDef['id'] : NULL));
        }
        return json_encode(array('status' => 'ok'));
    }
    /**
     * Сменить установленный для сайта сегмент
     * @return type
     */
    public function change(){
//        $domain = \LPS\Config::getParametr('site', 'domain');
//        $path = '/';
//        $expire = time() + 30 * 24 * 3600;
        $segment_key = $this->request->request->get('key', $this->request->query->get('key'));
        $segment_controller = \App\Segment::getInstance();
        $segment = $segment_controller->getByKey($segment_key);
        $server_referer = $this->request->server->get('HTTP_REFERER');
        if (empty($server_referer)){
            $server_referer = '/';
        }
        if (empty($segment)){
            return $this->redirect($server_referer);
        }
        if (empty($segment)){
            $this->response = $this->redirect($server_referer);
//            $this->response->headers->setCookie(new Cookie('segment_id', NULL, time()-1, $path, $domain));
        }else{
            $segments = \App\Segment::getInstance()->getAll();
            if (!empty($segments)){
                foreach ($segments as $id => $r){
                    $keys[$id] = $r['key'];
                }
            }
            $str = '\/('.implode('|', $keys).')\/';
            if (preg_match('~'.$str.'~', $server_referer, $out)){
                $referer_url = preg_replace('~'.$str.'~', $segment['key'] == $segment_controller->getDefaultKey() ? '/' : ('/' . $segment['key'] . '/'), $server_referer);
            }else{
                $url_array = parse_url($server_referer);
                if (empty($url_array['host'])){
                    $referer_url = '/';
                }else{
                    $referer_url = 
                        (!empty($url_array['scheme']) ? ($url_array['scheme'] . '://') : '') . 
                        $url_array['host'] .  
                        ($segment_key == $segment_controller->getDefaultKey() ? '' : ('/' . $segment['key'])) . 
                        (!empty($url_array['path']) && $url_array['path'] != '/' ? ('/' . $url_array['path']) : '') . 
                        (!empty($url_array['query']) ? ('?' . $url_array['query']) : '') . 
                        (!empty($url_array['fragment']) ? ('#' . $url_array['fragment']) : '');
                }
            }
            $this->response = $this->redirect(str_replace('//', '/', preg_replace('~http:\/\/[^\/]*~', '', $referer_url)));
//            $this->response->headers->setCookie(new Cookie('segment_id', !empty($segment) ? $segment['id'] : NULL, $expire, $path, $domain));
        }
        return $this->response;
    }
}