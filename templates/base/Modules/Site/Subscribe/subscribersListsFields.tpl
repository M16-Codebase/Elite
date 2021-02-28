{*<form action="/subscribe/addSubscribersList/" class="subscribers-lists-form">
	<table class="ribbed">
		<tr>
			<td class="td-title">
				<label for="name_key">Название списка</label>
			</td>
			<td>
				<input type="text" name="name" />
			</td>
		</tr>
		<input type="hidden" name="type" value="list">
	</table>
	<div class="buttons">
		<div class="submit a-button-blue">Создать</div>
	</div>
</form>*}
<form class="subscribers-lists-form" action="/subscribe/addSubscribersList/">
	<input type="hidden" name="type" value="list">
	<div class="content-top">
		<h1>{if !empty($smarty.post.id)}Редактирование {else}Добавление {/if}списка рассылки</h1>
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
				assign = createTeaserHandlers
				buttons = $buttons}	
			{$createTeaserHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3"><strong>Название списка</strong></div>
				<div class="w9">
					<input type="text" name="name" />
				</div>
			</div>
		</div>
	</div>
</form>