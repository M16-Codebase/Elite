{?$variant_ids = array()}
{if !empty($items)}	
	{foreach from=$properties item=prop name=compare_props}
		{if empty($prop_group) || $prop.group_id != $prop_group}
			<tr class="tr-border">
				<td>{$prop.group.title}</td>
				{if !first || !empty($prop_group)}
					{section loop=count($items) name=compare_loop}
						<td></td>
					{/section}
				{/if}
			</tr>
		{/if}
		{?$prop_group = $prop.group_id}
		<tr>
			<td>
				{$prop.title}
				{if !empty($prop.public_description)}
					{include file="components/tip.tpl" title=$prop.title content=$prop.public_description}
				{/if}
			</td>		
			{foreach from=$items item=item_group}
				{?$item = $item_group.item}
				{?$special_offer = $item['special_variant']}
				<td class="i{$item.id}">
					{if $prop.multiple}
						{?$prop_val = $special_offer[$prop.key]}
					{else}
						{?$prop_val = $item[$prop.key]}
					{/if}	
					{if !empty($prop_val)}
						{if $prop.set == 1}
							{implode(', ', $prop_val)}
						{else}
							{if $prop.key == 'kontaktnoe_litso' || $prop.key == 'kurator_objekta'}
								{if !empty($prop_val.name)}{$prop_val.name}{/if} {if !empty($prop_val.surname)}{$prop_val.surname}{/if}
							{else}
								{$prop_val}
							{/if}
						{/if}
					{else}
						<em>Не указано</em>
					{/if}
				</td>
			{/foreach}
		</tr>
	{/foreach}
	{*<tr class="big-table-row">
		<td></td>
		{foreach from=$items item=item_data}
			{?$item_id = $item_data.item.id}
			{foreach from=$item_data.var_ids item=id}
				{?$variant_ids[] = $id}
				{?$variant = $variants[$id]}
				<td class="cover-cell v{$variant.id}">
					<a href="#" class="btn btn-delete remove-item" data-id="{$variant.id}" data-item_id="{$item_id}" title="Удалить из сравнения"><i class="icon i-cart-delete"></i></a>
					<div class="title"><a href="{$variant->getUrl()}">{$variant.variant_title}</a></div>
				</td>
			{/foreach}
		{/foreach}
	</tr>
	<tr class="big-table-row table-bottom">
		<td></td>
		{foreach from=$variant_ids item=id}
			{?$variant = $variants[$id]}
			{?$var_item = $variant->getItem()}
			{if !empty($variant.price_variant)}
				<td class="buy-cell v{$variant.id}">
					<div class="items-count"><input type="text" name="count" class="count-input" value="1"/>{if !empty($var_item.unit)}{$var_item.unit}{else}шт.{/if}</div>
					<div><a class="btn btn-white-yellow add-to-cart" href="/order/" data-id="{$variant.id}">В корзину</a></div>
				</td>
			{else}
				<td class="buy-cell"></td>
			{/if}	
		{/foreach}
		{if count($variants) < 4}
			{?$compare_loop = 4-count($variants)}
			{section loop=$compare_loop name=compareCovers}
				<td class="buy-cell"></td>
			{/section}
		{/if}
	</tr>*}
{/if}

{*<table class="compare-table">
	{if !empty($variants)}	
		<tr class="big-table-row">
			<td></td>
			{foreach from=$variants item=variant}
				{?$variant_available = $variant.available_in_shops}
				{?$variant_count_in_region_shops = $variant.count_in_region_shops}
				<td class="cover-cell v{$variant.id}">
					<a href="#" class="btn btn-delete remove-item" data-id="{$variant.id}" title="Удалить из сравнения"><i class="icon i-cart-delete"></i></a>
					<div class="title"><a href="{$variant->getUrl()}">{$variant.variant_title}</a></div>
					<div class="info">
						<span class="item-number">{$variant.code}</span>
						{if empty($variant_available) || $variant_available == 'Нет'}
							<span class="availability not-available"><i class="icon i-availability"></i>Нет</span>
						{elseif $variant_available == 'Мало'}
							<span class="availability little"><i class="icon i-availability"></i>Мало</span>
						{elseif $variant_available == 'Есть'}
							<span class="availability normally"><i class="icon i-availability"></i>Есть</span>
						{elseif $variant_available == 'Много'}
							<span class="availability many"><i class="icon i-availability"></i>Много</span>
						{/if}
					</div>
					{?$var_item = $variant->getItem()}
					{?$var_cover = $var_item.gallery->getCover()}
					{if empty($var_cover)}
						{?$var_cover = $var_item.gallery->getDefault()}
					{/if}
					{if !empty($var_cover)}
						<a href="{$variant->getUrl()}" class="image">
							<img src="{$var_cover->getUrl(176,210)}" alt="{$variant.variant_title}" />
						</a>
					{/if}
					{if !empty($variant_count_in_region_shops)}
						{if !empty($variant.price_variant)}
							<div class="price-cont a-inline-block">
								<div class="price-block a-inline-cont{if !empty($variant.old_price_variant)} m-double{/if}">
									{if !empty($variant.old_price_variant)}
										<div class="price-discount">
											<span class="old-price"><i></i>{$variant.old_price_variant|price_format}</span>
											<span class="price">{$variant.price_variant|price_format}</span>
										</div>
										<span class="currency">руб.<br />{if !empty($var_item.unit)}{$var_item.unit}{else}шт.{/if}</span>
									{else}
										<span class="price">{$variant.price_variant|price_format}<span class="currency">руб. / {if !empty($var_item.unit)}{$var_item.unit}{else}шт.{/if}</span></span>
									{/if}											
								</div>
								<div class="price-popup">
									<div class="price-block a-inline-cont{if !empty($variant.old_price_variant)} m-double{/if}">
										{if !empty($variant.old_price_variant)}
											<div class="price-discount">
												<span class="old-price"><i></i>{$variant.old_price_variant|price_format}</span>
												<span class="price">{$variant.price_variant|price_format}</span>
											</div>
											<span class="currency">руб.<br />{if !empty($var_item.unit)}{$var_item.unit}{else}шт.{/if}</span>
										{else}
											<span class="price">{$variant.price_variant|price_format}<span class="currency">руб. / {if !empty($var_item.unit)}{$var_item.unit}{else}шт.{/if}</span></span>
										{/if}											
									</div>
									<div class="close-popup"></div>
									<div class="popup-links justify">
										<a class="subscr-price" href=".popup-subscr.subscr-price" data-toggle="popup" data-action="open" data-id="{$variant.id}">Подписаться<br />на изменение цены</a>
										<div class="border"></div>
										<a href=".popup-bargain" data-toggle="popup" data-action="open" data-type="variant-id" data-id="{$variant.id}" class="bargain-link">Видели дешевле?<br />Давайте поторгуемся!</a>
									</div>
								</div>
							</div>							
						{/if}
					{else}
						<div class="price">
							<div class="item-report">
								<a href=".popup-subscr.subscr-avail" class="subscr-avail" data-toggle="popup" data-action="open" data-id="{$variant.id}">— Сообщить<br /> о поступлении в продажу</a>
							</div>
						</div>
					{/if}
				</td>
			{/foreach}
			{if count($variants) < 4}
				{?$compare_loop = 4-count($variants)}
				{section loop=$compare_loop name=compareCovers}
					<td class="cover-cell">
						<div class="add-item-title">
							<a href="/catalog/" title="">— Выбрать еще<br /> товар для сравнения</a>
						</div>
						<div class="add-item-num">{iteration + count($variants)}</div>
					</td>
				{/section}
			{/if}			
		</tr>
		{if $accountType != 'Guest'}
			<tr>
				<td><div class="dotted">
						<span><i class="icon i-bonus"></i>Бонусные баллы
							{if !empty($post_bonus)}
								{include file="components/tip.tpl" color='grey' title=$post_bonus.title content=$post_bonus.text}
							{/if}
						</span>
					</div></td>
				{foreach from=$variants item=variant}
					<td class="v{$variant.id}">
						<div class="dotted"><span>{$variant.bonus|plural_form:'бонус':'бонуса':'бонусов'}</span></div>
					</td>
				{/foreach}
				{if count($variants) < 4}
					{?$compare_loop = 4-count($variants)}
					{section loop=$compare_loop name=compareCovers}
						<td></td>
					{/section}
				{/if}
			</tr>
		{/if}
		{foreach from=$properties item=prop}
			<tr>
				<td>
					<div class="dotted"><span>{$prop.title}
						{if !empty($prop.public_description)}
							{include file="components/tip.tpl" title=$prop.title content=$prop.public_description}												
						{/if}
					</span></div>
				</td>
				{foreach from=$variants item=variant}
					<td class="v{$variant.id}">
						<div class="dotted"><span>
							{if $prop.multiple}
								{?$prop_val = $variant[$prop.key]}
							{else}
								{?$var_item = $variant->getItem()}
								{?$prop_val = $var_item[$prop.key]}
							{/if}	
							{if !empty($prop_val)}
								{if $prop.set == 1}
									{implode(', ', $prop_val)}
								{else}
									{$prop_val}
								{/if}
							{else}
								<em>Не указано</em>
							{/if}
						</span></div>
					</td>
				{/foreach}
				{if count($variants) < 4}
					{?$compare_loop = 4-count($variants)}
					{section loop=$compare_loop name=compareCovers}
						<td></td>
					{/section}
				{/if}	
			</tr>
		{/foreach}
		<tr class="big-table-row table-bottom">
			<td></td>
			{foreach from=$variants item=variant}
				{?$var_item = $variant->getItem()}
				{if !empty($variant.price_variant)}
					<td class="buy-cell v{$variant.id}">
						<div class="items-count"><input type="text" name="count" class="count-input" value="1"/>{if !empty($var_item.unit)}{$var_item.unit}{else}шт.{/if}</div>
						<div><a class="btn btn-white-yellow add-to-cart" href="/order/" data-id="{$variant.id}">В корзину</a></div>
					</td>
				{else}
					<td class="buy-cell"></td>
				{/if}	
			{/foreach}
			{if count($variants) < 4}
				{?$compare_loop = 4-count($variants)}
				{section loop=$compare_loop name=compareCovers}
					<td class="buy-cell"></td>
				{/section}
			{/if}
		</tr>
	{/if}
	<tr class="empty-compare{if !empty($variants)} a-hidden{/if}">
		<td></td>
		<td colspan="4">
			В сравнении нет товаров
		</td>
	</tr>
</table>*}