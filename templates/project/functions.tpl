{function lang($ru_text, $en_text)}
	{if $request_segment.key == 'ru' || !$en_text}
		{return $ru_text}
	{else}
		{return $en_text}
	{/if}
{/function}