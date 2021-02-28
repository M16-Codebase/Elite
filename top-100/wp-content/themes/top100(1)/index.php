<!doctype html>
<html lang="ru">
		<?php get_header(); ?>
		<div id="brochure">
			<div class="container container-spacer">
				<div class="row justify-content-center text-center brochure-div">
					<div class="col-sm-12 col-md-7">          
						<img src="static/img/brochure.png"
						class="brochure-img">
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
			<div class="container">
				<div class="row justify-content-center">
						<div class="col-4 col-md-3 col-lg-2 photo-external">
							<img class="photo-normal" alt="m-16"  src="static/img/layer-25.png"/>
						</div>
						<div class="col-4 col-md-3 col-lg-2 photo-external">
							<img class="photo-normal" alt="m-16"  src="static/img/layer-26.png"/>
						</div>
						<div class="col-4 col-md-3 col-lg-2 photo-external">
							<img class="photo-normal" alt="m-16"  src="static/img/layer-27.png"/>
						</div>
						<div class="col-4 col-md-3 col-lg-2 photo-external">
							<img class="photo-normal" alt="m-16"  src="static/img/layer-28.png"/>
						</div>
						<div class="col-4 col-md-3 col-lg-2 photo-external">
							<img class="photo-normal" alt="m-16"  src="static/img/layer-30.png"/>
						</div>
						<div class="col-4 col-md-3 col-lg-2 photo-external">
							<img class="photo-normal" alt="m-16"  src="static/img/layer-32.png"/>
						</div>
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
									<img class="d-block col-4 col-md-3 col-lg-2 partner-img" src="static/img/ginza.png" alt="First slide">
									<img class="d-block col-4 col-md-3 col-lg-2 partner-img" src="static/img/mercedes.png" alt="First slide">
									<img class="d-block col-4 col-md-3 col-lg-2  partner-img" src="static/img/zenit.png" alt="First slide">
									<img class="d-none d-md-block col-md-3 col-lg-2 partner-img" src="static/img/land-rover.png" alt="Third slide">
									<img class="d-none d-lg-block col-lg-2 partner-img" src="static/img/italy.png" alt="Second slide">
									<img class="d-none d-lg-block col-lg-2 partner-img" src="static/img/rad.png" alt="First slide">
								</div>
								<div class="carousel-item row">
									<img class="d-block col-4  col-md-3 col-lg-2 partner-img" src="static/img/zenit.png" alt="First slide">
									<img class="d-block col-4  col-md-3 col-lg-2 partner-img" src="static/img/land-rover.png" alt="Third slide">
									<img class="d-block col-4  col-md-3 col-lg-2 partner-img"src="static/img/italy.png" alt="Second slide">
									<img class="d-none d-md-block col-md-3 col-lg-2 partner-img"  src="static/img/rad.png" alt="First slide">
									<img class="d-none d-lg-block col-lg-2 partner-img" src="static/img/ginza.png" alt="First slide">
									<img class="d-none d-lg-block col-lg-2 partner-img" src="static/img/mercedes.png" alt="First slide">
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
							<div class="row justify-content-center text-justify interview-rows">
								<a href="/" class="col-sm-10 col-md-6 col-lg-4 interview-desc">
									<img src="static/img/urgant.png"
										srcset="static/img/urgant@2x.png 2x,
										static/img/urgant@3x.png 3x"
										class="interview-img">
									<div href="/" class="interview-link">
										<h3 class="interview-person">Иван Ургант</h3>
										<span class="interview-person-desc">российский актёр, шоумен, телеведущий,
										певец, музыкант, продюсер</span>
									</div>
								</a>  
								<a href="/" class="col-sm-10 col-md-6 col-lg-4  interview-desc">
									<img src="static/img/urgant.png"
									srcset="static/img/urgant@2x.png 2x,
									static/img/urgant@3x.png 3x"
									class="interview-img">
									<div href="/" class="interview-link">
										<h3 class="interview-person">Иван Ургант</h3>
										<span class="interview-person-desc">российский актёр, шоумен, телеведущий,
										певец, музыкант, продюсер</span>
									</div>
								</a>  
								<a href="/" class="col-sm-10 col-md-6 col-lg-4  interview-desc">
									<img src="static/img/urgant.png"
									srcset="static/img/urgant@2x.png 2x,
									static/img/urgant@3x.png 3x"
									class="interview-img">
									<div href="/" class="interview-link">
										<h3 class="interview-person">Иван Ургант</h3>
										<span class="interview-person-desc">российский актёр, шоумен, телеведущий,
										певец, музыкант, продюсер</span>
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
						<div class="row justify-content-center text-justify publication-rows">
							<a href="/" class="col-sm-10 col-md-6 col-lg-4 publication-desc">
								<img src="static/img/layer.png"
									srcset="static/img/layer@2x.png 2x,
									static/img/layer@3x.png 3x"
									class="publication-img">
								<div class="publication-link">
									<span class="publication-text">Жилые комплексы СПб
										с причалами или пирсом у воды</span>
								</div>
							</a>  
							<a href="/" class="col-sm-10 col-md-6 col-lg-4  publication-desc">
								<img src="static/img/layer.png"
								srcset="static/img/layer@2x.png 2x,
								static/img/layer@3x.png 3x"
								class="publication-img">
								<div class="publication-link">
									<span class="publication-text">Жилые комплексы СПб
										с причалами или пирсом у воды</span>
								</div>
							</a>  
							<a href="/" class="col-sm-10 col-md-6 col-lg-4  publication-desc">
								<img src="static/img/layer.png"
								srcset="static/img/layer@2x.png 2x,
								static/img/layer@3x.png 3x"
								class="publication-img">
								<div class="publication-link">
									<span class="publication-text">Жилые комплексы СПб
										с причалами или пирсом у воды</span>
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
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"  crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" crossorigin="anonymous"></script>
<?php get_footer(); ?>