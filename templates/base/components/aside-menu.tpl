<ul class="aside-catalog-types">	
	{foreach from=$types_left_menu key=parent_id item=data}
		<li class="vm-item{if !empty($current_type) && $data.info.id == $current_type.id} m-current{/if}{if !empty($data.types)} has-menu{/if}">
			<div class="type-icon">
				{if !empty($data.info.cover)}
					<img src="{$data.info.cover->getUrl(28,26,90,null,true)}" alt="{$data.info.title}"/>
				{/if}
			</div>
			<a href="{$data.info->getUrl()}" class="vm-toggle">
				{$data.info.title}
				{if !empty($data.types)}
					<i class="arrow"></i>
				{/if}
			</a>
			{if !empty($data.types)}
				<div class="vm-menu a-hidden">
					<div class="vm-menu-inner">
						<a href="{$data.info->getUrl()}" class="type-cover">
							{if !empty($data.info.cover)}
								<img src="{$data.info.cover->getUrl(160,140,90,null,true)}" alt="{$data.info.title}"/>
							{/if}
							<span>{$data.info.title}</span>
						</a>
						{?$first_col = true}
						<ul class="child-types-list">
							{foreach from=$data.types key=type_id item=type name=aside_types}
								<li>
									<a href="{$type->getUrl()}">
										<div class="type-icon">
											{if !empty($type.cover)}
												<img src="{$type.cover->getUrl(28,26,90,null,true)}" alt="{$type.title}"/>
											{/if}
										</div>
										{$type.title}
									</a>
								</li>
								{if $first_col && (iteration > 1) && (iteration >= count($data.types)/2) && !last}
									{?$first_col = false}
									</ul>
									<ul class="child-types-list">
								{/if}
							{/foreach}
						</ul>
					</div>
				</div>				
			{/if}
		</li>
	{/foreach}	
</ul>