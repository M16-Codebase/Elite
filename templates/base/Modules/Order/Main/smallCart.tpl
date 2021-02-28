{?$count_user_order_positions = 0}
{?$price_user_order_positions = 0}
{?$cart_inner = ''}

{if !empty($user_order) && !empty($user_order.positions)}
	{capture assign="cart_inner"}		
		<ul class="cart-menu-items cart-menu dropdown-menu a-hidden">
			{foreach from=$user_order.positions item=pos}
				<li class="item link-wrap item-{$pos.entity.id}">
					{?$cover = $pos['image']}
					{if !empty($cover)}
						<img src="{$cover->getUrl(50, 80)}" alt="{$pos.title}" />
					{/if}
					<div class="info a-inline-block">
						<a href="{$pos.entity->getUrl()}" class="title link-target">{$pos.title}</a>
						<span class="item-price{if empty($pos.entity.old_price_variant)} all-price{else} one-price{/if}">
							<span class="num">
								{if !empty($pos.entity.old_price_variant)}
									<span class="old-price"><i></i>{($pos.entity.old_price_variant * $pos.count)|price_format}</span>
								{/if}
								{($pos.entity.price_variant * $pos.count)|price_format}
							</span>
							<span class="descr a-nowrap">руб. за {$pos.count}{if !empty($pos.entity.unit)} {$pos.entity.unit}{else} шт.{/if}</span>
						</span>
					</div>
				</li>
				{?$count_user_order_positions += $pos.count}
				{?$price_user_order_positions += $pos.count * $pos.entity.price_variant}
			{/foreach}			
		</ul>
	{/capture}
{/if}


<div class="header-cart dropdown hoverable"{if !empty($user_order)} data-id="{$user_order.id}"{/if}>	
	<div class="dropdown-toggle link-wrap {if empty($count_user_order_positions)}link-except{/if}">
		{if $count_user_order_positions}
			<div class="header-cart-count a-left">{$count_user_order_positions}</div>
		{else}
			<div class="header-cart-count a-left m-empty-cart"></div>
		{/if}	
		<div class="header-cart-text">
			{if empty($count_user_order_positions)}
				<span class="header-float-main link-target{if $count_user_order_positions} dd-arrow{/if}">Ваша корзина</span>
			{else if}
				<a href="/order/" class="header-float-main link-target{if $count_user_order_positions} dd-arrow{/if}">Ваша корзина</a>
			{/if}	
			{if $count_user_order_positions}
				<div class="header-cart-price header-float-white">на <strong>{$price_user_order_positions|price_format}</strong> Р</div>
			{else}
				<div class="header-cart-price header-float-white">пока пусто</div>
			{/if}
		</div>
	</div>

	<ul class="cart-add-popup cart-menu">
		<li class="item">
			<div class="cart-add-title"></div>
			<div class="shownItem link-wrap"></div>
		</li>
	</ul>

	{$cart_inner|html}
</div>