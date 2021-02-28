<h1>Производители</h1>
{include file="Admin/components/actions_panel.tpl"
	multiple = true
	buttons = array(
		'add' => ($account->isPermission('manuf', 'add')? 1 : 0),
		'delete' => ($account->isPermission('manuf', 'del')? array(
			'inactive' => 1
		) : 0)
	)}
{?$cover_width = 50}
{?$cover_height = 50}
	<table class="ribbed manuf-table">
		<thead>
			<tr>
				<th colspan="3">
					<form method="GET" class="manuf-search a-inline-cont">
						<input type="text" name="title" class="autocomplete" placeholder="Начните ввод названия" data-url="/manuf/getManufList/" />
						<button class="a-button-blue">Показать</button>
						<a href="/manuf/" class="reset">Сбросить</a>
					</form>
				</th>
				<th class="th-center">Обложка</th>
                                <th class="th-center">Сертификат</th>
				<th>Текст</th>
				<th></th>
			</tr>
		</thead>
{if !empty($manufs)}
		<tbody>
			{foreach from=$manufs item=manuf}
				<tr class="manuf_row" id="manuf_row-{$manuf.key}" data-manuf_key="{$manuf.key}" data-position="{$manuf.position}">
                    <td class="small"><div class="drag-drop"></div></td>
					<td class="small">
						<input type="checkbox" name="check[]" value="{$manuf.key}" class="check-item" />
					</td>
					<td class="td-title">{$manuf.title}{if !empty($counts[$manuf.title])} ({$counts[$manuf.title]}){/if}</td>
					<td class="reloadCover cover-input-{$manuf.key} td-center">
						{if empty($manuf.cover)}
							<div class="table-btn add addCover"></div>
						{else}
							<img src="{$manuf.cover->getUrl($cover_width, $cover_height, 90, false, true)}" />
						{/if}
					</td>
                                        <td class="reloadFile file-input-{$manuf.key} td-center">
                                                {if empty($manuf.file)}
                                                    <div class="table-btn add addFile"></div>
                                                {else}
                                                    <div class="table-btn reload"></div>
                                                {/if}
                                        </td>
					<td class="small td-center">
						{if !empty($manuf['post']) && $account->isPermission('manuf', 'edit')}
							<a class="table-btn reload" href="/manuf/edit/?id={$manuf['post']['id']}&manuf_key={$manuf.key}"></a>
						{elseif $account->isPermission('manuf', 'create')}
							<a class="table-btn add" href="/manuf/create/?manuf_key={$manuf.key}"></a>
						{/if}
					</td>
					<td class="small">
						{if $account->isPermission('manuf', 'del')}
							<div class="table-btn delete" title="Удалить производителя" data-key="{$manuf.key}"></div>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
{/if}
	</table>

<div class="popup-window popup-window-addManuf" title="Создание производителя">
    <form action="/manuf/add/" enctype="multipart/form-data">
		<table class="ribbed">
			<tr>
				<td class="td-title">Название</td>
				<td><input type="text" name="title" /></td>
			</tr>
		</table>
        <div class="buttons">
			<div class="submit a-button-blue">Добавить</div>
		</div>
    </form>
</div>

<div class="popup-window popup-window-addManufCover">
    <form action="/manuf/addCover/" enctype="multipart/form-data">
        <input type="hidden" name="manuf_key" />
        <input type="hidden" name="need_width" value="{$cover_width}" />
        <input type="hidden" name="need_height" value="{$cover_height}" />
		<table class="ribbed">
			<tr>
				<td class="td-title">Выберите изображение</td>
				<td><input type="file" name="cover" /></td>
			</tr>
		</table>
        <div class="buttons">
			<div class="submit a-button-blue">Загрузить</div>
		</div>
    </form>
</div>

<div class="popup-window popup-window-addManufFile">
    <form action="/manuf/addFile/" enctype="multipart/form-data">
        <input type="hidden" name="manuf_key" />
		<table class="ribbed">
			<tr>
				<td class="td-title">Выберите файл</td>
				<td><input type="file" name="file" /></td>
			</tr>
		</table>
        <div class="buttons">
			<div class="submit a-button-blue">Загрузить</div>
			<a class="remove-file-btn a-button-orange" data-manuf_key="" href="/manuf/addFile/">Удалить</a>
		</div>
    </form>
</div>

<div class="popup-window popup-errors">
	<div class="error-text">
	</div>
	<div class="buttons">
		<div class="close-popup a-button-blue">Закрыть</div>
	</div>
</div>