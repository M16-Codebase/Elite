
{* список районов *}
{if isset($districtList)}
{literal}
    <script>
        var cityDistricts = {{/literal}
            {foreach from=$districtList item=sval_view key=val}
                "{$val}":"{$sval_view}",
            {/foreach}
        {literal}
        };

        window.cityDistricts = {{/literal}
            {foreach from=$districtList item=sval_view key=val}
            "{$val}":"{$sval_view}",
            {/foreach}
            {literal}
        };
    </script>
{/literal}
{/if}

{* разрешить ЧПУ *}
{if isset($allowFilterFriendlyUrl)}
    {literal}
    <script>
        window.allowFilterFriendlyUrl = {/literal}{$allowFilterFriendlyUrl}{literal};
    </script>
    {/literal}
{/if}


