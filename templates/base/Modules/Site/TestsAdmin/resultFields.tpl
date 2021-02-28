{*<form action="/tests-admin/editResult/">
    <input type="hidden" name="id" />
    <input type="hidden" name="test_id" />
    Кол-во баллов: <input type="text" name="score" /><br />
    <input type="submit" value="Сохранить" />
</form>*}
<form action="/tests-admin/editResult/" class="edit-result-form">
    <input type="hidden" name="id" />
    <input type="hidden" name="test_id" />
	<div class="content-top">
		<h1>{if !empty($smarty.post.id)}Редактирование {else}Добавление {/if}вопроса</h1>
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
				<div class="w3"><strong>Кол-во баллов</strong></div>
				<div class="w9">
					<input type="text" name="score" />
				</div>
			</div>
		</div>
	</div>
</form>