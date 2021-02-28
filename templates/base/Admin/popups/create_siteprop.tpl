{*siteprop was here*}
{*old markup*}
<form action="/site-config/create/" class="add-form add-prop">
	<input type="hidden" name="type"{if !empty($param_type)} value="{$param_type}"{/if} />
	<div class="white-blocks">
		<div class="wblock white-block-row">
			<div class="w3">Ключ</div>
			<div class="w9">
				<input type="text" name="key" class="title_input bold" />
			</div>
		</div>
		<div class="wblock white-block-row">
			<div class="w3">Значение</div>
			<div class="w9">
				<input type="text" name="value" class="title_input bold" />
			</div>
		</div>
		<div class="wblock white-block-row">
			<div class="w3">Описание</div>
			<div class="w9">
				<input type="text" name="description" class="title_input bold" />
			</div>
		</div>
		<div class="wblock white-block-row"r>
			<div class="w3">Тип данных</div>
			<div class="w9">
				<select name="data_type">
					<option value="text">text</option>
					<option value="checkbox">checkbox</option>
					<option value="textarea">textarea</option>
				</select>
			</div>
		</div>
	</div>
	<input type="hidden" name="add" value="1" />
</form>

