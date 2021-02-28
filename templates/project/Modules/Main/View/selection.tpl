{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Индивидуальный подбор квартиры | М16-Недвижимость'}
	{?$pageDescription = 'Индивидуальный подбор квартиры — индивидуальный подбор жилья в элитных новостройках или на вторичном рынке элитной недвижимости от агентства Вячеслава Малафеева'}
{else}
	{?$pageTitle = 'Leave a request on selection of estate | M16 Real Estate Agency'}
	{?$pageDescription = 'Leave a request on selection of estate. Customized selection of luxury estate in new buildings or on the secondary market at M16 Real Estate Agency of Vyacheslav Malafeyev'}
{/if}
<div class="top-bg" id="site-top">
	<div class='bg-img'></div>
	<div class="site-top">
			<h1 class="title" title="{$lang->get('Индивидуальный подбор картиры', 'Personal apartment selection')}">
				{$lang->get('<span>Индивидуальный</span><br>подбор картиры', '<span>Personal</span><br>apartment selection')|html}
			</h1>
	</div>
</div>
	
	<div class="request-wrap row">
		<h2 class="title"><span>{$lang->get('Заявка', 'Request')}</span></h2>
		<div class="small-descr">
			{$lang->get('Наши специалисты подготовят для вас предложение, индивидуально сформированное на основе указанных вами параметров. Мы знаем свое дело. Мы достойны вашего доверия.', 'Our specialists will prepare a proposal individually formed on the basis of the parameters you specified. We know our business. We deserve your trust.')}
		</div>
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		<form action="/feedback/makeRequest/" class="user-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<div class="w2 open-block">
			<div class="request-form a-justify">
				<input type="hidden" name="check_string" value="" />
				<input type="hidden" name="hash_string" value="" />
				<input type="hidden" name="feedbackType" value="{$form_type}">
				<div class="f-group m-checkboxes">
					<label class="field checkbox">
						<div class="f-row">
							<div class="f-input">
								<input id="realestate-ch" type="checkbox" name="primary" value="1" checked/>
								<label for="realestate-ch"><div></div><span>{$lang->get('в строящемся<br>доме', 'In new<br>building')|html}</span></label>
							</div>
						</div>
					</label>
					<span class="slash"></span>
					<label class="field checkbox">
						<div class="f-row">
							<div class="f-input">
								<input id="resale-ch" type="checkbox" name="resale" value="1" checked/>
								<label for="resale-ch"><div></div><span>{$lang->get('на вторичном<br>рынке', 'Resale<br>offer')|html}</span></label>
							</div>
						</div>
					</label>
				</div>
				<div class="field f-dropdown" data-hoverable="0">
					<div class="dropdown-toggle"><span data-title="{$lang->get('все районы', 'all districts')}" data-title_one="{$lang->get('район', 'district')}" data-title_two="{$lang->get('района', 'districts')}" data-title_five="{$lang->get('районов', 'districts')}">{$lang->get('все районы', 'all districts')}</span><div>{fetch file=$path . "menu.svg"}</div></div>
					<ul class="dropdown-menu">
						{foreach from=$districts item=sval_view key=val}
							<li>
								<input id="district{$val}" type="checkbox" name="district[]" value="{$val}">
								<label for="district{$val}">
									<div></div>
									<span>{$sval_view.title}</span>
								</label>
							</li>
						{/foreach}
					</ul>
				</div>
				<div class="field m-bed-ch{if $request_segment.key != 'ru'} m-eng{/if}">
					<div class="f-title">
						<span>{$lang->get('Число<br>спален', 'Bedrooms<br>Number')|html}</span>
						<span class="slash"></span>
					</div>
					<div class="f-input">
						<label class="btn m-bedroom">
							<input type="checkbox" name="bed_number[]" value="{$bed_number_vals[1]}" class="m-hidden-input">
							<span>1</span>
						</label>
						<label class="btn m-bedroom">
							<input type="checkbox" name="bed_number[]" value="{$bed_number_vals[2]}" class="m-hidden-input">
							<span>2</span>
						</label>
							<label class="btn m-bedroom">
							<input type="checkbox" name="bed_number[]" value="{$bed_number_vals[3]}" class="m-hidden-input">
							<span>3</span>
						</label>
							<label class="btn m-bedroom">
							<input type="checkbox" name="bed_number[]" value="{$bed_number_vals[4]}" class="m-hidden-input">
							<span>4</span>
						</label>
						<label class="btn m-bedroom m-five">
							<input type="checkbox" name="bed_number[]" value="{$bed_number_vals[5]}" class="m-hidden-input">
							<span>5 +</span>
						</label>
					</div>
				</div>
				<div class="field">
					<div class="f-title">
						<span>{$lang->get('Площадь,<br>м<sup>2</sup>', 'Area,<br>m<sup>2</sup>')|html}</span>
						<span class="slash"></span>
					</div>
					<div class="f-input slider-wrap">
						<input type="text" name="area_min" class="input-min range-input a-left" maxlength="3"/>
						<input type="text" name="area_max" class="input-max range-input a-left" maxlength="3"/>
						<div class="slider range" data-min="1" data-max="300" data-step="10"></div>
					</div>
				</div>
				<div class="field">
					<div class="f-title m-rt-padding">
						<span>{$lang->get('Цена,<br>млн руб.', 'Price,<br>mln rub.')|html}</span>
						<span class="slash"></span>
					</div>
					<div class="f-input slider-wrap">
						<input type="text" name="price_min" class="input-min range-input a-left" maxlength="3"/>
						<input type="text" name="price_max" class="input-max range-input a-left" maxlength="3"/>
						<div class="slider range" data-min="1" data-max="300" data-step="10"></div>
					</div>
				</div>
				
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
							<input id="subscr" type="checkbox" name="subscr" value="1" checked/>
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
				{$lang->get('Ваша заявка успешно отправлена<br>консультантам агентства<br>недвижимости М16. Спасибо за ваше<br>обращение!', 'Your request is successfully sent<br>to conultants of M16 real estate agency.<br>Thank you for contacting us!')|html}

			</div>
			<a href="{$url_prefix}/" class="btn m-white">{$lang->get('На главную страницу', 'Main page')}</a>
		</div>
		</form>
	</div>