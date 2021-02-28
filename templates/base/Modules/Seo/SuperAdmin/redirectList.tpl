{if !empty($urls)}
	{foreach from=$urls item=url}
		<div class="wblock white-block-row">
			<div class="w5 view_from">{$url.fr}</div>
			<div class="w5 view_to"{*{if !empty($url.old_to)} style="color: #00aa00"{elseif isset($url.old_to)} style="color: #00F"{/if}*}>{$url.to}</div>
			<div class="action-button action-edit w1 m-border" title="Редактировать">
				<i class="icon-edit"></i>
			</div>
			<div class="action-button action-delete w1 m-border" title="Удалить">
				<i class="icon-delete"></i>
			</div>
		</div>
	{/foreach}
{else}
	<div class="white-blocks">
		<div class="wblock white-block-row">
			<div class="w12">Редиректы не созданы</div>
		</div>
	</div>
{/if}
