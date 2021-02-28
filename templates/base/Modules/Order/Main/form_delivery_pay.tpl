<div class="field f-delivery">
	<div class="f-title">
		<span>Способ доставки</span>
	</div>
	<div class="f-input">
		<select name="delivery_type" class="chosen select-delivery change-page">
			<option>Выберите способ доставки</option>
			<option value="self" data-page="self">Cамовывоз из магазина — бесплатно</option>
			<option value="courier" data-page="courier">Доставка курьером по Санкт-Петербургу — {if $order.total_cost >= $site_config.delivery_price_free}бесплатно{else}{$site_config.delivery_price} Р{/if}</option>
			{if !empty($transport_companies)}
				<option value="company" data-page="company">Отправка по России транспортной компанией</option>
			{/if}
		</select>
	</div>
    {if !empty($stores)}
        <div class="f-page self">
            {foreach from=$stores item=store}
                <label>
                    <input type="radio" name="self" value="1" class="radio" />
                    <span class="a-inline-block">
                        <strong class="store-title">{$store.title}</strong>, <span class="store-map a-link" data-coords="{$store.coords}">{$store.address}</span>{if !empty($store.metro)}, м. «{$store.metro}»{/if}<br />
                        {$store.phone}.{if !empty($store.days)} {$store.days}{/if}{if !empty($store.hours_start) && !empty($store.hours_end)} {$store.hours_start}&mdash;{$store.hours_end}{/if}
                    </span>
                </label>
            {/foreach}
        </div>
    {/if}
	<div class="f-page courier">
		<div class="f-row">
			<div class="page-field a-inline-block">
				<div class="pf-title">Улица</div>
				<input type="text" name="street" />
			</div>
			<div class="page-field short a-inline-block">
				<div class="pf-title">Дом</div>
				<input type="text" name="house" />
			</div>
			<div class="page-field short a-inline-block">
				<div class="pf-title">Корпус</div>
				<input type="text" name="korpus" />
			</div>
			<div class="page-field short a-inline-block">
				<div class="pf-title">Квартира</div>
				<input type="text" name="apart" />
			</div>
			<div class="page-field short a-inline-block">
				<div class="pf-title">Этаж</div>
				<input type="text" name="floor" />
			</div>
		</div>	
	</div>
    {if !empty($transport_companies)}
		<div class="f-page company">
			<div class="f-row">
				<div class="page-field a-inline-block">
					<div class="pf-title">Компания</div>
					<select name="transport_company_id" class="chosen select-transp fullwidth">
						{foreach from=$transport_companies item=tc}
							<option data-site="{$tc.calc}" value="{$tc.id}">{$tc.name}</option>
						{/foreach}
					</select>
				</div>
				<div class="page-field short a-inline-block">
					<div class="pf-title">Индекс</div>
					<input type="text" name="index" />
				</div>
				<div class="page-field a-inline-block">
					<div class="pf-title">Город / Область</div>
					<input type="text" name="city" />
				</div>
			</div>
			<div class="f-row">
				<div class="page-field a-inline-block">
					<div class="pf-title">Улица</div>
					<input type="text" name="street" />
				</div>
				<div class="page-field short a-inline-block">
					<div class="pf-title">Дом</div>
					<input type="text" name="house" />
				</div>
				<div class="page-field short a-inline-block">
					<div class="pf-title">Корпус</div>
					<input type="text" name="korpus" />
				</div>
				<div class="page-field short a-inline-block">
					<div class="pf-title">Квартира</div>
					<input type="text" name="apart" />
				</div>
				<div class="page-field short a-inline-block">
					<div class="pf-title">Этаж</div>
					<input type="text" name="floor" />
				</div>
			</div>
			<div class="transp-descr">
				Тариф компании «<span class="transp-name"></span>» Вы можете рассчитать на ее сайте: <a href="#" target="_blank" class="transp-site"></a>.<br />
				До офиса транспортной компании мы доставим товар бесплатно.
			</div>
		</div>
    {/if}
</div>
		
{if empty($type_org)}
	<div class="field f-paytype">
		<div class="f-title">
			<span>Способ оплаты</span>
		</div>
		<div class="f-input">
			<select class="chosen select-paytype change-page">
				<option>Выберите способ оплаты</option>
				<option data-page="nal">Наличными</option>
				<option data-page="visa">Visa\MasterCard</option>
				<option data-page="emoney">Электронные деньги</option>
				<option data-page="term">Терминалы</option>
				<option data-page="bank">Банковский перевод</option>
				<option data-page="cassa">Кассы «Евросеть» и «Связной»</option>
				<option data-page="pochta">Почтовый перевод</option>
			</select>
		</div>
		<div class="f-page nal not-open">
			<label>
				<input type="radio" name="pay_type" value="nal" class="radio" />
				<span>Наличными</span>
			</label>
		</div>
		<div class="f-page emoney">
			<label>
				<input type="radio" name="pay_type" value="YDX" class="radio" />
				<span>Яндекс.Деньги</span>
			</label>
			<label>
				<input type="radio" name="pay_type" value="WMR" class="radio" />
				<span>Webmoney</span>
			</label>
			<label>
				<input type="radio" name="pay_type" value="DMR" class="radio" />
				<span>Деньги@mail.ru</span>
			</label>
			<label>
				<input type="radio" name="pay_type" value="PPL" class="radio" />
				<span>PayPal</span>
			</label>
		</div>
		<div class="f-page term">
			<label>
				<input type="radio" name="pay_type" value="HBK" class="radio" />
				<span>Терминалы Элекснет (Handybank)</span>
			</label>
			<label>
				<input type="radio" name="pay_type" value="OSP" class="radio" />
				<span>Терминалы QIWI (ОСМП)</span>
			</label>
			<label>
				<input type="radio" name="pay_type" value="EVS" class="radio" />
				<span>Евросеть</span>
			</label>
		</div>
		<div class="f-page visa not-open">
			<label class="m-hidden">
				<input type="radio" name="pay_type" value="BVC" class="radio" />
				<span>Visa\MasterCard</span>
			</label>
		</div>
		<div class="f-page bank not-open">
			<label class="m-hidden">
				<input type="radio" name="pay_type" value="BTR" class="radio" />
				<span>Банковский перевод</span>
			</label>
		</div>
		<div class="f-page cassa not-open">
			<label class="m-hidden">
				<input type="radio" name="pay_type" value="EUS" class="radio" />
				<span>Кассы «Евросеть» и «Связной»</span>
			</label>
		</div>
		<div class="f-page pochta not-open">
			<label class="m-hidden">
				<input type="radio" name="pay_type" value="POT" class="radio" />
				<span>Почтовый перевод</span>
			</label>
		</div>
	</div>
{/if}