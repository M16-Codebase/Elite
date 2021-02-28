<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage top-100
 * @since top-100
 */

get_header(); ?>
	
	
	<?php if($_GET['page_id']!='26'){ ?>
	<div id="header">
        <div class="container container-spacer">
            <div class="row text-center">
                <div class="col align-self-center">
                        <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/top-100.png"
                        srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/top-100@2x.png 2x,
                        https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/top-100@3x.png 3x"
                        class="top-100">
                </div>
            </div>
            <div class="row justify-content-center text-justify">
                <div class="col-sm-10">
                    <p class="lead">
                            Перед Вами ежегодные журналы TOP 100 с лучшими* локациями Санкт-Петербурга категории VIP, брендами премиум-класса, топовыми заведениями и 
                            эксклюзивными интервью с российскими звездами.
                    </p>
                    <p class="lead">
                            Журнал выходит с 2016 года и распространяется в элитных заведениях 
                            Петербурга (более 200 точек распространения). В каждом выпуске Вы найдете новые компании, рекомендуемые Вячеславом Малафеевым. Приглашаем Вас в мир совершенства TOP 100!
                    </p> 
                    <p class="sub">   
                        *по версии Вячеслава и Екатерины Малафеевых
                    </p>
                </div>
            </div>
        </div>
    </div>
	<div id="brochure">
        <div class="container container-spacer">
            <div class="row justify-content-center text-center brochure-div">
            <div class="col-sm-12 col-md-7"> 
                    <div class="brochure-overlay">
                        <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/brochure.png"
                        class="brochure-img">
                        <div class="brochure-download">
                            <h2 class="brochure-download-title">ТОП-100 2018</h2>
                            <button class="brochure-download-button">СКАЧАТЬ КАТАЛОГ</button>
                        </div>
                    </div>         
                    <div class="choose-published-year">
                        <a class="published-year">/2016/</a>
                        <a class="published-year">/2017/</a>
                        <a class="published-year year-active">/2018/</a>
                    </div>  
                </div>
                <div class="col-sm-12 col-md-5 brochure-desc">
                    <div class="brochure-text-desc text-left">
                        <h1 class="brochure-year">2018</h1>
                        <p class="brochure-text"><b>Тираж:</b>14000 экз.</p>
                        <p class="brochure-text"><b>Точки распространения:</b>более 200</p>
                        <p class="brochure-text"><b>Эксклюзивные интервью звезд:</b>  
                            Иван Ургант, Сергей Шнуров,
                            Александр Кокорин,
                            Илья Ковальчук и др.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="where">
        <div class="container container-spacer">
                <div class="row justify-content-center text-justify">
                    <div class="col-sm-10">   
                        <h1 class="where-header">Места распространения</h1> 
                        <p class="lead">
                            Журнал TOP 100 распространяется в элитных заведениях Санкт-Петербурга.
                            Ginza, Italy Group, Villa ZimaLeto group, Probka Family, Babochka, 
                            AU pont Rouge, МЦ «Согаз», «Лахта Клиника», «СМТ», Mercedes-Benz Авангард и Олимп,
                            Rolls-Royce, Range Rover, Гольф-клуб Gorki, банный комплекс «Пар для Пар», «Азбука вкуса»,
                            «Глобус Гурмэ», Ленинград Центр, VIP-ложа стадиона «Зенит- Арена». 
                            Это только малая доля мест, где можно встретить печатную версию журнала TOP 100! 
                        </p>
                    </div>                   
            </div>
        </div>
    </div>
    <div id="photos">
        <div class="row justify-content-center scrolling-wrapper">
            <div class="col-4 col-md-3 col-lg-2 photo-external">
                <img class="photo-normal" alt="m-16"  src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-25.png"/>
            </div>
            <div class="col-4 col-md-3 col-lg-2 photo-external">
                <img class="photo-normal" alt="m-16"  src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-26.png"/>
            </div>
            <div class="col-4 col-md-3 col-lg-2 photo-external">
                <img class="photo-normal" alt="m-16"  src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-27.png"/>
            </div>
            <div class="col-4 col-md-3 col-lg-2 photo-external">
                <img class="photo-normal" alt="m-16"  src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-28.png"/>
            </div>
            <div class="col-4 col-md-3 col-lg-2 photo-external">
                <img class="photo-normal" alt="m-16"  src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-30.png"/>
            </div>
            <div class="col-4 col-md-3 col-lg-2 photo-external">
                <img class="photo-normal" alt="m-16"  src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-32.png"/>
            </div>
        </div>
    </div>
    <div id="partners">
        <div class="container container-spacer">
            <div class="row justify-content-center text-center">
                <div class="col-sm-12">   
                    <h1 class="partners-header">Партнеры проекта</h1> 

                    <div id="carouselExampleControls" class="carousel slide carousel-spacer" data-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item row active">
                                <img class="d-block col-4 col-md-3 col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/ginza.png" alt="First slide">
                                <img class="d-block col-4 col-md-3 col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/mercedes.png" alt="First slide">
                                <img class="d-block col-4 col-md-3 col-lg-2  partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/zenit.png" alt="First slide">
                                <img class="d-none d-md-block col-md-3 col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/land-rover.png" alt="Third slide">
                                <img class="d-none d-lg-block col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/italy.png" alt="Second slide">
                                <img class="d-none d-lg-block col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/rad.png" alt="First slide">
                            </div>
                            <div class="carousel-item row">
                                <img class="d-block col-4  col-md-3 col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/zenit.png" alt="First slide">
                                <img class="d-block col-4  col-md-3 col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/land-rover.png" alt="Third slide">
                                <img class="d-block col-4  col-md-3 col-lg-2 partner-img"src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/italy.png" alt="Second slide">
                                <img class="d-none d-md-block col-md-3 col-lg-2 partner-img"  src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/rad.png" alt="First slide">
                                <img class="d-none d-lg-block col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/ginza.png" alt="First slide">
                                <img class="d-none d-lg-block col-lg-2 partner-img" src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/mercedes.png" alt="First slide">
                            </div> 
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>

                    <button class="became-partner" data-toggle="modal" data-target="#partnerModal">Стать партнером ТОП-100</button>

                    <div class="modal fade" id="partnerModal" tabindex="-1" role="dialog" aria-labelledby="partnerModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body row justify-content-center text-center">
                                <h5 class="modal-title" id="exampleModalLabel">Стать партнером ТОП-100</h5>
                                <form class="col-sm-12 row">
                                    <div class="col-12">  
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
                                    <div class="col-12">  
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
                                    <div class="col-12 text-right">  
                                        <button class="send-request">Отправить заявку</button>
                                    </div>
                                </form>
                            </div>
                         
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
                        <div class="row justify-content-center text-justify interview-rows scrolling-wrapper">
                            <a href="https://m16-elite.ru/top-100/?page_id=26" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                    srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                    https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                    class="interview-img">
                                    <div class="interview-link">
                                        <h3 class="interview-person">Иван Ургант</h3>
                                        <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                        певец, музыкант, продюсер</span>
                                        <div class="interview-person-details row justify-content-center text-justify">
                                            <span class="interview-person-text col-4">12.03.1992</span>
                                            <span class="interview-person-text interview-person-view col-4">992</span>
                                            <span class="interview-person-text interview-person-comment col-4">12</span>
                                        </div>
                                        <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                            Жилые комплексы СПб с причалами или пирсом у воды.
                                            Жилые комплексы СПб с причалами или пирсом у воды.</span>
                                    </div>
                            </a>  
                            <a href="https://m16-elite.ru/top-100/?page_id=26" class="col-10 col-md-6 col-lg-4  interview-desc">
                            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                    srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                    https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                    class="interview-img">
                                <div class="interview-link">
                                    <h3 class="interview-person">Иван Ургант</h3>
                                    <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                    певец, музыкант, продюсер</span>
                                    <div class="interview-person-details row justify-content-center text-justify">
                                        <span class="interview-person-text col-4">12.03.1992</span>
                                        <span class="interview-person-text pinterview-person-view col-4">992</span>
                                        <span class="interview-person-text interview-person-comment col-4">12</span>
                                    </div>
                                    <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                        Жилые комплексы СПб с причалами или пирсом у воды.
                                        Жилые комплексы СПб с причалами или пирсом у воды.</span>
                                </div>
                            </a>  
                            <a href="https://m16-elite.ru/top-100/?page_id=26" class="col-10 col-md-6 col-lg-4  interview-desc">
                                <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                    srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                    https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                    class="interview-img">
                                <div class="interview-link">
                                    <h3 class="interview-person">Иван Ургант</h3>
                                    <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                    певец, музыкант, продюсер</span>
                                    <div class="interview-person-details row justify-content-center text-justify">
                                        <span class="interview-person-text col-4">12.03.1992</span>
                                        <span class="interview-person-text pinterview-person-view col-4">992</span>
                                        <span class="interview-person-text interview-person-comment col-4">12</span>
                                    </div>
                                    <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                        Жилые комплексы СПб с причалами или пирсом у воды.
                                        Жилые комплексы СПб с причалами или пирсом у воды.</span>
                                </div>
                            </a>  
                        </div>  
                    </div>
                    <button class="more-interview">Больше интервью</button>                  
            </div>
        </div>
    </div>
    <div id="last-publications">
        <div class="container container-spacer">
            <div class="row justify-content-center text-justify publication-div">
                <div class="col-sm-10">   
                    <h1 class="publication-header">Последние публикации</h1> 
                </div> 
                <div class="col-sm-12">   
                    <div class="row justify-content-center text-justify publication-rows scrolling-wrapper">
                        <a href="/" class="col-10 col-md-6 col-lg-4 publication-desc">
                        <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer@3x.png 3x"
                                class="publication-img">
                            <div class="publication-link">
                                <span class="publication-text-title">Жилые комплексы СПб
                                    с причалами или пирсом у воды</span>
                                <div class="publication-text-details row justify-content-center text-justify">
                                    <span class="publication-text col-4">12.03.1992</span>
                                    <span class="publication-text publication-view col-4">992</span>
                                    <span class="publication-text publication-comment col-4">12</span>
                                </div>
                                <span class="publication-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>  
                        <a href="/" class="col-10 col-md-6 col-lg-4 publication-desc">
                        <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer@3x.png 3x"
                                class="publication-img">
                            <div class="publication-link">
                                <span class="publication-text-title">Жилые комплексы СПб
                                    с причалами или пирсом у воды</span>
                                <div class="publication-text-details row justify-content-center text-justify">
                                    <span class="publication-text col-4">12.03.1992</span>
                                    <span class="publication-text publication-view col-4">992</span>
                                    <span class="publication-text publication-comment col-4">12</span>
                                </div>
                                <span class="publication-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>   
                        <a href="/" class="col-10 col-md-6 col-lg-4  publication-desc">
                        <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer@3x.png 3x"
                                class="publication-img">
                            <div class="publication-link">
                                <span class="publication-text-title">Жилые комплексы СПб
                                    с причалами или пирсом у воды</span>
                                <div class="publication-text-details row justify-content-center text-justify">
                                    <span class="publication-text col-4">12.03.1992</span>
                                    <span class="publication-text publication-view col-4">992</span>
                                    <span class="publication-text publication-comment col-4">12</span>
                                </div>
                                <span class="publication-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>  
                    </div>  
                </div>             
            </div>
        </div>
    </div>
    <div id="contact-form">
        <div class="container">
            <div class="row justify-content-center text-center">
                <form class="col-sm-12 row">
                    <div class="col-8 col-sm-4">  
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
                    <div class="col-8 col-sm-4">  
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
                    <div class="col-8 col-sm-4">  
                        <button class="sign-up-letters"> Подписаться на рассылку </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	<? }else{?>
	<div id="interview-header" class="row">
        <div class="overlay">
        <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/back.png"
        srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/back@2x.png 2x,
        https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/back@3x.png 3x"
        class="back-interview">
        </div>
        <div class="interview-path-links">
            <a class="interview-path-link">Главная /</a>
            <a class="interview-path-link">ТОП-100-PRESS /</a>
            <a class="interview-path-link">Интервью /</a>
            <a class="interview-path-link path-link-active">Интервью с Сергеем Шнуровым</a>
        </div>
        <div class="interview-header col-sm-12 col-md-6">
            <h1 class="interview-header-name">Сергей Шнуров</h1>
            <span class="interview-header-description">
                Очередным собеседником Вячеслава Малафеева для журнала «ТОП 100» стал известный музыкант, 
                певец, актер, художник и телеведущий. Как всегда, разговор шел на самые разные темы.
            </span>
        </div>
    </div>

   <div class="interview-text">
        <div class="container container-interview-spacer">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">За последний год ты покорил все рейтинги популярности, осуществляешь несколько творческих проектов. Какие планы на 2018 год?</span>
                    <span class="interview-answer">Бог его знает. Хотим обновить шоу «Ленинграда» немножко. </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Неужели танцев больше добавить?</span>
                    <span class="interview-answer">Да! Что еще предложишь? </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Клипы.</span>
                    <span class="interview-answer">Постоянно делаем, перманентно этим занимаемся.</span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Альбомы?</span>
                    <span class="interview-answer">Их мы не выпускаем уже давно. Да кому они нужны? Альбомы пусть молодые выпускают, у кого песен нет. А я уже 500 штук написал!</span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">  Когда я смотрю твои концерты, вижу, что ты делаешь на сцене, я офигеваю. Всем футболистам бы так!</span>
                    <hr />
                        <div class="interview-quote">Если бы все футболисты так играли, мы бы точно стали чемпионами мира!
                        </div>
                    <hr />    
                </div>   
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">За последний год ты покорил все рейтинги популярности, осуществляешь несколько творческих проектов. Какие планы на 2018 год?</span>
                    <span class="interview-answer">Бог его знает. Хотим обновить шоу «Ленинграда» немножко. </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Неужели танцев больше добавить?</span>
                    <span class="interview-answer">Да! Что еще предложишь? </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Клипы.</span>
                    <span class="interview-answer">Постоянно делаем, перманентно этим занимаемся.</span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Альбомы?</span>
                    <span class="interview-answer">Их мы не выпускаем уже давно. Да кому они нужны? Альбомы пусть молодые выпускают, у кого песен нет. А я уже 500 штук написал!</span>
                </div>              
            </div>
        </div>
    </div>
    <div class="interview-inbetween-photos justify-content-center row">
        <div class="col-sm-12 col-md-8 justify-content-center row">
            <div class="col-10 col-md-6"> <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo1.png"
                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo1@2x.png 2x,
                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo1@3x.png 3x"
                class="interview-img ">
            </div>
            <div class="col-10 col-md-6"> 
                <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo2.png"
                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo2@2x.png 2x,
                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo2@3x.png 3x" class="interview-img ">
            </div>
        </div>
    </div>
    <div class="interview-text">
        <div class="container  container-interview-spacer">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">За последний год ты покорил все рейтинги популярности, осуществляешь несколько творческих проектов. Какие планы на 2018 год?</span>
                    <span class="interview-answer">Бог его знает. Хотим обновить шоу «Ленинграда» немножко. </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Неужели танцев больше добавить?</span>
                    <span class="interview-answer">Да! Что еще предложишь? </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Клипы.</span>
                    <span class="interview-answer">Постоянно делаем, перманентно этим занимаемся.</span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Альбомы?</span>
                    <span class="interview-answer">Их мы не выпускаем уже давно. Да кому они нужны? Альбомы пусть молодые выпускают, у кого песен нет. А я уже 500 штук написал!</span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">  Когда я смотрю твои концерты, вижу, что ты делаешь на сцене, я офигеваю. Всем футболистам бы так!</span>
                    <hr />
                        <div class="interview-quote">Если бы все футболисты так играли, мы бы точно стали чемпионами мира!
                        </div> 
                    <hr />  
                </div>   
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">За последний год ты покорил все рейтинги популярности, осуществляешь несколько творческих проектов. Какие планы на 2018 год?</span>
                    <span class="interview-answer">Бог его знает. Хотим обновить шоу «Ленинграда» немножко. </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Неужели танцев больше добавить?</span>
                    <span class="interview-answer">Да! Что еще предложишь? </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Клипы.</span>
                    <span class="interview-answer">Постоянно делаем, перманентно этим занимаемся.</span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Альбомы?</span>
                    <span class="interview-answer">Их мы не выпускаем уже давно. Да кому они нужны? Альбомы пусть молодые выпускают, у кого песен нет. А я уже 500 штук написал!</span>
                </div>              
            </div>
        </div>
    </div>
    <div class="interview-inbetween-photos justify-content-center row">
        <div class="col-sm-12 col-md-8 justify-content-center row">
            <div class="col-10 col-md-6"> 
            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo.png"
                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo@2x.png 2x,
                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/photo@3x.png 3x"
                class="interview-img">
            </div>
            <div class="col-10 col-md-6"> 
                <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-41.png"
                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-41@2x.png 2x,
                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/layer-41@3x.png 3x"
                class="interview-img">
            </div>
        </div>
    </div>
    <div class="interview-text">
        <div class="container  container-interview-spacer">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">За последний год ты покорил все рейтинги популярности, осуществляешь несколько творческих проектов. Какие планы на 2018 год?</span>
                    <span class="interview-answer">Бог его знает. Хотим обновить шоу «Ленинграда» немножко. </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Неужели танцев больше добавить?</span>
                    <span class="interview-answer">Да! Что еще предложишь? </span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Клипы.</span>
                    <span class="interview-answer">Постоянно делаем, перманентно этим занимаемся.</span>
                </div>
                <div class="col-sm-12 col-md-7"> 
                    <span class="interview-question">Альбомы?</span>
                    <span class="interview-answer">Их мы не выпускаем уже давно. Да кому они нужны? Альбомы пусть молодые выпускают, у кого песен нет. А я уже 500 штук написал!</span>
                </div>   
            </div>
        </div>
    </div>
    <div class="other-interview">
        <div class="container">
            <div id="carouselExampleControls" class="carousel container slide" data-ride="carousel">
                <div class="carousel-inner carousel-inner-interview">
                    <div class="carousel-item row active justify-content-center text-justify interview-rows scrolling-wrapper">
                        <a href="/" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                class="interview-img">
                            <div class="interview-link">
                                <h3 class="interview-person">Иван Ургант</h3>
                                <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                певец, музыкант, продюсер</span>
                                <div class="interview-person-details row justify-content-center text-justify">
                                    <span class="interview-person-text col-4">12.03.1992</span>
                                    <span class="interview-person-text interview-person-view col-4">992</span>
                                    <span class="interview-person-text interview-person-comment col-4">12</span>
                                </div>
                                <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>  
                        <a href="/" class="col-10 col-md-6 col-lg-4  interview-desc">
                            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                class="interview-img">
                            <div class="interview-link">
                                <h3 class="interview-person">Иван Ургант</h3>
                                <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                певец, музыкант, продюсер</span>
                                <div class="interview-person-details row justify-content-center text-justify">
                                    <span class="interview-person-text col-4">12.03.1992</span>
                                    <span class="interview-person-text pinterview-person-view col-4">992</span>
                                    <span class="interview-person-text interview-person-comment col-4">12</span>
                                </div>
                                <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>  
                        <a href="/" class="col-10 col-md-6 col-lg-4  interview-desc">
                            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                class="interview-img">
                            <div class="interview-link">
                                <h3 class="interview-person">Иван Ургант</h3>
                                <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                певец, музыкант, продюсер</span>
                                <div class="interview-person-details row justify-content-center text-justify">
                                    <span class="interview-person-text col-4">12.03.1992</span>
                                    <span class="interview-person-text pinterview-person-view col-4">992</span>
                                    <span class="interview-person-text interview-person-comment col-4">12</span>
                                </div>
                                <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>          
                    </div>
                    <div class="carousel-item row justify-content-center text-justify interview-rows scrolling-wrapper">
                            <a href="/" class="col-10 col-md-6 col-lg-4 interview-desc">
                                <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                    srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                    https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                    class="interview-img">
                                <div class="interview-link">
                                    <h3 class="interview-person">Иван Ургант</h3>
                                    <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                    певец, музыкант, продюсер</span>
                                    <div class="interview-person-details row justify-content-center text-justify">
                                        <span class="interview-person-text col-4">12.03.1992</span>
                                        <span class="interview-person-text interview-person-view col-4">992</span>
                                        <span class="interview-person-text interview-person-comment col-4">12</span>
                                    </div>
                                    <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                        Жилые комплексы СПб с причалами или пирсом у воды.
                                        Жилые комплексы СПб с причалами или пирсом у воды.</span>
                                </div>
                        </a>  
                        <a href="/" class="col-10 col-md-6 col-lg-4 interview-desc">
                            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                class="interview-img">
                            <div class="interview-link">
                                <h3 class="interview-person">Иван Ургант</h3>
                                <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                певец, музыкант, продюсер</span>
                                <div class="interview-person-details row justify-content-center text-justify">
                                    <span class="interview-person-text col-4">12.03.1992</span>
                                    <span class="interview-person-text pinterview-person-view col-4">992</span>
                                    <span class="interview-person-text interview-person-comment col-4">12</span>
                                </div>
                                <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>  
                        <a href="/" class="col-10 col-md-6 col-lg-4  interview-desc">
                            <img src="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant.png"
                                srcset="https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@2x.png 2x,
                                https://m16-elite.ru/top-100/wp-content/themes/twentyseventeen/static/img/urgant@3x.png 3x"
                                class="interview-img">
                            <div class="interview-link">
                                <h3 class="interview-person">Иван Ургант</h3>
                                <span class="interview-person-desc">российский актёр, шоумен, телеведущий,
                                певец, музыкант, продюсер</span>
                                <div class="interview-person-details row justify-content-center text-justify">
                                    <span class="interview-person-text col-4">12.03.1992</span>
                                    <span class="interview-person-text pinterview-person-view col-4">992</span>
                                    <span class="interview-person-text interview-person-comment col-4">12</span>
                                </div>
                                <span class="interview-person-text">Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.
                                    Жилые комплексы СПб с причалами или пирсом у воды.</span>
                            </div>
                        </a>  
                    </div> 
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
    <div id="contact-form">
        <div class="container">
            <div class="row justify-content-center text-center">
                <form class="col-sm-12 row">
                    <div class="col-8 col-sm-4">  
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
                    <div class="col-8 col-sm-4">  
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
                    <div class="col-8 col-sm-4">  
                        <button class="sign-up-letters"> Подписаться на рассылку </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	<? } ?>
<?php get_footer(); ?>