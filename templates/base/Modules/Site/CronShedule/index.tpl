{?$pageTitle = 'Расписание задач — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Расписание задач</h1>
	<div class="content-options">
		{include file='Admin/components/actions_panel.tpl'
			buttons = array(
				'save' => ($account->isPermission('tasks', 'save') ? 1 : 0)
			)
		}
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
        <form action="/cron-shedule/save/" method="POST">
            <div class="wblock white-block-row">
                <div class="w12">Приоритет задач</div>
            </div>
            <div class="wblock white-block-row white-header">
                <div class="w05"></div>
                <div class="w3">Ключ</div>
                <div class="w3">Название</div>
                <div class="w3">План</div>
                <div class="w1">Активна</div>
                {if $accountType == 'SuperAdmin'}
                    <div class="w1">Fix</div>
                {/if}
            </div>
            <div class="white-body sortable" data-notsend=1>
                    {if !empty($shedule)}
                        {foreach from=$shedule item=sh}
                            <div class="wblock white-block-row">
                                <div class="w05 drag-drop"></div>
                                <div class="w3"><input type="hidden" name="{$sh.type}[type]"{if $sh.fixed && $accountType != 'SuperAdmin'} disabled{/if} />{$sh.type}</div>
                                <div class="w3"><input type="text" name="{$sh.type}[title]"{if $sh.fixed && $accountType != 'SuperAdmin'} disabled{/if} /></div>
                                <div class="w3">{if $sh['is_manual']}Ручная задача<input type="hidden" name="{$sh.type}[plan]" value="1" />{else}<input type="text" name="{$sh.type}[plan]"{if $sh.fixed && $accountType != 'SuperAdmin'} disabled{/if} />{/if}</div>
                                <div class="w1"><input type="hidden" name="{$sh.type}[status]" value="0"{if $sh.fixed && $accountType != 'SuperAdmin'} disabled{/if} /><input type="checkbox" name="{$sh.type}[status]" value="1"{if $sh.fixed && $accountType != 'SuperAdmin'} disabled{/if} /></div>
                                {if $accountType == 'SuperAdmin'}
                                    <div class="w1"><input type="hidden" name="{$sh.type}[fixed]" value="0" /><input type="checkbox" name="{$sh.type}[fixed]" value="1" /></div>
                                {/if}
                            </div>
                        {/foreach}
                    {/if}
            </div>
        </form>
	</div>
</div>