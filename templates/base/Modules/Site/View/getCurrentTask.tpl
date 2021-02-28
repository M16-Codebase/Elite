{?$end_task_name = array(
    'minutely end',
    'hourly end',
    'daily end',
    'weekly end'
)}
{if !empty($current_task) && !in_array($current_task.name, $end_task_name)}{$current_task.name} - {$current_task.start}{else}Нет текущих задач{/if}