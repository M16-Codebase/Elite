{if $seoItemsCount > 0}
	{foreach from=$seoItems item=item}
		<div class="wblock white-block-row" data-name="{$item.page_uid}" data-id="{$item.id}" title="{$item.title}">
			<div class="w1 td-num">{$item.id}</div>
			<div class="w1 td-name">{$item.page_uid}</div>
			<div class="w2 td-title">{if !empty($item.title)}{$item.title}{/if}</div>
			<div class="w2 td-descr">{if !empty($item.description)}{$item.description}{/if}</div>
			<div class="w2 td-keyword">{if !empty($item.keywords)}{$item.keywords}{/if}</div>
			<div class="w1 td-canonical">{if !empty($item.canonical)}{$item.canonical}{/if}</div>
			<div class="action-button action-visibility w1 m-border	action-{if $item.enabled}show{else}hide{/if}"
				 title="{if $item.enabled}Включен{else}Выключен{/if}">
				<i class="icon-{if $item.enabled}show{else}hide{/if}"></i>
			</div>
			<a href='/seo/edit/?id={$item.id}' class="action-button action-edit w1 m-border" title="Редактировать">
				<i class="icon-edit"></i>
			</a>
			<div class="action-button action-delete w1 m-border" title="Удалить">
				<i class="icon-delete"></i>
			</div>
		</div>
	{/foreach}
{else}
	<div class="white-blocks">
		<div class="wblock white-block-row">
			<div class="w12">Мета-теги еще не созданы</div>
		</div>
	</div>
{/if}