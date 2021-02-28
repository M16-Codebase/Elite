{if $values_type == 'enum' || $values_type == 'flag'}
	<div class="small-descr">В значении:</div>
	{if !empty($selected_prop)}
		{if $values_type == 'flag'}
			{?$enum_values = array()}
			{foreach from=$selected_prop.values key=k item=v}
				{if in_array($k, array('yes','no'))}
					{?$k = $k == 'yes' ? 1 : 0}
				{/if}
				{?$enum_values[$k] = $v}
			{/foreach}
		{else}
			{?$enum_values = $selected_prop.values}
		{/if}
	{elseif !empty($flag_values)}
		{?$enum_values = $flag_values}
	{/if}
	{if !empty($enum_values)}
		{foreach from=$enum_values key=val_id item=val_title}
			<label>
				<input type="checkbox" name="value[]" value="{$val_id}" class="cbx-value"{if !empty($rule_prop) && is_array($rule_prop) && in_array($val_id, $rule_prop.value)} checked{/if} /> 
				{if isset($val_title.value)}{$val_title.value}{else}{$val_title}{/if}
			</label>&nbsp;&nbsp;
		{/foreach}
	{/if}
{elseif $values_type == 'number' || $values_type == 'int' || $values_type == 'float'}
	<span class="small-descr">В значении </span>
	<input type="text" name="" class="value-input m-small"{if isset($rule_prop.value)} value="{$rule_prop.value}"{/if}>
	<span class="small-descr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;или&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	<span class="small-descr">от </span>
	<input type="text" name="" class="min-input m-small"{if isset($rule_prop.min)} value="{$rule_prop.min}"{/if}>
	<span class="small-descr"> до </span>
	<input type="text" name="" class="max-input m-small"{if isset($rule_prop.max)} value="{$rule_prop.max}"{/if}>
{else}
	<span class="small-descr">В значении </span>
	<input type="text" name="" class="value-input m-med"{if isset($rule_prop.value)} value="{$rule_prop.value}"{/if}>
{/if}