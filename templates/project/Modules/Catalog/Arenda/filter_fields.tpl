
{if !empty($search_properties_count)}
    {?$sprop_counter = 0}
    {? $filter_count_fields = 5}
    {if (empty($search_properties_count))} {? $search_properties_count = 10} {/if}


    {foreach from=$main_search_properties item=sprop name=sprop}
        {?$sprop_title = !empty($sprop.filter_title)? $sprop.filter_title : $sprop.title}
        {if $sprop.data_type=='flag'}
            <div class="field">
                <div class="f-title main">{$sprop_title}</div>
                <label class="btn m-bedroom">
                    <input type="checkbox" name="{$sprop.key}" value="1" class="m-hidden-input">
                    <span>{$lang->get('Есть', 'Yes')}</span>
                </label>
            </div>
        {elseif $sprop.search_type=='between'}
            {if !empty($sprop.search_values.min) && !empty($sprop.search_values.max)}
                {if !empty($foreign_price) && $sprop.key == 'close_price'}
                    <div class="field tabs-cont">
                        <div class="f-title main">
                            {$lang->get("Цена","Price")}
                            <span class="tab-title{if $request_segment.key == 'ru'} m-current{/if}"
                                  data-target=".js-rub-show"> {$lang->get("тыс руб.","mln rub.")}</span><span
                                    class="slash"></span>
                            <span class="tab-title{if $request_segment.key != 'ru'} m-current{/if}"
                                  data-target=".js-doll-show"> {$lang->get("тыс USD","ths USD")}</span>
                        </div>
                        <div class="f-input slider-wrap js-rub-show tab-page{if $request_segment.key == 'ru'} m-current{else} a-hidden{/if}">
                            <input type="text" name="{$sprop.key}[min]" class="input-min range-input a-left"
                                   maxlength="3" value="{$sprop.search_values.min|floor}"/>
                            <input type="text" name="{$sprop.key}[max]" class="input-max range-input a-left"
                                   maxlength="3" value="{$sprop.search_values.max|ceil}"/>
                            <div class="slider range" data-min="{$sprop.search_values.min|floor}"
                                 data-max="{$sprop.search_values.max|ceil}"
                                 data-step="{$sprop.search_values.step || 1}"></div>
                        </div>
                        {if !empty($foreign_price) && $sprop.key == 'close_price'}
                            <div class="f-input slider-wrap js-doll-show m-b-input tab-page{if $request_segment.key != 'ru'} m-current{else} a-hidden{/if}">
                                <input type="text" name="{$foreign_price.key}[min]"
                                       class="input-min range-input a-left" maxlength="4"
                                       value="{$foreign_price.min|floor}"/>
                                <input type="text" name="{$foreign_price.key}[max]"
                                       class="input-max range-input a-left" maxlength="4"
                                       value="{$foreign_price.max|ceil}"/>
                                <div class="slider range" data-min="{$foreign_price.min|floor}"
                                     data-max="{$foreign_price.max|ceil}"
                                     data-step="{$foreign_price.step || 1}"></div>
                            </div>
                        {/if}
                    </div>
                {else}
                    <div class="field">
                        <div class="f-title main">{$sprop_title}</div>
                        <div class="f-input slider-wrap">
                            <input type="text" name="{$sprop.key}[min]" class="input-min range-input a-left"
                                   maxlength="3" value="{$sprop.search_values.min|floor}"/>
                            <input type="text" name="{$sprop.key}[max]" class="input-max range-input a-left"
                                   maxlength="3" value="{$sprop.search_values.max|ceil}"/>
                            <div class="slider range" data-min="{$sprop.search_values.min|floor}"
                                 data-max="{$sprop.search_values.max|ceil}"
                                 data-step="{$sprop.search_values.step || 1}"></div>
                        </div>
                    </div>
                {/if}
            {/if}
        {elseif $sprop.search_type=='check'}
            {if !empty($sprop.search_values)}
                {if $sprop.key== 'district'}
                    <div class="field f-dropdown" data-hoverable="0">
                        <div class="f-title main">{$sprop_title}<a href="{$url_prefix}/district/">
                                — {$lang->get('Гид по районам', 'City guide')}</a></div>
                        <div class="dropdown-toggle"><span data-title="{$lang->get('все районы', 'all districts')}"
                                                           data-title_one="{$lang->get('район', 'district')}"
                                                           data-title_two="{$lang->get('района', 'districts')}"
                                                           data-title_five="{$lang->get('районов', 'districts')}">{$lang->get('все районы', 'all districts')}</span>
                            <div>{fetch file=$path . "menu.svg"}</div>
                        </div>
                        <ul class="dropdown-menu">
                            {foreach from=$sprop.search_values item=sval_view key=val}
                                <li>
                                    <input id="{$sprop.key . $val}" type="checkbox" name="{$sprop.key}[]"
                                           value="{$val}">
                                    <label for="{$sprop.key . $val}">
                                        <div></div>
                                        <span>{$sval_view}</span>
                                    </label>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {elseif $sprop.key== 'typerk'}
                    <div class="field f-dropdown" style="display:none" data-hoverable="0">
                        <div class="f-title main">{$sprop_title}</div>
                        <div class="dropdown-toggle"><span data-title="{$lang->get('все типы', 'all types')}"
                                                           data-title_one="{$lang->get('тип', 'type')}"
                                                           data-title_two="{$lang->get('типа', 'type')}"
                                                           data-title_five="{$lang->get('типов', 'types')}">{$lang->get('все типы', 'all types')}</span>
                            <div>{fetch file=$path . "menu.svg"}</div>
                        </div>
                        <ul class="dropdown-menu">
                            {foreach from=$sprop.values item=sval_view key=val}
                                <li>
                                    <input id="{$sprop.key . $val}" type="checkbox" name="{$sprop.key}[]"
                                           value="{$val}">
                                    <label for="{$sprop.key . $val}">
                                        <div></div>
                                        <span>{$sval_view.value}</span>
                                    </label>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {else}
                    <div class="field">
                        <div class="f-title main">{$sprop_title}</div>
                        <div class="f-input">
                            {?$many_bed = false}
                            {foreach from=$sprop.search_values item=sval_view key=val name=sval}
                                {if $sprop.key == 'bed_number' && $val > 4}
                                    {?$many_bed = true}
                                    {continue}
                                {/if}
                                <label class="btn m-bedroom">
                                    <input type="checkbox" name="{$sprop.key}[]" value="{$val}"
                                           class="m-hidden-input">
                                    <span>{$sval_view}</span>
                                </label>
                            {/foreach}
                            {if $many_bed}
                                <label class="btn m-bedroom m-five">
                                    <input type="checkbox" name="{$sprop.key}[]" value="5" class="m-hidden-input">
                                    <span>5 +</span>
                                </label>
                            {/if}
                        </div>
                    </div>
                {/if}
            {/if}
        {elseif $sprop.search_type=='select'}
            {if !empty($sprop.search_values)}
                <div class="field dropdown" data-hoverable="0">
                    <select name="{$sprop.key}" class="chosen slider-wrap fullwidth">
                        {foreach from=$sprop.search_values item=sval_view key=val}
                            <option value="{$val}">{$sval_view}</option>
                        {/foreach}
                    </select>
                </div>
            {/if}
        {elseif $sprop.search_type=='autocomplete'}
            <div class="field">
                <div class="f-title main">{$sprop_title}</div>
                <div class="f-input">
                    <input type="text" name="{$sprop.key}"/>
                </div>
            </div>
        {/if}
    {/foreach}
<!--
    <div class="additional-filters-open">
        <div class="additional-filters-open-button" id="additional_filters_open"><span class="main">дополнительно</span></div>
    </div>
    <div id="additional_filters" class="additional-filters">
        <div id="additional_filters_field" class="additional-filters-field">

        </div>
    </div>
-->
{/if}
