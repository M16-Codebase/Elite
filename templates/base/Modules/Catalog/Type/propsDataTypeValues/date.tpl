{*<div class="white-block-row">*}
	{*<div class="w3">*}
		{*<span class="text-icon">Маска ввода</span>*}
		{*{include file="Admin/components/tip.tpl" content="Можно задать формат ввода значений.<br /><strong>a</strong> (латинского алфавита) - любая буква<br /><strong>9</strong> - любая цифра<br /><strong>*</strong> - либо буква либо цифра<br /><strong>?</strong> - всё что после - не обязательно для заполнения.<br />Если до знака вопроса(или если в маске нет вопроса) не заполнен хоть один символ, то всё значение будет пустым.<br />Любые другие символы будут подставляться автоматически.<br />Пример 1: <strong>99-99-99</strong> - можно будет ввести только цифры, дефис подставится автоматически<br />Пример 2: <strong>aa-9999*</strong> - можно будет ввести сначала две буквы, дефис подставится автоматически, потом 4 цифры, потом либо букву либо цифру<br />Пример 3: <strong>aaК99/9?9a</strong> - две буквы, буква K подставится автоматически, две цифры, наклонная черта подставится автоматически, цифра. Далее следуют необязательные: цифра, потом буква."}*}
	{*</div>*}
	{*<div class="w9">*}
		{*<input type="text" name="values"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />*}
	{*</div>*}
{*</div>*}
<div class="white-block-row">
	<div class="w3">
		<span>Значение по умолчанию</span>
	</div>
	<div class="w9">
		<input name="default_value" type="text" class="m-small"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
	</div>
</div>