<?php
namespace LPS\Components\htmlPrepare;
/**
 * Производит базовые модицикации со страницой перед выводом
 * подготавливает страницу к выводу:
 * 1. в формах расставляет поумолчанию method = "POST" enctype= "multipart/form-data"
 * 2. в ссылках, которые внутри noindex ставит аттрибут rel=nofollow
 * 3. в дивах с аттрибутом image_max_width, ставит img-ам заданную ширину
 * @example $html=PagePersister::prepare($html);
 *
 */
class PagePersister extends \HTML_SemiParser{
    /**
     * @var ImagesResizer
     */
    protected $imagesResizer;
    /**
     * @var NoindexParser
     */
    protected $noindexParser;
	public function __construct(){
		$this->HTML_SemiParser();
		$this->sp_IGNORED = array('script', 'iframe', 'title');
    	$this->sp_SKIP_IGNORED = true;
    	$this->noindexParser = New NoindexParser();
    	$this->imagesResizer = New ImagesResizer(620);
    	$this->noindexParser->sp_IGNORED=$this->sp_IGNORED;
    	$this->noindexParser->sp_SKIP_IGNORED=$this->sp_SKIP_IGNORED;
    	$this->imagesResizer->sp_IGNORED=$this->sp_IGNORED;
    	$this->imagesResizer->sp_SKIP_IGNORED=$this->sp_SKIP_IGNORED;
    }

	function tag_form($attr) {
  		if (!isset($attr['method'])){
            $attr['method']='POST';
		}
		if (empty($attr['method']))
            unset($attr['method']);
		if (!isset($attr['enctype']) and $attr['method']=='POST'){
			$attr['enctype'] = 'multipart/form-data';
		}
		if (empty($attr['enctype']))
            unset($attr['enctype']);
		return $attr;
	}

	function tag_img($attr) {
  		if (!empty($attr['src'])){
			preg_replace('|^/?img/|', '/templates/img/', $attr['src']);
		}
		return $attr;
	}
	
    function container_noindex($attr)
    {
		$attr['_text'] = $this->noindexParser->process($attr['_text']);		
		return $attr;
    }
    function container_div($attr)
    {
        if (!empty($attr['image_max_width'])){
            $this->imagesResizer->setImageMaxWidth($attr['image_max_width']);
            $attr['_text'] = $this->imagesResizer->process($attr['_text']);
        }
		return $attr;
    }
}