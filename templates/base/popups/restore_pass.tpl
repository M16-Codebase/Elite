{?$current_user = $account->getUser()}
<div class="popup-window popup-restore" data-class="blue-form-popup popup-title-center" data-title="Восстановление пароля" data-width="450">
	<ul class="errors"></ul>
	<form action="/welcome/passwordRecovery/">		
		<div class="field">
			Мы не можем восстановить ваш старый пароль.<br />
			Укажите адрес вашей электронной почты,<br />
			мы пришлем вам письмо с инструкциями
		</div>
		<div class="field-cont">
			<div class="field">
				<input type="text" name="email" placeholder="Электронная почта" data-default="{$current_user.email}" />
			</div>
			<div class="buttons">
				<button class="btn btn-white-yellow-big">Отправить заявку</button>
				<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
			</div>	
		</div>
	</form>
</div>