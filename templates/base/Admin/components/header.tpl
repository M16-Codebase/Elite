<header class="page-header">
	<div class="float-header">
		<div class="page-center a-justify">
			<div class="aside-col">
				<a href="/site/" class="header-logo">
					<img src="{if !empty($site_config.site_logo)}{$site_config.site_logo->getUrl()}{/if}" alt="logo">
				</a>
			</div>
			<div class="main-col row">
				<div class="w4 a-inline-cont">
					<div class="open-menu header-col">
						<i class="icon-menu"></i>
					</div>
					<div class="header-user dropdown header-col">
						<div class="dropdown-toggle">
							<i class="icon-user"></i> {$account->getUser()->getEmail()} 
							{?$user_role = $account->getUser()->getRole()}
							{if !empty($roles[$user_role])}
								<span> — {$roles[$user_role]['title']}</span>
							{/if}
						</div>
						<ul class="dropdown-menu a-hidden">
							<li><a href="/logout/"><i class="action-icon icon-exit"></i>Выйти</a></li>
						</ul>
					</div>
				</div>
				<div class="w1">
					{*if empty($favorite_vars)}
						{?$favorite_vars = array()}
						{?$favorites = $account->getFavorites()}
						{if !empty($favorites.items)}
							{foreach from=$favorites.items item=fav_i}
								{foreach from=$fav_i.variants key=fav_vid item=fav_variant}
									{if !empty($fav_variant)}{?$favorite_vars[$fav_vid] = 1}{/if}
								{/foreach}
							{/foreach}
						{/if}
					{/if}
					<a href="/catalog-view/favorites/" class="header-favorites header-col">
						<i class="icon-favourites"></i>
						<span>{count($favorite_vars)}</span>
					</a>*}
				</div>
				<div class="w3">
					
				</div>
				<div class="w1">
					<div class="header-col">
						<a href="{if !empty($site_link)}{$site_link}{else}/{/if}" class="to-site m-ru{if empty($site_link)} a-hidden{/if}">RU</a>
						{*<a href="{if !empty($site_link_en)}{$site_link_en}{elseif !empty($site_link)}/en{$site_link}{else}/en/{/if}" class="to-site m-en">EN</a>*}
					</div>
				</div>
				<div class="w3">
					<form action="/catalog-view/search/" method="GET" class="header-search header-col">
						<div class="search-cont">
							<input type="text" name="search" placeholder="Поиск" class="search-input" />
							<button class="search-submit">
								<i class="icon-search"></i>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>	
	</div>
</header>