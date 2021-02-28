{if empty($path)}
	{if !empty($document_root)}
		{?$path = $document_root . "/templates/project/img/svg/"}
	{else}
		{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
	{/if}
{/if}
<div class="scheme-inner" data-title="{$lang->get('Выбор квартиры', 'Choose apartment')}" data-floor="{$floor.title}">
	{if !empty($floor.sheme_get)}
		{?$housing = $floor->getParent()}
		{?$complex = $housing->getParent()}
		{?$floorType = $floor->getType()}
		{?$floors = $housing->getChildren($floorType.id)}
		{?$floorsIds = array()}
		{?$maxFloor = 0}
		{foreach from=$floors item=f}
			{if $f.floor_number > $maxFloor}
				{?$maxFloor = $f.floor_number}
			{/if}
			{if !empty($f.sheme_get)}
				{?$floorsIds[$f.floor_number] = $f.id}
			{/if}
		{/foreach}
		{?$floorsArrows = array(null, null)}
		{if $floor.floor_number > 1}
			{?$i = 0}
			{while ++$i < $floor.floor_number}
				{if !empty($floorsIds[$i])}
					{?$floorsArrows[0] = $floorsIds[$i]}
				{/if}
			{/while}
		{/if}
		{if $floor.floor_number < $maxFloor}
			{?$j = $maxFloor+1}
			{while --$j > $floor.floor_number}
				{if !empty($floorsIds[$j])}
					{?$floorsArrows[1] = $floorsIds[$j]}
				{/if}
			{/while}
		{/if}
		<div class="back-btn-data" data-url="{$url_prefix}/real-estate/floorSelect/" data-id="{$housing.id}" data-title="К выбору этажа"></div>
		<div class="scheme-img m-white">
			<img src="{$floor.properties.sheme_get.complete_value->getUrl()}" alt="{$floor.title}" />
		</div>
		<div class="scheme-header a-inline-cont">
			<div class="col-small a-inline-cont{if empty($single_housing)} back-scheme{/if}" data-url="{$url_prefix}/real-estate/housingSelect/" data-id="{$complex.id}" title="{$lang->get('К выбору корпуса', 'To the choice of building')}">
				{if !empty($housing.title)}
					<div class="main m-vw">{$lang->get('Корпус', 'Building')}</div>
					<div class="title m-short">{$housing.title}</div>
				{/if}
			</div>
			<div class="col-small a-inline-cont m-floor">
				<div class="main m-vw">{$lang->get('Этаж', 'Floor')}</div>
				<div class="arrows">
					<div class="arrow m-up{if empty($floorsArrows[1])} m-disabled{else}" data-id="{$floorsArrows[1]}{/if}" data-url="{$url_prefix}/real-estate/apartSelect/">{fetch file=$path . "arrow2.svg"}</div>
					<div class="arrow m-down{if empty($floorsArrows[0])} m-disabled{else}" data-id="{$floorsArrows[0]}{/if}" data-url="{$url_prefix}/real-estate/apartSelect/">{fetch file=$path . "arrow2.svg"}</div>
				</div>
				<div class="title">{$floor.title}</div>
			</div>
			<div class="col-large">
				<div class="default-scheme{if !count($apartments)} m-empty a-inline-cont{/if}">
					{if count($apartments)}
						<div class="main m-vw">{$lang->get('Наведите указатель на интересующую квартиру на плане', 'Place your mouse over the apartments you are interested in on the plan')}</div>
					{else}
						<div class="main m-vw">{$lang->get('Квартиры<br />на этаже', 'Apartments<br />on floor')|html}</div>
						<div class="title m-gray">{$lang->get('НЕ ПРОДАЮТСЯ', 'NOT FOR SALE')}</div>
					{/if}
				</div>
				{foreach from=$apartments item=apt}
					{if $apt.properties.sheme_coords.set == 1}
						{?$poly_coords = implode('|', $apt.sheme_coords)}
					{else}
						{?$poly_coords = $apt.sheme_coords}
					{/if}
					<div class="apt-item scheme-item item-{$apt.id} a-inline-cont a-hidden" 
							data-type="flat"
							data-id="{$apt.id}" 
							data-coords="{$poly_coords}"
							data-url="{$apt->getUrl()}" 
							data-status="{$apt.properties.state.value_key}">
						<div class="col1 a-inline-cont">
							<div class="main m-vw">{$lang->get('Количество<br />спален / санузлов', 'Number<br />of bedrooms / bathrooms ')|html}</div>
							<div class="title">{$apt.bed_number}/{$apt.wc_number}</div>
						</div>
						<div class="col2 a-inline-cont">
							<div class="main m-vw">{$lang->get('Площадь<br />квартиры', 'Apartment<br />area')|html}</div>
							<div class="title">{$apt.area_all}</div>
						</div>
					</div>
				{/foreach}
			</div>
		</div>
	{else}
		<div class="back-btn-data" data-url="{$url_prefix}/real-estate/floorSelect/" data-id="{$housing.id}" data-title="К выбору этажа"></div>
		<div class="no-scheme title a-center">{$lang->get('Схема не загружена', 'The scheme is not loaded')}</div>
	{/if}
</div>