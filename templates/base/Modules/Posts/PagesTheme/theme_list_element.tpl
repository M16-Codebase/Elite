{foreach from=$themes_list key=k item=theme}
	<div class="wblock white-block-row{if !empty($child) && $child} values{/if}" data-id="{$theme.id}" data-position="{$theme.position}">
		<div class="drag-drop w05"></div>
		<a href="/{$moduleUrl}/?theme={$theme.id}{if !empty($theme.count) && $constants.segment_mode != 'none'}&s=1{/if}" class="w5">
			{if is_array($theme.title)}
				{if isset($theme.title[$request_segment.id])}{$theme.title[$request_segment.id]}{else}unknown title{/if}
			{else}
				{$theme.title}
			{/if}
		</a>
		<div class="w2"></div>
		<div class="w2">{$theme.count}</div>
		<div class="w05"></div>
		<a href="/{$moduleUrl}/editTheme/" class="action-button action-edit w1" title="Редактировать">
			<i class="icon-edit"></i>
		</a>
		<a href="/{$moduleUrl}/delTheme/" class="action-button action-delete delete-theme m-border w1" title="Удалить">
			<i class="icon-delete"></i>
		</a>
	</div>
	{?$child = false}
{/foreach}