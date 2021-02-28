<div class="white-block-row">
	<div class="w3">
		<span>Свойство</span>
	</div>
	<div class="w9">
		<select name="values">
			<option value="">Выберите свойство...</option>
			{foreach from=$range_properties item=prop}
				{if $prop.id!=$property.id && $prop.multiple == 1 && in_array($prop.data_type, array('int', 'float'))}
					<option value="{$prop.key}">{$prop.title}{if $prop.default_prop} (default){/if}</option>
				{/if}
			{/foreach}
		</select>
	</div>
</div>