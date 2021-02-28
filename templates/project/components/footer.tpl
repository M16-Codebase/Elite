{if empty($no_footer)}
<footer class="page-footer{if !empty($black_footer)} m-black{/if}" itemscope itemtype="http://schema.org/Organization">
	<meta itemprop="name" content="{$lang->get('Компания Вячеслава Малафеева M16', 'Vyacheslav Malafeyev M16 Real Estate Agency')}">
	{if empty($black_footer)}<a href="{$url_prefix}/" class="footer-logo">{fetch file=$path . "logo_m16.svg"}</a>{/if}
	<div class="footer-main">
		{$lang->get('Компания Вячеслава Малафеева M16', 'Vyacheslav Malafeyev M16 Real Estate Agency')}
	</div>
	<div class="footer-contacts footer-row">
		{if !empty($contacts.display_phone)}<{if $device_type == 'phone'}a href="tel:{$contacts.display_phone}"{else}span{/if} class="footer-col roistat_phone" itemprop="telephone">{$contacts.display_phone}</{if $device_type == 'phone'}a{else}span{/if}>{/if}
		<span class="footer-col-border"><span class="slash"></span></span>
		{if !empty($contacts.office_address)}<span class="footer-col m-sand m-right" itemscope itemtype="http://schema.org/PostalAddress"><span class="js-address" itemprop="streetAddress"></span></span>{/if}
	</div>
	<div class="footer-offer">
		{$lang->get('Настоящий сайт и представленные на нем материалы носят исключительно информационный характер и ни при каких условиях не являются публичной офертой, определяемой положениями Статьи 437 Гражданского кодекса РФ.', 'This website and all presented materials are for informational purposes only and under no circumstances are a public offer which is defined by the provisions of Article 437 of the Civil Code of Russia.')}
	</div>
	<div class="footer-catalog footer-row">
		<span class="footer-col">
			<a href="{$url_prefix}/real-estate/">{$lang->get('Строящаяся элитная недвижимость', 'Elite real estate under construction')}</a>
		</span>
		<span class="footer-col-border"><span class="slash"></span></span>
		<span class="footer-col m-right">
			<a href="{$url_prefix}/resale/">{$lang->get('Вторичная элитная недвижимость', 'Luxury apartments for resale')}</a>
		</span>
	</div>
	<nav class="footer-menu">
		<a href="{$url_prefix}/company/">{$lang->get('О нас', 'About')}</a>
		<a href="{$url_prefix}/service/">{$lang->get('Услуги', 'Service')}</a>
		<a href="{$url_prefix}/top16/">{$lang->get('Топ-16', 'TOP-16')}</a>
		<a href="{$url_prefix}/district/">{$lang->get('Районы', 'Districts')}</a>
		<a href="{$url_prefix}/contacts/">{$lang->get('Контакты', 'Contacts')}</a>
        <a href="{$url_prefix}/privacy_policy/">{$lang->get('Политика конфиденциальности', 'Privacy policy')}</a>
	</nav>
	<div class="footer-social">
		{if !empty($contacts.facebook)}<a href="{$contacts.facebook}" target="_blank">{fetch file=$path . "facebook.svg"}</a>{/if}
		{if !empty($contacts.odnoklassniki)}<a href="{$contacts.odnoklassniki}" target="_blank">{fetch file=$path . "odnoklassniki.svg"}</a>{/if}
		{if !empty($contacts.linkedin)}<a href="{$contacts.linkedin}" target="_blank">{fetch file=$path . "linkedin.svg"}</a>{/if}
		{if !empty($contacts.twitter)}<a href="{$contacts.twitter}" target="_blank">{fetch file=$path . "twitter.svg"}</a>{/if}
		{if !empty($contacts.instagram)}<a href="{$contacts.instagram}" target="_blank">{fetch file=$path . "instagram.svg"}</a>{/if}
		{if !empty($contacts.vk)}<a href="{$contacts.vk}" target="_blank">{fetch file=$path . "vk.svg"}</a>{/if}
	</div>
	<div class="footer-copy">
		© {time()|date_format:'%Y'}. {$lang->get('Все права защищены', 'All rights reserved')}. <!--noindex-->{$lang->get('Сайт сделан в', 'Site is made by')} <a href="http://webactives.ru/" target="_blank" rel="nofollow">Active</a><!--/noindex-->
	</div>
    <p style="text-align:center;margin:auto;margin-top:10px;" class="pbot"><small>{$lang->get('Мы собираем и храним файлы cookies. Файлы cookies не собирают и не хранят никакую личную информацию о Вас. Используя этот сайт, Вы даёте свое согласие на использование cookies. Чтобы отказаться от использования cookie Вам надо немедленно закрыть наш сайт. Более подробно ознакомиться с информацией об использовании файлов cookies, а также нашей Политикой защиты персональных данных Вы можете ', 'Site collects cookie data on first visit')} <a href="{$url_prefix}/privacy_policy/">{$lang->get('здесь', 'here')}</a></small></p>
    
</footer>
{/if}