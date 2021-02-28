{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Оставить заявку на несколько объектов | М16-Недвижимость'}
	{?$pageDescription = 'Понравилось несколько вариантов? Наши специалисты  проконсультируют Вас по всем объектам и помогут выбрать лучшее'}
{else}
	{?$pageTitle = 'Leave a request for several apartments'}
	{?$pageDescription = 'If you chose several apartments our experts would consult you on all options and assist you in selecting of the best offer'}
{/if}
{?$delim = ldelim . "!" . rdelim}
<div class="top-bg" id="site-top">
	<div class='bg-img'></div>
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Избранные предложения', 'Favorite offers collection')}">
			{$lang->get('<span>Избранные</span><br>предложения', '<span>Favorite</span><br>offers collection')|html}
		</h1>
	</div>
</div> 
	
<div class="sand-wrap">
	<div class="request-wrap row">
		<h2 class="title"><span>{$lang->get('Заявка', 'Request')}</span></h2>
		<div class="small-descr">
			{$lang->get('Наши специалисты свяжуться с вами, ответят на ваши вопросы, организуют просмотр квартир и проведут сделку на высшем уровне.', 'Our specialists will contact you, answer your questions, organize vizit to apartments and carry out the transaction at the highest level.')}
		</div>
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		<form action="/feedback/makeRequest/" class="user-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<div class="w2 open-block">
			<div class="request-form a-justify m-swiper">
				<input type="hidden" name="check_string" value="" />
				<input type="hidden" name="hash_string" value="" />
				<input type="hidden" name="feedbackType" value="{$form_type}">
				{if !empty($real_estate) || !empty($resale)}
					<div class="swiper-container">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
										<ul>
											{?$iteration=0}
								{foreach from=$real_estate item=item name=item_n}
									{if !empty($item)}{?$floor = $item->getParent()}{/if}
									{if !empty($floor)}{?$corpus = $floor->getParent()}{/if}
									{if !empty($item.complex_title)}{?$title = $item.complex_title|replace:$delim:' '}{/if}
											<li>
												<a href="{$item->getUrl()}">
												<span class="iteration">{iteration}.</span>
												<div class="title">
													<span>{if !empty($title)}{$title}{else}&nbsp;{/if}</span>
												</div>
												<div class="descr">
													{if !empty($corpus.title)}{$corpus.title}{/if}{if !empty($item.bed_number)} {$item.bed_number|plural_form:"спальня":"спальни":"спален"}{/if}{if !empty($item.area_all)} {$item.area_all}{/if}
												</div>
												{if !empty($item.id)}<input type="hidden" name="apartments[]" value="{$item.id}" />{/if}
												</a>
											</li>
									{?$iteration++}
									{if $iteration % 5 == 0}
										</ul>
									</div>
									<div class="swiper-slide">
										<ul>
									{/if}
									
								{/foreach}
								{foreach from=$resale item=item name=itemr_n}
									{?$title = $item.title|replace:$delim:' '}
											<li>
												<a href="{$item->getUrl()}">
												<span class="iteration">{iteration + $smarty.foreach.item_n.iteration}.</span>
												<div class="title">
													<span>{if !empty($title)}{$title}{else}&nbsp;{/if}</span>
												</div>
												<div class="descr">
													{if !empty($item.bed_number)}{$lang->get($item.bed_number|plural_form:"спальня":"спальни":"спален", $item.bed_number|plural_form:"bedroom":"bedrooms":"bedrooms")}{/if}{if !empty($item.area_all)} {$item.area_all}{/if}
												</div>
												{if !empty($item.id)}<input type="hidden" name="apartments_resale[]" value="{$item.id}" />{/if}
												</a>
											</li>
									{?$iteration++}
									{if $iteration % 5 == 0}
										</ul>
									</div>
									<div class="swiper-slide">
										<ul>
									{/if}
								{/foreach}
									</ul>
								</div>
						</div>
						{if !empty($smarty.foreach.item_n.total) || !empty($smarty.foreach.itemr_n.total)  && ($smarty.foreach.item_n.total + $smarty.foreach.itemr_n.total) > 5}
						<div class="nav">
							<div class="pagin"><span>1 — 5</span> <i>/</i> {if !empty($smarty.foreach.item_n.total) || !empty($smarty.foreach.itemr_n.total)}{(($smarty.foreach.item_n.total + $smarty.foreach.itemr_n.total)/5)|ceil}{else}1{/if}</div>
							<div class="swiper-pagination"></div>
							<div class="swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
							<div class="swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
						</div>
						{/if}
					</div>
				{/if}
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
				{$lang->get('Ваша заявка успешно отправлена<br>консультантам агентства<br>недвижимости М16. Спасибо за ваше<br>обращение!', 'Your request is successfully sent<br>to conultants of M16 real estate agency.<br>Thank you for contacting us!')|html}
			</div>
			<a href="{$url_prefix}/" class="btn m-white">{$lang->get('На главную', 'Main page')}</a>
		</div>
		</form>
	</div>
</div>