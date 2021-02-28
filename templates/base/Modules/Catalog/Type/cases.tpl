{?$cases = array('i'=> array('name' => 'Именительный', 'title' => 'кто? что?', 'case' => 'i'),
				'r'=> array('name' => 'Родительный', 'title' => 'кого? чего?', 'case' => 'r'),
				'd'=> array('name' => 'Дательный', 'title' => 'кому? чему?', 'case' => 'd'),
				'v'=> array('name' => 'Винительный', 'title' => 'кого? что?', 'case' => 'v'),
				't'=> array('name' => 'Творительный', 'title' => 'кем? чем?', 'case' => 't'),
				'p'=> array('name' => 'Предложный', 'title' => 'ком? чём?', 'case' => 'p'),
)}
{foreach from=$cases item=case}
<div class="white-block-row">
	<div class="w4">
		<span title="{$case.title}">{$case.name} падеж</span>
	</div>
	<div class="w4">
		<input type="text" name="word_cases[{$type}][1][{$case.case}]" placeholder="единственное число" />
	</div>
	<div class="w4">
		<input type="text" name="word_cases[{$type}][2][{$case.case}]" placeholder="множественное число" />
	</div>
</div>
{/foreach}
