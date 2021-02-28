{?$checkString = time()}
{?$checkStringSalt = $checkString . $hash_salt_string}
<div class="contacts-page content-block">
	<div class="page-title">
		{include file="components/breadcrumb.tpl" other_link=array(($ru)? 'Контактная информация' : 'Contact Information' => $url_prefix . '/main/contacts/')}
		<h1>{$lang->get('Заявка на аренду/продажу имеющейся недвижимости', 'Application for rent/sale of the owned property')|html}</h1>
	</div>
	<div class="order-block">
		<div class="fields-block feedback-block">
			<div class="order-block-title">
				{$lang->get('Вы владеете коммерческой недвижимостью и желаете сдать ее в аренду или продать? Заполните форму и мы поможем вам!', 'Are you a landlord and want to rent it or sell it? Fill out the form and we will help you!')|html}
			</div>
			<form enctype="multipart/form-data" action="/feedback/makeRequest/" method="post" class="feedback-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
				<input type="hidden" name="check_string" value="">
				<input type="hidden" name="hash_string" value="">
				<input type="hidden" name="feedbackType" value="sale">
				<div class="justify">
					<div class="col2">
						<label class="field f-col required">
							<div class="f-title">{$lang->get('Адрес', 'Address')|html}</div>
							<div class="f-input"><input type="text" name="address" required="required"/></div>
							<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите адрес', 'Please enter the email address')|html}</div>
						</label>
						<label class="field f-col required">
							<div class="f-title">{$lang->get('Предлагаемые площади', 'Available area')|html}</div>
							<div class="f-input"><input type="text" name="offer" required="required" /></div>
							<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите предлагаемую площадь', 'Please indicate the proposed area')|html}</div>
						</label>
						<label class="field f-col required">
							<div class="f-title">{$lang->get('Готовность', 'Readiness')|html}</div>
							<div class="f-input"><input type="text" name="readiness" required="required" /></div>
							<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите статус готовности', 'Please indicate readiness status')|html}</div>
						</label>
						<label class="field f-col required">
							<div class="f-title">{$lang->get('Коммерческие условия', 'Commercial terms')|html}</div>
							<div class="f-input"><input type="text" name="condition" required="required" /></div>
							<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите коммерческие условия', 'Please specify the commercial terms')|html}</div>
						</label>
						<label class="field f-col file-choice">
							<div class="file-choice-icon"><i></i></div>
							<div class="file-choice-cont">
								<div class="f-title">{$lang->get('Файл с описанием объекта', 'File describing the object')|html}</div>
								<div class="f-files"><input type="file" name="attach" /></div>
							</div>
						</label>	
						<label class="field f-col required">
							<div class="f-title">{$lang->get('Описание объекта', 'Description of the object')|html}</div>
							<div class="f-input"><textarea rows="10" name="description" required="required"></textarea></div>
							<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, напишите описание объекта', 'Please write a description of the object')|html}</div>
						</label>
					</div>
					<div class="col2">
						<label class="field f-message">
							<div class="f-title">{$lang->get('Статус заявителя', 'Status of the applicant')|html}</div>
							<div class="f-select">
								<select name="issue" class="chosen fullwidth">
									{foreach from=$status item=stat}
									<option value="{$stat}">{$stat}</option>
									{/foreach}
								</select>
							</div>
						</label>
						<label class="field f-col">
							<div class="f-title">{$lang->get('Организация', 'Company')|html}</div>
							<div class="f-input"><input type="text" name="organisation" /></div>
						</label>
						<label class="field f-col required">
							<div class="f-title">{$lang->get('Контактное лицо', 'Contact name')|html}</div>
							<div class="f-input"><input type="text" name="name" required="required"></div>
							<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите имя контактного лица', 'Please provide the name of the contact person')|html}</div>
						</label>
						<label class="field f-col">
							<div class="f-title">{$lang->get('Должность', 'Position')|html}</div>
							<div class="f-input"><input type="text" name="position" /></div>
						</label>
						<div class="f-row justify">
							<label class="field f-col required">
								<div class="f-title">{$lang->get('Телефон', 'Phone')|html}</div>
								<div class="f-input"><input type="text" name="phone" required="required" /></div>
								<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите контактный телефон', 'Please enter the telephone number')|html}</div>
							</label>
							<label class="field f-col required">
								<div class="f-title">{$lang->get('Эл. почта', 'E-mail')|html}</div>
								<div class="f-input"><input type="text" name="email" required="required" /></div>
								<div class="f-error e-empty a-hidden">{$lang->get('Пожалуйста, укажите контактный e-mail', 'Please enter the contact e-mail')|html}</div>
								<div class="f-error e-incorrect_format a-hidden">{$lang->get('Неверный формат e-mail', 'Invalid e-mail format')|html}</div>
							</label>
						</div>
						<label class="field f-col">
							<div class="f-title">{$lang->get('Дополнения, комментарии, пожелания', 'Additions, comments, suggestions')|html}</div>
							<div class="f-input"><textarea rows="10" name="addition"></textarea></div>
						</label>
					</div>
				</div>
				<ul class="f-error general-err a-hidden">
					<li class="e-check_sum a-hidden">{$lang->get('Ошибка при отправке формы. Перезагрузите страницу и попробуйте еще раз.', 'Error while sending the form. Please reload the page and try again.')|html}</li>
				</ul>
				<div class="buttons">
					<button class="a-btn-green">{$lang->get('Отправить сообщение', 'Send Message')|html}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="catalog-bottom">
	<div class="green-line"></div>
	{include file="components/benefits.tpl"}
	{include file="components/news-block.tpl"}
	{include file="components/cbre-belt.tpl"}
</div>


