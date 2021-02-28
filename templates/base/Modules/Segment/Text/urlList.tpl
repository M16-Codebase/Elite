{foreach from=$urls item=url_data}
	<div class="wblock white-block-row" data-id="{$url_data.id}" data-position="{$url_data.position}">
		<div class="w05 drag-drop"></div>
		<a href="/segment-text/urlSection/?id={$url_data.id}" class="w6">
			<span>{$url_data.title}</span>
		</a>
		<div class="w3">
			{$url_data.url}
		</div>
		<div class="w05"></div>
		<div class="action-button action-edit w1 m-border" title="Переименовать">
			<i class="icon-edit"></i>
		</div>
		<div class="action-button action-delete w1 m-border" title="Удалить" data-delname="группы текстов">
			<i class="icon-delete"></i>
		</div>
	</div>
{/foreach}