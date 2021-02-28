{* Составляет строку data-аттрибутов *}
{if empty($form_functions)}
    {?$form_functions = TRUE}
    {function get_element_data_string($data)}
        {?$result = ''}
        {foreach from=$data key=data_key item=data_value}
            {?$result .= 'data-' . $data_key . '="' . $data_value . '"'}
        {/foreach}
        {return $result}
    {/function}
    {* генерирует блок сообщений об ошибках валидации поля *}
    {function get_errors_block($field, $type)}
        {* Стандартные сообщения об ошибках, могут переопределяться *}
        {?$std_errors = array(
            'field' => array(
                'empty' => 'Заполните поле',
                'incorrect_format' => 'Неверный формат'
            ),
            'form' => array(
                'check_sum' => 'Вы робот!',
                '403' => 'Нет доступа',
                '500' => 'Ошибка сервера'
            )
        )}
        {?$result = '<ul class="f-errors a-hidden">'}
        {foreach from=$std_errors[$type] key=err_key item=err_descr}
            {if empty($field.errors[$err_key])}
                {?$result .= '<li class="error-' . $err_key . ' a-hidden">' . $err_descr . '</li>'}
            {/if}
        {/foreach}
        {if !empty($field.errors)}
            {foreach from=$field.errors key=err_key item=err_descr}
                {?$result .= '<li class="error-' . $err_key . ' a-hidden">' . $err_descr . '</li>'}
            {/foreach}
        {/if}
        {?$result .= '</ul>'}
        {return $result}
    {/function}
{/if}
{*
    form_data.antispam - включает защиту от спама, генерирует ключи, записывает их в data-аттрибуты формы и создает для них скрытые поля
 *}
{if !empty($form_data.antispam)}
    {?$checkString = time()}
    {?$checkStringSalt = $checkString . $hash_salt_string}
{/if}
<form class="user-form{if !empty($form_data.class)} {if is_array($form_data.class)}{implode(' ', $form_data.class)}{else}{$form_data.class}{/if}{/if}"
      {if !empty($form_data.antispam)}data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}"{/if}  {* защита от спама *}
        {if !empty($form_data.action)}action="{$form_data.action}"{/if}                                                 {* form action ($form_data.action)*}
        method="{if !empty($form_data.method)}{$form_data.method|strtoupper}{else}POST{/if}"                            {* form method ($form_data.method), POST default*}
        {if !empty($form_data.data)}{get_element_data_string($form_data.data)|html}{/if}                                {* data-attr (form_data.data) *}
>
    <div class="form-title">{if !empty($form_data.title)}{$form_data.title}{/if}</div>
    {* защита от спама *}
    {if !empty($form_data.antispam)}
        <input type="hidden" name="check_string" value="">
        <input type="hidden" name="hash_string" value="">
    {/if}
    {* Скрытые поля *}
    {if !empty($form_data.hidden_fields)}
        {*
            Скрытые поля задаются в параметре hidden_fields
            'hidden_fields' => array(
                <field_name> => <field_value>  // первый вариант
                <field_name> => array(
                    'value' => <field_value>,
                    'class' => <field_classes>
                    'data' => array() data-attr
                )
            )
        *}
        {foreach from=$form_data.hidden_fields key=field_name item=field_data}
            {if is_array($field_data)}
                <input{if !empty($field_data.class)} class="{if is_array($field_data.class)}{implode(' ', $field_data.class)}{else}{$field_data.class}{/if}"{/if} type="hidden" name="{$field_name}" value="{$field_data.value}"{if !empty($field_data.data)} {get_element_data_string($field_data.data)}{/if} />
            {else}
                <input type="hidden" name="{$field_name}" value="{$field_data}" />
            {/if}
        {/foreach}
    {/if}
    {*
        $form_data.fields - массив полей и групп полей
    *}
    {foreach from=$form_data.fields key=field_name item=field_data}
        {if $field_data.type == 'group'}
            {* группа полей, в поле fields массив вложенных полей, описание их параметров в forms/field.tpl *}
            <div class="f-group{if !empty($field_data.class)} {if is_array($field_data.class)}{implode(' ', $field_data.class)}{else}{$field_data.class}{/if}{/if}"{if !empty($field_data.data)} {get_element_data_string($field_data.data)}{/if}>
                <div class="f-group-title">{if !empty($field_data.title)}{$field_data.title}{/if}</div>
                {foreach from=$field_data.fields key=name item=field}
                    {include file='forms/field.tpl' name=$name field=$field}
                {/foreach}
            </div>
        {else}
            {* отдельное поле *}
            {include file='forms/field.tpl' name=$field_name field=$field_data}
        {/if}
    {/foreach}
    {* блок ошибок формы, аналогично блоку ошибок поля формы *}
    {get_errors_block($form_data, 'form')|html}
    <div class="buttons">
        <button class=”submit-form”>{if !empty($form_data.buttons.submit)}{$form_data.buttons.submit}{else}Отправить{/if}</button>
        <span class="clear-form">{if !empty($form_data.buttons.clear)}{$form_data.buttons.clear}{else}Очистить{/if}</span>
    </div>
</form>