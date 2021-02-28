<div class="white-blocks">
    <input type="hidden" name="last_update"{if !empty($post.last_update)} value="{$post.last_update}"{/if} />
    {if !empty($create_post_hash)}
        <input type="hidden" name="post_creation_hash" value="{$create_post_hash}" data-value="{$create_post_hash}" id="post_creation_hash" />
    {/if}
    {if empty($no_id) && !empty($post)}
        <input type="hidden" name="id" value="{$post.id}" id="post_id" />
    {/if}
    {if !empty($post)}
        <input type="hidden" name="type" value="{$post.type}">
    {/if}
    {if !empty($no_segment)}
        <input type="hidden" name="segment_id" value="{if !empty($post)}{$post.segment_id}{/if}" />
    {/if}
    {if !empty($smarty.get.item_id)}
        <input type="hidden" name="item_id" value="{$smarty.get.item_id}"/>
    {/if}
	{if !empty($themes)}
        <input type="hidden" name="theme_id" />
    {/if}
    {if !empty($no_title)}
        <input type="hidden" name="title" />
    {/if}

    {if isset($save_error_field)}
        <div class="error"><b>Ошибка: </b>
            {if $save_error_field=='title'}Слишком короткий заголовок
            {elseif $save_error_field=='text'}Мало текста (менее 10 символов)
            {else}Неправильно заполнено поле "{$save_error_field}"
            {/if}
        </div>
    {/if}
    {if empty($no_title)}
        <div class="wblock white-block-row">
            <label class="w3" for="text_id">
                <strong>Заголовок</strong>
            </label>
            <div class="w9">
                <input type="text" name="title" id="text_id" />
            </div>
        </div>
    {/if}
    {if $accountType == 'SuperAdmin'}
        <div class="wblock white-block-row">
            <label class="w3" for="text_key">
                <strong>Ключ</strong>
            </label>
            <div class="w9">
                <input type="text" name="key" id="text_key" />
            </div>
        </div>
    {/if}
    {if empty($no_status)}
        <div class="wblock white-block-row">
            <label class="w3" for="f_status">
                <strong>Статус</strong>
            </label>
			<div class="w4 dropdown post-status m-status">
				<input type="hidden" name='status' {if empty($post)}value="new"{/if}>
				<div class="dropdown-toggle action-button m-status-icon" title="{if $post.status=="close" || $post.status=="public"}Опубликован{elseif $post.status=="new"}Черновик{elseif $post.status=="hidden"}Скрыт{else}Черновик{/if}">
					<i class="icon-{if $post.status=="close" || $post.status=="public"}show{elseif $post.status=="new"}draft{elseif $post.status=="hidden"}hide{else}draft{/if}"></i>
					<span>{if $post.status=="close"}{if $moduleUrl == "blog-admin"}Опубликован, комментарии закрыты{else}Опубликован{/if}{elseif $post.status=="public"}Опубликован{elseif $post.status=="new"}Черновик{elseif $post.status=="hidden"}Скрыт{else}Черновик{/if}</span>
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
    {if !empty($themes)}
        <div class="wblock white-block-row">
            <label class="w3" for="f_themes">
                <strong>Тема</strong>
            </label>
            <div class="w9">
                <select name="theme_id" id="f_themes">
                    {foreach from=$themes item="theme" key="t"}
                        {*{if !empty($theme.parent_id)}*}
                            <option value="{$theme.id}">{$theme.title}</option>
                        {*{/if}*}
                    {/foreach}
                </select>
            </div>
        </div>
    {/if}
    {if $constants.segment_mode != 'none' && empty($no_segment)}
        <div class="wblock white-block-row">
            <label class="w3" for="f_segment">
                <strong>Сегмент</strong>
            </label>
            <div class="w9">
                <select name="segment_id" id="f_segment">
                    {if empty($used_segments) || empty($post)}
                        {if $constants.segment_mode != 'lang'}<option value="">Для всех</option>{/if}
                        {foreach from=$segments item=s}
                            <option value="{$s.id}">Только {$s.title}</option>
                        {/foreach}
                    {else}
                        {if (!in_array(NULL, $used_segments) || empty($post.segment_id)) && $constants.segment_mode != 'lang'}<option value="">Для всех</option>{/if}
                        {foreach from=$segments item=s}
                            {if !in_array($s.id, $used_segments) || $s.id == $post.segment_id}
                                <option value="{$s.id}">Только {$s.title}</option>
                            {/if}
                        {/foreach}
                    {/if}
                </select>
            </div>
        </div>
    {/if}
    {if !empty($else_fields)}
        {$else_fields|html}
    {/if}
    {if !empty($allow_change_date) || $moduleUrl == 'news-admin'|| $moduleUrl == 'blog-admin'}
        <div class="wblock white-block-row">
            <div class="w3">
                <strong>Размещено</strong>
            </div>
            <div class="w9">
                <input type="text" name="date" class="datepicker short" />&nbsp;&nbsp;<span>время:&nbsp;</span><input type="text" name="time" class="short mask" data-mask="99:99:99" />
            </div>
        </div>
    {/if}
    {if $moduleUrl == 'blog-admin'}
        <div class="wblock white-block-row">
            <div class="w3">
                <strong class="inline">Тэги</strong>
            </div>
            <div class="w9">
				<textarea name="tags" rows="3" class="tag-edit"></textarea>
            </div>
        </div>
    {/if}
    {if false && $moduleUrl == 'article-admin'}
        <div class="wblock white-block-row">
            <div class="w3">
                <strong>id привязанных товаров через запятую</strong>
            </div>
            <div class="w9">
                <ul class="num-area">
                    {if !empty($items)}
                        {foreach from=$items item=catalog_item}
                            <li class="tagit-choice" title="{$catalog_item.title}" tagvalue="{$catalog_item.id}">{$catalog_item.id}</li>
                        {/foreach}
                    {/if}
                </ul>
                <input type="hidden" name="items" />
            </div>
        </div>
    {/if}
    {if !empty($authors)}
        <div class="wblock white-block-row">
            <label class="w3" for="author_id_select">Опубликовать от имени</label>
            <div class="w9">
                <select id="author_id_select" name="staff_id">
                    <option value=""></option>
                    {foreach from=$authors item=author}
                        <option value="{$author.id}">{$author.surname} {$author.name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    {elseif !empty($need_author) || $moduleUrl == 'blog-admin'}
        <div class="wblock white-block-row">
            <label class="w3" for="author_name"><strong>Имя автора</strong></label>
            <div class="w9">
                <input type="text" name="author" id="author_name" />
            </div>
        </div>
        <div class="wblock white-block-row">
            <label class="w3" for="author_email"><strong>E-mail автора</strong></label>
            <div class="w9">
                <input type="text" name="email" id="author_email" />
            </div>
        </div>
    {/if}
    {if empty($no_annotation)}
        <div class="wblock white-block-row">
            <label class="w3" for="text_annotation">
                <strong>{if !empty($field_label)}{$field_label}{else}Анонс{/if}</strong>
            </label>
            <div class="w9">
                <textarea name="annotation" id="text_annotation" rows="3"></textarea>
            </div>
        </div>
    {/if}
	{if !empty($post) && $post.full_version == '0'}
		<div class="wblock white-block-row">
			<div class="w12">
				<textarea name="text" rows="5"></textarea>
			</div>
		</div>
	{else}
		<div class="wblock post-block">
			<textarea name="text" class="{if $action == 'edit'}redactor{else}redactor-init{/if}"></textarea>
		</div>
	{/if}
</div>