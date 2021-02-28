{?$objectTitle = !empty($catalog_item.title) ? $catalog_item.title : "No title"}
{*{?$pageTitle = $objectTitle . ' — Управление сайтом'}*}
{?$pageTitle = $objectTitle . ' — ' . (!empty($confTitle) ? $confTitle : '')}
{?$currentCatalog = $current_type->getCatalog()}

{if $account->isPermission('catalog-item', 'edit')}
	<div class="item-view-type a-right">
		<a href="/catalog-item/edit/?id={$catalog_item.id}" class="item-edit"></a>
		<a href="/catalog-view/?id={$catalog_item.id}" class="item-view m-current"></a>
	</div>
{/if}
{?$site_link = $catalog_item->getUrl()}
<h1>{if !empty($catalog_item.title)}{$catalog_item.title}{else}No title{/if}</h1>


{if !empty($catalog_item)}
	{?$visibleClasses = array('any' => 'show', 'export' => 'upload', 'none' => 'hide')}
	{include file="Admin/components/actions_panel.tpl"
		buttons = array(
			'back' => (!empty($type_back_url)? $type_back_url : '/catalog-type/catalog/?id=' . $catalog_item.type_id),
			'visability' => array(
				'visible' => (($catalog_item.status != 2)? 'show' : 'hide'),
				'unchangeable' => 1
			),
			'type' => ($account->isPermission('catalog-type')? '/catalog-type/catalog/?id=' . $current_type.id : 0)
		)}
	<ul class="anchores a-inline-cont">
		<li><a href="#view-specs" class="scroll-to">Характеристики и спецификации</a></li>
		{if !empty($variants)}
			<li><a href="#view-variants" class="scroll-to">{$currentCatalog.word_cases['v']['2']['i']}</a></li>
		{/if}
		{if !empty($concurrent_items) || !empty($assoc_items)}
			<li><a href="#view-related" class="scroll-to">Аналоги и сопутствующие {$currentCatalog.word_cases['i']['2']['i']}</a></li>
		{/if}
        {?$catalog = $current_type->getCatalog()}
		{if $catalog['nested_in'] == 1 && empty($current_type['nested_in'])}
			<li><a href="#view-nested" class="scroll-to">Вложенные {$currentCatalog.word_cases['i']['2']['i']}</a></li>
		{/if}
	</ul>
	{?$item_images = !empty($catalog_item.gallery)? $catalog_item.gallery->getImages() : array()}
	{if count($item_images)}
		<div class="view-gallery blue-block item-view-block{if count($item_images) > 3} carousel{/if}" data-step="3">
			<div class="car-wrap">
				<ul>
					{foreach from=$item_images item=$img}
						<li>
							<a href="{$img->getUrl()}" class="fancybox" rel="gallery" title="{$img.text}">
								<span class="zoom">Увеличить</span>
								<img src="{$img->getUrl(218,195)}" alt="{$catalog_item.title}" />
							</a>
						</li>
					{/foreach}
					{if count($item_images) < 3}
						<li><div class="nophoto"></div></li>
						{if count($item_images) == 1}
							<li><div class="nophoto"></div></li>
						{/if}
					{/if}
				</ul>
			</div>
			{if count($item_images) > 3}	
				<div class="arrows">
					<div class="arrows-inner a-inline-block a-inline-cont">
						<div class="arrow-button car-prev m-inactive"></div>
						<div class="count">
							<span class="from">1</span>—<span class="to">3</span>
							из <span class="all">{count($item_images)}</span>
						</div>
						<div class="arrow-button car-next"></div>
					</div>
				</div>
			{/if}
		</div>
	{/if}
	<div class="view-specs item-view-block" id="view-specs">
		<h3>Характеристики и спецификации</h3>
		<table class="spec-table">	
			<tr>
				<td>ID</td>
				<td>{$catalog_item.id}</td>
			</tr>
			{?$prop_i = 0}
			{foreach from=$type_properties item=$item_prop name=item_props}
				{if isset($catalog_item['properties'][$item_prop.key]['real_value']) && $catalog_item['properties'][$item_prop.key]['real_value'] != ''}
					{?$prop_i++}
					<tr{if $prop_i%2!=0} class="even"{/if}>
						<td{if $item_prop.segment == 1} class="marker"{/if}>
							{$item_prop.title}
							{if !empty($item_prop.description)}
								&nbsp;{include file="Admin/components/tip.tpl" content=$item_prop.description}
							{/if}
						</td>
						<td>
							{if $item_prop.set == 0}
								{$catalog_item[$item_prop.key]}
							{else}
								{*implode(', ', $catalog_item[$item_prop.key])*}
								<ul>
									{foreach from=$catalog_item['properties'][$item_prop.key]['complete_value'] item=prop key=val_id}
										<li>
											{$prop}
										</li>
									{/foreach}
								</ul>
							{/if}
						</td>
					</tr>
				{/if}
			{/foreach}
		</table>
                
                {if !empty($item_files)}
                    <h3>Файлы</h3>
                    <table class="spec-table">	
                            {foreach from=$item_files item=$file}
                                <tr>
                                    <td>{$file.title}</td>
                                    <td><a href="{$file.link}">{$file.full_name}</a></td>
                                </tr>
                            {/foreach}
                    </table>
                {/if}
	</div>
	{if !empty($catalog_item.post) && (!empty($catalog_item.post.title) || !empty($catalog_item.post.annotation) || !empty($catalog_item.post.text))}
		<div class="view-descr item-view-block">
			{if !empty($catalog_item.post.title)}
				<h3>{$catalog_item.post.title}</h3>
			{/if}			
			{if !empty($catalog_item.post.annotation)}
				<p class="main">{$catalog_item.post.annotation}</p>
			{/if}			
			{if !empty($catalog_item.post.text)}
				<div class="edited-text">
					{$catalog_item.post.text|html}
				</div>
			{/if}
		</div>
	{/if}
	{if !empty($variants)}
		{?$var_reg_prices = array()}
		{?$var_reg_count = array()}
		<div class="view-variants item-view-block" id="view-variants">
			<h3>Варианты исполнения</h3>
			<ul class="variants-list">
				{?$prop_excepts = array('price'=>1, 'count'=>1, 'code'=>1, 'variant_code'=>1)}
				{foreach from=$variants item=$var name=item_vars}
					<li class="variant-item{if (empty($smarty.get.var) && first) || (!empty($smarty.get.var) && $smarty.get.var == $var.id)} m-open{/if}" data-id="{$var.id}">
						<div class="header edit-icon-handle">
							{?$visible_titles = array('any' => 'Отображается', 'none' => 'Не отображается', 'export' => 'Выгружается')}
							<div class="icon {if !empty($var.variant_visible)}{$var.variant_visible}{else}none{/if}" title="{if !empty($var.variant_visible)}{$visible_titles[$var.variant_visible]}{else}Скрыт{/if}"></div>
							<div class="n-num">{*$var.code*}</div>
							{if !empty($var.variant_title)} {$var.variant_title}{else} No title{/if}
							{if $account->isPermission('catalog-item', 'edit')}
								<a href="/catalog-item/edit/?id={$catalog_item.id}&tab=variants&v={$var.id}" class="edit-icon dark" title="Редактировать"></a>
							{/if}
						</div>
						<div class="body">
							<table class="spec-table">
								<tr>
									<td>Номенклатурный номер</td>
									<td>{*$var.code*}</td>
								</tr>
								{foreach from=$variant_properties item=$var_prop name=var_props}
									{if isset($var[$var_prop.key]) && empty($prop_excepts[$var_prop.key])}
										<tr>
											<td{if $var_prop.segment == 1} class="marker"{/if}>
												{$var_prop.title}
												{if !empty($var_prop.description)}
													&nbsp;{include file="Admin/components/tip.tpl" content=$var_prop.description}
												{/if}
											</td>
											<td class="prop-{$var_prop.key}">
												{if $var_prop.set == 0}
													{$var[$var_prop.key]}
												{else}
													{implode(', ', $var[$var_prop.key])}
												{/if}
											</td>
										</tr>
									{/if}
								{/foreach}
								<tr>
									<td class="marker">Цена</td>
									<td><span class="price-table">
										{*if !empty($var['price_variant'])}
											<span class="a-link-dotted">{$var['price_variant']}</span>
										{else}
											<span class="a-link">•••</span>
										{/if*}										
									</span></td>
								</tr>
								<tr>
									<td class="marker">Наличие </td>
									<td><span class="avail-table">
										{if !empty($var['count'])}
											<span class="a-link-dotted">Есть</span>
										{else}
											<span class="a-link">•••</span>
										{/if}
									</span></td>
								</tr>
								
								{?$var_reg_prices[$var.id] = array()}
								{?$pr = $var->getPropertyBySegments('price_variant')}
								{foreach from=$pr item=region_pr key=reg_id}
									{?$var_reg_prices[$var.id][$reg_id] = $region_pr.real_value}
								{/foreach}
								
								{if !empty($catalog_item_files) && !empty($catalog_item_files[$var.id])}
									<tr>
										<td colspan="2">
											<div class="view-docs item-view-block" id="view-docs">
												<ul class="items-list files-list clearbox">
													{foreach from=$catalog_item_files[$var.id] item=file}
														{if !empty($file.type)}
															<li>
																<a href="{$file.link}" class="cover" target="_blank"></a>
																<div class="title"><a href="{$file.link}" target="_blank">
																	{if !empty($file_types[$file.type])}{$file_types[$file.type]}{else}{$file.full_name}{/if}
																</a></div>
																<div class="descr">{$file.ext} — {$file.full_size}</div>
															</li>
														{/if}
													{/foreach}	
												</ul>
											</div>
										</td>
									</tr>
								{/if}
								
							</table>
						</div>
					</li>
				{/foreach}	
			</ul>
		</div>
	{/if}
	
	<div class="view-row item-view-block" id="view-related">
		{if !empty($concurrent_items)}
			<div class="view-analog view-col">
				<h3>Аналоги</h3>
				<ul class="items-list">
					{foreach from=$concurrent_items item=$c_item}
						<li class="edit-icon-handle">
							<div class="title">
								<a href="/catalog-view/?id={$c_item.id}">{if !empty($c_item.title)}{$c_item.title}{else}No title{/if}</a>
								{if $account->isPermission('catalog-item', 'edit')}
									<a href="/catalog-item/edit/?id={$c_item.id}" class="edit-icon dark" title="Редактировать"></a>
								{/if}
							</div>
						</li>
					{/foreach}
				</ul>
			</div>
		{/if}
		{if !empty($assoc_items)}
			<div class="view-related view-col">
				<h3>Сопутствующие {$currentCatalog.word_cases['i']['2']['i']}</h3>
				<ul class="items-list">
					{foreach from=$assoc_items item=$a_item}
						<li class="edit-icon-handle">
							<div class="title">
								<a href="/catalog-view/?id={$a_item.id}">{if !empty($a_item.title)}{$a_item.title}{else}No title{/if}</a>
								{if $account->isPermission('catalog-item', 'edit')}
									<a href="/catalog-item/edit/?id={$a_item.id}" class="edit-icon dark" title="Редактировать"></a>
								{/if}
							</div>
						</li>
					{/foreach}
				</ul>
			</div>
		{/if}	
	</div>
	<div class="view-row item-view-block" id="view-nested">
		{if !empty($nested_items)}
            {foreach from=$nested_items key=nt_id item=items}
                <div class="view-col">
                    <h3>{$nested_types[$nt_id]['title']}</h3>
                    <ul class="items-list">
                        {foreach from=$items item=$n_item}
                            <li class="edit-icon-handle">
                                <div class="title">
                                    <a href="/catalog-view/?id={$n_item.id}">{if !empty($n_item.title)}{$n_item.title}{else}No title{/if}</a>
                                    {if $account->isPermission('catalog-item', 'edit')}
                                        <a href="/catalog-item/edit/?id={$n_item.id}" class="edit-icon dark" title="Редактировать"></a>
                                    {/if}
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/foreach}
		{/if}
	</div>
{/if}

{*include file="Admin/popups/viewer_price.tpl"}
{include file="Admin/popups/viewer_count.tpl"*}