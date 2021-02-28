<li class="wblock single-syn m-enabled">
    <form data-speed="400" class="slidebox edit-syn-form single-syn-form" action="/sphinx-wordforms/edit/" method="POST">
        <div class="read-mode white-block-row">
            <span class="main-word w4">{$wordform.dst_form}</span>
            <span class="syn-list w6">{implode(', ', $wordform.src_form)}</span>
			<a href="#" class="action-button action-edit slide-header w1">
				<i></i>
			</a>
			<a href="#" class="action-button action-delete  w1 m-border">
				<i></i>
			</a>
        </div>
        <div class="edit-mode slide-body a-hidden">
            <input type="hidden" name="old_dst_form" value="{$wordform.dst_form}">
            <div class="syn-editor">
				<input type="text" class="change-main-word" name="dst_form" value="{$wordform.dst_form}">				
                <div class="editor-descr">
                    Синонимы через запятую
                </div>
                <ul class="tags-cont syns-area">
                    {foreach from=$wordform.src_form item=src_form}
                        <li class="tagit-choice" title="{$src_form}" tagvalue="{$src_form}">{$src_form}</li>
                    {/foreach}
                </ul>
				<ul class="errors">

				</ul>
                <div class="form-btns">
                    <input type="submit" class="submit-tags a-button-blue" value="Сохранить">
					<span data-toggle="slidebox:close" class="cancel-tags">
						Отмена
					</span>
                </div>
            </div>	
        </div>
    </form>
</li>