<div class="field{if !empty($field.class)} {if is_array($field.class)}{implode(' ', $field.class)}{else}{$field.class}{/if}{/if}">
    // к .field должна быть возможность добавлять классы и атрибуты
    <div class="f-title">Заголовок поля 1</div>
    <div class="f-input">
        <input type="{$field.type}" name="name" />
        // type также может быть password или email
        // к инпуту должна быть возможность добавлять классы и атрибуты
    </div>
    <div class="f-descr">Описание поля</div>
    <ul class="f-errors a-hidden">
        <li class="error-empty a-hidden">Заполните поле</li>
        <li class="error-incorrect_format a-hidden">Неверный формат</li>
    </ul>
    // для каждого типа ошибки - своя li-шка. Когда с сервера приходит ошибка типа: field_name: err_key, то в форме ищется поле, у которого name=field_name, а затем у его ближайшего .f-errors находится и делается видимым элемент .error-err_key.
</div>