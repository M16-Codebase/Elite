
<form action="{if !empty($smarty.post.fr)}/seo/editRedirect/{else}/seo/createRedirect/{/if}" class="add-redirect">
	<input type="hidden" name="edit" value="1">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>{if !empty($smarty.post.fr)}Редактирование{else}Создание{/if} редиректа</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => !empty($smarty.post.fr)? 'Сохранить' : 'Создать',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3">Откуда</div>
				<div class="w9">
					{if !empty($smarty.post.fr)}
						<input type="hidden" name="fr" />
						<input type="text" name="from_visible" disabled data-disabled value="{$smarty.post.fr}"/>
					{else}
						<input type="text" name="fr" />
					{/if}
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">Куда</div>
				<div class="w9">
					<input type="text" name="to" />
				</div>
			</div>				
		</div>
	</div>
</form>