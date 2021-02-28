<form action="/seo/edit/?id={$item.id}" class="edit-meta">
{*		<input type="hidden" name="id" value="{$item.id}" />*}
	<div class="content-top">
		<h1>Редактирование META-тегов «{$item.page_uid}»</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl"
				buttons = array(
					'back' => '/seo/',
					'save' => '#',
					'site' => $item.page_uid
				)}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3">
					UID:
				</div>
				<div class="w9">
					<input type="text" name="page_uid" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Title:
				</div>
				<div class="w9">
					<input type="text" name="title" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Description:
				</div>
				<div class="w9">
					<input type="text" name="description" />
				</div>	
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Keywords:
				</div>
				<div class="w9">
					<input type="text" name="keywords" />
				</div>	
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Canonical:
				</div>
				<div class="w9">
					<input type="text" name="canonical" />
				</div>
			</div>
			<div class="wblock post-block">
				<textarea class="redactor" name="text"></textarea>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Состояние:
				</div>
				<div class="w9">
					<select name="enabled">
						<option value="1">Вкл.</option>
						<option value="0">Выкл.</option>
					</select>
				</div>	
			</div>	
		</div>
	</div>	
</form>
