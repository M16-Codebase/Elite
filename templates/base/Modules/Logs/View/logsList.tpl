{if !empty($logs)}
	{foreach from=$logs item=l name=logs}
		<div class="wblock white-block-row" title="Номер {$l.id}">
			<div class="w3">
				<div>{str_replace(',', '.', $l.time)|date_format:'%d.%m.%Y %H:%M'}</div>
				{?$it_user = !empty($l.user_id) ? $users[$l.user_id] : NULL}
				{if !empty($it_user)}<a href="mailto:{$it_user->getEmail()}">{$it_user->getEmail()}</a>
				{else}<em>Автоматическое обновление</em>{/if}
				<div>{if !empty($segments[$l.segment_id])}{$segments[$l.segment_id]['title']}{/if}</div>
			</div>
			<div class="w9">
				{if in_array($l.entity_type, array('item', 'variant', 'banner', 'city', 'collection', 'config', 'days', 'engine_system', 'file', 'filial', 'filial_region', 'icon', 'image', 'item_type', 'manuf', 'order', 'pool', 'post', 'post_theme', 'property', 'region', 'user', 'video', 'video_type'))}
					{include file="Modules/Logs/View/types/". $l.entity_type .".tpl" logged_fields = (!empty($logged_fields[$l.entity_type]) ? $logged_fields[$l.entity_type] : NULL)}
				{else}
					[Неизвестное событие: «{$l.entity_type}»]
				{/if}
			</div>
		</div>
	{/foreach}
	{include file="Admin/components/paging.tpl" show=5}
{else}
	<div class="wblock white-block-row">
		<div class="w12">{if !empty($empty_necessary_params)}Выберите тип события или инициатора события{else}Еще не было событий{/if}</div>
	</div>
{/if}