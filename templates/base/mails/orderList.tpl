{foreach from=$positions item=pos}
<tr>
	<td style="padding-left:18px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
	<td width="67" style="border-bottom:1px solid #e1e1e1; padding-top:20px; padding-bottom:8px; padding-left:8px;" align="left" valign="top">
		{if !empty($pos.image)}<img src="http://{$site_url}{$pos.image->getUrl(66)}" />{/if}
	</td>
	<td valign="top" width="344" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:35px; padding-top:25px; padding-right:15px; padding-left:12px; border-bottom:1px solid #e1e1e1; width:268px;">
		<a href="http://{$site_url}{$pos.url}" style="font-weight:bold;color:#059fdb;text-decoration:none;">{$pos.title}</a> ● <span style='white-space:nowrap;'></span> 2578-00998 
	</td>
	<td valign="top" width="91" style="font-size:18px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1; background-color:#f3f3f3; padding-top:23px; padding-bottom:27px; padding-left:25px;">
		&times;{$pos.count} <span style='color:#4a596e;font-size:15px;'>{if !empty($pos.unit)}{$pos.unit}{else}шт.{/if}</span>
{*		<br><span style='font-size:15px; color:#03b08d; font-weight:bold;'>Под заказ</span>*}
	</td>
	<td valign="top" width="91" style="font-size:18px; color:#000000; text-align:right; border-bottom:1px solid #e1e1e1; background-color:#f3f3f3; padding-top:23px; padding-bottom:27px; padding-left:25px;">
		<span style="white-space:nowrap;">{($pos.price*$pos.count)|price_format}<span style='color:#4a596e;font-size:15px;'> руб.</span></span>
	</td>
	<td style="padding-left:18px; line-height:0; width:1px; background-color:#f3f3f3;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
</tr>
{/foreach}
{if !empty($order.delivery_type)}
<tr>
	<td style="padding-left:18px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
	<td width="67" style="border-bottom:1px solid #e1e1e1; padding-top:26px; padding-bottom:8px; padding-left:8px;" align="left" valign="top">
		{if $order.delivery_type == "Самовывоз"}
		<img style="margin-left:-23px;" src="http://{$site_url}/templates/base/img/mails/order-shiping.png" alt="">
		{else}
		<img style="margin-left:-23px;" src="http://{$site_url}/templates/base/img/mails/order-truck.png" alt="" width="73" height="47">	
		{/if}
	</td> 
	<td width="344" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:27px; padding-top:25px; padding-right:15px; padding-left:12px; border-bottom:1px solid #e1e1e1; width:268px;">
			{?$delivery_address = ''}
			{if $order.delivery_type == "Самовывоз"}{if !empty($store.address)}{?$delivery_address = $store.address}{/if}{else}{if !empty($order.delivery_address)}{?$delivery_address = $order.delivery_address}{/if}{/if}
			<span style='color:#000;'>
			<span style="white-space:nowrap;">{if $order.delivery_type == "Самовывоз"}Самовывоз со склада{else}Доставка{/if}{if $order.delivery_type == "Курьер"} курьером{elseif $order.delivery_type == "Транспортная компания"} транспортной компанией{/if}{if !empty($delivery_address)} по адресу:</span><br>
			<span style="white-space:nowrap;">{$delivery_address|html}</span> ●</span>
			<a href="https://maps.yandex.ru/?text={$delivery_address|html}" target="_blank" style="font-weight:bold;color:#059fdb;text-decoration:none;">На карте</a><br><br>
			{else}
			</span>
			{/if}
	</td>
	<td valign="top" colspan="2" width="91" style="font-size:18px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1; background-color:#f3f3f3; padding-top:23px; padding-bottom:27px; padding-left:25px;{if !empty($order.delivery_price)}text-align:right;{/if}">
		{if !empty($order.delivery_price)}
			<span style="white-space:nowrap;">{$order.delivery_price|price_format}<span style='color:#4a596e;font-size:15px;'> руб.</span></span>
{*			Стоимость доставки:<br>{$order.delivery_price|price_format} руб.*}
		{elseif $order.delivery_price == '0'}
			Бесплатная доставка
		{else}
			Стоимость назовет<br>менеджер<br><br><span style='color:#4a596e;font-size:15px; padding-top:10px;'>когда свяжется с вами для согласования заказа</span>
		{/if}
	</td>
	<td style="padding-left:18px; line-height:0; width:1px; background-color:#f3f3f3;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
</tr>
{/if}
<tr>
	<td style="padding-left:18px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
	<td width="67" style="border-bottom:3px solid #000; padding-top:26px; padding-bottom:22px; padding-left:17px;" align="left" valign="top">
		<img src="http://{$site_url}/templates/base/img/mails/order-payment.png" alt="" width="48" height="42">
	</td>
	<td width="344" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:30px; padding-top:26px; padding-right:15px; padding-left:12px; border-bottom:3px solid #000; width:268px;">
		<span style='color:#000;'>
		{if !empty($order.pay_type)}{$order.pay_type}{/else}Способ оплаты не выбран{/if}
	</td>
	<td valign="top" colspan="2" width="91" style="font-size:18px; color:#000000; text-align:left; border-bottom:3px solid #000; background-color:#f3f3f3; padding-top:22px; padding-bottom:30px; padding-left:25px;">
		{if !empty($order.commission_online_pay)}Комиссия — <span style="white-space:nowrap;">{$order.commission_online_pay} <span style='color:#4a596e;font-size:15px; padding-top:10px;'>руб.</span></span>{/if}
	</td>
	<td style="padding-left:18px; line-height:0; width:1px; background-color:#f3f3f3;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
</tr>
<tr>
	<td style="padding-left:18px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
	<td width="344" colspan="2" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:30px; padding-top:42px; padding-right:15px; width:268px;">
		{if !empty($order.client_comment)}
		<span style='font-size:15px; color:#03b08d; font-weight:bold;'>Комментарий клиента:</span> {$order.client_comment}<br>
		{/if}
		<span style='line-height:25px;'>&nbsp;</span><br>
		<span style='color:#99a1a8;'>
			Окончательный состав заказа и стоимость будут содержаться<br>в коммерческом предложении.
		</span>
	</td>
	<td colspan="3" valign='top' style="padding-top:31px;background-color:#f3f3f3;">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td style="font-size:18px; color:#000; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #e1e1e1;">
					Итого
				</td>
				<td style="font-size:18px; color:#000; font-weight:bold; text-align:right; border-bottom:1px solid #e1e1e1;">
					<span style="white-space:nowrap;">{if !empty($order.total_cost)}{$order.total_cost|price_format}{/if} <span style='color:#4a596e; font-size:15px; padding-top:10px; font-weight:normal'>руб.</span></span>
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{if !empty($order.nds)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:9px; padding-top:6px; padding-right:15px; border-bottom:1px solid #e1e1e1;">
					В том числе НДС
				</td>
				<td style="font-size:18px; color:#000000; text-align:right; border-bottom:1px solid #e1e1e1;">
					<span style="white-space:nowrap;">{$order.nds} <span style='color:#4a596e;font-size:15px; padding-top:10px;'>руб.</span></span>
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
		{if !empty($order.bonus_enable)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:9px; padding-top:6px; padding-right:15px; border-bottom:1px solid #e1e1e1;">
					Бонусные баллы
				</td>
				<td style="font-size:18px; color:#000000; text-align:right; border-bottom:1px solid #e1e1e1;">
					<span style="white-space:nowrap;">+88 <span style='color:#4a596e;font-size:15px; padding-top:10px;'>баллов</span></span>
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
		{/if}
		</table>
	</td>
</tr>

{* 
<table style="font-family: Arial; width: 100%; margin-top: 30px;">
	<tr>
		<td style="vertical-align: top; width: 520px;">
			<table style="background: #ffffff; border: 1px solid #dcdcdc; border-bottom: none; font-family: Arial; margin-top: 3px; width: 477px;">
				<tr>
					<td colspan="3" style="text-align: left; font-size: 18px; padding-top: 26px; padding-left: 20px; padding-right: 20px;  ">Состав и стоимость заказа</td>
				</tr>
				{?$positions = $order['positions']}
				{foreach from=$positions item=pos name="positions_lists"}
				{?$v=$pos.entity}
				{?$item=$v->getItem()}
					<tr>
						<td style="{if !first}border-top: 1px solid #eceeed; {/if}padding-top: 27px; padding-left: 20px; padding-bottom: 10px; vertical-align: top;">
							{?$image = $pos.image}{if !empty($image)}<img src="http://{$site_url}{$pos.image->getUrl(65, 90)}" />{/if}
						</td>
						<td style="{if !first}border-top: 1px solid #eceeed; {/if}padding-top: 27px; padding-bottom: 20px; padding-left: 15px; vertical-align: top;">
							<a style="font-size: 14px; font-weight: bold; color: #005b9f; text-decoration: none;" href="http://{$site_url}{$pos.url}">{$pos.title}</a>
						</td>
						<td style="{if !first}border-top: 1px solid #eceeed; {/if}padding-top: 27px; padding-right: 20px; padding-bottom: 20px; padding-left: 25px; vertical-align: top; width:145px;">
							<span>
								<span style=" color: #000000; font-size: 14px; font-weight: bold;">{($pos.price*$pos.count)|price_format}</span>
								{if $pos.count != 1}<span style="color: #000000; font-size: 12px; font-style: italic;">руб. за {$pos.count}&nbsp;{if !empty($pos.unit)}{$pos.unit}{else}шт.{/if}</span>
								{else}<span style="color: #000000; font-size: 12px; font-style: italic;">руб. за 1 {if !empty($pos.unit)}{$pos.unit}{else}шт.{/if}</span>{/if}
							</span>
						</td>
					</tr>
				{/foreach}
				<tr>
					<td colspan="2" style="border-top: 1px solid #eceeed; padding-top: 15px; padding-left: 20px; padding-bottom: 15px; color: #000000; font-size: 14px;">Промежуточный итог</td>
					<td style="border-top: 1px solid #eceeed; padding-left: 25px; padding-right: 20px; padding-top: 15px; padding-bottom: 15px;">
						<span>
							<span style="color: #000000; font-size: 14px; font-weight: bold;">{$order.positions_price|price_format}</span>
							<span style="color: #000000; font-size: 12px; font-style: italic;"> руб.</span>
						</span>
					</td>
				</tr>
				{if !empty($order.discount)}
					<tr>
						<td colspan="2" style="color: #000000; font-size: 14px; padding-left: 20px; border-top: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px;">Скидка</td>
						<td style="padding-left: 25px; padding-top: 15px; padding-right: 20px; padding-bottom: 15px; border-top: 1px solid #eceeed;">
							<span style="color: #aa8b13; font-size: 14px; font-weight: bold;">{floatval($order.discount)}%</span>
							{if !empty($order.discount_type)}
								<span style="color: #000000; font-size: 12px; font-style: italic;"> — 
									{if $order.discount_type == 'friend'}
										Скидка от друга
									{else}
										{$order.discount_type}
									{/if}
								</span>
							{/if}	
						</td>
					</tr>
				{/if}
				{if !empty($order.bonus_count)}
					<tr>
						<td colspan="2" style="border-top: 1px solid #eceeed; color: #000000; padding-left: 20px; font-size: 14px; padding-top: 15px; padding-bottom: 15px;">Бонусные баллы, списанные в оплату</td>
						<td style="border-top: 1px solid #eceeed; padding-left: 25px; padding-right: 20px; padding-top: 15px; padding-bottom: 15px;">
							<span style="color: #000000; font-size: 14px; font-weight: bold;">{$order.bonus_count}</span><span style="color: #000000; font-size: 12px; font-style: italic;"> {$order.bonus_count|plural_form:'балл':'балла':'баллов':false}</span>
						</td>
					</tr>
				{/if}
				{if !empty($order_bonus)}
					<tr>
						<td colspan="2" style="border-top: 1px solid #eceeed; color: #000000; padding-left: 20px; font-size: 14px; padding-top: 15px; padding-bottom: 15px;">
							<div style="color: #000000; font-size: 14px;">Бонусные баллы к зачислению</div>
							<div style="font-size: 12px; color: #959595; font-style: italic;">Баллы будут начислены после выполнения заказа</div>
						</td>
						<td style="border-top: 1px solid #eceeed; padding-left: 25px; padding-right: 20px; padding-top: 15px; padding-bottom: 15px;">
							<span style="color: #000000; font-size: 14px; font-weight: bold;">+ {$order_bonus}</span><span style="color: #000000; font-size: 12px; font-style: italic;"> {$order_bonus|plural_form:'балл':'балла':'баллов':false}</span>
						</td>
					</tr>
				{/if}
			</table>
			<table style="background: #f8f8f8; border: 1px solid #dcdcdc; font-family: Arial; line-height: 1; width: 477px;">
				<tr>
					<td style="color: #000000; padding-top: 19px; padding-left: 20px;">Итого{if $order_total==true} к оплате{/if}</td>
					<td rowspan="2" style="padding-top: 19px; padding-bottom: 30px; padding-left: 25px; padding-right: 20px; vertical-align: top; width:145px;">
						<span style="color: #000000; font-size: 26px; font-weight: bold;">{$order.total_cost|price_format}</span>
						<span style="color: #000000; font-size: 14px;">руб.</span>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 30px; padding-left: 20px;">
						<span style="font-size: 12px; font-style: italic; color: #454545;">
						Цены указаны без учета <a style="font-size: 12px; font-style: italic; color: #005b9f; text-decoration: none;" href="http://{$site_url}/discount/">промо-акций</a></span>
					</td>
				</tr>
			</table>
		</td>
		<td style="vertical-align: top; width: 300px;">
			<div style="color: #000000; font-size: 12px;">
				<div style="color: #000000; font-size: 16px; margin-bottom: 7px;">Доставка</div>					
				<div style="color: #000000;">
                    {?$delivery_type = $order.properties.delivery_type.value_key}
					{if $delivery_type == 'self'}
						Cамовывоз
						{?$current_store = $order.store}
						{if !empty($current_store)}<br />
							
							<div style="color: #000000;">{$current_store.address}</div>
							<div style="color: #000000; margin-top: 17px;">
								<div style="color: #000000;">Телефон: {$current_store.phone}</div>
								<div style="color: #000000;">{if !empty($current_store.days)}{$current_store.days}{/if}{if !empty($current_store.hours_start) && !empty($current_store.hours_end)} {$current_store.hours_start}&mdash;{$current_store.hours_end}{/if}</div>
								<div style="color: #000000;">Стоимость доставки — бесплатно</div>
							</div>
						{/if}
					{elseif $delivery_type == 'courier'}
						Курьером
						<br />{if !empty($order.delivery_price)}({$order.delivery_price} руб.){/if}
					{elseif $delivery_type == 'company'}
						Транспортной компанией
						{if !empty($order.delivery_price)}<br />({$order.delivery_price} руб.){/if}
					{else}
						Способ еще не выбран
					{/if}
				</div>
				<div style="margin-top: 28px;">
					<div style="color: #000000; font-size: 16px; margin-bottom: 7px;">Оплата</div>
					<div style="color: #000000;">
                        {?$pay_type = $order.properties.pay_type.value_key}
                        {if $pay_type == 'nal'}
                            {if $delivery_type == 'self'}
                                Наличными / картой в магазине
                            {else}
                                Наличными при получении
                            {/if}
                        {elseif $pay_type == 'beznal'}
                            Безналичный расчет
                        {elseif $pay_type == 'online'}
                            Онлайн оплата: {$order.pay_system_method_title}
						{else}
							Способ еще не выбран
						{/if}
						{*include file="components/paytype_text.tpl"*}
						{*!empty($paytype_text[$order.pay_type]) ? $paytype_text[$order.pay_type] : 'Способ еще не выбран'*}
{*					</div>
				</div>
			</div>
		</td>
	</tr>
</table>
*}

{*<div style="margin-top: 30px; background-color: #ffffff; padding: 40px 30px;">
    <table width="100%">
        <tr><td><h2 style="color: #000000; font-size: 18px; text-transform: uppercase; font-weight: normal;"><font color="black">{if !empty($admin_mail)}Состав заказа{else}Напоминаем, что Вы заказали...{/if}</font></h2></td><td style="padding-left: 50px;"><h2 style="color: #000000; font-size: 18px; text-transform: uppercase; font-weight: normal;"><font color="black">{if empty($admin_mail)}...и выбрали{/if}</font></h2></td></tr>
        <tr>
            <td width="438px">
                <table width="100%">
                    {?$positions = $order['positions']}
                    {foreach from=$positions item=pos name="positions_lists"}
                        <tr>
                            <td rowspan="4" style="width: 111px; text-align: center;{if !first} padding-top: 20px;{/if}">{?$image = $pos.image}{if !empty($image)}<img src="http://{$site_url}{$pos.image->getUrl(65, 90)}" />{/if}</td>
                            <td style="color: #000000; font-size: 14px; text-transform: uppercase; font-weight: bold;{if !first} padding-top: 20px;{/if}"><font color="black">{$pos.title}</font></td>
                        </tr>
						{if !empty($pos.data.articul)}
							<tr>
								<td style="padding-top: 3px; font-size: 12px;"><span style="color: #67727e; font-style: italic;">Артикул &mdash;</span> <font color="black">{$pos.data.articul}</font></td>
							</tr>
						{/if}
                        <tr>
                            <td>
                                <table width="100%">
                                    <tr>
                                        <td rowspan="2" width="50">
                                            <span style="font-size: 12px; color: #000000;{if !empty($pos.data.color)} padding-left: 29px; background-color: #{$pos.data.color};{/if}">{if !empty($pos.data.size)}&nbsp;<span style="background-color: #ffffff;">&nbsp;&nbsp;<font color="black" style="white-space: nowrap;">{$pos.data.size}</font>&nbsp;&nbsp;</span>{/if}</span>
                                        </td>
                                        <td height="6px" style="border-bottom: 1px solid #dbdce6; line-height: 6px;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="6px" style="line-height: 6px;">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                               <span style="font-size: 14px; color: #000000"><font color="black">{($pos.price*$pos.count)|price_format}</font> Р</span>{if $pos.count != 1}<span style="font-size: 12px; font-style: italic; color: #67727e;"> за {$pos.count} шт.</span>{/if}
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </td>
            <td style="vertical-align: top; padding-left: 50px;">
                <div style="font-size: 16px; color: #000000;"><font color="black">Оплата</font></div>
                <div style="color: #67727e; font-size: 12px; font-style: italic; margin-top: 8px;">
                    {*include file="components/paytype_text.tpl"*}
                    {*!empty($paytype_text[$order.pay_type]) ? $paytype_text[$order.pay_type] : 'Способ еще не выбран'}
                </div>
                <div style="font-size: 16px; color: #000000; margin-top: 25px;"><font color="black">Доставка</font></div>
                <div style="color: #67727e; font-size: 12px; font-style: italic; margin-top: 8px;">
                    {if !empty($order.delivery_type_self) && $order.delivery_type_self != 0}
                        Cамовывоз из магазина{if !empty($stores[$order.delivery_type_self])}<br />{$stores[$order.delivery_type_self]['title']}{/if}
                    {elseif $order.delivery_type_courier == 1}
                        Курьер по Петербургу
						<br />{if !empty($order.delivery_price)}({$order.delivery_price} P){/if}
                    {elseif !empty($order.delivery_type_company) && $order.delivery_type_company !=0}
                        Транспортной компанией
						{if !empty($order.delivery_price)}<br />({$order.delivery_price} P){/if}
                    {else}
                        Способ еще не выбран
                    {/if}
                </div>
            </td>
        </tr>
		<tr>
			<td colspan="2" style="vertical-align: top; padding: 20px 0 0 121px;">
				<span style="color: #8e8f9d; font-size: 18px; text-transform: uppercase;">Итого</span>&nbsp;&nbsp;
				<span style="color: #000; font-size: 30px; font-weight: bold;">{$order.total_cost|price_format} Р</span>
				{if !empty($order.delivery_type_company)}
					<div style="color: #67727e; font-size: 12px; font-style: italic;">Указано без учета стоимости доставки</div>
				{/if}
			</td>
		</tr>
    </table>
</div>*} 