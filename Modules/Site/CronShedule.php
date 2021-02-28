<?php
namespace Modules\Site;

use Models\CronTasks\Task;
/**
 * Description of Tasks
 *
 * @author olya
 */
class CronShedule extends \LPS\AdminModule{
    const TASK_TYPE_PATH = 'Models/CronTasks';
    public function index(){
        $shedule = Task::getShedule();
        $task_types = Task::getTypes();
        $max_position = !empty($shedule) ? max(array_column($shedule, 'position')) : 0;
        foreach ($task_types as $t => $class_name){
            if (!isset($shedule[$t])){
                $shedule[$t] = array(
                    'title' => $class_name::TITLE,
                    'type' => $t,
                    'plan' => NULL,
                    'status' => 0,
                    'fixed' => 0,
                    'position' => ++$max_position,
                    'is_manual' => $class_name::MANUAL
                );
            }
        }
        $this->getAns()
            ->add('shedule', $shedule)
        ;
        $this->getAns()->setFormData($shedule);
    }
    public function save(){
        $this->setJsonAns()->setEmptyContent();
        $data = $this->request->request->all();
        $shedule = Task::getShedule();
        $position = 1;
        foreach ($data as $type => &$d){
            if (!$this->account instanceof \App\Auth\Account\SuperAdmin && isset($shedule[$type])){//если чувак не суперадмин и ему в интерфейсе не дают что-то сохранить, то подменяем старыми данными
                foreach ($shedule[$type] as $fn => $fv){
                    if (!array_key_exists($fn, $d)){
                        $d[$fn] = $fv;
                    }
                }
            }
            if ($d['status']){
                foreach (array('title', 'plan') as $f){
                    if (empty($d[$f])){
                        $errors[] = array(
                            'segment_id' => 0,
                            'key' => $d['type'] . '['.$f.']',
                            'title' => '',
                            'error' => \Models\Validator::ERR_MSG_EMPTY
                        );
                    }
                }
            }
            $d['position'] = $position;
            $position++;
        }
        if (!empty($errors)){
            $this->getAns()->setErrors($errors);
        }else{
            Task::setShedule($data);
        }
    }
    public function stopTask(){
        $this->setJsonAns()->setEmptyContent();
        $task_id = $this->request->request->get('task_id');
        $task = Task::getById($task_id);
        if (!$task->isStoppable()){
            $this->getAns()->addErrorByKey('exception', 'task is not stoppable');
            return;
        }
        $task->setStopEvent();
        $this->getAns()->setStatus('ok');
    }
    public function restartTask(){
        $this->setJsonAns()->setEmptyContent();
        $task_id = $this->request->request->get('task_id');
        $task = Task::getById($task_id);
        $task->setRestartEvent();
        $this->getAns()->setStatus('ok');
    }
    public function cancelTask(){
        $this->setJsonAns()->setEmptyContent();
        $task_id = $this->request->request->get('task_id');
        $task = Task::getById($task_id);
        if (!$task->isCancelable()){
            $this->getAns()->addErrorByKey('exception', 'task is not cancelable');
            return;
        }
        if ($task['event'] == Task::EVENT_CANCEL || !in_array($task['status'], array(Task::STATUS_NEW, Task::STATUS_PROCESS))){
            $this->getAns()->addErrorByKey('event', 'already');
            return;
        }
        $task->setCancelEvent();
        $this->getAns()->setStatus('ok');
    }
}
