{if $item_prop.key == 'stantsii_metro' || $item_prop.key == 'metro_data'}
	{if empty($prop_metro)}
		{?$prop_metro=true}
		<div class="prop-title">{if $item_prop.key == 'stantsii_metro'}{$item_prop.title}{/if}</div>
		<div class="prop">
			{if !empty($catalog_item.stantsii_metro) && $catalog_item.properties.stantsii_metro.value && !empty($metro_stations)}
				<div class="metro">
					{?$metro_current = $catalog_item.properties.stantsii_metro.value}
					{foreach from=$metro_current item=metro_id}
						{if !empty($metro_stations[$metro_id])}
							{?$metro = $metro_stations[$metro_id]}
							<div class="metro-line-{$metro.line_id}"><i class="i-metro line-{$metro.line_id}"></i>{$metro.title[$request_segment.id]}
								<span class="num">
									{?$first_prop=true}
									{if !empty($catalog_item.metro_data[$metro_id].distance)}{if !$first_prop}, {else}{?$first_prop=false}{/if}— {$catalog_item.metro_data[$metro_id].distance} {if $request_segment.id==1}м{else}m{/if}{/if}
									{if !empty($catalog_item.metro_data[$metro_id].time)}{if !$first_prop}, {else}{?$first_prop=false}{/if}~ {$catalog_item.metro_data[$metro_id].time}
										{if $request_segment.id==1} мин. {if $type_stead==true}езды{else}пешком{/if}
										{else} min {if $type_stead==true}drive{else}walk{/if}{/if}
									{/if}
									{if !empty($catalog_item.metro_data[$metro_id].transport)}{if !$first_prop}, {else}{?$first_prop=false}{/if}<i class="i-transport" title="{if $request_segment.id==1}Бесплатный транспорт от метро{else}Free transport from the metro{/if}"></i>{/if}
								</span>
							</div>
						{/if}
					{/foreach}	
				</div>
				{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
			{/if}
		</div>
	{/if}
{elseif $item_prop.key == 'transport_do_metro_bus' || $item_prop.key == 'transport_do_metro_trollbus'|| $item_prop.key == 'transport_do_metro_tramvai'|| $item_prop.key == 'transport_do_metro_marsh'}
	{if empty($prop_do_metro)}
		{?$prop_do_metro=true}
		<div class="prop-title">{if $request_segment.id==1}Транспорт до метро{else}Transport to the metro station{/if}</div>
		<div class="prop">
			{if !empty($catalog_item.transport_do_metro_bus)}<div class="transport">{if $request_segment.id==1}Автобус{else}Bus{/if} — {$catalog_item.transport_do_metro_bus}</div>{/if}
			{if !empty($catalog_item.transport_do_metro_tramvai)}<div class="transport">{if $request_segment.id==1}Трамвай{else}Tramway{/if} — {$catalog_item.transport_do_metro_tramvai}</div>{/if}
			{if !empty($catalog_item.transport_do_metro_trollbus)}<div class="transport">{if $request_segment.id==1}Троллейбус{else}Trolleybus{/if} — {$catalog_item.transport_do_metro_trollbus}</div>{/if}
			{if !empty($catalog_item.transport_do_metro_marsh)}<div class="transport">{if $request_segment.id==1}Маршрутное такси{else}Taxi{/if} — {$catalog_item.transport_do_metro_marsh}</div>{/if}
		</div>
	{/if}
{elseif $item_prop.key == 'ploschad_range' || $item_prop.key == 'ploschad_ot_offer'|| $item_prop.key == 'ploschad_do_offer'}
	{if empty($prop_area)}
		{?$prop_area=true}
		<div class="prop-title">{if $request_segment.id==1}Предлагаемая площадь{else}Area{/if}</div>
		<div class="prop">
			{?$current_entity = !empty($catalog_item.ploschad_range)? $catalog_item : $catalog_item.special_variant}
			{include file="Admin/components/view_entity/value_range_view.tpl" value_range=$current_entity.ploschad_range value_max=$current_entity.ploschad_do_offer value_min=$current_entity.ploschad_ot_offer}
		</div>
	{/if}
{elseif $item_prop.key == 'rasstojanie_do_zsd' || $item_prop.key == 'rasstojanie_do_kad'}
	{if empty($prop_distance_kad)}
		{?$prop_distance_kad=true}
		<div class="prop-title">
			{?$first_prop=true}
			{if $request_segment.id==1}
				Расстояние до {if !empty($catalog_item.rasstojanie_do_kad)}{if !$first_prop} и {else}{?$first_prop=false}{/if}КАД{/if}{if !empty($catalog_item.rasstojanie_do_zsd)}{if !$first_prop} и {else}{?$first_prop=false}{/if}ЗСД{/if}
			{else}
				Distance to {if !empty($catalog_item.rasstojanie_do_kad)}{if !$first_prop} and {else}{?$first_prop=false}{/if}KAD (Ring Road){/if}{if !empty($catalog_item.rasstojanie_do_zsd)}{if !$first_prop} and {else}{?$first_prop=false}{/if}ZSD (Western High Speed Diameter){/if}
			{/if}
		</div>
		<div class="prop">
			{if !empty($catalog_item.rasstojanie_do_kad)}<div class="transport">{if $request_segment.id==1}До КАД{else}To KAD (Ring Road){/if} — {$catalog_item.rasstojanie_do_kad}</div>{/if}
			{if !empty($catalog_item.rasstojanie_do_zsd)}<div class="transport">{if $request_segment.id==1}До ЗСД{else}To ZSD (Western High Speed Diameter){/if} — {$catalog_item.rasstojanie_do_zsd}</div>{/if}
		</div>
	{/if}
{elseif $item_prop.key == 'railway'}
	{if empty($prop_railway)}
		{?$prop_railway=true}
		<div class="prop-title">{$item_prop.title}</div>
		<div class="prop">
			<ul>
				{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
					<li>
						{$prop}
						{if !empty($catalog_item['properties'][$item_prop.key].comments.private[$val_id])}<div>{$catalog_item['properties'][$item_prop.key].comments.private[$val_id]} {if $request_segment.id==1}мин. езды{else}min drive{/if}</div>{/if}
					</li>
				{/foreach}
			</ul>
			{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
		</div>
	{/if}
{elseif $item_prop.key == 'etazhnost_do' || $item_prop.key == 'etazhnost_ot'}
	{if empty($prop_floor)}
		{?$prop_floor=true}
		<div class="prop-title">{if $request_segment.id==1}Этажность{else}Number of storeys{/if}</div>
		<div class="prop">
			{include file="Admin/components/view_entity/value_range_view.tpl" value_max=$catalog_item.etazhnost_do value_min=$catalog_item.etazhnost_ot}
		</div>
	{/if}
{elseif $item_prop.key == 'klass'}
	<div class="prop-title prop-ul">{if $request_segment.id==1}Класс{else}Class{/if} {$catalog_item[$item_prop.key]}</div>
	{include file ="Admin/components/view_entity/comments_properties.tpl"}
{elseif $item_prop.key == 'tip_stroiteljstva'}
	<div class="prop-title prop-ul">{$catalog_item[$item_prop.key]}</div>
	{include file ="Admin/components/view_entity/comments_properties.tpl"}
{elseif $item_prop.key == 'vysota_potolkov_ot_offer' || $item_prop.key == 'vysota_potolkov_do_offer'}
	{if empty($prop_floor_v)}
		{?$prop_floor_v = true}
		<div class="prop-title">{if $request_segment.id==1}Высота потолков{else}Ceiling height{/if}</div>
		<div class="prop">
			{include file="Admin/components/view_entity/value_range_view.tpl" value_max=$catalog_item.vysota_potolkov_do_offer value_min=$catalog_item.vysota_potolkov_ot_offer}
			{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
		</div>
	{else}
		{?$prop_floor_v = false}
	{/if}
{elseif $item_prop.key == 'napravlenie'}
	{if empty($prop_direction)}
		{?$prop_direction=true}
		<div class="prop-title">{$item_prop.title}</div>
		<div class="prop">
			{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=direction key=val_id}
				<i class="i-direction {if $direction == "Север" || $direction == "North"}north{elseif $direction == "Восток" || $direction == "East"}east{elseif $direction == "Юг" || $direction == "South"}south{elseif $direction == "Запад" || $direction == "West"}west{/if}"></i>
				<span>{$direction}</span>
				{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id])}<div class="comment comment-min"><i class="i-comment"></i>{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id]}</div>{/if}
			{/foreach}
			{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
		</div>
	{/if}
{elseif $item_prop.key == 'etazh_offer'}
	{if empty($prop_etazh)}
		{?$prop_etazh=true}
		<div class="prop-title">{$item_prop.title}</div>
		<div class="prop">
			{implode(', ', $catalog_item.etazh_offer)}
			{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
		</div>
	{/if}
{elseif $item_prop.key == 'city' || $item_prop.key == 'adres'}
	{if empty($prop_adres)}
		{?$prop_adres=true}
		<div class="prop-title">{if $item_prop.key == 'adres'}{$item_prop.title}{/if}</div>
		<div class="prop">
			{?$first_prop=true}
			{if !empty($catalog_item.city)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.city}{/if}
			{if !empty($catalog_item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.adres}
				{?$prop_id = $catalog_item.properties.adres.property.id}
				{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
			{/if}
		</div>
	{/if}
{elseif $item_prop.key == 'status_gotovnosti' || $item_prop.key == 'god_postrojki' || $item_prop.key == 'planiruemaja_data_gotovnosti' || $item_prop.key == 'planiruemaja_data_gotovnosti_kvartal'}  
	{if empty($prop_status_gotov)}
		{?$prop_status_gotov=true}
		<div class="prop-title">{if $type_investment==true}{if $request_segment.id==1}Год постройки{else}Year of construction{/if}{else}{if $request_segment.id==1}Статус готовности{else}Building status{/if}{/if}</div>
		<div class="prop">
			{if !empty($catalog_item.status_gotovnosti) && ($catalog_item.status_gotovnosti == 'Строящийся' || $catalog_item.status_gotovnosti == 'Under construction')}
				{if $request_segment.id==1}В стадии строительства{else}Under construction{/if}{if !empty($catalog_item.planiruemaja_data_gotovnosti_kvartal)} — {$catalog_item.planiruemaja_data_gotovnosti_kvartal}{/if}
				{if !empty($catalog_item.planiruemaja_data_gotovnosti)} {$catalog_item.planiruemaja_data_gotovnosti}{/if}
			{elseif (!empty($catalog_item.status_gotovnosti) && ($catalog_item.status_gotovnosti == 'Готовый' || $catalog_item.status_gotovnosti == 'Ready')) || !empty($catalog_item.god_postrojki)}
				{if $request_segment.id==1}Построен{else}Ready{/if}{if !empty($catalog_item.god_postrojki)} {if $request_segment.id==1}в{else}in{/if} {$catalog_item.god_postrojki}{/if}
			{/if}
			{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
		</div>
	{/if}
{elseif $item_prop.key == 'parkovka' || $item_prop.key == 'parkovka_data'}
	{if !empty($catalog_item['properties'][$item_prop.key]['complete_value'])}
		{if empty($prop_parkovka)}
			{?$prop_parkovka=true}
			<div class="prop-title">{if $item_prop.key == 'parkovka'}{$item_prop.title}{/if}</div>
			<div class="prop">
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
									{if $request_segment.id==1}
										{if !empty($parking_mm)} на {$parking_mm|plural_form:'место':'места':'мест'}{/if}
									{else}
										{if !empty($parking_mm)} on {$parking_mm|plural_form:'place':'places':'places'}{/if}
									{/if}
									{?$first_prop=true}
									<div>
										{if !empty($parking_m_mm)}{if !$first_prop}, {else}{?$first_prop=false}{/if} {if $request_segment.id==1}1 место на {$parking_m_mm} м²{else}1 place on {$parking_m_mm} m²{/if}{/if}
										{if !empty($parking_rub_mm)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$parking_rub_mm} {if $request_segment.id==1}руб./мес. за место{else}rub./place a place{/if}{/if}
									</div>
								{/if}
							{/if}
							{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id])}<div class="comment"><i class="i-comment"></i>{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id]}</div>{/if}
						</li>
					{/foreach}
				</ul>
				{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
			</div>
		{/if}
	{/if}
{elseif $item_prop.key == 'buildings' || $item_prop.key == 'buildings_data'}
	{if !empty($catalog_item['properties'][$item_prop.key]['complete_value'])}
		{if empty($prop_buildings)}
			{?$prop_buildings=true}
			<div class="prop-title">{if $item_prop.key == 'buildings'}{$item_prop.title}{/if}</div>
			<div class="prop">
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
										{if !empty($buildings_count)}{if !$first_prop}, {else}{?$first_prop=false}{/if} {if $request_segment.id==1}{$buildings_count|plural_form:'здание':'здания':'зданий'}{else}{$buildings_count|plural_form:'building':'buildings':'buildings'}{/if}{/if}
										{if !empty($buildings_area)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{if $request_segment.id==1}общая площадь{else}total area{/if} {$buildings_area} {if $request_segment.id==1}м²{else}m²{/if}{/if}
									</div>
								{/if}
							{/if}
							{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id])}<div class="comment"><i class="i-comment"></i>{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id]}</div>{/if}
						</li>
					{/foreach}
				</ul>
				{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
			</div>
		{/if}
	{/if}
{else}
	<div class="prop-title{if $item_prop.set != 0} prop-ul{/if}">{$item_prop.title}</div>
	<div class="prop">
		{if $item_prop.set == 0}
			{?$val_id = $catalog_item['properties'][$item_prop.key]['val_id'][0]}
			{if $item_prop.data_type == 'text'}<div class="a-pre">{/if}
			{$catalog_item[$item_prop.key]}
			{if $item_prop.data_type == 'text'}</div>{/if}
			{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
		{else}
			<ul>
				{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
					<li>
						{$prop}
						{if !empty($catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id])}<div class="comment"><i class="i-comment"></i>{$catalog_item['properties'][$item_prop.key].comments.public[$val_id][$request_segment.id]}</div>{/if}
					</li>
				{/foreach}
			</ul>
			{*include file ="Admin/components/view_entity/comments_properties.tpl"*}
		{/if}
	</div>
{/if}
