<div id="add_link" title="Добавить/изменить ссылку" />
<form>
    <fieldset>
        <input type="hidden" name="info" data-method="" data-id="" data-ihref="" data-text="" data-work="">
        <label for="cat_list">Район</label>
        <select id="cat_list">
            <option value="">Выберите</option>
            <option value="resale">вторичка</option>
            <option value="real-estate">первичка</option>
            <option value="residential">загородка</option>
        </select>
        <label for="dist_list">Район</label>
        <select id="dist_list">
            <option value="">Выберите</option>
            {foreach from=$districtsList item=dist key=key}
                <option value="{$key}">{$dist}</option>
            {/foreach}
        </select>
        <label for="bed_nums">Кол-во комнат</label>
        <select id="bed_nums">
            <option value="">Выберите</option>
            {foreach from=$bedNums item=item key=key}
                <option value="{$item[0]}">{$key}</option>
            {/foreach}
        </select>
        <label for="href">href</label>
        <input type="text" name="href" id="href" class="text ui-widget-content ui-corner-all" disable="true">
        <label for="text">Текс</label>
        <input type="text" name="text" id="text" class="text ui-widget-content ui-corner-all">
        <label for="work">Выводить</label>
        <input type="checkbox" name="work" id="work" class="text ui-widget-content ui-corner-all">
    </fieldset>
</form>
</div>