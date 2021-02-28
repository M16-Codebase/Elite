{if !empty($cities)}
	{foreach from=$cities item=$city}
		<a href="{$city.url}">{$city.name}</a>
	{/foreach}
{/if}		