{?$pageTitle = 'Применение — Управление сайтом | ТехноАльт'}
<h1>Применение</h1>
{include file="Admin/components/actions_panel.tpl"
	buttons = array(
		'add' => array(
			'url' => '/catalog-type/addUsingType/',
			'text' => 'Новый параметр'
		)
	)}
<div class="using-page">
	<ul class="using-types-list sortable">
		{include file="Modules/Catalog/Type/usingTypesList.tpl"}
	</ul>
</div>

<div class="popup-window popup-edit-property">
    <form action="/catalog-type/usingTypeSave/">
        
    </form>
</div>