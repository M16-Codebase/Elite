{?$currentCatalog = $current_type->getCatalog()}
<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />{*обязательно*}
		<title>TEST</title>
		<style>
			{literal}
			
			{/literal}
		</style>
		</head>
		
		<!--[if lte IE 7 ]> 
		<body class="browser-oldie browser-ie7"> 
	<![endif]--> <!--[if IE 8 ]> 
		<body class="browser-oldie browser-ie8"> 
	<![endif]--> <!--[if (gte IE 9)|!(IE)]><!--> 
	<body><!--<![endif]-->
		<div class="page-wrap">
			{*include file="components/header.tpl"*}
			<div class="page-main">
				<section class="page-content">
<h1>Отобранные {$currentCatalog.word_cases['v']['2']['i']}</h1>			
		{?$favorites = $account->getFavorites()}
	<div class="result-header header-objects">
		<div class="filter-result a-right">
			Найдено {$favorites.counts.variants} {$currentCatalog.word_cases['v']['2']['r']} в {$favorites.counts.items} объектах {$currentCatalog.word_cases['i']['2']['p']}
		</div>
	</div>
	<div class="main-content-gray offer-body">
		<div class="">
			<div class="input-item-cont">
				<div class="input-item">
					<div class="change-history"></div>
						<div class="input-title h4">Заголовок PDF-презентации</div>
					<div class="field justify">
						{$favorites.comments.title}
					</div>
				</div>
			</div>
			<div class="input-item-cont">
				<div class="input-item">
					<div class="change-history"></div>
						<div class="input-title h4">Вступительное слово для PDF-презентации</div>
					<div class="field justify">
						{$favorites.comments.text}
					</div>
				</div>
			</div>
		</div>
		{if !empty($favorites.items)}
			<ul class="offer-list">
				{foreach from=$favorites.items item=f_data}
					{?$item = $f_data.item}{*обычный объект айтема*}
				<li class="offer-item m-open">
					<div class="offer-header slide-header link-wrap">
						<div class="offer-selection a-right">
							<i class="i-selection"></i>
						</div>
						<div class="offer-visibility a-right link-except">
							<i class="i-visibility m-hidden"></i>
						</div>
						<div class="offer-status contract a-right">Договор
							<br />50%
						</div>
						{?$cover = $item['gallery']->getCover()}
						{if !empty($cover)}
							<div class="offer-cover a-left">
								<img class="m-zoom" src="{$cover->getUrl(40, 40)}" />
							</div>
						{/if}
						<div class="title">
							<div class="title-inner">
								{$item.title}
							</div>
						</div>
					</div>
					<div class="comment-pdf">
						<div class="change-history"></div>
							<div class="input-title">Комментарий для PDF-презентации</div>
						<div class="field justify">
							{$f_data.comment}
						</div>
					</div>
					<div class="offer-body">
						<ul class="more-offers">
							{foreach from=$f_data.variants item=variant}
								{* $variant - обычный объект варианта *}
							<li  class="offers-item a-link link-wrap variant-item">
								<div class="offer-selection a-right">
									<i class="i-selection m-white"></i>
								</div>
								<div class="offer-visibility a-right link-except">
									<i class="i-visibility m-white"></i>
								</div>
								<div class="offer-status free a-right">Свободно</div>
								<div class="offer-descr small-descr a-right">
									<i class="i-lock"></i>&nbsp;1000-1500 руб./м
								</div>
								<div class="title">
									{$variant.variant_title}
								</div>
							</li>
							{/foreach}
						</ul>
					</div>
				</li>
				{/foreach}
			</ul>
			{else}
				<div class="empty-list">
					Нет {$currentCatalog.word_cases['v']['2']['r']}
				</div>
			{/if}
	</div>
	</section>
				{*include file="components/footer.tpl"*}
			</div>
		</div>
	</body>
</html>