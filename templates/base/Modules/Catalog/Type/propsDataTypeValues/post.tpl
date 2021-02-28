<div class="white-block-row">
    <div class="w3">
        <span class="text-icon">Показывать заголовок</span>
        {include file="Admin/components/tip.tpl" content="Включить/выключить использование заголовка в статье."}
    </div>
    <div class="w9">
        <input type="hidden" name="values[show_title]" value="0" />
        <input type="checkbox" name="values[show_title]" value="1"{if is_array($property)} checked="checked"{/if} />
    </div>
</div>
<div class="white-block-row">
    <div class="w3">
        <span class="text-icon">Показывать аннотацию</span>
        {include file="Admin/components/tip.tpl" content="Включить/выключить использование аннотации в статье."}
    </div>
    <div class="w9">
        <input type="hidden" name="values[show_annotation]" value="0" />
        <input type="checkbox" name="values[show_annotation]" value="1"{if is_array($property)} checked="checked"{/if} />
    </div>
</div>
<div class="white-block-row">
    <div class="w3">
        <span class="text-icon">Показывать статус</span>
        {include file="Admin/components/tip.tpl" content="Включить/выключить использование статуса в статье."}
    </div>
    <div class="w9">
        <input type="hidden" name="values[show_status]" value="0" />
        <input type="checkbox" name="values[show_status]" value="1"{if is_array($property)} checked="checked"{/if} />
    </div>
</div>
<div class="white-block-row">
    <div class="w3">
        <span class="text-icon">Использовать изображения</span>
        {include file="Admin/components/tip.tpl" content="Включить/выключить использование изображений в статье."}
    </div>
    <div class="w9">
        <input type="hidden" name="values[allow_images]" value="0" />
        <input type="checkbox" name="values[allow_images]" value="1"{if is_array($property)} checked="checked"{/if} />
    </div>
</div>
{if !empty($allow_property_posts)}
    <div class="white-block-row">
        <div class="w3">
            <span>Выбор объекта из списка</span>
        </div>
        <div class="w9">
            <select name="values[post_type]">
                <option value="property_value">Пост каталога</option>
                {foreach from=$allow_property_posts key=post_type_key item=post_type_title}
                    <option value="{$post_type_key}">{$post_type_title}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="white-block-row">
        <div class="w3">
            <span>Выбор объекта из списка</span>
        </div>
        <div class="w9">
            <select name="values[edit_mode]">
                <option value="edit_popup">Редактирование в попапе</option>
                <option value="list">Выбор из списка</option>
                {*<option value="search_popup">Выбор из поиска</option>*}
            </select>
        </div>
    </div>

{/if}
