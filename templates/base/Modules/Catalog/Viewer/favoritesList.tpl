<ul class="offer-list">
    {foreach from=$favorites.items item=f_data}
        {?$item = $f_data.item}
        <li class="offer-item m-open" data-id="{$item.id}">
            <div class="offer-header slide-header link-wrap">
                <div class="offer-selection a-right link-except" title="Убрать из выборки">
                    <i class="i-selection"></i>
                </div>
                {if empty($ens_search)}
                    <div class="offer-visibility a-right link-except{if $account->isPermission('catalog-item', 'changeItemProp') && $item.status_object != 'В архиве' && $item.status_object != 'Не работаем'} m-active{/if}">
                        <i class="i-visibility{if empty($item['visible']) || $item['visible'] == 'Скрыт'} m-hidden{/if}"></i>
                    </div>
                {/if}
                {if !empty($item.status_object)}
                    <div class="offer-status {if $item.status_object=='Есть договор'}contract{elseif $item.status_object=='Не работаем'}not-work{elseif $item.status_object=='Есть устная договоренность'}tire-agreement{elseif $item.status_object=='В архиве'}archive{elseif $item.status_object=='Статус не определен'}non-status{elseif $item.status_object=='Гарантийное письмо'}guarantee{elseif $item.status_object=='На согласовании'}concordance{/if} a-right">
                    {if $item.status_object=='Есть устная договоренность'}Устная дог.{elseif $item.status_object=='Гарантийное письмо'}Гар. письмо{elseif $item.status_object=='На согласовании'}Согласование{elseif $item.status_object=='Статус не определен'}Без статуса{else}{$item.status_object}{/if}
                    {if !empty($item.razmer_komissii)}<br />{$item.razmer_komissii}{/if}
                </div>
            {elseif empty($item.status_object)}
                <div class="offer-status non-status a-right">Без статуса</div>
            {/if}
            <div class="offer-cbx a-left link-except">
                <input type="checkbox" name="check[]" value="{$item.id}" class="check-item" />
            </div>
            {if !empty($item.gallery)}
                {?$cover = $item.gallery->getCover()}
                {if empty($cover)}
                    {?$cover = $item.gallery->getDefault()}
                {else}	
                    <div class="offer-cover a-left">
                        <img{if $action != 'search'} class="m-zoom"{/if} src="{$cover->getUrl(215, 195, true)}" alt="{$item.title}" />
                    </div>
                {/if}
            {/if}	
            <div class="title">
                <div class="title-inner">
                    <a href="/catalog-view/?id={$item.id}" class="link-target">{$item.title}</a>
                    {if $account->isPermission('catalog-item', 'edit')}
                        <a href="/catalog-item/edit/?id={$item.id}" title="Редактировать" class="i-edit link-except"></a>
                    {/if}
                </div>
            </div>
        </div>
        <div class="comment-pdf">
            <div class="change-history"></div>
            <div class="input-title">Комментарий для PDF-презентации</div>
            <div class="field justify">
                <textarea name="comments[{$item.id}]" data-type="item" class="resizeable fav-comments" data-min-height="40"></textarea>
            </div>
        </div>
        <div class="offer-body">
            <ul class="more-offers">
                {foreach from=$f_data.variants item=variant}									
                    <li  class="offers-item a-link link-wrap variant-item" data-id="{$variant.id}">
                        <div class="offer-cbx a-left link-except">
                            <input type="checkbox" name="variants[]" value="{$variant.id}" class="check-item" />
                        </div>
                        <div class="offer-selection a-right link-except" title="Убрать из выборки">
                            <i class="i-selection m-white"></i>
                        </div>
                        {if empty($ens_search)}
                            <div class="offer-visibility a-right link-except{if $account->isPermission('catalog-item', 'changeItemProp') && $variant.status_offer != 'Сдано' && $variant.status_offer != 'Продано'} m-active{/if}">
                                <i class="i-visibility m-white{if empty($variant['variant_visible']) || $variant['variant_visible'] == 'Скрыто'} m-hidden{/if}"></i>
                            </div>
                        {/if}
                        <div class="offer-status free a-right">{$variant.status_offer}</div>
                        {if !empty($variant.price_min_variant_closed)}
                            <div class="offer-descr small-descr a-right">
                                <i class="i-lock"></i>&nbsp;
                                {$variant.price_range|price_format_range}
                            </div>
                        {/if}
                        <div class="title">
                            <a href="/catalog-view/?id={$item.id}&v={$variant.id}#view-sale" class="link-target">{$variant.variant_title}</a>
                            <a href="/catalog-item/edit/?id={$item.id}&tab=variants&v={$variant.id}" title="Редактировать" class="i-edit link-except"></a>
                        </div>
                    </li>
                {/foreach}
            </ul>
        </div>
    </li>
{/foreach}
</ul>