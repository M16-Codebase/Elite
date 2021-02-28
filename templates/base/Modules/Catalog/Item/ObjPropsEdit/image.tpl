<div class="content-top">
	<h1>{if !empty($object)}Редактирование{else}Создание{/if} изображения</h1>
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
	<div class="white-blocks viewport">
		<input type="hidden" name="object_id" />
		<input type="hidden" name="entity_id" />
		<input type="hidden" name="segment_id" />
		<input type="hidden" name="property_id" />

		<div class="wblock white-block-row">
			<div class="prop-item w12 img-preview">
				<a href="" class="fancybox row-image origin">
					<div class="preloader"><div></div></div>
					<img src="">
				</a>
				<div class="prop-title h4">Изображение</div>
				<div class="row{if empty($object)} a-hidden{/if}">
					<div class="w12 img-preview-body">
						{if !empty($object)}
						<a href="{$object->getUrl()}" class="fancybox row-image">
							<img src="{$object->getUrl(70, 70, true)}">
						</a>
						{/if}
					</div>
				</div>
				<div class="add-row row m-fullwidth">
					<label for="change-image" class="change-img">
					<div class="add-object add-btn w3">
						<i class="icon-{if !empty($object)}replace{else}add{/if}"></i> <span class="small-descr">{if !empty($object)}Заменить{else}Добавить{/if} изображение</span>
					</div>
					</label>
					<input type="file" name="image" class="hidden-input" id='change-image' accept="image/*"/>
				</div>
			</div>
		</div>
		{if $constants.segment_mode == 'lang'}
			{foreach from=$segments item=s}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>Заголовок ({$s.title})</strong>
					</div>
					<div class="w9">
						<input type="text" name="title[{$s.id}]"{if !empty($object.info.title[$s.id])} value="{$object.info.title[$s.id]}"{/if} />
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>Описание ({$s.title})</strong>
					</div>
					<div class="w9">
						<textarea name="text[{$s.id}]">{if !empty($object.info.text[$s.id])}{$object.info.text[$s.id]}{/if}</textarea>
					</div>
				</div>
			{/foreach}
		{else}
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Заголовок</strong>
				</div>
				<div class="w9">
					<input type="text" name="title" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Описание</strong>
				</div>
				<div class="w9">
					<textarea name="text"></textarea>
				</div>
			</div>
		{/if}
	</div>
</div>