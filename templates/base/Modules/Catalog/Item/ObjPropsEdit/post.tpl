{if $property.segment == 1}
	{if empty($smarty.get.second_segment)}
		<div class="tabs-cont">
			<div class="content-top">
				<h1>{if !empty($create)}Создание{else}Редактирование{/if} статьи{if !empty($property.title)} для свойства «{$property.title}»{/if}</h1>
				<div class="content-options">
					{include file="Admin/components/actions_panel.tpl"
						buttons = array(
							'back' => '#'
						)}
					{?$segment_tabs = array()}
					{foreach from=$segments item=s}
						{?$segment_tabs['segment-page-' . $s.key] = array(
							'current' => ($s.id == $segment_id),
							'text' => $s.title
						)}
					{/foreach}
					{include file="Admin/components/tabs.tpl" tabs=$segment_tabs}
				</div>
			</div>
			<div class="tabs-pages content-scroll-cont">
	{/if}
				<div id="segment-page-{$segments[$segment_id].key}" class="tab-page segment-{$segment_id}{if !empty($smarty.get.second_segment)} a-hidden{/if}">
					<div class="content-scroll">
						<div class="aside-panel">
							{include file="Admin/components/actions_panel.tpl"
								buttons = array(
									'save' => array(
										'class' => 'submit'
									)
								)}
						</div>
						<div class="viewport">
							<input type="hidden" name="object_id" />
							<input type="hidden" name="entity_id" />
							<input type="hidden" name="segment_id" />
							<input type="hidden" name="property_id" />
							<div class="white-blocks">
								{if !empty($property.values.show_title)}
									<div class="wblock white-block-row">
										<div class="w3">
											<strong>Заголовок</strong>
										</div>
										<div class="w9">
											<input type="text" name="title" />
										</div>
									</div>
								{/if}
								{if !empty($property.values.show_annotation)}
									<div class="wblock white-block-row">
										<div class="w3">
											<strong>Аннотация</strong>
										</div>
										<div class="w9">
											<textarea name="annotation" rows="5"></textarea>
										</div>
									</div>
								{/if}
								{if !empty($property.values.show_status)}
									<div class="wblock white-block-row">
										<label class="w3" for="f_status">
											<strong>Статус</strong>
										</label>
										<div class="w4 dropdown post-status m-status">
											<input type="hidden" name='status' {if empty($object)}value="new"{/if}>
											<div class="dropdown-toggle action-button m-status-icon" title="{if $object.status=="close" || $object.status=="public"}Опубликован{elseif $object.status=="new"}Черновик{elseif $object.status=="hidden"}Скрыт{else}Черновик{/if}">
												<i class="icon-{if $object.status=="close" || $object.status=="public"}show{elseif $object.status=="new"}draft{elseif $object.status=="hidden"}hide{else}draft{/if}"></i>
												<span>{if $object.status=="close"}Опубликован{elseif $object.status=="public"}Опубликован{elseif $object.status=="new"}Черновик{elseif $object.status=="hidden"}Скрыт{else}Черновик{/if}</span>
											</div>
											<ul class='dropdown-menu a-hidden'>
												{foreach from=$status_list item="v" key="k"}
													{if $k != 'delete'}
														<li data-type="{$k}"><span>{$v}</span></li>
													{/if}
												{/foreach}
											</ul>
										</div>
										<div class="w5"></div>
									</div>
								{/if}
								<div class="wblock post-block">
									<textarea name="text" class="redactor-init"></textarea>
								</div>
							</div>
							{if !empty($object) && !empty($property.values.allow_images)}
								{include file="Modules/Posts/Admin/post_uploader.tpl" post=$object}
							{/if}
						</div>
					</div>
				</div>
	{if empty($smarty.get.second_segment)}			
			</div>
		</div>
	{/if}
{else}
	
	<div class="content-top">
		<h1>{if !empty($create)}Создание{else}Редактирование{/if} статьи</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl"
				buttons = array(
					'back' => '#',
					'save' => array(
						'class' => 'submit'
					)
				)}
		</div>
	</div>

	<div class="content-scroll">
		<div class="viewport">
			<input type="hidden" name="object_id" />
			<input type="hidden" name="entity_id" />
			<input type="hidden" name="segment_id" />
			<input type="hidden" name="property_id" />
			<div class="white-blocks">
				{if !empty($property.values.show_title)}
					<div class="wblock white-block-row">
						<div class="w3">
							<strong>Заголовок</strong>
						</div>
						<div class="w9">
							<input type="text" name="title" />
						</div>
					</div>
				{/if}
				{if !empty($property.values.show_annotation)}
					<div class="wblock white-block-row">
						<div class="w3">
							<strong>Аннотация</strong>
						</div>
						<div class="w9">
							<textarea name="annotation" rows="5"></textarea>
						</div>
					</div>
				{/if}
				{if !empty($property.values.show_status)}
					<div class="wblock white-block-row">
						<label class="w3" for="f_status">
							<strong>Статус</strong>
						</label>
						<div class="w4 dropdown post-status m-status">
							<input type="hidden" name='status' {if empty($object)}value="new"{/if}>
							<div class="dropdown-toggle action-button m-status-icon" title="{if $object.status=="close" || $object.status=="public"}Опубликован{elseif $object.status=="new"}Черновик{elseif $object.status=="hidden"}Скрыт{else}Черновик{/if}">
								<i class="icon-{if $object.status=="close" || $object.status=="public"}show{elseif $object.status=="new"}draft{elseif $object.status=="hidden"}hide{else}draft{/if}"></i>
								<span>{if $object.status=="close"}Опубликован{elseif $object.status=="public"}Опубликован{elseif $object.status=="new"}Черновик{elseif $object.status=="hidden"}Скрыт{else}Черновик{/if}</span>
							</div>
							<ul class='dropdown-menu a-hidden'>
								{foreach from=$status_list item="v" key="k"}
									{if $k != 'delete'}
										<li data-type="{$k}"><span>{$v}</span></li>
									{/if}
								{/foreach}
							</ul>
						</div>
						<div class="w5"></div>
					</div>
				{/if}
				<div class="wblock post-block">
					<textarea name="text" class="redactor-init"></textarea>
				</div>
			</div>
			{if !empty($object) && !empty($property.values.allow_images)}
				{include file="Modules/Posts/Admin/post_uploader.tpl" post=$object}
			{/if}
		</div>
	</div>
	
{/if}