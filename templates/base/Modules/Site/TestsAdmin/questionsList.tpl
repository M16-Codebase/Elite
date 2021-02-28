{?$questions = $test_entity.questions}

{if empty($questions)}
    Вопросы еще не созданы
{else}
	{foreach from=$questions item=question}
		<div class="wblock white-block-row" data-test-id="{$test_entity.id}" data-id="{$question.id}" data-position="{$question.position}">
			<div class="w05 drag-drop"></div>
			<div class="w9">{$question.question}</div>
			<div class="w05"></div>
			<div class="w1 action-button action-edit" title="Редактировать"><i class="icon-edit"></i></div>
			<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
		</div>
	{/foreach}
{/if}