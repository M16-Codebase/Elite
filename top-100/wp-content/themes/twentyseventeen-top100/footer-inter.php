<?php
$data=ftInter();
$post_ids=$data[0];
$mmd=$data[1];
//echo $mmd;
$mobileP=0;
if (strpos($_SERVER['HTTP_USER_AGENT'],'ndroid') || strpos($_SERVER['HTTP_USER_AGENT'],'IOS')){
	$mobileP=1;
}
?>
&nbsp
<div class="dscs">
<div id="disqus_thread"></div>
<script>
var disqus_config = function () {
this.page.url = "https://m16-elite.ru"+window.location.pathname;  
this.page.identifier = window.location.pathname; 
};
(function() { // DON'T EDIT BELOW THIS LINE
var d = document, s = d.createElement('script');
s.src = 'https://top-100.disqus.com/embed.js';
s.setAttribute('data-timestamp', +new Date());
(d.head || d.body).appendChild(s);
})();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
</div>
                            


<div class="other-interview">
        <div class="container">
            <div id="carouselExampleControls" class="carousel container slide" data-ride="carousel">
                <div class="carousel-inner carousel-inner-interview">
				
				<?php 
				if($mobileP==0){
				foreach ($post_ids as $keyg=>$value) { 
				if($keyg==0){ ?> 
					<div class="carousel-item row active  text-justify interview-rows scrolling-wrapper">
                        <?php foreach ($post_ids[$keyg] as $key=>$value) { ?>
                            <a href="<?php echo $post_ids[$keyg][$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_ids[$keyg][$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $post_ids[$keyg][$key][5]; ?></h3>
                                        <span class="interview-person-desc"><?php echo $post_ids[$keyg][$key][6];?></span>
                                        <div class="interview-person-details row justify-content-center text-justify">
                                            <span class="interview-person-text col-4"><?php echo $post_ids[$keyg][$key][2];?></span>
                                            <span class="interview-person-text interview-person-view col-4"><?php echo $post_ids[$keyg][$key][4];?></span>
                                            <span class="interview-person-text interview-person-comment col-4"><?php echo $post_ids[$keyg][$key][3];?></span>
                                        </div>
                                        <span class="interview-person-text"><?php echo $post_ids[$keyg][$key][7]; ?></span>
                                    </div>
                            </a>
						<?php } ?> 
                    </div>
				<?php }else{ ?>
                    <div class="carousel-item row text-justify interview-rows scrolling-wrapper">
                        <?php foreach ($post_ids[$keyg] as $key=>$value) { ?>
                            <a href="<?php echo $post_ids[$keyg][$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_ids[$keyg][$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $post_ids[$keyg][$key][5]; ?></h3>
                                        <span class="interview-person-desc"><?php echo $post_ids[$keyg][$key][6];?></span>
                                        <div class="interview-person-details row justify-content-center text-justify">
                                            <span class="interview-person-text col-4"><?php echo $post_ids[$keyg][$key][2];?></span>
                                            <span class="interview-person-text interview-person-view col-4"><?php echo $post_ids[$keyg][$key][4];?></span>
                                            <span class="interview-person-text interview-person-comment col-4"><?php echo $post_ids[$keyg][$key][3];?></span>
                                        </div>
                                        <span class="interview-person-text"><?php echo $post_ids[$keyg][$key][7]; ?></span>
                                    </div>
                            </a>
						<?php } ?> 
                    </div>
				<?php }}}else{ 
				foreach ($post_ids as $keyg=>$value) { 
				if($keyg==0){ ?>  
					<div class="carousel-item row active text-justify interview-rows scrolling-wrapper">
                        <?php foreach ($post_ids[$keyg] as $key=>$value) { ?>
                            <a href="<?php echo $post_ids[$keyg][$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_ids[$keyg][$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $post_ids[$keyg][$key][5]; ?></h3>
                                    </div>
                            </a>
						<?php } ?> 
                    </div>
				<?php }else{ ?>
                    <div class="carousel-item row text-justify interview-rows scrolling-wrapper">
                        <?php foreach ($post_ids[$keyg] as $key=>$value) { ?>
                            <a href="<?php echo $post_ids[$keyg][$key][1];?>" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="<?php echo $post_ids[$keyg][$key][8]; ?>" class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person"><?php echo $post_ids[$keyg][$key][5]; ?></h3>
                                    </div>
                            </a>
						<?php } ?> 
                    </div>
					
					
				<?php }}} ?> 
                </div>
                <a class="carousel-control-prev carousel-control-prev-interview" href="#carouselExampleControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next carousel-control-next-interview" href="#carouselExampleControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>             
    </div>
	<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>

	<!-- .site-content -->
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
		
		
		
		
		
		<div style="display:none" class="rating-m16">
			<div id="" class="post-ratings" itemscope itemtype="http://schema.org/Article">
				<img src="https://m16-consulting.ru/wp-content/plugins/wp-postratings/images/stars/rating_on.gif" alt="1 оценка, среднее: 5,00 из 5" title="1 оценка, среднее: 5,00 из 5" class="post-ratings-image" /><img src="https://m16-consulting.ru/wp-content/plugins/wp-postratings/images/stars/rating_on.gif" alt="1 оценка, среднее: 5,00 из 5" title="1 оценка, среднее: 5,00 из 5" class="post-ratings-image" /><img src="https://m16-consulting.ru/wp-content/plugins/wp-postratings/images/stars/rating_on.gif" alt="1 оценка, среднее: 5,00 из 5" title="1 оценка, среднее: 5,00 из 5" class="post-ratings-image" /><img src="https://m16-consulting.ru/wp-content/plugins/wp-postratings/images/stars/rating_on.gif" alt="1 оценка, среднее: 5,00 из 5" title="1 оценка, среднее: 5,00 из 5" class="post-ratings-image" /><img src="https://m16-consulting.ru/wp-content/plugins/wp-postratings/images/stars/rating_on.gif" alt="1 оценка, среднее: 5,00 из 5" title="1 оценка, среднее: 5,00 из 5" class="post-ratings-image" /> (<em><strong>1</strong> оценок, среднее: <strong>5,00</strong> из 5, <strong>вы уже поставили оценку</strong></em>)
				<meta itemprop="headline" content="<?php the_title();?>" />
				<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
					<img itemprop="image url" alt="<?php the_title();?>" width="2002" height="1032" src="<?php echo get_field('page_image'); ?>"/>
					<meta itemprop="width" content="2002">
					<meta itemprop="height" content="1032">
				</div>
				<?php 
				$currDate=get_the_date();
				$currDate=explode('.',$currDate);
				$currDates=$currDate[2].'-'.$currDate[1].'-'.$currDate[0];
				?>
				<meta itemprop="description" content="<?php echo $mmd;?>" />
				<meta itemprop="datePublished" content="<?php echo $currDates; ?>T16:28:53+00:00" />
				<meta itemprop="dateModified" content="<?php echo $currDates; ?>T16:43:56+00:00" />
				<meta itemprop="url" content="https://m16-elite.ru<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<meta itemprop="author" content="TOP 100 PRESS" />
				<meta itemprop="mainEntityOfPage" content="https://m16-elite.ru<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<div style="display: none;" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
					<meta itemprop="name" content="M16-Элитная недвижимость|TOP 100 PRESS" />
					<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
						<meta itemprop="url" content="https://m16-consulting.ru/wp-content/themes/m16/favicon.ico" />
					</div>
				</div>
				
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
<script id="dsq-count-scr" src="//top-100.disqus.com/count.js" async></script>
<?php wp_footer("inter"); ?>

</body>
</html>