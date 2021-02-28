<div class="cabinet-page justify">
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
				<h1>Отзывы</h1>
			</div>
			<div class="grey-block-inner">
				{if !empty($reviews)}
					<ul class="review-list item-tab-list">
						{?$status_titles = array('new'=>'Новый', 'approved'=>'Принят', 'decline'=>'Отклонен')}
						{foreach from=$reviews item=review}
							<li class="review-item justify item-tab-listitem {$review.status}">
								<div class="review-status">{$status_titles[$review.status]}</div>
								<div class="col1">
									<div class="cover">
										{?$rev_cover = $review.item.gallery->getCover()}
										{if empty($rev_cover)}
											{?$rev_cover = $review.item.gallery->getDefault()}
										{/if}
										{if !empty($rev_cover)}
											<a href="{$review.item->getUrl()}"><img src="{$rev_cover->getUrl(176, 210)}" alt="{$review.item.title}" /></a>
										{/if}
									</div>
									<div class="name"><a href="{$review.item->getUrl()}">{$review.item.title}</a></div>
									<div class="stars s{$review.mark}"></div>
									<div class="main">{$review.timestamp|date_format_lang:'%d %B %Y':'ru'}</div>
								</div>
								<div class="col3">
									{if !empty($review.text_worth)}
										<div class="opinion">
											<div class="op-title">Достоинства</div>
											<p>{$review.text_worth|html}</p>
										</div>
									{/if}
									{if !empty($review.text_fault)}
										<div class="opinion">
											<div class="op-title">Недостатки</div>
											<p>{$review.text_fault|html}</p>
										</div>
									{/if}
									<div class="opinion">
										<div class="op-title">Отзыв</div>
										<p>{$review.text|html}</p>
									</div>
								</div>
							</li>
						{/foreach}
					</ul>
				{else}
					<div class="empty-result main">
						Вы еще не оставляли отзывов.
					</div>
				{/if}
			</div>		
		</div>		
	</div>
</div>
{*{include file="components/brands.tpl"}*}
{*{include file="components/news-block.tpl"}*}

