<div class="contacts-page content-block">
	<div class="page-title">
		{include file="components/breadcrumb.tpl" other_link=array(($ru)? 'Контактная информация' : 'Contact Information' => $url_prefix . '/main/contacts/' , ($ru)? 'Заявка в компанию' : 'Application to the company' => $url_prefix . '/main/contactsSend/')}
		<h1>{$lang->get('Онлайн заявка в компанию ', 'Online application to the company')|html}</h1>
	</div>
	<div class="justify aside-float-cont">
		<div class="aside-col object-aside-cont">
			{if !empty($objects)}				
				<div class="aside-menu{* floating*}">
					<div class="aside-top">
						{if $ru}
							Предмет вашей<br />заявки
						{else}
							The subject of your<br /> application 
						{/if}
					</div>
					{?$max_weight = 20}
					{?$page_weight = 0}
					<div class="carousel">
						<div class="car-wrap">
							<ul>
								<li class="objects-list">
									{foreach from=$objects item=item_data name=objects_list}
										{?$item = $item_data.item}
										{?$item_type = $item->getType()}
										{?$page_weight += 5}										
										<div class="object-item">
											{capture assign=object_header}
												<a href="{$item->getUrl()}" class="objects-title">
													{if $item_type.id=='63' || $item_type.id=='64'}
														{?$offer_type = ($ru) ? 'Офис' : 'Office'}
													{elseif $item_type.id=='65' || $item_type.id=='67'}
														{?$offer_type = ($ru) ? 'Индустриальная недвижимость' : 'Industrial'}
													{elseif $item_type.id=='59'}
														{?$offer_type = ($ru) ? 'Земельный участок' : 'Stead'}
													{elseif $item_type.id=='66' || $item_type.id=='68'}
														{?$offer_type = ($ru) ? 'Торговая недвижимость' : 'Retail property'}
													{elseif $item_type.id=='62'}
														{?$offer_type = ($ru) ? 'Жилая недвижимость' : 'Residental real estate'}
													{/if} 
													{$item.title}
												</a>												
												<div class="small-descr">
													{if !empty($item.country_residential)}{$item.country_residential}, {/if}
													{if !empty($item.city)}{$item.city}, 
													{elseif !empty($item.city_residential)}{$item.city_residential}, {/if}
													{if !empty($item.city)}{$item.district}, {/if}
													{if !empty($item.adres)}{$item.adres}{/if}
												</div>												
											{/capture}
											{$object_header|html}
											{foreach from=$item_data.offers item=offer name=object_offers}
												{?$page_weight += 3}
												<a href="{$offer->getUrl()}#offer-{$offer.id}" class="offer-title">{$offer.variant_title}</a>
												<div class="offer-info">
													{if $item_type.id=='63' || $item_type.id=='65' || $item_type.id=='66'}
														{?$sale_rent = ($ru)? 'Аренда' : 'Rent'}
													{else}
														{?$sale_rent = ($ru)? 'Продажа' : 'Sale'}
													{/if}
													<span class="main">{$sale_rent}</span> 
													{if !empty($offer.ploschad_ot_offer)}
														&nbsp;•&nbsp; <strong>{if $offer.ploschad_ot_offer != $offer.ploschad_do_offer}{$offer.ploschad_range}{else}{$offer.ploschad_ot_offer}{/if}</strong>
													{/if}
												</div>
												{if !empty($offer.price_variant)}
													<div class="offer-price"><i class="i-wallet"></i>{$offer.price_variant}</div>
												{/if}
												{if $page_weight >= $max_weight && !$smarty.foreach.object_offers.last}
														</div>
													</li>
													<li class="objects-list">
														<div class="object-item">
															{$object_header|html}
															{?$page_weight = 5}
												{/if}
											{/foreach}
										</div>
										{if $page_weight >= $max_weight && !$smarty.foreach.objects_list.last}
											</li>
											<li class="objects-list">
											{?$page_weight = 0}
										{/if}
									{/foreach}
								</li>
							</ul>
						</div>
						<div class="objects-paging car-arrows">
							<div class="car-prev a-link">{$lang->get('Назад', 'Back')|html}</div>
							<div class="car-next a-link">{$lang->get('Вперед', 'Next')|html}</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
		<div class="main-col">
			<div class="contacts-top">
				<div class="contacts-top-descr" ><i></i> — {$lang->get('обязательные для заполнения поля', 'required Fields')|html}</div>
			</div>
			<div class="contacts-block">
				<div class="justify">
					<div class="contacts-form">
						{?$checkString = time()}
						{?$checkStringSalt = $checkString . $hash_salt_string}
						<form method="post" action="/hr-feedback/makeRequest/" class="feedback-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
							<div class="sending-block"></div>
							<input type="hidden" name="check_string" value="" />
							<input type="hidden" name="hash_string" value="" />
							<input type="hidden" name="feedbackType" value="request" />
							<input type="hidden" name="variant_ids" />
							<div class="contacts-fields fields-block">
								<label class="field f-col">
									<div class="f-title">{$lang->get('Интересующая площадь помещений, м²', 'Interest premises area, m²')|html}</div>
									<div class="f-input"><input type="text" name="interest" /></div>
								</label>
								<label class="field f-col">
									<div class="f-title">{$lang->get('Организация', 'Company')|html}</div>
									<div class="f-input"><input type="text" name="organisation" /></div>
								</label>
								<label class="field f-col required">
									<div class="f-title">{$lang->get('Контактное лицо', 'Contact name')|html}</div>
									<div class="f-input"><input type="text" name="name" required="required" /></div>
									<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите имя контактного лица', 'Please provide the name of the contact person')|html}</div>
								</label>
								<label class="field f-name">
									<div class="f-title">{$lang->get('Должность', 'Position')|html}</div>
									<div class="f-input"><input type="text" name="position"/></div>
								</label>
								<div class="f-row justify">
									<label class="field f-col required">
										<div class="f-title">{$lang->get('Телефон/Факс', 'Phone / Fax')|html}</div>
										<div class="f-input"><input type="text" name="phone" required="required" /></div>
										<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите контактный телефон', 'Please enter the telephone number')|html}</div>
									</label>
									<label class="field f-col required">
										<div class="f-title">{$lang->get('Эл. почта', 'E-mail')|html}</div>
										<div class="f-input"><input type="text" name="email" required="required" /></div>
										<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите контактный e-mail', 'Please enter the contact e-mail')|html}</div>
										<div class="f-error e-incorrect_format a-hidden">{$lang->get('Неверный формат e-mail', 'Invalid e-mail format')|html}</div>
									</label>
								</div>
								<label class="field f-message">
									<div class="f-title">{$lang->get('Текст вашего обращения', 'Text')|html}</div>
									<div class="f-input"><textarea rows="11" name="addition"></textarea></div>
								</label>
								<ul class="f-error general-err a-hidden">
									<li class="e-check_sum a-hidden">{$lang->get('Ошибка при отправке формы. Перезагрузите страницу и попробуйте еще раз.', 'Error while sending the form. Please reload the page and try again.')|html}</li>
								</ul>
								<div class="buttons">
									<button class="a-btn-green">{$lang->get('Отправить сообщение', 'Send Message')|html}</button>
								</div>
							</div>
						</form>
					</div>
					<div class="contacts-info">
						<div class="title-top">{$lang->get('Другие способы связи', 'Other methods of communication')|html}</div>
						<div class="contacts-descr">
							<div class="text-title">{$lang->get('Адрес офиса', 'Office Address')|html}</div>
							<div class="adress">
								{?$postfix = ($request_segment.key=='ru')? '': '_' . $request_segment.key}
								<span>{$site_config['address' . $postfix]}</span><br>
								<span>{$site_config['office' . $postfix]}</span>
							</div>
							<div class="contacts-item">
								<div class="text-title">{$lang->get('Телефон', 'Phone')|html}</div>
								<span>{$site_config.phone}</span><br>
							</div>
							<div class="contacts-item">
								<div class="text-title">{$lang->get('Факс', 'Fax')|html}</div>
								<span>{$site_config.fax}</span><br>
							</div>
							<div class="contacts-item">
								<div class="text-title">{$lang->get('Электронная почта', 'E-mail')|html}</div>
								<strong><a href="mailto:maris@maris-spb.ru">{$site_config.email}</a></strong>
							</div>
						</div>
						<div class="aside-link">
							{$lang->get('См. также', '')|html}<br>
							<a href="{if $request_segment.id!=1}/{$request_segment.key}{/if}/main/contacts/">{$lang->get('Вся контактная информация', 'All contact information')|html}</a><br>
						</div>
						{if !empty($curator) && !empty($curator.parent_id)}
							{?$curator_cover = $curator['image']}
							<div class="contacts-agent">
								<div class="title-top">{$lang->get('Контактное лицо', 'Contact name')|html}</div>
								<div class="agent-cover">
									{if !empty($curator_cover)}
										<img src="{$curator_cover->getUrl(60, 60, true)}" alt="{if !empty($curator.name)}{$curator.name}{/if}"/>
									{else}
										<img src="/img/design/user_no_photo.jpg" class="nophoto" style="width: 60px; height: 60px;" alt="{if !empty($curator.name)}{$curator.name}{/if}"/>
									{/if}
									<span class="agent-name">{if !empty($curator.name)}{$curator.name} {/if}{if !empty($curator.surname)}{$curator.surname}{/if}<br />
										<span class="small-descr">{if !empty($curator.function)}{$curator.function}{/if}</span>
									</span>
								</div>
								<div class="agent-contacts">
									{if !empty($curator.phone)}
										<span><i class="i-contacts mobile" title="{$lang->get('Телефон', 'Phone')|html}"></i>{$curator.phone}</span>
									{/if}
									{if !empty($curator.email)}
										<span><i class="i-contacts email" title="{$lang->get('Электронная почта', 'E-mail')|html}"></i><a href="mailto:{$curator.email}">{$curator.email}</a></span>
									{/if}
								</div>
							</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="catalog-bottom">
	<div class="green-line"></div>
	{include file="components/benefits.tpl"}
	{include file="components/news-block.tpl"}
	{include file="components/cbre-belt.tpl"}
</div>