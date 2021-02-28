<input type="hidden" name="id">
<table class="ribbed edit-property">
	<tbody>
		<tr>
			<td class="td-title">Сокращение</td>
			<td>
                <input type="text" name="short_name" class="short-name-input" maxlength="2">
				<div class="small-descr">Не больше 2 символов, например «O2»</div>
			</td>
		</tr>
		<tr>
			<td class="td-title">Цвет</td> 
			<td>
				<input class="color-input" type="hidden" name="color">
				<div class="using-preview a-right">
					<div class="using-circle m-big"></div>
				</div>
				<ul class="color-select-list">
					{foreach from=$colors key=color item=status}
						<li class="color-select-btn{if $status == 'selected'} m-current{elseif $status == 'disabled'} disabled{/if}" data-color="{$color}" style="background-color: {$color};"></li>
					{/foreach}
				</ul>
			</td>
		</tr>
		<tr>
			<td class="td-title">Полное название</td>
			<td><input type="text" name="title"></td>
		</tr>
	</tbody>
</table>
<div class="buttons">
	<button class="submit a-button-blue">Сохранить</button>
</div>