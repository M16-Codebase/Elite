<div class="popup-window popup-registration" data-class="yellow-form-popup" data-title="Регистрация пользователя" data-width="540">
	<ul class="errors"></ul>
	<p class="top-text">Зарегистрируйтесь и вы сможете участвовать в наших бонусных программах и сократить время покупок.</p>
	<form action="/welcome/registration/" class="tabs-cont" autocomplete="off">
		<div class="tabs-header a-inline-cont">
			<a href=".reg-person" class="tab-title m-current">Физическое лицо</a>
			<a href=".reg-org" class="tab-title">Юридическое лицо</a>
		</div>
		<div class="tabs-body">
			<div class="descr security">Мы гарантируем сохранность информации. Данные, вводимые вами, будут использоваться только для обработки заказов.</div>
			
			<div class="tab-page reg-person">
				<input type="hidden" name="person_type" value="fiz" />
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Имя</div>
						<div class="f-input"><input type="text" name="name" tabindex="1" /></div>
					</label>
					<label class="field f-col">
						<div class="f-title">Фамилия</div>
						<div class="f-input"><input type="text" name="surname" tabindex="2" /></div>
					</label>
				</div>
				<label class="field f-row descr">
					<input type="checkbox" name="master" class="cbx" />
					Я работаю сантехником / монтажником <a href="/main/programs/" class="descr" target="_blank">— для вас бонусы</a>				
				</label>
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Телефон</div>
						<div class="f-input"><input type="text" name="phone" tabindex="3" /></div>
					</label>
					<label class="field f-col">
						<div class="f-title">Электронная почта</div>
						<div class="f-input"><input type="text" name="email" tabindex="4" /></div>
					</label>
				</div>
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Пароль <a href="#" class="descr generate-pass">— Сгенерировать</a></div>
						<div class="f-input"><input type="text" name="pass" tabindex="5" /></div>
					</label>
					<label class="field f-col">
						<div class="f-title">Пароль еще раз для надежности</div>
						<div class="f-input"><input type="text" name="pass2" tabindex="6" /></div>
					</label>
				</div>
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Регион</div>
						<div class="f-input">
							<select name="region_id" class="chosen fullwidth" tabindex="7">
								{foreach from=$segments item=reg}
									<option value="{$reg.id}"{if $reg.id == $request_segment.id} selected{/if}>{$reg.title}</option>
								{/foreach}
							</select>
						</div>
					</label>
					<label class="field f-col">
						<div class="f-title">Реферальный номер друга</div>
						<div class="f-input"><input type="text" name="referer" tabindex="8" /></div>
					</label>
				</div>
				<div class="f-row m-bordered">
					<label class="field f-row descr">
						<input type="checkbox" name="order_status" class="cbx subscr-cbx" checked />
						Подписка на уведомления об изменениях статусов заказов				
					</label>
					<label class="field f-row descr">
						<input type="checkbox" name="subscribe" class="cbx subscr-cbx" checked />
						Подписка на информационные материалы Мастер-Сантехник		
					</label>
				</div>			
			</div>
							
			<div class="tab-page reg-org a-hidden">
				<input type="hidden" name="person_type" value="org" />
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Наименование компании</div>
						<div class="f-input"><input type="text" name="company_name" tabindex="1" /></div>
					</label>
					<label class="field f-col">
						<div class="f-title">ИНН</div>
						<div class="f-input"><input type="text" name="inn" tabindex="2" /></div>
					</label>
				</div>
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Имя контактного лица</div>
						<div class="f-input"><input type="text" name="name" tabindex="3" /></div>
					</label>
					<label class="field f-col">
						<div class="f-title">Фамилия контактного лица</div>
						<div class="f-input"><input type="text" name="surname" tabindex="4" /></div>
					</label>
				</div>
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Телефон</div>
						<div class="f-input"><input type="text" name="phone" tabindex="5" /></div>
					</label>
					<label class="field f-col">
						<div class="f-title">Электронная почта</div>
						<div class="f-input"><input type="text" name="email" tabindex="6" /></div>
					</label>
				</div>
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Пароль <a href="#" class="descr generate-pass">— Сгенерировать</a></div>
						<div class="f-input"><input type="text" name="pass" tabindex="7" /></div>
					</label>
					<label class="field f-col">
						<div class="f-title">Пароль еще раз для надежности</div>
						<div class="f-input"><input type="text" name="pass2" tabindex="8" /></div>
					</label>
				</div>
				<label class="field f-row">
					<div class="f-title">Адрес доставки</div>
					<div class="f-input"><input type="text" name="address" tabindex="9" /></div>			
				</label>
				<div class="f-row justify">
					<label class="field f-col">
						<div class="f-title">Регион</div>
						<div class="f-input">
							<select name="region_id" class="chosen fullwidth" tabindex="10">
								{foreach from=$segments item=reg}
									<option value="{$reg.id}"{if $reg.id == $request_segment.id} selected{/if}>{$reg.title}</option>
								{/foreach}
							</select>
						</div>
					</label>
				</div>
				<div class="f-row m-bordered">
					<label class="field f-row descr">
						<input type="checkbox" name="order_status" class="cbx subscr-cbx" checked />
						Подписка на уведомления об изменениях статусов заказов				
					</label>
					<label class="field f-row descr">
						<input type="checkbox" name="subscribe" class="cbx subscr-cbx" checked />
						Подписка на информационные материалы Мастер-Сантехник		
					</label>
				</div>
			</div>
							
		</div>
		<div class="buttons">
			<div class="btn-cont a-inline-block">
				<button class="btn btn-white-yellow-big clear-add">Зарегистрироваться</button>
			</div>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>
	</form>	
</div>