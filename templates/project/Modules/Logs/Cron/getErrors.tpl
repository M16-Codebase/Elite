{if !empty($error_logs)}
	<div class="logs-table-cont">
		<div class="logs-table-header">
			<table class="ribbed">
				<tr>
					<th>Строка</th>
					<th>
						<a href="{$unsuccess_path}" class="small-descr a-right">Скачать файл</a>
						Текст ошибки
					</th>
				</tr>
			</table>
		</div>
		<div class="logs-table-scrolled">
			<table class="ribbed">
				<tr>
					<th>Строка</th>
					<th>Текст ошибки</th>
				</tr>
				{foreach from=$error_logs key=number item=error name=errors_logs}
					<tr{if first} class="first"{/if}>
						<td class="small">{$number}</td>
						<td>{$error}</td>
					</tr>
				{/foreach}
			</table>
		</div>
	</div>
{else}
    {$task.errors}
{/if}