<option value="">Выберите...</option>
{foreach from=$dc_used_props key=prop_key item=prop}
	<option value="{$prop_key}"{if !empty($rule_prop_key) && $rule_prop_key == $prop_key} selected{?$selected_prop = $prop}{/if}>{$prop.title}</option>
{/foreach}