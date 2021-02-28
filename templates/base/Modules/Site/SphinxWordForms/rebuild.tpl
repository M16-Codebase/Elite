<div class="content-top">
	<h1>Принудительная перегенерация индекса</h1>
	{if !empty($need_rebuild)}
		Поставлена сron-задача на перегенерацию индекса
	{/if}
	{$border_bottom=true}
	<form class="rebuild-form">
		<button class="a-button-blue">
			Поставить задачу
		</button>	
	</form>
</div>
<div class="popup-window cron-task" data-title="Принудительная перегенерация индекса" data-class="cron-task-popup">
	Поставлена сron-задача на перегенерацию индекса
</div>
