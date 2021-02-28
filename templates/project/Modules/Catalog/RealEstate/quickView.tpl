<ul>
    {foreach from=items item=complex}
        {if empty($quick_view_current_id)}{?$quick_view_current_id = $complex.id}{/if}
        <li{if $complex.id == $quick_view_current_id} class="m-current"{/if}>
            <a href="{$complex->getUrl($request_segment['id'])}">{$complex.title}</a>
        </li>
    {/foreach}
</ul>
