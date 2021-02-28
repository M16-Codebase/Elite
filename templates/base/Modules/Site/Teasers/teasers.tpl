{capture name=teasers_content assign=teasers_content}
    {?$teasers_exists=0}
    {if !empty($teasers)}
        {foreach from=$teasers item=teaser}
            {if !empty($teaser.image)}
                {?$teasers_exists++}
				<div class="wblock white-block-row" data-id="{$teaser.id}">
					<a href="{$teaser.image->getUrl()}" class="teaser-img w2 fancybox">
						<img src="{$teaser.image->getUrl(100)}" />
					</a>
					<div class="w2">
                        {$teaser.title}
						{if !empty($teaser.url)}
							<a href="{if $teaser.link_type == 'external'}http:\\{/if}{$teaser.url}" target="_blank">{if $teaser.url == '/'}Главная страница{else}{$teaser.url}{/if}</a>
						{/if}
					</div>
					<div class="w3">
						{if !empty($teaser.date_start) || !empty($teaser.date_end) || !empty($teaser.url)}
							{if !empty($teaser.date_start)}
								{if empty($teaser.date_end)}C {/if}{$teaser.date_start|date_format:'%d.%m.%Y'}{if empty($teaser.date_end)}<br>{/if}
							{/if}
							{if !empty($teaser.date_end)}
								{if !empty($teaser.date_start)} — {else}До {/if}{$teaser.date_end|date_format:'%d.%m.%Y'}<br>
							{/if}
						{/if}
					</div>
					<div class="w2"></div>
					<div class="w1 action-button action-visibility action-{if $teaser.active}show{else}hide{/if} m-border" title="{if $teaser.active}Показан{else}Скрыт{/if}">
						<i class="icon-{if $teaser.active}show{else}hide{/if}"></i>
					</div>
					<div class="w1 action-button action-edit m-border" title="Редактировать"><i class="icon-edit"></i></div>
					<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
				</div>
            {/if}
        {/foreach}
    {/if}
{/capture}

{if !empty($teasers_exists)}
    <div class="white-blocks" data-url="/site-teaser/teasers/">
        {$teasers_content|html}
    </div>
{else}
	<div class="white-blocks">
        <div class="wblock white-block-row">
            <div class="w12">Тизеры не созданы</div>
        </div>
    </div>
{/if}