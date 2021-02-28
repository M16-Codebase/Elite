{*id, status = new|public|delete, text, date, timestamp, item_id, user_id, important*}
{*-> getUser(), getItem() *}

{?$user_role = $account->getUser()->getRole()}
<div class="h2">Комментарии</div>
{if $account->isPermission('catalog-view', 'addComment')}
	<div class="add-button add-comment">Новый комментарий</div>
{/if}
{if !empty($item_comments)}
	<ul class="comment-list">
		{foreach from=$item_comments item=com}
			<li class="comment-item">
				{?$com_user = $com->getUser()->getStaff()}
				<div class="col2 curator">
					{if !empty($com_user)}{* картинки может и не быть в любом случае (надо прописать условие)*}
						{?$staff_cover = $com_user['image']}
						{if !empty($staff_cover)}
							<div class="curator-cover">
								<img src="{$staff_cover->getUrl(60, 60, true)}" alt="" />
							</div>
						{else}
							<div class="nocurator"></div>
						{/if}
					{else}
						<div class="nocurator"></div>
					{/if}
					<div class="curator-descr">{* проблема в том, что $com_user может быть NULL, тогда подставлять email {$com->getUser()->getEmail()} ??? *}
						<div class="curator-name">
							{if !empty($com_user)}
								{if !empty($com_user.name)}{$com_user.name}{/if}{if !empty($com_user.surname)} {$com_user.surname}{/if}
							{else}
								{$com->getUser()->getEmail()}
							{/if}
						</div>
						<div class="curator-post">
							{if !empty($com_user)}
								{?$department = $com_user->getParent()}
								{if !empty($department)}
									{$department.name}
								{/if}
							{else}
								{?$com_role = $com->getUser()}
								{$com_role.role_title}
							{/if}
						</div>
						<div class="comment-date">
							{if $last_view_date <= $com.timestamp}<span class="comment-new">Новый <i>—</i></span>{/if}
							{include file="Admin/components/date.tpl" date=$com.timestamp}							
						</div>
					</div>
				</div>
				<div class="col3 comment">
					{if !empty($smarty.get.edit) && $account->isPermission('catalog-view', 'changeCommentStatus')}
						<div class="edit-comment">
							{if $com.status=='public' || $com.status=='new'}
								<div class="comment-edit-btn delete-comment" data-comment_id='{$com.id}' data-status='delete' title="Удалить" data-text="комментарий"></div>	
							{else}
								<div class="comment-edit-btn reestablish-comment" data-comment_id='{$com.id}' data-status='public' title="Восстановить" data-text="комментарий"></div>
							{/if}
						</div>
					{/if}
					<div class="priority{if !empty($smarty.get.edit) && $account->isPermission('catalog-view', 'changeCommentImportant')} change-priority{/if}">
						{if $com.important == 1}
							<div class="priority-status important" data-comment_id='{$com.id}' data-important='0'>Важно!</div>
						{elseif $com.important == 0 && !empty($smarty.get.edit) && $account->isPermission('catalog-view', 'changeCommentImportant')}
							<div class="priority-status no_important" data-comment_id='{$com.id}' data-important='1'>Без приоритета</div>
						{/if}
					</div>
					{if !empty($com.text)}	
						<div class="comment-text{if !empty($com.status)} {$com.status}{/if}{if $last_view_date <= $com.timestamp} new{/if}">{$com.text}</div>
					{/if}
				</div>
			</li>
		{/foreach}
	</ul>
{else}
	<div class="empty-result small-descr">Еще нет комментариев</div>
{/if}