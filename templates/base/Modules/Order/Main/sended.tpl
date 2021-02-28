{?$includeOuterJS.map = 'https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU'}

<div class="cart-page cart-sended">
	<div class="content-white-block white-block-inner">
		<h1>Заказ №{$order.id} оформлен!</h1>
		<div class="descr">Благодарим вас за обращение в нашу компанию!</div>
	</div>
	<div class="form-content justify">
		{include file="Modules/Order/Main/orderItems.tpl"}
		<div class="content-white-block order-end col2">
			<div class="order-end-cont">
				<div class="title">Что делать дальше?</div>
				<div>
					Дождитесь звонка оператора нашего интернет-магазина.
					Он согласует и потвердит ваш заказ, а так же объяснит как вы можете его получить.
				</div>
			</div>
			<div class="order-end-cont">
				<div class="title">Если у вас есть вопросы, свяжитесь с нами</div>
				<div>Телефон: {$request_segment.phone}</div>
				<div>Электронная почта: <a href="mailto:{$request_segment.email}">{$request_segment.email}</a></div>
			</div>
			<div class="order-end-cont">
				{if !empty($order.delivery_type_self)}
					{?$stores = $request_segment->getStore()}
					{?$current_store = $stores[$order.delivery_type_self]}
					<div class="title">Информация о магазине, который вы выбрали</div>
					<div class="delivery-adds-info a-inline-block">
						<div class="info">{if !empty($current_store.coords)}<a href="#" class="show-map" data-coords="{$current_store.coords}" data-title="{$current_store.title}">{$current_store.address}</a>{else}{$current_store.address}{/if}</div>
						<div class="info">Телефон: {$current_store.phone}</div>
						<div class="info">{if !empty($current_store.days)}{$current_store.days}{/if}</div>
						<div class="info">Стоимость доставки — бесплатно</div>
					</div>
				{/if}
			</div>
			{include file="components/invite-block.tpl"}			
		</div>
	</div>
</div>
		
<div class="popup-window popup-map" data-class="yellow-form-popup" data-width="600">
	<div class="map-cont"></div>
</div>








{*{?$pageTitle = 'Заказ принят — Apex Sport'}
{?$no_float_header = 1}

<div class="main-content">
	<div class="page-center clearbox">
		
		<section class="order-col order-form a-left">
			<div class="order-title">
				<h3 class="a-inline-block">ЗАКАЗ ПРИНЯТ</h3>
			</div>
			<div class="order-col-content">
				<section class="order-sended-top">
					<h3>ОТЛИЧНО! Ваш заказ поступил в обработку</h3>
					<div>Благодарим за то, что Вы выбрали Apex Sport!</div>
					<div class="order-id">
						<span>НОМЕР ЗАКАЗА — </span><span class="num">{$order.id}</span>
					</div>
					<div>
						Вы сможете отслеживать состояние заказа до момента его получения.
						{*Вы можете отслеживать <a href="{$order_view_link}">состояние заказа</a>.*}
					{*</div>
				</section>
				<section class="order-sended-mid">
					<h3>ЧТО ДАЛЬШЕ?</h3>
					<ul class="order-steps">
						<li class="step-call">
							<div class="title">Менеджер позвонит для подтверждения</div>
							<p>Сотрудник магазина подтвердит наличие товара, оговарит детали доставки и оплаты. Колл-центр работает <span class="lowercase">{$site_config.phone_time}</span>.</p>
							<div class="popup-cont">
								<span class="tip-icon a-inline-block a-link">Что если Вам не перезвонили?</span>
								<div class="popup">
									Если такое все-таки произошло, пожалуйста, позвоните нам по телефону {$site_config.phone}. Обратите внимание на режим работы колл-центра.
								</div>
							</div>
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
				</section>
				<section class="order-sended-bottom">
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
				<div class="share">
					<span>Поделитесь с друзьями — </span>
					<div class="yashare-auto-init a-inline-block" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,gplus"></div> 
				</div>
			</div>
			
		</section>
		
		<section class="order-col order-items a-right">
			<div class="order-title">
				<h3 class="a-inline-block">СОСТАВ ЗАКАЗА {$order.id}</h3>
			</div>
			<div class="order-col-content">
				{if !empty($order)}
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
					{else}
						<div class="empty-list">В корзине ничего нет</div>
					{/if}
				{else}
					<div class="empty-list">В корзине ничего нет</div>
				{/if}
			</div>
		</section>
		
	</div>
</div>*}