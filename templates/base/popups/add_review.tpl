{?$current_user = $account->getUser()}
<div class="popup-window popup-add-review" data-class="blue-form-popup popup-review" data-title="Ваш отзыв о товаре" data-width="540">
	<ul class="errors"></ul>
	<form action="/catalog/addReview/" class="user-form" data-link="{$catalog_item->getUrl()}review/">
		<input class="item-id" type="hidden" name="item_id" value="{$catalog_item.id}" />
		<div class="field f-name">
			<div class="f-title">
				Нам и другим покупателям очень важно ваше мнение.<br />Пожалуйста, поделитесь вашим опытом.
			</div>
		</div>
		<div class="popup-grey-block">
			<div class="field-cont a-inline-cont">
				<div class="field f-name">
					<div class="f-title">
						<span>Ваше имя</span>
					</div>
					<div class="f-input">
						<input type="text" name="name" data-default="{$current_user.name}{if !empty($current_user.name) && !empty($current_user.surname)} {/if}{$current_user.surname}" />
					</div>
				</div>
				<div class="field f-mark">
					<div class="f-title">
						<span>Оценка<span class="descr"> —  нажмите на нужную звезду</span></span>
					</div>
					<div class="f-input">
						<input type="hidden" name="mark" value="" />
						<div class="a-inline-cont stars select-mark">
							<div></div><div></div><div></div><div></div><div></div>
						</div>
					</div>
				</div>
			</div>
			<div class="field">
				<div class="f-tape">
					<div class="blue-tape"><i class="icon i-worth"></i></div>
				</div>
				<div class="f-title">
					<span>Достоинства товара</span>
				</div>
				<div class="f-input">
					<textarea name="text_worth" rows="6"></textarea>
				</div>
			</div>
			<div class="field">
				<div class="f-tape">
					<div class="blue-tape"><i class="icon i-fault"></i></div>
				</div>
				<div class="f-title">
					<span>Недостатки товара</span>
				</div>
				<div class="f-input">
					<textarea name="text_fault" rows="6"></textarea>
				</div>
			</div>
			<div class="field f-text">
				<div class="f-tape">
					<div class="blue-tape"><i class="icon i-review-text"></i></div>
				</div>
				<div class="f-title">
					<span class="f-title">Отзыв: общее впечатление, рекомендации</span>
				</div>
				<div class="f-input">
					<textarea name="text" rows="6"></textarea>
				</div>
			</div>
			<div class="field f-descr">
				<p class="descr"><i class="icon i-descr"></i>Ваш отзыв появится на сайте после проверки модератором</p>
			</div>
		</div>
		<div class="buttons">
			<div class="button-cont a-inline-block">
				<button class="btn btn-white-yellow-big">Отправить отзыв</button>
			</div>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>	
	</form>
</div>