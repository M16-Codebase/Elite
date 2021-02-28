{if $request_segment.key == 'ru'}
	{?$pageTitle = 'М16 продаст вашу квартиру | М16-Недвижимость'}
	{?$pageDescription = 'М16 продаст вашу квартиру — продажа элитного жилья на вторичном рынке недвижимости через агентство Вячеслава Малафеева'}
{else}
	{?$pageTitle = 'Leave a request on estate sale | M16 Real Estate Agency'}
	{?$pageDescription = 'Leave a request on estate sale. Sell you luxury housing on the secondary market through M16 Real Estate Agency of Vyacheslav Malafeyev'}
{/if}
<!--<div class="top-bg" id="site-top">
	<div class='bg-img'></div>
	<div class="site-top">
		<h1 class="title" title="{$lang->get('М16 продаст вашу квартиру', 'M16 will sell your apartment')}">
			{$lang->get('<span>М16 продаст</span><br>вашу квартиру', '<span>M16 will sell</span><br>your apartment')|html}
		</h1>
	</div>
</div>-->

<section class="section__top">

	<div class="section__top-title">
		<h1 class="title" title="{$lang->get('М16 продаст вашу квартиру', 'M16 will sell your apartment')}">
			{$lang->get('<span>Вячеслав Малафеев</span>продаст вашу квартиру', '<span>M16 will sell</span><br>your apartment')|html}
		</h1>
	</div>

	<div class="section__items">
		<div class="block__big-man"></div>
		<div class="block__items-list">
			<div class="block__item">
				<div class="block__item-num">01</div>
				<div class="block__item-title">
					{$lang->get('Рекламные возможности', 'Advertising resources')}
				</div>
				<div class="block__item-text">
					{$lang->get('Усилиями нашей команды профессионалов на продажу вашего объекта будут работать все ведущие маркет плейсы и эффективные digital и офф-лайновые инструменты по взаимодействую с потенциальными клиентами, начиная с контекстной и таргетовой рекламы и заканчивая рекламными щитами и радио.', 'Our team uses all the leading marketplaces and effective digital and offline tools for attracting potential customers. We operate with everything from contextual and targeted advertising to banners and radio.')}
				</div>
			</div>
			<div class="block__item">
				<div class="block__item-num">02</div>
				<div class="block__item-title">
					{$lang->get('Бесплатная оценка недвижимости', 'Real estate valuation')}
				</div>
				<div class="block__item-text">
					{$lang->get('Наши специалисты быстро и бесплатно проведут первичную оценку стоимости лота, основываясь как на своем многолетнем опыте, так и на конкретных данных по рынку. А также сделают конечную корректировку после посещения объекта лично.', 'Our experts conduct a primary assessment of the lot value based on their long-term experience and current market data. The final cost will be determined after a personal visit to the property. The valuation is free of charge and takes a minimum of your time.')}
				</div>
			</div>
			<div class="block__item">
				<div class="block__item-num">03</div>
				<div class="block__item-title">
					{$lang->get('Своя база клиентов', 'Our client database')}
				</div>
				<div class="block__item-text">
					{$lang->get('За более чем 7 лет продуктивной работы на рынке недвижимости мы собрали большую базу регулярных партнеров и постоянных клиентов, и наладили инструменты взаимодействия с ними. Информация о вашем лоте, после оценки и подписания договора, в обязательном порядке будет доведена до наших лояльных покупателей, которым он будет интересен.', 'For more than 7 years of productive work in the real estate market, we have gathered a large base of regular partners and customers. Immediately after evaluating the property and signing the contract, we will expedite a full presentation about the property to a pool of loyal interested clients.')}
				</div>
			</div>
			<div class="block__item">
				<div class="block__item-num">04</div>
				<div class="block__item-title">
					{$lang->get('Широкая партнерская сеть', 'Wide partner network')}
				</div>
				<div class="block__item-text">
					{$lang->get('Независимо от того, хотите вы купить или продать недвижимость, на вас будет работать вся партнерская сеть компании М16 Недвижимость - более 500 застройщиков и риелторов', 'Regardless of whether you want to buy or sell real estate, there is our entire partner network at your service. M16 Group cooperates with more than 500 developers and realtors.')}
				</div>
			</div>
			<div class="block__item">
				<div class="block__item-num">05</div>
				<div class="block__item-title">
					{$lang->get('Финансовые гарантии продажи вашей квартиры', 'Financial guarantees of sale')}
				</div>
				<div class="block__item-text">
					{$lang->get('Мы всегда ориентируемся на качество и высокий уровень оказания услуг, мы работаем честно и прозрачно и уверены в своих возможностях, а потому готовы подкреплять уверенность наших клиентов конкретными финансовыми обязательствами со своей стороны.', 'We always focus on the quality and high level of service. We work honestly and transparently, and we are confident in our capabilities. Therefore, we are happy to assure our clients and provide strict financial obligations on our part.')}
				</div>
			</div>
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
		<div class="open-block">
			<div class="request-form a-justify">
				<input type="hidden" name="check_string" value="" />
				<input type="hidden" name="hash_string" value="" />
				<input type="hidden" name="feedbackType" value="{$form_type}">
				<!--<div class="f-group m-checkboxes">
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
				</div>-->
				<input type="hidden" name="address" />
				<input type="hidden" name="bed_number"/>
				<input type="hidden" name="area" />
				<input type="hidden" name="price"/>
				<input id="vid" type="hidden" name="species" value="1"/>
				<!--<input type="hidden" name="email" value="-" />-->
				<input type="hidden" name="message">
				<input id="subscr" type="hidden" name="subscr" value="on"/>
				<!--<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Адрес', 'Address')}</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">

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

						</div>
					</div>
				</label>
				<label class="field checkbox">
					<div class="f-row">
						<div class="f-input">

							<label for="vid"><div></div><span>{$lang->get('Видовая квартира', 'Great window view')}</span></label>
						</div>
					</div>
				</label>-->
				<!--</div>
            </div>
            <div class="w2 open-block">
			<div class="request-form a-justify">-->
				<input type="hidden" name="check_string" value="" />
				<input type="hidden" name="hash_string" value="" />
				<input type="hidden" name="feedbackType" value="{$form_type}">

				<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>{$lang->get('Имя', 'Name')}</span>
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
				<!--<label class="field">
					<div class="f-row">
						<div class="f-title">
							<span>E-mail</span>
							<span class="slash"></span>
						</div>
						<div class="f-input">

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
				</label>-->
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

</section>