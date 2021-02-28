{?$admin_page = 1}
<h1>Сегменты</h1>
{include file="Admin/components/actions_panel.tpl"
	buttons = array(
		'add' => 1
	)}
<table class="ribbed regionList type-segments-table">
	<thead>
		<th>ID</th>
		<th>Название</th>
		<th>Ключ</th>
		<th></th>
	</thead>
    <tbody>
        {include file="Modules/Lists/Admin/segmentList.tpl"}
    </tbody>
</table>

<div class="popup-window popup-region popup-window-addRegion">
    <form action="/lists/editSegment/">
        <div class="fields">
            {include file="Modules/Lists/Admin/editFieldsSegment.tpl"}
        </div>
        <div class="buttons">
            <button class="a-button-blue">Добавить</button>
        </div>
    </form>
</div>
<div class="popup-window popup-region popup-window-editRegion">
    <form action="/lists/editSegment/">
        <div class="fields">
            {include file="Modules/Lists/Admin/editFieldsSegment.tpl"}
        </div>
        <div class="buttons">
            <button class="a-button-blue">Сохранить</button>
        </div>
    </form>
</div>