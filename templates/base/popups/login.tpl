<div class="popup-window popup-login" data-class="yellow-form-popup" data-title="Авторизация пользователя" data-width="540">
	<ul class="errors"></ul>
	<p class="top-text">Если у вас еще нет учетной записи, <a href=".popup-registration" class="close-popup" data-toggle="popup" data-action="open">зарегистрируйтесь</a>.</p>
	<form action="/login/" class="tabs-cont">
		<div class="tabs-body">
			<div class="f-row justify">
				<label class="field f-col">
					<div class="f-title">Электронная почта</div>
					<div class="f-input"><input type="text" name="email" tabindex="1" /></div>
				</label>
				<label class="field f-col">
					<div class="f-title">Пароль <a href=".popup-restore" class="descr close-popup" data-toggle="popup" data-action="open">— Забыли пароль?</a></div>
					<div class="f-input"><input type="password" name="pass" tabindex="2" /></div>
				</label>
			</div>						
		</div>
		<div class="buttons">
			<div class="btn-cont a-inline-block">
				<button class="btn btn-white-yellow-big clear-add">Войти</button>
			</div>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>
	</form>	
</div>