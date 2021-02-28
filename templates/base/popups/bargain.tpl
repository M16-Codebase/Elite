{?$current_user = $account->getUser()}
<div class="popup-window popup-bargain" data-class="blue-form-popup popup-title-center" data-title="Давайте поторгуемся!" data-width="400">
	<ul class="errors"></ul>
	{?$checkString = time()}
	{?$checkStringSalt = $checkString . $hash_salt_string}
	<form action="/feedback/bargain/" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<input type="hidden" name="check_string" value="">
		<input type="hidden" name="hash_string" value="">
		<input type="hidden" name="item_id" value="" class="id-input">
		<div class="field">
			Видели этот товар дешевле? Дайте нам ссылку, и мы сделаем вам предложение
		</div>
		<div class="field-cont">
			<div class="field">
				<input type="text" name="phone_email" placeholder="Ваши контактные данные" data-default="{$current_user.email}" />
			</div>
			<div class="field">
				<input type="text" name="url" placeholder="Ссылка на товар" />
			</div>
			<div class="buttons">
				<button class="btn btn-white-yellow-big">Отправить заявку</button>
				<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
			</div>	
		</div>
	</form>
</div>