{?$pageTitle = 'Списки рассылки — ' . (!empty($confTitle) ? $confTitle : '')}
{?$admin_page = 1}
<div class="content-top">
	<h1>Списки рассылки</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" 
			multiple = true
			buttons = array(
				'back' => '/site/',
				'add' => array(
					'class' => 'show-create',
					'data' => array(
						'group_id' => !empty($group.id) ? $group.id : '',
					)	
				)
			)
		)}	
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		<div class="wblock white-block-row white-header">
			<div class="w12">Название</div>
		</div>
		<div class="white-body">
			 {include file="Modules/Site/Subscribe/subscribersListsInner.tpl"}
		</div>
	</div>
</div>
{include file="/Modules/Site/Subscribe/subscribersListsFields.tpl" assign=add_url}
{capture assign=editBlock name=editBlock}
	{$add_url|html}
{/capture}
{*{include file="Admin/components/actions_panel.tpl" 
    multiple = true
    buttons = array(
        'back' => '/site/',
        'add' => '#',
        'sync' => 1
	)}*}
{*{?$group_types = array('list' => 'Список', 'filter' => 'Набор фильтров')}
<table class="subscribers-table">
    <thead>
        <tr>
            <th>Название</th>
            <th><i class="i-delete-th"></i></th>
        </tr>
    </thead>
    <tbody class="subscribers-list">
        {include file="Modules/Site/Subscribe/subscribersListsInner.tpl"}
    </tbody>
</table>
    
<div class="popup-window popup-create-list">
    <form action="/subscribe/addSubscribersList/">
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
            {*<tr>
                <td class="td-title">
                    <label for="type">Тип</label>
                </td>
                <td>
                    <select name="type">
                        <option value="list">Список</option>
                        <option value="filter">Набор фильтров</option>
                    </select>
                </td>
            </tr>*}
     {*   </table>
        <div class="buttons">
            <div class="submit a-button-blue">Создать</div>
        </div>
    </form>
</div>*}