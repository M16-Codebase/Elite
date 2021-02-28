<?php
namespace LPS\Components\htmlPrepare;
/**
 * проставляет у всех картинок размеры не более заданного и оборачивает картинку в ссылку
 *
 */
class ImagesResizer extends \HTML_SemiParser{
    protected $maxImageWidth;
	function __construct($maxImageWidth){
	    $this->maxImageWidth=$maxImageWidth;
		$this->HTML_SemiParser();
    }
    
	function tag_img($attr) {
	    $src=$attr['src'];
	    if (isset($attr['width']) and intval($attr['width']) > $this->maxImageWidth){
            unset($attr['width']);
            unset($attr['height']);
			$attr['width']=$this->maxImageWidth-2;
			$attr['_left']='<a href="'.$attr['src'].'" target="_blank">';
			$attr['_right']='</a>';
	    }
	    if (!isset($attr['width']))
	       unset($attr['height']);
		return $attr;
	}
	
	function setImageMaxWidth($maxImageWidth){
        $this->maxImageWidth=$maxImageWidth;
	}
}