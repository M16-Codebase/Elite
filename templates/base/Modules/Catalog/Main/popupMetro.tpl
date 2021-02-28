<div class="popup-window" id="popup-metro" data-title="{if $request_segment.id==1}Выбор станций метро{else}Metro selection{/if}" data-class="popup-green">
	<div class="metro-scheme">
		<img src="/img/icons/metro-scheme.png" alt="metro" usemap="#metro-map" />
		<map class="metro-lines" name="metro-map">
			<area class="line-1" data-id="1" coords="364,19,370,19,375,24,376,30,375,35,370,41,369,46,369,55,369,78,369,103,369,143,369,157,369,186,369,210,369,212,372,213,374,216,375,219,375,221,374,224,371,228,369,229,365,230,361,230,349,242,334,258,334,260,335,263,334,267,333,270,329,272,325,274,320,272,318,270,320,273,316,275,278,313,279,312,280,314,280,317,279,321,275,324,269,326,267,325,259,332,249,342,236,355,223,368,217,374,218,376,218,380,217,384,212,388,207,388,203,388,199,391,194,398,190,401,188,404,186,408,185,416,186,421,186,453,186,487,186,520,186,523,188,527,191,531,193,535,193,540,191,546,186,549,182,550,177,549,174,546,172,542,171,538,172,533,175,528,177,525,178,521,178,501,178,484,178,440,178,425,179,417,178,413,179,406,181,400,182,396,188,391,198,382,199,379,201,383,202,383,207,378,214,372,211,370,215,364,225,354,237,342,251,327,261,318,264,320,276,309,273,307,295,284,315,264,317,265,319,269,331,256,326,254,356,223,359,225,370,214,366,212,361,213,361,211,361,157,361,90,361,44,359,41,355,36,353,32,354,25,357,22,361,19" shape="poly" href="#" />
			<area class="line-2" data-id="2" coords="207,18,213,20,216,22,219,28,219,33,215,39,212,43,211,51,212,72,212,124,212,192,212,210,218,215,218,221,217,226,212,229,212,242,211,244,208,243,209,252,201,257,204,260,211,260,212,265,212,327,212,368,215,371,215,373,203,385,206,387,211,385,212,386,212,454,212,513,212,524,217,532,220,537,218,545,211,549,205,550,200,547,197,542,196,535,200,529,203,524,204,517,204,485,204,440,204,387,199,382,198,377,199,373,202,369,204,369,203,330,204,284,204,261,200,257,198,250,201,245,203,242,204,228,200,226,213,212,210,211,205,211,204,210,204,148,204,95,204,45,201,40,196,33,197,25,201,20" shape="poly" href="#" />
			<area class="line-3" data-id="3" coords="18,219,20,213,23,208,31,207,37,210,42,214,45,214,50,215,77,215,160,215,182,215,188,216,199,216,200,212,204,210,212,209,215,212,218,216,241,215,301,215,356,215,357,214,359,212,361,210,366,210,371,212,372,214,374,217,375,220,393,237,402,246,409,253,414,261,416,270,415,301,415,340,416,341,418,342,418,344,405,356,408,357,415,357,415,373,415,431,415,505,415,524,419,530,422,535,422,539,420,545,415,549,409,549,404,547,399,541,401,532,407,525,407,515,407,434,407,379,407,357,402,354,401,348,404,343,408,339,407,295,407,270,405,263,396,252,386,242,372,227,371,226,375,220,372,214,359,226,358,227,356,224,356,223,284,223,217,223,216,220,217,215,213,213,201,225,198,223,187,222,188,216,181,216,182,223,112,223,64,223,48,223,43,223,35,229,31,230,25,229,21,227,20,224" shape="poly" href="#" />
			<area class="line-4" data-id="4" coords="452,549,458,544,460,541,461,535,457,532,453,525,452,475,452,448,452,404,452,391,449,382,445,377,422,354,421,352,421,347,420,342,418,340,417,342,410,350,404,355,402,350,401,345,407,339,368,300,347,279,334,266,335,262,334,258,332,255,325,252,321,252,319,254,312,249,295,250,218,250,216,244,211,241,207,240,207,244,207,252,211,253,215,257,220,258,282,258,305,258,309,258,314,260,315,262,315,265,317,268,319,270,324,264,332,257,334,260,334,266,331,269,327,273,340,284,372,316,400,343,400,344,403,353,405,357,409,359,415,358,428,372,441,384,444,391,444,423,444,474,444,523,441,529,436,535,438,543,441,548,447,550" shape="poly" href="#" />
			<area class="line-5" data-id="5" coords="178,21,182,18,189,19,192,21,195,26,196,30,195,36,191,42,189,48,189,70,189,98,189,139,189,172,189,216,188,223,192,230,197,236,202,242,204,244,199,250,201,257,208,250,217,257,256,296,266,305,267,306,271,305,275,306,276,308,276,310,264,321,267,323,273,323,274,324,274,366,273,405,270,405,266,405,266,369,267,323,262,320,260,316,261,311,243,295,211,261,210,260,206,260,202,259,199,256,199,249,197,248,190,241,185,236,183,230,181,226,181,218,181,182,181,156,181,121,181,87,181,69,181,49,180,45,178,40,176,36,173,32,174,25,176,23" shape="poly" href="#" />
		</map>
		<div class="metro-stations">
			<a class="station-70 line-1" data-line="1" data-id="70" style="top: 45px; left: 375px;" href="#">{if $request_segment.id==1}Девяткино{else}Devyatkino{/if}</a>
			<a class="station-15 line-1" data-line="1" data-id="15" style="top: 62px; left: 375px;" href="#">{if $request_segment.id==1}Гражданский пр.{else}Grazhdanskiy prospekt{/if}</a>
			<a class="station-16 line-1" data-line="1" data-id="16" style="top: 79px; left: 375px;" href="#">{if $request_segment.id==1}Академическая{else}Akademicheskaya{/if}</a>
			<a class="station-17 line-1" data-line="1" data-id="17" style="top: 96px; left: 375px;" href="#">{if $request_segment.id==1}Политехническая{else}Politekhnicheskaya{/if}</a>
			<a class="station-18 line-1" data-line="1" data-id="18" style="top: 113px; left: 375px;" href="#">{if $request_segment.id==1}Пл. Мужества{else}Ploschad' Muzhestva{/if}</a>
			<a class="station-19 line-1" data-line="1" data-id="19" style="top: 130px; left: 375px;" href="#">{if $request_segment.id==1}Лесная{else}Lesnaya{/if}</a>
			<a class="station-20 line-1" data-line="1" data-id="20" style="top: 147px; left: 375px;" href="#">{if $request_segment.id==1}Выборгская{else}Vyborgskaya{/if}</a>
			<a class="station-21 line-1" data-line="1" data-id="21" style="top: 164px; left: 375px;" href="#">{if $request_segment.id==1}Пл. Ленина{else}Ploschad' Lenina{/if}</a>
			<a class="station-22 line-1" data-line="1" data-id="22" style="top: 181px; left: 375px;" href="#">{if $request_segment.id==1}Чернышевская{else}Chernyshevskaya{/if}</a>
			<a class="station-23 line-1" data-line="1" data-id="23" data-related="40" style="top: 213px; left: 380px;" href="#">{if $request_segment.id==1}Пл. Восстания{else}Ploschad' Vosstaniya{/if}</a>
			<a class="station-24 line-1" data-line="1" data-id="24" data-related="63" style="top: 222px; left: 248px;" href="#">{if $request_segment.id==1}Владимирская{else}Vladimirskaya{/if}</a>
			<a class="station-25 line-1" data-line="1" data-id="25" data-related="32" style="top: 307px; left: 280px;" href="#">{if $request_segment.id==1}Пушкинская{else}Pushkinskaya{/if}</a>
			<a class="station-8 line-1" data-line="1" data-id="8" data-related="57" style="top: 347px; left: 6px;" href="#">{if $request_segment.id==1}Технологический институт — I{else}Tekhnologicheskiy institut — I{/if}</a>
			<a class="station-7 line-1" data-line="1" data-id="7" style="top: 422px; right: 427px;" href="#">{if $request_segment.id==1}Балтийская{else}Baltiyskaya{/if}</a>
			<a class="station-6 line-1" data-line="1" data-id="6" style="top: 439px; right: 427px;" href="#">{if $request_segment.id==1}Нарвская{else}Narvskaya{/if}</a>
			<a class="station-5 line-1" data-line="1" data-id="5" style="top: 456px; right: 427px;" href="#">{if $request_segment.id==1}Кировский завод{else}Kirovskiy zavod{/if}</a>
			<a class="station-4 line-1" data-line="1" data-id="4" style="top: 473px; right: 427px;" href="#">{if $request_segment.id==1}Автово{else}Avtovo{/if}</a>
			<a class="station-3 line-1" data-line="1" data-id="3" style="top: 490px; right: 427px;" href="#">{if $request_segment.id==1}Ленинский пр.{else}Leninskiy prospekt{/if}</a>
			<a class="station-1 line-1" data-line="1" data-id="1" style="top: 507px; right: 427px;" href="#">{if $request_segment.id==1}Пр. Ветеранов{else}Prospekt Veteranov{/if}</a>

			<a class="station-47 line-2" data-line="2" data-id="47" style="top: 45px; left: 218px;" href="#">{if $request_segment.id==1}Парнас{else}Parnas{/if}</a>
			<a class="station-48 line-2" data-line="2" data-id="48" style="top: 62px; left: 218px;" href="#">{if $request_segment.id==1}Пр. Просвещения{else}Prospekt Prosvescheniya{/if}</a>
			<a class="station-49 line-2" data-line="2" data-id="49" style="top: 79px; left: 218px;" href="#">{if $request_segment.id==1}Озерки{else}Ozerki{/if}</a>
			<a class="station-50 line-2" data-line="2" data-id="50" style="top: 96px; left: 218px;" href="#">{if $request_segment.id==1}Удельная{else}Udel'naya{/if}</a>
			<a class="station-51 line-2" data-line="2" data-id="51" style="top: 113px; left: 218px;" href="#">{if $request_segment.id==1}Пионерская{else}Pionerskaya{/if}</a>
			<a class="station-52 line-2" data-line="2" data-id="52" style="top: 130px; left: 218px;" href="#">{if $request_segment.id==1}Черная речка{else}Chyornaya rechka{/if}</a>
			<a class="station-53 line-2" data-line="2" data-id="53" style="top: 147px; left: 218px;" href="#">{if $request_segment.id==1}Петроградская{else}Petrogradskaya{/if}</a>
			<a class="station-54 line-2" data-line="2" data-id="54" style="top: 164px; left: 218px;" href="#">{if $request_segment.id==1}Горьковская{else}Gor'kovskaya{/if}</a>
			<a class="station-55 line-2" data-line="2" data-id="55" data-related="39" style="top: 183px; left: 218px;" href="#">{if $request_segment.id==1}Невский пр.{else}Nevskiy prospekt{/if}</a>
			<a class="station-56 line-2" data-line="2" data-id="56" data-related="12,31" style="top: 259px; right: 397px;" href="#">{if $request_segment.id==1}Сенная{else}Sennaya ploschad'{/if}</a>
			<a class="station-57 line-2" data-line="2" data-id="57" data-related="8" style="top: 362px; left: 6px;" href="#">{if $request_segment.id==1}Технологический институт — II{else}Tekhnologicheskiy institut — II{/if}</a>
			<a class="station-58 line-2" data-line="2" data-id="58" style="top: 405px; left: 218px;" href="#">{if $request_segment.id==1}Фрунзенская{else}Frunzenskaya{/if}</a>
			<a class="station-59 line-2" data-line="2" data-id="59" style="top: 422px; left: 218px;" href="#">{if $request_segment.id==1}Московские ворота{else}Moskovskiye vorota{/if}</a>
			<a class="station-60 line-2" data-line="2" data-id="60" style="top: 439px; left: 218px;" href="#">{if $request_segment.id==1}Электросила{else}Elektrosila{/if}</a>
			<a class="station-62 line-2" data-line="2" data-id="62" style="top: 456px; left: 218px;" href="#">{if $request_segment.id==1}Парк Победы{else}Park Pobedy{/if}</a>
			<a class="station-10 line-2" data-line="2" data-id="10" style="top: 473px; left: 218px;" href="#">{if $request_segment.id==1}Московская{else}Moskovskaya{/if}</a>
			<a class="station-9 line-2" data-line="2" data-id="9" style="top: 490px; left: 218px;" href="#">{if $request_segment.id==1}Звездная{else}Zvyozdnaya{/if}</a>
			<a class="station-2 line-2" data-line="2" data-id="2" style="top: 507px; left: 218px;" href="#">{if $request_segment.id==1}Купчино{else}Kupchino{/if}</a>

			<a class="station-46 line-3" data-line="3" data-id="46" style="top: 507px; right: 199px;" href="#">{if $request_segment.id==1}Рыбацкое{else}Rybatskoye{/if}</a>
			<a class="station-45 line-3" data-line="3" data-id="45" style="top: 490px; right: 199px;" href="#">{if $request_segment.id==1}Обухово{else}Obukhovo{/if}</a>
			<a class="station-44 line-3" data-line="3" data-id="44" style="top: 473px; right: 199px;" href="#">{if $request_segment.id==1}Пролетарская{else}Proletarskaya{/if}</a>
			<a class="station-43 line-3" data-line="3" data-id="43" style="top: 456px; right: 199px;" href="#">{if $request_segment.id==1}Ломоносовская{else}Lomonosovskaya{/if}</a>
			<a class="station-42 line-3" data-line="3" data-id="42" style="top: 439px; right: 199px;" href="#">{if $request_segment.id==1}Елизаровская{else}Yelizarovskaya{/if}</a>
			<a class="station-41 line-3" data-line="3" data-id="41" data-related="64" style="top: 320px; left: 423px;" href="#">{if $request_segment.id==1}Пл. Александра Невского — I{else}Ploschad' Aleksandra Nevskogo — I{/if}</a>
			<a class="station-40 line-3" data-line="3" data-id="40" data-related="23" style="top: 198px; left: 380px;" href="#">{if $request_segment.id==1}Маяковская{else}Mayakovskaya{/if}</a>
			<a class="station-39 line-3" data-line="3" data-id="39" data-related="55" style="top: 198px; left: 218px;" href="#">{if $request_segment.id==1}Гостиный двор{else}Gostiny dvor{/if}</a>
			<a class="station-38 line-3" data-line="3" data-id="38" style="top: 225px; left: 63px;" href="#">{if $request_segment.id==1}Василеостровская{else}Vasileostrovskaya{/if}</a>
			<a class="station-13 line-3" data-line="3" data-id="13" style="top: 198px; left: 52px;" href="#">{if $request_segment.id==1}Приморская{else}Primorskaya{/if}</a>

			<a class="station-68 line-4" data-line="4" data-id="68" style="top: 507px; left: 458px;" href="#">{if $request_segment.id==1}Ул. Дыбенко{else}Ulitsa Dybenko{/if}</a>
			<a class="station-67 line-4" data-line="4" data-id="67" style="top: 490px; left: 458px;" href="#">{if $request_segment.id==1}Пр. Большевиков{else}Prospekt Bol'shevikov{/if}</a>
			<a class="station-66 line-4" data-line="4" data-id="66" style="top: 473px; left: 458px;" href="#">{if $request_segment.id==1}Ладожская{else}Ladozhskaya{/if}</a>
			<a class="station-65 line-4" data-line="4" data-id="65" style="top: 456px; left: 458px;" href="#">{if $request_segment.id==1}Новочеркасская{else}Novocherkasskaya{/if}</a>
			<a class="station-69 line-4" data-line="4" data-id="69" style="top: 278px; left: 366px;" href="#">{if $request_segment.id==1}Лиговский пр.{else}Ligovskiy prospekt{/if}</a>
			<a class="station-64 line-4" data-line="4" data-id="64" data-related="41" style="top: 335px; left: 423px;" href="#">{if $request_segment.id==1}Пл. Александра Невского — II{else}Ploschad' Aleksandra Nevskogo — II{/if}</a>
			<a class="station-63 line-4" data-line="4" data-id="63" data-related="24" style="top: 236px; left: 248px;" href="#">{if $request_segment.id==1}Достоевская{else}Dostoyevskaya{/if}</a>
			<a class="station-12 line-4" data-line="4" data-id="12" data-related="31,56" style="top: 289px; right: 397px;" href="#">{if $request_segment.id==1}Спасская{else}Spasskaya{/if}</a>
			
			<a class="station-26 line-5" data-line="5" data-id="26" style="top: 45px; right: 424px;" href="#">{if $request_segment.id==1}Комендантский пр.{else}Komendantskiy prospekt{/if}</a>
			<a class="station-27 line-5" data-line="5" data-id="27" style="top: 62px; right: 424px;" href="#">{if $request_segment.id==1}Старая Деревня{else}Staraya Derevnya{/if}</a>
			<a class="station-28 line-5" data-line="5" data-id="28" style="top: 79px; right: 424px;" href="#">{if $request_segment.id==1}Крестовский остров{else}Krestovskiy ostrov{/if}</a>
			<a class="station-29 line-5" data-line="5" data-id="29" style="top: 96px; right: 424px;" href="#">{if $request_segment.id==1}Чкаловская{else}Chkalovskaya{/if}</a>
			<a class="station-11 line-5" data-line="5" data-id="11" style="top: 113px; right: 424px;" href="#">{if $request_segment.id==1}Спортивная{else}Sportivnaya{/if}</a>
			<a class="station-30 line-5" data-line="5" data-id="30" style="top: 242px; left: 80px;" href="#">{if $request_segment.id==1}Адмиралтейская{else}Admiralteyskaya{/if}</a>
			<a class="station-31 line-5" data-line="5" data-id="31" data-related="12,56" style="top: 274px; right: 397px;" href="#">{if $request_segment.id==1}Садовая{else}Sadovaya{/if}</a>
			<a class="station-32 line-5" data-line="5" data-id="32" data-related="25" style="top: 322px; left: 280px;" href="#">{if $request_segment.id==1}Звенигородская{else}Zvenigorodskaya{/if}</a>
			<a class="station-33 line-5" data-line="5" data-id="33" style="top: 339px; left: 280px;" href="#">{if $request_segment.id==1}Обводный канал{else}Obvodny kanal{/if}</a>
			<a class="station-35 line-5" data-line="5" data-id="35" style="top: 356px; left: 280px;" href="#">{if $request_segment.id==1}Волковская{else}Volkovskaya{/if}</a>
			<a class="station-36 line-5" data-line="5" data-id="36" style="top: 373px; left: 280px;" href="#">{if $request_segment.id==1}Бухарестская{else}Bukharestskaya{/if}</a>
			<a class="station-37 line-5" data-line="5" data-id="37" style="top: 390px; left: 280px;" href="#">{if $request_segment.id==1}Международная{else}Mezhdunarodnaya{/if}</a>

		</div>
	</div>
	<div class="buttons a-clearbox">
		<button class="save-metro a-btn-green a-right">{if $request_segment.id==1}Сохранить{else}Save{/if}</button>
		<a href="#" class="cancel-metro small-descr a-right">{if $request_segment.id==1}Отмена{else}Сancel{/if}</a>
	</div>
</div>