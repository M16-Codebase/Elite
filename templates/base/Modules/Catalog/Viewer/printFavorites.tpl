{?$currentCatalog = $current_type->getCatalog()}
{?$print=1}
<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="/templates/Modules/Catalog/Viewer/print.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div class="page-main">
			<div class="header">
				<div class="header-title a-left">
					<div class="title">Избранные {$currentCatalog.word_cases['v']['2']['i']}</div>
					<div class="date">Дата распечатки — {time()|date_format:'%d.%m.%Y'}</div>
				</div>
				<div class="header-logo a-right">
					<img src="/img/print/maris-logo.png" />
				</div>
			</div>
			{?$favorites = $account->getFavorites()}
			{if !empty($favorites.items)}
				<ul class="objects-list">
					{foreach from=$favorites.items item=f_data}
					{?$item = $f_data.item}
						<li class="object">
							<div class="object-discr">
								<div class="object-header">
									<img class="background" src="/img/print/bg-grey.png" />
									<div class="object-title a-left">
										<img class="background" src="/img/print/bg-black.png" />
										<span class="title" id="white">{$item.title}</span>
									</div>
									<div class="object-status a-right">
										<img class="background" src="/img/print/bg-grey.png" />
										{if !empty($item.status_object)}
										<span class="title" id="white">
											{if $item.status_object=='Есть устная договоренность'}Устная дог.{elseif $item.status_object=='Гарантийное письмо'}Гар. письмо{elseif $item.status_object=='На согласовании'}Согласование{elseif $item.status_object=='Статус не определен'}Без статуса{else}{$item.status_object}{/if}
											{if !empty($item.razmer_komissii)} — {$item.razmer_komissii}{/if}
										</span>
										{elseif empty($item.status_object)}
											<span class="title" id="white">Без статуса</span>
										{/if}
									</div>
									<div class="a-clear"></div>
								</div>
								<div class="object-body">
									<div class="object-adress">
										<span class="main">{if !empty($item.code)}{$item.code}{/if}
											{if !empty($item.oblast) || !empty($item.city) || !empty($item.district) || !empty($item.rajon) || !empty($item.adres)}<img class="icon i-region" src="/img/print/region.png" />{/if}
											{?$first_prop=true}
											{if !empty($item.oblast)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.oblast}{/if}
											{if !empty($item.rajon)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.rajon}{/if}
											{if !empty($item.city)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.city}{/if}
											{if !empty($item.district)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.district}{/if}
											{if !empty($item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.adres}{/if}
										</span>
										{if !empty($item.properties.stantsii_metro) && $item.properties.stantsii_metro.value && !empty($metro_stations)}
											<div class="metro">
												{?$metro_current = $item.properties.stantsii_metro.value}
												{foreach from=$metro_current item=metro_id}
													{if !empty($metro_stations[$metro_id])}
														{?$metro = $metro_stations[$metro_id]}
														<img class="icon i-metro" src="/img/print/metro.png" /> {$metro.title[$request_segment.id]}{if !empty($item.metro_data[$metro_id].distance)} <span class="num">— {$item.metro_data[$metro_id].distance} м</span>{/if}
													{/if}	
												{/foreach}	
											</div>
										{/if}
									</div>
									<div class="object-ready">
										<span class="main">
											{?$first_prop=true}
											{if !empty($item.status_gotovnosti)}
												{if $item.status_gotovnosti =='Строящийся'}
													Строящийся{if !empty($item.planiruemaja_data_gotovnosti_kvartal)} — {$item.planiruemaja_data_gotovnosti_kvartal}{/if}
													{if !empty($item.planiruemaja_data_gotovnosti)} {$item.planiruemaja_data_gotovnosti}{/if}
													{if !$first_prop}, {else}{?$first_prop=false}{/if}
												{elseif $item.status_gotovnosti =='Готовый'}
													Построен{if !empty($item.god_postrojki)} в {$item.god_postrojki}{/if}
													{if !$first_prop}, {else}{?$first_prop=false}{/if}
												{/if}
												{if !$first_prop}, {else}{?$first_prop=false}{/if}
											{/if}
											{if !empty($item.klass)}
												{if $item.klass =='Без класса'}
													{$item.klass}
												{else}
													Класс {$item.klass}
												{/if}
												{if !$first_prop}, {else}{?$first_prop=false}{/if}
											{/if}
											{if !empty($item['special_variant'])}
												{?$special_offer = $item['special_variant']}
												{?$first_prop=true}
												{if !empty($special_offer.ploschad_ot_offer)}
													{if !$first_prop}, {else}{?$first_prop=false}{/if}Площади:
													{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$special_offer.ploschad_ot_offer value_max=$special_offer.ploschad_do_offer value_range=$special_offer.ploschad_range}
												{/if}
												{if !empty($special_offer.price_min_variant_closed)}{if !$first_prop}, {else}{?$first_prop=false}{/if}
													{foreach from=$f_data.variants item=variant}	
														{?$current_type=array('id' => $variant.type_id)}
														{include file="Admin/components/types_view.tpl"}
													{/foreach}
													
													{if $type_rent==true}Ставка: {elseif $type_sale==true}Цена: {elseif $type_stead==true}Стоимость: {/if}
													{$special_offer.price_range|price_format_range}
													{*include file="Admin/components/view_entity/value_range_view.tpl" value_min=$special_offer.price_min_variant_closed value_max=$special_offer.price_max_variant_closed value_range=$special_offer.price_range*}
												{/if}
											{/if}
										</span>
									</div>
								</div>
							</div>
							<ul class="offers-list">
								{foreach from=$f_data.variants item=variant}	
									{?$current_type=array('id' => $variant.type_id)}
									{include file="Admin/components/types_view.tpl"}
									<li class="offer">
										<div class="offer-header">
											{if empty($ens_search)}
												<div class="a-right">
													{if empty($variant['variant_visible']) || $variant['variant_visible'] == 'Скрыто'}
														<img class="icon i-visible" src="/img/print/hidden.png" />
													{else}
														<img class="icon i-visible" src="/img/print/visible.png" />
													{/if}
												</div>
											{/if}
											<div class="title">{$variant.variant_title}</div>
											<div class="main">{$item.title}{if !empty($item.code)} — {$item.code}{/if}</div>
										</div>
										<table class="offer-table">
											{include file ="Admin/components/view_entity/view_table_properties.tpl"}
											{foreach from=$variant_properties_by_type[$variant.type_id] item=$variant_prop name=variant_props}
												{if $variant_prop.key == 'price_variant' || isset($variant['properties'][$variant_prop.key]['real_value']) && $variant['properties'][$variant_prop.key]['real_value'] != ''}
													{include file ="Admin/components/view_entity/table_properties.tpl" item_prop=$variant_prop catalog_item=$variant  prop_i=false}
												{/if}
											{/foreach}
										</table>
										<div class="notes">
											<img src="http://chart.apis.google.com/chart?cht=qr&chs=60x60&chld=L|0&chl=http://{$smarty.server.SERVER_NAME}{$variant->getUrl()}" title="http://{$smarty.server.SERVER_NAME}{$variant->getUrl()}" />
											<span class="num">Для заметок</span>
										</div>
									</li>
								{/foreach}
							</ul>
						</li>
					{/foreach}
				</ul>
			{/if}
		</div>
	</body>
	<script type="text/javascript">
		window.print();
	</script>
</html>
