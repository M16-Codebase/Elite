{if empty($menu_list) && !empty($product_menu_list)}
    {?$menu_list = $product_menu_list}
{/if}
<nav class="breadcrumb" itemprop="breadcrumb">
	<ul class="descr a-inline-cont">
        <li><a href="/">Мастер Сантехник</a></li>
		{if !empty($current_type)}
			{?$current_type_id = $current_type.id}
		{elseif !empty($catalog_item)}
			{?$current_type_id = $catalog_item.type_id}
		{elseif !empty($property) && isset($property.type_id)}
			{?$current_type_id = $property.type_id}
		{/if}
		{if isset($current_type_id)}
			{if !empty($menu_list)}
				{foreach from=$menu_list key=parent_id item=rubric}
					{if $parent_id > 1}
						<li{if !empty($rubric['children'])} class="dropdown hoverable"{/if}>							
							<a class="dropdown-toggle{if !empty($rubric['children'])} dd-arrow{/if}" href="{$main_types[$parent_id]->getUrl()}">{$rubric['title']}</a>							
							{if !empty($rubric['children'])}
								<ul class="dropdown-menu a-hidden">
									{foreach from=$rubric['children'] item=child key=id}
										<li>
											{if !($child.allow_children && !$account->isPermission('catalog-type'))}
												<a href="{$child->getUrl()}">
													<i class="a-inline-block"></i>{$child.title}
													{if isset($child.items_count)} 
														<span class="bc-children-count">({$child.items_count})</span>
													{/if}
												</a>
											{else}
												<span>
													<i class="a-inline-block"></i>{$child.title}
													{if isset($child.items_count)} 
														<span class="bc-children-count">({$child.items_count})</span>
													{/if}
												</span>
											{/if}
										</li>
									{/foreach}
								</ul>
							{/if}
						</li>
					{/if}
				{/foreach}
			{/if}
			{if !empty($current_type)}
				<li class="m-current">
					<a href="{$current_type->getUrl()}">{$current_type.title}</a>
				</li>
			{/if}  
			
		{/if}
		{if !empty($other_link)}
			{foreach from=$other_link item=bc_link key=bc_title}
				{if is_string($bc_link) || empty($bc_link)}
					<li class="m-current">
						<a href="{$bc_link}">{$bc_title}</a>
					</li>
				{else}
					<li class="dropdown hoverable">							
						<a class="dropdown-toggle dd-arrow" href="{if !empty($bc_link.link)}{$bc_link.link}{else}#{/if}">{$bc_title}</a>	
						<ul class="dropdown-menu a-hidden">
							{foreach from=$bc_link item=bc_dd_link key=bc_dd_title}
								{if $bc_dd_title != 'link'}
									<li>
										<a href="{$bc_dd_link}">
											<i class="a-inline-block"></i>{$bc_dd_title}
										</a>
									</li>
								{/if}
							{/foreach}
						</ul>
					</li>	
				{/if}
			{/foreach}
		{/if}
	</ul>
</nav>