<table class="variantsList ribbed">	
	{if !empty($variants)}
		{foreach from=$variants item=var}
			<tr>
				<td class="td-title">
					<a href="/catalog-view/?id={$var.item_id}&var={$var.id}#view-variants">{if !empty($var.variant_title)}{$var.variant_title}{else}No title{/if}</a>					
				</td>
				<td><div class="small-descr">{$var.code}</div></td>
			</tr>
		{/foreach}
	{else}
		<tr>
			<td class="td-title">Пусто</td>
		</tr>
	{/if}
</table>