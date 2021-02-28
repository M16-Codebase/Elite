<div class="white-block-row">
	<div class="w3">
		<span class="text-icon">Архив 3D-тура</span>
		{include file="Admin/components/tip.tpl" content=""}
	</div>
	<div class="w9">
        <input type="hidden" name="values[swfzip]" value="0"{if $unchangeable && in_array('set', $unchangeableParamsByProps)} disabled{/if}  />
        <input type="checkbox" name="values[swfzip]" value="1"{if $unchangeable && in_array('set', $unchangeableParamsByProps)} disabled{/if} />
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span class="text-icon">Допустимые форматы</span>
		{include file="Admin/components/tip.tpl" content="Допустимые расширения файла через запятую."}
	</div>
	<div class="w9">
		<input name="values[format]" type="text" />
	</div>
</div>
<div class="white-block-row">
	<div class="w3"></div>
	<span class="w9">
		{foreach from = $allow_file_types item = file_types name = foo}
			{$file_types}{if !$smarty.foreach.foo.last}, {/if}
		{/foreach}
	</span>
</div>
<div class="white-block-row">
	<div class="w3">
		<span class="text-icon">Макимальный размер</span>
		{include file="Admin/components/tip.tpl" content="Максимальный размер файла в байтах, например 500 - 500 байт, 15000 - 15 килобайт, 2000000 - 2 мегабайта."}
	</div>
	<div class="w9">
		<input name="values[max]" type="text" class="m-small" />
	</div>
</div>