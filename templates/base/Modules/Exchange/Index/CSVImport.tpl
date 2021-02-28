{?$admin_page = 1}
<form>
    <label>Картинки: 
        <select name="images">
            <option value="replace">Заменять</option>
            <option value="add">Добавлять</option>
        </select>
    </label>
    <label>Новые значения справочников:
        <select name="enum">
            <option value="add">Добавлять</option>
            <option value="ignore">Игнорировать</option>
        </select>
    </label>
    <label>Отображать товары:
        <select name="visible">
            <option value="any">Везде</option>
            <option value="site">Только на сайте</option>
            <option value="export">Только для экспорта</option>
            <option value="none">Нигде</option>
        </select>
    </label>
    <br />
    <select name="type_id">
        <option value="0">Выбор типа...</option>
		{if !empty($types_by_parents)}
            {foreach from = $types_by_parents key=parent_title item=p_types}
                <optgroup label="{$parent_title}">
                    {foreach from=$p_types key=p_type_id item=p_type}
                        <option value="{$p_type_id}">{$p_type.type_title}</option>
                    {/foreach}
                </optgroup>
            {/foreach}
        {/if}
    </select>
    <a href="/exchange/downloadExample/" class="download_keys">Скачать ключи</a>
    <input type="file" name="file" />
    <br />
    <input type="submit" name="import" value="Отправить" />
</form>
    
    <script>
        {literal}
            $('.download_keys').click(function(){
                $(this).prop('href', $(this).prop('href') + '?type_id=' + $('SELECT[name="type_id"]').val());
            });
        {/literal}
    </script>