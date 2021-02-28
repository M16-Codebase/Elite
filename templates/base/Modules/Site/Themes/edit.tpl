{?$pageTitle = $theme_data.title . ' — Редактирование темы — Управление сайтом | Сантехкомплект'}
{?$admin_page = 1}
<div class="content-top">
	<h1>Редактирование темы «{$theme_data.title}»</h1>
	<div class="content-options">
		{?$menu_list = array(array('url'=>'/site-themes/', 'title' => 'Темы'))}
		{include file="Admin/components/actions_panel.tpl"
		buttons = array(
			'back' => '/site-themes/',
			'save' => array(
				"class" => "edit-theme"
			)
		)}
	</div>
</div>
<div class="content-scroll">
	<form class="edit-theme-form viewport">
		<div class="white-blocks">
			<div class="wblock white-block-row">
				<div class="w3">Название</div>
				<div class="w9"><input type="text" name="title" /></div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">Ключевое слово</div>
				<div class="w9"><input type="text" name="keyword" disabled /></div>
			</div>
		</div>
	</form>
</div>