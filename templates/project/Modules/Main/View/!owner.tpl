{if $request_segment.key == 'ru'}
	{?$pageTitle = 'М16 продаст вашу квартиру | М16-Недвижимость'}
	{?$pageDescription = 'М16 продаст вашу квартиру — продажа элитного жилья на вторичном рынке недвижимости через агентство Вячеслава Малафеева'}
{else}
	{?$pageTitle = 'Leave a request on estate sale | M16 Real Estate Agency'}
	{?$pageDescription = 'Leave a request on estate sale. Sell you luxury housing on the secondary market through M16 Real Estate Agency of Vyacheslav Malafeyev'}
{/if}
<div class="top-bg" id="site-top">
	<div class='bg-img'></div>
	<div class="site-top">
		<h1 class="title" title="{$lang->get('М16 продаст вашу квартиру', 'M16 will sell your apartment')}">
			{$lang->get('<span>М16 продаст</span><br>вашу квартиру', '<span>M16 will sell</span><br>your apartment')|html}
		</h1>
	</div>
</div>
	
<div class="sand-wrap">
	<div class="request-wrap row">
		<h2 class="title"><span>{$lang->get('Заявка', 'Request')}</span></h2>
		<div class="small-descr">
			{$lang->get('Наши специалисты имеют многолетний опыт работы на рынке элитной недвижимости. Укажите параметры вашей квартиры и мы сделаем вам интересное предложение.','Our specialists have a long experience in the field of real estate market. Enter the parameters of your apartment, and we will make you an interesting suggestion.')}
		</div>
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		<form action="/feedback/makeRequest/" class="user-form js-owner-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<div class="w2 open-block">
			<div class="request-form a-justify">
				<input type="hidden" name="check_string" value="" />
				<input type="hidden" name="hash_string" value="" />
				<input type="hidden" name="feedbackType" value="{$form_type}">
				<div class="f-group m-checkboxes">
					<label class="field checkbox">
						<div class="f-row">
							<div class="f-input">
								<input id="realestate-ch" type="radio" name="estate_type" value="{$estate_type_vals.primary}" checked/>
								<label for="realestate-ch"><div></div><span>{$lang->get('в строящемся<br>доме', 'In new<br>building')|html}</span></label>
							</div>
						</div>
					</label>
					<span class="slash"></span>
					<label class="field checkbox">
						<div class="f-row">
							<div class="f-input">
								<input id="resale-ch" type="radio" name="estate_type" value="{$estate_type_vals.resale}" checked/>
								<label for="resale-ch"><div></div><span>{$lang->get('на вторичном<br>рынке', 'Resale<br>offer')|html}</span></label>
							</div>
						</div>
					</label>
				</div>
				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Адрес', 'Address')}</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">
							<input type="text" name="address" />
						</div>
					</div>
				</label>
				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Число спален', 'Bedrooms Number')}</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">
							<input type="text" name="bed_number" />
						</div>
					</div>
				</label>
				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Площадь, м<sup>2</sup>', 'Area, m<sup>2</sup>')|html}</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">
							<input type="text" name="area" />
						</div>
					</div>
				</label>
				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Цена, млн руб.', 'Price, mln rub.')}</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">
							<input type="text" name="price" />
						</div>
					</div>
				</label>
				<label class="field checkbox">
					<div class="f-row">
						<div class="f-input">
							<input id="vid" type="checkbox" name="species" value="1"/>
							<label for="vid"><div></div><span>{$lang->get('Видовая квартира', 'Great window view')}</span></label>
						</div>
					</div>
				</label>
			</div>
		</div>
		<div class="w2 open-block">
			<div class="request-form a-justify">
				<input type="hidden" name="check_string" value="" />
				<input type="hidden" name="hash_string" value="" />
				<input type="hidden" name="feedbackType" value="{$form_type}">

				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Имя', 'Name')}</span>
                            <span>*</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">
							<input type="text" name="author" />
						</div>
						<div class="f-errors a-hidden">
							{$lang->get('Обязательное поле', 'Required')}
						</div>
					</div>
				</label>
				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Тел.', 'Tel.')}</span>
                            <span>*</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">
							<input type="tel" name="phone" />
						</div>
						<div class="f-errors a-hidden">
							{$lang->get('Обязательное поле', 'Required')}
						</div>
					</div>
				</label>
				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>E-mail</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">
							<input type="email" name="email" />
						</div>
						<div class="f-errors a-hidden">
							{$lang->get('Обязательное поле', 'Required')}
						</div>
					</div>
				</label>
				<label class="field">
					<div class="f-row">
						<div class="f-input">
							<textarea type="text" name="message"></textarea>
						</div>
					</div>
				</label>
				<label class="field checkbox">
					<div class="f-row">
						<div class="f-input">
							<input id="subscr" type="checkbox" name="subscr" checked/>
							<label for="subscr"><div></div><span>{$lang->get('Подписка на интересные предложения', 'Sign up to interesting offers')}</span></label>
						</div>
					</div>
				</label>
			</div>
		</div>
		<div class="buttons open-block">
			<button class="btn m-sand">{$lang->get('Отправить заявку', 'Send your request')}</button>
		</div>
		<div class="sended-block">
			<div class="main">{$lang->get('Заявка отправлена', 'Message sent')}</div>
			<div class="small-descr">
				{$lang->get('Ваша заявка успешно отправлена<br>консультантам агентства<br>недвижимости М16. Спасибо за ваше<br>обращение!','Your request is successfully sent<br>to conultants of M16 real estate agency.<br>Thank you for contacting us!')|html}
			</div>
			<a href="{$url_prefix}/" class="btn m-white">{$lang->get('На главную страницу', 'Main page')}</a>
		</div>
		</form>
	</div>
</div>