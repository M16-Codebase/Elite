{?$pageTitle = 'Заказы — ' . (!empty($confTitle) ? $confTitle : '')}
{?$orders = $catalog_items}
{?$filter_url = "/order-admin/listItems/"}
{include file="Modules/Catalog/Item/itemFilter.tpl" assign=aside_filter}

<div class="content-top">
	<h1>Заказы</h1>
	<div class="content-options">
		{?$buttons = array(
			'back' => '/site/'
		)}
		{foreach from=$catalog_children key=key item=ch name=children_buttons}
			{?$buttons['add' . iteration] = array(
				'text' => ($key == 'orders_fiz')? 'Новый заказ физического лица' : 'Новый заказ юридического лица',
				'class' => 'action-add',
				'icon' => ($key == 'orders_fiz')? 'order_fiz' : 'order_ur',
				'inactive' => ($account->isPermission('order-admin', 'create') ? false : true),
				'data' => array('type_id' => $ch.id)
			)}
		{/foreach}
		{include file="Admin/components/actions_panel.tpl"
			assign = addFormButtons
			buttons = $buttons}
		{$addFormButtons|html}
	</div>
</div>
<div class="content-scroll">
	<div class="viewport">
		<div class="orders-list white-blocks sortable" data-url="/catalog-type/move/" data-positionattr="position" data-sendattrs="type_id;parent_id" data-newpositionname="position">
			{if !empty($catalog_items) && count($catalog_items)}
				{include file="Modules/Order/Admin/listItems.tpl" without_filter=true}
			{else}
				<div class="wblock white-block-row">
					<div class="w12">Нет заказов</div>
				</div>
			{/if}
		</div>
		{include file="Admin/components/paging.tpl" count=$catalog_items_count}
		{if !empty($current_type_filter)}
			{$current_type_filter|html}
		{/if}
	</div>
</div>


{capture assign=editBlock name=editBlock}
	<form action="/order-admin/findVariant/" class="add-position-form">
		<div class="content-top">
			<h1>Добавление позиции</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена')
				)}
				{include file="Admin/components/actions_panel.tpl"
					assign = addFormButtons
					buttons = $buttons}
				{$addFormButtons|html}
			</div>
		</div>
		<div class="content-scroll">
			<div class="viewport">
				<input type="hidden" name="order_id" value="" />
				<div class="white-blocks">
					<div class="white-body">
						<div class="wblock white-block-row">
							<div class="w2">ID</div>
							<div class="w9">
								<input type="hidden" name="field" value="id" />
								<input type="text" name="value" />
							</div>
							<div class="w1 action-button action-search" title="Найти"><i class="icon-search"></i></div>
						</div>
					</div>
					<div class="find-variant white-body"></div>
				</div>
			</div>
		</div>
	</form>
{/capture}