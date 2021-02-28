{?$pageTitle = 'Календарь'}
{?$admin_page = 1}
{?$includeJS.calendar = "js/lib/fullcalendar/fullcalendar.min.js"}
{?$includeCss.calendar = "js/lib/fullcalendar/fullcalendar.css"}

<h1>Календарь</h1>
<div id="evt-calendar">
	<div id="evt-popup" class="a-hidden"></div>
	<div class="evt-popup-overlay"></div>
</div>

<div class="a-hidden">
	<div id="evt-list">
		{foreach from=$events key=day item=list}
			{foreach from=$list item=event}
				<div class="evt-details" data-id="{$event.id}" data-start="{$event.start}" data-end="{$event.end}" data-title="{$event.title}" data-background="{!empty($event_colors[$event.properties.type_id]) ? $event_colors[$event.properties.type_id] : 0}" data-foreground="#000">{json_encode($event)}</div>
			{/foreach}
		{/foreach}
	</div>
</div>