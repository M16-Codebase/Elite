{if !empty($document_root)}
	{?$path = $document_root . "/templates/project/img/svg/"}
{else}
	{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
{/if}
{include file="functions.tpl"}
{?$temp_param_url = 'rbwu3ryg34f'}
{if !empty($innerTemplate)}
    {?$confTitle = ''}
	{if $constants.segment_mode == 'lang' && !empty($site_config[0].project_name)}{?$confTitle = ' | ' . $site_config[0].project_name}{elseif !empty($site_config.project_name)}{?$confTitle = ' | ' . $site_config.project_name}{/if}
	{if !empty($site_config.cms_type)}{?$confTitle = $site_config.cms_type . $confTitle}{else}{?$confTitle = "Управление сайтом" . $confTitle}{/if}
	{include file=$innerTemplate assign="moduleResult" main_template="index"}
{else}
	{include file="no_template.html" assign="moduleResult" main_template="index"}
{/if}

{if !empty($admin_page) && ($accountType == 'Admin' || $accountType == 'Broker' || $accountType == 'SeoAdmin' || $accountType == 'SuperAdmin')}

	{include file="Admin/index.tpl"}

{else}

	{$seoPagePersister->checkPageInfo($pageUID, $pageTitle, $pageDescription, $pageKeywords, $pageText, $pageCanonical)}
	<!DOCTYPE html>
	<html lang="ru">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta name="yandex-verification" content="71d0b7bf106081d5" />


			<meta name="format-detection" content="telephone=no" />
			<title>{if !empty($pageTitle)}{$pageTitle}{/if}</title>
			{if !empty($pageDescription)}
				<meta name="Description" content="{$pageDescription}" />
			{/if}
			{if isset($no_index_follow) && $no_index_follow == true}
				<meta name="robots" content="noindex, follow"/>
			{/if}
			{if !empty($pageKeywords)}
				<meta name="Keywords" content="{$pageKeywords}" />
			{/if}
            {if !empty($canonical_uri)}
				<link rel="canonical" href="{$canonical_uri}" />
            {/if}
			<link rel="shortcut icon" href="/{$template_dir}/img/favicon.ico" />

            {if $innerTemplate != 'Modules/Main/View/index.tpl' && !empty($og_meta)}
				{if !empty($og_meta['type'])}
					<meta property="og:type" content="{$og_meta['type']}">
                {/if}
                {if !empty($og_meta['site_name'])}
					<meta property="og:site_name" content="{$og_meta['site_name']}">
                {/if}
                {if !empty($og_meta['title'])}
					<meta property="og:title" content="{$og_meta['title']}">
                {/if}
                {if !empty($og_meta['description'])}
					<meta property="og:description" content="{$og_meta['description']}">
                {/if}
                {if !empty($og_meta['url'])}
					<meta property="og:url" content="{$og_meta['url']}">
                {/if}
                {if !empty($og_meta['locale'])}
					<meta property="og:locale" content="{$og_meta['locale']}">
                {/if}
				{if !empty($og_meta['image'])}
					<meta property="og:image" content="{$root_url . $og_meta['image']}">
					<meta property="og:image:width" content="{$og_meta['width']}">
					<meta property="og:image:height" content="{$og_meta['height']}">
				{/if}
            {/if}

			{if $innerTemplate == 'Modules/Main/View/index.tpl'}
				<meta property="og:type" content="website">
				<meta property="og:site_name" content="М16-Недвижимость">
				<meta property="og:title" content="Продажа элитной недвижимости | Агентство недвижимости Вячеслава Малафеева М16-Недвижимость">
				<meta property="og:description" content="Купить или продать элитную недвижимость в новостройках и на вторичном рынке вам поможет агентство Вячеслава Малафеева М16-Недвижимость">
				<meta property="og:url" content="{$page_url}">
				<meta property="og:locale" content="ru_RU">
				<meta property="og:image" content="http://m16-elite.ru/data/thumbs/w1200h500/342f41/single/650.jpg">
				<meta property="og:image:width" content="1200">
				<meta property="og:image:height" content="500">
			{/if}
			
			
			{if (strpos($page_url, '/en/') !== false)}
				<link rel="alternate" hreflang="ru" href="https://m16-elite.ru/{substr($page_url,24)}" />
				<link rel="alternate" hreflang="en" href="https://m16-elite.ru/en/{substr($page_url,24)}" />		
			{/if}
			
			{if (strpos($page_url, '/en/') == false)}
				<link rel="alternate" hreflang="ru" href="{$page_url}" />
				<link rel="alternate" hreflang="en" href="https://m16-elite.ru/en{substr($page_url,20)}" />
			{/if}
			
			{if (strpos($page_url, '/scheme/') !== false)}
				<link rel="canonical" href="{str_replace('scheme','apartments',$page_url)}" />
			{/if}
			
			
			<!--<meta name="viewport" content="width=1600">-->
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<!--[if IE]>
				<meta http-equiv="X-UA-Compatible" content="IE=edge" />
				<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
			<![endif]-->

            {if isset($districtList)}
            {literal}
				<script>
					var cityDistricts = {{/literal}
						{foreach from=$districtList item=sval_view key=val}
						 	"{$val}":"{$sval_view}",
						{/foreach}
					{literal}
					};

                    window.cityDistricts = {{/literal}
                        {foreach from=$districtList item=sval_view key=val}
                        "{$val}":"{$sval_view}",
                        {/foreach}
                        {literal}
                    };
				</script>
            {/literal}
			{/if}

            {if isset($allowFilterFriendlyUrl)}
                {literal}
                <script>
                    window.allowFilterFriendlyUrl = {/literal}{$allowFilterFriendlyUrl}{literal};
                </script>
                {/literal}
            {/if}


			{include file="js_main_includes.tpl" ver=$temp_param_url}

			{if !empty($outCss)}
				{foreach from=$outCss item="url"}
					<link href="{$url|html}" rel="stylesheet" />
				{/foreach}
			{/if}

     
{literal}
<script>
    setTimeout(function(){
        let el = document.querySelector('.main-loader');
        el.classList.add('m-loaded');
    },500);
</script>
{/literal}


            
			{include file="js_includes.tpl"}
           
			{if !empty($includeCss)}
				{foreach from=$includeCss item="css"}
					<link href="/{$template_dir}/{$css}?{$temp_param_url}" rel="stylesheet" type="text/css" />
				{/foreach}
			{/if}

			{if !empty($canonical_url) || !empty($pageCanonical)}
                <link rel="canonical" href="{if !empty($pageCanonical)}{$pageCanonical}{else}{$canonical_url}{/if}" />
            {/if}
			{if !empty($seo_config.head_content)}{$seo_config.head_content|html}{/if}
			{if $google_analytics.mode == 'analytics' || $google_analytics.mode == 'tagmanager'}
                {$google_analytics.seo_google_code|html}
            {/if}

			{if !empty($customCss)}
                {foreach from=$customCss item="file"}
					<link href="/{$template_dir}/{$file|html}?{$temp_param_url}" rel="stylesheet" type="text/css" />
                {/foreach}
            {/if}

			<link href="/templates/project/responsive.css" rel="stylesheet" type="text/css" />
		
{literal}
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(33436783, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/33436783" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
{/literal}
		</head>

		<!--[if lte IE 7 ]>
			<body class="browser-oldie browser-ie7">
		<![endif]--> <!--[if IE 8 ]>
			<body class="browser-oldie browser-ie8">
		<![endif]--> <!--[if (gte IE 9)|!(IE)]><!-->
		<body class="browser-new{if $device_type != 'desktop'} m-mobile{/if}" data-prefix-url="{$url_prefix}"><!--<![endif]-->
			<!-- Google Tag Manager (noscript) -->
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T3K6QSB"
			height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			<!-- End Google Tag Manager (noscript) -->
			<!--{$request_segment.key}-->

        	
			{if !empty($seo_config.body_top_content)}{$seo_config.body_top_content|html}{/if}
			{include file="mainLayout.tpl"}
			<div class="mobile-detect"></div>
			{if $accountType == 'SuperAdmin'}
				<div class="page-generate-time a-center"><!--GenerateTime--></div>
			{/if}
			{if !empty($debug_mode)}
				{debug charset="utf-8"}
			{/if}
		

	
			{literal}
			<div class="cookie-attention" id="cookie-attention-widget" style="display: none; ">
<div class="cookie-attention-holder">
<div class="cookie-attention-content cont-w" style="
    background: #000;
    border-top: 3px solid #b59974;
">
<div class="cookie-attention-wrapper" style="
    margin: 0 auto;
    padding-bottom: 22px;
    max-width: 800px;
">
<div class="cookie-attention-face">
<!--<img alt="" src="/images/icons/attention-man.png">-->
</div>
<div class="cookie-attention-body" style="
    display: flex;
    flex-direction: column;
">
<div class="cookie-attention-text" style="width:auto">
<div class="cookie-attention-text" style="
    color: #fff;
	width:auto;
">
Сайт <strong>m16-elite.ru</strong> использует файлы «cookie» и системы аналитики для персонализации сервисов и повышения удобства пользования веб-сайтом. Продолжая просмотр сайта, вы разрешаете их использование. С подробной информации о сборе персональных данных можно ознакомится <a href="https://m16-elite.ru/privacy_policy">тут</a>.</div>
</div>
<div class="cookie-attention-ui" onclick="setCookie('privacy_confirm', '1', 365 ); $('#cookie-attention-widget').css('display','none');">
<input class="cookie-attention-choice __positive js-cookie-attention-choice" id="cookie-attention-positive" type="radio" value="positive">
<label class="cookie-attention-presence" for="cookie-attention-positive" style="
    padding: 10px 20px;
">Согласен
</label>
</div>
</div>
</div>
</div>
</div>
</div>
		<script>


function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}


		setTimeout(function() {
			if(getCookie('privacy_confirm')!=1){
				$('#cookie-attention-widget').css('display','block');
			}
		}, 1000);


			</script>
			{/literal}
			
<script type="text/javascript" src="/js/lib/jquery.js"></script>
<script type="text/javascript" src="/js/lib/underscore.js"></script>
<script type="text/javascript" src="/js/lib/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/lib/jquery-ui/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="/js/lib/chosen/jquery.chosen.custom.js"></script>
<script type="text/javascript" src="/js/lib/jquery.form.js"></script>
<script type="text/javascript" src="/js/lib/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="/js/lib/greensock/TweenMax.min.js"></script>
<script type="text/javascript" src="/js/lib/snap.svg-min.js"></script>
<script type="text/javascript" src="/js/lib/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="/js/lib/idangero/swiper.jquery.js"></script>
<script type="text/javascript" src="/js/lib/fancybox/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="/js/lib/parallax.min.js" ></script>
<script type="text/javascript" src="/js/lib/require.js"></script>
<script type="text/javascript" src="/js/lib/mCustomScrollbar/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBpY-3bznL5ZkxbzjONYIgWSuiIQhocYXQ&libraries=places"></script>
<script type="text/javascript" src="/js/lib/infobubble.js"></script>
<script type="text/javascript" src="/js/filter.js?{$ver}"></script>
	<script src="/templates/project/responsive.js" type="text/javascript"></script>


{if !empty($includeJS)}
    {foreach from=$includeJS item="jsfile"}
        <script src="/{$template_dir}/{$jsfile}?{$temp_param_url}" type="text/javascript"></script>
    {/foreach}
{/if}
	{if !empty($seo_config.body_content)}{$seo_config.body_content|html}{/if}
 {include file="js_metrics_events.tpl"}

{if !empty($customJs)}
    {foreach from=$customJs item="file"}
        <script src="/{$template_dir}/{$file|html}?{$temp_param_url}" type="text/javascript"></script>
    {/foreach}
{/if}


		</body>
	</html>
{/if}
