{include file="Admin/components/buttons_titles.tpl"}

{* @TODO прокомментировать что значит каждый параметр
	'multiple' => bool
	'buttons' => array(
		'btn_name' => str || array(
			'url' => str,
			'text' => str,
			'inactive' => bool,
			'data' => array(),
			'class' => str,
			'attr' => str,
            'icon' => str, //название класса иконки class="action-icon icon-{$icon}"
			'list' => array(
				'item_name' => str || array(
					'url' => str,
					'text' => str,
					'class' => str,
					'attr' => str
				),
				'item_name' => ...
			),
			'buttons' => array(loop)
		)
	)
	'show' => num
*}

{if !empty($buttons)}
	{?$length = 0}
	{if empty($show)}{?$show = 999}{/if}
	{capture assign=actions_panel}	
		<div class="actions-panel-inner">
			{foreach from=$buttons key=name item=btn}
                {if (empty($btn.icon))}
					{?$icon = $name}
                {else}
                    {?$icon = $btn.icon}
                {/if}
				{if is_array($btn) && !empty($btn.buttons)}
					{?$length++}
					<div class="actions-group{if !empty($name)} group-{$name}{/if}{if $length > $show} hidden-action{/if}">
						{if !empty($name) || !empty($btn.text)}
							<div class="group-title">
								{if !empty($name)}
									<i class="group-icon icon-{$icon}"></i>
								{/if}
								{if !empty($btn.text)}
									<span class="group-text">{$name.text}<span>
								{/if}
							</div>
						{/if}
						{foreach from=$btn.buttons key=g_name item=g_btn}
							
							{* @TODO *}
							
						{/foreach}
					</div>
				{elseif !empty($btn)}
					{?$length++}
					{if is_array($btn)}
						{if empty($btn.text)}
							{if !empty($buttons_titles[$name])}
								{?$btn_text = $buttons_titles[$name]}
							{else}
								{?$btn_text = ''}
							{/if}
						{else}
							{?$btn_text = $btn.text}
						{/if}
					{/if}
					{if $name == 'more'}
						<div class="{if !empty($btn.list)}dropdown{/if}{if !empty($btn.inactive)} m-inactive{/if}{if $length > $show} hidden-action{/if}">
							<a href="{if !empty($btn.url)}{$btn.url}{else}#{/if}" 
							class="action-button action-{$name} dropdown-toggle{if !empty($btn.inactive)} m-inactive{/if}">
								<i class="action-icon icon-{$icon}"></i>
								<div class="action-text">{$btn_text}</div>
							</a>
							{if !empty($btn.list)}
								<ul class="dropdown-menu a-hidden">
									{foreach from=$btn.list key=$more_link_name item=$more_link}
										{if !is_array($more_link)}
											<li class="more-link more-link-{$more_link_name} action-{$more_link_name}">
												<a href="#">
													<i></i>
													<div>{if is_string($more_link)}{$more_link}{/if}</div>
												</a>
											</li>
										{else}	
											<li class="more-link more-link-{$more_link_name} action-{$more_link_name}{if !empty($more_link.class)} {$more_link.class}{/if}"
											{if !empty($more_link.attr)} {$more_link.attr}{/if}>
												<a href="{if !empty($more_link.url)}{$more_link.url}{else}#{/if}">
													<i></i>
													<div>{if !empty($more_link.text)}{$more_link.text}{/if}</div>
												</a>
											</li>		
										{/if}
									{/foreach}	
								</ul>
							{/if}
						</div>
					{else}
						{if !is_array($btn)}
							<a href="{if is_string($btn)}{$btn}{else}#{/if}" class="action-button action-{$name}{if $length > $show} hidden-action{/if}" title="{if !empty($buttons_titles[$name])}{$buttons_titles[$name]}{/if}">
								<i class="action-icon icon-{$icon}"></i>
								<div class="action-text">{if !empty($buttons_titles[$name])}{$buttons_titles[$name]}{/if}</div>
							</a>
						{else}
							{if !empty($btn.data)}
								{?$tag_data_string = ''}
								{foreach from=$btn.data key=tag_data_name value=tag_data_value}
									{?$tag_data_string = $tag_data_string . ' data-' . $tag_data_name . '=' . $tag_data_value}
								{/foreach}
							{/if}
							<a href="{if !empty($btn.url)}{$btn.url}{else}#{/if}"
							class="action-button action-{$name}{if !empty($btn.inactive)} m-inactive{/if}{if $length > $show} hidden-action{/if}{if !empty($btn.class)} {$btn.class}{/if}"
							title="{$btn_text}"
							{if !empty($tag_data_string)}{$tag_data_string}{/if} 
							{if !empty($btn.attr)} {$btn.attr}{/if}>
								<i class="action-icon icon-{$icon}"></i>
								<div class="action-text">{$btn_text}</div>
							</a>
						{/if}
					{/if}
				{/if}
			{/foreach}
			{if $length > $show}
				<div class="action-button action-expand" title="Развернуть">
					<i class="icon-expand"></i>
				</div>
			{/if}
		</div>
	{/capture}
	<div class="actions-panel a-clearbox{if !empty($multiple)} multiple{/if}{if $length > $show} expanded{/if}">
		{$actions_panel|html}
	</div>
{/if}