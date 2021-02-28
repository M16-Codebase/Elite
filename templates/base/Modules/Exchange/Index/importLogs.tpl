{?$admin_page = 1}
{?$status_rus = array(
    'in_work' => 'В работе',
    'new' => 'Новая',
    'complete' => 'Завершена',
    'cancel' => 'Отменена'
)}
<table class="ribbed">
	<tr>
		<th>Регион</th>
		<th>Тип</th>
		<th>Начало</th>
		<th>Окончание</th>
		<th>Коммент</th>
		<th>Статус</th>
		<th>Ошибки</th>
	</tr>
	{if !empty($tasks)}
		{foreach from=$tasks item=task key=task_id}
			<tr data-task_id="{$task_id}">
				<td>{if !empty($segments[$task.segment_id])}{$segments[$task.segment_id]['title']}{else}Регион удален{/if}</td>
				<td>{$task.type_id}</td>
				<td>{$task.timestamp_start|date_format:'%d.%m.%Y %H:%M:%S'}</td>
				<td>{$task.timestamp_end|date_format:'%d.%m.%Y %H:%M:%S'}</td>
				<td>
                    {if $task.status == 'complete'}
                        {?$comment_str = $task['comment']}
                        {?$comment = explode(';', $comment_str)}
                        {?$count_items = explode(':', $comment[0])}
                        {?$count_variants = explode(':', $comment[1])}
                        Загружено: {$count_items[1]|plural_form:'товар','товара','товаров'} и {$count_variants[1]|plural_form:'вариант','варианта','вариантов'}
                    {elseif $task.status == 'in_work'}
                        {if !empty($task.comment)}Загружено: {$task.comment}{/if}
                    {else}
                        {$task.comment}
                    {/if}
                </td>
				<td>{$status_rus[$task.status]}</td>
				<td>{if !empty($task.errors_count)}<a href="#" class="errors_count">{$task.errors_count}</a>{else}0{/if}</td>
			</tr>
		{/foreach}
	{/if}
</table>

<div class="popup-window popup-errors_log">

</div>