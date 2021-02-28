<div class="h3 current-type">Ваш личный кабинет</div>
<ul class="aside-catalog-types">
	<li class="vm-item{if $action == 'index'} m-current{/if}">
		<a href="/profile/" class="vm-toggle">
			<i class="icon i-personal"></i>
			Личные данные
		</a>
	</li>
	<li class="vm-item{if $action == 'orders'} m-current{/if}">
		<a href="/profile/orders/" class="vm-toggle">
			<i class="icon i-order"></i>
			Заказы
			<span class="count">{$count_orders}</span>
		</a>
	</li>
	<li class="vm-item{if $action == 'bonus'} m-current{/if}">
		<a href="/profile/bonus/" class="vm-toggle">
			<i class="icon i-bonus-count"></i>
			Бонусный счет
			{?$current_user = $account->getUser()}
			<span class="count">{$current_user.bonus}</span>
		</a>
	</li>
	<li class="vm-item{if $action == 'reviews'} m-current{/if}">
		<a href="/profile/reviews/" class="vm-toggle">
			<i class="icon i-reviews"></i>
			Отзывы
			<span class="count">{$count_reviews}</span>
		</a>
	</li>
</ul>