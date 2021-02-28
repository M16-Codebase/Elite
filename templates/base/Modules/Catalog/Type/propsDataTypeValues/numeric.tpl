<div class="white-block-row">
	<div class="w3">
		<span>Минимальное значение</span>
	</div>
	<div class="w9">
		<input name="values[min]" type="text" class="m-small"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span>Максимальное значение</span>
	</div>
	<div class="w9">
		<input name="values[max]" type="text" class="m-small"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span>Шаг</span>
	</div>
	<div class="w9">
		<input name="values[step]" type="text" class="m-small"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span>Значение по умолчанию</span>
	</div>
	<div class="w9">
		<input name="default_value" type="text" class="m-small"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
	</div>
</div>