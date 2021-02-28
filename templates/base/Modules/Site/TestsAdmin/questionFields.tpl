<form action="/tests-admin/editQuestion/" class="edit-questions-form">
    <input type="hidden" name="id" />
    <input type="hidden" name="test_id" />
	<div class="content-top">
		<h1>{if !empty($smarty.post.id)}Редактирование {else}Добавление {/if}вопроса {if !empty($question.question)}«{$question.question}»{/if}</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'url' => '#',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = createBannerHandlers
				buttons = $buttons}	
			{$createBannerHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3"><strong>Вопрос</strong></div>
				<div class="w9">
					<input type="text" name="question" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3"><strong>Примечание</strong></div>
				<div class="w9">
					<textarea name="note"></textarea>
				</div>
			</div>
			<div class="wblock slidebox-cont">
				<div class="slidebox-1{if (!empty($question) && $question.answer_type == 'value') || empty($smarty.post.id)} m-open{/if}">
					<label class="white-block-row">
						<div class="w3"><strong>Баллы в диапазоне</strong></div>
						<div class="w9">
							<input type="radio" name="answer_type" {if empty($smarty.post.id)}checked="checked"{/if} value="value" data-body=".slidebox-1"/>
						</div>
					</label>
					<div class="white-inner-cont slide-body-value">
						<div class="white-block-row">
							<div class="w3"><strong>Баллы в диапазоне</strong></div>
							<div class="w9">
								<span>от</span> <input type="text" class="m-small" name="answers[value_from]" /> <span>до</span> <input type="text" class="m-small" name="answers[value_to]" />
							</div>
						</div>
					</div>
				</div>
				<div class="slidebox-2{if !empty($question) && ($question.answer_type == 'answer' || $question.answer_type == 'multi_answer')} m-open{/if}">
					<label class="white-block-row">
						<div class="w3"><strong>Выбор одного варианта ответа</strong></div>
						<div class="w9">
							<input type="radio" name="answer_type" value="answer" data-body=".slidebox-2"/>
						</div>
					</label>
					<label class="white-block-row">
						<div class="w3"><strong>Выбор одного или нескольких вариантов ответа</strong></div>
						<div class="w9">
							<input type="radio" name="answer_type" value="multi_answer" data-body=".slidebox-2"/>
						</div>
					</label>
					<div class="slide-body-answer">
						<div class="white-block-row">
							<div class="w9"><strong>Ответы</strong></div>
							<div class="w3"><strong>Баллы за ответы</strong></div>
						</div>
						<div class="answers-list white-inner-cont">
							{if !empty($question.answers) && ($question.answer_type == 'answer' || $question.answer_type == 'multi_answer')}
								{foreach from=$question.answers item=answer name=foo}
									<div class="answer white-block-row">
								<div class="w9">
											<input type="text" name="answers[{$smarty.foreach.foo.iteration-1}][answer]" value="{$answer.answer}"/>
								</div>
								<div class="w2">
											<input type="text" name="answers[{$smarty.foreach.foo.iteration-1}][value]" value="{$answer.value}"/>
								</div>
								<div class="w1 action-button action-delete" title="Удалить ответ"><i class="icon-delete"></i></div>
							</div>
								{/foreach}
							{/if}
							<div class="answer white-block-row origin a-hidden">
										<div class="w9">
									<input type="text" name="answers[][answer]" />
										</div>
										<div class="w2">
									<input type="text" name="answers[][value]" />
										</div>
								<div class="w1 action-button action-delete" title="Удалить ответ"><i class="icon-delete"></i></div>
							</div>
							<div class="add-row row">
								<div class="add-button add-btn w3">
									<i class="icon-add"></i> <span class="small-descr">Добавить</span>
								</div>
								<div class="w9"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			{*<div class="wblock white-block-row slide-body-value">
				<div class="w3"><strong>Баллы в диапазоне</strong></div>
				<div class="w9">
					<span>от</span> <input type="text" class="m-small" name="answers[value_from]" /> <span>до</span> <input type="text" class="m-small" name="answers[value_to]" />
				</div>
			</div>
			<div class="wblock slide-body-answer">
				<div class="white-block-row">
					<div class="w9"><strong>Ответы</strong></div>
					<div class="w3"><strong>Баллы за ответы</strong></div>
				</div>
				<div class="answers-list white-inner-cont">
					{if !empty($question.answers)}
						{foreach from=$question.answers item=answer}
							<div class="answer white-block-row">
								<div class="w9">
									<input type="text" name="answers[][answer]" value="{$answer.answer}"/>
								</div>
								<div class="w2">
									<input type="text" name="answers[][value]" value="{$answer.value}"/>
								</div>
								<div class="w1 action-button delete-item" title="Удалить ответ"><i class="icon-prop-delete"></i></div>
							</div>
						{/foreach}
					{/if}
					<div class="answer white-block-row origin a-hidden">
						<div class="w9">
							<input type="text" name="answers[][answer]" />
						</div>
						<div class="w2">
							<input type="text" name="answers[][value]" />
						</div>
						<div class="w1 action-button action-delete" title="Удалить ответ"><i class="icon-delete"></i></div>
					</div>
					<div class="add-row row">
						<div class="add-button add-btn w3">
							<i class="icon-add"></i> <span class="small-descr">Добавить</span>
						</div>
						<div class="w9"></div>
					</div>
				</div>
			</div>*}
			{*<div class="wblock">
				<div class="wblock white-block-row">
					<div class="w3"><strong>Ответы</strong></div>
					<div class="w9">
						<span>от</span> <input type="text" class="m-small" name="answers[value_from]" /> <span>до</span> <input type="text" class="m-small" name="answers[value_to]" />
					</div>
				</div>
				<div class="white-inner-cont">
					
				</div>
			</div>*}
		</div>
	</div>
{*    Вопрос: <input type="text" name="question" /><br />
    Примечание: <textarea name="note"></textarea><br />
    Тип вопроса: <br />
    <input type="radio" name="answer_type" value="value" /> баллы в диапазоне<br />
    <input type="radio" name="answer_type" value="answer" /> выбор одного варианта ответа<br />
    <input type="radio" name="answer_type" value="multi_answer" /> выбор одного или нескольких вариантов ответа<br />*}
{*    Баллы в диапазоне от <input type="text" name="answers[value_from]" /> до <input type="text" name="answers[value_to]" /><br />*}
{*	
    Ответы: <br />
    Ответ: <input type="text" name="answers[1][answer]" /><br />
    Баллы за ответ: <input type="text" name="answers[1][value]" /><br />
    Ответ: <input type="text" name="answers[2][answer]" /><br />
    Баллы за ответ: <input type="text" name="answers[2][value]" /><br />
    Ответ: <input type="text" name="answers[3][answer]" /><br />
    Баллы за ответ: <input type="text" name="answers[3][value]" /><br />
    <input type="submit" value="Сохранить" />*}
</form>