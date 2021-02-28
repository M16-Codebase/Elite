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
	<div class="viewport">
		<div class="white-blocks questions-list overview">
			{if !empty($item_questions) && count($item_questions)}
				{foreach from=$item_questions item=question}
					<div class="wblock white-block-row" data-id="{$question.id}">
						<label class="w05">
							<input type="checkbox" name="check[{$question.id}]" class="check-item" />
						</label>
						<div class="w9">
							{$question.text}
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
						Вопросов нет
					</div>
				</div>
			{/if}
			<input type="submit" class="a-hidden" />
		</div>
	</div>
</div>