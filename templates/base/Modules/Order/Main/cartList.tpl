<div class="white-block-inner">
	<h1>КОРЗИНА{if !empty($order.positions)} №{$order.id}{/if}</h1>
	{?$total_sum = 0}
	{?$total_bonus = 0}
	{?$total_weight = 0}
	{?$no_weight = false}
	{?$positions = $order.positions}
	{if !empty($order) && !empty($positions)}
		<table class="cart-items" data-bonus="{$bonus_ratio}">
			<tr class="main">
				<th colspan="2">Наименование</th>
				<th>Вес</th>
				<th class="a-nowrap">ЦЕНА ЗА ЕДИНИЦУ</th>
				<th>Количество</th>
				<th>СУММА</th>
				{if $accountType != 'Guest'}
					<th class="a-nowrap">БОНУСЫ&nbsp;
						{if !empty($post_bonus)}
							{include file="components/tip.tpl" color='grey' title=$post_bonus.title content=$post_bonus.text}
						{/if}</th>
				{/if}
			</tr>
			{foreach from=$positions item=cart_item}
					{?$v = $cart_item['entity']}
				{?$variant_available = $v.available_in_shops}
				{?$variant_count_in_region_shops = $v.count_in_region_shops}
				<tr data-id="{$cart_item.id}" class="cart-item{if empty($variant_count_in_region_shops)} not-available-row{/if}">
					<td>
						{?$cover = $cart_item['image']}
						{if !empty($cover)}
							<a href="{$v->getUrl()}" class="cover">
								<img src="{$cover->getUrl(129, 115)}" alt="{$cart_item.title}" />
							</a>
						{/if}
					</td>
					<td>
						<a href="{$v->getUrl()}" class="item-title">{$v.variant_title}</a>
						<div class="item-info">
							<span class="art descr">{$v.code}</span>
							{if empty($variant_available) || $variant_available == 'Нет'}
								<span class="availability not-available"><i class="icon i-availability"></i>Нет в наличии</span>
							{elseif $variant_available == 'Мало'}
								<span class="availability little"><i class="icon i-availability"></i>Мало в наличии</span>
							{elseif $variant_available == 'Есть'}
								<span class="availability normally"><i class="icon i-availability"></i>Есть в наличии</span>
							{elseif $variant_available == 'Много'}
								<span class="availability many"><i class="icon i-availability"></i>Много в наличии</span>
							{/if}	
							<a href="#" class="compare compare-item-{$v.id}{if isset($compare_variants[$v.id])} m-active{/if}" data-id="{$v.id}" data-url="{$v->getUrl()}" data-image="{$cover->getUrl(60,70)}" data-title="{$v.variant_title}">
								<i class="icon i-compare"></i><span>{if isset($compare_variants[$v.id])}В сравнении{else}Сравнить{/if}</span>
							</a>
						</div>
					</td>
					<td>
						{if !$no_weight}
							{if !empty($cart_item.weight)}
								{?$total_weight += $cart_item.weight*$cart_item.count}
							{else}
								{?$no_weight = true}
							{/if}
						{/if}
						<div class="a-nowrap weight descr"{if !empty($cart_item.weight)} data-weight="{$cart_item.weight}"{/if}>{if !empty($v.ves_netto)}{$v.ves_netto}{else}—{/if}</div>
					</td>
					<td>
						<span class="item-price one-price" data-price="{$v.price_variant}">
							<span class="num">
								{if !empty($v.old_price_variant)}<span class="old-price"><i></i>{$v.old_price_variant|price_format}</span>{/if}
								{$v.price_variant|price_format}
							</span>
							<span class="descr a-nowrap">руб. / {if !empty($cart_item.data.unit)}{$cart_item.data.unit}{else}шт.{/if}</span>
						</span>
					</td>
					{if !empty($variant_count_in_region_shops)}
						<td class="a-nowrap">
							<input type="text" name="count" data-max="{$v.count}" class="count-input" data-mask="9?99" placeholder="1" value="{$cart_item.count}" />&nbsp;
							<span class="a-nowrap descr ">{if !empty($cart_item.data.unit)}{$cart_item.data.unit}{else}шт.{/if}</span>
						</td>
						<td>
							<span class="item-price all-price" data-price="{$v.price_variant}">
								{?$cart_item_price = $cart_item.count * $v.price_variant}
								{?$total_sum += $cart_item_price}
								<span class="num">{$cart_item_price|price_format}</span>
								<span class="descr">руб.</span>
							</span>
						</td>
					{else}
						<td colspan="2">
							<a href=".popup-subscr.subscr-avail" class="descr subscr-avail" data-toggle="popup" data-action="open" data-id="{$cart_item.id}">— Сообщить о поступлении в продажу</a>
						</td>
					{/if}
					<td>
						<div class="last-cell">
							<div class="delete" data-id="{$cart_item.id}" title="Удалить из корзины">
								<a href="#" class="btn btn-delete"><i class="icon i-cart-delete"></i></a>
							</div>
							{if $accountType != 'Guest'}	
								<span class="points-number descr">
									{if !empty($cart_item.bonus)}
										{?$total_bonus += $cart_item.bonus}
										<i class="icon i-bonus"></i><span class="num-bonus">{$cart_item.bonus}</span> {$cart_item.bonus|plural_form:'балл':'балла':'баллов':false}
										{if !empty($post_bonus)}
											{include file="components/tip.tpl" color='grey' title=$post_bonus.title content=$post_bonus.text}
										{/if}
									{else}
										—
									{/if}	
								</span>
							{/if}
						</div>
					</td>
				</tr>
			{/foreach}
		</table>
	{else}
		<div class="empty-list">
			<p class="empty-result main">В корзине ничего нет. <a href="/catalog/">Продолжить покупки.</a></p>
		</div>
	{/if}
</div>

{if !empty($order) && !empty($order.positions)}
	<div class="gray-block-inner cart-orders justify">
		<div class="col1"></div>
		<div class="orders">
			<div class="total-price">Итого&nbsp;&nbsp;<span class="price">{$total_sum|price_format}</span> руб.</div>
			<span>Цены указаны без учета <a href="/discount/">промо-акций</a></span>
			<div class="orders-btn">
				<a class="btn btn-white-yellow" href="/order/step_one/" title="">Оформить заказ</a>
			</div>
		</div>
		<div class="col2">
			<div class="orders-info{if $no_weight} m-no-weight{/if}">
				{if $accountType != 'Guest'}
					<div class="points-number descr">
						<i class="icon i-bonus"></i> <span class="num-bonus">{$total_bonus}</span> {$total_bonus|plural_form:'балл':'балла':'баллов':false} на счет
						{if !empty($post_bonus)}
							{include file="components/tip.tpl" color='grey' title=$post_bonus.title content=$post_bonus.text}
						{/if}
					</div>
				{/if}
				<div class="total-weight descr"><i class="icon i-weight"></i> Общий вес — <span class="num">{if !empty($total_weight)}{$total_weight}{/if}</span> кг</div>					
			</div>
			<div class="delete">
				{*<a href="#" class="clear-cart btn btn-delete">Очистить корзину<i class="icon i-cart-delete"></i></a>*}
			</div>
		</div>
	</div>
	<div class="text-center">
		<a href="/order/delay/" class="delay-order descr">— Отложить на потом</a>		
	</div>
{/if}
{if !empty($quicky.get.delayed)}
	<div class="order-delayed-popup a-hidden"></div>
{/if}