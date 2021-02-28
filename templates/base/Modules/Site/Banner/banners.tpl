{capture name=banners_content assign=banners_content}
    {?$banners_exists=0}
    {if !empty($banners)}
		{?$banner_get_height = 100}
        {foreach from=$banners item=banner}
            {if !empty($banner.image)}
                {?$banners_exists++}
				<div class="banner wblock white-block-row{if $banner.top == 1} sort-item{/if}" data-id="{$banner.id}" data-url="{$page_url}" data-position="{$banner.position}" data-segment_id="{$banner.segment_id}">
					<div class="w05{if $banner.top == 1} drag-drop{/if}"></div>
					{?$banner_height = $banner_get_height * ($banner.image.height/$banner.image.width)}
					<a href="{$banner.image->getUrl()}" class="banner-img w2 fancybox">
						<img src="{$banner.image->getUrl($banner_get_height)}" height="{$banner_height|floor}"/>
					</a>
					<div class="w2">
						{if !empty($banner.destination)}
							<a href="{if $banner.link_type == 'external'}http:\\{/if}{$banner.destination}" target="_blank">{if $banner.destination == '/'}Главная страница{else}{$banner.destination}{/if}</a>
						{/if}
					</div>
					<div class="w3">
						{if !empty($banner.date_start) || !empty($banner.date_end) || !empty($banner.url)}
							{if !empty($banner.date_start)}
								{if empty($banner.date_end)}C {/if}{$banner.date_start|date_format:'%d.%m.%Y'}{if empty($banner.date_end)}<br>{/if}
							{/if}
							{if !empty($banner.date_end)}
								{if !empty($banner.date_start)} — {else}До {/if}{$banner.date_end|date_format:'%d.%m.%Y'}<br>
							{/if}
							{*{if !empty($banner.url) && empty($catalog_banner)}
								{foreach from=$banner.url item=url name=foo}
									{if $smarty.foreach.foo.iteration == 3}
										<div class="custome-slidebox">
											<a class="slide-header">и еще {($smarty.foreach.foo.total -2)|plural_form:"страница":"страницы":"страниц"}</a>
											<div class="slide-body a-hidden">
									{/if}
										<div class="urls">{if $url == '/'}Главная страница{else}{$url}{/if}{if $smarty.foreach.foo.iteration == 2 || $smarty.foreach.foo.last}{else},{/if}</div>
									{if $smarty.foreach.foo.last && $smarty.foreach.foo.iteration > 2}
											</div>
										</div>
									{/if}
								{/foreach}
							{/if}*}
						{/if}
					</div>
					<div class="w05"></div>
					<div class="w1 action-button action-sort{if $banner.top == 1} action-order{/if}" title="{if $banner.top == 1}Учитывается сортировка{else}В случайном порядке{/if}">
						<i class="icon-{if $banner.top == 1}order{else}random{/if}"></i>
					</div>
					<div class="w1 action-button action-visibility action-{if $banner.active}show{else}hide{/if} m-border" title="{if $banner.active}Показан{else}Скрыт{/if}">
						<i class="icon-{if $banner.active}show{else}hide{/if}"></i>
					</div>
					<div class="w1 action-button action-edit m-border" title="Редактировать"><i class="icon-edit"></i></div>
					<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
				</div>
            {/if}
        {/foreach}
    {/if}
{/capture}
<div class="banners-list white-blocks sortable"  data-sendattrs="url;id;segment_id" data-url="/site-banner/banners/" data-cont=".banners-list" data-items=">.sort-item" data-newpositionname="position">
	{if !empty($banners_exists)}
		{$banners_content|html}
	{else}
		 <div class="wblock white-block-row">
		<div class="w12">Баннеры не созданы</div>
	</div>
	{/if}
</div>
	