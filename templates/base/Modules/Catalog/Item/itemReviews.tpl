<div class="content-scroll">
	<div class="aside-panel">
		{include file="Admin/components/actions_panel.tpl"
			multiple = true
			buttons = array(
				'add' => 1,
				'delete' => array(
					'inactive' => true
				)
			)}
	</div>
	<form class="actions-cont items-edit viewport">
		<div class="white-blocks reviews-list overview">
			{if !empty($item_reviews) && count($item_reviews)}
				{foreach from=$item_reviews item=review}
					<div class="wblock white-block-row" data-item_id="{$review.id}">
						<label class="w05">
							<input type="checkbox" name="check[{$review.id}]" class="check-item" />
						</label>
						<div class="w9">
							{$review.text}
						</div>
						<div class="w05"></div>
						<div class="action-button action-edit w1" title="Редактировать">
							<i class="icon-edit"></i>
						</div>
						<div class="action-button action-delete w1 m-border" title="Удалить">
							<i class="icon-delete"></i>
						</div>
					</div>
				{/foreach}
			{else}
				<div class="wblock white-block-row">
					<div class="w12">
						Отзывов нет
					</div>
				</div>
			{/if}
			<input type="submit" class="a-hidden" />
		</div>
	</form>
</div>