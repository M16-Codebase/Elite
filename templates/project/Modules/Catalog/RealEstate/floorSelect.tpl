<div class="scheme-inner" data-title="{$lang->get('Выбор этажа', 'Choose floor')}">
	{if !empty($housing.sheme_get)}
		{if empty($single_housing)}
			<div class="back-btn-data" data-url="{$url_prefix}/real-estate/housingSelect/" data-id="{$complex.id}" data-title="К выбору корпуса"></div>
		{/if}
		<div class="scheme-img">
			<img src="{$housing.properties.sheme_get.complete_value->getUrl()}" alt="{$housing.title}" />
		</div>
		<div class="scheme-header a-inline-cont">
			<div class="col-small a-inline-cont{if empty($single_housing)} back-scheme{/if}" data-url="{$url_prefix}/real-estate/housingSelect/" data-id="{$complex.id}" title="{$lang->get('К выбору корпуса', 'To the choice of building')}">
				{if !empty($housing.title)}
					<div class="main m-vw">{$lang->get('Корпус', 'Building')}</div>
					<div class="title m-short">{$housing.title}</div>
				{/if}
			</div>
			<div class="col-big">
				<div class="default-scheme{if !count($floors)} m-empty a-inline-cont{/if}">
					{if count($floors)}
						<div class="main m-vw">{$lang->get('Наведите указатель на этаж, квартира в котором вас интересует', 'Place your mouse over the floor where are apartments you are interested in')}</div>
					{else}
						<div class="main m-vw">{$lang->get('Квартиры<br />в корпусе', 'Apartments<br />in building')|html}</div>
						<div class="title m-gray">{$lang->get('НЕ ПРОДАЮТСЯ', 'NOT FOR SALE')}</div>
					{/if}
				</div>
				{foreach from=$floors item=floor}
					{if $floor.properties.sheme_coords.set == 1}
						{?$poly_coords = implode('|', $floor.sheme_coords)}
					{else}
						{?$poly_coords = $floor.sheme_coords}
					{/if}
					<div class="floor-item scheme-item item-{$floor.id} a-inline-cont a-hidden" 
							data-type="floor" 
							data-id="{$floor.id}" 
							data-num="{$floor.title}" 
							data-coords="{$poly_coords}"
							data-title="{$lang->get('Выбор квартиры', 'Choose apartment')}"
							data-url="{$url_prefix}/real-estate/apartSelect/"
							data-status="{if !empty($floor.sheme_get)}sale{else}not{/if}">
						<div class="col1 a-inline-cont"  style="width: 15%;">
							<div class="main m-vw">{$lang->get('Этаж', 'Floor')}</div>
							<div class="title">{$floor.title}</div>
						</div>
						<div class="col2 a-inline-cont" style="width: 35%;">
							<div class="main m-vw">{$lang->get('Квартир<br />в продаже', 'Apartments<br />for sale')|html}</div>
							<div class="title{if empty($floor.flats_for_sale_count)} m-gray{/if}">{$floor.flats_for_sale_count} /</div>
							<div class="bedrooms a-inline-cont">
								{section loop=5 name=bedrooms}
									<div{if !empty($bedroom_count_filters[$floor.id][iteration])} class="m-active"{/if}>
										{iteration}{iteration == 5 ? '+' : ''}
									</div>
									{if iteration == 3}<br />{/if}
								{/section}
							</div>
						</div>
						{if !empty($area_range[$floor.id])}
							<div class="col3 a-inline-cont">
								<div class="main m-vw">{$lang->get('Площади<br />квартир', 'Flats area')|html}</div>
								<div class="title">
									{?$min_area = $area_range[$floor.id]['area_min']|round}
									{?$max_area = $area_range[$floor.id]['area_max']|round}
									{if !empty($min_area) && !empty($max_area) && ($min_area < $max_area)}
										{$min_area}—{$max_area}<i>{$lang->get('м²', 'm²')}</i>
									{else}
										{!empty($min_area) ? $min_area : $max_area}<i>{$lang->get('м²', 'm²')}</i>
									{/if}
								</div>
							</div>
						{/if}
					</div>
				{/foreach}
			</div>
		</div>
	{else}
		{if empty($single_housing)}
			<div class="back-btn-data" data-url="{$url_prefix}/real-estate/housingSelect/" data-id="{$complex.id}" data-title="К выбору корпуса"></div>
		{/if}
		<div class="no-scheme title a-center">{$lang->get('Схема не загружена', 'The scheme is not loaded')}</div>
	{/if}
</div>