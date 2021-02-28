{if empty($currentCatalog)}
	{?$currentCatalog = $current_type->getCatalog()}
{/if}
{if $currentCatalog.nested_in}
	
	{?$child_types = array()}
	{foreach from=$types item=type}
		{if $type.nested_in}
			{if empty($child_types[$type.nested_in])}
				{?$child_types[$type.nested_in] = array()}
			{/if}
			{?$child_types[$type.nested_in][$type.id] = $type}
		{/if}
	{/foreach}
	{foreach from=$types item=type}
		{if empty($type.nested_in)}
			{include file="Modules/Catalog/Type/childTypesList.tpl" type=$type}
		{/if}
	{/foreach}
	
{else}
	
	{foreach from=$types item=type}
		{if $type.fixed && $accountType != 'SuperAdmin'}
			{?$type_unchangeable = 1}
		{else}
			{?$type_unchangeable = 0}
		{/if}
		{?$types_count = $type['counters']['all_types']}
		{?$items_count = $type['counters']['all_items']}
		<div class="wblock white-block-row{if $type_unchangeable} unchangeable{/if}" 
			data-allow-children="{$type['allow_children']}"
			data-auto-mult="{$type['only_items']}" 
			data-types-count="{$types_count}"
			data-items-count="{$items_count}"
			data-position="{$type['position']}"
			data-parent_id="{$current_type.id}"
			data-type_id="{$type['id']}"
		>
			<div class="w05 drag-drop{if $type_unchangeable} m-inactive{/if}">
				<input type="hidden" name="type_id" value="{$type['id']}" />
				<input type="hidden" name="parent_id" value="{$current_type.id}" />
				<input type="hidden" name="type_position" value="{$type['position']}" />
			</div>
			<label class="w05">
				<input type="checkbox" name="check[{$type['id']}]" class="check-item"{if $type_unchangeable} disabled{/if}/>
			</label>
			<a class="{if $current_type->isChildrenCanHasChildren()}w5{else}w6{/if}" href="/catalog-type/catalog/?id={$type['id']}">
				<span>{$type['title']}</span>
			</a>
            {if $current_type->isChildrenCanHasChildren()}
                <div class="w1">
                    <span class="type-counts{if $types_count==0} m-empty{/if}{if $type['status'] == 'hidden'} m-vis-hidden{/if}">{$types_count}</span>
                </div>
            {/if}
			<div class="w1">
				<span class="type-counts{if $items_count==0} m-empty{/if}{if $type['status'] == 'hidden'} m-vis-hidden{/if}">{$items_count}</span>
			</div>
			<div class="w1">
                {if !$currentCatalog.only_items}
                    <span class="type-counts{if empty($count_variants[$type['id']])} m-empty{/if}{if $type['status'] == 'hidden'} m-vis-hidden{/if}">{if !empty($count_variants[$type['id']])}{$count_variants[$type['id']]}{else}0{/if}</span>
                {/if}
			</div>
			<label class="w1">
				{if $accountType == 'SuperAdmin'}
					<input type="checkbox" name="fixed" class="fix-type" value="1"{if $type.fixed} checked{/if} />
				{/if}
			</label>
			<div class="action-button action-visibility w1 
				action-{if $type['status'] == 'hidden'}hide{else}show{/if}
				{if $account->isPermission('catalog-type', 'updateHidden')} m-active{else} m-inactive{/if}"
				title="{if $type['status'] == 'hidden'}Не отображается{else}Отображается{/if}">
				<i class="icon-{if $type['status'] == 'hidden'}hide{else}show{/if}"></i>
			</div>
			<div class="action-button action-edit edit-type w1 m-border{if $type_unchangeable} m-inactive{else} m-active{/if}" title="Редактировать">
				<i class="icon-edit"></i>
			</div>
		</div>
	{/foreach}
	
{/if}