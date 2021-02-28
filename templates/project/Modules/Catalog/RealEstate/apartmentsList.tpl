{if empty($path)}
	{if !empty($document_root)}
		{?$path = $document_root . "/templates/project/img/svg/"}
	{else}
		{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
	{/if}
{/if}
{if !empty($flats)}
	{foreach from=$flats item=flat name=name_n}
		{?$floor = $flat->getParent()}
		{?$corpus = $floor->getParent()}
		{if !empty($flat.shemes)}{?$gallery = $flat.shemes->getImages()}{/if}
		{?$url = !empty($flat->getUrl()) ? $flat->getUrl() : null}
		{?$cover = !empty($flat.shemes) ? $flat.shemes->getCover() : null}
		<a href="{if !empty($url)}{$url}{/if}" class="flat-wrap item-wrap">
			{if !empty($item.special_offer)}
				<div class="top">
					{foreach from=$item.special_offer item=special_offer}
						<div class="skew m-sand-skew">{$special_offer}</div>
					{/foreach}
				</div>
			{/if}

            {? $bg_url = '' }
            {? $bg_color = 'background-color: inherit;' }
            {if !empty($cover)}
                {? $bg_url = 'background:url(' . $cover->getUrl(275,250, false, false, array('gray', 'brit|0')) . ') center no-repeat;'}
                {? $bg_color = '' }

			{else}

				{? $bg_url = "background:url('/img/not_plane_min.jpg') center no-repeat;"}

            {/if}
			

            <div class="cover-padding" style="{$bg_color}">
                <div class="cover" style="{$bg_url} background-size:contain; {$bg_color}"></div>
            </div>



            {*
            {if !empty($cover)}
				<div class="cover-padding">
					<div class="cover" style="background:url({$cover->getUrl(275,250, false, false, array('gray', 'brit|0'))}) center no-repeat; background-size:contain;"></div>
				</div>
			{/if}

            *}



			<div class='params'>
				<div class="main">{$lang->get('Квартира', 'Appartment')}</div>
				<div class="title"><span>{$lang->get($flat.bed_number|plural_form:'спальня':'спальни':'спален', $flat.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}</span></div>
				<div class="descr">{if !empty($corpus.title)}{$lang->get('Корпус', 'Building')} {$corpus.title}{/if}{if !empty($corpus.title) && !empty($floor.title)} • {/if}{$lang->get('Этаж', 'Floor')} {$floor.title}</div>
				<div class="area"><i>~</i>{$flat.properties.area_all.value|round} <span>{$lang->get('м', 'm')}<sup>2</sup></span></div>
				{if !empty($item.overhang)}
					<div class="small-descr">
						{$lang->get('Есть', 'With')}
						{foreach from=$item.overhang item=overhang name=overhang_n}
							{$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
						{/foreach}
					</div>
				{/if}
				<div class="bottom"><div class="btn m-sand">{$lang->get('В деталях', 'In detail')}</div></div>
				<div class="favorite{if $flat.in_favorites} m-added{/if}" {if !empty($flat.id) && !empty($moduleUrl)}data-id="{$flat.id}" data-url="{$moduleUrl}"{/if}>{fetch file=$path . "favorite.svg"}</div>
			</div>
		</a>
	{/foreach}

	{?$per_page = 20}
	{if !empty($count)}
		{?$flats_page = empty($smarty.get.page)? 1 : $smarty.get.page}
		{?$flats_rest = $count - $per_page*$flats_page}
		{if $flats_rest > 0}
			{if $flats_rest > $per_page}{?$flats_rest = $per_page}{/if}
			<div class="more-row a-center">
				<div class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if} data-count="{$count}">
					{$lang->get('Показать еще ' . ($flats_rest|plural_form:'квартиру':'квартиры':'квартир'), 'Show ' . $flats_rest . ' more ' . ($flats_rest|plural_form:'appartment':'appartments':'appartments':false))}
				</div>
			</div>
		{/if}
	{/if}
{else}
	<div class="empty-result">
		<div class="main">К нашему великому сожалению</div>
		<div class="title"><span>Вариантов с заданными параметрами<br />не найдено</span></div>
	</div>
{/if}
