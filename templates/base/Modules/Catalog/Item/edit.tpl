{capture assign=catalog_item_title}
	{if !empty($catalog_item.title)}{$catalog_item.title}{else}No title{/if}
{/capture}
{?$currentCatalog = $current_type->getCatalog()}
{if !empty($catalog_item->getType()->getCatalog())}{?$item_name = $currentCatalog.nested_in ? $catalog_item->getType() : $catalog_item->getType()->getCatalog()}{/if}
{?$pageTitle = $current_type.title . ' — ' . (!empty($confTitle) ? $confTitle : '')}
{?$site_link_type = $catalog_item->getUrl()}
{?$site_link = $site_link_type . (!empty($show_variant_id) ? 'v' . $show_variant_id . '/' : '')}

<div class="edit-item-cont tabs-cont main-tabs"{if !empty($site_link)} data-site-url="{$site_link}"{/if}>
	<div class="content-top">
		{if $currentCatalog.enable_view_mode && $account->isPermission('catalog-item', 'edit')}
			<div class="switch-type a-right">
				<a href="/catalog-item/edit/?id={$catalog_item.id}" class="action-button type-edit action-edit m-current" title="Режим редактирования">
					<i class="icon-edit"></i>
				</a>
				<a href="/catalog-view/?id={$catalog_item.id}" class="action-button type-view action-view" title="Режим просмотра">
					<i class="icon-view"></i>
				</a>
			</div>
		{/if}
		<h1>
			{if $catalog_item.status != 1}
                Редактирование{if !empty($item_name)} {$item_name.word_cases['i']['1']['r']} {/if}
                {if !empty($catalog_item.title)}
                    {?$delim = ldelim . "!" . rdelim}
                    {?$catalog_item_title = $catalog_item.title|replace:$delim:' '}
                     «{$catalog_item_title}»
                {/if}
			{else}Создание{if !empty($item_name)} {$item_name.word_cases['i']['1']['r']}{else} объекта{/if}{/if}
		</h1>
		<div class="action-panel-cont content-options">
			{include file="Admin/components/actions_panel.tpl"
				show = 3
				buttons = array(
					'back' => $catalog_item.parent_id? ('/catalog-item/edit/?id=' . $catalog_item.parent_id . '&tab=' . $current_type.key) : ('/catalog-type/catalog/?id=' . $catalog_item.type_id . '&tab=items'),
					'show' => ($catalog_item.status == 3)? array(
						'class' => 'action-visibility',
						'text' => 'Отображается'
					) : 0,
					'hide' => ($catalog_item.status != 3)? array(
						'class' => 'action-visibility',
						'text' => 'Не отображается'
					) : 0,
					'delete' => !empty($item_name)? array(
						'text' => 'Удалить ' . $item_name.word_cases['i']['1']['v']
					) : '#',
					'copy' => $currentCatalog.key == 'catalog'? '#' : null,
					'move' => $currentCatalog.key == 'catalog'? '#' : null
				)}
			{?$req_tab = !empty($smarty.get.tab)? $smarty.get.tab : 'options'}
			{?$item_images = !empty($catalog_item.gallery)? $catalog_item.gallery->getImages() : array()}
			{?$child_types = $current_type->getNestedTypes()}
			{?$child_types_data = array()}
			{foreach from=$child_types item=child_type}
				{?$child_types_data[$child_type.key] = array(
					'key' => $child_type.key,
					'url' => '/catalog-item/edit/?id=' . $catalog_item.id . '&tab=' . $child_type.key,
					'current' => ($req_tab == $child_type.key),
					'text' => !empty($child_type.word_cases['i'][2]['i']) ? $child_type.word_cases['i'][2]['i'] : $child_type.title,
					'items' => $catalog_item->getChildren($child_type.id, FALSE)
				)}
				{?$child_types_data[$child_type.key]['count'] = count($child_types_data[$child_type.key]['items'])}
			{/foreach}
			{?$catalog = $current_type->getCatalog()}
			{?$tabs_list = array(
				'options' => array(
					'url' => '/catalog-item/edit/?id=' . $catalog_item.id . '&tab=options',
					'current' => ($req_tab == 'options'),
					'text' => 'Свойства'
				),
				'variants' => ($catalog.only_items == 0)? array(
					'url' => '/catalog-item/edit/?id=' . $catalog_item.id . '&tab=variants' . (!empty($smarty.get.v)? '&v=' . $smarty.get.v : ''),
					'current' => ($req_tab == 'variants'),
					'text' => $currentCatalog.word_cases['v']['2']['i'],
					'count' => !empty($catalog_item_variants) ? $catalog_item_variants : 0
				) : 0,
				'reviews' => (!empty($enable_reviews)) ? array(
					'url' => '/catalog-item/edit/?id=' . $catalog_item.id . '&tab=reviews',
					'current' => ($req_tab == 'reviews'),
					'text' => 'Отзывы',
					'count' => $item_reviews? count($item_reviews) . (!empty($new_reviews_count)? '(+' . $new_reviews_count . ')' : '') : '0'
				) : 0,
				'questions' => (!empty($enable_questions)) ? array(
					'url' => '/catalog-item/edit/?id=' . $catalog_item.id . '&tab=questions',
					'current' => ($req_tab == 'questions'),
					'text' => 'Вопросы',
					'count' => $item_questions? count($item_questions) . (!empty($new_questions_count)? '(+' . $new_questions_count . ')' : '') : '0'
                ) : 0
			)}
			{if $account->isPermission('seo') && $currentCatalog.show_metatags_tab}
				{if $constants.segment_mode != 'none'}
					{foreach from=$segments item=$s}
						{?$tabs_list['seo_'.$s.key] = Array(
							'url' => '/catalog-item/edit/?id=' . $catalog_item.id . '&tab=seo_' . $s.key,
							'current' => ($req_tab == 'seo_' . $s.key),
							'text' =>  'Мета-теги('.$s.title.')',
							'count' => !empty($item_meta_tag[$s.id].enabled)? '+' : '-'
						)}
					{/foreach}
				{else}
					{?$tabs_list['seo'] = array(
						'url' => '/catalog-item/edit/?id=' . $catalog_item.id . '&tab=seo',
						'current' => ($req_tab == 'seo'),
						'text' => 'Мета-теги',
						'count' => !empty($item_meta_tag[0].enabled)? '+' : '-'
					)}
				{/if}
			{/if}
			{?array_splice($tabs_list, 1, 0, $child_types_data)}
			{include file="Admin/components/tabs.tpl" 
				class = 'edit-item-tabs'
				data = array(
					'item_id' => $catalog_item.id,
					'item-link' => $catalog_item->getUrl()
				)
				tabs = $tabs_list}
		</div>
	</div>

	{if !empty($catalog_item)}
		<div id="tabs-pages" data-type-id="{$catalog_item.type_id}" data-item-id="{$catalog_item.id}" class="content-scroll-cont">

			{* Объект *}
			<div id="options" class="tab-page options-page actions-cont{if $req_tab == 'options'} m-current{/if}">
				<form class="edit_properties_form content-scroll" action="/catalog-item/save/" data-id="{$catalog_item.id}"{if !empty($catalog_item.special_variant)} data-special-id="{$catalog_item.special_variant.id}"{/if} data-parent_id="{$catalog_item.parent_id}">
					<div class="aside-panel">
						{if !empty($item_properties)}
							{include file="Admin/components/actions_panel.tpl"
								buttons = array(
									'save' => 1
								)}
						{/if}
					</div>
					<div class="white-blocks viewport">
						{if !empty($item_properties)}
							{include file="Modules/Catalog/Item/edit_item_properties.tpl" item_properties=$item_properties variant_list=false create=true show_item_id=true}
							<input type="submit" class="a-hidden" />
						{else}
							<div class="wblock white-block-row">
								<div class="w12">Свойства не заданы</div>
							</div>
						{/if}
					</div>
				</form>
			</div>

			{* Дочерние айтемы *}
			{foreach from=$child_types item=child_type}
				<div id="{$child_type.key}" class="tab-page child-page {$child_type.key}-page actions-cont{if $req_tab == $child_type.key} m-current{/if}" data-type-id="{$child_type.id}" data-parent-id="{$catalog_item.id}">
					<div class="content-scroll">
						<div class="aside-panel">
							{include file="Admin/components/actions_panel.tpl" 
								multiple = true
								buttons = array(
									'add' => ($account->isPermission('catalog-item', 'create') && $current_type.allow_children == 0 ? '/catalog-item/create/?type_id=' . $child_type.id . '&parent_id=' . $catalog_item.id : 0),
									'show' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
										'inactive' => 1,
									) : 0),
									'hide' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
										'inactive' => 1,
									) : 0),
									'delete' => ($account->isPermission('catalog-item', 'delete')? array(
										'inactive' => 1,
									) : 0),
									'edit' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
										'text' => 'Групповая правка',
										'inactive' => $child_types_data[$child_type.key]['count']? 0 : 1
									) : 0),
									'type' => '/catalog-type/catalog/?id=' . $child_type.id
								)}
						</div>
						<form class="actions-cont items-edit viewport">
							<div class="items-list" data-count="{$child_types_data[$child_type.key]['count']}">
								{include file="Modules/Catalog/Item/listItems.tpl" catalog_items=$child_types_data[$child_type.key]['items'] catalog_items_count=$child_types_data[$child_type.key]['count'] without_filter=true current_type=$child_type not_paging = 1}
                                {?$not_paging = 0}
							</div>
						</form>
						{if !empty($current_type_filter)}
							{$current_type_filter|html}
						{/if}
					</div>
				</div>
			{/foreach}

			{* Варианты *}
			{if $currentCatalog.only_items == 0}
				<div id="variants" class="tab-page variants-page actions-cont{if $req_tab == 'variants'} m-current{/if}" 
					 data-origin="/catalog-item/edit/?id={$catalog_item.id}&tab=variants" data-variant="{if !empty($smarty.get.v)}{$smarty.get.v}{/if}">
				</div>
			{/if}

			{* Отзывы *}
			{if !empty($enable_reviews)}
				<div id="reviews" class="tab-page reviews-page actions-cont{if $req_tab == 'reviews'} m-current{/if}"
					 data-origin="/catalog-item/edit/?id={$catalog_item.id}&tab=reviews" data-type_id="{$reviews_type.id}">
						{include file='Modules/Catalog/Item/itemReviews.tpl'}
				</div>
			{/if}

			{* Вопросы *}
			{if !empty($enable_questions)}
				<div id="questions" class="tab-page reviews-page actions-cont{if $req_tab == 'questions'} m-current{/if}"
					 data-origin="/catalog-item/edit/?id={$catalog_item.id}&tab=questions" data-type_id="{$questions_type.id}">
						{include file='Modules/Catalog/Item/itemQuestions.tpl'}
				</div>
            {/if}
			
			{* Мета-теги *}
			{if $account->isPermission('seo') && $currentCatalog.show_metatags_tab}
				{if $constants.segment_mode != 'none'}
					{foreach from=$segments item=$s}
						<div id="seo_{$s.key}" class="tab-page seo-page actions-cont{if $req_tab == 'seo_'.$s.key} m-current{/if}"
							 data-origin="/catalog-item/edit/?id={$catalog_item.id}&tab=seo">
							{include file='Modules/Catalog/Item/itemSeoMetaTags.tpl' metatag_segment_id = $s.id}
						</div>
					{/foreach}
				{else}
					<div id="seo" class="tab-page seo-page actions-cont{if $req_tab == 'seo'} m-current{/if}"
						 data-origin="/catalog-item/edit/?id={$catalog_item.id}&tab=seo">
						{include file='Modules/Catalog/Item/itemSeoMetaTags.tpl'}
					</div>
				{/if}
			{/if}
		</div>
	{else}
		<div class="empty-result">Такого {$currentCatalog.word_cases['i']['1']['r']} нет</div>
	{/if}
</div>

