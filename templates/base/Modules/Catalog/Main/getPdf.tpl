{include file="Admin/components/types_view.tpl"}
{?$ru = $request_segment.key == 'ru'}
{?$url_prefix = !$ru ? ('/' . $request_segment.key) : ''}
{?$path = "http://" . $smarty.server['SERVER_NAME']}
{?$tmp_path = $path . "/templates"}

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>TEST</title>
		<style>
			{fetch assign=pdf_css file= $tmp_path . "/Modules/Catalog/Main/getPdf.css"}
			{$pdf_css|replace:'#path':$tmp_path|html}
		</style>
	</head>
	<body>
		<div class="page-wrap">
			<div class="print-header a-clearbox">
				<div class="header-logo a-left">
					<img src="{$tmp_path}/img/print/print-small-logo.png" />
				</div>
				<div class="header-title a-left">
					<div class="title">{if $ru}Управляющая компания Maris{else}Management company Maris{/if}</div>
					<div class="descr">{if $ru}В ассоциации С CBRE{else}In Association With CBRE{/if}</div>
				</div>
				<div class="header-contacts a-right">
					<div class="descr">{if $ru}Звоните{else}Call{/if}</div>
					<div class="title">{$site_config.phone}</div>
				</div>
			</div>
			<div class="print-body">
				<div class="item-top">
					<h1>{$catalog_item.title}</h1>
					{if !empty($catalog_item.post.annotation)}
						<div class="item-annotation">{$catalog_item.post.annotation}</div>
					{/if}
					<div class="item-info a-clearbox justify">
						<div class="small-col a-right"><span class="main">{if $ru}Код объекта{else}Code{/if} — {$catalog_item.code}</span></div>
						<div class="big-col a-left">
							<span class="main">{if $type_rent}{if $ru}АРЕНДА{else}RENT{/if}{else}{if $ru}ПРОДАЖА{else}SALE{/if}{/if}</span>
							{if !$type_apartments}
								{if !empty($catalog_item.klass)}
									 • <strong>{if $ru}Класс{else}Class{/if} {$catalog_item.klass}</strong>
								{/if}
							{else}
								{if !empty($catalog_item.kolichestvo_spalen)}
									 • <strong>{if $ru}{$catalog_item.kolichestvo_spalen|plural_form:'спальня':'спальни':'спален'}{else}{$catalog_item.kolichestvo_spalen|plural_form:'bedroom':'bedrooms':'bedrooms'}{/if}</strong>
								{/if}
								{if !empty($catalog_item['special_variant'])}
									{?$special_offer = $catalog_item['special_variant']}
									 • <strong>
										{if !empty($special_offer.foreign_price)}
											{if !empty($special_offer.foreign_currency)}{$special_offer.foreign_currency} {/if}{$special_offer.foreign_price|price_format}
										{else}
											{if $request_segment.id==1}Цена договорная{else}Negotiated price{/if}
										{/if}
									</strong>
								{/if}
							{/if}
							{if !empty($catalog_item['special_variant']) && ($type_investment!=true && $type_apartments!=true)}
								{?$special_offer = $catalog_item['special_variant']}
								{if !empty($special_offer.ploschad_ot_offer)}
									 • <strong>
										{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$special_offer.ploschad_ot_offer value_max=$special_offer.ploschad_do_offer value_range=$special_offer.ploschad_range}
									</strong>
								{/if}
							{/if}
							{if !empty($catalog_item.price_open_min)}
								 • <strong>{$catalog_item.price_open_range|price_format_range}</strong>
							{/if}
						</div>
					</div>
					{if !$type_apartments}
						<div class="item-info">
							{if !empty($catalog_item.adres) || !empty($catalog_item.city)}
								{?$first_prop=true}
								<div class="item-add">
									{if !empty($catalog_item.city)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.city}{/if}
									{if !empty($catalog_item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.adres}{/if}
								</div>
							{/if}
							{if !empty($catalog_item.properties.stantsii_metro) && $catalog_item.properties.stantsii_metro.value && !empty($metro_stations)}
								<div class="metro">
									{foreach from=$catalog_item.properties.stantsii_metro.value item=metro_id}
										{if !empty($metro_stations[$metro_id])}
										{?$metro = $metro_stations[$metro_id]}
											<span>
												<img class="i-metro" src="{$tmp_path}/img/icons/pdf-metro.png" />
												{$metro.title[$request_segment.id]}
												{if !empty($catalog_item.metro_data[$metro_id].time) || !empty($catalog_item.metro_data[$metro_id].transport)}
													<span class="small-descr">
														~ {$catalog_item.metro_data[$metro_id].time}
														{if $ru} мин {if $type_stead==true}езды{else}пешком{/if}
														{else} min {if $type_stead==true}drive{else}walk{/if}{/if}
														{if !empty($catalog_item.metro_data[$metro_id].transport)}<img class="i-transport" src="{$tmp_path}/img/print/transport.png" />{/if}
													</span>
												{/if}
											</span>
										{/if}
									{/foreach}
								</div>
							{/if}
						</div>
					{/if}
				</div>
				<div class="item-gallery">
					{if !empty($catalog_item.gallery)}
						{?$images = $catalog_item.gallery->getImages()}
						<div class="item-gallery-list">
							{foreach from=$images item=img name=object_images}
								{if iteration < 4}
									<div><img src="{$path}{$img->getUrl(243,175,true)}"></div>
								{/if}
							{/foreach}
						</div>
					{/if}
					{if !$type_apartments && !empty($catalog_item.uslugi_maris) && !empty($maris_services)}
						{foreach from=$catalog_item.uslugi_maris item=servise key=serv_id}
							{if $servise == 'm'}
								<div class="object-service">
									<img class="service" src="{$tmp_path}/img/icons/pdf-service.png" />
									<div class="object-service-title">
										{if $ru}
											Данный объект находится под управлением Maris
										{else}
											This facility is operated by Maris
										{/if}
									</div>
								</div>
							{/if}
						{/foreach}
					{/if}
				</div>
				<div class="item-contacts a-clearbox">
					<div class="a-right">
						<div class="contact-title">{if $ru}Контакты Maris{else}Contact Maris{/if}</div>
						<div class="contact-info"><i>T</i>{$site_config.phone}</div>
						<div class="contact-info"><i>Е</i>{$site_config.service}</div>
					</div>
					<div class="contact-name">
						{if !empty($catalog_item.kontaktnoe_litso) || !empty($current_type.user)}
							{if !empty($catalog_item.kontaktnoe_litso)}
								{?$curator = $catalog_item.kontaktnoe_litso}
								{?$curator_cover = $curator['image']}
							{else}
								{if !empty($current_type.user)}{?$curator = $current_type.user}{/if}
							{/if}
							<div class="contact-title">
								{if !empty($catalog_item.kontaktnoe_litso)}
									{if $ru}Контактное лицо{else}Contact person{/if}
								{else}
									{if $ru}Контакты{else}Contacts{/if}
								{/if}
							</div>
							<div class="contact-info">
								<strong>
									{if !empty($curator.name)}{$curator.name} {/if}
									{if !empty($curator.surname)}{$curator.surname}{/if}
								</strong>
								{if !empty($curator.function)}
									, <span>{$curator.function}</span>
								{/if}
							</div>
							<div class="contact-info">
								{if !empty($curator.phone)}
									<i>Т</i>{$curator.phone} 
								{/if}
								{if !empty($curator.email)}
									<i>Е</i>{$curator.email}
								{/if}
							</div>
						{/if}
					</div>
				</div>
				{if !$type_apartments  && !$type_investment}
					{?$offers = $catalog_item->searchVariants()}
					{if !empty($offers)}
						<div class="offers-cont">
							<ul class="offers-list">
								{foreach from=$offers item=offer name=object_offers}
									{if $offer.properties.status_offer.value!=$constants.variant_status_special_value}
										<li class="offer">
											<div class="title">{$offer.variant_title}</div>
											<div class="item-info">
												<span class="main">{if $type_rent}{if $ru}АРЕНДА{else}RENT{/if}{else}{if $ru}ПРОДАЖА{else}SALE{/if}{/if}</span> 
												{if !empty($offer.ploschad_ot_offer)}
													 •  <strong>{include file="Admin/components/view_entity/value_range_view.tpl" value_range=$offer.ploschad_range value_max=$offer.ploschad_do_offer value_min=$offer.ploschad_ot_offer}</strong> 
												{/if}											
												 •  <strong>
													<img class="i-wallet" src="{$tmp_path}/img/print/wallet.png" />
													{if !empty($offer.price_variant)}
														{$offer.price_variant}
													{else}
														{if $ru} Цена договорная{else} Negotiated price{/if}
													{/if}
												</strong>
												{if !empty($offer.bez_komissii_offer) && ($offer.bez_komissii_offer=='Да' || $offer.bez_komissii_offer=='Yes')}
													 •  <strong><span class="pink">{if $ru}Без комиссии{else}No commission{/if}</span></strong> 
												{/if}
											</div>
											{?$images = $offer.gallery->getImages()}
											{if count($images)}												
												<div class="offer-gallery">
													{foreach from=$images item=img name=offer_images}
														{if iteration < 4}
															<div><img src="{$path}{$img->getUrl(243,175,true)}"></div>
														{/if}
													{/foreach}
												</div>
											{/if}
											{if !empty($offer.properties) && !empty($variant_properties_full)}
											{?$print=1}
											{?$printPdf=1}
												<table class="show-table">
													{include file ="Admin/components/view_entity/view_table_properties.tpl"}
													{foreach from=$variant_properties_full item=$offer_prop name=offer_props}
														{if $offer_prop.key == 'price_variant' || isset($offer['properties'][$offer_prop.key]['real_value']) && $offer['properties'][$offer_prop.key]['real_value'] != ''}
															{include file ="Modules/Catalog/Main/print_table_properties.tpl" item_prop=$offer_prop catalog_item=$offer  prop_i=false}
														{/if}
													{/foreach}
												</table>
											{/if}
										</li>
									{/if}
								{/foreach}								
							</ul>
						</div>
					{/if}
				{else}
					<div class="offers-cont border-middle"></div>
				{/if}
				{if !empty($type_properties)}
					<div class="item-properties">					
						{?$end_table = false}
						{?$group = 0}
						{?$current_group = 0}
						{?$prop_i = 0}
						{?$print=1}
						{?$printPdf=1}
						{include file ="Admin/components/view_entity/view_table_properties.tpl"}
						{foreach from=$type_properties item=$item_prop name=item_props}
							{if $item_prop.group_id != 4 && $item_prop.group_id != 3 && !empty($item_prop.group_id)}
								{if (isset($catalog_item['properties'][$item_prop.key]['real_value']) && $catalog_item['properties'][$item_prop.key]['real_value'] != '') ||
									(isset($catalog_item['special_variant']['properties'][$item_prop.key]['real_value']) && $catalog_item['special_variant']['properties'][$item_prop.key]['real_value'] != '')}
									{?$end_table = true}
									{?$current_group = $item_prop.group_id}
									{if $group != $current_group}
										{if $group != 0}						
											{?$prop_i = 0}
											</table>										
											{if !empty($catalog_item['group_comments'][$group]) && !empty($catalog_item['group_comments'][$group][$request_segment.id])}
												<div class="comment"><img class="icon i-comment" src="{$tmp_path}/img/print/comment.png" />{$catalog_item['group_comments'][$group][$request_segment.id]}</div>
											{/if}
										{/if}
										<h2 class="prop-header">{$item_prop.group.title}</h2>
										<table class="show-table">
									{/if}
									{include file ="Modules/Catalog/Main/print_table_properties.tpl"}
									{?$group = $item_prop.group_id}
								{/if}	
							{/if}
						{/foreach}
						{if $end_table = true}
							</table>
						{/if}
					</div>
				{/if}				
				{if !empty($catalog_item.post.text)}
					<div class="print-post">
						{if !empty($catalog_item.title)}
							<h1>{$catalog_item.title}</h1>
						{/if}
						{if !empty($catalog_item.post.annotation)}
							<div class="annotation">{$catalog_item.post.annotation}</div>
						{/if}
						<div class="text">{$catalog_item.post.text|html}</div>
					</div>
				{/if}
			</div>
			<div class="print-qr">
				<img src="http://chart.apis.google.com/chart?cht=qr&amp;chs=142x142&amp;chld=L|0&amp;chl=http://{$smarty.server['SERVER_NAME']}{$catalog_item->getUrl()}" alt="maris qr" />
				<div class="qr-info">
					{if $ru}
						Отсканируйте этот QR-код и описание объекта автоматически откроется на вашем мобильном устройстве. Некоторые устройства могут потребовать специального ПО.
					{else}
						Scan this QR-code and description of the object automatically opens on your mobile device. Some devices may require special software.
					{/if}
				</div>
			</div>
			<div class="print-cbre"><img src="{$tmp_path}/img/icons/pdf-cbre.png"></div>
			<div class="print-footer justify">
				<div class="copyright">
					<div class="copyright-info">
						© 2013 Maris | Part of the CBRE Affiliate Network
						<p>
							{if $ru}
								Использование материалов разрешено только с предварительного<br /> согласия правообладателей. Предложение не является публичной офертой.
							{else}
								Using materials are allowed only with prior consent of the copyright holders. Offer does not constitute<br /> a public offer.
							{/if}
						</p>
					</div>
				</div>
			</div>
		</div>
		{if isset($smarty.get.test)}				
			<div class="a-hidden">{$nope}</div>
		{/if}
	</body>
</html>