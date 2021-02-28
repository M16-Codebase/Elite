{?$pageTitle = 'Редактор меню — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Редактор меню</h1>
	<div class="content-options">
		{include file='Admin/components/actions_panel.tpl'
			buttons = array(
				'add' => ($account->isPermission('menu-editor', 'create')? 1 : 0)
			)
		}
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		<div class="wblock white-block-row white-header">
			<div class="w3">Ключ</div>
			<div class="w3"></div>
			<div class="w6"></div>
		</div>
		<div class="white-body menu-list">
			{include file='Modules/Site/MenuEditor/menuList.tpl'}
		</div>
	</div>
</div>
{include file="/Modules/Site/MenuEditor/addMenu.tpl" assign=add_menu}
{include file="/Modules/Site/MenuEditor/editMenu.tpl" assign=edit_menu}
{capture assign=editBlock name=editBlock}
	{$add_menu|html}
	{$edit_menu|html}
{/capture}