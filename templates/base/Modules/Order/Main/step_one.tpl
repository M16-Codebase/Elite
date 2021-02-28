<div class="cart-page step-one content-white-block">
	<div class="white-block-inner">
			<h1>Оформление заказа №{$user_order.id}</h1>
			<div class="cart-registration justify">
				<div class="col1">
					<div class="registration-title"><i class="icon i-login"></i>Авторизоваться</div>
					<div class="registration-info">
						Если у вас уже есть учетная запись, вы можете авторизоваться
						и воспользоваться преимуществами бонусных программ.
					</div>
					<form method="POST">
						<input type="text" name="email" placeholder="Электронная почта" />
						<input type="password" name="pass" placeholder="Пароль" />
						{if !empty($error)}
							<div class="errors">
								<strong>Неверный логин или пароль</strong>
							</div>
						{/if}
						<div class="buttons">
							<button class="btn btn-yellow">Войти<span class="more-arrow">►</span></button>
							<em><a href=".popup-restore" data-toggle="popup" data-action="open">— Забыли пароль?</a></em>
						</div>
					</form>
				</div>
				<div class="col1">
					<div class="registration-title"><i class="icon i-first-purchase"></i>Покупаете первый раз?</div>
					<div class="registration-info">
						Зарегистрируйтесь и вы сможете участвовать в наших бонусных программах
						и сократить время следующих покупок. Это займет всего пару минут.
					</div>
					<a href=".popup-registration" class="btn btn-yellow" data-toggle="popup" data-action="open">Быстрая регистрация<span class="more-arrow">►</span></a>
					<div class="registration-bottom">Регистрируясь, вы соглашаетесь<br /> с <a href="#">политикой конфиденциальности</a></div>
				</div>
				<div class="col1">
					<div class="registration-title"><i class="icon i-without-registr"></i>Оформить без регистрации</div>
					<div class="registration-info">
						Вы можете оформить заказ и без регистрации. Бонусные программы в этом случае не действуют.
					</div>
					<a href="/order/form/" class="btn btn-white-yellow">Купить без регистрации<span class="more-arrow">►</span></a>
				</div>
			</div>
	</div>
</div>
{include file="/components/bonus-program.tpl"}
