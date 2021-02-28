{* {?$lang = $request_segment.key}  *}
{if $request_segment.key == 'ru'}
  {?$pageTitle = 'О компании | М16-Недвижимость'}
  {?$pageDescription = 'О компании — все о преимуществах, принципах работы с клиентами, сотрудниках и основателях агентства недвижимости М16'}
{else}
  {?$pageTitle = 'Company Presentation | M16 Real Estate Agency'}
  {?$pageDescription = 'Company Presentation — all about our benefits, how do we work with customers, our employees and the founders of M16 Real Estate Agency'}
{/if}

<section class="company-sc company-sc1 sc">

  <div class="company-sc1-upp top-bg m-white">
    <div class="site-top">
      <h1 class="title" title="{$lang->get("М16 — элитная", "M16 - elite")} {$lang->get("недвижимость", "real estate")}">
      <span class="title-top"> {$lang->get("М16 — элитная", "M16 - elite")} </span>
      <div class="title-bottom">
      {$lang->get("недвижимость", "real estate")|html}
      </div>
      </h1>
      <div class="main">
      {$lang->get("в санкт-петербурге", "in St.Petersburg")|html}
      </div>
    </div>
  </div>

  <div class="company-sc1-low">

    <h2 title="Агентство недвижимости М16 появилось на рынке">
      <span>{$lang->get("Агентство недвижимости М16 появилось на рынке", "Real-estate agency M16 entered the market")|html}</span>
    </h2>

    <h3>
      {$lang->get("1 марта 2013 года", "on March 1st, 2013")|html}
    </h3>


  </div>
	{if ($request_segment.key == 'ru' && !empty($site_config.company_pdf_ru)) || (!empty($site_config.company_pdf_en) && $request_segment.key != 'ru')}
	<div class="more-row a-center">
		<a rel="nofollow" target="_blank" href="{if $request_segment.key == 'ru' && !empty($site_config.company_pdf_ru)}{$site_config.company_pdf_ru->getUrl()}{elseif !empty($site_config.company_pdf_en)}{$site_config.company_pdf_en->getUrl()}{/if}" class="see-more">
			{fetch file=$path . "presentation.svg"}
			{$lang->get('Скачать презентацию', 'Company presentation')}
		</a>
	</div>
	{/if}
</section>


<section class="sc company-sc company-sc2">

  <div class="company-sc2-wrap">

    <div class="company-sc2-lead">
      {$lang->get("Всего за несколько лет работы компания смогла занять прочные позиции на весьма непростом рынке  петербургской недвижимости.", "In the span of just several years, the company has firmly established itself on the extremely complex market of St.Petersburg real estate.      ")|html}
    </div>

    <div class="company-sc2-desc">

      <div class="company-sc2-title">
        {$lang->get("Вячеслав Малафеев", "Vyacheslav Malafeyev")|html}
      </div>

      <div class="company-sc2-caption">

        {$lang->get("основатель компании, голкипер «Зенита», <br>накопил немалый опыт не только в футболе, но и в работе с недвижимостью:", "The company’s founder, a goalkeeper for Zenit, <br>has gained a great experience not only in football, <br>but also in real estate deals:")|html}
      </div>

    </div>



    <div class="company-sc2-slider">

      <div class="swiper company-sc2-swiper">
        <div class="swiper-container">
          <div class="swiper-wrapper">

			<div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">
                    {$lang->get("«<b>Н</b>а протяжении почти всей карьеры я что-то покупал, продавал, пытался улучшить жилищные условия… Много читал, начал разбираться в недвижимости и осознавать перспективы. Пришел к решению: почему бы не создать свое агентство? Есть связи и команда специалистов, есть определенные возможности помочь людям находить то, что им нужно».", "«<b>D</b>uring almost all my career, I was buying and selling something, trying to improve my housing facilities… I put up the capital into commercial property, too. I read a lot and learnt the ropes in the subject and realized the prospects.            Little by little, I arrived at a decision: why not create my own agency? I was well-connected, had a team of specialists and had an opportunity to help people find what they needed».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>С</b> людьми, которые приобретают элитную недвижимость, я разговариваю на одном языке. Мне понятны их желания».", "«<b>I</b> speak the same language with those people who purchase premium property. I understand their desires».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                  {$lang->get("«<b>В</b> мире есть только одно беспроигрышное вложение: ты сам».", "«<b>T</b>here is only one win-win investment in the world — yourself».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Д</b>ля меня очень важно, чтобы сервис в агентстве всегда был на максимально высоком уровне. Не важно, покупает клиент маленькую студию или стометровую квартиру — сервис должен всегда быть на высоте».", "«<b>F</b>or me, it is very important that the service in the agency be always at the highest level. No matter whether the client buys a small studio or a spacious 100-square meter flat — the service should always be up to the mark».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Я</b> в первую очередь говорю сотрудникам: «Если это твое, то оно должно тебе нравиться. Если тебе это не нравится, то тогда лучше поискать что-то другое».", "«<b>F</b>irst of all, I say to my employees, «If this thing is yours, it should be up to your taste. If you do not like it, then you need to look for something else».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Я</b> человек креативный и целеустремленный, мне интересно двигаться вперед».", "«<b>I</b> am a creative and purposeful person and I am interested in moving forward».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Б</b>изнес-процессы должны быть идеально отточены. Мы к этому стремимся».", "«<b>B</b>usiness processes should be perfect. We are longing for it».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Я</b> хотел создать бизнес, который будет не только приносить деньги, но и будет мне нравиться, как футбол».", "«<b>I</b> would like to build up the business that will not only produce a profit, but also please me, as much as football».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Я</b> считаю, что за качественный сервис и юридическую чистоту при сделках мы должны отвечать в первую очередь».", "«<b>I</b>n my opinion, first of all we should be in charge of qualitative service and legal clarity of transactions».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Н</b>а данный момент с учетом ситуации на рынке я бы посоветовал вкладываться в элитку».", "«<b>A</b>t the given moment, with due account for market situation I would advise investing money in luxury property».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Р</b>еакция, быстрота мышления, широкий кругозор и интуиция — все эти навыки одинаково важны и в футболе, и в бизнесе».", "«<b>Q</b>uick reflexes, mental speed, spacious mind and intuition — all these attainments are similarly important both in football and in business».")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Б</b>есплатное проживание для клиентов  —  это уникальная услуга в Санкт-Петербурге».", "«<b>F</b>ree overnight stay is a unique service in St.Petersburg».")|html}


                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>В</b>сем движет страсть! Нет страсти — нет успеха в любом деле!»", "«<b>P</b>assion fuels everything that we do! If you have no passion, there will be no success wheresoever!»")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>Э</b>то увлеченность, граничащая с любовью. Я получаю от своей новой деятельности не меньшее удовольствие, чем от футбола, и часто говорю своим сотрудникам: “Если вы не влюблены в свое дело, значит эта работа не ваша!»", "«<b>I</b>t is dedication on the verge of love. My new work gives me no less delight than football does, and I often say to my employees, “If you are not in love with what you do, this work is not for you!»")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>М</b>ечты и преданность делу — мощная комбинация, которая реализует твои цели в реальность!»", "«<b>D</b>reams and commitment build up a potent combination, which can convert your purposes into a reality!»")|html}

                  </div>
                </div>
              </div>
            </div>

            <div class="swiper-slide">
              <div class="table-block">
                <div class="cell-block">
                  <div class="company-sc2-quote">

                    {$lang->get("«<b>О</b>тветственность — это именно то, что помогает нам расти в этом мире».", "«<b>R</b>esponsibility is the very thing that helps us grow under the Sun».")|html}

                  </div>
                </div>
              </div>
            </div>

          </div>

          <div class="swiper-pagination swiper-pagination-clickable"></div>
        </div>
        <div class="swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
        <div class="swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
      </div>

    </div>


  </div>

</section>

<section class="company-sc company-sc3 sc">

  <div class="company-sc1-low company-sc1-heading">
    <h2>
      {$lang->get("В команде «М16 Недвижимость» <br>работают высококвалифицированные",            "The team of M16-Estate <br>are highly qualified")|html}
      </h2>
      <h3>
      {$lang->get("профессионалы", "professionals")}
      </h3>
  </div>

  <div class="company-sc3-desc">
    {$lang->get("      Важнейшая задача наших специалистов — обеспечить безопасность при оформлении сделок и предоставить высокий уровень сервиса.  <br>Мы всегда соблюдаем стандарты качества.     ", "      The main task of our specialists is to provide security in the processof transaction processing and offer the clients a high level of service. <br>We always uphold the standards of quality.     ")|html}
  </div>

  <div class="company-sc3-control">
    <div class="send-req appeared">
		<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">

    {$lang->get('Оставить заявку','Send your request')}


    </a>
    </div>
  </div>








  <div class="company-article_group">

    <div class="company-article cfix">

      <div class="company-article-desc">
        {$lang->get("Приоритетной деятельностью компании М16 является работа с недвижимостью элитного сегмента. Выбирая для вас квартиру или дом премиум-класса, мы всегда действуем исходя из ваших пожеланий. В нашей базе широко представлены квартиры в центре города, на Крестовском острове, на Петроградке, а также в живописных уголках всего земного шара. В настоящее время вложение денежных средств в элитную недвижимость является самым надежным и выгодным с учетом ситуации на рынке.", "The business priority of М16-Estate is dealing with luxury segment realty. Choosing the flat or the house for you we always act in view of your requests. In our base, there is a wide choice of flats situated in the centre of the city, on Krestovsky Island, in the most picturesque parts of the Earth. At the present day, investment of capital into premium property is the most profitable and safe variant in respect of market situation.")|html}
      </div>

      <div class="company-article-author" style="display:none">

        <div class="company-article-author-pic">
          <img src="/img/company-worker1.jpg" alt="">
        </div>
        <div class="company-article-author-name">
          {$lang->get("Антон Чванов", "Anton Chvanov")|html}

        </div>
        <div class="company-article-author-caption">
          {$lang->get("Руководитель отдела <br>элитной недвижимости в M16", "Head of Luxury <br>Property Department")|html}
        </div>

      </div>

    </div>



    <div class="company-article company-article-invert cfix">

      <div class="company-article-desc">

        {$lang->get("У нас представлен широкий выбор квартир премиум-класса. Мы тщательно следим за юридической чистотой представленных объектов. Мы всегда работаем в интересах клиента, поэтому берем на себя полное юридическое сопровождение сделки, действуем максимально оперативно.", "There is a wide choice of upscale apartments. We pay close attention to good title to the objects represented. We always serve the interests of the Client; this is why we take personal charge of complete legal support of deals and act as promptly as possible.")|html}

      </div>

      <div class="company-article-author">

        <div class="company-article-author-pic">
          <img src="/img/company-worker-4.png" alt="">
        </div>
        <div class="company-article-author-name">
          {$lang->get("Екатерина Ермолова", "Svetlana Diakova")|html}

        </div>
        <div class="company-article-author-caption">
          {$lang->get("Руководитель отдела  <br>вторичной  недвижимости в M16", "Head of Real Estate <br>Property Department")|html}

        </div>

      </div>

    </div>








  </div>


</section>

<section class="sc company-sc company-sc4">

  <span class="parallax-bg" data-parallax="scroll" data-bleed="-260" data-image-src="/img/ekaterina_mini2.jpg" data-natural-width="1200" data-natural-height="548" data-speed="0.4"></span>


  <article class="company-sc4-quote">

    <div class="company-sc4-text">

      {$lang->get("— Только творчество в сочетании с самодисциплиной позволяют достичь успеха в бизнесе.", "— One should love his or her job and be able to work even when having a rest.")|html}

    </div>

    <div class="company-sc4-author">

      {$lang->get("Екатерина Малафеева", "Ekaterina Malafeyeva")|html}

    </div>

    <div class="company-sc4-status">

      {$lang->get("Директор «М16 Недвижимость»", "Director of М16")|html}

    </div>

  </article>


</section>

<section class="sc company-sc company-sc5">

  <div class="company-article_group">

    <div class="company-article cfix">

      <div class="company-article-desc" style="text-align: center; width: 100%;">
        {$lang->get("Рынок недвижимости не стоит на месте и каждый день преподносит нам новые сюрпризы. Изменяются тренды, потребности клиента, предложения от застройщиков, способы взаимодействия. Мы следим за всеми изменениями и движемся только вперед!", "The property market does not stand still and every day springs new surprises to us. Everything is changing — the trends, customers’ needs, developers' offers and ways of communication. We track all the changes and never look back!")|html}
      </div>

      <!--<div class="company-article-author">

        <div class="company-article-author-pic">
          <img src="/img/company-worker3.jpg" alt="">
        </div>
        <div class="company-article-author-name">
          {$lang->get("Герман Ашмарин", "German Ashmarin")|html}
        </div>
        <div class="company-article-author-caption">
          {$lang->get("Директор по развитию в M16", "Development Director")|html}
        </div>

      </div>-->

    </div>


    {*<div class="company-article company-article-invert cfix">*}

      {*<div class="company-article-desc">*}
        {*{$lang->get("Организация успешной деятельности компании во многом зависит от грамотного управления сотрудниками и взаимоотношений в коллективе. Каждый сотрудник «М16 Недвижимость» занимается любимой сферой деятельности и каждый сотрудник находится на своем месте.", "A strategic management of the Company’s successful functioning depends to a large extent on the competent direction over the employee and the personnel relations. Every member of M16-Estate is busy with his favorite occupation and takes a proper place.")|html}*}
      {*</div>*}

      {*<div class="company-article-author">*}

        {*<div class="company-article-author-pic">*}
          {*<img src="/img/company-worker4.jpg" alt="">*}
        {*</div>*}
        {*<div class="company-article-author-name">*}
          {*{$lang->get("Александр <br>Комаров", "Alexandr Komarov")|html}*}
        {*</div>*}
        {*<div class="company-article-author-caption">*}
          {*{$lang->get("Генеральный директор в M16", "General Director")|html}*}
        {*</div>*}

      {*</div>*}

    {*</div>*}




  </div>

</section>


<section class="sc company-sc company-sc6">

  <h2 class="company-sc6-h" title="11 Принципов М16">
    {$lang->get("11 Принципов М16", "Fundamentals of M16")|html}
  </h2>

  <div class="company-sc6-block">

    <div class="company-sc6-bg">
      <div class="company-sc6-bg-content"></div>
    </div>

    <div class="company-sc6-content">

        <div class="company-sc6-slider">
          <div class="swiper company-sc6-swiper">
            <div class="swiper-container">
              <div class="swiper-wrapper">

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      1
                    </div>
                    <div class="company-sc6-rule-desc">

                      {$lang->get("М16 — это не работа, а призвание", "M16 is not a work, but vocation")|html}

                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Мы любим свою компанию и готовы развиваться вслед за изменениями рынка и за потребностями наших клиентов.", "We love our company and we are ready to go forward on the morrow of market changes and the demands of our clients.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      2
                    </div>
                    <div class="company-sc6-rule-desc">
                      {$lang->get("Мы — команда", "We are a team")|html}
                    </div>
                    <div class="company-sc6-rule-sub">
                      {$lang->get("Мы всегда готовы работать вместе со своим клиентом и<br>с учетом его интересов.", "We are always ready to work with the client, taking into account his or her interests.")|html}
                    </div>
                  </div>
                </div>




                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      3
                    </div>
                    <div class="company-sc6-rule-desc">
                      {$lang->get("Траектория успеха — это наш путь", "Our way is the trajectory of success")|html}

                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Мы не боимся даже самых сложных сделок, потому что сложности — это единственный путь к развитию.", "We are not afraid of the most complex transactions, because difficulties are the only way to development.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      4
                    </div>
                    <div class="company-sc6-rule-desc">

                      {$lang->get("Делайте то, что любите! Любите то, что делаете!", "Do what you love! Love what you do!")|html}

                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Каждый наш сотрудник с любовью относится к выбранному делу, понять это можно, переступив через порог нашей компании.", "Every member of our staff is fond of his work. To feel it you just need to cross the threshold of our company.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      5
                    </div>
                    <div class="company-sc6-rule-desc">

                      {$lang->get("Все в ваших руках!", "Everything is in your hands! ")|html}

                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Ответственное и внимательное отношение к каждой сделке позволяет нам добиваться отличных результатов.", "Responsible and diligent attitude to every transaction lets us achieve perfect results.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      6
                    </div>
                    <div class="company-sc6-rule-desc">
                      {$lang->get("Время — это деньги!", "Time is money!")|html}
                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Мы ценим ваше время, поэтому решаем все вопросы с максимальной оперативностью.", "We know the value of your time; this is why we do all the tasks as fast as possible.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      7
                    </div>
                    <div class="company-sc6-rule-desc">

                      {$lang->get("Суди о прожитом дне не по урожаю, который ты собрал, а по семенам, которые ты посеял в этот день", "Estimate every day of your life not by the crops you’ve gathered, but by the seeds you’ve set")|html}

                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Любая сделка с недвижимостью — это сложный поэтапный процесс, и ответственное отношение к каждому этапу приводит к достижению поставленной цели.", "Every real estate transaction is a complicated step-by-step process, and a responsible attitude to its every stage leads to successful achievement of the company’s goals.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      8
                    </div>
                    <div class="company-sc6-rule-desc">

                      {$lang->get("Знания — это сила", "Knowledge is power")|html}

                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Наши сотрудники имеют многолетний стаж работы, обладают исчерпывающей информацией об объектах и застройщиках, следят за всеми обновлениями рынка недвижимости.", "The members of our team have a long employment history, possess comprehensive information on objects and developers, and keep a wary eye on all the renovations on property market.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      9
                    </div>
                    <div class="company-sc6-rule-desc">
                      {$lang->get("Сервис начинается с улыбки!", "Service begins with a smile!")|html}
                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Работа с недвижимостью это прежде всего работа с людьми, поэтому профессионализм невозможен без искреннего доброжелательного отношения к клиенту.", "Dealing with real estate is, first of all, people business, therefore there can not be any professionalism without sincere benevolence towards the client.")|html}



                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      10
                    </div>
                    <div class="company-sc6-rule-desc">
                      {$lang->get("Бог дает человеку не то, что он хочет, а то, что ему надо", "The God gives a person not what he wants, but what he needs")|html}

                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Поэтому не спрашивайте: «за что?», а подумайте: «для чего?».", "That’s why you should not ask “Why?”, you should think “What for?”.")|html}

                    </div>
                  </div>
                </div>

                <div class="swiper-slide">
                  <div class="company-sc6-rule">
                    <div class="company-sc6-rule-number">
                      11
                    </div>
                    <div class="company-sc6-rule-desc">
                      {$lang->get("Если ты хочешь продать что-нибудь,<br>продай сначала себя", "If you want to sell something,<br>try to sell yourself first of all")|html}
                    </div>
                    <div class="company-sc6-rule-sub">

                      {$lang->get("Успешность компании во многом строится на взаимодействии менеджера и клиента. Доверительные отношения и искренняя заинтересованность наших сотрудников в успехе сделки помогают поддерживать безупречную репутацию «М16 Недвижимость» и привлекать новых клиентов.", "The success of the company is built up to a large extent on cooperation of the manager and the client. Trust-based relations and sincere commitment of our team members in success of the transaction help us maintain the untarnished reputation of M16-Estate and attract new clients.")|html}

                    </div>
                  </div>
                </div>



              </div>

            </div>

            <div class="swiper-controls">
              <div class="swiper-counter">
                <span class="swiper-counter-active"></span>
                <span class="swiper-counter-slash">/</span>
                <span class="swiper-counter-length"></span>
              </div>

              <div class="swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
              <div class="swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
            </div>

          </div>
        </div>

    </div>

  </div>

</section>


 <section class="sc company-sc company-sc7">

   <div class="section-title">
    <div class="svg-wrap">
      {fetch file=$path . "service.svg"}
    </div>
    <div class="lead">
      {$lang->get("Индивидуальный подход", "Individual approach")|html}
    </div>
    <h2 class="title" title="Flat Trade-In">
      <span>{$lang->get("И высочайший уровень сервиса", "And the highest level of service")|html}</span>
    </h2>
    <div class="descr">

      {$lang->get("Мы любим своих клиентов, поэтому обеспечиваем для вас уровень сервиса международного класса", "We love our clients, therefore we provide you the service of world-class level:")|html}

    </div>
   </div>

   <div class="small-text-wrap">

    <div class="small-text-block">
      <div class="main">
        {$lang->get("Комплексное <br>решение задач", "Comprehensive approach to issues")|html}
      </div>
      <div class="descr">
        {$lang->get("Любая сложность и ориентированность на интересы клиента", "Any degree of any complexity and orientation to the client’s interests")|html}
       </div>
    </div>
    <div class="small-text-block">
      <div class="main">
        {$lang->get("Предоставление актуальной информации", "Submission of update information in any client-friendly manner")|html}
      </div>
      <div class="descr">

        {$lang->get("Наша реклама постоянно обновляется, мы готовы предоставить вам всю информацию о заинтересовавших вас объектах недвижимости.", "Our advertisement is constantly renovated; we are always ready to offer you all the necessary data about the real estate items you are interested in.")|html}

      </div>
    </div>
    <div class="small-text-block">
      <div class="main">
        {$lang->get("Предпродажная подготовка", "Pre-sale preparation of real estate")|html}
      </div>
      <div class="descr">

      {$lang->get("Клининговые услуги, ремонтные работы, профессиональная фотосъемка, создание 3D-тура и многое другое.", "Сleaning services, remedial works, professional photography, creation of virtual (3D) tours and so on.")|html}

      </div>
    </div>
    <div class="small-text-block">
      <div class="main">
        {$lang->get("Автомобиль бизнес-класса ", "Executive car")|html}
      </div>
      <div class="descr">

        {$lang->get("Мы предоставляем автомобиль бизнес-класса с личным водителем для просмотра объектов.", "With a personal driver for examining the objects.")|html}

      </div>
    </div>

    <div class="small-text-block">
      <div class="small-text-block-mark">
        <div class="skew m-sand-skew">Exclusive</div>
      </div>
      <div class="main">
        {$lang->get("Бизнес-тур на вертолете", "Helicopter tour above the premium class property.")|html}
      </div>
    </div>

    <div class="small-text-block">
      <div class="main">{$lang->get("Персональный менеджер", "There will be a personal account manager")|html} </div>
      <div class="descr">
      {$lang->get("С вами работает персональный менеджер, который всегда знает, что нужно именно вам.", "To deal with you; and he will always know what you personally need.")|html}
      </div>
    </div>

    <div class="small-text-block">
      <div class="main">

      {$lang->get("Для гостей <br>города", "Transfer service")|html}


      </div>
      <div class="descr">

      {$lang->get("Трансфер и размещение в отелях класса люкс.", "And accommodation of city visitors in luxury hotels.")|html}

      </div>
    </div>

    <div class="small-text-block">
      <div class="main">
      {$lang->get("Мы ценим <br>ваше время", "We appreciate your time")|html}
      </div>
      <div class="descr">
      {$lang->get("Все сделки совершаются максимально оперативно.", "All the transactions are made as promptly as possible.")|html}
      </div>
    </div>

   </div>

 </section>

<section class="sc company-sc company-sc8">

  <div class="section-title">
   <div class="svg-wrap">
     {fetch file=$path . "bezopasn.svg"}
   </div>
   <div class="lead">
    {$lang->get("Безопасность сделки", "Efficient customer advocacy")|html}
   </div>
   <h2 class="title" title="Flat Trade-In">
     <span>
     {$lang->get("И защита интересов клиента", "And protection of transactions")|html}
     </span>
   </h2>
   <div class="descr">
    {$lang->get("Благодаря уникальным возможностям и многочисленным надежным партнерам компании", "Through the unique possibilities and numerous reliable partners of the Company")|html}
   </div>
  </div>

  <div class="small-text-wrap">

   <div class="small-text-block">
     <div class="main">
      {$lang->get("Юридическая экспертиза", "Valuation and due diligence")|html}
     </div>
     <div class="descr">
      {$lang->get("Оценка и юридическая экспертиза недвижимости, профессиональные консультации.", "Real estate and professional consultations.")|html}
     </div>
   </div>
   <div class="small-text-block">
     <div class="main">
      {$lang->get("Персональный инвестиционный пакет", "Formation of the private investment package")|html}
     </div>
     <div class="descr">
      {$lang->get("Формирование персонального инвестиционного пакета и контроль эффективности инвестиций.", "And control of return on investment.")|html}
     </div>
   </div>
   <div class="small-text-block">
     <div class="main">Private <br>banking</div>
     <div class="descr">
      {$lang->get("Private banking от крупнейших международных и российских банков.", "Private banking from the largest international and Russian banks.")|html}
      </div>
   </div>
   <div class="small-text-block">
     <div class="small-text-block-mark">
       <div class="skew m-sand-skew">Exclusive</div>
     </div>
     <div class="main">
      {$lang->get("Своя служба безопасности.", "Private security service.")|html}
     </div>
   </div>

  </div>

</section>

<section class="sc company-sc company-sc9">

  <div class="section-title">
   <div class="svg-wrap">
     {fetch file=$path . "assort.svg"}
   </div>
   <div class="lead">
    {$lang->get("Широкий ассортимент", "The wide range")|html}
   </div>
   <h2 class="title" title="Flat Trade-In">
     <span>
     {$lang->get("Недвижимости класса премиум", "of premium-class property items")|html}
     </span>
   </h2>
   <div class="descr">
     {$lang->get("и эксклюзивные предложения в Санкт-Петербурге", "and exclusive offers in St.Petersburg")|html}
    </div>
  </div>

  <div class="small-text-wrap">

   <div class="small-text-block">
     <div class="main">
        {$lang->get("Мы всегда <br>в тренде", "We set new trends on market")|html}
      </div>
     <div class="descr">
        {$lang->get("Участие в формировании тенденций в сфере элитной недвижимости.", "Participation in tendency formation in the sphere of premium property.")|html}
      </div>
   </div>
   <div class="small-text-block">
     <div class="main">
      {$lang->get("Выкуп элитной недвижимости", "Acquisition of premium property")|html}
     </div>
     <div class="descr">
      {$lang->get("До 90% рыночной стоимости недвижимости в максимально сжатые сроки.", "You get up to 90% of the market value of the object.")|html}
     </div>
   </div>
   <div class="small-text-block">
     <div class="main">
      {$lang->get("Инвестиции <br>в недвижимость", "Investment in real property.")|html}
     </div>
     <div class="descr">
      {$lang->get("Мы предлагаем программы по инвестированию денежных средств в недвижимость.", "M16-Estate offers the programs on investment of monetary resources into real estate. There are special offers for the clients dealing with luxury property.")|html}
     </div>
   </div>
   <div class="small-text-block">
     <div class="main">
      {$lang->get("Большой <br>опыт работы", "Solid grounding in premium property segment.")|html}
     </div>
     <div class="descr">
      {$lang->get("Мы работаем как с первичным, так и со вторичным рынком недвижимости премиум-класса, разбираемся во всех нюансах загородной недвижимости.", "We deal both with new construction and resale real estate markets and we have a good handle in all the peculiarities of countryside real estate.")|html}
     </div>
   </div>
  </div>

  <div class="company-sc9-sub">
    {$lang->get("Рынок недвижимости не стоит на месте. Мы развиваемся вместе с ним и постоянно <br>готовим для вас новые предложения.", "Property market does not stand still. Our company makes progress together with <br>it and constantly creates new offers for you.")|html}
  </div>

</section>

<section class="sc company-sc company-sc10">


  <h3 title="Миссия и репутация" class="company-sc10-hd">
    {$lang->get("Миссия и репутация", "Our Mission and Reputation")|html}
  </h3>

  <div class="art-tiles">

    {* m-rotated  *}

    <div class="tiles-inner" >

      <div class="article art-tile art-1 m-white tile m-rotated" style="background-image: url(/img/test-company-tile-bg1.jpg);">

        <span class="tile-cover">
          <span class="table-block">
            <span class="cell-block">

              <div class="company-tile-desc">
                <div class="company-tile-desc-hd">
                  {$lang->get("На первом месте интересы клиента", "The interests of the client are in the first flight for us.")|html}
                </div>
                <div class="company-tile-desc-caption">
                  {$lang->get("Миссия компании <br>«М16 Недвижимость» — предоставить клиентам все возможные варианты недвижимости и сделки. Вы будете уверены, что выбрали лучшее!", "The mission of M16-Estate <br>is to offer the customers a great choice of real estate and transaction. Be sure you’ve chosen the best!")|html}
                </div>
              </div>

            </span>
          </span>
        </span>

        <div class="content">
          <div class="btn m-black">
            {$lang->get("Интересы клиента", "The interests of the client")|html}
          </div>
        </div>

      </div>
      <div class="article art-tile art-2 m-black tile m-rotated " style="background-image: url(/img/test-company-tile-bg2.jpg);">
        <div class="content">
          <div class="btn m-black">
            {$lang->get("Достойный подход", "ndividual approach")|html}
          </div>
        </div>

        <span class="tile-cover">
          <span class="table-block">
            <span class="cell-block">

              <div class="company-tile-desc">
                <div class="company-tile-desc-hd">
                  {$lang->get("Индивидуальный подход", "Individual approach")|html}
                </div>
                <div class="company-tile-desc-caption">
                  {$lang->get("Оформляя сделку с компанией «М16 Недвижимость», вы можете быть уверены, что мы предложим вам варианты с учетом всех ваших пожеланий к планировке квартиры, району проживания, этажности дома, наличию паркинга.", "Executing a transaction with M16-Estate you can be sure that we will offer you the variants with due regard to all your preferences towards the flat planning, district, number of floors and presence of parking area.")|html}
                </div>
              </div>

            </span>
          </span>
        </span>


      </div>
      <div class="article art-tile art-3 m-black tile " style="background-image: url(/img/test-company-tile-bg3.jpg);">
        <div class="content">
          <div class="btn m-black">
            {$lang->get("Гарантия Малафеева", "Guarantee from Malafeyev")|html}
          </div>
        </div>

        <span class="tile-cover">
          <span class="table-block">
            <span class="cell-block">

              <div class="company-tile-desc">
                <div class="company-tile-desc-hd">
                  {$lang->get("Гарантия качества от Малафеева", "Guarantee of quality from Malafeyev.")|html}
                </div>
                <div class="company-tile-desc-caption">
                  {$lang->get("Известность владельца компании гарантирует ее безупречную репутацию и ответственность перед каждым клиентом.", "The serious reputation of the Company owner ensures its clean slate and liability to every client.")|html}
                </div>
              </div>

            </span>
          </span>
        </span>


      </div>
      <div class="article art-tile art-4 m-white tile" style="background-image: url(/img/test-company-tile-bg4.jpg);">
        <div class="content">
          <div class="btn m-black">
            {$lang->get("Европейский сервис", "European level service")|html}
          </div>
        </div>

        <span class="tile-cover">
          <span class="table-block">
            <span class="cell-block">

              <div class="company-tile-desc">
                <div class="company-tile-desc-hd">
                  {$lang->get("Сервис европейского класса", "European level service")|html}
                </div>
                <div class="company-tile-desc-caption">
                  {$lang->get("Мы берем на себя все организационные вопросы: проживание иногородних клиентов, трансфер до объекта недвижимости, экскурсии по объектам.", "We take personal charge of all the organizational issues: accommodation of non-resident clients, transfer service to the item of immovable property and site tours.")|html}
                </div>
              </div>

            </span>
          </span>
        </span>

      </div>
      <div class="article art-tile art-5 m-black tile" style="background-image: url(/img/test-company-tile-bg5.jpg);">
        <div class="content">
          <div class="btn m-black">
            {$lang->get("Участие в жизни города", "Involvement in the city life")|html}
          </div>
        </div>

        <span class="tile-cover">
          <span class="table-block">
            <span class="cell-block">

              <div class="company-tile-desc">
                <div class="company-tile-desc-hd">
                  {$lang->get("Активное участие в жизни города", "Active Involvement in the life of the city")|html}
                </div>
                <div class="company-tile-desc-caption">
                  {$lang->get("Компания «М16 Недвижимость» активно принимает участие в городских и благотворительных проектах.", "M16-Estate actively participates in the city and goodwill projects.")|html}
                </div>
              </div>

            </span>
          </span>
        </span>

      </div>
      <div class="article art-tile art-6 m-white tile m-rotated" style="background-image: url(/img/test-company-tile-bg6.jpg);">
        <div class="content">
          <div class="btn m-black">
            {$lang->get("Доверие", "Trust")|html}
          </div>
        </div>

        <span class="tile-cover">
          <span class="table-block">
            <span class="cell-block">

              <div class="company-tile-desc">
                <div class="company-tile-desc-hd">
                  {$lang->get("Доверие", "Trust")|html}
                </div>
                <div class="company-tile-desc-caption">
                  {$lang->get("Многие из наших клиентов обращаются в компанию снова и снова или приводят своих знакомых.", "Many of our clients contact us again and again or write their acquaintances up for dealing with us.")|html}
                </div>
              </div>

            </span>
          </span>
        </span>

      </div>
      <div class="article art-tile art-7 m-black tile m-rotated" style="background-image: url(/img/test-company-tile-bg7.jpg);">
        <div class="content">
          <div class="btn m-black">
            {$lang->get("Лучшие предложения", "Best offers")|html}
          </div>
        </div>

        <span class="tile-cover">
          <span class="table-block">
            <span class="cell-block">

              <div class="company-tile-desc">
                <div class="company-tile-desc-hd">
                  {$lang->get("Лучшие предложения в элитном сегменте", "Best offers in the segment of premium property")|html}
                </div>
                <div class="company-tile-desc-caption">
                  {$lang->get("Компания «М16 Недвижимость» является ведущей компанией в Санкт-Петербурге на рынке элитной недвижимости.", "M16-Estate is the anchor company in St.Petersburg in the market for high-end real estate.")|html}
                </div>
              </div>

            </span>
          </span>
        </span>

      </div>

    </div>



  </div>






</section>




