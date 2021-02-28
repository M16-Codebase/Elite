{?$pageTitle = 'Статус заказа №' . $order.id . ' — Apex Sport'}

<div class="page-title">
	<div class="page-center">
		{*include file="components/breadcrumbs.tpl"*}
		<nav class="breadcrumbs" itemprop="breadcrumb">
			<ul>
				<li class="a-inline-block"><a href="/">Apex Sport</a></li>
			</ul>
		</nav>
		<h1>Состояние заказа {$order.id}</h1>
	</div>
</div>

<div class="main-content">
	<div class="page-center clearbox">
		
		<section class="order-aside-contacts">
			<h3>Есть вопросы?</h3>
			<ul class="order-contacts justify">
				<li>
					<div class="title">Единый телефон</div>
					<div class="cont">{$site_config.phone}</div>
				</li>
				<li>
					<div class="title">Электронная почта</div>
					<div class="cont">
						<a href="mailto:{$site_config.email}">{$site_config.email}</a>
					</div>
				</li>
				<li>
					<div class="title">ICQ</div>
					<div class="cont">
						<span class="cont-icon icq">{$site_config.icq}</span>
					</div>
				</li>
				<li>
					<div class="title">Skype</div>
					<div class="cont">
						<a href="skype:{$site_config.skype}?chat" class="cont-icon skype">{$site_config.skype}</a>
					</div>
				</li>
			</ul>
		</section>
		
		<section class="order-status">
			<div class="order-header">
				<div class="order">Заказ {$order.id} от {$order.date|date_format_lang:'%d %B %Y':'ru'}</div>
				<h2>
					{if $order.status == 'new'}
						ПРИНЯТ в обработку
					{elseif $order.status == 'pay_waiting'}
						ОЖИДАЕТ ОПЛАТЫ
					{elseif $order.status == 'complete'}
						оплачен
					{/if}	
				</h2>
			</div>
			{if $order.status == 'new'}	
				<div class="order-help a-inline-block">
					<span class="tip-icon a-link a-inline-block">Что будет дальше?</span>
					<div class="steps-popup">
						<ul class="order-steps">
							<li class="step-call">
								<div class="title">Менеджер позвонит для подтверждения</div>
								<p>Сотрудник магазина подтвердит наличие товара, оговарит детали доставки и оплаты. Колл-центр работает <span class="lowercase">{$site_config.phone_time}</span>.</p>
							</li>
							<li class="step-mail">
								<div class="title">Вы получаете электронное письмо</div>
								<p>Оно содержит инструкцию по оплате и получению заказа.</p>
							</li>
							<li class="step-pay">
								<div class="title">Оплачиваете и получаете покупку</div>
								<p>Вы платите наличными в банке, салоне связи, на почте, в терминале или совершаете платеж онлайн. После поступления платежа Вы получаете свой заказ.</p>
							</li>
						</ul>
					</div>
				</div>
			{elseif $order.status == 'pay_waiting' && !empty($payment_link)}	
				<div class="order-pay-button">
					<a href="{$payment_link}" target="_blank" class="a-button-red b-big">Оплатить заказ</a>
					<div class="descr">на безопасном сервере OnPay</div>
				</div>
			{elseif $order.status == 'complete'}	
				<div class="order-descr">
					Спасибо за покупку! Ожидайте курьера по оговоренному адресу в назначенное время. В день доставки он позвонит Вам для подтверждения. Не забудьте проверить комплектность и внешний вид товара.
				</div>
			{/if}	
			<div class="order-items">
				{if !empty($order)}
					<div class="order-list-title a-inline-block">Состав заказа {$order.id}</div>
					{?$positions = $order.positions}
					{if !empty($positions)}
						<ul class="order-items-list">
							{foreach from=$positions item=cart_item}
								<li class="order-item clearbox">
									<a href="{$cart_item.data.url}" class="order-item-cover">
										{?$image = $cart_item.image}
										{if !empty($image)}
											<img src="{$image->getUrl(86, 100)}" alt="{$cart_item.title}" />
										{else}
											<img src="/img/icons/no-image.png" alt="{$cart_item.title} noimage" />
										{/if}
									</a>
									<div class="order-item-content">
										<h3><a href="{$cart_item.data.url}">{$cart_item.title}</a></h3>
										<div class="order-item-info">
											<div class="info-inner a-inline-block">
												{if !empty($cart_item.data.color)}<div class="color a-inline-block" style="background-color: #{$cart_item.data.color}"></div>{/if}
												{if !empty($cart_item.data.size)}<div class="size a-inline-block">{$cart_item.data.size}</div>{/if}
											</div>
										</div>
										<div class="order-item-price">
											{?$cart_item_price = $cart_item.count * $cart_item.price}
											<span class="price">{$cart_item_price|price_format} Р</span>
											{if $cart_item.count > 1}
												<span class="count">за {$cart_item.count} шт.</span>
											{/if}
										</div>
									</div>
								</li>
							{/foreach}
						</ul>
						<div class="order-stats clearbox">
							{if !empty($order.delivery_type_self) || !empty($order.delivery_type_courier) || !empty($order.delivery_type_company)}
								<div class="order-deliv">
									<div class="title">Доставка</div>
									{if !empty($order.delivery_type_self)}
										<p>Cамовывоз из магазина</p>
										<div class="price">0 P</div>
									{elseif !empty($order.delivery_type_courier)}									
										<p>Курьер по Петербургу</p>
										<div class="price">{if $order.total_cost >= $site_config.delivery_price_free}0{else}{$site_config.delivery_price}{/if} P</div>
									{elseif !empty($order.delivery_type_company)}
										<p>Транспортной компанией</p>
										{if !empty($order.delivery_price)}<div class="price">{$order.delivery_price} P</div>{/if}
									{/if}
								</div>
							{/if}
							{if !empty($order.pay_type)}
								<div class="order-pay">
									<div class="title">Оплата </div>
									{include file="components/paytype_text.tpl"}
									<p>{$paytype_text[$order.pay_type]}</p>
								</div>
							{/if}
						</div>
						<div class="order-total">
							<div class="total-plank a-inline-block">ИТОГО <span><span>{$order.total_cost|price_format}</span> Р</span></div>
							{if !empty($order.delivery_type_company)}
								<div class="descr">Указано без учета стоимости доставки</div>
							{/if}
						</div>
					{/if}
				{/if}
			</div>
		</section>
			
	</div>
</div>
			
{*include file="popups/onpay_redirect.tpl"*}