{?$checkString = time()}
{?$checkStringSalt = $checkString . $hash_salt_string}
<form class="user-form" action="/catalog/makeQuestion/" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
    <input type="hidden" name="check_string" value="" />
    <input type="hidden" name="hash_string" value="" />
    <input type="hidden" name="id" value="" />
    <input type="hidden" name="product_id" value="{$product.id}" />
    Заголовок <input type="text" name="title" /><br />
    Текст вопроса <br />
    <textarea name="text"></textarea><br />
    Имя <input type="text" name="author" /><br />
    Электронная почта <input type="text" name="email" /><br />
    Пол
    <select name="gender">
        <option value=""></option>
        {foreach from=$reviews_props.gender.values item=val}
            <option value="{$val.id}">{$val.value}</option>
        {/foreach}
    </select><br />
    Возраст <input type="text" name="age" /><br />
    Город <input type="text" name="city" /><br />
    <input type="submit" value="Отправить отзыв" />
</form>