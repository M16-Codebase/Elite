<form action="/menu-editor/editMenuItem/" class="create-menu-item-form">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>Редактирование раздела меню</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	<input type="hidden" name="id">
    <input type="hidden" name="menu_id">
    <input type="hidden" name="parent_id">
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock">
				<div class="white-block-row">
					<div class="w4">Заголовок</div>
					<div class="w8">
						<input type="text" name="name">
					</div>
				</div>
				<div class="white-block-row">
					<div class="w4">Подсказка</div>
					<div class="w8">
						<input type="text" name="title">
					</div>
				</div>
				<div class="white-block-row">
					<div class="w4">Ссылка</div>
					<div class="w8">
						 <input type="text" name="url">
					</div>
				</div>
				<div class="white-block-row">
					<div class="w4">Изображение</div>
					<div class="w8">
						<input type="file" name="image">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

{*
<tr>
    <td class="td-title">Заголовок</td>
    <td>
        <input type="hidden" name="id">
        <input type="hidden" name="menu_id">
        <input type="hidden" name="parent_id">
        <input type="text" name="name">
    </td>
</tr>
<tr>
    <td class="td-title">Подсказка</td>
    <td>
        <input type="text" name="title">
    </td>
</tr>
<tr>
    <td class="td-title">Ссылка</td>
    <td>
        <input type="text" name="url">
    </td>
</tr>
<tr>
    <td class="td-title">Изображение</td>
    <td>
        <input type="file" name="image">
    </td>
</tr>*}