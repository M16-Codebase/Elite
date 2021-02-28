{if empty($tmp_path)}{?$tmp_path = ''}{/if}
{if $current_type.id == 62 && ($item_prop.key == 'price_variant' || $item_prop.key == 'foreign_price' || $item_prop.key == 'foreign_currency')}
	{if empty($prop_zagorod_price)}
		{?$prop_zagorod_price=true}		
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $ru}Цена{else}Price{/if}</td>
			<td class="table-value">
				{if !empty($catalog_item.special_variant.foreign_price)}
					{if !empty($catalog_item.special_variant.foreign_currency)}{$catalog_item.special_variant.foreign_currency} {/if}{$catalog_item.special_variant.foreign_price|price_format}
				{else}
					{if $ru}Цена договорная{else}Negotiated price{/if}
				{/if}
			</td>
		</tr>		
	{/if}
{elseif $item_prop.key == 'stantsii_metro' || $item_prop.key == 'metro_data'}
	{if  $prop_metro==false}
		{?$prop_metro=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $item_prop.key == 'stantsii_metro'}{$item_prop.title}{/if}{?$prop_i++}</td>
			<td class="table-value">
				{if !empty($catalog_item.stantsii_metro) && $catalog_item.properties.stantsii_metro.value && !empty($metro_stations)}
					<div class="metro">
						{?$metro_current = $catalog_item.properties.stantsii_metro.value}
						{foreach from=$metro_current item=metro_id}
							{if !empty($metro_stations[$metro_id])}
								{?$metro = $metro_stations[$metro_id]}
								<div class="metro-line-{$metro.line_id}">{if !empty($printPdf)}<img src="{$tmp_path}/img/icons/pdf-metro.png"  class="i-metro" alt="metro"/>{else}<img src="{$tmp_path}/img/icons/metro-icon.png" class="i-metro" alt="metro" />{/if} {$metro.title[$request_segment.id]}
									<span class="num">
										{?$first_prop=true}
										{if !empty($catalog_item.metro_data[$metro_id].distance)}{if !$first_prop}, {else}{?$first_prop=false}{/if}— {$catalog_item.metro_data[$metro_id].distance} м{/if}
										{if !empty($catalog_item.metro_data[$metro_id].time)}{if !$first_prop}, {else}{?$first_prop=false}{/if}~ {$catalog_item.metro_data[$metro_id].time} {if $ru}мин {if $type_stead==true}езды{else}пешком{/if}{else}min {if $type_stead==true}drive{else}walk{/if}{/if}{/if}
										{if !empty($catalog_item.metro_data[$metro_id].transport)}{if !$first_prop}, {else}{?$first_prop=false}{/if}<i class="i-transport" title="Бесплатный транспорт от метро"></i>{/if}
									</span>
								</div>
							{/if}
						{/foreach}	
					</div>
					{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
				{/if}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'transport_do_metro_bus' || $item_prop.key == 'transport_do_metro_trollbus'|| $item_prop.key == 'transport_do_metro_tramvai'|| $item_prop.key == 'transport_do_metro_marsh'}
	{if $prop_do_metro==false}
		{?$prop_do_metro=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $ru}Транспорт до метро{else}Transport to the metro station{/if}{?$prop_i++}</td>
			<td class="table-value">
				{if !empty($catalog_item.transport_do_metro_bus)}<div class="transport">{if $ru}Автобус{else}Bus{/if} — {$catalog_item.transport_do_metro_bus}</div>{/if}
				{if !empty($catalog_item.transport_do_metro_tramvai)}<div class="transport">{if $ru}Трамвай{else}Tramway{/if} — {$catalog_item.transport_do_metro_tramvai}</div>{/if}
				{if !empty($catalog_item.transport_do_metro_trollbus)}<div class="transport">{if $ru}Троллейбус{else}Trolleybus{/if} — {$catalog_item.transport_do_metro_trollbus}</div>{/if}
				{if !empty($catalog_item.transport_do_metro_marsh)}<div class="transport">{if $ru}Маршрутное такси{else}Taxi{/if} — {$catalog_item.transport_do_metro_marsh}</div>{/if}
				{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'ploschad_range' || $item_prop.key == 'ploschad_ot_offer'|| $item_prop.key == 'ploschad_do_offer'}
	{if $prop_area==false}
		{?$prop_area=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $ru}Предлагаемая площадь{else}Area{/if}{?$prop_i++}</td>
			<td class="table-value">
				{?$current_entity = !empty($catalog_item.ploschad_range)? $catalog_item : null}
				{if !empty($current_entity)}
					{include file="Admin/components/view_entity/value_range_view.tpl" value_range=$current_entity.ploschad_range value_max=$current_entity.ploschad_do_offer value_min=$current_entity.ploschad_ot_offer}
					{if !empty($variant_properties.ploschad_ot_offer)}
						{?$property_ploschad_ot = $variant_properties.ploschad_ot_offer}
						{if !empty($current_entity['property_comments'][$property_ploschad_ot.id][0])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="{$tmp_path}/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
							{$current_entity['property_comments'][$property_ploschad_ot.id][0]}</div>
						{/if}
						{if !empty($current_entity['property_comments'][$property_ploschad_ot.id][$request_segment.id])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="{$tmp_path}/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
							{$current_entity['property_comments'][$property_ploschad_ot.id][$request_segment.id]}</div>
						{/if}
					{/if}
				{/if}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'rasstojanie_do_zsd' || $item_prop.key == 'rasstojanie_do_kad'}
	{if $prop_distance_kad==false}
		{?$prop_distance_kad=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>
				{?$first_prop=true}{?$prop_i++}
				{if $ru}
					Расстояние до {if !empty($catalog_item.rasstojanie_do_kad)}{if !$first_prop} и {else}{?$first_prop=false}{/if}КАД{/if}{if !empty($catalog_item.rasstojanie_do_zsd)}{if !$first_prop} и {else}{?$first_prop=false}{/if}ЗСД{/if}
				{else}
					Distance to {if !empty($catalog_item.rasstojanie_do_kad)}{if !$first_prop} and {else}{?$first_prop=false}{/if}KAD (Ring Road){/if}{if !empty($catalog_item.rasstojanie_do_zsd)}{if !$first_prop} and {else}{?$first_prop=false}{/if}ZSD (Western High Speed Diameter){/if}
				{/if}
			</td>
			<td class="table-value">
				{if !empty($catalog_item.rasstojanie_do_kad)}<div class="transport">{if $ru}До КАД{else}To KAD (Ring Road){/if} — {$catalog_item.rasstojanie_do_kad}</div>{/if}
				{if !empty($catalog_item.rasstojanie_do_zsd)}<div class="transport">{if $ru}До ЗСД{else}To ZSD (Western High Speed Diameter){/if} — {$catalog_item.rasstojanie_do_zsd}</div>{/if}
				{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'railway'}
	{if $prop_railway==false}
		{?$prop_railway=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{$item_prop.title}{?$prop_i++}</td>
			<td class="table-value">
				<ul>
					{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
						<li>
							{$prop}
							{if !empty($catalog_item['properties'][$item_prop.key].comments.private[$val_id])}<div>{$catalog_item['properties'][$item_prop.key].comments.private[$val_id]} {if $ru}мин. езды{else}min drive{/if}</div>{/if}
						</li>
					{/foreach}
				</ul>
				{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'prilozhenie'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td class="table-value">
			<ul class="document-appls">
				{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
					{if !empty($catalog_item['properties'][$item_prop.key].comments.private[$val_id])}
						<li>
							{?$link = $catalog_item['properties'][$item_prop.key].comments.private[$val_id]|regex_replace:'/^\w*\:\/\//':''}
							{if $link == $catalog_item['properties'][$item_prop.key].comments.private[$val_id]}							
								{?$link = '//' . $link}
							{else}
								{?$link = $catalog_item['properties'][$item_prop.key].comments.private[$val_id]}
							{/if}
							<a href="{$link}" target="_blank">{if !empty($prop)}{$prop}{else}{$catalog_item['properties'][$item_prop.key].comments.private[$val_id]|truncate:20}{/if}</a>
						</li>
					{/if}
				{/foreach}
			</ul>
			{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
		</td>
	</tr>
	
{elseif $item_prop.key == 'fajl'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td class="table-value">
			{?$link = $catalog_item.fajl|regex_replace:'/^\w*\:\/\//':''}
			{if $link == $catalog_item.fajl}							
				{?$link = '//' . $link}
			{else}
				{?$link = $catalog_item.fajl}
			{/if}
			<a href="{$link}" target="_blank">{$catalog_item.fajl}</a>
			{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
		</td>
	</tr>	
	
{elseif $item_prop.key == 'etazhnost_do' || $item_prop.key == 'etazhnost_ot'}
	{if $prop_floor==false}
		{?$prop_floor=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $ru}Этажность{else}Number of storeys{/if}{?$prop_i++}</td>
			<td class="table-value">
				{include file="Admin/components/view_entity/value_range_view.tpl" value_max=$catalog_item.etazhnost_do value_min=$catalog_item.etazhnost_ot}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'vysota_potolkov_ot_offer' || $item_prop.key == 'vysota_potolkov_do_offer'}
	{if empty($prop_floor_v)}
		{?$prop_floor_v = true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $ru}Высота потолков{else}Ceiling height{/if}{?$prop_i++}</td>
			<td class="table-value">
				{include file="Admin/components/view_entity/value_range_view.tpl" value_max=$catalog_item.vysota_potolkov_do_offer value_min=$catalog_item.vysota_potolkov_ot_offer}
			</td>
		</tr>
	{else}
		{?$prop_floor_v = false}
	{/if}
{elseif $item_prop.key == 'napravlenie'}
	{if $prop_direction==false}
		{?$prop_direction=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{$item_prop.title}{?$prop_i++}</td>
			<td class="table-value">
				{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=direction key=val_id}
					<i class="i-direction {if $direction == "Север"}north{elseif $direction == "Восток"}east{elseif $direction == "Юг"}south{elseif $direction == "Запад"}west{/if}"></i>
					<span>{$direction}</span>
				{/foreach}
				{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'etazh_offer'}
	{if $prop_etazh==false}
		{?$prop_etazh=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{$item_prop.title}{?$prop_i++}</td>
			<td class="table-value">
				{implode(', ', $catalog_item.etazh_offer)}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'city' || $item_prop.key == 'adres'}
	{if $prop_adres==false}
		{?$prop_adres=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $item_prop.key == 'adres'}{$item_prop.title}{/if}{?$prop_i++}</td>
			<td class="table-value">
				{?$first_prop=true}
				{if !empty($catalog_item.city)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.city}{/if}
				{if !empty($catalog_item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.adres}
					{?$prop_id = $catalog_item.properties.adres.property.id}
					{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
				{/if}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'koordinaty_na_karte'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td class="table-value">
			{?$coord = explode(',', $catalog_item.koordinaty_na_karte)}
			<i class="i-region green"></i><span class="a-link link-map">{if $ru}Широта{else}Latitude{/if} — {$coord[0]}, {if $ru}долгота{else}longitude{/if} — {$coord[1]}</span>
			{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
		</td>
	</tr>
{elseif $item_prop.key == 'status_gotovnosti' || $item_prop.key == 'god_postrojki' || $item_prop.key == 'planiruemaja_data_gotovnosti' || $item_prop.key == 'planiruemaja_data_gotovnosti_kvartal'}  
	{if $prop_status_gotov==false}
		{?$prop_status_gotov=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $type_investment==true}{if $ru}Год постройки{else}Year of construction{/if}{else}{if $ru}Статус готовности{else}Building status{/if}{/if}{?$prop_i++}</td>
			<td class="table-value">
				{if !empty($catalog_item.status_gotovnosti) && $catalog_item.status_gotovnosti == 'Строящийся'}
					{if $ru}В стадии строительства{else}Under construction{/if}{if !empty($catalog_item.planiruemaja_data_gotovnosti_kvartal)} — {$catalog_item.planiruemaja_data_gotovnosti_kvartal}{/if}
					{if !empty($catalog_item.planiruemaja_data_gotovnosti)} {$catalog_item.planiruemaja_data_gotovnosti}{/if}
				{elseif !empty($catalog_item.status_gotovnosti) && $catalog_item.status_gotovnosti == 'Готовый' || !empty($catalog_item.god_postrojki)}
					{if $ru}Построен{else}Ready{/if}{if !empty($catalog_item.god_postrojki)} {if $ru}в{else}in{/if} {$catalog_item.god_postrojki}{/if}
				{/if}
				{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'parkovka' || $item_prop.key == 'parkovka_data'}
	{if !empty($catalog_item['properties'][$item_prop.key]['complete_value'])}
		{if $prop_parkovka==false}
			{?$prop_parkovka=true}
			<tr{if $prop_i%2!=0} class="even"{/if}>
				<td>{if $item_prop.key == 'parkovka'}{$item_prop.title}{/if}{?$prop_i++}</td>
				<td class="table-value">
					<ul>
						{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
							<li>
								{$prop}
								{if !empty($catalog_item.parkovka_data)}
									{?$enum_id = $catalog_item['properties'][$item_prop.key]['value'][$val_id]}
									{?$parking_mm = $catalog_item.parkovka_data[$enum_id]['mm']}
									{?$parking_m_mm = $catalog_item.parkovka_data[$enum_id]['m-mm']}
									{?$parking_rub_mm = $catalog_item.parkovka_data[$enum_id]['rub-mm']}
									{if !empty($parking_mm) || !empty($parking_m_mm) || !empty($parking_rub_mm)}
										{if $ru}
											{if !empty($parking_mm)} на {$parking_mm|plural_form:'место':'места':'мест'}{/if}
										{else}
											{if !empty($parking_mm)} on {$parking_mm|plural_form:'place':'places':'places'}{/if}
										{/if}
										{?$first_prop=true}
										<div>
											{if !empty($parking_m_mm)}{if !$first_prop}, {else}{?$first_prop=false}{/if} 1 {if $ru}место на{else}place on{/if} {$parking_m_mm} {if $ru}м²{else}m²{/if}{/if}
											{if !empty($parking_rub_mm)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$parking_rub_mm} {if $ru}руб./мес. за место{else}rub./place a place{/if}{/if}
										</div>
									{/if}
								{/if}
							</li>
						{/foreach}
					</ul>
					{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
				</td>
			</tr>
		{/if}
	{/if}
{elseif $item_prop.key == 'buildings' || $item_prop.key == 'buildings_data'}
	{if !empty($catalog_item['properties'][$item_prop.key]['complete_value'])}
		{if $prop_buildings==false}
			{?$prop_buildings=true}
			<tr{if $prop_i%2!=0} class="even"{/if}>
				<td>{if $item_prop.key == 'buildings'}{$item_prop.title}{/if}{?$prop_i++}</td>
				<td class="table-value">
					<ul>
						{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
							<li>
								{$prop}
								{if !empty($catalog_item.buildings_data)}
									{?$enum_id = $catalog_item['properties'][$item_prop.key]['value'][$val_id]}
									{?$buildings_count = $catalog_item.buildings_data[$enum_id]['count']}
									{?$buildings_area = $catalog_item.buildings_data[$enum_id]['area']}
									{if !empty($buildings_count) || !empty($buildings_area)}
										{?$first_prop=true}
										<div>
											{if !empty($buildings_count)}{if !$first_prop}, {else}{?$first_prop=false}{/if} {if $ru}{$buildings_count|plural_form:'здание':'здания':'зданий'}{else}{$buildings_count|plural_form:'building':'buildings':'buildings'}{/if}{/if}
											{if !empty($buildings_area)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{if $ru}общая площадь{else}total area{/if} {$buildings_area} {if $ru}м²{else}m²{/if}{/if}
										</div>
									{/if}
								{/if}
							</li>
						{/foreach}
					</ul>
					{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
				</td>
			</tr>
		{/if}
	{/if}
{elseif $item_prop.key == 'uslugi_maris' && !empty($maris_services)}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td class="table-value">
			<ul>
				{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
					<li>
						<a href="{$maris_services[$prop]->getUrl()}">{$maris_services[$prop]['title']}</a>
						{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][1])}
							<span class="small-descr">(<a href="{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][1]}">{if $ru}См. в портфолио{else}See portfolio{/if}</a>)</span>
						{/if}
					</li>
				{/foreach}
			</ul>
			{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
		</td>
	</tr>
{else}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td class="table-value">
			{if $item_prop.set == 0}
				{?$val_id = $catalog_item['properties'][$item_prop.key]['val_id'][0]}
				{if $item_prop.data_type == 'text'}<div class="a-pre">{/if}
				{$catalog_item[$item_prop.key]}
				{if $item_prop.data_type == 'text'}</div>{/if}
				{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
			{else}
				<ul>
					{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
						<li>
							{$prop}
							{*if !empty($catalog_item['properties'][$item_prop.key].comments.private[$val_id])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
								{$catalog_item['properties'][$item_prop.key].comments.private[$val_id]}</div>
							{/if*}
							{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="{$tmp_path}/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id]}</div>{/if}
						</li>
					{/foreach}
				</ul>
				{include file ="Modules/Catalog/Main/print_comments_properties.tpl"}
			{/if}
		</td>
	</tr>
{/if}