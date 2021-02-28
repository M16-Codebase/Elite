{if $property.data_type == 'enum'}
	<select name="values[]" class="new-val"{if $property.set} multiple{/if} data-segment_id="0">
		<option value="">Выберите...</option>
		{foreach from=$property.values item=enum_val}
			<option value="{$enum_val.id}">{$enum_val.value}</option>
		{/foreach}	
	</select>
{elseif $property.data_type == 'flag'}
	<select name="values[]" class="new-val flag-vals" data-segment_id="0">
		<option value="">Не определено</option>
		<option value="0">Нет</option>
		<option value="1">Есть</option>
	</select>
{else}
	<input type="text" name="values[]" class="new-val" data-type="{$property.data_type}" data-segment_id="0" />
{/if}