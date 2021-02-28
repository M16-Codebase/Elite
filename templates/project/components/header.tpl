{if empty($no_header)}
<div class="main-loader">
	<div class="loader-inner ball-pulse">
		{fetch file=$path . "logo_m16.svg"}
	</div>
</div>

<header class="page-header">
	<div class="white-cover"></div>
	<div class="header-float">

		<div class="page-center row header-big dropdown-search" data-hoverable="0">
			<div class="left-menu">
				<ul>
					<li class="elite">
					<a href="{$url_prefix}/">{$lang->get('Элитная недвижимость', 'Elite real estate')}<br>
					<span>{$lang->get('в Санкт-Петербурге', 'in St.Petersburg')}</a></span>
					</li>
					<li><a href="{$url_prefix}/real-estate/"{if $moduleUrl == 'real-estate'} class="active"{/if}>{$lang->get('Строящаяся', 'New objects')}</a></li>
					<li><a href="{$url_prefix}/resale/"{if $moduleUrl == 'resale'} class="active"{/if}>{$lang->get('Вторичная', 'Resale')}</a></li>
                    <li><a href="{$url_prefix}/residential/"{if $moduleUrl == 'residential'} class="active"{/if}>{$lang->get('Загородная', 'Residential')}</a></li>
					<li><a href="{$url_prefix}/arenda/"{if $moduleUrl == 'arenda'} class="active"{/if}>{$lang->get('Аренда', 'Rent')}</a></li>
					<li class="search dropdown-toggle">{fetch file=$path . "search.svg"}</li>
				</ul>
			</div>
			<div class="search-wrap dropdown-menu a-hidden">
				{?$checkString = time()}
				{?$checkStringSalt = $checkString . $hash_salt_string}
				<form action="{$url_prefix}/realtysearch/" method="GET" class="request-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
				<div class="center">
						<div class="s-icon">{fetch file=$path . "search.svg"}</div>
						<input id="search" type="text" placeholder="{$lang->get('ПОИСК ПО САЙТУ', 'SEARCH')}" data-url="{$url_prefix}/main/realtysearchautocomplete/" autocomplete="off" name="phrase">
						<div class="close">{fetch file=$path . "close.svg"}</div>
						<div class="search-loader">
							<div class="loader-inner ball-pulse">
								<div></div>
								<div></div>
								<div></div>
							</div>
						</div>
						<button class="btn m-dark-sand-fill js-search">{$lang->get('Искать', 'Search')}</button>
				</div>
				<div id="search-autocomplete-result"></div>
				</form>
			</div>
			<a href="{$url_prefix}/" class="logo">{fetch file=$path . "logo_m16.svg"}</a>
			<div class="right-menu" itemscope itemtype="http://schema.org/Organization">
				<ul>
					<li><a href="{$url_prefix}/company/">{$lang->get('О нас', 'About')}</a></li>
					<li><a href="{$url_prefix}/service/">{$lang->get('Услуги', 'Service')}</a></li>
					<li><a href="{$url_prefix}/top16/">{$lang->get('Топ-16', 'TOP-16')}</a></li>
					<li><a href="https://m16-elite.ru/top-100/">{$lang->get('Топ-100', 'TOP-100')}</a></li>
					<li><a href="{$url_prefix}/district/">{$lang->get('Районы', 'Districts')}</a></li>
					<li><a href="{$url_prefix}/contacts/">{$lang->get('Контакты', 'Contacts')}</a></li>
			
					{if isset($page_url)}
						{if (strpos($page_url, '/en/') !== false)}
							<li class="segment"><a href="https://m16-elite.ru/{substr($page_url,24)}"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></li>
						{/if}
						{if (strpos($page_url, '/en/') == false)}
							<li class="segment"><a href="https://m16-elite.ru/en{substr($page_url,20)}"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></li>
						{/if}
					{/if}
					
					{if !isset($page_url)}
						{$page_url=" "}
						{if (strpos($page_url, '/en/') !== false)}
							<li class="segment"><a href="https://m16-elite.ru/"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></li>
						{/if}
						{if (strpos($page_url, '/en/') == false)}
							<li class="segment"><a href="https://m16-elite.ru/en/"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></li>
						{/if}
					{/if}
					
					{if !empty($contacts.display_phone)}
						<li class="phone roistat_phone" itemprop="telephone">
							{if $device_type == 'phone'}<a href="tel:{$contacts.display_phone}">{/if}{$contacts.display_phone}{if $device_type == 'phone'}</a>{/if}
						</li>
					{/if}
				</ul>
			</div>


			<noindex>
				<div id="mobile_header">

					<div class="burgerBtn" id="mob_all"><span></span><span></span><span></span></div>

					<div class="search dropdown-toggle">{fetch file=$path . "search.svg"}</div>

					<a href="{$url_prefix}/" class="logomob">{fetch file=$path . "logo_m16.svg"}</a>

					{if isset($page_url)}
						{if (strpos($page_url, '/en/') !== false)}
							<div class="segment"><a href="https://m16-elite.ru/{substr($page_url,24)}"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></div>
						{/if}
						{if (strpos($page_url, '/en/') == false)}
							<div class="segment"><a href="https://m16-elite.ru/en{substr($page_url,20)}"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></div>
						{/if}
					{/if}

					{if !isset($page_url)}
						{$page_url=" "}
						{if (strpos($page_url, '/en/') !== false)}
							<div class="segment"><a href="https://m16-elite.ru/"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></div>
						{/if}
						{if (strpos($page_url, '/en/') == false)}
							<div class="segment"><a href="https://m16-elite.ru/en/"><span>{$request_segment.key == 'ru' ? 'EN' : 'РУ'}</span></a></div>
						{/if}
					{/if}

					{if !empty($contacts.display_phone)}
						<div class="phone roistat_phone">
							{if $device_type == 'phone'}<a href="tel:{$contacts.display_phone}">{/if}{$contacts.display_phone}{if $device_type == 'phone'}</a>{/if}
						</div>
					{/if}

					<div class="menu_one" id="mm_one">
						<div class="zag_one">
							<div class="burgerBtn" id="mob_one"><span></span><span></span><span></span></div>
							<div class="zag">{$lang->get('недвижимость', 'real estate')}</div>
						</div>
						<div id="menu_one">
							<ul>
								<li><a href="{$url_prefix}/real-estate/"{if $moduleUrl == 'real-estate'} class="active"{/if}>{$lang->get('Строящаяся', 'New objects')}</a></li>
								<li><a href="{$url_prefix}/resale/"{if $moduleUrl == 'resale'} class="active"{/if}>{$lang->get('Вторичная', 'Resale')}</a></li>
								<li><a href="{$url_prefix}/residential/"{if $moduleUrl == 'residential'} class="active"{/if}>{$lang->get('Загородная', 'Residential')}</a></li>
								<li><a href="{$url_prefix}/arenda/"{if $moduleUrl == 'arenda'} class="active"{/if}>{$lang->get('Аренда', 'Rent')}</a></li>
							</ul>
						</div>
					</div>
					<div class="menu_one" id="mm_two">
						<div class="zag_two">
							<div class="burgerBtn" id="mob_two"><span></span><span></span><span></span></div>
							<div class="zag">{$lang->get('о компании', 'about')}</div>
						</div>
						<div id="menu_two">
							<ul>
								<li><a href="{$url_prefix}/company/">{$lang->get('О нас', 'About')}</a></li>
								<li><a href="{$url_prefix}/service/">{$lang->get('Услуги', 'Service')}</a></li>
								<li><a href="{$url_prefix}/top16/">{$lang->get('Топ-16', 'TOP-16')}</a></li>
								<li><a href="https://m16-elite.ru/top-100/">{$lang->get('Топ-100', 'TOP-100')}</a></li>
								<li><a href="{$url_prefix}/district/">{$lang->get('Районы', 'Districts')}</a></li>
								<li><a href="{$url_prefix}/contacts/">{$lang->get('Контакты', 'Contacts')}</a></li>
							</ul>
						</div>
					</div>

				</div>
			</noindex>



		</div>
	</div>
</header>
{/if}
{?$gravity = array(
		'TL' => 'top left',
		'T' => 'top center',
		'TR' => 'top right',
		'L' => 'left center',
		'C' => 'center center',
		'R' => 'right center',
		'BL' => 'bottom left',
		'B' => 'bottom center',
		'BR' => 'bottom right',
	)}