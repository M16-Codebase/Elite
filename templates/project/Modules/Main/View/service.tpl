{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Достойный сервис на элитном рынке | М16-Недвижимость'}
	{?$pageDescription = 'Заем под залог квартиры, индивидуальный поиск, инвестиционный тур, срочный выкуп — эти и другие услуги доступны каждому клиенту агентства недвижимости М16'}
{else}
	{?$pageTitle = 'Decent services for demanding clients | M16 Real Estate Agency'}
	{?$pageDescription = 'Real estate secured loan, individual search, the investment tour, urgent buyout — these services and other are available to every client of M16 Real Estate Agency'}
{/if}
<div class="top-bg m-white service-sc1 service-sc">
	<div class="site-top">
		<h1 class="title" title="Достойный сервис на элитном рынке">
      <span class="title-top">
        {$lang->get("Достойный сервис", "Respectable service")|html}
      </span>
      <div class="title-bottom">
        {$lang->get("на элитном рынке", "on the luxury market")|html}
      </div>
    </h1>
		<div class="main">
      {$lang->get("в санкт-петербурге", "in St.Petersburg")|html}
    </div>
	</div>
</div>
<div class="section-title service-sc2 service-sc">
	<div class="list m-top">
		<a href="#purchase" class="scroller">
		  {$lang->get("выкуп", "purchase")|html}
		</a>
			<span class="slash"></span>
		<a href="#rentier" class="scroller">
		  {$lang->get("«рантье»", "rentier")|html}
		</a>
			<span class="slash"></span>
		{*}<a href="#loans" class="scroller">
		  {$lang->get("займы", "loans")|html}
		</a>
			<span class="slash"></span>*}
		<a href="#tradein" class="scroller">
		  {$lang->get("trade-in", "trade-in")|html}
		</a>
			<span class="slash"></span>
		<a href="#invest" class="scroller">
		  {$lang->get("инвест-тур", "invest tour")|html}
		</a>
			<span class="slash"></span>
		<a href="#manager" class="scroller">
		  {$lang->get("менеджер", "manager")|html}
		</a>
			{*<span class="slash"></span>
		<a href="#lawers" class="scroller">
		  {$lang->get("юристы", "lawers")|html}
		</a>*}
			<span class="slash"></span>
		<a href="#security" class="scroller">
		  {$lang->get("безопасность", "security")|html}
		</a>
			<span class="slash"></span>
		<a href="#advertising" class="scroller">
		  {$lang->get("реклама", "advertising")|html}
		</a>
	</div>
	<div id="purchase" class="svg-wrap">
		{fetch file=$path . "vykup.svg"}
	</div>
	<h2 class="title" title="Срочный выкуп">
		<span>
      {$lang->get("Срочный выкуп", "Urgent purchase")|html}
    </span>
	</h2>
	<div class="descr">
    {$lang->get("Выкуп квартир за наличные деньги или безналичный расчет в кратчайшие сроки.", "The purchase of apartments for cash or non-cash payment in the shortest possible time.")|html}
  </div>
</div>
<div class="section-text row m-sand m-watch service-sc3 service-sc">
	<div class="w2">
		<div class="main">
      {$lang->get("Выгодная<br>сделка", "Profitable transaction")|html}
    </div>
		<div class="descr">
      {$lang->get("«М16» выплачивает до 90% <br>рыночной стоимости <br>объекта недвижимости.", "M16 pays up to 90% <br>of the market value <br>of the real estate object")|html}
    </div>
	</div>
	<div class="w2">
		<div class="main">
      {$lang->get("Уникальные<br>предложения", "Unique offers")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы готовы рассмотреть <br>возможность выкупа даже <br>ипотечных квартир.", "We are ready to consider <br>the possibility of urgent <br>purchase of mortgage apartments.")|html}
    </div>
	</div>
	<div class="img"></div>
</div>
<div class="small-text-wrap service-sc4">
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Мы берем на себя все заботы", "We take care of everything")|html}
    </div>
		<div class="descr">
      {$lang->get("Наши специалисты свяжутся с вами, ответят на ваши вопросы, организуют просмотр квартиры.", "When dealing with elite and business class real estate we take upon ourselves the costs of registration and conducting of transaction.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Мы заботимся о вашей безопасности", "We take care of your safety")|html}
    </div>
		<div class="descr">
      {$lang->get("«М16» предоставляет автомобиль с водителем и охраной на сделку.", "М16 provides you with a car with a driver and a guard for a transaction.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Мы дорожим своей репутацией", "We value our reputation")|html}
    </div>
		<div class="descr">
      {$lang->get("Все сделки юридически чистые, прозрачные и конфиденциальные.", "All transactions are legally sound, transparent and confidential.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Мы ценим ваше время", "We appreciate your time")|html}
    </div>
		<div class="descr">
    {$lang->get("Сделка заключается оперативно, и продавец может получить аванс до <span class='a-nowrap'>500 000 р.</span> уже в день обращения.", "The transaction is conducted quickly and the seller can obtain an advance amount up to <span class='a-nowrap'>500 000 rubles</span> on the day of application. The transaction is conducted in several stages")|html}
    </div>
	</div>
</div>
<div class="deal-wrap service-sc5 ">
	<div class="splited-block">
		<div class="splited-border m-left"><span class="splited-inner">
      {$lang->get("Сделка за", "Deal is made by")|html}
    </span></div>
		<div class="splited-center">6</div>
		<div class="splited-border m-right"><span class="splited-inner">
    {$lang->get("этапов", "steps")|html}
    </span>
    </div>
	</div>
	<div class="splited-block">
		<div class="splited-border m-left"><span class="splited-inner">
      {$lang->get("Всего", "Only")|html}
    </span></div>
		<div class="splited-center">4</div>
		<div class="splited-border m-right"><span class="splited-inner">
    {$lang->get("документа", "documents")|html}
    </span>
    </div>
	</div>
</div>
<div class="send-req">
	<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
    {$lang->get("Отправить заявку", "Send request")|html}
  </a>
</div>

  <div id="rentier" class="section-title service-sc6">
	<div class="svg-wrap">
		{fetch file=$path . "rantie.svg"}
	</div>
	<h2 class="title" title="Программа «Рантье»">
		<span>
      {$lang->get("Программа «Рантье»", "Rentier")|html}
    </span>
	</h2>
	<div class="descr">
    {$lang->get("Программа «Рантье» — это возможность в течение многих лет получать вам и вашей семье стабильный доход от аренды с минимальными затратами времени и труда.", "You have a real estate that you would like to rent or that you are already renting, and you want to optimize the process? The Rentier program is an opportunity to receive regular rental income with a minimum expenditure of time and labor. It will provide you and your family with a stable income for many years.")|html}
  </div>
</div>
<div class="service-sc7">
  <div class="section-text row m-sofa">
    <div class="w2">
      <div class="main">
        {$lang->get("Вы цените свое время<br>и готовы предоставить<br>управление вашей<br>недвижимостью<br>профессионалам?", "You value your time <br> and are ready to entrust <br> the management of your <br> property to the <br> professionals? ")|html}
      </div>
    </div>
    <div class="w2">
      <div class="main">
        {$lang->get("Специалисты компании<br>М16 предоставляют<br>полное юридическое<br>сопровождение<br>вашей сделки.", "The specialists <br> of the M16 company <br> will provide <br> full legal support of <br> your transaction.")|html}
      </div>
    </div>
    <div class="img"></div>
  </div>
</div>
<div class="small-text-wrap service-sc4">
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Безопасность сделки", "Safety of the transaction")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы самостоятельно проверяем всех контрагентов и гарантируем прозрачность всех сделок.", "We check all the contractors by our own means and guarantee the transparency of all transactions.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Ответственный подбор арендаторов", "Responsible selection of tenants")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы учтем все ваши пожелания относительно будущих жильцов и самостоятельно проверим их в своей службе безопасности.", "We will consider all your wishes regarding future tenants and will check them in our security service.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Образцовое содержание квартиры", "Keeping the apartment in perfect order")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы гарантируем хорошее состояние вашей недвижимости на протяжении всего срока действия договора.", "We guarantee the good condition of your property throughout the term of the contract.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Еще не имеете недвижимости?", "You don't have real property for rent?")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы поможем вам подобрать наиболее прибыльные варианты  жилья для инвестиций и проконсультируем.", "We will help you find the most profitable housing options for investment and advise you on all issues. ")|html}
    </div>
	</div>
</div>
<div class="send-req m-border m-text service-sc8 service-sc">
	<div>
    {$lang->get("Мы внимательно относимся к каждому клиенту. Наши менеджеры подберут программу с учетом ваших пожеланий и на максимально выгодных для вас условиях.", "We are attentive to each client. Our managers will choose a program based on your wishes and on the most favorable terms for you.")|html}
  </div>
	<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
    {$lang->get("Отправить заявку", "Send request")|html}
  </a>
</div>
{*}
<div id="loans" class="section-title service-sc6 service-sc9" id='zalog'>
	<div class="svg-wrap">
		{fetch file=$path . "zalog.svg"}
	</div>
	<h2 class="title" title="Заем под залог недвижимости">
		<span>
      {$lang->get("Заем под залог недвижимости", "A loan secured by an apartment")|html}
    </span>
	</h2>
	<div class="descr">
    {$lang->get("М16 предоставляет крупные денежные займы с небольшой процентной ставкой под залог вашего имущества:", "M16 provides cash loans with a small interest rate secured by:")|html}
  </div>
	<div class="list">
		<div>
		  {$lang->get("квартиры", "an apartment")|html}
		</div>
			<span class="slash"></span>
		<div>
		  {$lang->get("доли в квартире", "a share in the apartment")|html}
		</div>
			<span class="slash"></span>
		<div>
		  {$lang->get("Парковочного места", "a parking space")|html}
		</div>
			<span class="slash"></span>
		<div>
		  {$lang->get("загородного дома", "a vacation house")|html}
		</div>
	</div>
</div>

<div class="service-sc7">
  <div class="section-text row m-wallet">
    <div class="w2">
      <div class="main">
        {$lang->get("Низкие процентные<br>ставки", "Low interest <br>rates")|html}
      </div>
      <div class="descr">
        {$lang->get("<span><span>от</span> 1 ,6%</span><br>от суммы займа", "<span> <span>from</span> 1.6 %</span><br> of the loan amount")|html}
      </div>
    </div>
    <div class="w2">
      <div class="main">
        {$lang->get("Договор займа<br>это выгодно", "The loan agreement <br> is profitable")|html}
      </div>
      {$lang->get("", "")|html}
      <div class="descr">
        {$lang->get("<span><span>до</span> 90%</span><br>от рыночной стоимости", "<span><span>up to</span> 90%</span><br> of the market value of the object.")|html}
      </div>
    </div>
    <div class="img"></div>
  </div>
</div>
<div class="small-text-wrap service-sc4">
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Мы дорожим вашим временем", "We appreciate your time")|html}
    </div>
		<div class="descr">
      {$lang->get("Денежные займы предоставляются в течение 2&nbsp;дней после обращения. Менеджер по оценке помещения оперативно выезжает на ваш объект и делает оценку.", "Cash loans are provided within 2&nbsp;days after the application. The assessment manager promptly leaves for your facility and makes an assessment.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Индивидуальный подход", "The individual approach")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы рассматриваем гибкие схемы сотрудничества и всегда работаем исходя из интересов клиента. Сроки и сумма займа рассчитываются исходя из ваших потребностей.", "We consider flexible schemes of cooperation and are always working in the interests of the client. The terms and the amount of the loan are depending on your needs.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Особые условия", "Special conditions")|html}
    </div>
		<div class="descr">
      {$lang->get("Компания М16 готова взять на себя расходы по обременению объектов.", "The M16 company is ready to defray the expenses of the encumbrance of objects.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Мы ценим ваше время", "We appreciate your time")|html}
    </div>
		<div class="descr">
      {$lang->get("Сделка заключается оперативно, и продавец может получить аванс до 500 000 р. уже в день обращения.", "We make deals fast and the seller may receive an advance of up to 500 000 rub. in the same day.")|html}
    </div>
	</div>
</div>
<div class="send-req">
	<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
    {$lang->get("Отправить заявку", "Send request")|html}
  </a>
</div>
*}
<div id='tradein' class="section-title service-sc6" id="tradein">
	<div class="svg-wrap">
		{fetch file=$path . "tradein.svg"}
	</div>
	<h2 class="title" title="Flat Trade-In">
		<span>Flat Trade-In</span>
	</h2>
	<div class="descr">
    {$lang->get("Как хочется поменять старую квартиру на квартиру своей мечты! На нестабильном рынке цены меняются регулярно, а самые интересные предложения быстро расходятся.", "You want so much to change the old apartment to the apartment of your dreams! In the unstable market the prices change regularly, and the most interesting proposals are purchased quickly.")|html}
  </div>
</div>
<div class="small-text-wrap service-sc4">
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Оперативность сделки", "The efficiency of the transaction")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы сразу выкупаем понравившуюся вам квартиру и берем на себя обязательства по продаже старого жилья.", "We immediately purchase the apartment that you like and undertake obligations on the sale of your old property.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Не бойтесь роста цен", "Don't be afraid of the rising prices")|html}
    </div>
		<div class="descr">
      {$lang->get("Компания М16 сразу приобретает для вас жилплощадь, что позволяет зафиксировать цену на вашу новую квартиру. С нами не страшны никакие колебания цен!", "The M16 company immediately acquires a residence for you, which allows you to lock in the price on your new apartment. With us you have to fear no fluctuations of prices!")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Минимальные вложения", "The minimum investment")|html}
    </div>
		<div class="descr">
      {$lang->get("Достаточно внести 5% от стоимости нового жилья.", "It is enough to pay in 5% of the cost of a new residence.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Юридическая чистота", "Legal purity")|html}
    </div>
		<div class="descr">
      {$lang->get("Наши специалисты с многолетним стажем оформляют все необходимые документы.", "Our experts with years of experience prepare all necessary documents.")|html}
    </div>
	</div>
</div>


<div class="section-title service-skew-section service-sc10 service-sc">
  <div class="service-skew-section-bg"></div>

  <div class="service-skew-section-content">
    <div class="service-skew-section-pic">
      <img src="/img/service-heart.png" alt="heart">
    </div>
    <div class="service-skew-section-title">
      {$lang->get("С М16 вам нужно только выбрать достойное <br>предложение из обширной базы элитного жилья,<br>и вы можете считать, что оно ваше", "With the M16 you only need to choose a decent offer from our extensive database of luxury housing, and you can already consider it yours")|html}
    </div>
    <div class="service-skew-section-caption">
      {$lang->get("Остальное: выкуп желаемой квартиры, продажа вашей старой недвижимости,<br>заключение сделки с банком, финансовые операции — это наши заботы.", "The rest is our concern: purchase of the desired apartment, sale of your old property, the transaction with the bank, financial operations")|html}
    </div>
  </div>
</div>

<div class="small-text-wrap service-sc4">
  <div class="small-text-block">
    <div class="main">
      {$lang->get("Безопасность <br> сделки", "The security of the transaction")|html}
    </div>
    <div class="descr">
      {$lang->get("Мы работаем только с проверенными банками и застройщиками.", "We work only with reliable banks and developers.")|html}
    </div>
  </div>
  <div class="small-text-block">
    <div class="main">
      {$lang->get("Минимум наличных <br> средств", "Minimum cash")|html}
    </div>
    <div class="descr">
      {$lang->get("Вам не нужно самостоятельно перевозить крупные суммы денег.", "You do not need to bring large sums of money with you.")|html}
    </div>
  </div>
  <div class="small-text-block">
    <div class="main">
      {$lang->get("Индивидуальный <br> подход к клиенту", "Individual <br> approach to the client")|html}
    </div>
    <div class="descr">
      {$lang->get("Наши менеджеры всегда действуют исходя из ваших интересов.", "Our managers always act according to your interests.")|html}
    </div>
  </div>
</div>
<div class="send-req">
  <a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
    {$lang->get("Отправить заявку", "Send request")|html}
  </a>
</div>



<div id='invest' class="section-title service-sc6" id='tour'>
	<div class="svg-wrap">
		{fetch file=$path . "investtur.svg"}
	</div>
	<h2 class="title" title="Инвестиционный тур">
		<span>
      {$lang->get("Инвестиционный тур", "Investment tour")|html}
    </span>
	</h2>
	<div class="descr">
    {$lang->get("Мы организуем экскурсии по объектам элитной недвижимости. Вы можете ознакомиться с вариантами вашего будущего жилья или объекта инвестиций в наиболее комфорте для вас формате.", "We organize excursions to the objects of real estate. You can acquaint yourself with several variants of your future home or investment object in the most convenient format.")|html}
  </div>
</div>
<div class="small-text-wrap service-sc11">
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "meeting.svg"}</div>
		<div class="descr">
      {$lang->get("Встреча в аэропорту или на вокзале", "Meeting at the airport or train station")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "car.svg"}</div>
		<div class="descr">
      {$lang->get("Автомобиль бизнес-класса с водителем", "Comfortable business class car with a driver")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "excursion.svg"}</div>
		<div class="descr">
      {$lang->get("Экскурсия по объектам элитной недвижимости <span class='a-nowrap'>Санкт-Петербурга</span>", "Tour to the objects of elite real estate of St.Petersburg")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "talk.svg"}</div>
		<div class="descr">
      {$lang->get("Консультация о рынке жилья и предложениях", "Comprehensive consultation on the housing market and the specific proposals")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "hotel.svg"}</div>
		<div class="descr">
      {$lang->get("Проживание в отеле премиум-класса за счет компании", "Accommodation in a premium class hotel at the company's expense for up to 90 days")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "ur.svg"}</div>
		<div class="descr">
      {$lang->get("Юридическое сопровождение и заключение сделки", "Full legal support and conclusion of a transaction")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "gift.svg"}</div>
		<div class="descr">
      {$lang->get("Приятные бонусы и подарки", "Pleasant bonuses and gifts")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main m-svg">{fetch file=$path . "transfer.svg"}</div>
		<div class="descr">
      {$lang->get("Трансфер в аэропорт или на вокзал", "Transfer to the airport or train station")|html}
    </div>
	</div>
</div>
<div class="send-req">
	<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
    {$lang->get("Отправить заявку", "Send request")|html}
  </a>
</div>
 
{*<div class="section-title service-sc6">
	<div class="svg-wrap">
		{fetch file=$path . "ur-sopr.svg"}
	</div>
	<h2 class="title" title="Юридическое сопровождение">
		<span>
      {$lang->get("Юридическое сопровождение", "")|html}
    </span>
	</h2>
	<div class="descr">
    {$lang->get("Сделки с недвижимостью это всегда риски, которых можно избежать, обратившись к профессионалам!", "")|html}
  </div>
</div>
<div class="small-text-wrap service-sc4">
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Доверие и безопасность на первом месте", "")|html}
    </div>
		<div class="descr">
      {$lang->get("Мы очень ответственно подходим к юридическому сопровождению вашей сделки с недвижимостью.", "")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Консультации по всем вопросам", "")|html}
    </div>
		<div class="descr">
      {$lang->get("Позвонив по телефону", "")|html}
      <{if $device_type == 'phone'}a href="tel:{$contacts.display_phone}"{else}span{/if} class="a-nowrap"> {$contacts.display_phone}</{if $device_type == 'phone'}a{else}span{/if}>
      {$lang->get("вы всегда можете получить консультацию по вопросам, касающимся юридической безопасности вашей сделки.", "")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Только опытные специалисты", "")|html}
    </div>
		<div class="descr">
      {$lang->get("Наши юристы уже более 10&nbsp;лет занимаются вопросами недвижимости.", "")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
      {$lang->get("Мы понимаем ценность вашего времени", "")|html}
    </div>
		<div class="descr">
      {$lang->get("Вам не нужно тратить его на дополнительный поиск юриста или нотариуса для сопровождения вашей сделки.", "")|html}
    </div>
	</div>

	<div class="small-text-block">
		<div class="main">
    {$lang->get("Защита от бумажной волокиты", "")|html}
    </div>
		<div class="descr">
    {$lang->get("Мы проводим полное сопровождение вашей сделки, вам не нужно будет заниматься рутинным изучением бумаг.", "")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
    {$lang->get("Отсутствие подводных камней", "")|html}
    </div>
		<div class="descr">
    {$lang->get("Мы обязательно проверяем юридическую чистоту объекта, с которым работаем.", "")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
    {$lang->get("Чистота и прозрачность сделки", "")|html}
    </div>
		<div class="descr">
    {$lang->get("Вы можете отследить ход вашей сделки на любом ее этапе. По ходу сделки осуществляется составление всех необходимых соглашений и организация взаиморасчетов.", "")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
    {$lang->get("Мы следим за своей репутацией", "")|html}
    </div>
		<div class="descr">
    {$lang->get("Поэтому очень внимательно относимся к любым, даже самым сложным сделкам.", "")|html}
    </div>
	</div>
</div>
<div class="send-req">
	<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
  {$lang->get("Отправить заявку", "")|html}
  </a>
</div>*}
 
<div id='manager' class="section-title service-sc6">
	<div class="svg-wrap">
		{fetch file=$path . "pers-manager.svg"}
	</div>
	<h2 class="title" title="Персональный менеджер">
		<span>
    {$lang->get("Персональный менеджер", "Personal manager")|html}
    </span>
	</h2>
	<div class="descr">
  {$lang->get("При заключении договора с М16 вам назначается персональный менеджер из нашей команды. Вы всегда работаете только с одним человеком, который полностью погружается в особенности вашей сделки.", "When concluding the contract with M16 a personal manager from our team is assigned to you. You are always working with only one person, who fully immerses himself in the specifics of your transaction.")|html}
  </div>
</div>
<div class="service-sc7">
  <div class="section-text row m-pen">
    <div class="w2">
      <div class="main">
      {$lang->get("Персональный<br>менеджер и клиент —<br>это команда.", "Personal manager<br> and client are a team.")|html}
      </div>
    </div>
    <div class="w2">
      <div class="descr">
      {$lang->get("Наши сотрудники осуществляют<br>поддержку клиента на всех этапах<br>сделки, начиная от заключения<br>первого договора и заканчивая<br>помощью с переездом.", "Our employees support <br> clients at all stages of the transaction,  <br> from signing the first contract to <br >helping with the moving <br> to the new residence.")|html}
      </div>
    </div>
    <div class="img"></div>
  </div>
</div>
<div class="small-text-wrap service-sc4">
	<div class="small-text-block">
		<div class="main">
    {$lang->get("Только опытные специалисты", "Only experienced professionals")|html}
    </div>
		<div class="descr">
    {$lang->get("Каждый специалист нашей команды имеет многолетний стаж в сфере работы с недвижимостью.", "Every specialist in our team has years of experience in the field of real estate.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
    {$lang->get("Прежде всего — потребности клиента", "First and foremost — the needs of the client")|html}
    </div>
		<div class="descr">
    {$lang->get("Мы назначаем персонального менеджера исходя из ваших запросов. Каждый наш сотрудник обладает широким спектром знаний в своей области.", "We assign a personal manager according to your requests. Each member of our staff has a wide range of knowledge in his field.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
    {$lang->get("Всегда на связи", "Always in touch")|html}
    </div>
		<div class="descr">
    {$lang->get("Наш менеджер всегда готов ответить на интересующие вас вопросы. Все процедуры взаиморасчетов понятны и согласуются с вами.", "Our manager is always ready to answer your questions. All the procedures of mutual settlements are clear and are agreed upon with you.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="main">
    {$lang->get("Оформление документов", "Preparation of documents")|html}
    </div>
		<div class="descr">
    {$lang->get("Назначение персонального менеджера избавит вас от бумажной волокиты.", "The appointment of a personal manager will save you from routine paperwork.")|html}
     </div>
	</div>
</div>
<div class="m-border send-req m-text">
	<div>
  {$lang->get("Персональный менеджер всегда действует исходя из интересов клиента и подбирает наиболее выгодные условия совершения сделки.", "Our personal manager always acts in the interests of the client and selects the most favorable conditions of the transaction.")|html}
  </div>
	<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
    {$lang->get("Отправить заявку", "Send request")|html}
  </a>
</div>

<div id='security' class="section-title service-sc6">
	<div class="svg-wrap">
		{fetch file=$path . "bezopasnost.svg"}
	</div>
	<h2 class="title" title="Безопасность сделки">
		<span>
    {$lang->get("Безопасность сделки", "Safety of transaction")|html}
    </span>
	</h2>
	<div class="descr">
  {$lang->get("Приоритетом компании М16 является доверие клиента и безопасность всех заключаемых сделок.", "The priority of the M16 company is the customer’s confidence and the safety of all transactions.")|html}
  </div>
</div>
<div class="small-text-wrap row service-sc12">
	<div class="w2">
		<div class="small-text-block a-left">
			<div class="main">
      {$lang->get("Своя служба безопасности", "Our own security service")|html}
      </div>
			<div class="descr">
      {$lang->get("Компания М16 отдельно предоставляет услуги по проверке объектов недвижимости, даже если вы не являетесь нашим клиентом.", "The M16 company can separately render the services of real estate inspection, even if you are not our client.")|html}
      </div>
		</div>
		<div class="small-text-block a-right">
			<div class="main">
        {$lang->get("Работа с самыми сложными сделками", "Working with the most complex transactions")|html}
      </div>
			<div class="descr">
        {$lang->get("Мы дорожим своей репутацией, поэтому внимательно следим за безопасностью.", "We value our reputation, so we carefully control the security.")|html}
      </div>
		</div>

	</div>
	<div class="w2">
		<div class="small-text-block a-left">
			<div class="main">
      {$lang->get("Только надежные застройщики. Только надежные банки.", "Only trusted developers. Only reliable banks")|html}
       </div>
			<div class="descr">
      {$lang->get("Мы проводим собственную проверку всех контрагентов, не полагаясь на сторонние оценки. Поэтому сотрудничество с М16 — это всегда 100% гарантия честности всех сторон.", "We carry out our own checks of all counterparties, not relying on third-party evaluation. Therefore, cooperation with M-16 is always a 100% guarantee of the honesty of all parties.")|html}
      </div>
		</div>
		<div class="small-text-block a-right">
			<div class="main">
      {$lang->get("Мы дорожим вашим спокойствием", "We value your peace of mind")|html}
      </div>
			<div class="descr">
      {$lang->get("Заключая договор с М16, вам не нужно заказывать дополнительные услуги у юриста, нотариуса или детектива, чтобы проверить чистоту объекта.", "Signing a contract with M16 you don't need to order additional services of a lawyer, a notary or a detective to check the purity of the object. Each package of the documents undergoes a thorough examination.")|html}
      </div>
		</div>
	</div>
	<div class="img"></div>
</div>
<div class="small-text-wrap m-black service-sc6 service-sc">
	<div class="small-text-block">
		<div class="descr">
    {$lang->get("Каждый пакет документов проходит тщательную экспертизу.", "Each package of the documents undergoes a thorough examination.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="descr">
    {$lang->get("Каждый сотрудник имеет высокую квалификацию и многолетний стаж работы, что позволяет избежать ошибок в ведении сделки.", "Every employee has high qualification and long work experience, which allows him to avoid mistakes in conducting the transaction.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="descr">
    {$lang->get("М16 имеет все необходимые лицензии и сертификаты и является участником Ассоциации Риэлтеров.", "M16 has all the necessary licenses and certificates and is a member of the Association of Realtors.")|html}
    </div>
	</div>
	<div class="small-text-block">
		<div class="descr">
    {$lang->get("Опыт наших сотрудников позволяет учитывать все возможные риски при проведении сделок с недвижимостью.", "The experience of our staff allows us to consider all possible risks when conducting real estate transactions.")|html}
    </div>
	</div>
</div>
<div class="send-req">
</div>

<div id='advertising' class="section-title service-sc6">
	<div class="svg-wrap">
		{fetch file=$path . "reklam-camp.svg"}
	</div>
	<h2 class="title" title="Рекламная кампания">
		<span>
    {$lang->get("Рекламная кампания", "Advertising campaign")|html}
    </span>
	</h2>
	<div class="descr">
  {$lang->get("Не знаете, как найти своих покупателей? Позвольте нам заняться распространением информации о вашем объекте.", "You don't know how to find your customers? Let us disseminate the information about your property object.")|html}
  </div>
</div>
<div class="small-text-wrap m-tile">
	<div class="small-text-block m-black">
		<div class="center">
			<div class="main m-sand">1</div>
			<div class="descr">
      {$lang->get("Профессиональная<br>фотосъемка", "Professional photography")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-white">
		<div class="center">
			<div class="main">2</div>
			<div class="descr">
      {$lang->get("3D-туры по объектам<br>и окрестностям", "3D-tours of objects and surroundings")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-sand-white">
		<div class="center">
			<div class="main m-white">3</div>
			<div class="descr">
      {$lang->get("Реклама на телевидении<br>и радио", "Advertising on television and radio")|html}
    </div>
		</div>
	</div>
	<div class="small-text-block m-white">
		<div class="center">
			<div class="main m-sand">4</div>
			<div class="descr">
      {$lang->get("Наружная реклама<br>в Санкт-Петербурге<br>и Ленинградской области", "Printing the information about the property object in our own real estate catalog M16, which is distributed in more than 300 locations around St.Petersburg")|html}
      </div>
		</div>
	</div>

	<div class="small-text-block m-white">
		<div class="center">
			<div class="main m-sand">5</div>
			<div class="descr">
      {$lang->get("Создание и распространение<br>печатной продукции:<br>листовок, каталогов,<br>презентаций", "Thematic newsletter using the client base to the customers who are interested in the elite real estate")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-black">
		<div class="center">
			<div class="main m-sand">6</div>
			<div class="descr">
      {$lang->get("Размещение в собственном<br>каталоге недвижимости М16,<br>распространяемом в более чем<br>300 местах по всему<br>Санкт-Петербургу", "Printing the information about the property object in our own real estate catalog M16, which is distributed in more than 300 locations around St.Petersburg")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-white">
		<div class="center">
			<div class="main">7</div>
			<div class="descr">
      {$lang->get("Создание сайтов для продвижения<br>объектов", "Development of websites for promoting the objects")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-sand-black">
		<div class="center">
			<div class="main m-white">8</div>
			<div class="descr">
      {$lang->get("Контекстно-баннерная<br>реклама в интернете,<br>баннеры на профильных<br>порталах, таргетированная<br>реклама в соцсетях и др.", "Сontext-banner advertisement in the Internet, banners at the relevant portals, targeted advertising in social networks and other types of Internet promotion")|html}
      </div>
		</div>
	</div>

	<div class="small-text-block m-sand-black">
		<div class="center">
			<div class="main m-white">9</div>
			<div class="descr">
      {$lang->get("Организация бесплатных<br>экскурсий на объект", "Organization of free guided tours to the object")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-white">
		<div class="center">
			<div class="main">10</div>
			<div class="descr">
      {$lang->get("Изготовление<br>сувенирной продукции", "The creation and distribution of printed products: leaflets, catalogs, presentations in the proven distribution points")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-black">
		<div class="center">
			<div class="main m-sand">11</div>
			<div class="descr">
      {$lang->get("Создание информационного<br>поля: блоги, статьи,<br>отзывы", "Creation of a media landscape: blogs, articles, reviews")|html}
      </div>
		</div>
	</div>
	<div class="small-text-block m-white">
		<div class="center">
			<div class="main m-black">12</div>
			<div class="descr">
      {$lang->get("Тематическая рассылка<br>по базе клиентов,<br>интересующихся элитной<br>недвижимостью", "Thematic newsletter using the client base to the customers who are interested in the elite real estate")|html}
      </div>
		</div>
	</div>
</div>
<div class="send-req m-text m-fullwidth">
	<div class="main">
  {$lang->get("Грамотная рекламная кампания существенно повышает скорость сделки", "Competent advertising campaign significantly increases the speed of the transaction.")|html}
  </div>
	<div>
  {$lang->get("Заказывая у нас рекламную кампанию для своего объекта, вы можете быть уверены в профессиональном подходе и представлении вашего объекта в достойном виде.", "You can be confident in our professional approach and worthy presentation of your property.")|html}
  </div>
	<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">
    {$lang->get("Отправить заявку", "Send request")|html}
  </a>
</div>

{*{if !empty($page_posts.main_post) && $page_posts.main_post.status == 'close'}
    <h4>{$page_posts.main_post.title}</h4>
    <p>{$page_posts.main_post.annotation}</p>
    <div>{$page_posts.main_post.text|html}</div>
{/if}*}