<?php
if (!function_exists('closetags')){
	function closetags ( $html ){    
		#put all opened tags into an array    
		preg_match_all ( "#<(?!(br|img|hr))([a-z]+)( .*)?(?!/)>#iU", $html, $result );    
		$openedtags = $result[2];     
		#put all closed tags into an array    
		preg_match_all ( "#</(?!(br|img|hr))([a-z]+)>#iU", $html, $result );    
		$closedtags = $result[2];    
	
		$len_opened = count ( $openedtags );    
		# all tags are closed    
		if( count ( $closedtags ) == $len_opened )    { return $html; }    
	
		$openedtags = array_reverse ( $openedtags );    
		# close tags    
		for( $i = 0; $i < $len_opened; $i++ )    {        
			if ( !in_array ( $openedtags[$i], $closedtags ) ) {
				$html .= "</" . $openedtags[$i] . ">";        
			} else {            
				unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );        
			}    
		}
		return $html;
	}
}
function quicky_modifier_cut($text, $add="", $alternative_len=200)
{
	$pattern='~<!-- pagebreak[^>]*-->~isU';
	if (preg_match($pattern, $text)){
		$chunks = preg_split($pattern, $text, 2);
		if (count($chunks) > 1) {
			$text = array_shift($chunks);
		}
	}else{
		$chunks = explode('.', $text);
		$text='';
		$i=0;
		do{
			$text.=$chunks[$i].'.';
			$i++;
		}while (isset($chunks[$i]) and strlen(strip_tags($text.$chunks[$i])) < $alternative_len);
		$text = closetags($text);
	}
	return trim(trim($text.$add, '.'));
}

?>