<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 09.10.2017
 * Time: 20:15
 */

namespace App;


class Redirect {

    private $actionMark = '$i';
    private $uriSeparator = '://';
    private $request;
    private $requestStr;

    private $redirectMap = [
        '/real-estate/complex/$i' => '/real-estate/$i',
    ];

    public function __construct($request){
        if (! $request instanceof \Symfony\Component\HttpFoundation\Request){
            return false;
        }
        $this->request = $request;
        $this->requestStr = $request->getRequestUri();
        $this->scanRequest();
    }


    private function scanRequest() {
        //$request = trim($this->request, '/');
        foreach ($this->redirectMap as $key => $variant) {
            $key = str_replace($this->actionMark, '', $key);
            if (substr_count($this->requestStr, $key) === 1) {
                $action = str_replace($key, '', $this->requestStr);
                $url = str_replace($this->actionMark, $action, $variant);
                $this->makeRedirect($url);
            }
        }
    }

    private function explodeUri($uri) {
        return explode('/', trim($uri, '/'));
    }

    private function makeRedirect($url) {
        $scheme = $this->request->server->get('REQUEST_SCHEME');
        $host = $this->request->server->get('HTTP_HOST');

        $fullUri = $scheme . $this->uriSeparator . $host . $url;
		header("HTTP/1.1 301 Moved Permanently"); 
        header("Location: {$fullUri}");
        exit();
    }

}