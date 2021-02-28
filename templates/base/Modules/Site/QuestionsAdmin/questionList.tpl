{foreach from=$questions item=question}
	<li class="question-item slide-box" data-id="{$question.id}" data-position="{$question.position}">
		<div class="q-header slide-header a-link">
			<div class="drag-drop box-except"></div>
			{$question.question}
		</div>
		<div class="q-body slide-body a-hidden">
			<a href="/questions-admin/deleteQuestion/" data-id="{$question.id}" class="delete-q">
				<i></i><span>Удалить вопрос</span>
			</a>
			<table class="q-table">
				<tr>
					<td>Вопрос</td>
					<td><input type="text" name="question[{$question.id}]" value="{$question.question}"></td>
				</tr>
				<tr>
					<td>Ответ</td>
					<td><textarea name="answer[{$question.id}][1]" rows="4">{$question.answers[1]}</textarea></td>
				</tr>
			</table>
		</div>		
	</li>
{/foreach}