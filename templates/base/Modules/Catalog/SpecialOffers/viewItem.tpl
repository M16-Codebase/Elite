<h1>{$item.title}</h1>

{$item.start_date|date_format_lang:'%d %B %Y':'ru'}&nbsp;—&nbsp;{$item.end_date|date_format_lang:'%d %B %Y':'ru'}

{$item.offer_type}

{$item.description.text|html}