<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="/templates/Modules/Catalog/Viewer/print.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div class="page-main contact-page">
			<div class="header">
				<div class="header-title a-left">
					<div class="title">Контакты контрагентов</div>
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
									<img class="icon i-object" src="/img/print/object-icon.png" />
									<div class="title">{$item.title}</div>
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
									{if !empty($item.kontakty_kontragenta)}<div class="contacts"><b>{$item.kontakty_kontragenta}</b></div>{/if}
									{if !empty($item.dop_kontakty_kontragenta)}<div class="contacts">{$item.dop_kontakty_kontragenta}</div>{/if}
									<div class="notes">
										<img src="http://chart.apis.google.com/chart?cht=qr&chs=60x60&chld=L|0&chl=http://{$smarty.server.SERVER_NAME}{$item->getUrl()}" title="http://{$smarty.server.SERVER_NAME}{$item->getUrl()}" />
										<span class="num">Для заметок</span>
									</div>
								</div>
							</div>
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
