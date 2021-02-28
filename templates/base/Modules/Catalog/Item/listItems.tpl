{?$current_type_filter = ''}
{?$currentCatalog = $current_type->getCatalog()}
<div class="white-blocks">
	{*if empty($currentCatalog.nested_in) || !empty($current_type.nested_in_final)*}
		{?$type_search_properties = $current_type->getSearchableProperties(1, false)}
		{?$search_properties=$type_search_properties}
	{*{$search_properties|var_dump}*}
		{include file="Modules/Catalog/Item/itemFilter.tpl" assign=filter_inner}
		{capture assign=current_type_filter name=current_type_filter}
			<div class="hidden-filter ui-ignore a-hidden">
				{$filter_inner|html}
			</div>
		{/capture}
		{if empty($without_filter)}
			{$current_type_filter|html}
		{/if}
	{*/if*}

	{if !empty($catalog_items) && count($catalog_items)}
		{if $currentCatalog.key  == 'staff_list'}
			{include file="Modules/Catalog/Item/itemStaff.tpl"}
		{else}
			<div class="wblock white-block-row white-header">
				{if empty($ens_search)}
					<div class="w05">
						<input type="hidden" name="page" value="{!empty($smarty.get.page) ? $smarty.get.page : 1}" />
						<input type="hidden" name="type_id" value="{$current_type.id}" />
					</div>
					<label class="w05"><input type="checkbox" class="check-all" /></label>
				{/if}
				<div class="{if $currentCatalog.key == "infrastructure"}w4{else}w8{/if}">Название</div>
				{if $currentCatalog.key == "infrastructure"}
					<div class="w4">Тип</div>
				{/if}
				{if !empty($ens_search)}
					<div class="w1">
						<input type="hidden" name="page" value="{!empty($smarty.get.page) ? $smarty.get.page : 1}" />
						<input type="hidden" name="type_id" value="{$current_type.id}" />
					</div>
				{/if}
				<div class="w3"></div>
			</div>
			<div class="white-body sortable" data-url="/catalog-item/move/" data-cont="items-list" data-newpositionname="position" data-sendattrs="item_id">
				{foreach from=$catalog_items item=item}
					{?$item_title = !empty($item.title) ? $item.title|trim : ''}
					<div class="wblock white-block-row" data-item_id="{$item.id}" data-position="{$item.position}" data-item-text="{$currentCatalog.nested_in ? $current_type.word_cases['i']['1']['p'] : $currentCatalog.word_cases['i']['1']['p']}"{if !empty($currentCatalog.word_cases['v'])} data-variant-text="{$currentCatalog.word_cases['v']['2']['r']}"{/if}>
						{if empty($ens_search)}
							<div class="w05 {if $account->isPermission('catalog-item', 'move')}drag-drop{/if}"></div>
							<label class="w05">
								{if $account->isPermission('catalog-item', 'changeVisible')}
									<input type="checkbox" name="check[]" value="{$item.id}" class="check-item" />
								{/if}
							</label>
						{/if}
						{if  $currentCatalog.enable_view_mode}
							{* Режим просмотра разрешен *}
							<a href="/catalog-view/?id={$item.id}" class="{if $currentCatalog.key == "infrastructure"}w4{elseif empty($ens_search)}w7{else}w8{/if}">
								<input type="hidden" name="item_id" value="{$item.id}" />
								<span class="item-title">{if !empty($item_title)}{$item_title}{else}{$item.key}{/if}</span>
							</a>
						{else}
							{* Режим просмотра запрещен, пока просто убрана ссылка *}
							<div class="{if $currentCatalog.key == "infrastructure"}w4{elseif empty($ens_search)}w7{else}w8{/if}">
								<input type="hidden" name="item_id" value="{$item.id}" />
								<span class="item-title">
                                    {if !empty($item_title)}
                                        {?$delim = ldelim . "!" . rdelim}
                                        {?$item_title = $item_title|replace:$delim:' '}
                                        {$item_title}
                                    {else}
                                        {$item.key}
                                    {/if}
                                </span>
							</div>
						{/if}
						{if $currentCatalog.key == "infrastructure"}
							<div class="w3">{if !empty($item.type)}{$item.type}{/if}</div>
						{/if}
						{if $currentCatalog.key  == 'video' && !empty($item.url)}
							<a href="{$item.url}" target="_blank" class="action-button action-site w1" title="Удалить">
								<i class="icon-video_view"></i>
							</a>
						{else}
							<div class="w1"></div>
						{/if}
						{if !empty($currentCatalog.dynamic_for)}
							<div class="w2"></div>
							{if $account->isPermission('catalog-item', 'edit')}
								<a href="/catalog-item/edit/?id={$item.id}" class="action-button action-edit w1 m-border{if $currentCatalog.key  == 'video'} m-border{/if}" title="Редактировать">
									<i class="icon-edit"></i>
								</a>
							{else}
								<div class="action-button action-edit m-inactive w1 m-border{if $currentCatalog.key  == 'video'} m-border{/if}" title="Редактировать">
									<i class="icon-edit"></i>
								</div>
							{/if}
						{else}
							<div class="action-button action-visibility w1
								{if $account->isPermission('catalog-item', 'changeVisible')} m-active{else} m-inactive{/if} 
								action-{if $item['status'] == 3}show{else}hide{/if}"
								title="{if $item['status'] == 3}Отображается{else}Не отображается{/if}">
								<i class="icon-{if $item['status'] == 3}show{else}hide{/if}"></i>
							</div>
							{if $account->isPermission('catalog-item', 'edit')}
								<a href="/catalog-item/edit/?id={$item.id}" class="action-button action-edit w1 m-border{if $currentCatalog.key  == 'video'} m-border{/if}" title="Редактировать">
									<i class="icon-edit"></i>
								</a>
							{else}
								<div class="action-button action-edit m-inactive w1 m-border{if $currentCatalog.key  == 'video'} m-border{/if}" title="Редактировать">
									<i class="icon-edit"></i>
								</div>
							{/if}
							<div class="action-button action-delete w1 m-border" title="Удалить">
								<i class="icon-delete"></i>
							</div>
						{/if}
						{if !empty($ens_search)}
							{if $account->isPermission('catalog-item', 'changeVisible')}
								<input type="hidden" name="check[]" value="{$item.id}" class="check-item" />
							{/if}
						{/if}
					</div>
				{/foreach}
			</div>
		{/if}
	{else}

		<div class="white-body">
			<div class="wblock white-block-row">
				<div class="w12">
					{if empty($ens_search)}
						Нет {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['r'] : $currentCatalog.word_cases['i']['2']['r']}
					{else}
						Подходящие {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['v'] : $currentCatalog.word_cases['i']['2']['v']} не найдены
					{/if}
				</div>
			</div>
		</div>
	{/if}
</div>
{if empty($not_paging)}
    {if isset($catalog_items_count)}
        {include file="Admin/components/paging.tpl" count=$catalog_items_count show=5 url=( !empty($paging_url) ? $paging_url : ('/catalog-type/catalog/?id=' . $current_type.id))}
    {else}
        {include file="Admin/components/paging.tpl" show=5 url='/catalog-type/catalog/?id=' . $current_type.id}
    {/if}
{/if}