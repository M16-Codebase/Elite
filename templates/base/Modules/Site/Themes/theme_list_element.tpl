{strip}
    {foreach from=$themes_list key="k" item="theme"}
        <div class="wblock white-block-row{if !empty($child) && $child} values{/if}" data-id="{$theme.id}" data-position="{$theme.position}">
            <div class="w1 small not-edit"><div class="drag-drop m-active"></div></div>
            <div class="w10">
                <a href="/site-themes/edit/?id={$theme.id}">{$theme.title}</a>
            </div>
			{*<a class="table-btn delete" href="/site-themes/?del={$theme.id}" onClick="return confirm('Вы желаете удалить тему №{$theme.id} &laquo;{$theme.title}&raquo;?')"></a>*}
			<a href="/site-themes/?del={$theme.id}" data-themeid='{$theme.id}' class="action-button action-delete delete-theme-btn w1" title="Удалить"><i></i></a>
        </div>
        {if !empty($themes[$theme.id])}
            {include file="Modules/Site/Themes/theme_list_element.tpl" themes_list=$themes[$theme.id] child=true}
        {/if}
            {?$child=false}
	{/foreach}
{/strip}