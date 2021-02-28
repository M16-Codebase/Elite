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
				{foreach from=$error_logs item=error name=errors_logs}
					<tr{if first} class="first"{/if}>
						<td class="small">{$error.row_num}</td>
						<td>{$error.message}</td>
					</tr>
				{/foreach}
			</table>
		</div>
	</div>	
{/if}