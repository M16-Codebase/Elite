{if !empty($wife) || !empty($item.malafeev_text) || !empty($item.description.text) || !empty($page_posts.main_post) && $page_posts.main_post.status == 'close'}
	<div class="about post{if !empty($wife)} m-two{/if}" id="about">
		{if !empty($wife) && empty($hide_main)}
			<h2 class="main"
                title="{if !empty($main_about_text)}{$main_about_text}{else}{$lang->get('Строящаяся элитная недвижимость в Санкт-Петербурге','Elite real estate under construction')}{/if}">
                {if !empty($main_about_text)}{$main_about_text}{else}{$lang->get('Строящаяся элитная недвижимость в Санкт-Петербурге','Elite real estate under construction')}{/if}
            </h2>
		{elseif empty($hide_main)}
			{if !empty($title)}<h2 class="main" title="{$lang->get('О жилом комплексе','About residential complex')} {$title}">{$lang->get('О жилом комплексе','About residential complex')} {$title}</h2>{/if}
		{/if}
		{if !empty($page_posts.main_post) && $page_posts.main_post.status == 'close'}
			{?$about_post = $page_posts.main_post}
		{elseif empty($items_list_flag) && !empty($item.description)}
			{?$about_post = $item.description}
		{/if}
		{if !empty($about_post.title)}
			<div class="title"><span>{$about_post.title|html}</span></div>
		{/if}
		{if !empty($about_post.annotation)}
			<h3 class="descr" title="{$about_post.annotation}">{$about_post.annotation|html}</h3>
		{/if}
		<div class="post-row{if !empty($about_post.text) && !empty($wife)} m-border{elseif !empty($about_post.text) && empty($item.malafeev_text)} m-dborder{elseif !empty($about_post.text)} m-border{/if}">
			{if !empty($about_post.text)}
				<div class="text{if empty($item.malafeev_text) && empty($wife)} m-center{/if}">
					{$about_post.text|html}
				</div>
			{/if} 
			{if !empty($wife)} 
				<div class="opinion{if empty($about_post.text)} m-center{/if}">
					<p class="check-text">
						{$lang->get('Максимальная ориентация <br>на интересы клиента и высокий <br>уровень сервиса', 'Maximum attention <br/>to client\'s needs and <br />high level of service')|html}
					</p>
					<p class="check-text">
						{$lang->get('Эффективная защита интересов <br>клиента, конфиденциальность <br>и безопасность сделки ', 'We effectively protect intersests<br /> of each client and provide <br />top security of each deal')|html}
					</p>
					<div class="main">{$lang->get('Вячеслав <br>и Екатерина <br>Малафеевы', 'EKATERINA<br/>& VYACHESLAV<br/>MALAFEYEV')|html}</div>
					<div class="descr">{$lang->get('Владельцы агентства<br>недвижимости M16','Owners of <br />M16 Real Estate Agency')|html}</div>
					<div class="logo"></div>
					<a href="{$url_prefix}/company/" class="btn m-sand">{$lang->get('О компании', 'About M16')}</a>
				</div>
				<div class="photo"><img src="/img/slavaandkatya.jpg" alt="{$lang->get('Вячеслав Малафеев', 'Vyacheslav Malafeyev')}"></div>
			{else}	
				{if !empty($item.malafeev_text)}
					<div class="opinion{if empty($item.description.text)} m-center{/if}">
						<div class="opinion-text">
							{$item.malafeev_text|html}
						</div>
						<div class="main">{$lang->get('Вячеслав <br>Малафеев', 'Vyacheslav <br/>Malafeyev')|html}</div>
						<div class="descr">{$lang->get('Владелец агентства<br>недвижимости M16', 'Owner of <br />M16 Real Estate Agency')|html}</div>
						<div class="descr m-gray">{$lang->get('Голкипер ФК «Зенит»', 'Goalkeeper in Zenit FC')}</div>
						<div class="logo"></div>
						<div class="photo"><img src="/img/photo.jpg" alt="{$lang->get('Вячеслав Малафеев', 'Vyacheslav Malafeyev')}"></div>
					</div>
				{/if}
			{/if}
		</div>
	</div>
{/if}