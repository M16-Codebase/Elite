{?$pageTitle = 'Поиск по каталогу — ' . (!empty($confTitle) ? $confTitle : '')}
{*{?$currentCatalog = $current_type->getCatalog()}*}
<ul>
{foreach from=$real_estate_items item=object}
	<li>{$object.title}</li>
{/foreach}
</ul>
<ul>
{foreach from=$resale_items item=flat}
	<li>{$flat.title}</li>
{/foreach}
</ul>
{*{?$filter_url="/catalog-view/searchList/?search=" . $search_string}*}
{*{include file="Modules/Catalog/Item/itemFilter.tpl" assign=aside_filter }*}
{*<div class="content-top">*}
	{*<h1>Поиск по каталогу</h1>*}
	{*<div class="content-options">*}
		{*{include file="Admin/components/actions_panel.tpl" *}
			{*multiple = true*}
			{*buttons = array(*}
				{*'back' => '/site/',*}
			{*)*}
		{*)}	*}
	{*</div>*}
{*</div>*}
{*<div class="content-scroll">*}
	{*<form class="white-blocks items-edit viewport">*}
		{*<div class="items-list white-blocks">*}
			{*{include file="Modules/Catalog/Item/listItems.tpl" ens_search=true without_filter=true}*}
		{*</div>*}
		{*{include file="Admin/components/paging.tpl" show=5}*}
	{*</form>*}
	{*{if !empty($current_type_filter)}*}
		{*{$current_type_filter|html}*}
	{*{/if}*}
{*</div>*}

<div class="edit-item-cont tabs-cont main-tabs">
	<div class="content-top">
		<h1>
			Поиск по каталогу
		</h1>
		<div class="action-panel-cont content-options">
			{include file="Admin/components/actions_panel.tpl"
			show = 3
			buttons = array(
			'back' => '/site/',
			)}
			{?$tabs_list = array(
			'real-estate' => array(
			'url' => '#',
			'current' => 1,
			'text' => 'Новостройки'
			),
			'resale' => array(
			'url' => '#'),
			'current' => 0,
			'text' => 'Вторичка'
			)}
			{include file="Admin/components/tabs.tpl"
			class = 'edit-item-tabs'
			data = array(
			)
			tabs = $tabs_list}
		</div>
	</div>

		<div id="tabs-pages" class="content-scroll-cont">
			<div id="real-estate" class="tab-page">
				{if empty($real_estate)}
					<div class="empty-result">Ничего не найдено</div>
				{/if}
			</div>
			<div id="resale" class="tab-page">
				{if empty($resale)}
					<div class="empty-result">Ничего не найдено</div>
				{/if}
			</div>
		</div>
</div>