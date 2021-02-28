{if $constants.segment_mode == 'lang'}
	<div class="white-block-row">
		<div class="w3">
			<span>Значение <img src="/templates/Admin/img/flag-yes.gif" /></span>
		</div>
		{foreach from = $segments item=$s}
			<div class="w45">
				<input type="text" name="values[yes][{$s.id}]"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
			</div>
		{/foreach}
	</div>
	<div class="white-block-row">
		<div class="w3">
			<span>Значение <img src="/templates/Admin/img/flag-no.gif" /></span>
		</div>
		{foreach from = $segments item=$s}
			<div class="w45">
				<input type="text" name="values[no][{$s.id}]"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
			</div>
		{/foreach}
	</div>
{else}
	<div class="white-block-row">
		<div class="w3">
			<span>Значение <img src="/templates/Admin/img/flag-yes.gif" /></span>
		</div>
		<div class="w9">
			<input type="text" name="values[yes]"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
		</div>
	</div>
	<div class="white-block-row">
		<div class="w3">
			<span>Значение <img src="/templates/Admin/img/flag-no.gif" /></span>
		</div>
		<div class="w9">
			<input type="text" name="values[no]"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
		</div>
	</div>
{/if}

<div class="white-block-row">
	<div class="w3">
		<span>Значение по умолчанию</span>
	</div>
	<div class="w9">
		<select name="default_value">
			<option value="">Выберите...</option>
			<option value="1">Да</option>
			<option value="0">Нет</option>
		</select>
	</div>
</div>