<form action="/catalog-type/dcRulesList/">
    <h1>list</h1>
    type_id <input type="text" name="type_id" /><br />

    <input type="submit" value="send" />
</form>

<form action="/catalog-type/dcAddRule/">
    <h1>add</h1>
    type_id <input type="text" name="type_id" /><br />
    field <input type="text" name="field" /><br />
    type <select name="type">
        <option value="equal">Равно</option>
        <option value="range">Диапазон</option>
    </select><br />
    Равно <input type="text" name="value" /><br />
    От <input type="text" name="min" /><br />
    До <input type="text" name="max" /><br />
    <input type="submit" value="send" />
</form>

</form><form action="/catalog-type/dcEditRule/">
    <h1>edit</h1>
    type_id <input type="text" name="type_id" /><br />

    <input type="submit" value="send" />
</form>

</form><form action="/catalog-type/dcDeleteRules/">
    <h1>delete</h1>
    type_id <input type="text" name="type_id" /><br />

    <input type="submit" value="send" />
</form>
