{if !empty($error_logs)}
	<table class="ribbed">
		{foreach from=$error_logs item=error}
			<tr>
				<td>{$error.row_num}</td>
				<td>{$error.message}</td>
			</tr>
		{/foreach}
	</table>
{/if}