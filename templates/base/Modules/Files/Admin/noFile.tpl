{?$admin_page = 1}

{capture assign=aside_filter name=aside_filter}
	<section class="aside-filter">
		<form class="user-form" method="GET">
			<div class="field noborder">
				<div class="f-title">Тип файла</div>
				<div class="f-input cbx">
					{foreach from=$types key=id item=title}
						<label>
							<input type="checkbox" name="type_ids[]" value="{$id}"> {$title}
						</label>
					{/foreach}
				</div>
			</div>
			<div class="buttons">
				<button class="a-button-blue submit">Показать</button>
			</div>
		</form>
	</section>
{/capture}

<h1>Товары без файлов</h1>

{include file="Admin/components/actions_panel.tpl"
	buttons = array(
		'back' => '/files-edit/'
	)
}
<table class="ribbed">
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
	
{include file="Admin/components/paging.tpl" count=$variants_count pageSize=$pageSize pageNum=$pageNum show=5}