{if !empty($variant)}
	{?$cover = $item.cover_view}
	<div class="wblock white-block-row">
		<div class="w2">
			<input type="hidden" name="variant_id" value="{$variant.id}" />
			{if !empty($cover)}
				<a href="{$cover->getUrl()}" class="row-cover fancybox">
					<img src="{$cover->getUrl(70, 70)}" alt="{$variant.title}" />
				</a>
			{/if}
		</div>
		<div class="w9">
			<a href="{$variant->getUrl()}" target="_blank">{$item.title} {$variant.variant_title}</a> <span class="descr">— {$variant.id}</span>
			{if !empty($variant.price_variant)}
				<div>{$variant.price_variant|price_format}</div>
			{/if}
		</div>
		<div class="w1 action-button action-add" title="Добавить к заказу"><i class="icon-add"></i></div>
	</div>		
{/if}