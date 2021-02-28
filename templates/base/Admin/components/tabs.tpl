{*
	'tabs' => array(
		'tab_name' => array(
			'key' => str,
			'url' => str,
			'text' => str,
			'current' => bool,
			'inactive' => bool,
			'data' => array(),
			'class' => str,
			'attr' => str,
			'count' => str,
			'count_class' => str
		),
		'tab_name' => ...
	)
	'class' => str
	'attr' => str
	'data' => array()
	'count' => num
*}

{if !empty($tabs)}
	{if !empty($data)}
		{?$tabs_data_string = ''}
		{foreach from=$data key=tabs_data_name value=tabs_data_value}
			{?$tabs_data_string = ($tabs_data_string|html) . ' data-' . $tabs_data_name . '="' . ($tabs_data_value|regex_replace:'/[\'"]/':"") . '"'}
		{/foreach}
	{/if}
	<div class="tabs{if !empty($class)} {$class}{/if}"{if !empty($tabs_data_string)}{$tabs_data_string|html}{/if}{if !empty($attr)} {$attr|html}{/if}>
		{?$tabs_i = 0}
		{?$tabs_overflow = false}
		{foreach from=$tabs item=tab key=tab_key}
			{if is_array($tab)}
				{?$tabs_i++}
				{if !empty($tab.key)}
					{?$tab_key = $tab.key}
				{/if}
				{if !empty($tab.data)}
					{?$tab_data_string = ''}
					{foreach from=$tab.data key=tab_data_name value=tab_data_value}
						{?$tab_data_string = $tab_data_string . ' data-' . $tab_data_name . '=' . $tab_data_value}
					{/foreach}
				{/if}
				<a data-target="#{$tab_key}"{if !empty($tab_data_string)}{$tab_data_string}{/if} 
				href="{if !empty($tab.url)}{$tab.url}{else}#{/if}" 
				class="tab-title tab-title-{$tab_key}
					{if !empty($tab.class)} {$tab.class}{/if}
					{if !empty($tab.current)} m-current{/if}
					{if !empty($tab.inactive)} m-inactive{/if}
					{if !empty($count) && $tabs_i > $count} a-hidden{?$tabs_overflow = true}{/if}"
					{if !empty($tab.attr)} {$tab.attr}{/if}>
					{if !empty($tab.text)}{$tab.text} {else}{$tab_key} {/if}
					{if isset($tab.count)}
						<span class="num count-{$tab_key}{if !empty($tab.count_class)} {$tab.count_class}{/if}">
							{if $tab.count === '+'}<i class="icon-check"></i>{else}{$tab.count}{/if}
						</span>
					{/if}
				</a>
			{/if}
		{/foreach}
		<div class="tab-select{if !$tabs_overflow} a-hidden{/if}">
			<select name="tabs" class="tab-title">
				{foreach from=$tabs item=tab key=tab_key}
					{if is_array($tab)}
						{if !empty($tab.key)}
							{?$tab_key = $tab.key}
						{/if}
						<option value="{$tab_key}" data-target="#{$tab_key}"{if !empty($tab.url)} data-url="{$tab.url}"{/if}{if !empty($tab.current)} selected{/if}>
							{if !empty($tab.text)}{$tab.text}{else}{$tab_key}{/if}
						</option>
					{/if}
				{/foreach}
			</select>
		</div>
	</div>
{/if}