{?$path = "http://" . $site_url . '/'}
{?$tmp_path = $path . "templates/"}

{?$floor = $item->getParent()}
{?$corpus = $floor->getParent()}
{?$complex = $corpus->getParent()}
{?$delim = ldelim . "!" . rdelim}
{?$title_arr = $delim|explode:$complex.title}
{?$title = $complex.title|replace:$delim:' '}

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style>
		{fetch assign=pdf_css file= $tmp_path . "project/Modules/Catalog/RealEstate/apartmentPdf.css"}
		{$pdf_css|replace:'{$tmp_path}':$tmp_path|html}
	</style>
</head>

<body>
	{if !empty($item)}
		<div class="page m-first">
			<a href="{$path}" class="pdf-header">
				<span>{$lang->get('Агентство Недвижимости', 'Vyacheslav Malafeyev')}</span>
				<img src="{$tmp_path}project/img/pdf-logo.png" alt="m16">
				<span>{$lang->get('Вячеслава Малафеева', 'Real Estate Agency')}</span>
			</a>
			<div class="pdf-item">
				<div class="row-top{if !empty($item.special_offer) && !empty($item.special_offer.comment)} m-noborder{/if}">
					<table>
						<tr>
							<td class="col1">
								<div class="title">
									{!empty($corpus.title) ? ($lang->get('Корпус', 'Building') . ' ' . $corpus.title) : ''}{if !empty($bed_number[0]) && !empty($bed_number[1])} / {$bed_number[0]} {$bed_number[1]}{/if}{if !empty($item.area_all)} / {$item.area_all|replace:'²':'<sup>2</sup>'|html}{/if}
								</div>
							</td>
							<td class="price-wrap col2">
								<table>
									<tr>
										{if !empty($item.price)}
											<td>
												<div class="price">{$item.price}</div>
												<div class="price_per_m">
													{if !empty($item.properties.area_all.value)}{($item.price/$item.properties.area_all.value*1000)|ceil} {$lang->get('тыс. руб. за м','ths rub. per m')}<sup>2</sup>{/if}
												</div>
											</td>
											<td class="border">/</td>
											<td class="m-last">
												<div class="price">0%</div>
												<div class="price_per_m">{$lang->get('комиссия', 'commission')}</div>
											</td>
										{else}
											<td><div class="price">{$lang->get('Цена по запросу', 'Price on request')}</div></td>
										{/if}
									</tr>
								</table>
							</td>
						</tr>
					</table>
					{if !empty($item.special_offer) && !empty($item.special_offer.comment)}
						<table class="pdf-special">
							<tr>
								<td class="special-main">{$lang->get('Акция', 'Promo')}</td>
								<td class="special-text">{$item.special_offer.comment}</td>
							</tr>
						</table>
					{/if}
				</div>
				<div class="row-bottom">
					<table>
						<tr>
							<td class="col1">
								<div class="item-cover">
									{?$scheme = !empty($item.shemes) ? $item.shemes->getCover() : null}
									{if !empty($scheme)}
										<img class="img" src="http://{$site_url}{$scheme->getUrl(450,330,false)}" alt="">
									{/if}
								</div>
								{if !empty($complex.consultant)}
									{foreach from=$complex.consultant item=cons name=cons}
										{?$consultant = $cons}
										{break}
									{/foreach}
								{elseif !empty($site_config.resale_consultant)}
									{if $site_config.properties.resale_consultant.set == 1}
										{foreach from=$site_config.resale_consultant item=cons name=cons}
											{?$consultant = $cons}
											{break}
										{/foreach}
									{else}
										{?$consultant = $site_config.resale_consultant}
									{/if}
								{/if}
								{if !empty($consultant)}
									<div class="consultant">
										<table class="person">
											<tr>
												{if !empty($consultant.photo)}
													<td class="photo">
														<div><img src="http://{$site_url}{$consultant.photo->getUrl(124,124,true)}" alt=""></div>
													</td>
												{/if}
												<td class="text">
													{if !empty($consultant.name)}<div class="name">{$consultant.name} {$consultant.surname}</div>{/if}
													{if !empty($consultant.phone)}
														<span class="phone">{$consultant.phone}</span>
													{/if}
													{if !empty($consultant.phone) && !empty($consultant.email)}<span> / </span>{/if}
													{if !empty($consultant.email)}
														<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>
													{/if}
													{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
												</td>
											</tr>
										</table>
									</div>
								{/if}
							</td>
							<td class="small-schemes col2">
								{if !empty($item.apartment_overlay)}
									<div class="parent-scheme">
										<div class="main">{$floor.title} {$lang->get('этаж', 'floor')}</div>
										<div class="sch-cover">
											<img src="http://{$site_url}{$item.apartment_overlay}" alt="" />
										</div>
									</div>
								{/if}
								{if !empty($item.building_overlay)}
									<div class="parent-scheme">
										<div class="main">{$lang->get('Корпус', 'Building')} {$corpus.title}</div>
										<div class="sch-cover">
											<img src="http://{$site_url}{$item.building_overlay}" alt="" />
										</div>
									</div>
								{/if}
							</td>
							<td class="col3">
								{if !empty($item.features)}
									<div class="item-features">
										{foreach from=$item.features item=feature name=features}
											{if iteration>1} / {/if}<span>{$feature}</span>
										{/foreach}
									</div>
								{/if}
								{if !empty($type_properties)}
									<table class="opt-table">
										{foreach from=$type_properties item=prop}
											{if !empty($item[$prop.key])}
												{?$prop_val = ''}
												{if $prop.set == 1}
													{foreach from=$item[$prop.key] item=val}
														{if !empty($val['title']) || !empty($val['variant_title'])}
															{if !empty($prop_val)}{?$prop_val .= ', '}{/if}
															{?$prop_val .= !empty($val['variant_title'])? $val['variant_title'] : $val['title']}
														{elseif !is_array($val) && !is_object($val)}
															{if !empty($prop_val)}{?$prop_val .= ', '}{/if}
															{?$prop_val .= $val}
														{/if}
													{/foreach}
												{elseif !empty($item[$prop.key]['title']) || !empty($item[$prop.key]['variant_title'])}
													{?$prop_val = !empty($item[$prop.key]['variant_title'])? $item[$prop.key]['variant_title'] : $item[$prop.key]['title']}
												{elseif !is_array($item[$prop.key]) && !is_object($item[$prop.key])}
													{?$prop_val = $item[$prop.key]}
												{/if}
												{if !empty($prop_val)}
													<tr class="opt-row">
														<td class="opt-col">{$prop.title}</td>
														<td class="opt-col m-right">{$prop_val|replace:'²':'<sup>2</sup>'|html}</td>
													</tr>
												{/if}
											{/if}		
										{/foreach}
									</table>
								{/if}
								<table class="opt-table buttons">
									<tr class="opt-row">
										<td class="opt-col"></td>
										<td class="opt-col m-right">
											<a href="http://{$site_url}{$item->getUrl()}" class="btn m-sand">{$lang->get('Подробнее на сайте', 'More info')}</a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		
		<div class="page">
			<a href="{$path}" class="pdf-header m-sand">
				<span>{$lang->get('Агентство Недвижимости', 'Vyacheslav Malafeyev')}</span>
				<img src="{$tmp_path}project/img/pdf-logo.png" alt="m16">
				<span>{$lang->get('Вячеслава Малафеева', 'Real Estate Agency')}</span>
			</a>
			<div class="center-title">
				<div class="title">{$title}</div>
				{if !empty($complex.address)}
					<div class="big-descr">
						<i>&nbsp;&nbsp;&nbsp;</i>
						<span>{$complex.address}</span>
						<i>&nbsp;&nbsp;&nbsp;</i>
					</div>
				{/if}
				{if !empty($complex.district)}
					<div class="small-descr">{$complex.district.title}</div>
				{/if}
				{if !empty($complex.description.annotation)}
					<div class="annotation">
						<div></div>
						<p>{$complex.description.annotation}</p> 
					</div>
				{/if}
			</div>
			<table><tr><td>
				<div class="specs-row">
					<table>
						<tr>
							{?$sp_first = true}
							{if !empty($complex.properties.price_meter_from.value)}
								{?$sp_first = false}
								<td>
									<div class="sp-main">{$complex.properties.price_meter_from.value}<span>+</span></div>
									<div class="sp-descr">{$lang->get('Тысяч рублей за м','ths rub. per m')}<sup>2</sup></div>
								</td>
							{/if}
							{if !empty($complex.properties.app_area.value)}
								{if !$sp_first}<td class="border">/</td>{else}{?$sp_first = false}{/if}
								<td>
									<div class="sp-main">{$complex.properties.app_area.value} {$lang->get('м', 'm')}<sup>2</sup></div>
									<div class="sp-descr">{$lang->get('Площади квартир', 'Flats area')}</div>
								</td>
							{/if}
							{if !empty($complex.flats_count)}
								{if !$sp_first}<td class="border">/</td>{else}{?$sp_first = false}{/if}
								<td>
									<div class="sp-main">{$complex.flats_count}</div>
									<div class="sp-descr">{$lang->get(($complex.flats_count|plural_form:'Квартира':'Квартиры':'Квартир':false) . ' в доме','Apartments')}</div>
								</td>
							{/if}
							{if !empty($complex.properties.number_storeys.value)}
								{if !$sp_first}<td class="border">/</td>{else}{?$sp_first = false}{/if}
								<td>
									<div class="sp-main">{$complex.properties.number_storeys.value}</div>
									<div class="sp-descr">{$lang->get($complex.properties.number_storeys.value|plural_form:'Этаж':'Этажа':'Этажей':false, $complex.properties.number_storeys.value|plural_form:'Floor':'Floors':'Floors':false)}</div>
								</td>
							{/if}
							{if !empty($complex.properties.ceiling_height.value)}	
								{if !$sp_first}<td class="border">/</td>{else}{?$sp_first = false}{/if}
								<td>
									<div class="sp-main">{$complex.properties.ceiling_height.value|replace:'.':','}</div>
									<div class="sp-descr">{$lang->get('Высота потолков, М', 'Ceiling height, M')}</div>
								</td>
							{/if}
							{if !empty($complex.properties.construction_stage.value_key) && $complex.properties.construction_stage.value_key == 'under_construction'}
								{?$complete = Array('first' => '1','second' => '2','third' => '3','fourth' => '4')}
								{?$complete_ending = Array('first' => '1','second' => '2','third' => '3','fourth' => '4')}
								{if !empty($complex.properties.complete.value_key) && !empty($complete[$complex.properties.complete.value_key]) && $complex.complete_year}
									{if !$sp_first}<td class="border">/</td>{else}{?$sp_first = false}{/if}
									<td>
										<div class="sp-main">{$complete[$complex.properties.complete.value_key]}{if $request_segment.key != 'ru'}-{$complex.properties.complete.value_key|substr:-2:2}{/if} {$lang->get('квартал', 'quarter')} {$complex.complete_year}</div>
										<div class="sp-descr">{$lang->get('Срок завершения строительства','Completion of construction')}</div>
									</td>
								{/if}
							{else}
								{if !$sp_first}<td class="border">/</td>{else}{?$sp_first = false}{/if}
								<td>
									<div class="sp-main">{$lang->get('ДОМ СДАН','Complete')}</div>
									<div class="sp-descr">{$lang->get('Строительство завершено', 'Сonstruction process')}</div>
								</td>
							{/if}
						</tr>
					</table>
				</div>
			</td></tr>
			<tr><td>
				{?$gallery = !empty($complex.gallery) ? $complex.gallery->getImages() : null}
				{if !empty($gallery)}
					{?$images = array()}
					{foreach from=$gallery item=img name=gallery}
						{if iteration > 5}{break}{/if}
						{?$images[] = array(
							'http://' . $site_url . $img->getUrl(600, 300, true), 
							'http://' . $site_url . $img->getUrl(300, 300, true), 
							'http://' . $site_url . $img->getUrl(300, 150, true)
						)}
					{/foreach}
					<div class="gallery">
						<table>
							{if count($images) == 1}
								<tr>
									<td></td>
									<td rowspan="2" colspan="2"><img src="{$images[0][0]}" alt="" /></td>
									<td></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
							{elseif count($images) == 2}
								<tr>
									<td rowspan="2" colspan="2"><img src="{$images[0][0]}" alt="" /></td>
									<td rowspan="2" colspan="2"><img src="{$images[1][0]}" alt="" /></td>
								</tr>
								<tr></tr>
							{elseif count($images) == 3}
								<tr>
									<td rowspan="2"><img src="{$images[0][1]}" alt="" /></td>
									<td rowspan="2" colspan="2"><img src="{$images[1][0]}" alt="" /></td>
									<td rowspan="2"><img src="{$images[2][1]}" alt="" /></td>
								</tr>
								<tr></tr>
							{elseif count($images) == 4}
								<tr>
									<td rowspan="2"><img src="{$images[0][1]}" alt="" /></td>
									<td rowspan="2" colspan="2"><img src="{$images[1][0]}" alt="" /></td>
									<td><img src="{$images[2][2]}" alt="" /></td>
								</tr>
								<tr>
									<td><img src="{$images[3][2]}" alt="" /></td>
								</tr>
							{else}
								<tr>
									<td><img src="{$images[0][2]}" alt="" /></td>
									<td rowspan="2" colspan="2"><img src="{$images[1][0]}" alt="" /></td>
									<td><img src="{$images[2][2]}" alt="" /></td>
								</tr>
								<tr>
									<td><img src="{$images[3][2]}" alt="" /></td>
									<td><img src="{$images[4][2]}" alt="" /></td>
								</tr>
							{/if}
						</table>
					</div>
		
				{/if}
			</td></tr></table>
		</div>
		
	{/if}
</body>
</html>
