<?php
/**
 * @package WordPress
 * @subpackage top100
 * @since top100
 */
require_once('/var/www/estate/data/www/m16-elite.ru/top-100/wp-content/themes/twentyseventeen-top100/assets/php/pageData.php');
//include('/var/www/estate/data/www/m16-elite.ru/top-100/wp-content/plugins/wordpress-seo/frontend/class-frontend.php');
$data=array();
$data=init();
$breadcrumbs=$data[0];
$mobileP=$data[1];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="theme-color" content="#CFB27B"/>
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"  crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:700|Raleway:300">
	<link rel="shortcut icon" href="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen-top100/static/favicon.ico" />
    <link rel="stylesheet" href="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen-top100/style.css">
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
	<!--
	function sucPart(){
		swal("Ваш запрос отправлен.", "Мы свяжемся с Вами в ближайшее время.", "success");
	}
	//-->
	<!--
	function sucSub(){
		swal("Спасибо, что подписались на новости «Топ-100»! ", "На Вашу почту выслано письмо с подтверждением подписки. Пожалуйста, перейдите по ссылке из письма, чтобы получать рассылку от «Топ-100».", "success");
	}
	//-->
	</script>
	
	<style>
	   .dscs {
		margin: 0 auto;
		width:635px;
	   }
	</style>
	<link href="/templates/project/notFound.css" rel="stylesheet" type="text/css" />
	
	<meta property="og:title" content="<?php the_title();?>"/>
	<meta property="og:description" content="Эксклюзивные интервью с петербургскими звездами, неизменным интервьюером которых является Вячеслав Малафеев, и публикации, посвященные элитной недвижимости."/>
	<meta property="og:image" content="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen-top100/static/img/m16-gold.svg"/>
	<meta property="og:type" content="webpage"/>
	<meta property="og:url" content= "<?php echo('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" />
  
	<meta name="yandex-verification" content="71d0b7bf106081d5" />
	
	<script>
	var nam=false;
	var emal=false;
	var HttpClient = function() {
		this.get = function(aUrl, aCallback) {
			var anHttpRequest = new XMLHttpRequest();
			anHttpRequest.onreadystatechange = function() { 
				if (anHttpRequest.readyState == 4 && anHttpRequest.status == 200)
					aCallback(anHttpRequest.responseText);
			}

			anHttpRequest.open( "GET", aUrl, true );            
			anHttpRequest.send( null );
		}
	}
	<!--
        function subs()
        {
			var name = document.subscribe.author.value;
			var email = document.subscribe.email.value;
			if(name.length<2){
				document.getElementById("author").classList.add('undefied_field_top100');
				var nam=true;
			}else{
				var nam=false;
				document.getElementById("author").classList.remove('undefied_field_top100');
			}
			if(email.length<4){
				document.getElementById("email").classList.add('undefied_field_top100');
				var emal=true;
			}else{
				var emal=false;
				document.getElementById("email").classList.remove('undefied_field_top100');
			}
			if(email.length>4 & name.length>4){
				var client = new HttpClient();
				client.get('https://api.unisender.com/ru/api/subscribe?format=json&api_key=5f36zqctha3jd4na8kc1q783iq9jihdh4hnfxtfy&list_ids=12017153&fields[email]='+email+'&fields[Name]='+name+'&overwrite=1', function(response) {
				});
			}
			
        }
        //-->
		
	<!--
        function becPart()
        {
			//alert('start');
			var name = document.bpartner.pauthor.value;
			var email = document.bpartner.pemail.value;
			if(name.length<2){
				document.getElementById("author").classList.add('undefied_field_top100');
				var nam=true;
			}else{
				var nam=false;
				document.getElementById("author").classList.remove('undefied_field_top100');
			}
			if(email.length<4){
				document.getElementById("email").classList.add('undefied_field_top100');
				var emal=true;
			}else{
				var emal=false;
				document.getElementById("email").classList.remove('undefied_field_top100');
			}
			if(email.length>4 & name.length>4){
				var xmlhttp = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
				xmlhttp.open('POST', 'https://us18.api.mailchimp.com/3.0/api/1.0/messages/send.json');
				xmlhttp.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4) {
						if(xmlhttp.status == 200) alert('Mail sended!')
						else if(xmlhttp.status == 500) alert('Check apikey')
						else alert('Request error');
					}
				}
				xmlhttp.send(JSON.stringify({'key': 'facc27728e1761c8b07b536eb4da26b6-us18',
				   'message': {
					   'from_email': 'shkaphik00@gmail.com',
					   'to': [{'email': 'aakzhigitov001@gmail.com', 'type': 'to'}],
					   'autotext': 'true',
					   'subject': 'Yeah!',
					   'html': '<h1>Its work!</h1>'
					}}));
			}
			
        }
        //-->

	</script>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-T3K6QSB');</script>
	<!-- End Google Tag Manager -->
	
	<?php wp_head(); ?>
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T3K6QSB"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<nav class="navbar navbar-expand-md fixed-top top-header">
        <div class="nav navbar-text d-none d-lg-block w-100 order-1 order-md-0 ">
            <ul class="navbar-nav justify-content-center text-justify">
                <li class="nav-item elite">
                    <a href="/" class="nav-link">ЭЛИТНАЯ НЕДВИЖИМОСТЬ<br>
                    <span>в Санкт-Петербурге</span></a>
                </li>
                <li class="nav-item item-white"><a href="/real-estate/"  class="nav-link">Строящаяся</a></li>
                <li class="nav-item item-white"><a href="/resale/"  class="nav-link">Вторичная</a></li>
                <li class="nav-item item-white"><a href="/residential/"  class="nav-link">Загородная</a></li>
				<li class="nav-item item-white"><a href="/arenda/"  class="nav-link">Аренда</a></li>
            </ul>
        </div> 
        <a class="navbar-brand order-0" href="https://m16-elite.ru/" >
            <img class="logo-normal" alt="m-16"  height="30" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen-top100/static/img/m16-gold.svg"/>
        </a>
        <button class="navbar-dark navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="nav navbar-text justify-content-center text-justify collapse w-100 w-100 order-3 dual-collapse2 navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ">
                <li class="nav-item item-gold additional-menu"><a href="https://m16-elite.ru/top-100/"  class="nav-link" class="nav-link">О журнале</a></li>
                <li class="nav-item item-gold additional-menu"><a href="https://m16-elite.ru/top-100/#where" class="nav-link">Распространение</a></li>
                <li class="nav-item item-gold additional-menu"><a href="https://m16-elite.ru/top-100/#partners" class="nav-link" >Партнеры</a></li>
                <li class="nav-item item-gold additional-menu"><a href="https://m16-elite.ru/top-100/interviews/"  class="nav-link" class="nav-link">Интервью</a></li>
                <li class="nav-item item-gold additional-menu"><a href="https://m16-elite.ru/top-100/articles/" class="nav-link">Публикации</a></li>
                <li class="nav-item item-gold additional-menu"><a href="https://m16-elite.ru/" class="nav-link properties-button" >Недвижимость</a></li>
                <li class="nav-item item-gold main-menu" ><a href="/company/"  class="nav-link" class="nav-link">О нас</a></li>
                <li class="nav-item item-gold main-menu"><a href="/service/" class="nav-link">Услуги</a></li>
				<li class="nav-item item-gold main-menu"><a href="/top16/" class="nav-link">Топ-16</a></li>
                <li class="nav-item item-gold main-menu"><a href="/top-100/" class="nav-link">Топ 100</a></li>
                <li class="nav-item item-gold main-menu"><a href="/district/" class="nav-link">Районы</a></li>
                <li class="nav-item item-gold main-menu"><a href="/contacts/" class="nav-link">Контакты</a></li>
                <li class="nav-item translation text-center main-menu"><a href="/en/" class="nav-link"><span>EN</span></a></li>
                <li class="nav-item phone roistat_phone main-menu"><a  class="nav-link">+7 812 999-99-16</a></li>
            </ul>
        </div>
    
    </nav>
	<div id="interview-header" class="row">
	<div class="overlay"><img class="back-interview" src="<?php echo get_field('page_image'); ?>"/></div>
	<?php echo $breadcrumbs; ?>
	<div class="interview-header col-sm-12 col-md-6">
	<h1 class="interview-header-name"><?php echo get_field('page_h1'); ?></h1>
	<span class="interview-header-description">
	<?php echo get_field('page_description'); ?>
	</span>
	<p>
	&nbsp
	</p>
	</div>
	</div>
	

    