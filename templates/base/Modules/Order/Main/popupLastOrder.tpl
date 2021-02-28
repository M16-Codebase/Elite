{?$user = $account->getUser()}
{if !empty($user)}
	<form action="/order/setLast/">
		<input type="hidden" class="order-input" />
		<div class="field-cont">
			<div class="order-title field">В корзине вашей учетной записи содержатся следующие товары:</div>
			<ul class="order-list">
				{foreach from=$last_user_order.positions item=pos}
					{?$v = $pos.entity}
					<li><a href="{$v->getUrl()}">—{$v.variant_title}</a> <span class="descr">({$pos.count} {if !empty($pos.data.unit)}{$pos.data.unit}{else}шт.{/if})</span></li>
				{/foreach}
			</ul>
			<div class="order-title field">В вашей текущей корзине:</div>
			<ul class="order-list">
				{foreach from=$current_order.positions item=pos}
					{?$v = $pos.entity}
					<li><a href="{$v->getUrl()}">—{$v.variant_title}</a> <span class="descr">({$pos.count} {if !empty($pos.data.unit)}{$pos.data.unit}{else}шт.{/if})</span></li>
				{/foreach}
			</ul>
			<ul class="order-actions justify">
				{if $last_user_order['segment_id'] == $current_order['segment_id']}
					<li class="btn btn-white-yellow-big" data-name="merge" data-val="1">Совместить корзины</li>
				{/if}
				<li class="btn btn-white-yellow-big" data-name="nothing" data-val="1">Оставить текущую</li>
				<li class="btn btn-white-yellow-big" data-name="" data-val="">Удалить текущую</li>
			</ul>
		</div>
	</form>
{/if}