{if !empty($document_root)}
	{?$path = $document_root . "/templates/project/img/svg/"}
{else}
	{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
{/if}
{if !empty($real_estate) || !empty($resale)}
	<div class="fav-header">
		<div class="fav-sort fav-row a-justify">
			<label class="f-cbx"><input type="checkbox" class="cbx" data-cbxclass="m-white" name="apartments[]" /></label>
			<a href="?order[date]=1" class="f-date{if empty($smarty.get.sort) || !empty($smarty.get.sort.date)} m-current{/if}">{$lang->get('Дата', 'Date')}</a>
			<a href="?order[title]=1" class="f-object m-border{if !empty($smarty.get.sort.title)} m-current{/if}">{$lang->get('Объект', 'Object')}</a>
			<div class="f-plan">{$lang->get('Планировка', 'Layout')}</div>
			<a href="?order[area_all]=1" class="f-area m-border{if !empty($smarty.get.sort.area_all)} m-current{/if}">{$lang->get('Площадь', 'Area')}</a>
			<a href="?order[floors]=1" class="f-level m-border{if !empty($smarty.get.sort.floors)} m-current{/if}">{$lang->get('Уровни', 'Levels')}</a>
			<a href="?order[bed_number]=1" class="f-bed m-border{if !empty($smarty.get.sort.bed_number)} m-current{/if}">{$lang->get('Спальни', 'Bedrooms')}</a>
			<a href="?order[wc_number]=1" class="f-wc m-border{if !empty($smarty.get.sort.wc_number)} m-current{/if}">{$lang->get('С/У', 'WC')}</a>
			<a href="?order[floor]=1" class="f-floor m-border{if !empty($smarty.get.sort.floor)} m-current{/if}">{$lang->get('Этаж', 'Floor')}</a>
			<a href="?order[price]=1" class="f-price m-border{if !empty($smarty.get.sort.price)} m-current{/if}">{$lang->get('Цена', 'Price')}</a>
			<div class="f-link"></div>
			<div class="f-del" data-href="{$url_prefix}/main/clearFavorites/?ajax=1" title="{$lang->get('Очистить список избранного', 'Clear favorites list')}">{fetch file=$path . "close.svg"}</div>
		</div>
		<div class="fav-order a-justify a-hidden">
			<div class="col1 unselect">
				<div class="cell-inner a-inline-cont">
					{fetch file=$path . "arrow.svg"}
					<div class="main">{$lang->get('Выбрано<br />квартир', 'Apartments<br />selected')|html}</div>
					<div class="num">0</div>
				</div>
			</div>
			<div class="col2">
				<div class="cell-inner">
					<a href="{$url_prefix}/favorites_request/" data-href="{$url_prefix}/favorites_request/" class="make-order btn m-sand ">{$lang->get('Отправить заявку по этим квартирам', 'Send request on thesу apartments')}</a>
				</div>
			</div>
			<div class="col3 unselect">
				<div class="cell-inner">
					<span class="a-link">{$lang->get('Отменить выбор', 'Cancel choise')}</span>
				</div>
			</div>
		</div>
	</div>
	{if !empty($real_estate)}
		<div class="fav-type">{$lang->get('Квартиры в строящихся домах', 'Apartments in new residential complexes')}</div>
		<ul class="fav-list">
			{foreach from=$real_estate item=item name=real_estate_list}
				{?$floor = $item->getParent()}
				{?$corpus = $floor->getParent()}
				{?$complex = $corpus->getParent()}
				<li class="fav-item fav-row a-justify i{iteration}{if !(iteration%2)} m-even{/if}">
					<label class="f-cbx">
						<div class="cell-inner">
							<input type="checkbox" class="cbx" name="apartments[]" value="{$item.id}" />
						</div>
					</label>
					<div class="f-date">
						<div class="cell-inner m-disable">
							{if !empty($item.favor_add_date)}
								{$item.favor_add_date|date_format:'%d'}<br />{$item.favor_add_date|date_format_lang:'%B':$request_segment.key|mb_substr:0:3}
							{else}
								—
							{/if}
						</div>
					</div>
					<div class="f-object">
						<a href="{$complex->getUrl()}" class="cell-inner a-inline-cont">
							{if !empty($complex.gallery)}
								{?$complex_cover = $complex.gallery->getCover()}
								{if !empty($complex_cover)}
									<img src="{$complex_cover->getUrl(140,140,true)}" alt="">
								{/if}
							{/if}
							<div><span>{$complex.title}</span></div>
						</a>
					</div>
					<div class="f-plan">
						{if !empty($item.shemes)}
							{?$shemes_cover = $item.shemes->getCover()}
							{if !empty($shemes_cover)}
								<a href="{$item->getUrl()}" class="cell-inner">
									<img src="{$shemes_cover->getUrl(140,140,true,true,array('gray'))}" alt="">
								</a>
							{/if}
						{else}
							<div class="cell-inner m-disable">—</div>
						{/if}
					</div>
					<div class="f-area">
						<div class="cell-inner">
							{if !empty($item.area_all)}
								{$item.area_all}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-level">
						<div class="cell-inner">
							{?$floors = (!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? ' '|explode:$item.properties.floors.real_value : NULL}
							{if !empty($floors)}
								{$floors[0]}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-bed">
						<div class="cell-inner">
							{if !empty($item.bed_number)}
								{$item.bed_number}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div  class="f-wc">
						<div class="cell-inner">
							{if !empty($item.wc_number)}
								{$item.wc_number}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-floor">
						<div class="cell-inner">
							{if !empty($floor.title)}
								{$floor.title}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-price">
						<div class="cell-inner">
							{if !empty($item.price)}
								{$item.price}
							{else}
								<span class="m-disable">{$lang->get('По запросу', 'On request')}</span>
							{/if}
						</div>
					</div>
					<div class="f-link">
						<a href="{$item->getUrl()}" class="cell-inner btn m-sand">{$lang->get('В деталях', 'In detail')}</a>
					</div>
					<div class="f-del" title="Удалить из избранного" data-id="{$item.id}" data-url="{$url_prefix}/real-estate">
						<div class="cell-inner">
							{fetch file=$path . "close.svg"}
						</div>
					</div>
				</li>
			{/foreach}
		</ul>
	{/if}
	
	{if !empty($resale)}
		<div class="fav-type">{$lang->get('Квартиры на вторичном рынке', 'Apartments for resale')}</div>
		<ul class="fav-list">
			{foreach from=$resale item=item name=resale_list}
				<li class="fav-item fav-row a-justify{if !(iteration%2)} m-even{/if}">
					<label class="f-cbx">
						<div class="cell-inner">
							<input type="checkbox" class="cbx" name="apartments_resale[]" value="{$item.id}" />
						</div>
					</label>
					<div class="f-date">
						<div class="cell-inner m-disable">
							{if !empty($item.favor_add_date)}
								{$item.favor_add_date|date_format:'%d'}<br />{$item.favor_add_date|date_format_lang:'%B':$request_segment.key|mb_substr:0:3}
							{else}
								—
							{/if}
						</div>
					</div>
					<div class="f-object">
						<a href="{$item->getUrl()}" class="cell-inner a-inline-cont">
							{if !empty($item.gallery)}
								{?$item_cover = $item.gallery->getCover()}
								{if !empty($item_cover)}
									<img src="{$item_cover->getUrl(140,140,true)}" alt="">
								{/if}
							{/if}
							<div><span>{$item.title}</span></div>
						</a>
					</div>
					<div class="f-plan">
						{if !empty($item.shemes)}
							{?$shemes_cover = $item.shemes->getCover()}
							{if !empty($shemes_cover)}
								<a href="{$item->getUrl()}" class="cell-inner">
									<img src="{$shemes_cover->getUrl(140,140,true,true,array('gray'))}" alt="">
								</a>
							{/if}
						{else}
							<div class="cell-inner m-disable">—</div>
						{/if}
					</div>
					<div class="f-area">
						<div class="cell-inner">
							{if !empty($item.area_all)}
								{$item.area_all}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-level">
						<div class="cell-inner">
							{?$floors = (!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? ' '|explode:$item.properties.floors.real_value : NULL}
							{if !empty($floors)}
								{$floors[0]}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-bed">
						<div class="cell-inner">
							{if !empty($item.bed_number)}
								{$item.bed_number}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div  class="f-wc">
						<div class="cell-inner">
							{if !empty($item.wc_number)}
								{$item.wc_number}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-floor">
						<div class="cell-inner">
							{if !empty($item.floor)}
								{$item.floor}{if !empty($item.number_storeys)}/{$item.number_storeys}{/if}
							{else}
								<span class="m-disable">—</span>
							{/if}
						</div>
					</div>
					<div class="f-price">
						<div class="cell-inner">
							{if !empty($item.price)}
								{$item.price}
							{else}
								<span class="m-disable">{$lang->get('По запросу', '')}</span>
							{/if}
						</div>
					</div>
					<div class="f-link">
						<a href="{$item->getUrl()}" class="cell-inner btn m-sand">{$lang->get('В деталях', 'In detail')}</a>
					</div>
					<div class="f-del" title="Удалить из избранного" data-id="{$item.id}" data-url="{$url_prefix}/resale">
						<div class="cell-inner">
							{fetch file=$path . "close.svg"}
						</div>
					</div>
				</li>
			{/foreach}
		</ul>
	{/if}
	<div class="bowtie-wrap row">
		<div class="w2">
			<div class="title">{$lang->get('Хотите <b>найти</b> себе<br>идеальную квартиру без усилий?', 'Trying <b>to find</b> yourself<br>the perfect apartment without effort?')|html}</div>
			<div class="descr">{$lang->get('Опытные специалисты в области недвижимости помогут Вам выбрать оптимальный вариант.', 'Qualified real estate professionals will help you to choose the best one.')}</div>
			<a href="{$url_prefix}/selection/" class="btn m-light-magenta m-vw">{$lang->get('Персональный подбор', 'PERSONAL SELECTION')}</a>
		</div>
		<div class="w2">
			<div class="title">{$lang->get('Хотите <b>продать</b> квартиру<br>премиум-класса выгодно и безопасно?', 'Want <b>to sell</b> luxury apartment safely and with real profit?')|html}</div>
			<div class="descr">{$lang->get('Мы оперативно найдем Вам покупателей и возьмем на себя юридическое сопровождение сделки.', 'We’ll find buyers in the shortest possible time and provide legal support of transactions.')}</div>
			<a href="{$url_prefix}/owner/" class="btn m-light-magenta m-vw">{$lang->get('Продать квартиру с М16', 'SELL APARTMENT WITH M16')}</a>
		</div>
		<div class="bow_tie"></div>
	</div>
	
{else}
	
	<div class="fav-empty">
		<div class="main">{$lang->get('К нашему великому сожалению', 'Unfortunatly')}</div>
		<div class="title"><span>{$lang->get('В избранном пока нет квартир', 'There\'re no apartments in your favorites list')}</span></div>
		<div class="descr">{$lang->get('Чтобы добавить квартиру в этот список, нажмите на значок', 'To add an apartment to this list, click')} {fetch file=$path . "favorite.svg"} {$lang->get('рядом с интересующими квартирами в каталоге', 'icon next to the appartments of interest')}</div>
	</div>
	{if !empty($site_config.real_estate_consultant)}
		{?$consultants = $site_config.real_estate_consultant}
	{else}
		{?$consultants = null}
	{/if}
	{if !empty($consultants)}
		<div class="consultant row a-justify">
			<div class="info w2">
				<div class="title">{$lang->get('Трудно <span>найти</span><br />квартиру вашей мечты?', 'Hard <span>to find</span><br />the apartment of your dreams?')|html}</div>
				<div class="small-descr">{$lang->get('Позвоните по телефону', 'Call us')}</div>
				{if !empty($contacts.phone)}<div class="phone">{$contacts.phone}</div>{/if}
				<a href="" class="btn m-magenta-fill">{$lang->get('Оставить заявку', 'Send your request')}</a>
				<div class="slash"></div>
			</div>
			{foreach from=$consultants item=consultant name=cons}
				<div class="person w1">
					{if !empty($consultant.photo)}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
					{if !empty($consultant.title)}<div class="name">{$consultant.title}</div>{/if}
					{if !empty($consultant.email)}<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}
					{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
				</div>
			{/foreach}
		</div>
	{/if}
{/if}
