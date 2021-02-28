{if !empty($url_list)}
	{foreach from=$url_list item=url_data}
		<div class="wblock white-block-row{if !$url_data.valid} invalid{/if}" data-id="{$url_data.id}">
			<div class="w1"><input class="check-item" type="checkbox" name="ids[]" value="{$url_data.id}"></div>
			<div class="w3">{$url_data.url}</div>
			<div class="w3">{$url_data.priority}</div>
			<div class="w3">{$url_data.timestamp|date_format:'%d.%m.%Y %H:%M:%S'}</div>
			<div class="w1 action-button action-edit" title="Редактировать"><i class="icon-edit"></i></div>
			<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
			{*<a href="#" data-id="{$url_data.id}" data-url="{$url_data.url}" data-pri="{$url_data.priority}" data-mod="{$url_data.last_modification}" class="action-button action-edit edit-link-btn w1" title="Редактировать"><i></i></a>
			<a href="#" data-id="{$url_data.id}" data-url="{$url_data.url}" data-pri="{$url_data.priority}" data-mod="{$url_data.last_modification}" data-id="{$url_data.id}" class="action-button action-delete delete-link-btn w1" title="Удалить"><i></i></a>*}
		</div>
	{/foreach}
	{else}
		<div class="white-body">
			<div class="wblock white-block-row">
				<div class="w12"> Параметры выборки не созданы</div>
			</div>
		</div>
{/if}
