<div class="not-multiple white-block-row{if $property.multiple} a-hidden{/if}">
	<div class="w3">
		<span>Значение</span>
	</div>
	<div class="w9">
		<textarea name="values" rows="5" id="props_values_combine"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if}></textarea><br />
		{if !$unchangeable || !in_array('values', $unchangeableParamsByProps)}
			<select name="temp_props">
				<option value="0">Выбор свойств...</option>
				{foreach from=$type_properties item=prop}
					{if $prop.id!=$property.id && $prop.multiple != 1}
						<option value="{$prop.key}">{$prop.title}</option>
					{/if}
				{/foreach}
			</select>
		{/if}
	</div>
</div>
<div class="multiple white-block-row{if !$property.multiple} a-hidden{/if}">
	<div class="w3">
		<span>Значение</span>
	</div>
	<div class="w9">
		<textarea name="values" rows="5" id="props_values_combine_multiple"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if}></textarea><br />
		<select name="temp_props_variant">
			<option value="0">Выбор свойств...</option>
			{foreach from=$variants_properties item=prop}
				{if $prop.id!=$property.id}
					<option value="{$prop.key}">{$prop.title}</option>
				{/if}
			{/foreach}
		</select>
	</div>
</div>