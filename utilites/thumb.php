<?php
/*
 * Принимаются такие параметры:
 * w - высота картинки, задается в пикселях
 * h - высота картинки, задается в пикселях
 * q - качество в jpeg
 * p - позиционирование при смещении пропорций
 * full_name - путь к исходному файлу от точки $config['source']
 * fltr - фильтры
brit|{parameter}  	яркость (-100 - 100)
cont|{parameter} 	контраст (-100 - 100)
ds|{parameter}      desaturated (100 или любое)
sat|{parameter} 	saturation (-100 или любое)
gray                grayscale
clr|{amount}|{color}	colorized
amount(0 - 100)         интенсивность
color(hex)              в какой цвет красить
sep|{amount}|{color}	sepia amount(0 - 100; def 80), color ?
gam|{amount}            гамма (0.001 - 10)
neg
th|{amount}             Threshold (0 - 255)
rcd|{colors}|{dither}	ReduceColorDepth
colors (0 - 8, def 256 ?)
dither(bool, def true)
flip|{parameter}        перевернуть (x,y,xy)
edge|{parameter}        Edge Detect (def 2)
emb|{parameter}     	Emboss (def 2)
lvl|{band}|{method}|{threshold}
    band (RGBA\)
    method (default 2). =0 если используется фильтр wb
        0 - internal RGB
        1 - internal grayscale
        2 - ImageMagick "contrast-stretch"
        3 - ImageMagick "normalize"
    threshold (0, 100, def 0.1)
wb|{$threshold}     white balance (0 - 100, def 0.1)
blur|{radius}		(0 - 25, default 1)
gblr|{radius}		gaussian blur (0 - 25, default 1)
usm|{amount}|{radius}|{threshold}	unsharp masking
    amount(0-255, def 80) 
    radius (0 - 10, def 0.5) 
    threshold (0 - 50, def 3)
bord|{width}|{radiusX}|{radiusY}|{color}	border
crop|{left}|{right}|{top}|{bottom}		crop filter
    sblr	selective blur
    mean	Mean Removal
    smth	Smooth
bvl|{width}|{colorTL}|{colorBR}	bevel edge
wmi|{image_url}|{position}|{opacity}|{marginX}|{marginY}|{rotation} watermark. position(L|R|T|B|TR|TL|BR|BL|C или * или absolute “XxY”) в шаблоне можно задавать все параметры кроме адреса картинки. если не задать параметры, то по умолчанию - BR|50|10|10|0
wmt|{text}|{size}|{position}|{color}|{font}|{?}|{?}|{?}|{bg_color}|{bg_transp}|{bg_position(x|y)} text overlay
over|{image_url}|{over\under}	Overlay\Underlay
hist		histograms of RGB
fram|{width1}|{width2}|{color1}|{color2}|{color3}
drop			Drop shadow (drop|5|10|000000|225)
mask|{image_url}	накладывает маску-изображение
elip
ric|{radiusX}|{radiusY}	curved border corners
stc 	сreate transparency from source image color (stc|FFFFFF|5|10). не забыть f=png или gif
size

 */
    define('NEED_CACHE',FALSE);
    define('HASH_SOLT_IMAGE_STRING', 'IMAGES SECURITY STRING');//такая же в ImageManagement/Image.php
    define('DEVELOP_STAGE', FALSE);
    define('SOURCE_SHORT_PATH', '/data/images/');
    define('OUTPUT_SHORT_PATH', '/data/thumbs/');
    require_once ('../Config.php'); // Config Load
//   error_reporting(E_ALL ^ E_NOTICE);
//    ini_set('display_errors', 1);
    if (empty ($_GET['full_name'])){
        NotFound('Image name not found');
    }
    $config = array(
        'source' => $_SERVER['DOCUMENT_ROOT'].SOURCE_SHORT_PATH, //папка в которой ищется путь full_name
        'output' => $_SERVER['DOCUMENT_ROOT'].OUTPUT_SHORT_PATH,  //папка кеша
        'watermark' => '/templates/img/watermark.png',
        'watermark_params' => 'BR|50|10|10|0'
    );
    //Возможные положения позиционирования если используется ImageMagic
    $positions = array(
        'N' => FALSE, //gravity north
        'T' => 'T', //gravity north
        'B' => 'B',  //gravity south
        'L' => 'L',  //gravity west
        'R' => 'R',  //gravity east
        'TL' => 'TL', //gravity northwest
        'TR' => 'TR', //gravity northeast
        'BL' => 'BL', //gravity southwest
        'BR' => 'BR',  //gravity southeast
        'C'  => 'C'  //gravity center
    );
    date_default_timezone_set('Europe/Moscow');
    require_once('../includes/phpThumb/phpthumb.class.php');

    function NotFound($msg = "404 Not Found") {
        Header("HTTP/1.0 404 Not Found");
        exit($msg);
    }
    function makeDirs($path, $mode = 0777, $set_umask=true) {
        if ($set_umask){
            $old_umask = umask(0);
        }
        if (!file_exists($path)){
            makeDirs(dirname($path), $mode, false);
        clearstatcache();
        if (!file_exists($path))
            @mkdir($path, $mode);
        }
        if ($set_umask){
           umask($old_umask);
           clearstatcache();
        }
    }
    function get_mime_type($file)
    {
        // our list of mime types
        $mime_types = array(
                "pdf"=>"application/pdf",
                "exe"=>"application/octet-stream",
                "zip"=>"application/zip",
                "docx"=>"application/msword",
                "doc"=>"application/msword",
                "xls"=>"application/vnd.ms-excel",
                "ppt"=>"application/vnd.ms-powerpoint",
                "gif"=>"image/gif",
                "png"=>"image/png",
                "jpeg"=>"image/jpg",
                "jpg"=>"image/jpg",
                "mp3"=>"audio/mpeg",
                "wav"=>"audio/x-wav",
                "mpeg"=>"video/mpeg",
                "mpg"=>"video/mpeg",
                "mpe"=>"video/mpeg",
                "mov"=>"video/quicktime",
                "avi"=>"video/x-msvideo",
                "3gp"=>"video/3gpp",
                "css"=>"text/css",
                "jsc"=>"application/javascript",
                "js"=>"application/javascript",
                "php"=>"text/html",
                "htm"=>"text/html",
                "html"=>"text/html",
        );
        $file_tokens = explode('.', $file);
        $extension = strtolower(end($file_tokens));
        return isset($mime_types[$extension]) ? $mime_types[$extension] : false;
    }

    $thumb_params = array('source'=>$_GET['full_name']);
    $thumb_params['thumb_width']  = !empty($_GET['w']) ? $_GET['w'] : NULL;
    $thumb_params['thumb_height'] = !empty($_GET['h']) ? $_GET['h'] : NULL;
    $thumb_params['position'] = !empty($_GET['p']) ? $_GET['p'] : NULL;
    $thumb_params['f'] = !empty($_GET['f']) ? $_GET['f'] : NULL;
    $thumb_params['fltr'] = !empty($_GET['fltr']) ? str_replace('-', '|', explode('_', $_GET['fltr'])) : array();
    $str = (!empty($thumb_params['thumb_width'])? $thumb_params['thumb_width'] : '') . 
            (!empty($thumb_params['thumb_height']) ? $thumb_params['thumb_height'] : '') . 
            (!empty($thumb_params['position']) ? $thumb_params['position'] : '') . 
            (!empty($thumb_params['f']) ? $thumb_params['f'] : '') . 
            (!empty( $thumb_params['fltr']) ? serialize($thumb_params['fltr']) : '') . 
            SOURCE_SHORT_PATH . $_GET['full_name'] .
            HASH_SOLT_IMAGE_STRING;
    $hash = substr(md5($str), 0, 6);
    if (!DEVELOP_STAGE && $hash != $_GET['hash']){
        NotFound('Используются запрещенные параметры');
    }
    if (!empty($thumb_params['fltr'])){
        foreach ($thumb_params['fltr'] as &$fltr){
            if (strpos($fltr, 'wmi') === 0){
                $params = substr($fltr, 3);
                $fltr = 'wmi|' . $config['watermark'] . (empty($params) ? ('|' . $config['watermark_params']) : $params);
            }
        }
    }
    if (!file_exists($config['output'])){
        makeDirs($config['output']);
        if (!file_exists($config['output'])){
            NotFound('can\'t create thumb files directory ' . $config['output']);
        }
    }
    $source = realpath($config['source'].$_GET['full_name']);
    if (!file_exists($source))
        NotFound('File ' . $source . ' not exists');
    $sub_path =
       (!empty($thumb_params['thumb_width']) ? 'w'.$thumb_params['thumb_width']  : '').
       (!empty($thumb_params['thumb_height']) ? 'h'.$thumb_params['thumb_height'] : '').
       (!empty($thumb_params['position']) ? ''.$thumb_params['position'] : '').
        (!empty($thumb_params['fltr']) ? str_replace('|', '', str_replace('/', '', 'f'.implode('-', $thumb_params['fltr']))) : '').
        (!empty($thumb_params['f']) ? ''.$thumb_params['f'] : '');
    if (empty($sub_path))
       NotFound('no params');
    $newfile = $config['output'] . $sub_path . '/' . $_GET['full_name'];
    makeDirs(dirname($newfile));
    if (!file_exists($newfile) or @filemtime($source) > @filemtime($newfile)) {
        header("Thumb-Create: server"); //debug
        $phpThumb = new phpThumb();
        $phpThumb->config_imagemagick_path = LPS\Config::isWin() ? LPS\Config::IMAGEMAGICK_PATH_WINDOWS : LPS\Config::IMAGEMAGICK_PATH;// /usr/bin/convert или 'C:/imagemagick/convert.exe' для винды
        $phpThumb->setSourceFilename($source);
        $phpThumb->setParameter('w', $thumb_params['thumb_width']);
        $phpThumb->setParameter('h', $thumb_params['thumb_height']);
        $phpThumb->setParameter('zc', $positions[!empty($thumb_params['position']) ? $thumb_params['position'] : 'N']);
        $phpThumb->setParameter('fltr', $thumb_params['fltr']);
        $phpThumb->setParameter('q', 90);
        if (!empty($thumb_params['f'])){
            $phpThumb->setParameter('f', $thumb_params['f']);
        }
        if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
            $phpThumb->RenderToFile($newfile);
            @touch($newfile, @filemtime($source));
            @chmod($newfile, 0666);
            clearstatcache();
        } else {
            header('Content-Type: text/plain');
            echo 'Failed:<pre>'.implode("\n\n", $phpThumb->debugmessages).'</pre>';
            exit;
        }
    }
	
	$headers = getallheaders();
	if (strpos($headers['Accept'], 'image/webp') !== false && (get_mime_type($newfile)=='image/jpg'||get_mime_type($newfile)=='image/jpeg')) { 
		header('Content-Type: image/webp');
		if(!file_exists($newfile.'.webp')){

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://m16-elite.ru/webp-on-demand.php?source=".$newfile);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			curl_close($ch);
			
			$newfile = $newfile.'.webp';
			header('Server-source: script');
			header('Content-Length: '.filesize($newfile));
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', @filemtime($newfile)).' GMT');
			header('Cache-control: public');
			readfile($newfile);
		
		}else{
			header("Cache-control: public");
			header("Expires: " . gmdate("D, d M Y H:i:s", time() + 7*60*60*24) . " GMT");
			$newfile = $newfile.'.webp';
			header('Server-source: script');
			header('Content-Length: '.filesize($newfile));
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', @filemtime($newfile)).' GMT');
			header('Cache-control: public');
			readfile($newfile);
		}
		
		

	}else{
		header('Content-Type: '.get_mime_type($newfile));
		if (!file_exists($newfile))
			NotFound('some errors for create this file');
		if (NEED_CACHE){//вообще кеширование должно быть настроено уровнем выше - в веб сервере
			$allheaders = getallheaders();
			$if_modified = !empty($allheaders["If-Modified-Since"]) ? strtotime($allheaders["If-Modified-Since"]) : 0;
			if ($if_modified >= @filemtime($newfile)) {
				header("HTTP/1.0 304 Not Modified");
				exit();
			}
		}
		
		header("Cache-control: public");
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + 7*60*60*24) . " GMT");	
		//header('File-Path: '.$newfile); //debug
		header('Server-source: script');
		header('Content-Length: '.filesize($newfile));
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', @filemtime($newfile)).' GMT');
		header('Cache-control: public');
		readfile($newfile);
		
		
	}
	
	



	


?>