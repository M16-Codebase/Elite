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
        <th>Пользователь</th>
		<th>Начало</th>
		<th>Окончание</th>
		<th>Коммент</th>
		<th>Статус</th>
		<th>Ошибки</th>
	</tr>
	{if !empty($tasks)}
		{foreach from=$tasks item=task key=task_id}
			<tr data-task_id="{$task_id}">
				<td>{if !empty($regions[$task.segment_id])}{$regions[$task.segment_id]['title']}{else}Регион удален{/if}</td>
				<td><a href="/catalog-item/?id={$task.type_id}" title="{$task.type_title}">{$task.type_id}</a></td>
                <td><span title="{$task.user_email}">{if !empty($task.user_id)}{$task.user_id}{else}&mdash;{/if}</span></td>
				<td>{$task.timestamp_start|date_format:'%d.%m.%Y %H:%M:%S'}</td>
				<td>{$task.timestamp_end|date_format:'%d.%m.%Y %H:%M:%S'}</td>
				<td>
                    {if $task.status == 'complete'}
                        {?$comment_str = $task['comment']}
                        {?$comment = explode(';', $comment_str)}
                        {?$count_items = explode(':', $comment[0])}
                        {?$count_variants = explode(':', $comment[1])}
                        Загружено:<br />
						{$count_items[1]|plural_form:'товар','товара','товаров'} и
						{$count_variants[1]|plural_form:'вариант','варианта','вариантов'}
                    {elseif $task.status == 'in_work'}
                        {if !empty($task.comment)}Загружено:<br />{$task.comment}{/if}
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
{include file="Admin/components/paging.tpl" show=5}
<div class="popup-window popup-errors_log">

</div>