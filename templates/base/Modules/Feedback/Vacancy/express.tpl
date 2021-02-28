{?$includeJS.send_form = 'Modules/Feedback/Main/feedback.js'}
{?$checkString = time()}
{?$checkStringSalt = $checkString . $hash_salt_string}
<h1>Экспресс-заявка</h1>
<form method="post" class="feedback-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
    <input type="hidden" name="check_string" value="">
    <input type="hidden" name="hash_string" value="">
    <input type="hidden" name="feedbackType" value="express">
    <div>
        <label>Тип недвижимости</label>
        <select name="realty_type">
            <option></option>
            {foreach from=$realty_type item=type}
                <option value="{$type}">{$type}</option>
            {/foreach}
        </select>
    </div>
    <div>
        <label>Тип сделки</label>
        <input type="radio" name="deal_type" value="rent">Аренда<br>
        <input type="radio" name="deal_type" value="sale">Продажа
    </div>
    <div>
        <label>Интересующая площадь, м²</label>
        <textarea name="interested_place"></textarea>
    </div>
    <div>
        <label>Контактное лицо</label>
        <input type="text" name="name" required="required">
    </div>
    <div>
        <label>Организация</label>
        <input type="text" name="organisation">
    </div>
    <div>
        <label>Должность</label>
        <input type="text" name="position">
    </div>
    <div>
        <label>Телефон / факс</label>
        <input type="text" name="phone" required="required">
    </div>
    <div>
        <label>Электронная почта</label>
        <input type="text" name="email" required="required">
    </div>
    <input type="submit" value="Отправить">
</form>