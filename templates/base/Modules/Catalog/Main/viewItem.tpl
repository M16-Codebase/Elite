{*include file="/components/monthAccordance.tpl"}
{include file="/components/orderPositions.tpl"*}
{capture assign=catalogItemPrice}{/capture}
{?$title_variant = empty($variant_id) ? $first_variant : (!empty($variants) ? $variants[$variant_id] : NULL)}
{?$pageTitle = $catalog_item.title . ' ' . $title_variant.variant_title . ' купить — ' . $current_type.title . ' — ТехноАльт'}
{?$pageDescription = $catalog_item.title . ' ' . $title_variant.variant_title . ' всего за ' . 0 . ' | ТехноАльт —  ' . $current_type.title . ' — интернет-каталог. | Доставка по всей России и самовывоз.'}
{?$admin_link = '/catalog-item/edit/?id=' . $catalog_item.id . (!empty($variant_id) ? '&tab=variants&v=' . $variant_id : '')}
<div class="view-item-page" data-id="{$catalog_item.id}">
	<div class="item-main-block-wrapper">
	<div class="item-main-block-sub-wrapper">
        {if !empty($catalog_item.foto)}
            <img src="{$catalog_item.foto->getUrl()}" />
        {/if}
		<section class="item-main-block justify container">
			<div class="item-col">
				{*include file="components/breadcrumb.tpl"*}
				<h2 class="page-aside-header grad-text">{$h1 = $catalog_item.title}</h2>
				{if !empty($variants)}
					{if count($variants) > 1}
						<div class="item-variants-select">
							<div class="variants-select-cloud">Выберите из {count($variants)|plural_form:'варианта':'вариантов':'вариантов'}</div>
							<div class="variants-select-block">
								<div class="variant-titles">
									{foreach from=$variants item=variant name=variants_list}
										<div class="title var-switch v{$variant.id} {if (empty($variant_id) && first) || ($variant_id == $variant.id)} m-current{else} a-hidden{/if}" data-id="{$variant.id}" data-itemId="{$catalog_item.id}" title="{$variant.variant_title}">
											{$variant.variant_title}
										</div>
									{/foreach}
								</div>
								<div class="arrows">
									<div class="arrow-prev"><i></i></div>
									<div class="arrow-next"><i></i></div>
								</div>
							</div>
						</div>
					{/if}
					<div class="item-code" data-item-id="{$catalog_item.id}">
						Код продукта — 
						{foreach from=$variants item=variant name=variants_list}
							<span class="variant-code var-switch 
								v{$variant.id}{if (empty($variant_id) && first) || ($variant_id == $variant.id)} m-current{else} a-hidden{/if}
								{if !empty($variant.properties.photo_merge.value)} photo-merge{/if}" 
								data-id="{$variant.id}">{$variant.id}</span>
						{/foreach}
					</div>
				{/if}

				<div class="item-bottom-links">
					<div class="bottom-link">
						<a href="#" class="icon-text btn-item-question"><i class="icon i-comment"></i><span>Задать вопрос по товару</span></a>
					</div>
				</div>
			</div>
			<div class="item-gallery-col">
				<ul class="variant-markers-list">
					<li class="variant-markers m-current">
						{if !empty($catalog_item.properties.sp_good.value)}
							<div class="marker m-recomend" title="Рекомендуем">
								<i></i>{if !empty($catalog_item.sp_good_text)}<div class="help tooltip" title="{$catalog_item.sp_good_text}">?</div>{/if}
							</div>
						{/if}
						{if !empty($catalog_item.properties.sp_new.value)}
							<div class="marker m-new" title="Новинка">new</div>
						{/if}
					</li>
				</ul>
				<div class="item-gallery">				
					<div class="arrows a-hidden">
						<div class="arrow-prev"></div>
						<div class="arrow-next"></div>
					</div>				
					<div class="item-images">
						{*{?$cover_id = null}*}
						{*{?$all_images = array()}*}
						{*{foreach from=$variants item=variant name=variants_list}*}
							{*{if (empty($variant_id) && first) || ($variant_id == $variant.id)}*}
								{*{?$var_cover = $variant.gallery->getCover()}*}
								{*{if !empty($var_cover)}*}
									{*{?$cover_id = $var_cover.id}*}
								{*{/if}*}
							{*{/if}*}
							{*{?$var_images = $variant.gallery->getImages()}*}
							{*{if !empty($var_images)}*}
								{*{foreach from=$var_images item=img}*}
									{*{?$all_images[$img.id] = array($img, $variant.id)}*}
								{*{/foreach}	*}
							{*{/if}*}
						{*{/foreach}					*}
						{*{if empty($cover_id)}*}
							{*{?$item_cover = $catalog_item.gallery->getCover()}*}
							{*{if !empty($item_cover)}*}
								{*{?$cover_id = $item_cover.id}*}
							{*{/if}*}
						{*{/if}*}
						{*{?$item_images = $catalog_item.gallery->getImages()}*}
						{*{if !empty($item_images)}*}
							{*{foreach from=$item_images item=img}*}
								{*{?$all_images[$img.id] = array($img, null)}*}
							{*{/foreach}*}
						{*{/if}*}
						{*{if !empty($all_images)}*}
							{*{foreach from=$all_images item=img_cont name=item_images}*}
								{*{?$img = $img_cont[0]}*}
								{*<a href="{$img->getUrl()}"*}
								   {*{if !empty($img_cont[1])} data-id="{$img_cont[1]}"{/if} *}
								   {*{if !empty($img.text)} title="{$img.text}"{/if} *}
								   {*class="img{if !empty($img_cont[1])} v{$img_cont[1]}{else} item{/if}{if $cover_id == $img.id} m-current{else} a-hidden{/if}" *}
								   {*rel="gal">*}
									{*<img src="{$img->getUrl(522, 522, true)}" alt="{$catalog_item.title}">*}
								{*</a>*}
							{*{/foreach}*}
						{*{/if}*}
						<div class="img empty-img{if !empty($cover_id)} a-hidden{else} m-current{/if}">
								<img src="/img/icons/cap-tool.png" alt="{$catalog_item.title}">
						</div>
					</div>
				</div>
			</div>
			<section class="item-col m-right-col">
				{if !empty($variants)}
					<ul class="variants-info-list">
						{foreach from=$variants item=variant name=variants_list}
							<li class="item-variant var-switch v{$variant.id}{if (empty($variant_id) && first) || ($variant_id == $variant.id)} m-current{else} a-hidden{/if}" data-id="{$variant.id}">
								<div class="variant-title">
									<h3 class="variant-title-btn icon-text m-right" data-id="{$variant.id}"><span>{$catalog_item.title} {$variant.variant_title}</span> <i class="icon i-variant"></i></h3>
								</div>
								{if !empty($variant.price_variant)}
									<div class="variant-price">
										<div class="price-btn m-big-btn">
											<img src="/img/icons/history-02.png" alt="">
											<div class="content">{$variant.price_variant|price_format} Р/шт.</div> 
										</div>
									</div>
								{/if}
								{if !empty($variant.bonus)}
									<div class="variant-bonus icon-text">
										<i class="icon i-bonus"></i><span>{$variant.bonus|plural_form:'бонусный балл','бонусных балла','бонусных баллов'}</span>
									</div>
								{/if}
								{if !empty($variant.count)}
								<div class="buy-cheaper">
									<div class="cheap-dialog-open icon-text a-bold m-blue">
										<span class="a-link">Как купить еще дешевле?</span>
										<i class="icon i-blue-arrow"></i>									
									</div>
									<div class="cheap-dialog a-hidden">
										<div class="cheap-header icon-text a-bold m-blue">
											<span class="cheap-dialog-close a-link">Как купить еще дешевле?</span>
											<i class="icon i-blue-arrow-top"></i>
										</div>		
										<ul class="cheap-list">
											<li class="price-noty-handler a-link" data-variant_id="{$variant.id}">Узнать, когда будет еще дешевле?</li>
											<li><a href="/howBuy/">Ознакомиться с условиями скидок</a></li>
										</ul>
									</div>
								</div>
								{/if}
								<div class="variant-delivery tabs-cont">
									<div class="gradient-tabs m-3-tabs tab-titles">
										<div class="gradient-tabs-inner">
											<div class="gradient-tab tab-title m-current" data-target=".tab-city"><i class="tab-icon i-courier"></i></div>
											<div class="gradient-tab tab-title" data-target=".tab-self"><i class="tab-icon i-self"></i></div>
											<div class="gradient-tab tab-title" data-target=".tab-company"><i class="tab-icon i-company"></i></div>
										</div>
									</div>
									<div class="tab-page tab-city">
										<div><strong>Доставка по Санкт-Петербургу</strong></div>
										<div>Бесплатно</div>
										<div class="descr">Мы доставляем заказы от 10 000 Р</div>
									</div>
									<div class="tab-page tab-self a-hidden">
										<div><strong>Самовывоз со склада</strong></div>
										<div>Санкт-Петербург, ул. Пр. Качалова, 3</div>
										<div class="descr">Пн—Пт 09:00—17:00</div>
									</div>
									<div class="tab-page tab-company a-hidden">
										<div><strong>Доставка транспортной компанией</strong></div>
                                        {?$tk_line = ''}
										<div class="descr">{if !empty($tk_line)}{$tk_line}...{else}Деловые линии, КИТ, Тэкрос...{/if}</div>
									</div>
								</div>
								<div class="variant-available">
									{if !empty($variant.count)}<strong>В наличии на складе</strong> {$variant.count} шт.{else}<strong>Нет в наличии</strong>{/if}
									{if !empty($variant.count_expects)}
									<div class="month-order-amount">
									Под заказ 
										{?$month=$variant.available_date|substr:3, 2}
										{if !empty($month)}{$monthArray[$month]}{/if}									
										{if !empty($variant.count_expects)}{$variant.count_expects}&nbsp;шт.{/if}										
									</div>
									{/if}	
									{if !empty($variant.count)}<div class="a-bold m-blue more-goods-handler more-goods-handler" data-variant_id="{$variant.id}"><span class="a-link">Нужно больше?</span></div>{else}<div class="a-bold m-blue income-info" data-variant_id="{$variant.id}">Узнать о поступлении</div>{/if}
								</div>
								{if !empty($variant.count)}								
										<div class="{if !empty($ordersPositions[$variant.id])}a-hidden {/if}variant-count multi-count v{$variant.id}">
											<div class="change-count-arrows change-count">
												<i class="count-arrow arrow-down"></i>
													<span class="count-text a-link">
														<span class="num">1</span> <span class="unit">шт.</span>
													</span>
												<i class="count-arrow arrow-up"></i>
											</div>
											<div class="change-count-inputs a-hidden">
												<div class="count-selector tabs-cont">
													{if !empty($variant.pack_count)}
														<div class="count-units-cloud">
															<div class="tab-title title-one a-link m-current" data-target=".tab-one">Шт.</div>
															<div class="tab-title title-pack a-link" data-target=".tab-pack">Упаковки по {$variant.pack_count} шт.</div>
														</div>
													{/if}
													<div class="tab-page tab-one">
														<input type="text" data-mask="?99999999">
														<span class="unit">шт.</span>
													</div>
													{if !empty($variant.pack_count)}
														<div class="tab-page tab-pack a-hidden">
															<input type="text" data-pack="{$variant.pack_count}" data-mask="?99999999">
															<span class="unit">уп.</span>
														</div>
													{/if}
												</div>
											</div>
										</div>		
									<div class="variant-buy">
										<a href="/order/" class="{if !empty($ordersPositions[$variant.id])}a-hidden {/if}add-to-cart btn btn-blue-big m-icon pull-to-basket m-fullwidth v{$variant.id}" data-id="{$variant.id}"><i class="btn-icon i-cart"><i></i></i> В корзину</a>
										<a href="/order/" href="/order/" class="{if empty($ordersPositions[$variant.id])}a-hidden {/if}btn btn-blue-big m-fullwidth m-in-basket v{$variant.id}" data-id="{$variant.id}">
											<i class="icon i-basket-icon"></i>
											<span>В корзине</span>
										</a>
									</div>
								{else}
									<div class="variant-buy">
										<a href="#" class="variant-request btn btn-blue-big m-icon m-fullwidth" data-id="{$variant.id}"><i class="btn-icon"><i></i></i> Запросить</a>
									</div>
								{/if}
							</li>
						{/foreach}
					</ul>
				{/if}
			</section>
		</section>
	</div>
	</div>		
	<div class="site-body container clearfix">
		<div class="endless-line m-site-body-var-1"></div>
		<aside class="page-aside a-left m-on-product-page">
			{if !empty($item_articles)}
				<section class="aside-block">
					<h3>Полезная информация</h3>
					<ul class="dashed-list">
						{foreach from=$item_articles item=article}
							<li><a href="{$article->getUrl()}">{$article.title}</a></li>
						{/foreach}
					</ul>
				</section>
			{/if}
			{if !empty($item_files)}
				<section class="aside-block">
					<h3>Скачать</h3>
					<ul class="files-list">
						{foreach from=$item_files item=item_file}
							<li>
								<a href="{$item_file.link}">
									<i class="file-icon"><span>{$item_file.ext}</span></i>
									<span class="file-info">
										<span class="title">{if !empty($item_file.title)}{$item_file.title}{else}{$item_file.name}{/if}</span>
										<span class="size">{$item_file.full_size}</span>
									</span>
								</a>
							</li>
						{/foreach}
					</ul>
				</section>
			{/if}
		</aside>
		<div class="page-content a-left">
			<div class="item-specs-block">
				{if !empty($type_properties)}
					<section class="item-block">
						<h2>Характеристики</h2>
						{if !empty($type_properties)}
							<div class="variant-specs-table">
								{foreach from=$variants item=variant name=variants_list}
									<table class="specs-table var-switch v{$variant.id}{if (empty($variant_id) && first) || ($variant_id == $variant.id)} m-current{else} a-hidden{/if}" data-id="{$variant.id}">
										{foreach from=$type_properties item=prop}
											{if $prop.multiple}
												{?$current_entity = $variant['properties']}
											{else}
												{?$current_entity = $catalog_item['properties']}
											{/if}
											{if !empty($current_entity[$prop.key]['complete_value'])}
												<tr data-prop="{$prop.key}">
													<td>{$prop.title}</td>
													<td class="small">											
														{if !empty($prop.public_description)}<i class="icon i-tip has-tip" title="{$prop.public_description}"></i>{/if}
													</td>
													<td>
														{if is_array($current_entity[$prop.key]['complete_value'])}
															{implode(', ', $current_entity[$prop.key]['complete_value'])}
														{else}
															{$current_entity[$prop.key]['complete_value']}
														{/if}
													</td>
												</tr>
											{/if}
										{/foreach}
									</table>
								{/foreach}
							</div>
						{/if}
					</section>
				{/if}
				{capture assign="usingType"}
				{if !empty($catalog_item.using_types)}
					<section class="item-block">
						<h2>Применение</h2>
						<div class="usage-info m-big">
							{foreach from=$catalog_item.using_types item=using_type}
								<span class="usage-mark m-big tooltip"
									{if !empty($catalog_item.properties[$using_type.key]['value'])}
										{?$counter=true}
										style="border-color: {$using_type.color}; color: {$using_type.color};"
									{else}
										style="border-color: #ddd; color: #ddd;"
									{/if} 
									title="{$using_type.title}">{$using_type.short_name}</span> 
							{/foreach}
						</div>
					</section>
				{/if}
				{/capture}
				{if !empty($counter)}
					{$usingType|html}
				{/if}	
				{*{if !empty($catalog_item.post.text)}*}
					{*<section class="item-block item-post">*}
						{*<h2>{$catalog_item.title}</h2>*}
						{*<p class="annotation">{$catalog_item.post.annotation}</p>*}
						{*<div class="article-page">*}
							{*{$catalog_item.post.text|html}*}
						{*</div>*}
					{*</section>*}
				{*{/if}*}
			</div>
		</div>
	</div>
	
	{if !empty($assoc_items) || !empty($concurrent_items)}
		<div class="item-analogs tabs-cont new-and-best-goods container">
			<ul class="goods-tabs">
				{if !empty($concurrent_items)}
					<li class="tab-title m-current" data-target=".best-goods"><a href="#analog-goods" class="best-goods-header">Аналоги</a></li>
				{/if}
				{if !empty($assoc_items)}
					<li class="tab-title{if empty($concurrent_items)} m-current{/if}" data-target=".new-goods"><a href="#assoc-goods" class="new-goods-header">Сопутствующие товары</a></li>
				{/if}
			</ul>
			<div class="goods-area clearfix">
				{if !empty($concurrent_items)}
					<div class="best-goods tab-page" id="analog-goods">
						<div class="goods-carousel-wrapper">
							<div class="goods-carousel">
								<div class="goods-list">
									<div class="goods-portion">
										{foreach from=$concurrent_items item=item name=concurrent_items}
											{if (iteration+1)%4 == 0 && !last}
												</div>
												<div class="goods-portion">
											{/if}
											{include file="components/catalog-item.tpl" catalog_item=$item}
										{/foreach}
									</div>
								</div>
							</div>
							{if count($concurrent_items) > 3}	
								<div class="nav-elems-parent">
									<a class="nav-elems m-prev" href="#"></a>
									<a class="nav-elems m-next" href="#"></a>
								</div>
							{/if}
						</div>
					</div>
				{/if}
				{if !empty($assoc_items)}
					<div class="new-goods tab-page a-hidden" id="assoc-goods">
						<div class="goods-carousel-wrapper">
							<div class="goods-carousel">
								<div class="goods-list">
									<div class="goods-portion">
										{foreach from=$assoc_items item=item name=assoc_items}
											{if (iteration+1)%4 == 0 && !last}
												</div>
												<div class="goods-portion">
											{/if}
											{include file="components/catalog-item.tpl" catalog_item=$item}
										{/foreach}
									</div>
								</div>
							</div>
							{if count($assoc_items) > 4}	
								<div class="nav-elems-parent">
									<a class="nav-elems m-prev" href="#"></a>
									<a class="nav-elems m-next" href="#"></a>
								</div>
							{/if}
						</div>
					</div>
				{/if}
			</div>
		</div>
	{/if}
</div>


<div class="ring-order popup-window popup-item-question">
	<h2 class="ring-order-header">Вопрос по товару</h2>
	<div class="ring-order-body">
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		{?$user = $account->getUser()}
		 <form action="/feedback/makeRequest/" class="item-question-form popup-form" data-cont=".user-data" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
			<div class="sended-form a-hidden">
				Спасибо за обращение в нашу компанию. Мы свяжемся с Вами в ближайшее время.
			</div>
			<div class="normal-form">
				<input type="hidden" name="check_string" value="">
				<input type="hidden" name="hash_string" value="">
				<input type="hidden" name="feedbackType" value="item_question">
				<input type="hidden" name="item_id" value="{$catalog_item.id}">				
				<label for="fio-field" class="name-label">ФИО</label>
				<input id="fio-field" class="name-field default-data-field" type="text" name="fio" value="" data-default-data="{if !empty($user)}{if !empty($user.surname)}{$user.surname} {/if}{if empty($user.name)}{$user.name}{/if}{/if}" />
				<label for="email-field" class="email-label">E-mail</label>
                <input id="email-field" class="email-field default-data-field" type="text" name="email" value="" {if !empty($user)}data-default-data="{$user.email}"{/if}/>
				<label for="phone-field" class="phone-label">Телефон</label>
				<input id="phone-field" class="phone-field default-data-field" type="text" name="phone" value=""  {if !empty($user)}data-default-data="{$user.phone}"{/if} />
				<label for="speak-about" class="speak-about-label">Вопрос</label>
				<textarea id="speak-about" class="speak-about-area" name="message"></textarea>
				<button type="submit" class="send-ring btn btn-blue-big">Отправить</button>
			</div>
		</form>
	</div>
</div>

<div data-width="349" class="ring-order popup-window popup-variant-request">
	<h2 class="ring-order-header">Запрос товара</h2>
	<div class="ring-order-body">
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		{?$user = $account->getUser()}
		<form action="/feedback/makeRequest/" class="variant-request-form popup-form" data-cont=".user-data" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
			<div class="sended-form a-hidden">
				Спасибо за обращение в нашу компанию. Мы свяжемся с Вами в ближайшее время.
			</div>
			<div class="normal-form">
				<div class="descr-form">Вы можете оставить заявку на отсутствующий в наличии товар и мы известим вас о сроках поставки.</div>
				<input type="hidden" name="check_string" value="">
				<input type="hidden" name="hash_string" value="">
				<input type="hidden" name="feedbackType" value="variant_request">
				<input type="hidden" name="variant_id" value="">		
				<label for="fio-field" class="name-label">ФИО</label>
				<input id="fio-field" class="name-field default-data-field" type="text" name="fio" value="" data-default-data="{if !empty($user)}{if !empty($user.surname)}{$user.surname} {/if}{if empty($user.name)}{$user.name}{/if}{/if}" />
				<label for="email-field" class="email-label">E-mail</label>
                <input id="email-field" class="email-field default-data-field" type="text" name="email" value="" {if !empty($user)}data-default-data="{$user.email}"{/if}/>
				<label for="phone-field" class="phone-label">Телефон</label>
				<input id="phone-field" class="phone-field default-data-field" type="text" name="phone" value=""  {if !empty($user)}data-default-data="{$user.phone}"{/if} />
				<label for="count-field" class="count-label">Требуемое количество</label>
				<input id="count-field" class="count-field" type="text" name="count" />
				<label for="speak-about" class="speak-about-label">Комментарий</label>
				<textarea id="speak-about" class="speak-about-area" name="message"></textarea>
				<button type="submit" class="send-ring btn btn-blue-big">Отправить</button>
			</div>
		</form>
	</div>
</div>
				
{*<div class="ring-order popup-window popup-price-changing">
	<h2 class="ring-order-header">Уведомление об изменении цены</h2>
	<div class="ring-order-body">
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		{?$user = $account->getUser()}
		<form action="/catalog/subscribePrice/" class="variant-request-form popup-form" data-cont=".user-data" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
                    <input type="hidden" name="check_string" value="">
                    <input type="hidden" name="hash_string" value="">
                    <input class="variant-id-fld" type="hidden" name="variant_id" value="">
                    {?$price_group = 'price_variant'}
                    <input type="hidden" name="price_group" value="{$price_group}">
			<div class="sended-form a-hidden">
				Спасибо за обращение в нашу компанию. Мы свяжемся с Вами в ближайшее время.
			</div>
			<div class="normal-form">
				<div class="descr-form">Когда цена на данный товар будет снижена&nbsp;—&nbsp;мы пришлем вам письмо</div>
				<label for="email" class="mail-label">Электронная почта</label>
				<input type="text" name="email" class="mail-field default-data-field" {if !empty($user)}data-default-data="{$user.email}"{/if}>
				<button type="submit" class="send-ring btn btn-blue-big">Отправить</button>
			</div>
		</form>
	</div>
</div>
			
<div class="ring-order popup-window popup-more-goods">
	<h2 class="ring-order-header">Уведомление о поступлении товара</h2>
	<div class="ring-order-body">
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		{?$user = $account->getUser()}
		<form action="/catalog/subscribeCount/" class="variant-request-form popup-form" data-cont=".user-data" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
                    <input type="hidden" name="check_string" value="">
                    <input type="hidden" name="hash_string" value="">
                    <input class="variant-id-fld" type="hidden" name="variant_id" value="">
			<div class="sended-form a-hidden">
				Спасибо за обращение в нашу компанию. Мы свяжемся с Вами в ближайшее время.
			</div>
			<div class="normal-form">
				<div class="descr-form">После поступления на склад необходимого вам количества товаров&nbsp;—&nbsp;мы пришлем вам письмо</div>
				<label for="email" class="mail-label">Электронная почта</label>
				<input type="text" name="email" class="mail-field default-data-field" {if !empty($user)}data-default-data="{$user.email}"{/if}>
				<label for="count" class="goods-label">Необходимое количество</label>
				<input type="text" id="goods-amount-field" name="count" class="goods-amount">				
				<span class="unit">шт.</span>
				<button type="submit" class="send-ring btn btn-blue-big">Отправить</button>
			</div>
		</form>
	</div>
</div>
				
<div class="ring-order popup-window popup-income-info">
	<h2 class="ring-order-header">Уведомление о поступлении товара</h2>
	<div class="ring-order-body">
		{?$checkString = time()}
		{?$checkStringSalt = $checkString . $hash_salt_string}
		{?$user = $account->getUser()}
		<form action="/catalog/subscribeAvailable/" class="variant-request-form popup-form" data-cont=".user-data" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
                    <input type="hidden" name="check_string" value="">
                    <input type="hidden" name="hash_string" value="">
                    <input class="variant-id-fld" type="hidden" name="variant_id" value="">
			<div class="sended-form a-hidden">
				Спасибо за обращение в нашу компанию. Мы свяжемся с Вами в ближайшее время.
			</div>
			<div class="normal-form">
				<div class="descr-form">Когда данный товар поступит в продажу&nbsp;—&nbsp;мы пришлем вам письмо</div>
				<label for="email" class="mail-label">Электронная почта</label>
				<input type="text" name="email" class="mail-field default-data-field" {if !empty($user)}data-default-data="{$user.email}"{/if}>
				<button type="submit" class="send-ring btn btn-blue-big">Отправить</button>
			</div>
		</form>
	</div>
</div>*}
