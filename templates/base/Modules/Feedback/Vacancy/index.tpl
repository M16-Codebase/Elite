{?$pageTitle = 'База откликов соискателей — ' . (!empty($confTitle) ? $confTitle : '')}
{?$admin_page = 1}
{capture assign=aside_filter name=aside_filter}
	<section class="aside-filter">
		<form class="user-form items-filter" method="GET" action="/hr-feedback/logsList/">
			<input type="hidden" name="order[date]" class="input-sort" />
			<div class="field">
				<div class="f-title">Номер обращения</div>
				<div class="f-input">
					<input type="text" name="number" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Имя</div>
				<div class="f-input">
					<input type="text" name="author" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Телефон</div>
				<div class="f-input">
					<input type="text" name="phone" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">E-mail</div>
				<div class="f-input">
					<input type="text" name="email" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Тип обращения</div>
				<div class="f-input">
					<select name="type">
						<option value="">Выберите...</option>
						{foreach from=$types key=k item=t}
							<option value="{$k}">{$t}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="field">
				<div class="f-title">Дата</div>
				<div class="f-input between">
					<input type="text" name="date_start" mask="99.99.9999" class="datepicker a-left">
					<input type="text" name="date_end" mask="99.99.9999" class="datepicker a-right">
				—
				</div>
			</div>
			<div class="buttons">
				<button class="submit btn btn-main a-block">Показать</button>
				<div class="link-cont">
					<span class="clear-form a-link small-descr">Сбросить фильтр</span>
				</div>
			</div>
		</form>
	</section>
{/capture}

<div class="content-top">
	<h1>База откликов соискателей</h1>
</div>
<div class="content-scroll">
	<div class="viewport">
		<div class="handling-base overview">
			{include file="Modules/Feedback/Main/logsList.tpl"}
		</div>
	</div>
</div>

{capture assign=editBlock name=editBlock}
	<div class="content-top">
		<h1>Форма обращения</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена')
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = back
				buttons = $buttons}
			{$back|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="viewport">
			<div class="overview">
				{include assign=feedbackForm file="Admin/popups/feedback_form.tpl"}
				{$feedbackForm|html}
			</div>
		</div>
	</div>
{/capture}	