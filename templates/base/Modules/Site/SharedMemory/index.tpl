Id: {$shm_id}<br />
Лимит: {if !empty($shm_limit)}{$shm_limit}{else}Использование разделяемой памяти выключено{/if}<br />
Текущий размер: {if !empty($shm_length)}{$shm_length}{else}0{/if}<br />
{*Данные: <br />
<pre>{$shm_data|var_dump}</pre>*}
<br /><a href="/shared-memory/delete/">Удалить все данные</a>
