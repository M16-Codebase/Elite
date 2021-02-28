<?php
/**
 * Description of Search
 *
 * @author pochka
 */
namespace Models;
class Search {
	const TABLE = 'search';
    const TABLE_LOG = 'search_log';
    private static $allowOrderParams = array('count', 'phrase', 'date');
	private $db = NULL;
	/**
     * @var Search
     */
    private static $instance = NULL;
    /**
     * @return Search
     */
    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new Search();
        }
        return self::$instance;
    }
	private function __construct(){
		$this->db = \App\Builder::getInstance()->getDB();
	}
	public function get($params = array()){
		$order_string = '';
		if (!empty($params['order'])) {
			foreach($params['order'] as $field => $desc) {
				$order_string .= (!empty($order_string) ? ', ' : '') . '`' . $field . '`' . (!empty($desc) ? ' DESC' : '');
			}
			$order_string = ' ORDER BY ' . $order_string;
		}
		return $this->db->query('
			SELECT `id`, LOWER(`phrase`) AS `phrase`, `url` FROM `'.self::TABLE.'` WHERE 1{ AND `id` = ?d}{ AND `id` != ?d}{ AND `phrase` = ?s}' . $order_string,
			!empty($params['id']) ? $params['id'] : $this->db->skipIt(),
			!empty($params['not_id']) ? $params['not_id'] : $this->db->skipIt(),
			!empty($params['phrase']) ? $params['phrase'] : $this->db->skipIt())
		->select('id');
	}
	public function getById($id){
		if (empty($id)) {
			return NULL;
		}
		$data = $this->get(array('id' => $id));
		return reset($data);
	}
	public function getUrl($phrase){
        $phrase = mb_strtolower($phrase, 'utf8');
		$data = $this->get(array('phrase' => $phrase));
		$data = reset($data);
		return !empty($data) ? $data['url'] : NULL;
	}
	public function add($phrase, $url, &$errors = array()){
		if (empty($phrase)){
			$errors['phrase'] = \Models\Validator::ERR_MSG_EMPTY;
		} elseif ($this->phraseExists($phrase)) {
			$errors['phrase'] = \Models\Validator::ERR_MSG_EXISTS;
		}
		if (empty($url)){
			$errors['url'] = \Models\Validator::ERR_MSG_EMPTY;
		}
		if (empty($errors)){
			$url = Validator::getInstance()->getRelativeUrl($url);
			$this->db->query('INSERT INTO `'.self::TABLE.'` SET `phrase` = ?s, `url` = ?s', $phrase, $url);
			return true;
		}else{
			return false;
		}
	}
	public function del($id, &$errors = array()){
		$phrase_data = $this->getById($id);
		if (empty($phrase_data)) {
			$errors['id'] = empty($id) ? \Models\Validator::ERR_MSG_EMPTY : \Models\Validator::ERR_MSG_NOT_FOUND;
			return false;
		}else {
			$this->db->query('DELETE FROM `'.self::TABLE.'` WHERE `id` = ?d', $id);
			return true;
		}
	}
	public function edit($id, $phrase, $url, &$errors = array()){
		$phrase_data = $this->getById($id);
		if (empty($phrase_data)) {
			$errors['id'] = empty($id) ? \Models\Validator::ERR_MSG_EMPTY : \Models\Validator::ERR_MSG_NOT_FOUND;
		}
		if (empty($phrase)){
			$errors['phrase'] = \Models\Validator::ERR_MSG_EMPTY;
		} elseif ($phrase_data['phrase'] != $phrase && $this->phraseExists($phrase, $id)) {
			$errors['phrase'] = \Models\Validator::ERR_MSG_EXISTS;
		}
		if (empty($url)){
			$errors['url'] = \Models\Validator::ERR_MSG_EMPTY;
		}
		if (empty($errors)){
			$url = Validator::getInstance()->getRelativeUrl($url);
			$this->db->query('UPDATE `'.self::TABLE.'` SET `url` = ?s, `phrase` = ?s WHERE `id` = ?d', $url, $phrase, $id);
			return true;
		} else {
			return false;
		}
	}
	public function phraseExists($phrase, $not_id = NULL){
		$result = $this->get(array('phrase' => $phrase, 'not_id' => $not_id));
		return !empty($result);
	}
    public function getLogs($params){
        $order_sql = array();
        if (!empty($params['order'])){
            if (is_array($params['order'])){
                foreach ($params['order'] as $order => $desc){
                    $order_sql[] = '`' . $order . '`' . (!empty($desc) ? ' DESC' : '');
                }
            }else{
                throw new \LogicException('param order must be an array');
            }
        }
        $logs = $this->db->query('SELECT * FROM `'.self::TABLE_LOG.'`
            '. (!empty($order_sql) ? ('ORDER BY ' . implode(', ', $order_sql)) : '') .'
        ')->select('phrase');
        return $logs;
    }
    public function log($phrase){
        $phrase = mb_strtolower($phrase, 'utf8');
        $count_phrase = $this->db->query('SELECT `count` FROM `'.self::TABLE_LOG.'` WHERE `phrase` = ?s', $phrase)->getCell();
        if (empty($count_phrase)){
            $this->db->query('INSERT INTO `'.self::TABLE_LOG.'` SET `phrase` = ?s, `count` = 1, `date` = NOW()', $phrase);
        }else{
            $this->db->query('UPDATE `'.self::TABLE_LOG.'` SET `count` = ?d, `date` = NOW() WHERE `phrase` = ?s', $count_phrase+1, $phrase);
        }
        return true;
    }
}

?>
