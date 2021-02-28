<div class="content-white-block col2">
	<div class="item-list">
		{?$positions = $order.positions}
		{if !empty($positions)}
			<table class="cart-items">
				<thead>
					<tr class="main">
						<th colspan="2">Состав и стоимость заказа</th>
						<th>
							{if $action == 'form'}
								<a href="/order/">— Изменить заказ</a>
							{/if}
						</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$positions item=cart_item}
						{?$v = $cart_item.entity}
						<tr class="item" data-id="{$cart_item.id}">
							<td>
								{?$cover = $cart_item['image']}
								{if !empty($cover)}
									<a href="{$v->getUrl()}">
										<img class="item-img" src="{$cover->getUrl(129, 115)}" alt="{$cart_item.title}" />
									</a>
								{/if}
							</td>
							<td>
								<a href="{$v->getUrl()}">
									<span class="title">{$v.variant_title}</span>
								</a>
							</td>
							<td>
								<span class="item-price one-price" data-price="{$v.price_variant}">
									<span class="num">
										{if !empty($v.old_price_variant)}<span class="old-price"><i></i>{($v.old_price_variant*$cart_item.count)|price_format}</span>{/if}
										{($v.price_variant * $cart_item.count)|price_format}
									</span>
									<span class="descr">руб. за {$cart_item.count} {if !empty($cart_item.data.unit)}{$cart_item.data.unit}{else}шт.{/if}</span>
								</span>
							</td>
						</tr>
					{/foreach}
					<tr>
						<td colspan="2">Промежуточный итог</td>
						<td>
							<span class="item-price all-price">
								<span class="num">{$order.positions_price|price_format}</span>
								<span class="descr">руб.</span>
							</span>
						</td>
					</tr>
					{if !empty($order.discount)}
						<tr>
							<td colspan="2">Скидка</td>
							<td>
								<span class="discount">{floatval($order.discount)}%</span>
								{if !empty($order.discount_type)}
									<span class="descr"> —
										{if $order.discount_type == 'friend'}
											Скидка от друга
										{else}
											{$order.discount_type}
										{/if}
									</span>
								{/if}
							</td>
						</tr>
					{/if}					
					{if $accountType != 'Guest' && $action == 'sended'}
						{if !empty($order.bonus_count)}
							<tr>
								<td colspan="2">Бонусные баллы, списанные в оплату</td>
								<td>
									<span class="num">{$order.bonus_count}</span><span class="descr"> {$order.bonus_count|plural_form:'балл':'балла':'баллов':false}</span>
								</td>
							</tr>
						{/if}
						{if !empty($order_bonus)}
							<tr>
								<td colspan="2">Бонусные баллы к зачислению 
									{if !empty($post_bonus)}
										{include file="components/tip.tpl" color='grey' title=$post_bonus.title content=$post_bonus.text}
									{/if}
								</td>
								<td>
									<span class="num">+ {$order_bonus}</span> <span class="descr">{$order_bonus|plural_form:'балл':'балла':'баллов':false}</span>
								</td>
							</tr>
						{/if}
					{/if}					
				</tbody>
			</table>
		{/if}
		{if $accountType != 'Guest' && $action == 'form'}
			{?$max_bonus_allow = $order->getMaxBonusAllow()}
            {?$user = $account->getUser()}
            {?$max_bonus = $user['bonus'] < $max_bonus_allow ? $user['bonus'] : $max_bonus_allow}
			{if !empty($max_bonus)}
				<div class="bonus">
					<span>
						Сколько бонусных баллов вы потратите на оплату заказа?
						{if !empty($post_bonus)}
							{include file="components/tip.tpl" color='grey' title=$post_bonus.title content=$post_bonus.text}
						{/if}
					</span>
					<div class="bonus-slide-cont">
						<i class="start"></i>
						<div class="bonus-slide" data-max="{$max_bonus}" data-id="{$order.id}"{if !empty($order.bonus_count)} data-val="{$order.bonus_count}"{/if}></div>
					</div>
				</div>
			{/if}	
		{/if}
	</div>
	<div class="form-content-bottom grey-block-inner justify">
		<div class="col1">
			<div class="total">Итого к оплате</div>
			<div class="descr">Цены указаны без учета <a href="/discount/">промо-акций</a></div>
		</div>
		<div class="item-price all-price col1">
			<span class="num">{$order.total_cost|price_format}</span>&nbsp;руб.
		</div>
	</div>
</div>