<div class="white-block-row">
	<div class="w3">
		<span class="text-icon">Маска ввода</span>
		{include file="Admin/components/tip.tpl" content="Можно задать формат ввода значений.<br /><strong>a</strong> (латинского алфавита) - любая буква<br /><strong>9</strong> - любая цифра<br /><strong>*</strong> - либо буква либо цифра<br /><strong>?</strong> - всё что после - не обязательно для заполнения.<br />Если до знака вопроса(или если в маске нет вопроса) не заполнен хоть один символ, то всё значение будет пустым.<br />Любые другие символы будут подставляться автоматически.<br />Пример 1: <strong>99-99-99</strong> - можно будет ввести только цифры, дефис подставится автоматически<br />Пример 2: <strong>aa-9999*</strong> - можно будет ввести сначала две буквы, дефис подставится автоматически, потом 4 цифры, потом либо букву либо цифру<br />Пример 3: <strong>aaК99/9?9a</strong> - две буквы, буква K подставится автоматически, две цифры, наклонная черта подставится автоматически, цифра. Далее следуют необязательные: цифра, потом буква."}
	</div>
	<div class="w9">
		<input type="text" name="values"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
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
<div class="white-block-row">
	<div class="w3">
		<span>Валидация</span>
	</div>
	<div class="w9">
		<select name="validation[mode]">
			<option value=""{if empty($property.validation.mode)} selected="selected"{/if})>Выкл</option>
			<option value="preset">Предустановленные типы</option>
			<option value="sel_opts">Название пункта про галочки</option>
			<option value="regex">Регулярное выражение</option>
		</select>
		{*<input name="validation[mode]" type="radio" value="off" />&nbsp;Выкл<br />*}
		{*<input name="validation[mode]" type="radio" value="preset" />&nbsp;Предустановленные типы<br />*}
		{*<input name="validation[mode]" type="radio" value="sel_opts" />&nbsp;Название пункта про галочки<br />*}
		{*<input name="validation[mode]" type="radio" value="regex" />&nbsp;Регулярное выражение*}
		{*<input name="validation[mode]" type="radio" value="off"{if empty($property.validation.mode) || $property.validation.mode == 'off'} checked="checked"{/if} />&nbsp;Выкл<br />*}
		{*<input name="validation[mode]" type="radio" value="preset"{if !empty($property.validation.mode) && $property.validation.mode == 'preset'} checked="checked"{/if} />&nbsp;Предустановленные типы<br />*}
		{*<input name="validation[mode]" type="radio" value="sel_opts"{if !empty($property.validation.mode) && $property.validation.mode == 'sel_opts'} checked="checked"{/if} />&nbsp;Название пункта про галочки<br />*}
		{*<input name="validation[mode]" type="radio" value="regex"{if !empty($property.validation.mode) && $property.validation.mode == 'regex'} checked{/if} />&nbsp;Регулярное выражение*}
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span>Тип данных</span>
	</div>
	<div class="w9">
		<select name="validation[preset]">
			<option value="">Выберите...</option>
            {foreach from=$string_validation_presets item=v_preset}
                <option value="{$v_preset.key}">{$v_preset.title}</option>
            {/foreach}
		</select>
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span>Всякие галочки</span>
	</div>
	<div class="w9">
		<div class="export-list">
			<label><input name="validation[sel_opts][digits]" type="checkbox" value="1" />&nbsp;Цифры</label>
		</div>
		<div class="export-list">
			<label><input name="validation[sel_opts][cyrillic]" type="checkbox" value="1" />&nbsp;Русские буквы</label>
		</div>
		<div class="export-list">
			<label><input name="validation[sel_opts][english]" type="checkbox" value="1" />&nbsp;Английские буквы</label>
		</div>
		<div class="export-list">
			<label>Дополнительные символы&nbsp;<input class="m-small" type="text" name="validation[sel_opts][symbols]" /></label>
		</div>
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span>Регулярное выражение</span>
	</div>
	<div class="w9">
		<input type="text" name="validation[regex]" />
	</div>
</div>
