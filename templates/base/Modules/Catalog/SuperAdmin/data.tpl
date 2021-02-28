{if !empty($data)}
	{foreach from=$data item=v key=k}
		{$tab|html}<strong>{$k}</strong>: {if !is_array($v)}{$v}{else}<br />{?$tab=$tab . '&nbsp;&nbsp;&nbsp;&nbsp;'}{include file="Modules/Catalog/SuperAdmin/data.tpl" data=$v}{?$tab = strlen($tab) <= 24 ? '' : substr($tab, -24)}{/if}<br />
	{/foreach}
{/if}