{*
	$variant_list - флаг, означающий будем ли мы использовать свойства для вариантов или только для товара
*}
{if !empty($variant_list) || empty($item_properties)}{*редактируемые поля для товаров и вариантов разные*}
	{?$type_properties_run = !empty($item_variants_properties) ? $item_variants_properties : null}
{else}
	{?$type_properties_run = $item_properties}
{/if}

{if !empty($type_properties_run)}
	<table class="ribbed edit-property"{if !empty($editing_variant_id)} data-variant_id="{$editing_variant_id}"{/if}>		
		{if !empty($variant_list)}
			{?$part = ''}
			{if !empty($catalog_item_variants) || !empty($variant_create)}
				{if !empty($variant_create) && empty($editing_variant_id)}{*при создании варианта*}
					{?$variant_id_tmp = 0}
				{elseif !empty($catalog_item_variants['id'])}
					{if empty($variant)}{?$variant = $catalog_item_variants}{/if}
					{?$variant_id_tmp = $catalog_item_variants['id']}{*при редактировании свойств товара с одним дефолтным вариантом*}
				{else}
					{?$variant_id_tmp = $editing_variant_id}{*при редактировании одного из вариантов*}
				{/if}
				{?$part = "[". $variant_id_tmp ."]"}
			{/if}
			{?$name="prop_variant". $part}
		{else}
			{?$name="prop"}
		{/if}
		{?$propExceptions = array('color'=>1, 'available_variant'=>1, 'count' => 1, 'visible' => 1, 'variant_visible' => 1)}
		{?$firstProps = ''}
		{?$otherProps = ''}
		{if !empty($specials) && empty($variant_list)}
			{foreach from=$specials item=sp_group key=group_title}
				<tr>
					<td class="td-title sp">{$group_title}</td>
					<td>
						{foreach from=$sp_group item=data}
							<input type="hidden" name="{$name}[{$data.id}]" value="0" />
							<label class="specials"><input type="checkbox" name="{$name}[{$data.id}]" value="1" /> {$data.title}</label>
						{/foreach}
					</td>
				</tr>
			{/foreach}
		{/if}
		{if empty($variant_list) && !empty($catalog_item.id)}
			<tr class="">
				<td class="td-title">ID</td>
				<td><div class="disabled-input">{$catalog_item.id}</div></td>
			</tr>
		{/if}
		{foreach from=$type_properties_run item=property key=prop_id}
            {if $property.key == 'title'}
                {capture assign=firstProps name=firstProps}
                    {$firstProps|html}
                    <tr class="{if $property.necessary != 0}req{/if}{if $property.unique != 0} unique{/if}">
                        <td class="td-title">{$property.title}</td>
                        <td><input type="text" name="{$name}[{$prop_id}]" disabled /></td>
                    </tr>
                {/capture}
            {elseif $property.key == 'variant_code'}
                {capture assign=firstProps name=firstProps}
                    {$firstProps|html}
                    <tr class="{if $property.necessary != 0}req{/if}{if $property.unique != 0} unique{/if}">
                        <td class="td-title">{$property.title}</td>
                        <td><div class="disabled-input">{$catalog_variant['code']}</div></td>
                    </tr>
                {/capture}
            {else}
                {capture assign=otherProps name=otherProps}
                    {$otherProps|html}
                    {if !empty($propExceptions[$property.key]) || ($property.read_only == 1 && !empty($action) && $action == 'createItem') || empty($properties_available[$prop_id]['available']) || $property.fixed == 2}
                        {*Пропускаем*}
                        {*<tr class="a-hidden"><td></td><td><input class="chooseColor" type="text" name="{$name}[{$prop_id}]" /></td></tr>*}
                    {elseif $property.read_only == 1}
                        {if empty($create)}
                            <tr class="{if $property.necessary != 0}req{/if}{if $property.unique != 0} unique{/if}">
                                <td class="td-title">{$property.title}</td>
                                <td>
                                    <div class="param_value prop-{$property.key}">
                                        {capture assign="input_form"}
                                            <input type="text" name="{$name}[{$prop_id}]" class="{$property.data_type}" disabled />
                                        {/capture}
                                        {?$masked_input = $property.mask|replace:ldelim . "!" . rdelim:$input_form}
                                        {$masked_input|html}
                                    </div>
                                </td>
                            </tr>
                        {/if}
                    {else}
                        <tr class="{if $property.necessary != 0}req{/if}{if $property.unique != 0} unique{/if}">
                            <td class="td-title">{$property.title}</td>
                            <td>
                                <div class="param_value{if $property.unique != 0} unique{/if} prop-{$property.key}" data-prop_key="{$property.key}">
                                    {if $property.key == 'collection'}
                                        <select{if $property.set == 1} multiple{else} class="chosen"{/if} name="{$name}[{$prop_id}]" disabled>
                                            {if $property.set == 0}<option value="">Выберите...</option>{/if}
                                            {foreach from=$catalog_item.galleries item=gallery key=gal_id}
                                                <option value="{$gallery->getId()}">{$gallery->getColorText()}</option>
                                            {/foreach}
                                        </select>
                                    {else}
                                        {*записываем вид полей ввода в переменную*}
                                        {capture assign="input_form"}
                                            {if !empty($property.values) && is_array($property.values)}
                                                {if $property.data_type == 'int' || $property.data_type == 'float'}
                                                    {if $property.set == 1}
                                                        <li class="one_value clearbox">
                                                            <div class="values_input"><input name="{$name}[{$prop_id}][]" type="text" disabled /></div>
                                                            <div class="table-btn delete remove_enum_value" title="Удалить значение"></div>
                                                        </li>
                                                    {else}
                                                        <input type="text" name="{$name}[{$prop_id}]" class="{$property.data_type}" disabled />
                                                    {/if}
                                                {elseif $property.data_type=='enum'}
                                                    <select{if $property.set == 1} multiple{else} class="chosen"{/if} name="{$name}[{$prop_id}]{if $property.set == 1}[]{/if}" disabled>
                                                        {if $property.set == 0}<option value="">Выберите...</option>{/if}
                                                        {foreach from=$property.values item=prop_val key=prop_val_id}
                                                            {if !empty($properties_available[$property.id]['ids'][$prop_val_id]) || !isset($properties_available[$property.id]['ids'])}
                                                                <option value="{$prop_val_id}">{$prop_val.value}</option>
                                                            {/if}
                                                        {/foreach}
                                                    </select>
                                                {elseif $property.data_type=='flag'}
                                                    <label>
                                                        <input type="radio" name="{$name}[{$prop_id}]" value="0" disabled />
                                                        {$property.values.no}
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="{$name}[{$prop_id}]" value="1" disabled />
                                                        {$property.values.yes}
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="{$name}[{$prop_id}]" value="" disabled />
                                                        Не определено
                                                    </label>
                                                {/if}
                                            {else}
                                                {if $property.set == 1}
                                                    <li class="one_value clearbox">
                                                        <div class="values_input"><input name="{$name}[{$prop_id}][]" type="text" /></div>
                                                        <div class="table-btn delete remove_enum_value" title="Удалить значение"></div>
                                                    </li>
                                                {else}
                                                    <input type="text" name="{$name}[{$prop_id}]" disabled
                                                        class="{$property.data_type}
                                                        {if $property.mask != '{!}'} int{/if}
                                                        {if $property.key == 'discounts_end'} datepicker{/if}
                                                        {if $property.key == 'manufacturer'} autocomplete{/if}"
                                                        {if !empty($property.values)} mask="{$property.values}"{/if} />
                                                {/if}
                                            {/if}
                                        {/capture}
                                        {*в маске заменяем {!} на поле ввода*}
                                        {?$masked_input = $property.set? $input_form : $property.mask|replace:ldelim . "!" . rdelim:$input_form}
                                        {*значения свойства у товара*}
                                        {if $variant_list}
                                            {?$item_values = !empty($catalog_variant[$property.key]) ? $catalog_variant[$property.key] : null}
                                        {else}
                                            {?$item_values = !empty($catalog_item[$property.key]) ? $catalog_item[$property.key] : null}
                                        {/if}
                                        {if $property.set == 1 && $property.data_type != 'enum'}
                                            <ul class="adder a-hidden">
                                                <li class="one_value clearbox m-new">
                                                    <div class="values_input"><input type="text" /></div>
                                                    <div class="table-btn delete remove_enum_value" title="Удалить значение"></div>
                                                </li>
                                            </ul>
                                            <ul class="being_values">
                                                {if !empty($item_values) && is_array($item_values)}
                                                    {foreach from=$item_values item=val}
                                                        {$masked_input|html}
                                                    {/foreach}
                                                {/if}
                                            </ul>
                                        {/if}

                                        {if $property.set == 1 && $property.data_type != 'enum'}
                                            <div class="add_form property-set" data-name="{$name}[{$prop_id}][]">
                                                <input class="new-enum-value" type="text" value="" placeholder="Добавить" disabled />
                                                <div class="table-btn add" title="Добавить значение"></div>
                                            </div>
                                        {else}
                                            {$masked_input|html}
                                            {*добавляем слайдер если надо*}
                                            {if ($property.data_type=='int' || $property.data_type=='float') && is_array($property.values) && $property.values.min!=''}
                                                <div class="slider" data-min="{$property.values.min}" data-max="{$property.values.max}" data-step="{$property.values.step}"></div>
                                            {/if}
                                        {/if}
                                    {/if}
                                </div>
                            </td>
                        </tr>
                    {/if}
                {/capture}
            {/if}
		{/foreach}
		{$firstProps|html}
		{$otherProps|html}		
	</table>
{/if}