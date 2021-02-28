<?php
namespace LPS\Components\htmlPrepare;
/**
 * в ссылках ставит аттрибут rel=nofollow
 *
 */
class NoindexParser extends \HTML_SemiParser{
	function __construct(){
		$this->HTML_SemiParser();
    }
    
    function tag_a($attr) {
  		$attr['rel']='nofollow';
		return $attr;
	}
}