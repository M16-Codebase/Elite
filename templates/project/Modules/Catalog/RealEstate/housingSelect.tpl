<div class="scheme-inner" data-title="{$lang->get('Выбор корпуса', 'Choose building')}">
	{if !empty($complex.sheme_get)}
		<div class="scheme-img">
			<img src="{$complex.properties.sheme_get.complete_value->getUrl()}" alt="{$complex.title}" />
		</div>
		<div class="scheme-header">
			<div class="default-scheme">
				<div class="main m-vw">{$lang->get('Наведите указатель на корпус, квартира в котором вас интересует', 'Place your mouse over the building where are apartments you are interested in')}</div>
			</div>
			{foreach from=$housing item=corpus}
				{if $corpus.properties.sheme_coords.set == 1}
					{?$poly_coords = implode('|', $corpus.sheme_coords)}
				{else}
					{?$poly_coords = $corpus.sheme_coords}
				{/if}
				<div class="corpus-item scheme-item item-{$corpus.id} a-inline-cont a-hidden" 
						data-type="corpus"
						data-id="{$corpus.id}" 
						data-title="{$lang->get('Выбор этажа', 'Choose floor')}"
						data-coords="{$poly_coords}"
						data-url="{$url_prefix}/real-estate/floorSelect/"
						data-status="{$corpus.properties.state.value_key}"
						{if empty($corpus.flats_for_sale_count)} data-disabled="1"{/if}>
					<div class="col1 a-inline-cont">
						{if !empty($corpus.title)}
							<div class="main m-vw">{$lang->get('Корпус', 'Building')}</div>
							<div class="title m-short">{$corpus.title}</div>
						{/if}
					</div>
					<div class="col2 a-inline-cont">
						<div class="main m-vw">{$lang->get('Квартир<br />в продаже', 'Apartments<br />for sale')|html}</div>
						<div class="title{if empty($corpus.flats_for_sale_count)} m-gray{/if}">{$corpus.flats_for_sale_count} /</div>
						<div class="bedrooms a-inline-cont">
							{section loop=5 name=bedrooms}
								<div{if !empty($bedroom_count_filters[$corpus.id][iteration])} class="m-active"{/if}>
									{iteration}{iteration == 5 ? '+' : ''}
								</div>
								{if iteration == 3}<br />{/if}
							{/section}
						</div>
					</div>
					{if !empty($area_range[$corpus.id])}
						<div class="col3 a-inline-cont">
							<div class="main m-vw">{$lang->get('Площади<br />квартир', 'Flats area')|html}</div>
							<div class="title">
								{?$min_area = $area_range[$corpus.id]['area_min']|round}
								{?$max_area = $area_range[$corpus.id]['area_max']|round}
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
	{else}
		<div class="no-scheme title a-center">{$lang->get('Схема не загружена', 'The scheme is not loaded')}</div>
	{/if}
</div>