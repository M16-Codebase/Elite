{?$no_layout = true}
{?$pageTitle = (($request_segment.id==1)? 'Сравнение товаров — Управляющая компания Maris|CBRE' : 'Comparing objects — Management company Maris|CBRE')}

<div class="page-center compare-page{if count($items) > 2} m-big{/if}">
	<div class="page-title a-clearbox">
		<a href="/" class="header-logo a-left">
			<img src="/img/icons/header-logo.png" alt="Maris logo" />
		</a>
		<div class="header-info">
			{include file="components/breadcrumb.tpl"}
			<div class="a-inline-cont">
				<h1>{if $ru}Сравнение объектов{else}Comparing objects{/if}</h1>
				<div class="header-lang a-inline-cont">
					<a href="/catalog/compare/"{if $ru} class="m-current"{/if}>Ру</a>
					<a href="/en/catalog/compare/"{if !$ru} class="m-current"{/if}>En</a>
				</div>
			</div>
		</div>		
	</div>
	<div class="">
		<div class="compare-top">
			<div class="objects"><i></i>{if $ru}Объекты{else}Objects{/if} <span>{count($items)}</span></div>
			{if count($items)}
				<a href="{$url_prefix}/feedback/request/" class="make-order">{if $ru}Оставить заявку на отмеченные предложения{else}Submit a request for proposal marked{/if} — <span class="big-btn"><i></i></span></a>
			{/if}
		</div>
		{if count($items)}
			<div class="compare-wrap">
				<table class="compare-table">
					<tr class="tr-header">
						<td>
							{*<div class="rel-links print-link">
								<a href="#"><i></i> Версия для печати</a>
							</div>*}
							<div class="rel-links remove-link">
								<a href="#" class="clear-compare a-pink"><i></i> {if $ru}Очистить список{else}Clear List{/if}</a>
							</div>
						</td>
						{foreach from=$items item=item_group}
							{?$item = $item_group.item}
							{?$special_offer = $item['special_variant']}
							<td rowspan="2" class="i{$item.id}">
								<a class="remove-item del-compare-item" href="#" data-id="{$item.id}" title="{if $ru}Удалить{else}Remove{/if}"><i></i></a>
								<div class="item-info">
									{?$cover = $item.gallery->getCover()}
									{if empty($cover)}
										{?$cover = $item.gallery->getDefault()}
									{/if}
									{if !empty($cover)}
										<a href="{$item->getUrl()}" class="cover">
											<img src="{$cover->getUrl(204,138,true)}" alt="{$item.title}" />
										</a>
									{/if}
									<a href="{$item->getUrl()}" class="item-title more-arrow a-block">{$item.title}</a>
									<div class="item-info">
										<span class="main">										
											{if in_array($item.type_id, array('63', '65', '66'))}
												{$lang->get('Аренда', 'Rent')}
											{else}
												{$lang->get('Продажа', 'Sale')}
											{/if}
										</span>
										{if $item.type_id == 62}
											{if !empty($item.kolichestvo_spalen)}
												&nbsp;•&nbsp; <strong>
													{if $ru}
														{$item.kolichestvo_spalen|plural_form:'спальня':'спальни':'спален'}
													{else}
														{$item.kolichestvo_spalen|plural_form:'bedroom':'bedrooms':'bedrooms'}
													{/if}
												</strong>
											{/if}
											&nbsp;•&nbsp; <strong><i class="i-wallet"></i> 
											{if !empty($special_offer) && !empty($special_offer.foreign_price)}	
												{if !empty($special_offer.foreign_price)}
													{if !empty($special_offer.foreign_currency)}{$special_offer.foreign_currency} {/if}{$special_offer.foreign_price|price_format}
												{/if}
											{else}
												{$lang->get('Цена договорная', 'Negotiated price')}
											{/if}
										{else}
											{if !empty($special_offer.ploschad_ot_offer)}
												&nbsp;•&nbsp; <strong>{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$special_offer.ploschad_ot_offer value_max=$special_offer.ploschad_do_offer value_range=$special_offer.ploschad_range}</strong> 
											{/if}
											&nbsp;•&nbsp; <strong><i class="i-wallet"></i> 
											{if !empty($item.price_open_min)} 
												{$item.price_open_range|price_format_range}
											{elseif !empty($special_offer.price_variant)}
												{$special_offer.price_variant}
											{else}
												{$lang->get('Цена договорная', 'Negotiated price')}
											{/if}</strong>
										{/if}
									</div>
									<div class="item-location">
										<div class="small-descr">
											{?$first_prop=true}
											{if !empty($item.city)}{?$first_prop=false}{$item.city}{/if}
											{if !empty($item.district)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.district}{/if}
										</div>
										<div class="item-adress">{if !empty($item.adres)}{$item.adres}{/if}</div>
										{if !empty($item.properties.stantsii_metro.value) && !empty($metro_stations)}
											<ul class="item-distance">
												{foreach from=$item.properties.stantsii_metro.value item=metro_id}
													{if !empty($metro_stations[$metro_id])}
														{?$metro = $metro_stations[$metro_id]}
														<li><i class="i-metro line-{$metro.line_id}"></i>{$metro.title[$request_segment.id]}{if !empty($item.metro_data[$metro_id].distance)} <span class="small-descr">— {$item.metro_data[$metro_id].distance} м</span>{/if}</li>
													{/if}
												{/foreach}
											</ul>
										{/if}
									</div>
								</div>
							</td>
						{/foreach}					
					</tr>
					{include file="Modules/Catalog/Main/compareTable.tpl"}
					{if $item.type_id != 61 && $item.type_id != 62}
						{include file="Modules/Catalog/Main/compareVariants.tpl"}
					{else}
						<tr class="last-row">
							<td></td>
							{section loop=count($items) name=compare_loop}
								<td></td>
							{/section}
						</tr>
					{/if}
				</table>
			</div>
		{else}
			<div class="compare-wrap empty-compare">
				{if $ru}В сравнении еще нет объектов.{else}For comparison there are no objects.{/if}
			</div>
		{/if}
	</div>
	<footer class="page-footer">
		<div class="footer-top justify">
			<div class="footer-contacts column">
				<div class="phone h3">{$site_config.phone}</div>
				<div class="callback"><a href="#" class="a-pink">{if $ru}Заказать обратный звонок{else}Request a Callback{/if}</a></div>
				<div class="email">E-mail: <a href="mailto:{$site_config.service}">{$site_config.service}</a></div>
			</div>
			<div class="footer-contacts-big">
				<div class="address">{?$postfix = ($request_segment.key=='ru')? '': '_' . $request_segment.key}{$site_config['address' . $postfix]}</div>
				<div class="contacts-link"><a href="#" class="a-pink">{if $ru}Вся контактная информация{else}All contact information{/if}</a></div>
			</div>			
			<div class="footer-sites justify column">
				<div>
					<div class="title">CBRE {if $ru}Россия{else}Russia{/if}</div>
					<a href="//www.cbre.ru" target="_blank">www.cbre.ru</a>
				</div>
				<div>
					<div class="title">CBRE Global</div>
					<a href="//www.cbre.com" target="_blank">www.cbre.com</a>
				</div>
			</div>
		</div>
		<div class="footer-bottom justify">
			<div class="copyright">
				<a href="/" class="footer-logo a-left"><img src="/img/icons/footer-logo.png" alt="maris logo" /></a>
				<div>
					© 2013 Maris | Part of the CBRE Affiliate Network
					{if $ru}
						<p>Использование материалов разрешено только с предварительного согласия правообладателей.<br />Предложение не является публичной офертой.</p>
					{else}
						<p>Using materials are allowed only with prior consent of the copyright holders.<br /> Offer does not constitute a public offer.</p>
					{/if}
				</div>
			</div>			
			<div class="developer column">
				{if $ru}Сайт сделан в{else}Website developed by{/if}
				<a href="http://webactives.ru/" target="_blank">Active Internet Solutions</a>
			</div>
		</div>
	</footer>              
</div>