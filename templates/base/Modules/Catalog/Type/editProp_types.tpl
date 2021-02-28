{?$not_set = array(
    'view',
    'range',
    'flag'
)}
{foreach from=$properties_key.data_type key=data_type item=data_type_rus}
    <div class="white-inner-cont tab-page tab-{$data_type}{if $property.data_type != $data_type} a-hidden{/if}{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if}">
        {if !in_array($data_type, $not_set)}
        <label class="white-block-row">
            <div class="w3">
                <span class="text-icon">Множественное</span>
                {include file="Admin/components/tip.tpl" content="Дает возможность задавать более одного значения данного свойства."}
            </div>
            <div class="w9">
                <input type="hidden" name="set" value="0"{if $unchangeable && in_array('set', $unchangeableParamsByProps)} disabled{/if}  />
                <input type="checkbox" name="set" value="1"{if $unchangeable && in_array('set', $unchangeableParamsByProps)} disabled{/if} />
            </div>
        </label>
        {/if}
        {if $data_type == 'enum'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/enum.tpl"}
        {elseif $data_type == 'view'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/view.tpl"}
        {elseif $data_type == 'range'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/range.tpl"}
        {elseif $data_type == 'int' || $data_type == 'float'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/numeric.tpl"}
        {elseif $data_type == 'string' || $data_type == 'text'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/string.tpl"}
        {elseif $data_type == 'file'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/file.tpl"}
        {elseif $data_type == 'flag'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/flag.tpl"}
        {elseif $data_type == 'date'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/date.tpl"}
		{elseif $data_type == 'color'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/color.tpl"}
        {elseif $data_type == 'rule'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/rule.tpl"}
        {elseif $data_type == 'post'}
            {include file="Modules/Catalog/Type/propsDataTypeValues/post.tpl"}
        {elseif strpos($data_type, 'item') === 0 || strpos($data_type, 'variant') === 0}
            {include file="Modules/Catalog/Type/propsDataTypeValues/catalogposition.tpl"}
        {elseif strpos($data_type, 'diapason') === 0}
            {include file="Modules/Catalog/Type/propsDataTypeValues/diapason.tpl"}
        {/if}
    </div>
{/foreach}