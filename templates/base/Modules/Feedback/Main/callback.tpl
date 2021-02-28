{?$includeJS.send_form = 'Modules/Feedback/Main/feedback.js'}
{?$checkString = time()}
{?$checkStringSalt = $checkString . $hash_salt_string}
<h1>Форма обратной связи / заказ звонка</h1>

<form method="post" class="feedback-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
    <input type="hidden" name="check_string" value="">
    <input type="hidden" name="hash_string" value="">
    <input type="hidden" name="feedbackType" value="callback">
    <div>
        <label>Контактное лицо *</label>
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
        <label>Телефон / факс *</label>
        <input type="text" name="phone" required="required">
    </div>
    <div>
        <label>Удобное время звонка</label>
        с <input type="text" name="time_from" value="9"> до <input type="text" name="time_to" value="19"> часов, время Спб
    </div>
    <div>
        <label>Электронная почта *</label>
        <input type="text" name="email" required="required">
    </div>
    <div>
        <label>Тема обращения</label>
        <select name="issue">
            <option></option>
            {foreach from=$issues item=issue}
                <option value="{$issue}">{$issue}</option>
            {/foreach}
        </select>
    </div>
    <div>
        <label>Сообщение</label>
        <textarea name="message"></textarea>
    </div>
        <input type="submit" value="Отправить">
</form>