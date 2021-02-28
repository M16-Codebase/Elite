{include file="components/orderPositions.tpl"}
<div class="added-block">
	<div class="close-popup"></div>
	<h2 class="page-aside-header grad-text">{$catalog_item.title}</h2>
</div>
{if !empty($type_properties)}
	{capture assign=item_props}
		{?$first_item_prop = true}
		{foreach from=$type_properties item=prop}
			{if empty($prop.multiple) && !empty($catalog_item['properties'][$prop.key]['complete_value'])}
				{if !$first_item_prop}, {/if}
				{$prop.title} — {$catalog_item['properties'][$prop.key]['complete_value']}
				{?$first_item_prop = false}
			{/if}
		{/foreach}
	{/capture}
	{?$item_props = $item_props|trim}
	{if !empty($item_props)}
		<div class="added-block">
			{$item_props|html}
		</div>
	{/if}
{/if}
<div class="search-variant-filter">
	{include file="Modules/Catalog/Main/itemsFilter.tpl" filter_popup=1}
</div>
<div class="search-variant-result">
	{if !empty($variants)}
		{capture assign=props_table}
			<table>
				<tr>
					<th class="title-col aside-cell sortable{if empty($smarty.get.sort) || isset($smarty.get.sort.variant_title)} m-sort{if !empty($smarty.get.sort) && $smarty.get.sort.variant_title == 1} m-up" data-value="0{else}" data-value="1{/if}{/if}" data-sort="sort[variant_title]">
						<span>Наименование</span>
					</th>
					<th class="avail-col sortable{if !empty($smarty.get.sort) && isset($smarty.get.sort.count)} m-sort{if $smarty.get.sort.count == 1} m-up" data-value="0{else}" data-value="1{/if}{/if}" data-sort="sort[count]">
						<span><i class="avail-circle" title="Наличие"></i></span>
					</th>
					{if !empty($type_properties)}
						{foreach from=$type_properties item=prop}
							{if $prop.multiple}
								<th class="sortable{if !empty($smarty.get.sort) && isset($smarty.get.sort[$prop.key])} m-sort{if $smarty.get.sort[$prop.key] == 1} m-up" data-value="0{else}" data-value="1{/if}{/if}" data-sort="sort[{$prop.key}]">
									<span>{$prop.title}</span>
								</th>
							{/if}
						{/foreach}
					{/if}
					<th class="price-col last-col aside-cell"></th>
				</tr>
				{foreach from=$variants item=variant}
					<tr class="{if empty($variant.count) && empty($variant.count_expects)}not-available{/if}">
						<td class="aside-cell"><a href="{$variant->getUrl()}" class="variant-title" data-id="{$variant.id}">{$variant.variant_title}</a></td>
						<td>
							{if !empty($variant.count)}
								<i class="avail-circle m-avail" title="Есть в наличии"></i>
							{elseif !empty($variant.count_expects)}
								<i class="avail-circle m-order" title="Под заказ"></i>
							{else}
								<i class="avail-circle m-none" title="Нет в наличии"></i>
							{/if}
						</td>
						{if !empty($type_properties)}
							{foreach from=$type_properties item=prop}
								{if $prop.multiple}
									<td>
										<span>
											{if !empty($variant.properties[$prop.key]['complete_value'])}
												{if is_array($variant.properties[$prop.key]['complete_value'])}
													{implode(', ', $variant.properties[$prop.key]['complete_value'])}
												{else}
													{$variant.properties[$prop.key]['complete_value']}
												{/if}
											{else}
												—
											{/if}
										</span>
									</td>
								{/if}
							{/foreach}
						{/if}
						<td class="last-col aside-cell">
							{if !empty($variant.count) || !empty($variant.count_expects)}
								<a href="/order/" class="add-to-cart price-btn m-with-basket v{$variant.id}{if !empty($ordersPositions[$variant.id])} a-hidden{/if}" data-id="{$variant.id}">
									<img src="/img/icons/product_s_3-02.png" alt="">
									<div class="content">{$variant.price_variant|price_format} Р/{if !empty($catalog_item.unit)}{$catalog_item.unit}{else}шт.{/if}</div> 
								</a>
								<a href="/order/" class="add-to-cart price-btn m-in-basket v{$variant.id}{if empty($ordersPositions[$variant.id])} a-hidden{/if}">
									<img src="/img/icons/in-basket-body.png" alt="">
									<div class="content">{$variant.price_variant|price_format} Р/{if !empty($catalog_item.unit)}{$catalog_item.unit}{else}шт.{/if}</div>
								</a>
								<!--<a href="/order/" class="btn btn-blue-small m-in-basket v{$variant.id}{if empty($ordersPositions[$variant.id])} a-hidden{/if}">
									<img src="/img/icons/in-basket-body.png" alt="">
									
								</a>-->
							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
		{/capture}
		<div class="variants-title-table">
			{$props_table|html}
		</div>
		<div class="variants-buy-table">
			{$props_table|html}
		</div>
		<div class="variants-props-table">
			{$props_table|html}
		</div>
	{else}
		<div class="empty-result">
			<div class="main">Нет товаров соответствующих заданным параметрам</div>
			Попробуйте сделать следующее:<br>
			— Измените параметры фильтрации<br>
			— Свяжитесь с нашими консультантами по телефону <strong>{$site_config.office_contacts.phone[0]}</strong>
		</div>
	{/if}
</div>