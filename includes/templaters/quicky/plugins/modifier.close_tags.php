<?php
function quicky_modifier_close_tags($html)
{
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
	
	return preg_replace('~<[^>]+$~iU','',$html);
}
