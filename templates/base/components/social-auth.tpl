{if empty($social_links_list)}
    {?$social_links_list = $infoBlocks->get('socialLinks')}
{/if}
<div class="social-auth-buttons">
    {if !empty($social_links_list.vk)}<a class="auth-btn" href="#" data-network="{$social_links_list.vk}">авторизация вк</a>{/if}
    {if !empty($social_links_list.facebook)}<a class="auth-btn" href="#" data-network="{$social_links_list.facebook}">авторизация фб</a>{/if}
    {if !empty($social_links_list.odnoklassniki)}<a class="auth-btn" href="#" data-network="{$social_links_list.odnoklassniki}">одноклассники</a>{/if}
    {if !empty($social_links_list.twitter)}<a class="auth-btn" href="#" data-network="{$social_links_list.twitter}">Twitter</a>{/if}
    {if !empty($social_links_list.google)}<a class="auth-btn" href="#" data-network="{$social_links_list.google}">Google</a>{/if}
</div>