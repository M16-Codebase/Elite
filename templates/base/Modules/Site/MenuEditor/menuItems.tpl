{?$pageTitle = (!empty($menu.key) ? $menu.key . ' — ' : '') . 'Редактор меню — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>{$menu.key}</h1>
	<div class="content-options">
		{include file='Admin/components/actions_panel.tpl'
			buttons = array(
				'back' => ('/menu-editor/'),
				'add' => ($account->isPermission('menu-editor', 'create')? 1 : 0)
			)
		}
	{*	{include file='Admin/components/actions_panel.tpl'
			buttons = array(
				'add' => array('data' => array('menu_id' => $menu.id))
			)
		}*}
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		<div class="wblock white-block-row white-header">
			<div class="w3">Ключ</div>
			<div class="w3"></div>
			<div class="w6"></div>
		</div>
		<div class="white-body sortable menu-sortable" data-url="/menu-editor/changePosition/" data-sendattrs="id;parent_id" data-newpositionname='position'>
			 {include file='Modules/Site/MenuEditor/menuItemsList.tpl'}
		</div>
	</div>
</div>	
{include file="/Modules/Site/MenuEditor/createMenuItem.tpl" assign=create_menu_item}
{capture assign=editBlock name=editBlock}
	{$create_menu_item|html}
{/capture}