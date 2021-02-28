{include file="components/order-status.tpl"}

<div class="cabinet-page justify">
	<aside class="aside-catalog-menu aside-col m-scrollable">
		<div class="white-block-inner content-white-block vert-menu">
			{*<div class="aside-catalog-select">*}
				{*{include file="components/catalog-menu.tpl"}*}
			{*</div>*}
			{include file="Modules/Profile/My/cabinet-menu.tpl"}
		</div>
		{*<div class="white-block-inner benefits-cont content-white-block">*}
			{*{include file="components/benefits.tpl"}*}
		{*</div>*}
	</aside>
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{include file="components/breadcrumb.tpl" other_link=array('Личный кабинет' => array('link'=>'/profile/',  'Личные данные' => '/profile/', 'Заказы' => '/profile/orders/', 'Бонусный счет' => '/profile/bonus/', 'Отзывы' => '/profile/reviews/'))}
				<h1>ЗАКАЗЫ</h1>
				<form class="order-form" method="GET">
					<div class="grey-block justify">
						<div class="col1">
							<div class="field f-data a-inline-block">
								<div class="f-title">Даты заказа</div>
								<div class="f-input">
									<input type="text" name="date_from" class="datepicker" />—<input type="text" name="date_to" class="datepicker" />
								</div>
							</div>
							<div class="field f-number-item a-inline-block">
								<div class="f-title">Номенклатурный номер товара</div>
								<div class="f-input">
									<input type="text" name="code" />
								</div>
							</div>
						</div>
						<div class="form-left col2">
							<div class="justify">
								<div class="field f-number-order a-inline-block col1">
									<div class="f-title">Номер заказа</div>
									<div class="f-input">
										<input type="text" name="id" />
									</div>
								</div>
								<div class="field f-status a-inline-block col1">
									<div class="f-title">Статус заказа</div>
									<div class="f-input">
										<div class="status-select">
											<select name="status" class="chosen fullwidth">
												<option value="">Все</option>
												{foreach from=$status_titles key=s_key item=s_title}
													<option value="{$s_key}">{$s_title}</option>
												{/foreach}
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="field f-name a-inline-block">
								<div class="f-title">Наименование товара</div>
								<div class="f-input">
									<input type="text" name="position_title" />
								</div>
							</div>
						</div>
					</div>
					<div class="btn-cont">
						<div class="orders-btn a-inline-block">
							<button class="btn btn-white-yellow-big">Найти</button>
						</div>
					</div>
				</form>			
			</div>
			{if !empty($orders)}	
				<div class="white-block-bay">
					<div class="variants-switcher m-short a-inline-cont">
						<div class="switch-showing a-link m-short m-current"><i class="icon i-list"></i>Кратко</div>
						<div class="switcher"><i></i></div>
						<div class="switch-showing a-link m-full"><i class="icon i-list"></i>Подробно</div> 
					</div>
				</div>
			{/if}	
			<div class="grey-block-inner">
				{if !empty($orders)}
					<ul class="variants-list item-tab-list">						
						{foreach from=$orders item=order}
							{?$order_status = 'processed'}
							{if !empty($status_titles[$order.status])}
								{?$order_status = $order.status}								
							{/if}
							<li class="variant item-tab-listitem m-short">
								<div class="var-header justify">
									<div class="item-specs-col">
										{?$order_date=$order.date}
										{if !empty($order_date)}
											<div class="main">{$order.timestamp|date_format_lang:'%d %B %Y':'ru'}</div>
										{/if}
										<div class="order-title">Заказ №{$order.id}</div>
										<div class="review-status {$order_status} a-inline-block">{$status_titles[$order_status]}</div>
										{if $order_status == 'delay'}&nbsp;<a href="#" class="descr undelay-order" data-id="{$order.id}">—Возобновить заказ</a>{/if}
									</div>									
									<div class="variant-info item-specs-col">
										{if !empty($order.positions)}
											{foreach from=$order.positions item=$p name=order_positions}
												{if iteration > 1}, {/if}
												{$p.title} — {$p.count} {if !empty($p.data.unit)}{$p.data.unit}{else}шт.{/if}
											{/foreach}
										{/if}
										. <span class="change-size a-link" data-size="full">Подробно...</span>
									</div> 
								</div>
								<div class="var-specs">
									{if !empty($order.positions)}
										<table class="cart-items">
											{foreach from=$order.positions item=$p name=order_positions}
												{?$v = $p.entity}
												<tr class="item" data-id="{$p.id}">
													<td>
														{?$cover = $p['image']}
														{if !empty($cover)}
															<a href="{$v->getUrl()}">
																<img class="item-img" src="{$cover->getUrl(129, 115)}" alt="{$p.title}" />
															</a>
														{/if}
													</td>
													<td>
														<a href="{$v->getUrl()}">
															<span class="title">{$v.variant_title}</span>
														</a>
														<span class="item-number">{$v.code}</span>
													</td>
													<td>
														<span class="item-price one-price" data-price="{$v.price_variant}">
															<span class="num">
																{if !empty($v.old_price_variant)}<span class="old-price"><i></i>{($v.old_price_variant*$p.count)|price_format}</span>{/if}
																{($v.price_variant * $p.count)|price_format}
															</span>
															<span class="descr">руб. за {$p.count} {if !empty($p.data.unit)}{$p.data.unit}{else}шт.{/if}</span>
														</span>
													</td>
												</tr>
											{/foreach}											
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
											{if !empty($order.bonus_count)}
												<tr>
													<td colspan="2">Бонусные баллы, потраченные на оплату заказа</td>
													<td>
														<span class="num">{$order.bonus_count}</span><span class="descr"> {$order.bonus_count|plural_form:'балл':'балла':'баллов':false}</span>
													</td>
												</tr>												
											{/if}
										</table>
									{/if}		
									<span class="change-size a-link td" data-size="short">Кратко...</span>
								</div>
								<div class="var-footer justify">
									<div class="item-specs-col">
										<div class="order-price">
											<span class="num">{$order.total_cost|price_format}</span> руб.
										</div>
										<div class="descr">без учета <a href="/discount/">промо-акций</a></div>
									</div>
									<div class="item-specs-col">
										<div class="a-right">
											{?$current_user = $account->getUser()}
											{if $current_user.person_type == 'org' && $order.status == 'processed'}
												<a href="#" class="btn btn-grey-blue">Счет</a>
											{/if}
										</div>
										<p class="descr">
											Доставка — 
											{if $order.delivery_type_self == 1}
												Самовывоз (бесплатно)
											{elseif $order.delivery_type_courier}
												Курьером по Петербургу{if !empty($order.delivery_price)} ({$order.delivery_price} руб.){else} (бесплатно){/if}
											{elseif $order.delivery_type_company}
												{if !empty($company_tiles) && !empty($company_tiles[$order.delivery_type_company])}
													{$company_tiles[$order.delivery_type_company]}
												{else}	
													Транспортной компанией
												{/if}
												{if !empty($order.delivery_price)} ({$order.delivery_price} руб.){/if}
											{else}
												Способ еще не выбран
											{/if}<br />
											{if !empty($order.pay_type)}
												Оплата заказа {$paytype_text[$order.pay_type]}
											{else}
												Оплата — Способ еще не выбран
											{/if}
										</p>
									</div>
								</div>
							</li>
						{/foreach}
					</ul>
				{else}
					<div class="empty-result main">
						Заказов по запросу не найдено.
					</div>
				{/if}
			</div>
		</div>
	</div>		
</div>
{*{include file="components/brands.tpl"}*}
{*{include file="components/news-block.tpl"}*}