{*
Каждый элемент формы объявляется элементом массива вида
<field_name> => array(
    'type' => тип элемента: text, password, email, file, checkbox, radio, select, slider (обязательное поле)
    'class' => строка или массив имен классов для оболочки элемента формы (div.field)
    'data' => массив с data-аттрибутами
    'title' => заголовок поля (обязательное поле)
    'description' => описание поля
    'errors' => массив ключи которого соответсвуют ключи ошибок, а значениям - описания
  для всех полей кроме радиобатонов и массива чекбоксов можно указать следующие параметры
    'field_class' | Аналогичны class и data, с различием в том,
    'field_data'  | что задают значения для элемента формы input, select etc.
)
*}
<div class="field{if $field.type == 'slider'} slider-wrap{/if}{if !empty($field.class)} {if is_array($field.class)}{implode(' ', $field.class)}{else}{$field.class}{/if}{/if}"{if !empty($field.data)} {get_element_data_string($field.data)|html}{/if}>
    <div class="f-title">{$field.title}</div>
    <div class="f-input">
        {if in_array($field.type, array('text', 'password', 'email', 'file'))}
            {* Текстовые поля и файлы*}
            <input type="{$field.type}" name="{$name}"{if !empty($field.field_class)} class='{if is_array($field.field_class)}{implode(' ', $field.field_class)}{else}{$field.field_class}{/if}'{/if}{if !empty($field.field_data)} {get_element_data_string($field.field_data)|html}{/if} />
        {elseif $field.type == 'textarea'}
            <textarea name="{$name}"{if !empty($field.field_class)} class='{if is_array($field.field_class)}{implode(' ', $field.field_class)}{else}{$field.field_class}{/if}'{/if}{if !empty($field.field_data)} {get_element_data_string($field.field_data)|html}{/if}></textarea>
        {elseif $field.type == 'checkbox'}
            {* Чекбоксы *}
            {if !empty($field.value)}
                {* Одиночный чекбокс *}
                {*
                    Параметры уникальные для одиночного чекбокса
                        'label' => подпись к чекбоксу (обязательное поле)
                        'value' => значение для чекнутого бокса
                        'default_value' => значение анчекнутого
                        'not_default' => не указывать значение для нечекнутого
                *}
                {if empty($field.not_default)}
                    {* для одиночного чекбокса возможно значение по умолчанию *}
                    <input type="hidden" name="{$name}" value="{if isset($field.default_value)}{$field.default_value}{/if}">
                {/if}
                <label>
                    <input type="checkbox" class="cbx{if !empty($field.field_class)} {if is_array($field.field_class)}{implode(' ', $field.field_class)}{else}{$field.field_class}{/if}{/if}" name="{$name}"{if isset($field.value)} value="{$field.value}"{/if}{if !empty($field.field_data)} {get_element_data_string($field.field_data)|html}{/if} />
                    <span>{$field.label}</span>
                </label>
            {else}
                {* или массив чекбоксов *}
                {*
                    Параметры для групы чекбоксов
                    'values' => array(  // собственно массив чекбоксов
                        <cbx_index> => array( // <input type="checkbox" name="<field_name>[<cbx_index>]" ....
                            'label' => подпись к чекбоксу (обязательное поле)
                            'value' => значение для чекнутого
                            'class' => масиив или строка с классами
                            'data' => массив data-аттрибутов
                        )
                    )
                *}
                {foreach from=$field.values key=cbx_key item=cbx}
                    <label>
                        <input type="checkbox" name="{$name}[{$cbx_key}]" class='cbx{if !empty($cbx.class)} {if is_array($cbx.class)}{implode(' ', $cbx.class)}{else}{$cbx.class}{/if}{/if}'{if isset($cbx.value)} value="{$cbx.value}"{/if}{if !empty($cbx.data)} {get_element_data_string($cbx.data)|html}{/if} />
                        <span>{$cbx.label}</span>
                    </label>
                {/foreach}
            {/if}
        {elseif $field.type == "radio"}
            {* Радиобатоны *}
            {*
                Параметры для радиобатонов
                'values' => array(  // массив радиобатонов
                    array( // <input type="radio" name="<field_name>" value="$value"
                        'label' => подпись к радиобатона (обязательное поле)
                        'value' => значение для чекнутого
                        'class' => масиив или строка с классами
                        'data' => массив data-аттрибутов
                    )
                )
            *}
            {foreach from=$field.values item=radio}
                <label>
                    <input type="radio" name="{$name}" class='radio{if !empty($radio.class)} {if is_array($radio.class)}{implode(' ', $radio.class)}{else}{$radio.class}{/if}{/if}'{if isset($radio.value)} value="{$radio.value}"{/if}{if !empty($radio.data)} {get_element_data_string($radio.data)|html}{/if} />
                    <span>{$radio.label}</span>
                </label>
            {/foreach}
        {elseif $field.type == 'select'}
            {* селект *}
            {*
                Уникальные параметры для селекта
                'options' => array(   // Опции селекта
                    'opt_value' => 'opt_description'
                    .........
                )
            *}
            <select name="{$name}" class="chosen{if !empty($field.field_class)} {if is_array($field.field_class)}{implode(' ', $field.field_class)}{else}{$field.field_class}{/if}{/if}"{if !empty($field.field_data)} {get_element_data_string($field.field_data)|html}{/if}>
                {foreach from=$field.options key=opt_value item=opt_descr}
                    <option value="{$opt_value}">{$opt_descr}</option>
                {/foreach}
            </select>
        {elseif $field.type == 'slider'}
            {* слайдер *}
            <input type="text" name="{$name}_min" class=”input-min” />
            <input type="text" name="{$name}_max" class=”input-max” />
            <div class="text-min"></div>
            <div class="text-max"></div>
            <div class="slider range" data-min=”{if !empty($field.min)}{$field.min}{else}0{/if}” data-max=”{if !empty($field.max)}{$field.max}{else}100{/if}” data-step=”{if !empty($field.step)}{$field.step}{else}1{/if}”></div>
        {/if}

    </div>
    {if !empty($field.description)}
        <div class="f-descr">{$field.description}</div>
    {/if}
    {get_errors_block($field, 'field')|html}
</div>