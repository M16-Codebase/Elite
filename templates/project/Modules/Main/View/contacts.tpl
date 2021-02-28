{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Контактная информация | М16-Недвижимость'}
	{?$pageDescription = 'Контактная информация санкт-петербургского агентства недвижимости Вячеслава Малафеева М16-Недвижимость'}
{else}
	{?$pageTitle = 'Contact Information | M16 Real Estate Agency'}
	{?$pageDescription = 'Contact Information for St.Petersburg M16 Real Estate Agency of  Vyacheslav Malafeyev'}
{/if}
<div class="top-bg m-white">
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Контактная информация', 'Contact Information')}"><span>{$lang->get('Контактная', 'Contact')}</span><br>{$lang->get('информация', 'information')}</h1>
		<div class="main">{$lang->get('М16 недвижимость', 'M16 Real Estate Agency')}</div>
	</div>
</div>
<div class="contacts">
	{if !empty($contacts.phone)}<span class="col roistat_phone">{$contacts.phone}</span>{/if}
	<span class="slash"></span>
	{if !empty($contacts.office_address)}<span class="js-address col m-sand"></span>{/if}
	<div class="descr">{$contacts.office_work_mode}</div>
</div>
<div class="contacts-map" id="contacts-map">
	{if !empty($contacts.office_address_coords)}
		<div class="map-big">
				<div class="close-map" title="Закрыть карту">{fetch file=$path . "close.svg"}</div>
				<div class="map" data-coords="{$contacts.office_address_coords}"></div>
				<div class="infoblock-content a-hidden">
					<div class="map-item-content">
						<div class="item-type main"></div>
						<div class="item-title descr-big">{$site_config.company_name}</div>
						<div class="js-address address"></div>
						<div class="descr">{$contacts.office_work_mode}</div>
					</div>
				</div>
				{*{foreach from=$item.infra item=infra}
					<div class="marker" data-coords="{$infra.address_coords}" data-title="{$infra.title}" data-img=""></div>
				{/foreach}*}
		</div>
	{/if}
	<div class="map-small">
		{if !empty($contacts.office_address_coords)}
			<div class="open-map">
				<div class="btn m-magenta-fill">
        <span class="open-map-cover"></span>
        {fetch file=$path . "expand.svg"} {$lang->get('Открыть большую карту', 'Open Large Map')}
        </div>
				<div class="marker"></div>
				<div class="map-lock"></div>
				<div class="map-wrap">
					<div class="map" data-coords="{$contacts.office_address_coords}"></div>
				</div>
			</div>
		{/if}
	</div>
	{if !empty($managers)}
	<div class="swiper-container consultant">
		<div class="swiper-wrapper">
			{foreach from=$managers item=consultant name=cons}
				<div class="swiper-slide person{if total > 3} m-border{/if}">
					{if !empty($consultant.photo) && !empty($consultant.photo->getUrl())}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
					{if !empty($consultant.name) && !empty($consultant.surname)}<div class="name">{$consultant.name}<br>{$consultant.surname}</div>{/if}
					{if !empty($consultant.email)}<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}
					{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
				</div>
			{/foreach}
		</div>
		<div class="swiper-button-prev{if !empty($smarty.foreach.cons.total) && $smarty.foreach.cons.total < 5} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
		<div class="swiper-button-next{if !empty($smarty.foreach.cons.total) && $smarty.foreach.cons.total < 5} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
	</div>
{/if}
</div>


{if !empty($contacts.facebook) || !empty($contacts.odnoklassniki) || !empty($contacts.linkedin) || !empty($contacts.twitter) || !empty($contacts.instagram) || !empty($contacts.vk)}
<div class="contacts-social">
	<div class="title"><span>{$lang->get('М16 в соцсетях', 'M16 in social networks')}</span></div>
	{if !empty($contacts.facebook)}<a href="{$contacts.facebook}" target="_blank"><span>{fetch file=$path . "facebook.svg"}</span>Facebook</a>{/if}
	<span class="slash"></span>
	{if !empty($contacts.odnoklassniki)}<a href="{$contacts.odnoklassniki}" target="_blank"><span>{fetch file=$path . "odnoklassniki.svg"}</span>{$lang->get('Одноклассники', 'Odnoklassniki')}</a>{/if}
	<span class="slash"></span>
	{if !empty($contacts.linkedin)}<a href="{$contacts.linkedin}" target="_blank"><span>{fetch file=$path . "linkedin.svg"}</span>Linked in</a>{/if}
	<span class="slash"></span>
	{if !empty($contacts.twitter)}<a href="{$contacts.twitter}" target="_blank"><span>{fetch file=$path . "twitter.svg"}</span>Twitter</a>{/if}
	<span class="slash"></span>
	{if !empty($contacts.instagram)}<a href="{$contacts.instagram}" target="_blank"><span>{fetch file=$path . "instagram.svg"}</span>Instagram</a>{/if}
	<span class="slash"></span>
	{if !empty($contacts.vk)}<a href="{$contacts.vk}" target="_blank"><span>{fetch file=$path . "vk.svg"}</span>{$lang->get('Вконтакте', 'VKontakte')}</a>{/if}
</div>
{/if}
<div class="bowtie-wrap row">
	<div class="w2">
		<div class="title">{$lang->get('Хотите <b>найти</b> себе<br>идеальную квартиру без усилий?', 'Trying <b>to find</b> yourself<br>the perfect apartment without effort?')|html}</div>
		<div class="descr">{$lang->get('Опытные специалисты в области недвижимости помогут Вам выбрать оптимальный вариант.', 'Qualified real estate professionals will help you to choose the best one.')}</div>
		<a href="{$url_prefix}/selection/" class="btn m-light-magenta m-vw">{$lang->get('Персональный подбор', 'Personal selection')}</a>
	</div>
	<div class="w2">
		<div class="title">{$lang->get('Хотите <b>продать</b> квартиру<br>премиум-класса выгодно и безопасно?', 'Want <b>to sell</b> luxury apartment safely and with real profit?')|html}</div>
		<div class="descr">{$lang->get('Мы оперативно найдем Вам покупателей и возьмем на себя юридическое сопровождение сделки.', 'We’ll find buyers in the shortest possible time and provide legal support of transactions.')}</div>
		<a href="{$url_prefix}/owner/" class="btn m-light-magenta m-vw">{$lang->get('Продать квартиру с М16', 'Sell apartment with M16')}</a>
	</div>
	<div class="bow_tie animated"></div>
</div>
<div class="qr-block-wrap">
	<div class="qr-block">
		<div class="qr">
			<div class="qr-wrap">
				<img src="https://chart.googleapis.com/chart?cht=qr&chs=235x235&chld=L|2&chl=http://{$smarty.server.SERVER_NAME}/contacts/" alt="" />
			</div>
		</div>
		<div class="main">{$lang->get('Откройте страницу на смартфоне', 'Open this page on your smartphone')}</div>
		<div class="small-descr">{$lang->get('Нажмите, чтобы увеличить QR-код', 'Press it to enlarge QR code')}</div>
	</div>
</div>
<div class="request-wrap" id='form'>
	<div class="title"><span>{$lang->get('Напишите нам', 'Send a message')}</span></div>
	{?$checkString = time()}
	{?$checkStringSalt = $checkString . $hash_salt_string}
	<form action="/feedback/makeRequest/" class="request-form user-form a-justify" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<input type="hidden" name="check_string" value="" />
		<input type="hidden" name="hash_string" value="" />
		<input type="hidden" name="feedbackType" value="feedback">
		<div class="open-block">
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
						<span class="slash"></span>
					</div>
					<div class="f-input tel">
						<input type="tel" name="phone"/>
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
						<textarea type="text" name="message" placeholder="{$lang->get('Меня интересует...', 'I\'m interested in...')}">
						</textarea>
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
            <label class="field checkbox">
    					<div class="f-row">
    						<div class="f-input">
    							<input id="agree" type="checkbox" name="agree" checked="checked"/>
    						  <label for="agree"><div></div><span>{$lang->get('Принимаю', 'I agree to the ')}</span>
    								<a href="/privacy_policy/">
    									<span>{$lang->get('соглашение на обработку персональных данных', 'processing of personal data')}</span>
                                    </a>
    							</label>
                            </div>
    						<div class="f-errors a-hidden">
                                {$lang->get('Нужно согласиться', 'Need agree')}
    						</div>
    					</div>
    				</label>
			<div class="buttons">
				<button class="btn m-sand">{$lang->get('Отправить заявку', 'Send your request')}</button>
			</div>
		</div>
		<div class="sended-block">
			<div class="main">{$lang->get('Заявка отправлена', 'Message sent')}</div>
			<div class="small-descr">
				{$lang->get('Ваша заявка успешно отправлена<br>консультантам агентства<br>недвижимости М16. Спасибо за ваше<br>обращение!', 'Your request is successfully sent<br>to conultants of M16 real estate agency.<br>Thank you for contacting us!')|html}
			</div>
		</div>
	</form>
</div>
{*{if !empty($page_posts.main_post) && $page_posts.main_post.status == 'close'}
    <h4>{$page_posts.main_post.title}</h4>
    <p>{$page_posts.main_post.annotation}</p>
    <div>{$page_posts.main_post.text|html}</div>
{/if}*}