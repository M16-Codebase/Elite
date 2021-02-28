{?$admin_page = 1}
{?$pageTitle = 'Логи крона — ' . (!empty($confTitle) ? $confTitle : '')}
{?$status_rus = array(
    'processed' => 'В работе',
    'new' => 'Новая',
    'complete' => 'Завершена',
    'cancel' => 'Отменена',
    'sent' => 'Отправлено'
)}
<div class="content-top">
	<h1>Логи крона</h1>
</div>
<div class="content-scroll">
	<div class="viewport">
		<div class="white-blocks">
			<div class="wblock white-block-row white-header">
				<div class="w2">Задача</div>
                {if $constants.segment_mode != 'none'}
                    <div class="w1">Сегмент</div>
                {/if}
				<div class="w1">Юзер</div>
				<div class="w1">Начало</div>
				<div class="w1">Время</div>
				<div class="w1">%</div>
				<div class="w1">Статус</div>
				<div class="w2">Коммент</div>
				<div class="w1">Ошибки</div>
                {if $account->isPermission('cron-shedule', 'stopTask')}
                    <div class="w1">Действие</div>
                {/if}
			</div>
			{if !empty($tasks)}
				{foreach from=$tasks item=task key=task_id}
					<div class="wblock white-block-row" data-task_id="{$task_id}">
						<div class='w2'>{if !empty($task_title[$task.type])}{$task_title[$task.type]}{else}{$task.type}{/if}</div>
                        {if $constants.segment_mode != 'none'}
                            <div class='w1'>{if !empty($segments[$task.segment_id])}{$segments[$task.segment_id]['key']}{elseif !empty($task.segment_id)}Сегмент ID: {$task.segment_id} удален{/if}</div>
                        {/if}
						<div class='w1'><span title="{$task.user_email}">{if !empty($task.user_id)}{$task.user_id}{else}&mdash;{/if}</span></div>
						<div class='w1'>{$task.timestamp_create|date_format:'%d.%m.%Y %H:%M:%S'}</div>
						<div class='w1'>
							<span title='{$task.timestamp_start|date_format:'%d.%m.%Y %H:%M:%S'} - {$task.timestamp_end|date_format:'%d.%m.%Y %H:%M:%S'}'>
								{if !empty($task.timestamp_end) && !empty($task.timestamp_start)}
									{?$seconds = $task.timestamp_end - $task.timestamp_start}
									{if (!empty($seconds))}
										{?$hours = floor($seconds / 60 / 60)}
										{?$minutes = floor(($seconds / 60) - ($hours * 60))}
										{?$seconds = $seconds - ($minutes * 60) - ($hours * 60 * 60)}
										{$hours}ч{$minutes}м{$seconds}с
									{else}
										0
									{/if}
								{else}&mdash;
								{/if}
							</span>
						</div>
						<div class='w1'>{if isset($task.percent)}{$task.percent}%{/if}</div>
						<div class='w1'>{$status_rus[$task.status]}{if !empty($task.event)}<i>{$task.event}</i>{/if}</div>
						<div class='w2'>
							{*комменты у всех разные*}
							{include file='Modules/Logs/Cron/types/'.$task.type.'.tpl'}
						</div>
						<div class='w1'>{if !empty($task.errors)}<a href="#" class="errors_count" title="{$task.errors}">1</a>{elseif !empty($task.count_errors)}<a href="#" class="errors_count" title="Нажмите, чтобы увидеть список ошибок">{$task.count_errors}</a>{else}{if $task.status == 'complete'}0{/if}{/if}</div>
                        {if $account->isPermission('cron-shedule', 'stopTask')}
                            <div class="w1">
                                {if $task->isStoppable()}<a href="#" class="set_event stop" data-event="stop">Остановить</a><br />{/if}
                                {if $task->isCancelable()}<a href="#" class="set_event cancel" data-event="cancel">Отменить</a><br />{/if}
                                {if $task->isRestartable()}<a href="#" class="set_event restart" data-event="restart">Возобновить</a><br />{/if}
                            </div>
                        {/if}
					</div>
				{/foreach}
			{/if}
		</div>
		{include file="Admin/components/paging.tpl" show=5}
	</div>
</div>
<div class="popup-window popup-errors_log">

</div>