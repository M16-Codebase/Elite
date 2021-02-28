{*?$pageTitle = ''*}
{?$pageDescription = ''}
{?$admin_link = '/order-admin/'}

<div class="cart-page content-white-block">
	{include file="/Modules/Order/Main/cartList.tpl"}
</div>	
{include file="/components/bonus-program.tpl"}
{if !empty($order.positions)}
	<div class="cart-catalog-select">
		<div class="descr">Продолжить покупки — </div>
		{include file="components/catalog-menu.tpl"}	
	</div>
{/if}


{*
<div class="main-content">
	<div class="page-center clearbox">
		<div class="cart-top">
			{if !empty($order)}
				<div class="cart-items-cont">
					{include file="Modules/Order/Main/cartItems.tpl"}
				</div>
				<div class="cart-total">
					<div class="to-shop a-inline-block">
						<a href="/catalog/">В магазин</a>
					</div>
					<div class="total-price a-inline-block">
						<div class="total-plank a-inline-block">ИТОГО К ОПЛАТЕ <span><span class="total-sum">{$total_sum|price_format}</span> Р</span></div>
					</div>
					<div class="make-order a-inline-block">
						<a href="/order/form/" class="a-button-red b-big">оформить заказ <span class="num">№{$order.id}</span><span class="arrow"></span></a>
						<div class="descr">
							Также вы можете оформить заказ 
							<div class="phone-help a-inline-block">
								<span class="a-link-dotted">по телефону</span>
								<div class="popup">
									<div class="title">Не хотите возиться с оформлением?</div>
									<p>Позвоните нам по телефону {$site_config.phone} <span class="lowercase">{$site_config.phone_time}</span> и назовите номер заказа, который вы видете чуть выше в рамочке и наш консультант оформит все за вас.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			{else}
				<div class="empty-list">
					В корзине ничего нет
				</div>
			{/if}
		</div>
		
		<div class="cart-bottom">
			<ul class="cart-delivery justify">
				<li>
					<div class="marker m-free"></div>
					<div class="icon m-self"></div>
					<div class="content">
						<div class="title">Самовывоз из магазина</div>
						<p>Вы можете <span>бесплатно</span> забрать заказ в любом из наших магазинов уже сегодня</p>
					</div>				
				</li>
				<li>
					<div class="marker m-free"></div>
					<div class="icon m-spb"></div>
					<div class="content">
						<div class="title">Курьер по Петербургу</div>
						<p>Мы доставим ваш заказ <span>бесплатно</span> до дверей вашего дома уже завтра</p>
					</div>				
				</li>
				<li>
					<div class="icon m-russia"></div>
					<div class="content">
						<div class="title">Доставка по России</div>
						<p>По тарифам транспортных компаний (менеджер уточнит во время звонка)</p>
					</div>				
				</li>
			</ul>

			<div class="cart-paytypes">
				<a href="/main/payment/"></a>
			</div>
			
			{if !empty($post)}
				<div class="bordered-block edited-text">
					<h1>{$post.title}</h1>
					{if !empty($post.annotation)}
						<p class="main a-left">{$post.annotation}</p>
					{/if}
					{$post.text|html}
				</div>
			{/if}
		</div>	
		
	</div>		
</div>*}
