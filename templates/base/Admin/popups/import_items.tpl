<div class="popup-window popup-import">
    <form action="/exchange/CSVImport/">        
        <table class="ribbed">
            <tr>
                <td>Тип товара</td>
                <td>
                    <select name="type_id" class="chosen fullwidth">
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
					{if $account->isPermission('exchange', 'downloadExample')}
						<a href="/exchange/downloadExample/" class="download_keys small-descr">Скачать ключи</a>
					{/if}
                </td>
            </tr>
            <tr>
                <td>Картинки</td>
                <td>
                    <select name="images" class="chosen" style="width: 160px;">
                        <option value="replace">Заменять</option>
                        <option value="add">Добавлять</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Новые значения справочников</td>
                <td>
                    <select name="enum" class="chosen" style="width: 160px;">
                        <option value="add">Добавлять</option>
                        <option value="ignore">Игнорировать</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Отображать товары</td>
                <td>
                    <select name="visible" class="chosen" style="width: 160px;">
                        <option value="any">Везде</option>
                        <option value="site">Только на сайте</option>
                        <option value="export">Только для экспорта</option>
                        <option value="none">Нигде</option>
                    </select>
                </td>
            </tr>
            <tr><td>Кодировка</td>
                <td><select name="encoding" class="chosen" style="width: 160px;">
                    <option value="cp1251">Windows-1251</option>
                    <option value="utf">UTF-8</option>
                </select></td>
            </tr>
            <tr>
                <td>Файл</td>
                <td>
                    <input type="file" name="file" />
                </td>
            </tr>
        </table>
        <div class="buttons clearbox">
            <div class="submit a-button-green">Отправить</div>
        </div>
    </form>
</div>
