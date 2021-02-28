<?php
namespace LPS;
/**
 * Отправляем запросы, пока только curl c POST
 *
 * @author olya
 */
class RequestSender {
    public static function make($url, $post_params = NULL, $get_params = NULL, $options = array(), $send_on_local = FALSE){
        if (\LPS\Config::isLocal() && !$send_on_local){
            return array('body' => 'Local');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//                curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, array_key_exists('followlocation', $options) ? $options['followlocation'] : TRUE);
        curl_setopt($ch, CURLOPT_HEADER, array('Content-Type: application/x-www-form-urlencoded'));
        if (!empty($post_params)){
            $post_string = http_build_query($post_params);
            $post_string = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $post_string);
            curl_setopt($ch,CURLOPT_POST, TRUE);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $post_string);
        }
        $return = array();
        $result = curl_exec($ch);
        if ($result){
            $return_tmp = explode("\r\n\r\n", $result);
            $return['headers'] = array_shift($return_tmp);
            $return['body'] = implode("\r\n\r\n", $return_tmp);
        }else{
            $return['error'] = curl_error($ch);            
        }
        $return['info'] = curl_getinfo($ch);
        curl_close($ch);
        return $return;
    }
}
