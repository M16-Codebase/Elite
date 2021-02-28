{*{if !empty($logs)}
	<div class="white-blocks">
		<div class="wblock white-header white-block-row">
			<div class="w2">Номер</div>
			<div class="w2">Тип обращения</div>
			{?$current_sort = 0}
			{if isset($smarty.get.order.time)}
				{?$current_sort = 1}
				{?$sort_val = ($smarty.get.order.time == 1)? 0 : 1}
			{else}{?$sort_val = 0}{/if}
			<a href="/hr-feedback/?order[time]={$sort_val}" data-sort="order[time]" data-val="{$sort_val}" class="w2 sort-link{if $current_sort} m-sort-{$sort_val}{/if}">
				Дата			
			</a>
			<div class="w2">Имя</div>
			<div class="w2">Телефон</div>
			<div class="w2">E-mail</div>
			<div class="w1"></div>
		</div>
		<div class="white-body">	
			{foreach from=$logs item=log name=logs}
				{?$log_type = $log->getType()}
				<div class="wblock white-block-row">
					<div class="w1"><strong>{$log.full_number}</strong></div>
					<div class="w2 m-border">
						<strong>{$log_type.title}</strong>
						{$log.treat_status}
					</div>
					<div class="w2 m-border">{if !empty($log.timestamp)}{$log.timestamp|date_format:'%d.%m.%Y <br> <span class="m-gray">%H:%M</span>'|html}{/if}</div>
					<div class="w2 m-border">{if !empty($log.author)}{$log.author}{/if}</div>
					<div class="w2 m-border">{if !empty($log.phone)}{$log.phone}{/if}</div>
					<div class="w2 m-border">{if !empty($log.email)}<a href="mailto:{$log.email}">{$log.email}</a>{else}—{/if}</div>
					<div class="action-button action-site open-form w1 m-border" data-id="{$log.id}" title="Открыть обращение">
						<i class="icon-site"></i>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
	{include file="Admin/components/paging.tpl"}
{else}
	<div class="white-blocks">
		<div class="wblock white-block-row">
			<div class="w12">Обращений нет</div>
		</div>
	</div>
{/if}*}
{include file="Modules/Feedback/Main/logsList.tpl"}