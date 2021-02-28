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
                <form class="col-sm-12 row">
                    <div class="col-12 col-md-4">  
                        <label class="contact-field">
                            <div class="contact-row">
                                <div class="contact-title">
                                    <span>Имя</span>
                                    <span>*</span>
                                    <span class="slash"></span>
                                </div>
                                <div class="contact-input">
                                    <input type="text" name="author" value="">
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-12 col-md-4">  
                        <label class="contact-field">
                            <div class="contact-row">
                                <div class="contact-title">
                                    <span>Email</span>
                                    <span>*</span>
                                    <span class="slash"></span>
                                </div>
                                <div class="contact-input">
                                    <input type="text" name="email" value="">
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-12 col-md-4">  
                        <input type="button" onClick="subs();" class="sign-up-letters" VALUE="Подписаться на рассылку">
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
                <span class="footer-address"> Большая Зеленина, 18</span>
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
    <!-- jQuery first, then Popper.js, then Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"  crossorigin="anonymous"></script>-->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" crossorigin="anonymous"></script>
	</div><!-- .site -->

<?php wp_footer(); ?>

</body>
</html>