<form>
    <input type="hidden" name="create" value="1">
    Начало<br>
    Дата <input type="text" name="startDate">Время<input type="text" name="startTime">
    <br>
    Конец<br>
    Дата <input type="text" name="endDate">Время<input type="text" name="endTime">
    <br>
    Тип мероприятия
    <select name="event_type">
        <option>конференция</option>
        <option>турслет</option>
    </select>
    <br>
    Название <input type="text" name="summary">
    <br>
    Организатор <input type="text" name="organizer">
    <br>
    Место <input type="text" name="location">
    <br>
    Стоимость участия <input type="text" name="price">
    <br>
    Ссылка на описание <input type="text" name="htmlLink">
    <br>
    Описание <br>
    <textarea name="description"></textarea>
    <br>
    Заинтересованные лица <br>
    <input type="text" name="attendees[]"><br>
    <input type="text" name="attendees[]"><br>
    <input type="text" name="attendees[]"><br>
    <input type="text" name="attendees[]">
    <br>
    Период уведомления
    <select name="reminder">
        <option value="15">За 15 минут</option>
        <option value="30">За 30 минут</option>
        <option value="60">За 1 час</option>
        <option value="120">За 2 час</option>
        <option value="180">За 3 час</option>
        <option value="300">За 5 час</option>
        <option value="720">За 12 час</option>
        <option value="1440">За 1 день</option>
        <option value="2880">За 2 дня</option>
        <option value="10080">За неделю</option>
    </select>
    <br>
    <input type="submit" value="Сохранить">
</form>