<?php
/**
 * Класс объединяющий правила (Rule или RuleAggregator) логическими операторами OR или AND
 *
 * @author olga
 */
namespace Models\CatalogManagement\Rules;
class RuleAggregator implements iRule{
    const LOGIC_AND = 'AND';
    const LOGIC_OR = 'OR';
    /**
     * Какой логический оператор использовать
     * OR или AND
     * @var string
     */
    private $logic = NULL;
    /**
     * Список правил
     * @var iRule[]
     */
    private $rules = array();
    /**
     * Сквозная логика. Для определения LEFT JOIN или INNER, 
     * требуется знать, есть ли хотя бы у одного из 
     * родительских правил логика OR
     * @var string
     */
    private $throughLogic = NULL;
    /**
     * Список ключей свойств, участвующих в запросе
     * @var array
     */
    private $rulePropKeys = array();
	/**
	 * Список свойств
	 */
	private $ruleProps = array();
    /**
     * 
     * @param array $rules iRule[] or array to make Rule[]
     * @return RuleAggregator[]
     */
    public static function makeRules($rules){
        $result = array();
        if (empty($rules)){
            return array();
        }
        $try_create_microRules = FALSE;
        foreach ($rules as $r){
            if (is_object($r) && $r instanceof self){
                $result[] = $r;
            }else{
                $try_create_microRules = TRUE;
                break;
            }
        }
        if (empty($result) && !$try_create_microRules){
            throw new \LogicException('rules must be array of Rule, RuleAggregator or compatible array to make rule');
        }elseif ($try_create_microRules && empty($result)){
            $result[] = self::make(self::LOGIC_AND, Rule::makeRules($rules));
        }elseif($try_create_microRules && !empty($result)){
            throw new \LogicException('array of rules must be uniform');
        }
        return $result;
    }
    /**
     * 
     * @param string $logic self::LOGIC_OR|self::LOGIC_AND
     * @param iRule[] $rules
     * @return RuleAggregator
     */
    public static function make($logic, $rules, $parent_logic = NULL){
        return new RuleAggregator($logic, $rules, $parent_logic);
    }
    /**
     * 
     * @param string $logic — OR|AND
     * @param iRule[] $rules
     * @param string $parent_logic — OR|AND
     * @throws \LogicException
     */
    private function __construct($logic, $rules, $parent_logic = NULL){
        if ($logic != self::LOGIC_AND && $logic != self::LOGIC_OR){
            throw new \LogicException('Logic must be "OR" or "AND"');
        }
        $this->logic = $logic;
        $this->rules = $rules;
        $this->throughLogic = $parent_logic == self::LOGIC_OR ? self::LOGIC_OR : $this->logic;
        $props_keys = array();
        $child_or = false;
        foreach ($this->rules as $rule){
            if (! ($rule instanceof iRule)){
                throw new \LogicException('Rules must be instance of iRule');
            }
            $aggRules = FALSE;
            if ($rule instanceof self){
                $rulePropsKeys = $rule->getRulePropKeys();
                foreach ($rulePropsKeys as $key => $endingLogic){
                    $props_keys[$key] = !empty($props_keys[$key]) && $props_keys[$key] == self::LOGIC_OR ? self::LOGIC_OR : $endingLogic;
                }
                if ($this->throughLogic == self::LOGIC_OR && $rule->getThroughLogic() != self::LOGIC_OR){
                    // throughLogic OR для детей, если в текущем агрегаторе OR
                    $rule->setThroughLogicOr();
                } elseif ($rule->getThroughLogic() == self::LOGIC_OR){
                    $child_or = TRUE;
                }
                $aggRules = TRUE;
            }elseif(!empty($aggRules)){
                throw new \LogicException('$rules in RuleAggregator must be of the same class');
            }else{
                $props_keys[$rule['key']] = $this->throughLogic;
            }
        }
        // Если хоть один дочерний агрегатор использует OR, ставим throughLogic OR для всех
        if ($this->throughLogic != self::LOGIC_OR && $child_or){
            $this->setThroughLogicOr();
        }
        $this->rulePropKeys = $props_keys;
    }

    /**
     *
     * @return string
     */
    protected function getThroughLogic(){
        return $this->throughLogic;
    }

    /**
     * Каскадная установка throughLogic на OR
     * Необходимо, чтобы если в стеке рулов есть хоть один OR, все проперти цеплялись через LEFT JOIN
     */
    protected function setThroughLogicOr(){
        if ($this->throughLogic == self::LOGIC_OR){
            return;
        }
        $this->throughLogic = self::LOGIC_OR;
        foreach($this->rules as $rule){
            if ($rule instanceof RuleAggregator){
                $rule->setThroughLogicOr();
            }
        }
    }

    public function compileSql($props, &$sql, $segment_id = NULL, $search_type){
        foreach($this->rulePropKeys as $k => $v){
            $this->rulePropKeys[$k] = $this->throughLogic;
        }
        Rule::setPropsStack($this->rulePropKeys);
        return $this->_getSql($props, $sql, $segment_id, $search_type);
    }
    /**
     * Собрать sql. Вызывается только из других методов интерфейса iRule.
     * @param array $props все свойства, участвующие в запросе. формат: array('key' => array('id' => свойство))
     * @param array $sql части запроса
     * @param int $segment_id
     * @return array
     */
    public function _getSql($props, &$sql, $segment_id = NULL, $search_type = NULL){
		$select_part = '';
		$from_part = '';
		$order_part = array();
		$where_part = array();
        foreach ($this->rules as $r){
			$rule_sql = array();
            $r->_getSql(
				$r instanceof self ? $props : (!empty($props[$r['key']]) ? $props[$r['key']] : NULL), 
				$rule_sql,
				$segment_id,
				$search_type
            );
			//записываем все получившиеся части для каждого отдельного свойства (или группы свойств, если это RuleAggregator)
			$select_part .= !empty($rule_sql['select']) ? $rule_sql['select'] : '';
			$from_part .= !empty($rule_sql['from']) ? $rule_sql['from'] : '';
			$order_part = !empty($rule_sql['order']) ? array_merge($order_part, $rule_sql['order']) : $order_part;
			$where_part = !empty($rule_sql['where']) ? array_merge($where_part, $rule_sql['where']) : $where_part;
        }
		//теперь компануем уровнем выше все части с нужной логикой в where части
		if (!empty($select_part)){
			$sql['select'] = (!empty($sql['select']) ? $sql['select'] : '') . $select_part;
		}
		if (!empty($from_part)){
			$sql['from'] = (!empty($sql['from']) ? $sql['from'] : '') . $from_part;
		}
		if (!empty($order_part)){
			$sql['order'] = array_merge(!empty($sql['order']) ? $sql['order'] : array(), $order_part);
		}
		if (!empty($where_part)){
			$sql['where'][] = (count($where_part) > 1 ?' (':'') . implode(' ' . $this->logic . ' ', $where_part) . (count($where_part) > 1 ?') ':'');
		}
        return $sql;
    }
    
    public function getRules(){
        return $this->rules;
    }
    
    public function getRulePropKeys(){
        return $this->rulePropKeys;
    }
}
