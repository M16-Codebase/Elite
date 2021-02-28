<?php
$data=array();
$data=ftMain();
$post_id=$data[0];
$art_id=$data[1];
$mobileP=0;
if (strpos($_SERVER['HTTP_USER_AGENT'],'ndroid') || strpos($_SERVER['HTTP_USER_AGENT'],'IOS')){
	$mobileP=1;
}
?>

<div id="partners">
<div class="container container-spacer">
<div class="row justify-content-center text-center">
<div class="col-sm-12">
<h1 class="partners-header">Партнеры проекта</h1>
<div style="align:center">
<?php echo do_shortcode('[metaslider id="320"]'); ?>
</div>
<button class="became-partner" data-target="#partnerModal" data-toggle="modal">Стать партнером ТОП 100</button>
<div id="partnerModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="partnerModalLabel">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header"><button class="close" type="button" aria-label="Close" data-dismiss="modal">
<span aria-hidden="true">×</span>
</button></div>
<div class="modal-body row justify-content-center text-center">
<h5 id="exampleModalLabel" class="modal-title">Стать партнером ТОП 100</h5>
<form class="col-sm-12 row TOP100_JOIN">
                                    <div class="col-12">  
                                        <label class="contact-field">
                                            <div class="contact-row">
                                                <div class="contact-title">
                                                    <span>Имя</span>
                                                    <span>*</span>
                                                    <span class="slash"></span>
                                                </div>
                                                <div class="contact-input">
                                                    <input type="text" name="authorp" id="authorp" value="">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-12">  
                                        <label class="contact-field">
                                            <div class="contact-row">
                                                <div class="contact-title">
                                                    <span>Email</span>
                                                    <span>*</span>
                                                    <span class="slash"></span>
                                                </div>
                                                <div class="contact-input">
                                                    <input type="text" name="emailp" id="emailp" value="">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    
                                </form><div class="col-12 text-right">  
                                        <button onclick="Pusk(); sucPart(); yaCounter33436783.reachGoal('TOP100_JOIN');" class="send-request" aria-hidden="true" aria-label="Close" data-dismiss="modal">Отправить заявку</button>
										<div style="display:none;" id="ajax"></div>
                                    </div></div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div id="interviews">
        <div class="container container-spacer">
                <div class="row justify-content-center text-justify interview-div">
                    <div class="col-sm-10">   
                        <h1 class="interview-header">Интервью</h1> 
                        <p class="lead">
                            Эксклюзивные интервью с самыми яркими российскими знаменитостями от Вячеслава Малафеева! 
                            Только в журнале TOP 100! Интеллектуальные беседы, провокационные вопросы и не поднимавшиеся ранее темы. 
                            Интервью откроют для Вас с новой стороны не только гостей, но и самого Вячеслава! 
                        </p>
                    </div> 
                    <div class="col-sm-12">   
                        <div class="row text-justify interview-rows scrolling-wrapper">
						<?php 
						if($mobileP==0){
						foreach ($post_id as $key=>$value) {
						if($key>2){break;}else{
						?>
                            <a href="<?php echo $post_id[$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_id[$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $post_id[$key][5]; ?></h3>
                                        <span class="interview-person-desc"><?php echo $post_id[$key][6];?></span>
                                        <div class="interview-person-details row justify-content-center text-justify">
                                            <span class="interview-person-text col-4"><?php echo $post_id[$key][2];?></span>
                                            <span class="interview-person-text interview-person-view col-4"><?php echo $post_id[$key][4];?></span>
                                            <span class="interview-person-text interview-person-comment col-4"><?php echo $post_id[$key][3];?></span>
                                        </div>
                                        <span class="interview-person-text"><?php echo $post_id[$key][7]; ?></span>
                                    </div>
                            </a>
						<?php }}}else{ 
						foreach ($post_id as $key=>$value) {
						if($key>2){break;}else{
						?>
						<a href="<?php echo $post_id[$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_id[$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <span class="interview-person"><?php echo ($post_id[$key][5].'        '); ?></span>
                                    </div>
                            </a>
						
						<?php }}} ?>
                        </div>  
                    </div>
                    <a href="https://m16-elite.ru/top-100/interviews/"><button class="more-interview">Больше интервью</button></a>                 
            </div>
        </div>
    </div>
    <div id="last-publications">
        <div class="container container-spacer">
            <div class="row text-justify justify-content-center  publication-div">
                <div class="col-sm-10">   
                    <h1 class="publication-header">Последние публикации</h1> 
                </div> 
                <div class="col-sm-12">   
                    <div class="row text-justify justify-content-center  publication-rows scrolling-wrapper">
                        <?php 
						if($mobileP==0){
						foreach ($art_id as $key=>$value) { 
							if($key>2){break;}else{ ?> 
                            <a href="<?php echo $art_id[$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $art_id[$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $art_id[$key][5]; ?></h3>
                                        <span class="interview-person-desc"><?php echo $art_id[$key][6];?></span>
                                        <div class="interview-person-details row justify-content-center text-justify">
                                            <span class="interview-person-text col-4"><?php echo $art_id[$key][2];?></span>
                                            <span class="interview-person-text interview-person-view col-4"><?php echo $art_id[$key][4];?></span>
                                            <span class="interview-person-text interview-person-comment col-4"><?php echo $art_id[$key][3];?></span>
                                        </div>
                                        <span class="interview-person-text"><?php echo $art_id[$key][7]; ?></span>
                                    </div>
                            </a>
						<?php }}}else{ foreach ($art_id as $key=>$value) { 
							if($key>2){break;}else{ ?>
							<a href="<?php echo $art_id[$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $art_id[$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $art_id[$key][5]; ?></h3>
                                    </div>
                            </a>
						<?php }}} ?>
                    </div>  
                </div> 
				<a href="https://m16-elite.ru/top-100/articles/"><button class="more-interview">Больше статей</button></a>  				
            </div>
        </div>
    </div>
<div id="contact-form">
        <div class="container">
            <div class="row justify-content-center text-center">
                <form name="subscribe" class="col-sm-12 row TOP000_SUBSCRIBE">
                    <div id="author" class="col-12 col-md-4">  
                        <label class="contact-field">
                            <div class="contact-row">
                                <div class="contact-title">
                                    <span>Имя</span>
                                    <span>*</span>
                                    <span class="slash"></span>
                                </div>
                                <div class="contact-input">
                                    <input  type="text" name="author" value="">
                                </div>
                            </div>
                        </label>
                    </div>
                    <div id="email" class="col-12 col-md-4">  
                        <label class="contact-field">
                            <div class="contact-row">
                                <div class="contact-title">
                                    <span>Email</span>
                                    <span>*</span>
                                    <span class="slash"></span>
                                </div>
                                <div class="contact-input">
                                    <input  type="text" name="email" value="">
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-12 col-md-4">  
                        <input type="button" onClick="sucSub(); subs(); yaCounter33436783.reachGoal('TOP000_SUBSCRIBE');" class="sign-up-letters" value="Подписаться на рассылку">
                    </div>
                </form>
				
            </div>
        </div>
    </div>
	<footer>
        <div class="container  container-footer">
            <div class="justify-content-center text-center">
                <a href="/" class="footer-logo">
                <img src="https://m16-elite.ru/top-100/wp-content/themes/top-100/static/img/m16-logo.svg"></a>
            </div>
            <div class="row justify-content-center text-center">
                <h2 class="malafeev-footer">Компания Вячеслава Малафеева M16</h2>
            </div>
            <div class="row justify-content-center text-center footer-contacts">
                <span class="footer-phone">8 800 550-55-16</span>
                <span class="footer-border"><span class="slash"></span></span>
                <span class="footer-address">Большая Зеленина, 18</span>
            </div>
            <div class="row justify-content-center text-center footer-offer">
                Настоящий сайт и представленные на нем материалы носят 
                исключительно информационный характер и ни при каких условиях не являются публичной офертой,
                определяемой положениями Статьи 437 Гражданского кодекса РФ.
            </div>
            <div class="row justify-content-center text-center footer-catalog footer-row">
                <span class="footer-nedv">
                <a href="/real-estate/">Строящаяся элитная недвижимость</a>
                </span>
                <span class="footer-border"><span class="slash"></span></span>
                <span class="footer-nedv">
                <a href="/resale/">Вторичная элитная недвижимость</a>
                </span>
            </div>
           
            <nav class="row justify-content-center text-center footer-menu">
                <a href="/company/">О нас</a>
                <a href="/service/">Услуги</a>
                <a href="/top16/">Топ-16</a>
                <a href="/district/">Районы</a>
                <a href="/contacts/">Контакты</a>
                <a href="/privacy_policy/">Политика конфиденциальности</a>
            </nav>
            <div class="row justify-content-center text-center footer-social">
                <a href="http://www.facebook.com/m16group/">
                    <img src="https://m16-elite.ru/top-100/wp-content/themes/top-100/static/img/facebook.svg">
                </a>
                <a href="http://ok.ru/group/54832562634771">
                    <img src="https://m16-elite.ru/top-100/wp-content/themes/top-100/static/img/ok.svg">
                </a>
                <a href="https://www.linkedin.com/groups?mostRecent=&amp;gid=6616211&amp;trk=my_groups-tile-flipgrp">
                    <img src="https://m16-elite.ru/top-100/wp-content/themes/top-100/static/img/linkedin.svg">
                </a>
                <a href="https://twitter.com/m16bz">
                    <img src="https://m16-elite.ru/top-100/wp-content/themes/top-100/static/img/twit.svg">
                </a>
                <a href="http://www.instagram.com/m16group/">
                    <img src="https://m16-elite.ru/top-100/wp-content/themes/top-100/static/img/instagram.svg">
                </a>
                <a href="http://vk.com/m16group">
                    <img src="https://m16-elite.ru/top-100/wp-content/themes/top-100/static/img/vk.svg">
                </a>
            </div>
            <div class="row justify-content-center text-center footer-copy ">
                © 2018. Все права защищены.
            </div>
            <div class="row justify-content-center text-center footer-collect-coockies">
               Сайт собирает данные cookie при первом посещении.
            </div>
        </div>
        <!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter33436783 = new Ya.Metrika({
                    id:33436783,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/33436783" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-84755249-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-84755249-3');
</script>
    </footer>
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"  crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" crossorigin="anonymous"></script>
	</div><!-- .site -->


<?php wp_footer("main"); ?>
</body>
</html>
