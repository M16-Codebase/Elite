{?$currentCatalog = $current_type->getCatalog()}
<table class="rev-table ribbed">
	<tr>
		<th class="">{$currentCatalog.word_cases['i']['1']['i']}</th>
		<th class="">Имя</th>
		<th class="th-short">Дата</th>
		<th class="th-short">Статус</th>
		<th class="th-short"></th>
	</tr>
	{foreach from=$reviews item=review}
		{include file="Modules/Catalog/Admin/review_row.tpl"}
	{/foreach}
</table>
{?$url = '/product/reviews/?' . (!empty($smarty.get.status) ? ('status=' . $smarty.get.status) : '')}
{include file="components/paging.tpl" pageNum=$pageNum}

<div class="popup-window popup_edit-review">
</div>
