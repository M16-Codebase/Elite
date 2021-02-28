<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!--<meta name="viewport" content="width=device-width, initial-scale=0.8" />-->
		<meta name="format-detection" content="telephone=no" />
		<title>404 — Страница не найдена</title>
		<link rel="shortcut icon" href="/img/favicon.ico" />
		<link href="/templates/project/style.css" rel="stylesheet" type="text/css" />
		<link href="/templates/project/notFound.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="/js/lib/jquery.js"></script>
		{literal}
			<!--<script>
				var viewport = $("meta[name='viewport']");
				if ($(window).width() < 1220) {
					viewport.attr("content","width=device-width, initial-scale=0.3, user-scalable=yes")
				} else {
					viewport.attr("content","width=device-width, initial-scale=0.8, user-scalable=yes")
				};
			</script>-->
		{/literal}
        {literal}
			<!-- Yandex.Metrika counter -->
			<script type="text/javascript">(function(d,w,c){(w[c]=w[c]||[]).push(function(){try{w.yaCounter33436783 = new Ya.Metrika({id:33436783, webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true});}catch(e){}});var n=d.getElementsByTagName("script")[0],s=d.createElement("script"),f=function(){n.parentNode.insertBefore(s,n);};s.type="text/javascript";s.async=true;s.src=(d.location.protocol=="https:"?"https:":"http:")+"//mc.yandex.ru/metrika/watch.js";if(w.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded",f,false);}else{f();}})(document,window,"yandex_metrika_callbacks");</script>
			<noscript>&lt;div&gt;&lt;img src="//mc.yandex.ru/watch/33436783?ut=noindex" style="position:absolute; left:-9999px;" alt="" /&gt;&lt;/div&gt;</noscript>
			<!-- /Yandex.Metrika counter -->
			<!-- Google Tag Manager -->
			<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-T3K6QSB" height="0" width="0" style="display:none;visibility:hidden" </iframe></noscript>
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-T3K6QSB');</script>
			<!-- End Google Tag Manager -->
        {/literal}
		<script type="text/javascript" src="/js/lib/require.js"></script>
		<script src="/templates/project/Modules/Main/View/index.js" type="text/javascript"></script>
		<script src="/templates/project/script.js" type="text/javascript"></script>
		<script src="/templates/project/notFound.js" type="text/javascript"></script>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp" type="text/javascript"></script>

		<link href="/templates/project/responsive.css" rel="stylesheet" type="text/css" />
		<script src="/templates/project/responsive.js" type="text/javascript"></script>

	</head>
	<!--[if lte IE 7 ]> 
	<body class="browser-oldie browser-ie7" data-prefix-url="{$url_prefix}">
	<![endif]--> <!--[if IE 8 ]> 
	<body class="browser-oldie browser-ie8" data-prefix-url="{$url_prefix}">
	<![endif]--> <!--[if (gte IE 9)|!(IE)]><!--> 
<body class="browser-new" data-prefix-url="{$url_prefix}"><!--<![endif]-->
{?$pageTitle = $lang->get('404 — Страница не найдена', 'Page not found')}
{*{?$includeOuterJS['googleMap'] = 'https://maps.googleapis.com/maps/api/js?v=3.exp'}*}
{if !empty($document_root)}	
	{?$path = $document_root . "/templates/project/img/svg/"}
{else}
	{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
{/if}
<div class="page-wrap">
	{include file="components/header.tpl"}
	<div class="top-bg m-black">
		<div class="site-top">
			<div class="ball-wrap">
				<div class="w2">4</div>
				<div class="ball"></div>
				<div class="w2">4</div>
			</div>
			<h1 class="title" title="Страница не найдена">{$lang->get('<span>Страница</span><br>не найдена', '<span>Page</span><br>not found')|html}</h1>
			<a href="{$url_prefix}/" class="btn m-sand">{$lang->get('На главную страницу', 'Main page')}</a>
		</div>
	</div>
	{?$black_footer = 1}
	{include file="components/footer.tpl"}
</div>
<div class="mobile-detect"></div>
</body>
</html>	
