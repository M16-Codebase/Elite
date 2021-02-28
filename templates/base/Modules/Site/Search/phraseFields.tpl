<form class="edit-teaser" enctype="multipart/form-data" action="/site-search/savePhrase/" class="create-phrase-form">
	<input type="hidden" name="id" />
	<div class="content-top">
		<h1>{if !empty($smarty.post.id)}Редактирование {else}Добавление {/if}поисковой фразы</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'url' => '#',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = createTeaserHandlers
				buttons = $buttons}	
			{$createTeaserHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3">Фраза</div>
                <div class="w9">
                    <input type="hidden" name="id" />
                    <input type="text" name="phrase" />
                </div>
			</div>
			<div class="wblock white-block-row">
                <div class="w3">URL</div>
                <div class="w9">
                    <input type="text" name="url" />
                </div>
            </div>
		</div>
	</div>
</form>
{*<form enctype="multipart/form-data" action="/site-search/savePhrase/" class="create-phrase-form">
    <div class="content-scroll">
        <div class="white-blocks viewport">
            <div class="wblock white-block-row">
                <div class="w3">Фраза:</div>
                <div class="w9">
                    <input type="hidden" name="id" />
                    <input type="text" name="phrase" />
                </div>
            </div>
            <div class="wblock white-block-row">
                <div class="w3">Url:</div>
                <div class="w9">
                    <input type="text" name="url" />
                </div>
            </div>
        </div>
    </div>
</form>*}