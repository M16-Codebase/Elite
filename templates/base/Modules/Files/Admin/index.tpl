{?$admin_page = 1}

{if !empty($smarty.get.type)}
	{capture assign=aside_filter name=aside_filter}
		<section class="aside-filter">
			<form class="user-form" method="GET">
				<input type="hidden" name="type" value="{$smarty.get.type}" />
				<div class="field">
					<div class="f-title">Название файла</div>
					<div class="f-input">
						<input type="text" name="name" />
					</div>
				</div>
				<div class="field">
					<div class="f-title">Сертифицирующий орган</div>
					<div class="f-input">
						<select class="chosen fullwidth" name="org">
							<option value="">Выберите</option>
							{foreach from=$orgs_list item=org key=id}
								<option value="{$id}">{$org}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="field">
					<div class="f-title">Дата окончания действия</div>
					<div class="f-input between">
						<input type="text" name="date_min" class="datepicker a-left" />
						<input type="text" name="date_max" class="datepicker a-right" />
						—
					</div>
				</div>
				<div class="tabs-cont field">
					{?$codes_range = false}
					{if !empty($smarty.get.type_code) || !empty($smarty.get.code_min) || !empty($smarty.get.code_max)}
						{?$codes_range = true}
					{/if}
					<div class="field">
						<div class="tabs-titles a-inline-cont">
							<a href="#number-filter" class="a-link-dotted{if !$codes_range} m-current{/if}">Номера</a>
							<a href="#between-filter" class="a-link-dotted{if $codes_range} m-current{/if}">Диапазон</a>
						</div>
					</div>
					<div class="tab-page field noborder{if $codes_range} a-hidden{/if}" id="number-filter">
						<div class="field noborder">
							<div class="f-title">Номенклатурные номера</div>
							<div class="f-input">
								<textarea name="codes"></textarea>
							</div>
						</div>
					</div>
					<div class="tab-page field noborder{if !$codes_range} a-hidden{/if}" id="between-filter">
						<div class="field noborder">
							<div class="f-title">Номенкл. номер категории</div>
							<div class="f-input">
								<input type="text" name="type_code" />
							</div>
						</div>
						<div class="field noborder">
							<div class="f-title">6-значный номер товара</div>
							<div class="f-input between">
								<input type="text" name="code_min" class="a-left" />
								<input type="text" name="code_max" class="a-right" />
								—
							</div>
						</div>
					</div>
				</div>
				<div class="buttons">
					<button class="a-button-blue submit">Показать</button>
					<div class="link-cont">
						<span class="clear-form a-link-dotted">Сбросить фильтр</span>
					</div>
				</div>
			</form>
		</section>
	{/capture}
{/if}

<form class="select-file-type blue-block" method="GET">
	<span class="title">Файлы:</span>
    <select name="type" class="chosen">
		<option value="">Выберите</option>
		{if !empty($types)}
			{foreach from=$types key=id item=title}
				<option value="{$id}">{$title}</option>
			{/foreach}
		{/if}
	</select>
</form>

{if !empty($smarty.get.type)}
	
	{include file="Admin/components/actions_panel.tpl" 
		multiple = true
		buttons = array(
			'add' => array(
				'text' => 'Создать файл'
			),
			'delete' => array(
				'inactive' => 1
			),
			'lock' => array(
				'url' => '/files-edit/noFile/',
				'text' => 'Товары без файлов'
			)
		)}

	<form class="files_list" action="/files-edit/delFiles/">
		{include file="Modules/Files/Admin/filesList.tpl"}
	</form>
	
	{include file="Admin/components/paging.tpl" count=$files_count pageSize=$pageSize pageNum=$pageNum show=5}

	{include file="/Admin/popups/upload_file.tpl"}
	{include file="/Admin/popups/reload_file.tpl"}
	{include file="/Admin/popups/file_variants.tpl"}

{/if}





