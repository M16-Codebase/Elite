{?$delim = ldelim . "!" . rdelim}
{if !empty($mode)}
    {if $mode == 'complex'}
        {if !empty($complex)}
            {if $request_segment.key == 'ru'}
                {?$pageTitle = 'Оставить заявку на недвижимость. ' . $complex.title . ' — может стать Вашим новым адресом | М16-Недвижимость'}
                {?$pageDescription = 'Хотите приобрести недвижимость в жилом комплексе ' . $complex.title . '? Заполните форму заявки, наши специалисты свяжутся с вами и расскажут о вариантах квартир и условиях приобретения'}
            {else}
                {?$pageTitle = 'Leave a request for apartment in ' . $complex.title . ' | M16 Real Estate Agency'}
                {?$pageDescription = 'If you want to purchase property in ' . $complex.title . ', fill out the application form. Our experts will contact you and consult on available apartment options and conditions of housing acquisition'}
            {/if}
            {if !empty($complex.title)}{?$title = $delim|explode:$complex.title}{else}{$title = ''}{/if}
            {?!empty($complex.gallery) ? $cover = $complex.gallery->getCover() : $cover = ''}
            {?$main = $lang->get('Жилой комплекс', 'Residential Complex')}
            {?!empty($complex.consultant) ? $consultants = $complex.consultant : $consultants = $site_config.real_estate_consultant}
            {?!empty($complex->getUrl()) ? $url = $complex->getUrl() : $url = $url_prefix.'/'}
        {/if}
    {elseif $mode == 'single_apartment'}
        {if !empty($apartment)}
            {?$floor = $apartment->getParent()}
            {?$corpus = $floor->getParent()}
            {?$complex = $corpus->getParent()}
            {if $request_segment.key == 'ru'}
                {?$pageTitle = 'Оставить заявку на недвижимость. ' . $complex.title . ' — может стать Вашим новым адресом | М16-Недвижимость'}
                {?$pageDescription = 'Хотите приобрести недвижимость в жилом комплексе ' . $complex.title . '? Заполните форму заявки, наши специалисты свяжутся с вами и расскажут о вариантах квартир и условиях приобретения'}
            {else}
                {?$pageTitle = 'Leave a request for apartment in ' . $complex.title . ' | M16 Real Estate Agency'}
                {?$pageDescription = 'If you want to purchase property in ' . $complex.title . ', fill out the application form. Our experts will contact you and consult on available apartment options and conditions of housing acquisition'}
            {/if}
            {if !empty($complex.title)}{?$title = $complex.title|replace:$delim:' '}{else}{$title = ''}{/if}
            {?!empty($complex.gallery) ? $cover = $complex.gallery->getCover() : $cover = ''}
            {?$main = $lang->get('Квартира в жилом комплексе', 'Apartment in residential complex')}
            {if !empty($apartment.bed_number)}{?$bed_number = $apartment.bed_number|plural_form:'спальня':'спальни':'спален'}{?$bed_number = (' '|explode:$bed_number)}{/if}
            {?!empty($complex.consultant) ? $consultants = $complex.consultant : $consultants = $site_config.real_estate_consultant}
            {?!empty($apartment->getUrl()) ? $url = $apartment->getUrl() : $url = $url_prefix.'/'}
        {/if}
    {elseif $mode == 'appartments_list'}
        {if !empty($apartments)}
            {foreach from=$apartments item=apartment}
                {?$floor = $apartment->getParent()}
                {?$corpus = $floor->getParent()}
                {?$complex = $corpus->getParent()}
                {?!empty($complex.consultant) ? $consultants = $complex.consultant : $consultants = $site_config.real_estate_consultant}
                {break}
            {/foreach}
        {/if}
    {elseif $mode == 'resale_single'}
        {if !empty($apartment)}
            {if $request_segment.key == 'ru'}
                {?$pageTitle = 'Оставить заявку на недвижимость. ' . $apartment.address . ' — может стать Вашим новым адресом | М16-Недвижимость'}
                {?$pageDescription = 'Интересует недвижимость по адресу ' . $apartment.address . '? Наши специалисты ответят на все интересующие вас вопросы и помогут сделать выбор в пользу лучшего жилья'}
            {else}
                {?$pageTitle = 'Leave a request for apartments. ' . $apartment.address . ' — может стать Вашим новым адресом | M16 Real Estate Agency'}
                {?$pageDescription = 'Interested in property ' . $apartment.address . '? Our experts will answer all your questions and assist you in selecting of the best offer'}
            {/if}
            {if !empty($apartment.title)}{?$title = $apartment.title}{else}{$title = ''}{/if}
            {?!empty($apartment.gallery) ? $cover = $apartment.gallery->getCover() : $cover = ''}
            {?!empty($apartment.consultant) ? $consultants = $apartment.consultant : $consultants = $site_config.real_estate_consultant}
            {?!empty($apartment->getUrl()) ? $url = $apartment->getUrl() : $url = $url_prefix.'/'}
        {/if}
    {/if}
{/if}

{if !empty($mode) && $mode == 'complex'}{include file='/components/main_menu.tpl' item=$complex title=(is_array($title) ? implode(' ', $title) : $title)}{/if}
<div class="top-bg{if !empty($mode)} m-{$mode}{/if}" id="site-top">
    <div class='bg-img' style='background: url(/img/veil.png), url({!empty($cover) ? $cover->getUrl() : ''});background-size:cover;'></div>
    {if !empty($mode) && ($mode != 'complex' && $mode != 'resale_single') && !empty($main)}<div class="main">{$main}</div>{/if}
    <div class="site-top">
        {if !empty($mode) && ($mode == 'complex' || $mode == 'resale_single') && !empty($main)}<div class="main">{$main}</div>{/if}
        {if !empty($title)}
            <h1 class="title" title="{if !empty($title[0])}{$title[0]}{else}{$title}{/if} {if !empty($title[1])} {$title[1]}{/if}{!empty($corpus.title) ? $corpus.title : ''}{if !empty($bed_number[0]) && !empty($bed_number[1])} {$bed_number[0]} {$bed_number[1]}{/if}{if !empty($apartment.area_all)} {$apartment.area_all}{/if}">
				<span>
					{if !empty($mode) && $mode == 'complex' && !empty($title[0])}{$title[0]}{elseif !empty($title)}{$title}{/if}
				</span><br>{if !empty($title[1])}{$title[1]}{/if}
                {!empty($corpus.title) ? $corpus.title : ''}
                {if !empty($bed_number[0]) && !empty($bed_number[1]) && !empty($corpus.title)} <i>•</i>{/if}{if !empty($bed_number[0]) && !empty($bed_number[1])} {$bed_number[0]} {$bed_number[1]}{/if}
                {if (!empty($bed_number[0]) && !empty($bed_number[1])) && (!empty($apartment.area_all) && !empty($mode) && $mode != 'resale_single')} <i>•</i>{/if}{if !empty($apartment.area_all) && !empty($mode) && $mode != 'resale_single'} {$apartment.area_all}{/if}
            </h1>
        {/if}
    </div>
</div>

<div class="request-wrap row">
    <h2 class="title"><span>{$lang->get('Заявка', 'Request')}</span></h2>
    <div class="w2">
        <div class="small-descr">
            {$lang->get('Наши специалисты свяжуться с вами, ответят на ваши вопросы, организуют просмотр квартиры и проведут сделку на высшем уровне', 'Our specialists will contact you, answer your questions, organize vizit to apartments and carry out the transaction at the highest level.')}
        </div>
        {if !empty($consultants)}
            <div class="swiper-container consultant">
                <div class="swiper-wrapper">
                    {foreach from=$consultants item=consultant name=cons}
                        <div class="swiper-slide person">
                            {if !empty($consultant.photo) && !empty($consultant.photo->getUrl())}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
                            {if !empty($consultant.title)}<div class="name">{$consultant.title}</div>{/if}
                            {if !empty($consultant.email) || !empty($consultant.phone)}<div class="email"><span class="a-nowrap">{!empty($consultant.phone) ? $consultant.phone . '<span>•</span>'|html : ''}</span>{if !empty($consultant.email)}<a href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}</div>{/if}
                            {if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
                        </div>
                    {/foreach}
                </div>
                <div class="swiper-button-prev{if !empty($smarty.foreach.cons.total) && $smarty.foreach.cons.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
                <div class="swiper-button-next{if !empty($smarty.foreach.cons.total) && $smarty.foreach.cons.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
            </div>
        {/if}
    </div>
    <div class="w2">
        {?$checkString = time()}
        {?$checkStringSalt = $checkString . $hash_salt_string}
        <form action="/feedback/makeRequest/" class="request-form user-form a-justify js-request-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
            <input type="hidden" name="check_string" value="" />
            <input type="hidden" name="hash_string" value="" />
            <input type="hidden" name="feedbackType" value="{$form_type}">
            {if !empty($mode)}
                {if $mode == 'complex'}
                    <input type="hidden" name="complex" value="{$complex.id}" />
                {elseif $mode == 'single_apartment'}
                    <input type="hidden" name="apartments" value="{$apartment.id}" />
                {elseif $mode == 'appartments_list'}
                    {foreach from=$apartments item=apartment}
                        <input type="hidden" name="apartments[]" value="{$apartment.id}" />
                    {/foreach}
                {elseif $mode == 'resale_list'}
                    {foreach from=$apartments item=apartment}
                        <input type="hidden" name="apartments_resale[]" value="{$apartment.id}" />
                    {/foreach}
                {elseif $mode == 'resale_single'}
                    <input type="hidden" name="apartments_resale" value="{$apartment.id}" />
                {/if}
            {/if}
            <div class="open-block a-justify">
                <label class="field">
                    <div class="f-row">
                        <div class="f-title">
                            <span>{$lang->get('Имя', 'Name')}</span>
                            <span class="">*</span>
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
                            <span class="">*</span>
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
                            <span class="">*</span>
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
								<textarea type="text" name="message">
									{if !empty($mode)}
                                        {if $mode == 'complex'}
                                            {$lang->get('Интересует жилой комплекс', 'Interested in residential complex')} {if !empty($complex.title)}«{$complex.title|replace:$delim:' '}»{/if}
                                        {elseif $mode == 'single_apartment'}
                                            {$lang->get('Интересует квартира', 'Interested in apartment')} «{if !empty($corpus.title)}{$corpus.title} — {/if}{if !empty($apartment.bed_number)}{$lang->get($apartment.bed_number|plural_form:'спальня':'спальни':'спален', $apartment.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')} — {/if}{if !empty($apartment.area_all)}{$apartment.area_all}{/if}» {$lang->get('в жилом комплексе', 'in residential complex')} {if !empty($complex.title)}«{$complex.title|replace:$delim:' '}»{/if}
                                        {elseif $mode == 'appartments_list'}
                                            {$lang->get('Интересует квартиры', 'Interested in apartments')}:
                                            {foreach from=$apartments item=apartment}
                                                - «{if !empty($corpus.title)}{$corpus.title} — {/if}{if !empty($apartment.bed_number)}{$lang->get($apartment.bed_number|plural_form:'спальня':'спальни':'спален', $apartment.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')} — {/if}{if !empty($apartment.area_all)}{$apartment.area_all}{/if}» {$lang->get('в жилом комплексе', 'in residential complex')} {if !empty($complex.title)}«{$complex.title|replace:$delim:' '}»{/if}
                                            {/foreach}
                                        {elseif $mode == 'resale_single'}
                                            {$lang->get('Интересует коттедж', 'Interested in cottage')} «{if !empty($apartment.area_all)}{$apartment.area_all}{/if}» {$lang->get('по адресу', 'by address')} {if !empty($apartment.address)}{$apartment.address}{/if}
                                        {/if}
                                    {/if}
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
                {if empty($url) || $url == $url_prefix . '/'}
                    <a href="{$url_prefix}/" class="btn m-white">{$lang->get('На главную', 'Main page')}</a>
                {else}
                    <a href="{$url}" class="btn m-white">{$lang->get('На страницу объекта', 'To the complex page')}</a>
                {/if}
            </div>
        </form>
    </div>
</div>