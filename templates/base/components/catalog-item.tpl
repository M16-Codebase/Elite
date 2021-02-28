<li class="catalog-item link-wrap">
	<div class="title"><a href="{$catalog_item->getUrl()}"><span>{if !empty($catalog_item.title)}{$catalog_item.title}{else}No title{/if}</span></a></div>
	<a href="{$catalog_item->getUrl()}" class="cover-block link-target">
		{if !empty($catalog_item.gallery)}
			{?$gallery = $catalog_item.gallery}
			{?$cover = $gallery->getCover()}
			{if empty($cover)}
				{?$cover = $gallery->getDefault()}
			{/if}
			{if !empty($cover)}
				<div class="markers m-small">
					{if !empty($catalog_item.properties.sp_new.value)}<div class="new"></div>{/if}
					{if !empty($catalog_item.properties.sp_hit.value)}<div class="hit"></div>{/if}
				</div>
				<img src="{$cover->getUrl(176, 210)}" alt="{if !empty($catalog_item.title)}{$catalog_item.title}{else}No title{/if}" class="cover" />
			{/if}
		{/if}
		{if !empty($catalog_item.price)}
			{if empty($catalog_item.old_price)}
				<span class="price-block cover-price a-inline-cont">
					{*<span class="price">{if $catalog_item.properties.prefix.value == 1}от {/if}{$catalog_item.price|price_format}<span class="currency">руб.</span></span>*}
				</span>
			{else}
				<div class="price-block cover-price a-inline-cont m-double">
					<div class="price-discount">
						<span class="old-price"><i></i>{if $catalog_item.properties.prefix.value == 1}от {/if}{$catalog_item.old_price|price_format}</span>
						<span class="price">{if $catalog_item.properties.prefix.value == 1}от {/if}{$catalog_item.price|price_format}</span>
					</div>
					<span class="currency">руб.</span>
				</div>
			{/if}
		{/if}
	</a>
</li>


{*{?$gallery_listing = ''}
<li class="catalog-item">
	<div class="preloader"></div>
	<div class="markers">
		{if empty($moduleUrl) || !($moduleUrl == 'main' && $action == 'index')}
			{if !empty($item.properties.sp_new.value)}
				<div class="new" title="Новинка"></div>
			{elseif !empty($item.properties.sp_hit.value)}
				<div class="hit" title="Хит продаж"></div>
			{/if}
		{/if}
	</div>
	<div class="cover-cont">
		<div class="cover">
			{?$no_images = true}
			{?$colors_count = 0}
			{if !empty($item.galleries)}
				{foreach from=$item.galleries item=gallery name=gallery_list}
					{?$images = $gallery->getImages()}
                    {?$gal_variant_count = $gallery->getVariantCount()}
					{if !empty($images) && !empty($gal_variant_count)}
						{?$cover = 0}
						{?$colors_count++}
						{?$no_images = false}						
						{?$first_cover_id = -1}
						{?$gal_color = $gallery->getColor()}
						{?$searching_color = false}
						{if !empty($colors_search)}
							{?$searching_color = true}
						{else}
							{?$cover = $gallery->getCover()}
						{/if}
						{?$showed_id = array()}
						{?$back_pic = $gallery->getBack()}
						{?$front_pic = $gallery->getFront()}
						{?$cover_pic = $gallery->getCover()}
						{if !empty($back_pic)}{?$showed_id[$back_pic.id] = 1}{/if}
						{if !empty($front_pic)}{?$showed_id[$front_pic.id] = 1}{/if}
						{if !empty($cover_pic)}{?$showed_id[$cover_pic.id] = 1}{/if}
						{if first && !empty($cover)}
							<img src="{$cover->getUrl(180, 220)}" data-color-id="{$gallery->getId()}" data-color="{$gal_color}" alt="{$item.title} {$gallery->getColorText()}" />
							{?$first_cover_id = $cover.id}
						{/if}
						{foreach from=$images item=img}
							{if !empty($showed_id[$img.id])}
								{if empty($cover) && ($first_cover_id == -1) && (!$searching_color || ($searching_color && !empty($colors_search[$gal_color]) ))}
									<img src="{$img->getUrl(180, 220)}" data-color="{$gal_color}" alt="{$item.title} {$gallery->getColorText()}" />
									{?$first_cover_id = $img.id}
								{elseif $img.id != $first_cover_id}
									<img data-src="{$img->getUrl(180, 220)}" data-color="{$gal_color}" src="#" alt="{$item.title} {$gallery->getColorText()}" />
								{/if}
							{/if}
						{/foreach}
						{?$gallery_listing = $gallery_listing . '<li data-color="'.$gal_color.'" data-id="' .$gallery->getId(). '" title="'.$gallery->getColorText().'" data-url="'.$gallery->getUrl().'"><div style="background-color: #'.$gal_color.';"></div></li>'}
					{/if}	
				{/foreach}
			{/if}
			{if $no_images}
				{?$defImage = $item->getDefaultImage()}
				<img src="/img/icons/no-image.png" alt="{$item.title} no-image" class="no-image" />
			{/if}
		</div>
	</div>
	<div class="info">
		<div class="controls">
			<div class="arrow-prev link-except"><div></div></div>
			<div class="arrow-next link-except"><div></div></div>
		</div>
		<div class="bottom-block">
			<ul class="colors link-except">
				{if !empty($gallery_listing) && $colors_count > 1}				
					{$gallery_listing|html}				
				{/if}
			</ul>	
			<div class="titles-block">
				<a href="{$item->getUrl()}" class="title link-target">{$item.title}</a>
				{?$uses_array = array()}
				{if !empty($item.properties.use_snow.value)}{?$uses_array[] = 'Снегоход'}{/if}
				{if !empty($item.properties.use_water.value)}{?$uses_array[] = 'Гидроцикл'}{/if}
				{if !empty($item.properties.use_quad.value)}{?$uses_array[] = 'Квадроцикл'}{/if}
				{if !empty($item.properties.use_moto.value)}{?$uses_array[] = 'Мотоцикл'}{/if}
				<div class="type">{if (!empty($uses_array))}{implode(', ', $uses_array)}{/if}</div>
				<div class="price">
					{if $item.old_price > $item.price}
						<span class="old-price">{$item.old_price|price_format} Р</span>
					{/if}
					{if !empty($item.price)}{$item.price|price_format} Р{/if}
				</div>
			</div>
		</div>						
	</div>
</li>*}