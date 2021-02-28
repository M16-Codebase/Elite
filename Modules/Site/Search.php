<?php
/**
 * Description of Search
 *
 * @author pochka
 */
namespace Modules\Site;
class Search extends \LPS\WebModule{
    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        if ($this->account instanceof \App\Auth\Account\Admin){
            return true;
        }
        return false;
    }
    /**
     * Поиск
     */
    public function index(){
        $this->phrasesList(true);
    }

    public function phrasesList($inner = false) {
        $ans = $inner ? $this->getAns() : $this->setJsonAns();
        $params = array();
        $order = $this->request->query->get('sort');
        if (!empty($order)){
            foreach($order as $field => $desc) {
                if (in_array($field, array('phrase', 'url'))) {
                    $params['order'][$field] = !empty($desc) ? 1 : 0;
                }
            }
        } else {
            $params['order']['phrase'] = 0;
        }
        $phrases = \Models\Search::getInstance()->get($params);
        $ans->add('phrases', $phrases)
            ->add('order', $params['order']);
    }

    public function phraseFields(){
        $ans = $this->setJsonAns();
        $id = $this->request->request->get('id');
        if (!empty($id)) {
            $phrase_data =\Models\Search::getInstance()->getById($id);
            if (empty($phrase_data)) {
                $ans->setEmptyContent()
                    ->addErrorByKey('id', \Models\Validator::ERR_MSG_NOT_FOUND);
            } else {
                $ans->add('phrase_data', $phrase_data)
                    ->setFormData($phrase_data);
            }
        }
    }

    public function savePhrase() {
        $id = $this->request->request->get('id');
        $phrase = $this->request->request->get('phrase');
        $url = $this->request->request->get('url');
        $search = \Models\Search::getInstance();
        if (empty($id)) {
            $search->add($phrase, $url, $errors);
        } else {
            $search->edit($id, $phrase, $url, $errors);
        }
        if (!empty($errors)) {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('phrasesList');
        }
    }

    /**
     * @ajax
     */
    public function deletePhrase(){
        \Models\Search::getInstance()->del($this->request->request->get('id'), $errors);
        if (!empty($errors)) {
            $this->setJsonAns()->setEmptyContent()->setErrors($errors);
        } else {
            return $this->run('phrasesList');
        }
    }
    
    public function logs(){
        if ($this->request->query->has('export')) {
            $logs = \Models\Search::getInstance()->getLogs(array('order' => array('count' => 1)));
            if (!empty($logs)) {
                $path = \LPS\Config::getRealDocumentRoot() . '/data/temp/';
                if (!file_exists($path)){
                    \LPS\Components\FS::makeDirs($path);
                }
                $filename = $path . 'search_log' . time() . '.csv';
                $fp = fopen($filename, 'w');
                $in_cp = \LPS\Config::getParametr('site', 'codepage');
                $out_cp = 'cp1251';
                fwrite($fp, iconv($in_cp, $out_cp, 'Фраза;Количество;Последний поиск') . PHP_EOL);
                foreach($logs as $log){
                    fwrite($fp,
                        iconv(
                            $in_cp,
                            $out_cp,
                            $log['phrase'] . ';' . $log['count'] . ';' . date('d.m.Y H:i:s', strtotime($log['date']))
                        ) . PHP_EOL
                    );
                }
                fclose($fp);
                \Models\FilesManagement\Download::existsFile($filename, 'search_log.csv', TRUE);
                unlink($filename);
                exit();
            }
        } else {
            $this->logsList(true);
        }
    }

    public function logsList($inner = false) {
        if (!$inner) {
            $this->setJsonAns();
        }
        $params = array();
        $order = $this->request->query->get('sort');
        if (!empty($order)){
            foreach($order as $field => $desc) {
                if (in_array($field, array('phrase', 'count', 'date'))) {
                    $params['order'][$field] = !empty($desc) ? 1 : 0;
                }
            }
        } else {
            $params['order']['count'] = 1;
        }
        $logs = \Models\Search::getInstance()->getLogs($params);
        $this->getAns()
            ->add('order', $params['order'])
            ->add('logs', $logs);
    }
}