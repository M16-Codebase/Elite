{?$current_user = $account->getUser()}

{capture assign=months_list}
	{section loop=12 name=months}
		<option value="{iteration}">{iteration|plural_form:'месяц':'месяца':'месяцев'}</option>
	{/section}
{/capture}

<div class="popup-window popup-subscr subscr-avail" data-class="blue-form-popup" data-title="Уведомление о поступлении в продажу" data-width="400">
	<ul class="errors"></ul>
	<form action="/catalog/subscribeAvailable/">
		<input type="hidden" name="variant_id" class="input-id" />
		<div class="field">
			Укажите адрес вашей электронной почты. Мы сообщим Вам, когда товар появится в продаже.
		</div>
		<div class="field-cont">
			<div class="field">
				<input type="text" name="email" placeholder="Электронная почта" data-default="{$current_user.email}" />
			</div>
			<div class="field">
				<select name="months" class="chosen fullwidth">
					<option value="" selected>Срок ожидания</option>
					{$months_list|html}
				</select>
			</div>
			<div class="buttons">
				<button class="btn btn-white-yellow-big">Оформить подписку</button>
				<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
			</div>	
		</div>
	</form>
</div>

<div class="popup-window popup-subscr subscr-price" data-class="blue-form-popup" data-title="Подписка на изменение цен" data-width="400">
	<ul class="errors"></ul>
	<form action="/catalog/subscribePrice/">
		<input type="hidden" name="variant_id" class="input-id" />
		<div class="field">
			Укажите адрес вашей электронной почты. Мы сообщим вам, когда у товара изменится цена.
		</div>
		<div class="field-cont">
			<div class="field">
				<input type="text" name="email" placeholder="Электронная почта" data-default="{$current_user.email}" />
			</div>
			<div class="field">
				<select name="months" class="chosen fullwidth">
					<option value="" selected>Срок ожидания</option>
					{$months_list|html}
				</select>
			</div>
			<div class="buttons">
				<button class="btn btn-white-yellow-big">Оформить подписку</button>
				<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
			</div>	
		</div>
	</form>
</div>