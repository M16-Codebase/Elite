{?$admin_left_menu = 1}
<form method="GET">
	Кто падла: <select name="user_id">
		<option value="">-</option>
		{foreach from=$users item=$u}
			<option value="{$u->getId()}">{$u->getEmail()}</option>
		{/foreach}
	</select>
	Что ищем: <select name="search">
		<option value="">-</option>
		<option value="type">Type</option>
		<option value="item">Item</option>
		<option value="prop">Prop</option>
		<option value="variant">Variant</option>
	</select>&nbsp;&nbsp;&nbsp;
	#id :<input type="text" name="id" /><br />
	С <input class="datepicker" type="text" name="date_begin" /><input type="text" name="time_begin" />&nbsp;&nbsp;&nbsp;
	До<input class="datepicker" type="text" name="date_end" /><input type="text" name="time_end" /><br />
	<input type="submit" value="Фильтр" />
</form>
{if !empty($result)}
	<style>
		{literal}
			.userLogs TD{
				border: 1px solid black;
			}
		{/literal}
	</style>
	<table class="userLogs">
		<tr>
			<th>Кто</th>
			<th>Когда</th>
			<th>Что</th>
			<th>Data</th>
		</tr>
		{foreach from=$result item="r"}
			<tr>
				<td>{$r.user_id}</td>
				<td>{$r.time}</td>
				<td>{$r.event}</td>
				<td>
					{include file='Modules/Catalog/SuperAdmin/data.tpl' tab='' data=$r.data iteration=1}
				</td>
			</tr>
		{/foreach}
	</table>
	{include file="components/paging.tpl"}
{/if}