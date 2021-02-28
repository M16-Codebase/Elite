{?$pageTitle = 'Настройка счетчиков — ' . (!empty($confTitle) ? $confTitle : '')}
<form>
	<div class="content-top">
		<h1>Настройка счетчиков</h1>
		<div class="content-options">
			{?$buttons = array(
				'save' => array(
					'text' => 'Сохранить',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock{if $seo_google.enable == "1"} m-open{/if}">
				<div class="white-block-row">
					<div class="w3">
						<strong>Google</strong>
					</div>
					<label class="w9">
						<input type="hidden" name="seo_google[save]" value="1" />
						<input type="hidden" name="seo_google[enable]" value="0" />
						<input type="checkbox" name="seo_google[enable]" value="1" class="allow-variants"/>
						<span>Включить</span>
					</label>
				</div>
				<div class="white-inner-cont slidebox-cont g-big-slide{if $seo_google.enable == "0"} a-hidden{/if}">
					<div class="g-slidebox{if !empty($seo_google.mode) && $seo_google.mode =="analytics"} m-open{/if}">
						<div class="white-block-row">
							<label class="w12">
								<input type="radio" name="seo_google[mode]" value="analytics" class="allow"/>
								<span>Google Analytics</span>
							</label>
						</div>
						<div class="white-block-row g-slide-body {if empty($seo_google.mode) || $seo_google.mode !="analytics"} a-hidden{/if}">
							<div class="w3">
								<span>Google Analytics ID</span>
							</div>
							<div class="w9">
								<input type="text" name="seo_google[analytics_id]" />
							</div>
						</div>
					</div>
					<div class="g-slidebox{if !empty($seo_google.mode) && $seo_google.mode == "tagmanager"} m-open{/if}">
						<div class="white-block-row">
							<label class="w12">
								<input type="radio" name="seo_google[mode]" value="tagmanager"  class="allow"/>
								<span>Google Tag Manager</span>
							</label>
						</div>
						<div class="white-block-row g-slide-body {if empty($seo_google.mode) || $seo_google.mode != "tagmanager"} a-hidden{/if}">
							<div class="w3">
								<span>Google Tag Manager ID</span>
							</div>
							<div class="w9">
								<input type="text" name="seo_google[tag_manager_id]" />
							</div>
						</div>
					</div>
					{if (!empty($google_targets))}
						{foreach from=$google_targets item=target key=key}
							<div class="white-block-row">
								<label class="w12">
									<input type="hidden" name="seo_google[{$key}]" value="0" />
									<input type="checkbox" name="seo_google[{$key}]" value="1" />
									<span>{$target.title}</span>
								</label>
							</div>
						{/foreach}
					{/if}
				</div>
			</div>
			<div class="wblock{if $seo_yandex.enable == "1"} m-open{/if}">
				<div class="white-block-row">
					<div class="w3">
						<strong>Яндекс</strong>
					</div>
					<label class="w9">
						<input type="hidden" name="seo_yandex[save]" value="1" />
						<input type="hidden" name="seo_yandex[enable]" value="0" />
						<input type="checkbox" name="seo_yandex[enable]" value="1"  class="allow-variants"/>
						<span>Включить</span>
					</label>
				</div>
				<div class="white-inner-cont{if $seo_yandex.enable == "0"} a-hidden{/if}">
					<div class="white-block-row">
						<div class="w3">
							<span>ID счетчика Яндекс.Метрика</span>
						</div>
						<div class="w9">
							<input type="text" name="seo_yandex[id]" />
						</div>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[webvisor]" value="0" />
							<input type="checkbox" name="seo_yandex[webvisor]" value="1" />
							<span>Вебвизор</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[click_map]" value="0" />
							<input type="checkbox" name="seo_yandex[click_map]" value="1" />
							<span>Карта кликов</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[track_links]" value="0" />
							<input type="checkbox" name="seo_yandex[track_links]" value="1" />
							<span>Внешние ссылки, загрузки файлов и отчет по кнопке «Поделиться»</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[denial]" value="0" />
							<input type="checkbox" name="seo_yandex[denial]" value="1" />
							<span>Точный показатель отказов</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[informer]" value="0" />
							<input type="checkbox" name="seo_yandex[informer]" value="1" />
							<span>Информер</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[noindex]" value="0" />
							<input type="checkbox" name="seo_yandex[noindex]" value="1" />
							<span>Запрет отправки на индексацию страниц сайта</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[async]" value="0" />
							<input type="checkbox" name="seo_yandex[async]" value="1" />
							<span>Асинхронный код</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[url_hash]" value="0" />
							<input type="checkbox" name="seo_yandex[url_hash]" value="1" />
							<span>Отслеживание хеша в адресной строке браузера</span>
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="seo_yandex[xml_site]" value="0" />
							<input type="checkbox" name="seo_yandex[xml_site]" value="1" />
							<span>Для XML — сайтов</span>
						</label>
					</div>
					{if (!empty($yandex_targets))}
						{foreach from=$yandex_targets item=target key=key}
							<div class="white-block-row">
								<label class="w12">
									<input type="hidden" name="seo_yandex[{$key}]" value="0" />
									<input type="checkbox" name="seo_yandex[{$key}]" value="1" />
									<span>{$target.title}</span>
								</label>
							</div>
						{/foreach}
					{/if}
				</div>
			</div>
		</div>
	</div>
</form>