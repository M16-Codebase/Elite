{?$delim = ldelim . "!" . rdelim}

{if !empty($real_estate)}
	<div class="center autocomplete-wrap">
		<div class="text-wrap">
			<div class="main">{$lang->get('Строящаяся недвижимость', 'New objects')}</div>
		</div>
		{foreach from=$real_estate item=item name=real_n}
			{?$title = $delim|explode:$item.title}
			<a href="{if !empty($item->getUrl())}{$item->getUrl()}{/if}" class="object-row">
				<div class="left">
					{if !empty($title)}<div class="title">{if !empty($title[0])}<span>{$title[0]}</span>{/if} {if !empty($title[1])}{$title[1]}{/if}</div>{/if}
					{if !empty($item.district.prepositional)}<div class="small-descr">{$item.district.prepositional}</div>{/if}
				</div>
				<div class="right">
					{if !empty($item.properties.price_meter_from.value)}<div class="price">{$item.properties.price_meter_from.value} <i>+</i><br><span>{$lang->get('тыс.рублей за м', 'ths rub. per m')}<sup>2</sup></span></div>{/if}
					{if !empty($item.app_area)}<div class="small-descr area">{$item.app_area}</div>{/if}
				</div>
			</a>
		{/foreach}
		
		{if $real_estate_count > $list_size}
            {?$else_count = $real_estate_count - $smarty.foreach.real_n.total}
			<div class="more-row">
				<a href="{$url_prefix}/real-estate/?title={$phrase}" class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if}>
					{$lang->get('Показать еще ' . ($else_count|plural_form:'объект':'объекта':'объектов'), 'Show ' . $else_count . ' more ' . ($else_count|plural_form:'offer':'offers':'offers':false))}
				</a>
			</div>
		{/if}
	</div>
{/if}

{if !empty($resale)}
	<div class="center autocomplete-wrap">
		<div class="text-wrap">
			<div class="main">{$lang->get('Вторичная недвижимость', 'Resale apartments')}</div>
		</div>
		{foreach from=$resale item=item name=resale_n}
			{?$title = $delim|explode:$item.title}
			<a href="{if !empty($item->getUrl())}{$item->getUrl()}{/if}" class="object-row">
				<div class="left">
					{if !empty($title)}<div class="title">{if !empty($title[0])}<span>{$title[0]}</span>{/if} {if !empty($title[1])}{$title[1]}{/if}</div>{/if}
					{if !empty($item.district.prepositional)}<div class="small-descr">{$item.district.prepositional}</div>{/if}
				</div>
				<div class="right">
					{if !empty($item.properties.price.value)}<div class="price">{$item.properties.price.value}<br><span>{$lang->get('млн рублей', 'mln rubles')}</span></div>{/if}
					{if !empty($item.app_area)}<div class="small-descr area">{$item.app_area}</div>{/if}
				</div>
			</a>
		{/foreach}
		
		{if $resale_count > $list_size}
            {?$else_count = $resale_count - $smarty.foreach.resale_n.total}
		<div class="more-row">
			<a href="{$url_prefix}/resale/?title={$phrase}" class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if}>
				{$lang->get('Показать еще ' . ($else_count|plural_form:'объект':'объекта':'объектов'), 'Show ' . $else_count . ' more ' . ($else_count|plural_form:'offer':'offers':'offers':false))}
			</a>
		</div>
		{/if}
	</div>
{/if}
{if !empty($residential)}
	<div class="center autocomplete-wrap">
		<div class="text-wrap">
			<div class="main">{$lang->get('Загородная недвижимость', 'residential apartments')}</div>
			
		</div>
		{foreach from=$residential item=item name=residential_n}
			{?$title = $delim|explode:$item.title}
			<a href="{if !empty($item->getUrl())}{$item->getUrl()}{/if}" class="object-row">
				<div class="left">
					{if !empty($title)}<div class="title">{if !empty($title[0])}<span>{$title[0]}</span>{/if} {if !empty($title[1])}{$title[1]}{/if}</div>{/if}
					{if !empty($item.district.prepositional)}<div class="small-descr">{$item.district.prepositional}</div>{/if}
				</div>
				<div class="right">
					{if !empty($item.properties.price.value)}<div class="price">{$item.properties.price.value}<br><span>{$lang->get('млн рублей', 'mln rubles')}</span></div>{/if}
					{if !empty($item.app_area)}<div class="small-descr area">{$item.app_area}</div>{/if}
				</div>
			</a>
		{/foreach}
		
		{if $residential_count > $list_size}
            {?$else_count = $residential_count - $smarty.foreach.residential_n.total}
		<div class="more-row">
			<a href="{$url_prefix}/residential/?title={$phrase}" class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if}>
				{$lang->get('Показать еще ' . ($else_count|plural_form:'объект':'объекта':'объектов'), 'Show ' . $else_count . ' more ' . ($else_count|plural_form:'offer':'offers':'offers':false))}
			</a>
		</div>
		{/if}
	</div>
{/if}
{if !empty($arenda)}
	<div class="center autocomplete-wrap">
		<div class="text-wrap">
			<div class="main">{$lang->get('Аренда недвижимости', 'arenda apartments')}</div>
			
		</div>
		{foreach from=$arenda item=item name=arenda_n}
			{?$title = $delim|explode:$item.title}
			<a href="{if !empty($item->getUrl())}https://m16-elite.ru/arenda/{substr($item->getUrl(),8)}{/if}" class="object-row">
				<div class="left">
					{if !empty($title)}<div class="title">{if !empty($title[0])}<span>{$title[0]}</span>{/if} {if !empty($title[1])}{$title[1]}{/if}</div>{/if}
					{if !empty($item.district.prepositional)}<div class="small-descr">{$item.district.prepositional}</div>{/if}
				</div>
				<div class="right">
					{if !empty($item.properties.price.value)}<div  class="price">{$item.properties.price.value}<br><span>{$lang->get('тыс. рублей', 'mln rubles')}</span></div>{/if}
					{if !empty($item.app_area)}<div class="small-descr area">{$item.app_area}</div>{/if}
				</div>
			</a>
		{/foreach}
		
		{if $arenda_count > $list_size}
            {?$else_count = $arenda_count - $smarty.foreach.arenda_n.total}
		<div class="more-row">
			<a href="{$url_prefix}/arenda/?title={$phrase}" class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if}>
				{$lang->get('Показать еще ' . ($else_count|plural_form:'объект':'объекта':'объектов'), 'Show ' . $else_count . ' more ' . ($else_count|plural_form:'offer':'offers':'offers':false))}
			</a>
		</div>
		{/if}
	</div>
{/if}						
