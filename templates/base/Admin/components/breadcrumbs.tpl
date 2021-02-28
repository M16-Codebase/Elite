{if $moduleUrl == 'catalog-view' || $moduleUrl == 'catalog-type' || $moduleUrl == 'catalog-item'}
	{if !empty($current_type)}
		{?$current_type_id = $current_type.id}
	{elseif !empty($catalog_item)}
		{?$current_type_id = $catalog_item.type_id}
	{elseif !empty($property) && isset($property.type_id)}
		{?$current_type_id = $property.type_id}
	{/if}
    {if empty($menu_list) && isset($current_type_id)}
		{?$menu_list = $infoBlocks->get('path', array('type_id' => $current_type_id, 'admin_page' => 1), $request_segment['id'])}
	{/if}
	{capture assign="breadcrumbs"}
		{if isset($current_type_id)}
			{if !empty($menu_list)}
				{foreach from=$menu_list key=parent_id item=rubric}
                    <li class="bc-item{if !empty($rubric['children'])} bc-children-contains dropdown hoverable{/if}">
                        {if $account->isPermission('catalog-type') && 
								($accountType == 'SuperAdmin' || 
								($constants.default_type_id != $parent_id && $rubric['entity'].key != 'config' && $rubric['entity'].key != 'reviews_question'))}
                            <a href="/catalog-type/{if !empty($rubric['entity']['parent_id'])}catalog/{/if}?id={$parent_id}" class="dropdown-toggle small-descr">{$rubric['title']}</a>
                        {else}
                            <span class="dropdown-toggle small-descr">{$rubric['title']}</span>
                        {/if}
                        {if !empty($rubric['children'])}
                            <ul class="bc-menu dropdown-menu a-hidden">
                                {foreach from=$rubric['children'] item=child key=id}
                                    <li class="bc-menu-item">
                                        {if !($child.allow_children && !$account->isPermission('catalog-type'))}
                                            <a href="/catalog-type/catalog/?id={$id}">
                                                {$child.title}
                                                {if isset($child.items_count)} 
                                                    <span class="bc-count small-descr">({$child.items_count})</span>
                                                {/if}
                                            </a>
                                        {else}
                                            <span>
                                                {$child.title}
                                                {if isset($child.items_count)} 
                                                    <span class="bc-count small-descr">({$child.items_count})</span>
                                                {/if}
                                            </span>
                                        {/if}
                                    </li>
                                {/foreach}
                            </ul>
                        {/if}
                    </li>
				{/foreach}	
			{/if}
			{if (!empty($catalog_item) || !empty($property))}
				<li class="bc-item">
					{if !($current_type.allow_children && !$account->isPermission('catalog-type'))}
						<a href="/catalog-type/catalog/?id={$current_type_id}" class="small-descr">
							{$current_type.title}
						</a>
					{else}
						<span class="small-descr">{$current_type.title}</span>
					{/if}
				</li>
			{/if}
		{/if}
	{/capture}

	{?$breadcrumbs = $breadcrumbs|trim}

	{if !empty($breadcrumbs)}
		<nav class="breadcrumbs">
			<ul>				
				{$breadcrumbs|html}
			</ul>
		</nav>
	{/if}
{/if}