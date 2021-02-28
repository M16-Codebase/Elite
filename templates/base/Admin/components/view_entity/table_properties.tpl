{if $current_type.id != 62 && ($item_prop.key == 'price_variant' || $item_prop.key == 'price_range' || $item_prop.key == 'price_min_variant_closed' || $item_prop.key == 'price_max_variant_closed' || $item_prop.key == 'price_usd_range' || $item_prop.key == 'price_usd_min_offer' || $item_prop.key == 'price_usd_max_offer')}
	{if $prop_price==false}
		{?$prop_price=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $type_rent==true}Арендная ставка{elseif $type_stead==true}Стоимость{else}Цена{/if}{?$prop_i++}</td>
			<td>
				{?$current_entity = array_key_exists('price_variant', $catalog_item['properties'])? $catalog_item : $catalog_item.special_variant}
				<div>
					{if !empty($print)=='printFavorites'}<img class="icon i-open-price" src="/img/print/open-price.png" />{else}<i class="i-agreement"></i>{/if}
					{if !empty($current_entity.price_variant)}{$current_entity.price_variant}{else}Цена договорная{/if}</div>
				{if !empty($current_entity.price_min_variant_closed)}
					<div>{if !empty($print)=='printFavorites'}<img class="icon i-clouse-price" src="/img/print/clouse-price.png" />{else}<i class="i-lock-black"></i>{/if}
						{$current_entity.price_range|price_format_range}
						{*include file="Admin/components/view_entity/value_range_view.tpl" value_range=$current_entity.price_range value_max=$current_entity.price_max_variant_closed value_min=$current_entity.price_min_variant_closed*}
						{if !empty($current_entity.price_usd_min_offer)}&nbsp;(~{$current_entity.price_usd_range|price_format_range}{*include file="Admin/components/view_entity/value_range_view.tpl" value_range=$current_entity.price_usd_range value_max=$current_entity.price_usd_max_offer value_min=$current_entity.price_usd_min_offer*}){/if}
					</div>
				{/if}
				{include file ="Admin/components/view_entity/comments_properties.tpl"  entity=1}
			</td>
		</tr>
	{/if}
{elseif $current_type.id == 62 && ($item_prop.key == 'price_variant' || $item_prop.key == 'foreign_price' || $item_prop.key == 'foreign_currency')}
	{if empty($prop_zagorod_price)}
		{?$prop_zagorod_price=true}		
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>Цена</td>
			<td>
				{if !empty($catalog_item.special_variant.foreign_price)}
					{if !empty($catalog_item.special_variant.foreign_currency)}{$catalog_item.special_variant.foreign_currency} {/if}{$catalog_item.special_variant.foreign_price|price_format}
				{else}
					Цена договорная
				{/if}
			</td>
		</tr>		
	{/if}
{elseif $item_prop.key == 'stantsii_metro' || $item_prop.key == 'metro_data'}
	{if  $prop_metro==false}
		{?$prop_metro=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $item_prop.key == 'stantsii_metro'}{$item_prop.title}{/if}{?$prop_i++}</td>
			<td>
				{if !empty($catalog_item.stantsii_metro) && $catalog_item.properties.stantsii_metro.value && !empty($metro_stations)}
					<div class="metro">
						{?$metro_current = $catalog_item.properties.stantsii_metro.value}
						{foreach from=$metro_current item=metro_id}
							{if !empty($metro_stations[$metro_id])}
								{?$metro = $metro_stations[$metro_id]}
								<div class="metro-line-{$metro.line_id}"><i class="i-metro line-{$metro.line_id}"></i>{$metro.title[$request_segment.id]}
									<span class="num">
										{?$first_prop=true}
										{if !empty($catalog_item.metro_data[$metro_id].distance)}{if !$first_prop}, {else}{?$first_prop=false}{/if}— {$catalog_item.metro_data[$metro_id].distance} м{/if}
										{if !empty($catalog_item.metro_data[$metro_id].time)}{if !$first_prop}, {else}{?$first_prop=false}{/if}~ {$catalog_item.metro_data[$metro_id].time} мин {if $type_stead==true}езды{else}пешком{/if}{/if}
										{if !empty($catalog_item.metro_data[$metro_id].transport)}{if !$first_prop}, {else}{?$first_prop=false}{/if}<i class="i-transport" title="Бесплатный транспорт от метро"></i>{/if}
									</span>
								</div>
							{/if}
						{/foreach}	
					</div>
					{include file ="Admin/components/view_entity/comments_properties.tpl"}
				{/if}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'transport_do_metro_bus' || $item_prop.key == 'transport_do_metro_trollbus'|| $item_prop.key == 'transport_do_metro_tramvai'|| $item_prop.key == 'transport_do_metro_marsh'}
	{if $prop_do_metro==false}
		{?$prop_do_metro=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>Транспорт до метро{?$prop_i++}</td>
			<td>
				{if !empty($catalog_item.transport_do_metro_bus)}<div class="transport">Автобус — {$catalog_item.transport_do_metro_bus}</div>{/if}
				{if !empty($catalog_item.transport_do_metro_tramvai)}<div class="transport">Трамвай — {$catalog_item.transport_do_metro_tramvai}</div>{/if}
				{if !empty($catalog_item.transport_do_metro_trollbus)}<div class="transport">Троллейбус — {$catalog_item.transport_do_metro_trollbus}</div>{/if}
				{if !empty($catalog_item.transport_do_metro_marsh)}<div class="transport">Маршрутное такси — {$catalog_item.transport_do_metro_marsh}</div>{/if}
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'ploschad_range' || $item_prop.key == 'ploschad_ot_offer'|| $item_prop.key == 'ploschad_do_offer'}
	{if $prop_area==false}
		{?$prop_area=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>Предлагаемая площадь{?$prop_i++}</td>
			<td>
				{?$current_entity = !empty($catalog_item.ploschad_range)? $catalog_item : null}
				{if !empty($current_entity)}
					{include file="Admin/components/view_entity/value_range_view.tpl" value_range=$current_entity.ploschad_range value_max=$current_entity.ploschad_do_offer value_min=$current_entity.ploschad_ot_offer}
					{if !empty($variant_properties.ploschad_ot_offer)}
						{?$property_ploschad_ot = $variant_properties.ploschad_ot_offer}
						{if !empty($current_entity['property_comments'][$property_ploschad_ot.id][0])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
							{$current_entity['property_comments'][$property_ploschad_ot.id][0]}</div>
						{/if}
						{if !empty($current_entity['property_comments'][$property_ploschad_ot.id][$request_segment.id])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
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
				Расстояние до {if !empty($catalog_item.rasstojanie_do_kad)}{if !$first_prop} и {else}{?$first_prop=false}{/if}КАД{/if}{if !empty($catalog_item.rasstojanie_do_zsd)}{if !$first_prop} и {else}{?$first_prop=false}{/if}ЗСД{/if}
			</td>
			<td>
				{if !empty($catalog_item.rasstojanie_do_kad)}<div class="transport">До КАД — {$catalog_item.rasstojanie_do_kad}</div>{/if}
				{if !empty($catalog_item.rasstojanie_do_zsd)}<div class="transport">До ЗСД — {$catalog_item.rasstojanie_do_zsd}</div>{/if}
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			</td>
		</tr>
	{/if}
	
{elseif $item_prop.key == 'railway'}
	{if $prop_railway==false}
		{?$prop_railway=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{$item_prop.title}{?$prop_i++}</td>
			<td>
				<ul>
					{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
						<li>
							{$prop}
							{if !empty($catalog_item['properties'][$item_prop.key].comments.private[$val_id])}<div>{$catalog_item['properties'][$item_prop.key].comments.private[$val_id]} мин. езды</div>{/if}
						</li>
					{/foreach}
				</ul>
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			</td>
		</tr>
	{/if}
	
{elseif $item_prop.key == 'prilozhenie'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
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
			{include file ="Admin/components/view_entity/comments_properties.tpl"}
		</td>
	</tr>
	
{elseif $item_prop.key == 'fajl'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
			{?$link = $catalog_item.fajl|regex_replace:'/^\w*\:\/\//':''}
			{if $link == $catalog_item.fajl}							
				{?$link = '//' . $link}
			{else}
				{?$link = $catalog_item.fajl}
			{/if}
			<a href="{$link}" target="_blank">{$catalog_item.fajl}</a>
			{include file ="Admin/components/view_entity/comments_properties.tpl"}
		</td>
	</tr>	
	
{elseif $item_prop.key == 'etazhnost_do' || $item_prop.key == 'etazhnost_ot'}
	{if $prop_floor==false}
		{?$prop_floor=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>Этажность{?$prop_i++}</td>
			<td>
				{include file="Admin/components/view_entity/value_range_view.tpl" value_max=$catalog_item.etazhnost_do value_min=$catalog_item.etazhnost_ot}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'vysota_potolkov_ot_offer' || $item_prop.key == 'vysota_potolkov_do_offer'}
	{if empty($prop_floor_v)}
		{?$prop_floor_v = true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>Высота потолков{?$prop_i++}</td>
			<td>
				{include file="Admin/components/view_entity/value_range_view.tpl" value_max=$catalog_item.vysota_potolkov_do_offer value_min=$catalog_item.vysota_potolkov_ot_offer}
			</td>
		</tr>
	{else}
		{?$prop_floor_v = false}
	{/if}
{elseif $item_prop.key == 'status_object'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>Статус{?$prop_i++}</td>
		<td>
			{if !empty($catalog_item.status_object)}
				<i class="prop_status {if $catalog_item.status_object=='Есть договор'}contract{elseif $catalog_item.status_object=='Не работаем'}not-work{elseif $catalog_item.status_object=='Есть устная договоренность'}tire-agreement{elseif $catalog_item.status_object=='В архиве'}archive{elseif $catalog_item.status_object=='Статус не определен'}non-status{elseif $catalog_item.status_object=='Гарантийное письмо'}guarantee{elseif $catalog_item.status_object=='На согласовании'}concordance{/if}"></i>
				{if $catalog_item.status_object=='Статус не определен'}
					Без статуса
				{else}
					{$catalog_item[$item_prop.key]}
				{/if}
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			{/if}
		</td>
	</tr>
{elseif $item_prop.key == 'data_istechenija_dogovora'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
			{?$no_actual = floor(($catalog_item.properties.data_istechenija_dogovora.value - time())/(60*60*24))+1}
			<span {if $no_actual< 0}class="no-actual"{/if}>{if $no_actual< 0}<i class="i-date no-actual"></i>{/if}{$catalog_item.data_istechenija_dogovora}</span>
			{if $no_actual< 0}{?$no_actual_dates = -$no_actual}<span class="comment"> — просрочен уже {$no_actual_dates|plural_form:'день':'дня':'дней'}</span>{/if}
			{include file ="Admin/components/view_entity/comments_properties.tpl"}
		</td>
	</tr>
{elseif $item_prop.key == 'napravlenie'}
	{if $prop_direction==false}
		{?$prop_direction=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{$item_prop.title}{?$prop_i++}</td>
			<td>
				{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=direction key=val_id}
					<i class="i-direction {if $direction == "Север"}north{elseif $direction == "Восток"}east{elseif $direction == "Юг"}south{elseif $direction == "Запад"}west{/if}"></i>
					<span>{$direction}</span>
				{/foreach}
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'etazh_offer'}
	{if $prop_etazh==false}
		{?$prop_etazh=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{$item_prop.title}{?$prop_i++}</td>
			<td>
				{implode(', ', $catalog_item.etazh_offer)}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'city' || $item_prop.key == 'adres'}
	{if $prop_adres==false}
		{?$prop_adres=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $item_prop.key == 'adres'}{$item_prop.title}{/if}{?$prop_i++}</td>
			<td>
				{?$first_prop=true}
				{if !empty($catalog_item.city)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.city}{/if}
				{if !empty($catalog_item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.adres}
					{?$prop_id = $catalog_item.properties.adres.property.id}
					{include file ="Admin/components/view_entity/comments_properties.tpl"}
				{/if}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'koordinaty_na_karte'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
			{?$coord = explode(',', $catalog_item.koordinaty_na_karte)}
			<i class="i-region green"></i><span class="a-link link-map">Широта — {$coord[0]}, долгота — {$coord[1]}</span>
			{include file ="Admin/components/view_entity/comments_properties.tpl"}
		</td>
	</tr>
{elseif $item_prop.key == 'status_gotovnosti' || $item_prop.key == 'god_postrojki' || $item_prop.key == 'planiruemaja_data_gotovnosti' || $item_prop.key == 'planiruemaja_data_gotovnosti_kvartal'}  
	{if $prop_status_gotov==false}
		{?$prop_status_gotov=true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>{if $type_investment==true}Год постройки{else}Статус готовности{/if}{?$prop_i++}</td>
			<td>
				{if !empty($catalog_item.status_gotovnosti) && $catalog_item.status_gotovnosti == 'Строящийся'}
					В стадии строительства{if !empty($catalog_item.planiruemaja_data_gotovnosti_kvartal)} — {$catalog_item.planiruemaja_data_gotovnosti_kvartal}{/if}
					{if !empty($catalog_item.planiruemaja_data_gotovnosti)} {$catalog_item.planiruemaja_data_gotovnosti}{/if}
				{elseif !empty($catalog_item.status_gotovnosti) && $catalog_item.status_gotovnosti == 'Готовый' || !empty($catalog_item.god_postrojki)}
					Построен{if !empty($catalog_item.god_postrojki)} в {$catalog_item.god_postrojki}{/if}
				{/if}
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			</td>
		</tr>
	{/if}
{elseif $item_prop.key == 'parkovka' || $item_prop.key == 'parkovka_data'}
	{if !empty($catalog_item['properties'][$item_prop.key]['complete_value'])}
		{if $prop_parkovka==false}
			{?$prop_parkovka=true}
			<tr{if $prop_i%2!=0} class="even"{/if}>
				<td>{if $item_prop.key == 'parkovka'}{$item_prop.title}{/if}{?$prop_i++}</td>
				<td>
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
										{if !empty($parking_mm)} на {$parking_mm|plural_form:'место':'места':'мест'}{/if}
										{?$first_prop=true}
										<div>
											{if !empty($parking_m_mm)}{if !$first_prop}, {else}{?$first_prop=false}{/if} 1 место на {$parking_m_mm} м²{/if}
											{if !empty($parking_rub_mm)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$parking_rub_mm} руб./мес. за место{/if}
										</div>
									{/if}
								{/if}
							</li>
						{/foreach}
					</ul>
					{include file ="Admin/components/view_entity/comments_properties.tpl"}
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
				<td>
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
											{if !empty($buildings_count)}{if !$first_prop}, {else}{?$first_prop=false}{/if} {$buildings_count|plural_form:'здание':'здания':'зданий'}{/if}
											{if !empty($buildings_area)}{if !$first_prop}, {else}{?$first_prop=false}{/if}общая площадь {$buildings_area} м²{/if}
										</div>
									{/if}
								{/if}
							</li>
						{/foreach}
					</ul>
					{include file ="Admin/components/view_entity/comments_properties.tpl"}
				</td>
			</tr>
		{/if}
	{/if}
	
{elseif $item_prop.key == 'uslugi_maris' && !empty($maris_services)}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
			<ul>
				{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
					{if !empty($maris_services[$prop])}
						<li>
							<a href="{$maris_services[$prop]->getUrl()}">{$maris_services[$prop]['title']}</a>
							{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][1])}
								<span class="small-descr">(<a href="{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][1]}">См. в портфолио</a>)</span>
							{/if}
						</li>
					{elseif $prop == 'm'}
						<li>
							<a href="/managment/">Управление и эксплуатация недвижимости</a>
							{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][1])}
								<span class="small-descr">(<a href="{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][1]}">См. в портфолио</a>)</span>
							{/if}
						</li>
					{/if}
				{/foreach}
			</ul>
			{include file ="Admin/components/view_entity/comments_properties.tpl"}
		</td>
	</tr>
		
{elseif $item_prop.key == 'kurator_objekta' || $item_prop.key == 'kontaktnoe_litso'}	
	
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
			<a href="{if !empty($catalog_item[$item_prop.key]['parent_id'])}/staff/view/?id={$catalog_item[$item_prop.key]['id']}{else}/staff/{/if}">{$catalog_item[$item_prop.key]['name']} {$catalog_item[$item_prop.key]['surname']}</a>
		</td>
	</tr>

{elseif $item_prop.key == 'kontakty_kontragenta' || $item_prop.key == 'dop_kontakty_kontragenta'}
	{if empty($prop_kontakty_kontragenta)}
		{?$prop_kontakty_kontragenta = true}
		<tr{if $prop_i%2!=0} class="even"{/if}>
			<td>Контакты контрагента{?$prop_i++}</td>
			<td>
				{if !empty($catalog_item.kontakty_kontragenta)}
					<p class="a-pre"><b>{$catalog_item.kontakty_kontragenta}</b></p>
				{/if}
				{if !empty($catalog_item.dop_kontakty_kontragenta)}
					<p class="a-pre">{$catalog_item.dop_kontakty_kontragenta}</p>
				{/if}			
			</td>
		</tr>
	{/if}
        
{elseif $item_prop.key == 'otdel'}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
			{if !empty($catalog_item['properties'][$item_prop.key]['complete_value']) && !empty($dept_list[$catalog_item['properties'][$item_prop.key]['complete_value']])}
				{$dept_list[$catalog_item['properties'][$item_prop.key]['complete_value']]['name']}
			{else}
				Не указан
			{/if}
		</td>
	</tr>
{else}
	<tr{if $prop_i%2!=0} class="even"{/if}>
		<td>{$item_prop.title}{?$prop_i++}</td>
		<td>
			{if $item_prop.set == 0}
				{?$val_id = $catalog_item['properties'][$item_prop.key]['val_id'][0]}
				{if $item_prop.data_type == 'text'}<div class="a-pre">{/if}
				{$catalog_item[$item_prop.key]}
				{if $item_prop.data_type == 'text'}</div>{/if}
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			{else}
				<ul>
					{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
						<li>
							{$prop}
							{if !empty($catalog_item['properties'][$item_prop.key].comments.private[$val_id])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
								{$catalog_item['properties'][$item_prop.key].comments.private[$val_id]}</div>
							{/if}
							{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id])}<div class="comment"><i class="i-comment"></i>{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id]}</div>{/if}
						</li>
					{/foreach}
				</ul>
				{include file ="Admin/components/view_entity/comments_properties.tpl"}
			{/if}
		</td>
	</tr>
{/if}