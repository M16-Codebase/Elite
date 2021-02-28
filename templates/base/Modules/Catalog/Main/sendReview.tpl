{?$checkString = time()}
{?$checkStringSalt = $checkString . $hash_salt_string}
<form class="user-form" action="/catalog/makeReview/" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
    <input type="hidden" name="check_string" value="" />
    <input type="hidden" name="hash_string" value="" />
    <input type="hidden" name="id" value="" />
    <input type="hidden" name="product_id" value="{$product.id}" />
    Заголовок <input type="text" name="title" /><br />
    Текст отзыва <br />
    <textarea name="text"></textarea><br />
    <input type="checkbox" name="recommendation" value="1" checked="checked" />&nbsp;Рекомендую<br />
    Имя <input type="text" name="author" /><br />
    Пол
    <select name="gender">
        <option value=""></option>
        {foreach from=$reviews_props.gender.values item=val}
            <option value="{$val.id}">{$val.value}</option>
        {/foreach}
    </select><br />
    Возрастная группа
    <select name="age_group">
        <option value=""></option>
        {foreach from=$reviews_props.age_group.values item=val}
            <option value="{$val.id}">{$val.value}</option>
        {/foreach}
    </select><br />
    Длительность использования продукта
    <select name="duration">
        <option value=""></option>
        {foreach from=$reviews_props.duration.values item=val}
            <option value="{$val.id}">{$val.value}</option>
        {/foreach}
    </select><br />
    Город <input type="text" name="city" /><br />
    <input type="submit" value="Отправить отзыв" />
</form>
{*{$reviews_props|var_dump}*}