{?$current_user = $account->getUser()}
<div class="popup-window popup-callback" data-class="blue-form-popup callback-form-popup popup-title-center" data-title="Мы вам позвоним" data-width="400">
	<ul class="errors"></ul>
	{?$checkString = time()}
	{?$checkStringSalt = $checkString . $hash_salt_string}
	<form action="/feedback/callback/" data-cont=".user-data" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<input type="hidden" name="check_string" value="">
		<input type="hidden" name="hash_string" value="">
		<div class="field">
			Укажите, пожалуйста, ваше имя и телефон<br /> и наш сотрудник свяжется с вами.
		</div>
		<div class="field-cont">
			<div class="field">
				<input type="text" name="name" placeholder="Ваше имя" data-default="{$current_user.name}{if !empty($current_user.name) && !empty($current_user.surname)} {/if}{$current_user.surname}" />
			</div>
			<div class="field">
				<input type="text" name="phone" placeholder="Телефон" data-default="{$current_user.phone}" />
			</div>
			<div class="buttons">
				<button class="btn btn-white-yellow-big">Отправить заявку</button>
				<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
			</div>	
		</div>
	</form>
</div>