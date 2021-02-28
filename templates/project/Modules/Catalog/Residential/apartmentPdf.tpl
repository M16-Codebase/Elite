{?$path = "http://" . $site_url . '/'}
{?$tmp_path = $path . "templates/"}
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style>
		{fetch assign=pdf_css file= $tmp_path . "project/Modules/Catalog/Residential/apartmentPdf.css"}
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
				<div class="row-top">
					<table>
						<tr>
							<td class="col1">
								<div class="title">{$item.title}</div>
								<div class="price_per_m">
									{if !empty($item.district)}{$item.district.title}{/if}
									{if !empty($item.district) && !empty($item.object_title)} / {/if}
									{if !empty($item.object_title)}{$item.object_title}{/if}
								</div>
							</td>
							<td class="price-wrap col2">
								<table class="opt-table">
									<tr class="opt-row">
										<td class="opt-col"></td>
										<td class="opt-col m-right">
											{if !empty($item.price)}
												<div class="price">{$item.price}</div>
												<div class="price_per_m">
													{if !empty($item.properties.area_all.value)}{($item.price/$item.properties.area_all.value*1000)|ceil} {$lang->get('тыс. руб. за м','ths rub. per m')}<sup>2</sup>{/if}
												</div>
											{else}
												<div class="price">Цена по запросу</div>
											{/if}
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<div class="row-bottom">
					<table>
						<tr>
							<td class="col1">
								<div class="item-cover">
									{?$scheme = !empty($item.shemes) ? $item.shemes->getCover() : null}
									{if !empty($scheme)}
										<img class="img" src="http://{$site_url}{$scheme->getUrl(600,330,false)}" alt="">
									{/if}
								</div>
								{if !empty($item.consultant)}
									{?$consultant = $item.consultant}
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
							<td class="col2">
								{if !empty($item.features)}
									<div class="item-features">
										{foreach from=$item.features item=feature name=features}
											{if iteration>1} / {/if}<span>{$feature}</span>
										{/foreach}
									</div>
								{/if}
								{if !empty($type_properties)}
									<table class="opt-table">
										{foreach from=$type_properties item=prop name=type_properties}
											{if iteration < 10 && !empty($item[$prop.key])}
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
	
		{?$gallery = !empty($item.gallery) ? $item.gallery->getImages() : null}
		{if !empty($gallery)}
			<div class="page">
				<a href="{$path}" class="pdf-header m-sand">
					<span>{$lang->get('Агентство Недвижимости', 'Vyacheslav Malafeyev')}</span>
					<img src="{$tmp_path}project/img/pdf-logo.png" alt="m16">
					<span>{$lang->get('Вячеслава Малафеева', 'Real Estate Agency')}</span>
				</a>
				<div class="center-title">
					<div class="main">{$lang->get('Элитный коттедж', 'Luxury cottage')}</div>
					<div class="title">{$item.title}</div>
					{if !empty($item.object_title)}
						<div class="big-descr">
							<i>&nbsp;&nbsp;&nbsp;</i>
							<span>{$item.object_title}</span>
							<i>&nbsp;&nbsp;&nbsp;</i>
						</div>
					{/if}
					{if !empty($item.district)}
						<div class="small-descr">{$item.district.title}</div>
					{/if}
				</div>
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
				{if !empty($item.price)}
					<table class="center-price">
						<tr>
							<td></td>
							<td class="price-wrap">
								<div class="price">{$item.price}</div>
								<div class="price_per_m">
									{if !empty($item.properties.area_all.value)}{($item.price/$item.properties.area_all.value*1000)|ceil} тыс. руб. за м<sup>2</sup>{/if}
								</div>
							</td>
							<td></td>
						</tr>
					</table>
				{/if}
			</div>
		{/if}
	{/if}
</body>
</html>
