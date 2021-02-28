{?$company_tiles = array()}
{if !empty($transport_companies)}
	{foreach from=$transport_companies item=company}
		{?$company_tiles[$company.id] = $company.name}
	{/foreach}
{/if}
{?$positions = $order->getPositions()}
{?$order_type = $order->getType()}
<div class="order wblock slidebox{if !empty($smarty.get.opened)} m-open{/if}" data-id="{$order.id}" data-speed="500">
	<div class="order-header white-block-row slide-header">
		<div class="order-num w2">
			<span class="state m-{$order.properties.state.value_key}">
				<strong>{$order.id}</strong> <span class="small-descr">({$segments[$order.segment_id].key})</span>
			</span><br />
			<span class="order-status state m-{$order.properties.state.value_key}">{$order.state}</span>
		</div>
		<div class="w2">
			<div class="date">{$order.timestamp|date_format:'%d.%m.%Y'}</div>
			<div class="time small-descr">{$order.timestamp|date_format:'%H:%M'}</div>
		</div>
		<div class="w3">
			{if $order.state != 'tmp'}
				<div class="name">
					{if !empty($order_type)}
						<i class="icon-{$order_type.key}" title="{$order_type.title}"></i>
					{/if}
					<a href="mailto:{if !empty($order.email)}{$order.email}{/if}">
						{if !empty($order.name)}{$order.name} {/if}
						{if !empty($order.patronymic)}{$order.patronymic} {/if}
						{if !empty($order.surname)}{$order.surname}{/if}
					</a>
				</div>
				{if !empty($order.phone)}
					<div class="phone">{$order.phone}</div>
				{/if}
			{/if}
		</div>
		<div class="w1 action-button action-delivery" title="{$order.delivery_type}{if !empty($order.delivery_price)} ({$order.delivery_price} Р){/if}">
			{if !empty($order.delivery_type)}
				<i class="icon-{if $order.properties.delivery_type.value_key == 'self'}warehouse{else}delivery{/if}"></i>
			{/if}
		</div>
		<div class="w2">
			<strong>{$order.total_cost|price_format} Р</strong> ({count($positions)})
			{if !empty($order.pay_type)}
				<div class="paytype m-{$order.properties.pay_type.value_key}">{$order.pay_type}</div>
			{/if}
		</div>
		<div class="w1 action-button action-comment"{if !empty($order.descr)} title="{$order.descr}"{/if}>
			{if !empty($order.descr)}
				<i class="icon-comment"></i>
			{/if}
		</div>
		<div class="action-button action-edit w1 box-except" title="Редактировать заказ">
			<i class="icon-edit"></i>
		</div>
	</div>
		
	<div class="order-inner slide-body{if empty($smarty.get.opened)} a-hidden{/if}">
		<div class="white-inner-cont">
			<div class="order-aside">
				<div class="action-button action-add">
					<i class="action-icon icon-add"></i>
					<div class="action-text">Новая<br />позиция</div>
				</div>
			</div>
			{if !empty($positions)}
				{foreach from=$positions item=$p}
					<div class="order-position white-block-row" data-position_id="{$p.id}" data-id="{$order.id}">
						<div class="w5">
							<a href="{$p->getUrl()}" class="title">{$p.title}</a>&nbsp;&nbsp;
							<span class="small-descr">({$p.entity_id})</span>
						</div>
						<div class="w3 count-price">
							<input type="text" class="count-input" value="{$p.count}{if !empty($p.unit)} {$p.unit}{/if}" data-value="{$p.count}" /> x 
							<input type="text" class="price-input" value="{$p.price|price_format} Р" data-value="{$p.price}" />
						</div>
						<div class="w2">
							{($p.count*$p.price)|price_format} Р
						</div>
						<div class="w1 action-button action-delete" title="Удалить позицию">
							<i class="icon-delete"></i>
						</div>
						<div class="w1"></div>
					</div>
				{/foreach}
				<div class="white-block-row">
					<div class="w5"></div>
					<div class="w3 count-price">
						<div class="input-col">Итого</div>
					</div>
					<div class="w2">
						<strong>{$order.real_positions_price|price_format} Р</strong>
						{if !empty($order.delivery_price)}
							<div>+ доставка&nbsp;&nbsp;<strong>{$order.delivery_price|price_format} Р</strong></div>
						{/if}
					</div>
					<div class="w1"></div>
					<div class="w1"></div>
				</div>
			{else}
				<div class="white-block-row">
					<div class="w12">В заказе нет позиций</div>
				</div>
			{/if}
		</div>
	</div>
</div>