<div class="cabinet-page bonus-page justify">
	<aside class="aside-catalog-menu aside-col m-scrollable">
		<div class="white-block-inner content-white-block vert-menu">
			{*<div class="aside-catalog-select">*}
				{*{include file="components/catalog-menu.tpl"}*}
			{*</div>*}
			{include file="Modules/Profile/My/cabinet-menu.tpl"}
		</div>
		{*<div class="white-block-inner benefits-cont content-white-block">*}
			{*{include file="components/benefits.tpl"}*}
		{*</div>*}
	</aside>
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{include file="components/breadcrumb.tpl" other_link=array('Личный кабинет' => array('link'=>'/profile/',  'Личные данные' => '/profile/', 'Заказы' => '/profile/orders/', 'Бонусный счет' => '/profile/bonus/', 'Отзывы' => '/profile/reviews/'))}
				<h1>Бонусный счет</h1>
				<div class="bonus justify">
					<div class="bonus-info col1">
						<div class="bonus-info-top">
							<div class="bonus-info-title">Текущий баланс</div>
							{?$current_user = $account->getUser()}
							<span class="bonus-cont">{$current_user.bonus}<span class="currency">{$current_user.bonus|plural_form:'балл':'балла':'баллов':false}</span></span>
						</div>
						<div class="bonus-info-bottom">							
							<div class="bonus-info-title">Бонусная программа</div>
							{if $current_user.person_type == 'org'}
								<a href="/main/programs/">«Профи-Бонус»</a>
							{else}
								<a href="/main/programs/">«Мастер-Бонус»</a>
							{/if}
						</div>
					</div>
					<div class="col3">
						{*{include file="components/invite-block.tpl"}*}
					</div>
				</div>
			</div>
		</div>	
		{if !empty($post)}
			<div class="info-block content-white-block">
				{if !empty($post.title)}
					<h1>{$post.title}</h1>
				{/if}
				{if !empty($post.annotation)}
					<div class="annotation">{$post.annotation}</div>
				{/if}
				{if !empty($post.text)}
					{$post.text|html}
				{/if}
			</div>
		{/if}
	</div>
</div>
{*{include file="components/brands.tpl"}*}
{*{include file="components/news-block.tpl"}*}
