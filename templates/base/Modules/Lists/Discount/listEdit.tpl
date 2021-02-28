{?$admin_page = 1}
<h1>Акции</h1>
{include file="Admin/components/actions_panel.tpl"
	buttons = array(
		'add' => '/discount/create/'
	)}
<table class="ribbed discountList">
    <thead>
        <tr>
			<th></th>
            <th>ID</th>
            <th>Название</th>
            <th>Статус</th>
            <th>Регион</th>
            <th>Дата начала</th>
            <th>Дата окончания</th>
            <th class="th-delete"></th>
        </tr>
    </thead>
    <tbody>
        {include file="Modules/Lists/Discount/discountList.tpl"}
    </tbody>
</table>