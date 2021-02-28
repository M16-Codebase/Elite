<div class="main-menu">
	<div class="swiper-container">
		<div class="w4 swiper-wrapper">
			<a href="{if $action != 'complexPage' && !empty($complex)}{$complex->getUrl()}{else}#site-top{/if}" class="swiper-slide skew m-dark-sand-skew {if $action == 'complexPage'}scroller{/if}" data-shift='-76'>{$title}</a>
			<div class="swiper-slide"><a href="{if $action != 'complexPage' && !empty($complex)}{$complex->getUrl()}{/if}#gallery-tiles"{if $action == 'complexPage'} class="scroller"{/if} data-shift='-76'>{$lang->get('Фотогалерея','Photo gallery')}</a></div>
			<span class="slash swiper-slide"></span>
			<div class="swiper-slide"><a href="{if $action != 'complexPage' && !empty($complex)}{$complex->getUrl()}{/if}#contacts-map"{if $action == 'complexPage'} class="scroller"{/if} data-shift='-76'>{$lang->get('Расположение', 'Location')}</a></div>
			<span class="slash swiper-slide"></span>
			<div class="swiper-slide"><a href="{if $action != 'complexPage' && !empty($complex)}{$complex->getUrl()}{/if}#options"{if $action == 'complexPage'} class="scroller"{/if} data-shift='-76'>{$lang->get('Детали', 'Details')}</a></div>
			<span class="slash swiper-slide"></span>
			<div class="swiper-slide"><a href="{if $action != 'complexPage' && !empty($complex)}{$complex->getUrl()}{/if}#about"{if $action == 'complexPage'} class="scroller"{/if} data-shift='-76'>{$lang->get('Описание', 'Description')}</a></div>
			{if !empty($item.flats_for_sale_count)}
            {*{var_dump($item.flats_for_sale_count)}*}
				<span class="slash swiper-slide"></span>
				<div class="swiper-slide"><a href="{if $action == 'complexPage' || $action == 'informationBlock' || $action == 'request'}{$item->getUrl()}apartments/{/if}" class="{if $action == 'apartments' || $action == 'flatPage'}m-black-filled-skew{/if}">{$lang->get('Квартиры','Apartments')}</a></div>
			{/if}
			{if $action != 'request' && !empty($complex)}<a href="{$url_prefix}/real-estate/request/?id={$complex.id}" class="swiper-slide btn m-black">{$lang->get('Оставить заявку','Send your request')}</a>{/if}
			
		</div>
		<div class="swiper-scrollbar"></div>
	</div>
</div>
