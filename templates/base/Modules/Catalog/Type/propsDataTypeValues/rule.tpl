<div class="white-block-row">
	<div class="w3">
		<span>Каталог</span>
	</div>
	<div class="w9">
        <select name="values[catalog]">
            {foreach from=$catalogs_list item=cat}
                {if !in_array($cat.key, array('orders', 'brands', 'config'))}
                    <option value="{$cat.key}">{$cat.title}</option>
                {/if}
            {/foreach}
        </select>
		{*<input name="values[catalog]" type="text" class="m-small"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />*}
	</div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span>Максимальное значение</span>
	</div>
	<div class="w9">
        <select name="values[entity]">
            <option value="item">Айтем</option>
            <option value="variant">Вариант</option>
        </select>
	</div>
</div>