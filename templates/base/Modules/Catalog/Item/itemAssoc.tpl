{?$currentCatalog = $current_type->getCatalog()}
{if !empty($item_associated)}
	{foreach from=$item_associated item=assoc}
		<div class="prop-item-cont assoc-item">
			<div class="prop-item justify">
				<div class="check-col">
					<input type="checkbox" name="check[]" value="{$assoc.id}" class="check-item" />
				</div>
				<div class="title-col">
					<a href="/catalog-view/?id={$assoc.id}">{if !empty($assoc.title)}{$assoc.title}{else}No title{/if}</a>
				</div>
				<div class="delete-col">
					<i class="i-delete" data-id="{$assoc.id}" title="Удалить сопутствующий {$currentCatalog.nested_in ? $current_type.word_cases['i']['1']['i'] : $currentCatalog.word_cases['i']['1']['i']}"></i>
				</div>
			</div>
		</div>
	{/foreach}
{else}
	
{/if}