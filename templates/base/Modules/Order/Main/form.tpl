{?$includeOuterJS.map = 'https://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU'}

<div class="cart-page cart-form ">
	<div class="content-white-block white-block-inner">
		<h1>Оформление заказа №{$order.id}</h1>
		<div class="descr">Не хотите оформлять на сайте? Позвоните на {$request_segment.phone} и назовите номер заказа</div>
	</div>
	<div class="form-content justify">
		{include file="Modules/Order/Main/orderItems.tpl"}
		<div class="content-white-block form-add col2">
			<form class="order-forming-form">
				{*<input type="hidden" name="bonus" value="0" class="bonus-input" />*}
				<div class="delivery">
					<div class="title">Как вы хотите получить заказ?<span class="descr"> — Все о <a href="/main/delivery/">доставке</a></span></div>
					<div class="tabs-cont">
						<nav class="item-tabs">
							<ul class="tabs-titles a-inline-block a-inline-cont">
								<li><a class="m-current"href="#pickup">Самовывоз</a></li>
								{*<li><a href="#courier">Курьером</a></li>
								<li><a href="#transport-company">Транспортной компанией</a></li>*}
							</ul>
						</nav>
						<div id="pickup" class="tab-page m-current">							
							<input type="hidden" name="delivery_type" value="self" />
							<div class="title">Где вы хотите забрать заказ?</div>
							<div class="delivery-store justify">
								{if !empty($stores)}
									<div class="col1">
										<ul class="delivery-adds">
											{foreach from=$stores item=store name=stores}
												<li>
													<label class="address-radio{if first} btn btn-light-grey m-current{/if}" data-id="{$store.id}">
														<input type="radio" name="store_id" value="{$store.id}"{if first} checked{/if}>
														<i class="icon i-adds"></i>{$store.title}
													</label>
												</li>
											{/foreach}
										</ul>
									</div>
								{/if}
								<div class="delivery-adds-info col1">
									{foreach from=$stores item=store name=stores}
										<div class="address{if first} m-current{/if} adds-{$store.id}">
											<div class="info">{if !empty($store.coords)}<a href="#" class="show-map" data-coords="{$store.coords}" data-title="{$store.title}">{$store.address}</a>{else}{$store.address}{/if}</div>
											{if !empty($store.phone)}
												<div class="info">Телефон: {$store.phone}</div>
											{/if}
											{if !empty($store.days)}
												<div class="info">{$store.days}</div>
											{/if}
											<div class="info">Стоимость доставки — бесплатно</div>
										</div>
									{/foreach}								
								</div>
								<div class="title paytype">
									<input type="hidden" name="pay_type" value="nal" />
									Оплата заказа наличными в кассе магазина<span class="descr"> — Все об <a href="/main/payment/">оплате</a></span>
								</div>
								</div>
							</div>
						{*<div id="courier" class="tab-page">			
							<input type="hidden" name="delivery_type" value="courier" />
							<div class="title">Курьером</div>
							<input type="text" name="street" value="">
							<input type="text" name="house" value="">
							<input type="text" name="korpus" value="">
							<input type="text" name="apart" value="">
							<input type="text" name="floor" value="">
						</div>
						<div id="transport-company" class="tab-page">										
							<input type="hidden" name="delivery_type" value="company" />
							<div class="title">Транспортной компанией</div>
							<select name="transport_company_id" class="chosen select-transp fullwidth chzn-done" id="selHOW" style="display: none;">
								<option data-site="emspost.ru/ru/calc/" value="1">EMS Почта России</option>
								<option data-site="pecom.ru/ru/calc/" value="2">ПЭК</option>
							</select>
							<input type="text" name="index" value="">
							<input type="text" name="city" value="">
							<input type="text" name="street" value="">
							<input type="text" name="house" value="">
							<input type="text" name="korpus" value="">
							<input type="text" name="apart" value="">
							<input type="text" name="floor" value="">
						</div>*}
					</div>
				</div>
				<div class="registration">
					{?$current_user = $account->getUser()}
					<div class="tabs-cont">
						<nav class="item-tabs">
							<ul class="tabs-titles a-inline-block a-inline-cont">
								{if $current_user.person_type == 'fiz'}
									<li><a class="m-current" href="#individual">Физическое лицо</a></li>
								{elseif $current_user.person_type == 'org'}
									<li><a class="m-current" href="#person">Юридическое лицо</a></li>
								{else}
									<li><a class="m-current" href="#individual">Физическое лицо</a></li>
									<li><a href="#person">Юридическое лицо</a></li>
								{/if}								
							</ul>
						</nav>
						<div class="grey-block-inner">
							<form class="registration-form">
								<div class="descr">
									<i class="icon i-lock"></i>
									<span class="a-inline-block">Мы гаратируем сохранность информации. Данные, вводимые вами, будут использоваться только для обработки данного заказа.</span>
								</div>
								
								<div id="individual" class="tab-page{if $current_user.person_type != 'org'} m-current{/if}">
									<input type="hidden" name="person_type" value="fiz">
									<div class="field-cont field-row justify">
										<div class="field">
											<div class="f-title">
												<span class="required">Имя</span>
											</div>
											<div class="f-input">
												<input type="text" name="name" />
											</div>
										</div>
										<div class="field">
											<div class="f-title">
												<span class="required">Фамилия</span>
											</div>
											<div class="f-input">
												<input type="text" name="surname" />
											</div>
										</div>
									</div>
									<div class="field-cont field-row justify">
										<div class="field">
											<div class="f-title">
												<span class="required">Телефон</span>
											</div>
											<div class="f-input">
												<input type="text" name="phone" />
											</div>
										</div>
										<div class="field">
											<div class="f-title">
												<span class="required">Электронная почта</span>
											</div>
											<div class="f-input">
												<input type="text" name="email" />
											</div>
										</div>
									</div>									
								</div>	
								
								<div id="person" class="tab-page{if $current_user.person_type == 'org'} m-current{/if}">
									<input type="hidden" name="person_type" value="org">
									<div class="field-cont field-row justify">
										<div class="field">
											<div class="f-title">
												<span class="required">Имя контактного лица</span>
											</div>
											<div class="f-input">
												<input type="text" name="name" />
											</div>
										</div>
										<div class="field">
											<div class="f-title">
												<span class="required">Фамилия контактного лица</span>
											</div>
											<div class="f-input">
												<input type="text" name="surname" />
											</div>
										</div>									
									</div>
									<div class="field-cont field-row justify">
										<div class="field">
											<div class="f-title">
												<span class="required">Название организации</span>
											</div>
											<div class="f-input">
												<input type="text" name="company_name" />
											</div>
										</div>
										<div class="field">
											<div class="f-title">
												<span class="required">ИНН</span>
											</div>
											<div class="f-input">
												<input type="text" name="inn" />
											</div>
										</div>									
									</div>
									<div class="field-cont field-row justify">
										<div class="field">
											<div class="f-title">
												<span class="required">Телефон</span>
											</div>
											<div class="f-input">
												<input type="text" name="phone" />
											</div>
										</div>
										<div class="field">
											<div class="f-title">
												<span class="required">Электронная почта</span>
											</div>
											<div class="f-input">
												<input type="text" name="email" />
											</div>
										</div>
									</div>
								</div>	
								
								<div class="field-cont">
									<a class="help open-descr-field" href="#">— Есть дополнительные пожелания и уточнения?</a>
									<div class="field hidden-text">
										<div class="f-title">
											<span class="required">Дополнительные пожелания и комментарии</span>
										</div>
										<div class="f-input">
											<input type="text" name="descr" />
										</div>
									</div>
								</div>
								
								<div class="btn-cont">
									<div class="orders-btn a-inline-block">
										<button class="btn btn-white-yellow">Отправить заказ</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
									
<div class="popup-window popup-map" data-class="yellow-form-popup" data-width="600">
	<div class="map-cont"></div>
</div>
{include file="/components/bonus-program.tpl"}										
											
{*
{?$pageTitle = 'Оформление заказа — Apex Sport'}

{?$no_float_header = 1}
{?$total_sum = 0}

<div class="main-content">
	<div class="page-center clearbox">
		
		<section class="order-col order-form a-left">
			<div class="order-title">
				<h3 class="a-inline-block">ОФОРМЛЕНИЕ ЗАКАЗА</h3>
			</div>
			<div class="order-col-content">
				<div class="catalog-tabs">
					<ul>
						<li class="a-inline-block m-current" data-type="fiz">Я — ФИЗЛИЦО</li>
						<li class="a-inline-block" data-type="org">МЫ — ЮРЛИЦО</li>
					</ul>
					<div class="item-tab-pages">
						<div class="tab-page fiz">
							<form action="/order/form/" class="user-form">
								<input type="hidden" name="person_type" value="fiz" />
								<p class="notify">Мы будем использовать Ваши личные данные только для оформления этого заказа.</p>
								<div class="field f-name">
									<div class="f-title">
										<span class="error-text e-empty">Укажите, пожалуйста, Ваше имя</span>
										<span class="required">Ваше имя</span>
									</div>
									<div class="f-input">
										<input type="text" name="name" />
									</div>
								</div>
								<div class="field f-phone">
									<div class="f-title">
										<span class="error-text e-empty">Укажите, пожалуйста, Ваш номер телефона</span>
										<span class="required">Номер телефона</span>
									</div>
									<div class="f-input">
										<input type="text" name="phone" />
									</div>
								</div>
								<div class="field f-email check-valid" data-type="checkEmail">
									<div class="f-title">
										<span class="error-text e-empty">Укажите, пожалуйста, адрес Вашей электронной почты</span>
										<span class="error-text e-valid e-incorrect_format">Некорректный адрес электронной почты</span>
										<span class="required">Электронная почта</span>
									</div>
									<div class="f-input">
										<input type="text" name="email" />
									</div>
									<label class="f-descr">
										<input type="checkbox" class="cbx" name="want_news" checked />
										<span>Я хочу получать на указанный адрес новости и специальные предложения от Apex Sport</span>
									</label>
								</div>
								{include file="Modules/Order/Main/form_delivery_pay.tpl"}
								<div class="field">
									<div class="f-title"><span>Пожелания и комментарии </span></div>
									<div class="f-input">
										<textarea name="descr" rows="6"></textarea>
									</div>
								</div>
								<div class="buttons">
									<button class="a-button-red submit">Отправить заказ<span class="arrow"></span></button>
								</div>
							</form>
						</div>
						<div class="tab-page org a-hidden">
							<form action="/order/form/" class="user-form">
								<input type="hidden" name="person_type" value="org" />
								<p class="notify">Мы будем использовать Ваши личные данные только для оформления этого заказа.</p>
								<div class="field f-name">
									<div class="f-title">
										<span class="error-text e-empty">Укажите, пожалуйста, имя контактного лица</span>
										<span class="required">Имя контактного лица </span>
									</div>
									<div class="f-input">
										<input type="text" name="name" />
									</div>
								</div>
								<div class="field f-phone">
									<div class="f-title">
										<span class="error-text e-empty">Укажите, пожалуйста, Ваш номер телефона</span>
										<span class="required">Номер телефона</span>
									</div>
									<div class="f-input">
										<input type="text" name="phone" />
									</div>
								</div>
								<div class="field f-email check-valid" data-type="checkEmail">
									<div class="f-title">
										<span class="error-text e-empty">Укажите, пожалуйста, адрес Вашей электронной почты</span>
										<span class="error-text e-valid">Некорректный адрес электронной почты</span>
										<span class="required">Электронная почта</span>
									</div>
									<div class="f-input">
										<input type="text" name="email" />
									</div>
									<label class="f-descr">
										<input type="checkbox" class="cbx" name="want_news" checked />
										<span>Я хочу получать на указанный адрес новости и специальные предложения от Apex Sport</span>
									</label>
								</div>
								
								{include file="Modules/Order/Main/form_delivery_pay.tpl" type_org=true}
								<div class="field">
									<div class="f-title"><span>Реквизиты</span></div>
									<div class="f-input">
										<textarea name="requisites" rows="6"></textarea>
									</div>
								</div>
								<div class="field">
									<div class="f-title"><span>Пожелания и комментарии </span></div>
									<div class="f-input">
										<textarea name="descr" rows="6"></textarea>
									</div>
								</div>
								<div class="buttons">
									<p class="descr a-right">Отправляя заказ, Вы соглашаетесь с <span class="rules a-link">Условиями продажи</span></p>
									<button class="a-button-red submit">Отправить заказ<span class="arrow"></span></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<section class="order-col order-items a-right">
			<div class="order-title">
				<h3 class="a-inline-block">В КОРЗИНЕ</h3>
				<a href="/order/" class="back-to-cart a-inline-block">Изменить корзину</a>
			</div>
			<div class="order-col-content">
				{if !empty($order)}
					{?$positions = $order.positions}
					{if !empty($positions)}
						<ul class="order-items-list">
							{foreach from=$positions item=cart_item}
								<li class="order-item clearbox">
									<div class="order-item-cover">
										{if !empty($cart_item.image_id)}
											<img src="{$cart_item.image->getUrl(86, 100)}" alt="{$cart_item.title}" />
										{/if}
									</div>
									<div class="order-item-content">
										<h3>{$cart_item.title}</h3>
										<div class="order-item-info">
											<div class="info-inner a-inline-block">
												{if !empty($cart_item.data.color)}<div class="color a-inline-block" style="background-color: #{$cart_item.data.color}"></div>{/if}
												{if !empty($cart_item.data.size)}<div class="size a-inline-block">{$cart_item.data.size}</div>{/if}
											</div>
										</div>
										<div class="order-item-price">
											{?$cart_item_price = $cart_item.count * $cart_item.price}
											{?$total_sum += $cart_item_price}
											<span class="price">{$cart_item_price|price_format} Р</span>
											{if $cart_item.count > 1}
												<span class="count">за {$cart_item.count} шт.</span>
											{/if}
										</div>
									</div>
								</li>
							{/foreach}
						</ul>
						<div class="order-total">
							<div class="total-plank a-inline-block">ИТОГО <span><span>{$order.total_cost|price_format}</span> Р</span></div>
							<div class="descr">Указано без учета стоимости доставки</div>
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
</div>
			
<div class="popup-window popup-map">
	<div class="map"></div>
</div>
			
<div class="popup-window popup-article" data-title="Условия продажи">
	<div class="edited-text"></div>
</div> *}