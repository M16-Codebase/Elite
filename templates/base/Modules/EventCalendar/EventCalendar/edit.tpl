{capture assign=time_select}
	{?$cal_hour = 0}
	{section name=hours start=0 loop=24 step=1}
		{?$shown_hour = ($cal_hour < 10)? '0'.$cal_hour : $cal_hour}
		<option value="{$shown_hour}:00:00">{$shown_hour}:00</option>
		<option value="{$shown_hour}:30:00">{$shown_hour}:30</option>
		{?$cal_hour++}
	{/section}
{/capture}
<a class="close-evt-popup" href="#"></a>
<h4>
	{if !empty($evt_id)}
		Редактирование события
	{else}
		Новое событие
	{/if}	
</h4>
<form class="justify">
	<div class="fields-col">
		<input type="hidden" name="save" />
		<input type="hidden" name="id" />
		<div class="field">
			<div class="f-title">Заголовок</div>
			<div class="f-input">
				<input type="text" name="title" />
			</div>
		</div>
		<div class="field">
			<div class="f-title">Продолжительность</div>
			<div class="f-input">
				<span class="unit">С</span> 
				<input type="text" name="startDate" class="datepicker" style="width: 90px;" />
				<input type="hidden" name="startTime" class="starttime-input" disabled />
				<select name="startTime" class="starttime-select" style="width: 70px;">
					{?$cal_hour = 0}
					{section name=hours start=0 loop=24 step=1}
						{?$shown_hour = ($cal_hour < 10)? '0'.$cal_hour : $cal_hour}
						<option value="{$shown_hour}:00:00">{$shown_hour}:00</option>
						<option value="{$shown_hour}:30:00"{if $cal_hour == 9} selected{/if}>{$shown_hour}:30</option>
						{?$cal_hour++}
					{/section}
				</select>
				<span class="unit">&nbsp;&nbsp; по</span> 
				<input type="text" name="endDate" class="datepicker" style="width: 90px;" /> 
				<input type="hidden" name="endTime" class="endtime-input" disabled />
				<select name="endTime" class="endtime-select" style="width: 70px;">
					{?$cal_hour = 0}
					{section name=hours start=0 loop=24 step=1}
						{?$shown_hour = ($cal_hour < 10)? '0'.$cal_hour : $cal_hour}
						<option value="{$shown_hour}:00:00"{if $cal_hour == 12} selected{/if}>{$shown_hour}:00</option>
						<option value="{$shown_hour}:30:00">{$shown_hour}:30</option>
						{?$cal_hour++}
					{/section}
				</select>
			</div>
		</div>
		<div class="field">
			<div class="f-title">Тип мероприятия</div>
			<div class="f-input">
				<select name="type" class="chosen fullwidth">
					<option value="">Выберите тип...</option>
					{foreach from=$evt_types key=key item=type}
						<option value="{$key}">{$type.title}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="field">
			<div class="f-title">Организатор</div>
			<div class="f-input">
				<input type="text" name="organizer" />
			</div>
		</div>
		<div class="field">
			<div class="f-title">Место</div>
			<div class="f-input">
				<input type="text" name="location" />
			</div>
		</div>
		<div class="field">
			<div class="f-title">Стоимость участия</div>
			<div class="f-input">
				<input type="text" name="price" />
			</div>
		</div>
		<div class="field">
			<div class="f-title">Ссылка на описание</div>
			<div class="f-input">
				<input type="text" name="htmlLink" />
			</div>
		</div>
		<div class="field">
			<div class="f-title">Описание</div>
			<div class="f-input">
				<textarea name="description" rows="6"></textarea>
			</div>
		</div>
		<div class="buttons justify">
			<button class="a-button-green">Сохранить</button>
			{if !empty($evt_id)}
				<a class="remove-evt-btn" href="/eventcal/deleteEvent/?id={$evt_id}"><i></i> Удалить</a>
			{/if}
		</div>
	</div>
		
	<div class="mails-col">
		<div class="mails-title">Разослать приглашения</div>
		<div class="mails-list">
			<label class="all"><input type="checkbox" name="company" /> Всей компании</label>
			<ul>
				{foreach from=$depts key=dept_id item=dept}
					<li>
						<label class="dept"><input type="checkbox" name="dept[{$dept_id}]"> {$dept.name}</label>
						<ul>
							{foreach from=$dept.staff item=$person}
								<li>
									<label><input type="checkbox" name="pers[{$person.id}]"> {$person.name} {$person.surname}</label>
								</li>
							{/foreach}
						</ul>
					</li>
				{/foreach}
			</ul>
		</div>
	</div>
</form>