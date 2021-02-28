{?$currentCatalog = $current_type->getCatalog()}
{?$pageTitle = (!empty($current_type.title) ? $current_type.title . ' — ': '') . (!empty($currentCatalog.title) && $current_type.id != $currentCatalog.id ? $currentCatalog.title . ' — ': '') . (!empty($confTitle) ? $confTitle : '')}
{?$site_link = $current_type->getUrl()}
{if $current_type.fixed && $accountType != 'SuperAdmin'}
	{?$current_type_unchangeable = 1}
{else}
	{?$current_type_unchangeable = 0}
{/if}

<div class="tabs-cont main-tabs" data-catalog="{$currentCatalog.key}"{if !empty($site_link)} data-site-url="{$site_link}"{/if}>
	<div class="content-top">
		<h1>{$current_type.title}</h1>
		<div class="content-options">
			{if $action == 'catalog'}
				{if $accountType == 'SuperAdmin'}
					{?$back_url = '/catalog-type/' . (($current_type.parent_id != $constants.default_type_id)? ('catalog/?id=' . $current_type.parent_id) : '')}
				{elseif $currentCatalog.key == 'config' ||  $currentCatalog.key == 'reviews_question'}
					{?$back_url = 0}
				{else}
					{?$back_url = ($current_type.parent_id != $constants.default_type_id)? ('/catalog-type/catalog/?id=' . $current_type.parent_id) : 0}
				{/if}
				{include file="Admin/components/actions_panel.tpl"
					buttons = array(
						'back' => $back_url,
						'save' => ($currentCatalog.key == 'config')? array(
							'class' => 'config-submit'
						) : 0
					)
				}
			{/if}
			{if $action == 'catalog'}
				{?$type_url = '/catalog-type/catalog/'}
			{else}
				{?$type_url = '/catalog-type/'}
			{/if}
			{?$req_tab = !empty($smarty.get.tab)? $smarty.get.tab : ($current_type.allow_children? 'types' : 'items')}
			{?$type_cover = $current_type['cover']}
			{?$type_default = $current_type['default']}
			{?$empty_filter = ($req_tab == 'items')}
			
				{?$tabs = array(
					'types' => $current_type.allow_children? array(
						'url' => $type_url . '?id=' . $current_type.id . '&tab=types',
						'current' => ($req_tab == 'types'),
						'text' => $current_type.id == 1 ? 'Каталоги' : 'Категории',
						'count' => !empty($types)? count($types) : 0
					) : 0,
					'items' => !$current_type.allow_children ? array(
						'url' => $type_url . '?id=' . $current_type.id . '&tab=items',
						'current' => ($req_tab == 'items'),
						'text' => $currentCatalog.nested_in ? $current_type.word_cases['i']['2']['i'] : $currentCatalog.word_cases['i']['2']['i'],
						'count' => $current_type['counters']['all_items'],
						'count_class' => $currentCatalog.key == 'config'? 'a-hidden' : ''
					) : 0,
					'rules' => (!$current_type.allow_children && $currentCatalog.dynamic_for) ? array(
						'url' => $type_url . '?id=' . $current_type.id . '&tab=rules',
						'current' => ($req_tab == 'rules'),
						'text' => 'Правила'
					) : 0,
					'properties' => (($accountType == 'SuperAdmin' || $currentCatalog.show_props_tab) && !$currentCatalog.dynamic_for) ? array(
						'url' => $type_url . '?id=' . $current_type.id . '&tab=properties',
						'current' => ($req_tab == 'properties'),
						'text' => 'Свойства',
						'count' => !empty($current_type.properties)? count($current_type.properties) : 0
					) : 0,
					'groups' => (($accountType == 'SuperAdmin' || $currentCatalog.show_groups_tab) && !$currentCatalog.dynamic_for) ? array(
						'url' => $type_url . '?id=' . $current_type.id . '&tab=groups',
						'current' => ($req_tab == 'groups'),
						'text' => 'Группы',
						'count' => !empty($prop_groups)? count($prop_groups) : 0
					) : 0,
				)}
				{if (($accountType == 'SuperAdmin' || $currentCatalog.show_text_tab) && $current_type.id != 1 && $currentCatalog.key != 'config')}
					{if $constants.segment_mode == 'lang'}
						{foreach from=$segments item=$s}
							{?$tabs['description_'.$s.key] = Array('url' => $type_url . '?id=' . $current_type.id . '&tab=description_' . $s.key,
								'current' => ($req_tab == 'description_' . $s.key),
								'inactive' => ($current_type.id == 0),
								'text' =>  'Текст('.$s.title.')',
								'count' => !empty($current_type.segment_data.post[$s.id].text)? '+' : '-')}
						{/foreach}
					{else}
						{?$tabs['description'] = array(
							'url' => $type_url . '?id=' . $current_type.id . '&tab=description',
							'current' => ($req_tab == 'description'),
							'inactive' => ($current_type.id == 0),
							'text' =>  'Текст' ,
							'count' => !empty($current_type.post.text)? '+' : '-'
						)}
					{/if}
				{/if}
				{?$tabs['cover'] = ($accountType == 'SuperAdmin' || $currentCatalog.show_cover_tab) && $currentCatalog.key != 'config' ? array(
						'url' => $type_url . '?id=' . $current_type.id . '&tab=cover',
						'current' => ($req_tab == 'cover'),
						'inactive' => ($current_type.id == 0),
						'text' => 'Обложка',
						'count' => !empty($type_cover)? '+' : '-'
					) : 0
				}
				{?$tabs['banners'] = ($accountType == 'SuperAdmin' || $currentCatalog.show_banner_tab) && $currentCatalog.key != 'config' && $action == 'catalog' ? array(
						'url' => $type_url . '?id=' . $current_type.id . '&tab=banners',
						'current' => ($req_tab == 'banners'),
						'inactive' => ($current_type.id == 0),
						'text' => 'Баннеры',
						'count' => !empty($banners)? count($banners) : 0
					) : 0
				}
			
			{include file="Admin/components/tabs.tpl" tabs=$tabs}
				
		</div>
	</div>

	<div id="tabs-pages" class="content-scroll-cont" data-type-id="{$current_type.id}" data-type_catalog="1">

		{* типы *}
		{if $current_type.allow_children}
			<div id="types" class="tab-page actions-cont{if $req_tab == 'types'} m-current{/if}">
				<div class="content-scroll">
					{if $accountType != 'Admin'}
						<div class="aside-panel">
							{if $currentCatalog.nested_in}
									{include file="Admin/components/actions_panel.tpl"
										buttons = array(
											'add' => ($account->isPermission('catalog-type', 'create')? 1 : 0)							
										)}
							{else}
								{include file="Admin/components/actions_panel.tpl"
									multiple = true
									buttons = array(
										'add' => ($account->isPermission('catalog-type', 'create')? 1 : 0),
										'delete' => ($account->isPermission('catalog-type', 'delete')? array(
											'inactive' => 1
										) : 0),
										'more' => ($account->isPermission('catalog-type', 'updateHidden')? array(
											'inactive' => 1,
											'list' => array(
												'show' => 'Показать',
												'hide' => 'Скрыть'
											)
										) : 0)
									)}
							{/if}
						</div>
					{/if}
					<form class="type-form viewport">
						<input type="hidden" name="parent_id" value="{$current_type.id}" />
						<div class="types-list white-blocks{if $currentCatalog.nested_in} bush-catalog{/if}">
							{if !$currentCatalog.nested_in}
								<div class="wblock white-block-row white-header">
									<div class="w05"></div>
									<label class="w05"><input type="checkbox" class="check-all" /></label>
									<div class="{if $current_type->isChildrenCanHasChildren()}w5{else}w6{/if}">{if $current_type.id == 1}Каталоги{else}Категории{/if}</div>
									{if $current_type->isChildrenCanHasChildren()}<div class="w1">Категорий</div>{/if}
									<div class="w1">{if $current_type.id == 1}Айтемов{else}{$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['i'] : $currentCatalog.word_cases['i']['2']['i']}{/if}</div>
									<div class="w1" data-only_items="{$currentCatalog.title}">{if !$currentCatalog.only_items}{if $current_type.id == 1}Вариантов{else}{$currentCatalog.word_cases['v'][2]['r']}{/if}{/if}</div>
									<div class="w3">
										{if $accountType == 'SuperAdmin'}Фикс.{/if}
									</div>
								</div>
							{/if}
							<div class="white-body sortable" data-url="/catalog-type/move/" data-positionattr="position" data-sendattrs="type_id;parent_id" data-newpositionname="position">
								{if !empty($types)}
									{include file="Modules/Catalog/Type/typesList.tpl"}
								{else}
									<div class="wblock white-block-row">
										<div class="w12">Нет категорий</div>
									</div>
								{/if}
							</div>
						</div>
					</form>
				</div>
			</div>
		{/if}

		{* айтемы *}
		{if !$current_type.allow_children}
			{if $currentCatalog.key == 'config'}
				{?$catalog_item = $catalog_items->getFirst()}
				<div id="items" class="tab-page actions-cont{if $req_tab == 'items'} m-current{/if}" data-type-id="{$catalog_item.id}">
					<form class="edit_properties_form content-scroll" action="/catalog-item/save/" data-id="{$catalog_item.id}"{if !empty($catalog_item.special_variant)} data-special-id="{$catalog_item.special_variant.id}"{/if} data-parent_id="{$catalog_item.parent_id}">
						{if $currentCatalog.key != 'config' }
							<div class="aside-panel">
								{include file="Admin/components/actions_panel.tpl"
									buttons = array(
										'save' => 1
									)}
							</div>
						{/if}
						<div class="white-blocks viewport">
							{if !empty($current_type.properties)}
								{include file="Modules/Catalog/Item/edit_item_properties.tpl" item_properties=$current_type.properties config_props=true}
							{/if}
							<input type="submit" class="a-hidden" />
						</div>
					</form>
				</div>
			{elseif $currentCatalog.key == 'reviews_question'}
				<div id="items" class="tab-page reviews-page actions-cont{if $req_tab == 'items'} m-current{/if}" data-type_id="{$current_type.id}">
					{include file='Modules/Catalog/Item/itemReviews.tpl' item_reviews=$catalog_items}
				</div>
			{else}
				<div id="items" class="tab-page actions-cont{if $req_tab == 'items'} m-current{/if}" data-type-id="{$current_type.id}">
					<div class="content-scroll">
						{if !$currentCatalog.dynamic_for}
							<div class="aside-panel">
								{if $currentCatalog.nested_in}
									{?$parent_type = $current_type->getNestedIn()}
									{if empty($parent_type)}
										{?$can_create_items = true}
									{else}
										{?$can_create_items = false}
									{/if}
								{else}
									{?$can_create_items = true}
								{/if}
							{else}
								{?$can_create_items = true}
							{/if}
							{include file="Admin/components/actions_panel.tpl" 
								multiple = true
								buttons = array(
									'add' => ($account->isPermission('catalog-item', 'edit') && $current_type.allow_children == 0 && $can_create_items)? 
										('/catalog-item/edit/?type_id=' . $current_type.id) : 0,
									'delete' => ($account->isPermission('catalog-item', 'delete')? array(
										'inactive' => 1,
									) : 0),
									'edit-group' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
										'text' => 'Групповая правка',
										'inactive' => $catalog_items_count? 0 : 1
									) : 0),
									'more' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
										'inactive' => 1,
										'list' => array(
											'show' => 'Показать',
											'hide' => 'Скрыть'
										)
									) : 0)
								)}
						</div>
						<form class="actions-cont items-edit viewport">
							<div class="items-list" data-count="{$catalog_items_count}">
								{include file="Modules/Catalog/Item/listItems.tpl" catalog_items_count=$catalog_items_count without_filter=true}
							</div>
						</form>
						{if !empty($current_type_filter)}
							{$current_type_filter|html}
						{/if}
					</div>
				</div>
			{/if}
		{/if}
		
		{* правила *}
		{if (!$current_type.allow_children && $currentCatalog.dynamic_for)}
			<div id="rules" class="tab-page actions-cont{if $req_tab == 'rules'} m-current{/if}">
				<div class="content-scroll">
					<div class="aside-panel">
						{include file="Admin/components/actions_panel.tpl"
							multiple = true
							buttons = array(
								'add' => ($account->isPermission('catalog-type', 'createProp')? 1 : 0),
								'delete' => ($account->isPermission('catalog-type', 'delProps')? array(
									'inactive' => 1,
								) : 0)
							)}
					</div>
					<div class="viewport">
						<form class="rules-list white-blocks overview">
							{include file="Modules/Catalog/Type/dynamicRulesList.tpl"}
						</form>
					</div>
				</div>
			</div>
		{/if}

		{* свойства *}
		<div id="properties" class="tab-page actions-cont{if $req_tab == 'properties'} m-current{/if}">
			<div class="content-scroll">
				<div class="aside-panel">
					{include file="Admin/components/actions_panel.tpl"
						multiple = true
						buttons = array(
							'add' => ($account->isPermission('catalog-type', 'createProp')? 1 : 0),
							'delete' => ($account->isPermission('catalog-type', 'delProps')? array(
								'inactive' => 1,
							) : 0)
						)}
				</div>
				<form class="white-blocks properties-form viewport">
					<input type="hidden" name="type_id" value="{$current_type.id}" />
					<div class="properties-list">
						{include file="/Modules/Catalog/Type/properties.tpl" properties=$current_type.properties type_id=$current_type.id}
					</div>
				</form>
			</div>
		</div>

		{* группы *}
		<div id="groups" class="tab-page actions-cont{if $req_tab == 'groups'} m-current{/if}">
			<div class="content-scroll">
				<div class="aside-panel">
					{include file="Admin/components/actions_panel.tpl"
						buttons = array(
							'add' => ($account->isPermission('catalog-type', 'addPropGroup')? 1 : 0)
						)}
				</div>
				<form class="viewport">
					<div class="groups-list white-blocks sortable" data-url="/catalog-type/movePropGroup/" data-positionattr="position" data-sendattrs="group_id;type_id" data-newpositionname="move">
						{include file="/Modules/Catalog/Type/propGroups.tpl"}
					</div>
				</form>
			</div>		
		</div>

		{* описание *}
		{if $current_type.id != 1}
			{if $constants.segment_mode == 'lang'}
				{foreach from=$segments item=$s}
					<div id="description_{$s.key}" class="description tab-page actions-cont{if $req_tab == 'description' . $s.key} m-current{/if}">
						<div class="content-scroll">
							<div class="aside-panel">
								{include file="Admin/components/actions_panel.tpl"
									buttons = array(
										'save' => ($account->isPermission('catalog-type', 'addDescription')? 1 : 0)
									)}
							</div>
							<div class="viewport">
								<form class="white-blocks post-form" action="/catalog-type/addDescription/">
									<input type="hidden" value="{$current_type.id}" name="type_id" />
									<input type="hidden" value="{$s.id}" name="segment_id" />
									{include file="Modules/Posts/Admin/post_edit_error.tpl"}
									<div class="wblock white-block-row">
										<div class="w3">
											<strong>Заголовок</strong>
										</div>
										<div class="w9">
											<input type="text" name="title"{if !$account->isPermission('catalog-type', 'addDescription')} disabled{/if} value="{if !empty($current_type.segment_data.post[$s.id])}{$current_type.segment_data.post[$s.id].title}{/if}" />
										</div>
									</div>
									<div class="wblock white-block-row">
										<div class="w3">
											<strong>Аннотация</strong>
										</div>
										<div class="w9">
											<textarea name="annotation" rows="5"{if !$account->isPermission('catalog-type', 'addDescription')} disabled{/if} >
												{if !empty($current_type.segment_data.post[$s.id])}{$current_type.segment_data.post[$s.id].annotation}{/if}
											</textarea>
										</div>
									</div>
									<div class="wblock post-block">
										<textarea name="text" class="redactor" rows="15"{if !$account->isPermission('catalog-type', 'addDescription')} disabled{/if}>
											{if !empty($current_type.segment_data.post[$s.id])}{$current_type.segment_data.post[$s.id].raw_text}{/if}
										</textarea>
									</div>
								</form>
								{if !empty($current_type.segment_data.post[$s.id]) && $account->isPermission('catalog-type', 'addDescription')}
									{include file="Modules/Posts/Admin/post_uploader.tpl" post=$current_type.segment_data.post[$s.id]}
								{/if}
							</div>
						</div>
					</div>
				{/foreach}
			{else}
				<div id="description" class="description tab-page actions-cont{if $req_tab == 'description'} m-current{/if}">
					<div class="content-scroll">
						<div class="aside-panel">
							{include file="Admin/components/actions_panel.tpl"
								buttons = array(
									'save' => ($account->isPermission('catalog-type', 'addDescription')? 1 : 0)
								)}
						</div>
						<div class="viewport">
							<form class="white-blocks post-form" action="/catalog-type/addDescription/">
								<input type="hidden" value="{$current_type.id}" name="type_id" />
								<input type="hidden" value="{$current_type.post_id}" name="post_id" />
								{include file="Modules/Posts/Admin/post_edit_error.tpl"}
								<div class="wblock white-block-row">
									<div class="w3">
										<strong>Заголовок</strong>
									</div>
									<div class="w9">
										<input type="text" name="title"{if !$account->isPermission('catalog-type', 'addDescription')} disabled{/if} />
									</div>
								</div>
								<div class="wblock white-block-row">
									<div class="w3">
										<strong>Аннотация</strong>
									</div>
									<div class="w9">
										<textarea name="annotation" rows="5"{if !$account->isPermission('catalog-type', 'addDescription')} disabled{/if} ></textarea>
									</div>
								</div>
								<div class="wblock post-block">
									<textarea name="text" class="redactor" rows="15"{if !$account->isPermission('catalog-type', 'addDescription')} disabled{/if}></textarea>
								</div>
							</form>
							{if !empty($current_type.post) && $account->isPermission('catalog-type', 'addDescription')}
								{include file="Modules/Posts/Admin/post_uploader.tpl" post=$current_type.post}
							{/if}
						</div>
					</div>
				</div>
			{/if}
		{/if}

		{* обложка *}
		<div id="cover" class="tab-page{if $req_tab == 'cover'} m-current{/if}">
			<div class="content-scroll">
				<div class="viewport white-blocks">
					<form class="wblock upload-file-cover main-cover" action="/catalog-type/addCover/" />
						<div class="wblock white-block-row">
							<div class="prop-item w12">
								<div class="prop-title h4">Обложка</div>
								<input type="hidden" name="type_id" value="{$current_type.id}" />
								<div class="row">
								{include file="/Modules/Catalog/Type/addCover.tpl" type_cover=$type_cover}
								</div>
								<div class="add-row row m-fullwidth">
									<label for="change-image-cover-main" class="change-img">
									<div class="add-object add-btn w3">
										<i class="icon-{if !empty($type_cover)}replace{else}add{/if}"></i> <span class="small-descr">{if !empty($type_cover)}Заменить{else}Добавить{/if} изображение</span>
									</div>
									</label>
									<input type="file" name="cover" class="hidden-input" id="change-image-cover-main"/>
								</div>
							</div>
						</div>
					</form>

					<form class="wblock upload-file-cover" action="/catalog-type/addDefault/">
						<div class="wblock white-block-row">
							<div class="prop-item w12">
								<div class="prop-title h4">Картинка по умолчанию</div>
								<input type="hidden" name="type_id" value="{$current_type.id}" />
								<div class="row">
									{include file="/Modules/Catalog/Type/addCover.tpl" type_cover=$type_default}
								</div>
								<div class="add-row row m-fullwidth">
									<label for="change-image-cover-default" class="change-img">
									<div class="add-object add-btn w3">
										<i class="icon-{if !empty($type_default)}replace{else}add{/if}"></i> <span class="small-descr">{if !empty($type_default)}Заменить{else}Добавить{/if} изображение</span>
									</div>
									</label>
									<input type="file" name="default" class="hidden-input" id="change-image-cover-default"/>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		{* баннеры *}
		<div id="banners" class="tab-page{if $req_tab == 'banners'} m-current{/if}" data-cat-url="{$current_type->getUrl()}">
			<div class="content-scroll">
				<div class="aside-panel">
					{include file="Admin/components/actions_panel.tpl"
						buttons = array(
							'add' => ($account->isPermission('catalog-type', 'addBanners')? 1 : 0),
						)}
				</div>
				<div class="viewport">
					{include file="/Modules/Site/Banner/banners.tpl" catalog_banner=1}
				</div>
			</div>
		</div>
		
	</div>
</div>

{include file="/Modules/Catalog/Item/edit_single_prop.tpl" assign=edit_single_prop}
{include file="/Modules/Catalog/Item/transfer_item.tpl" assign=transfer_item}
{include file="/Modules/Catalog/Type/create_prop.tpl" assign=create_prop}
{include file="/Modules/Catalog/Item/edit_coords.tpl" assign=edit_coords}
{include file="/Modules/Catalog/Item/edit_poly.tpl" assign=edit_poly}
{capture assign=editBlock name=editBlock}
	{$edit_single_prop|html}
	{$transfer_item|html}
	{$create_prop|html}
	{$edit_coords|html}
	{$edit_poly|html}
{/capture}
{if !empty($catalog_item) && $currentCatalog.key != 'config'}
	{include file="/Modules/Catalog/Item/edit.tpl" assign=edit_item}
	{?$currentEditBlock_class = 'item-props-edit'}
	{capture assign=currentEditBlock name=currentEditBlock}
		{$edit_item|html}
	{/capture}
{/if}