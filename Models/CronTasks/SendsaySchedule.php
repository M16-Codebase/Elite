<?php
/**
 * Created by PhpStorm.
 * User: manson
 * Date: 06.11.15
 * Time: 17:52
 */

namespace Models\CronTasks;


use Models\SubscribeManagement\SubscribeController;
use Models\SubscribeManagement\SubscribeMember;

class SendsaySchedule extends Task
{
    const TITLE = 'Запуск синхронизации Sendsay';
    /**
     * создать задачу
     *
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL){

    }

    public static function createAndStart(){
        $task = Task::getNext(SendsaySynchronize::getType(), array(Task::STATUS_NEW, Task::STATUS_PROCESS));
        if (empty($task)) {
            $task_id = parent::add(array(
                'type' => self::getType()
            ));
            $task = static::getById($task_id);
            $task->start();
        }
        return;
    }
    public function start(){
        $this->setStart();
        $controller = SubscribeController::getInstance();
        // Проверка наличия стандартных списков
        $controller->recreateStdGroups();
        $request_list = array();
        $subscribers_for_update = SubscribeMember::search(array('need_update' => true), $count, 0, 100000);
        if (!empty($subscribers_for_update)){
            foreach($subscribers_for_update as $member){
                $request_list[] = $controller->setMember($member->asArray(), TRUE);
            }

            $result = $controller->makeRequest(array(
                'action' => 'batch',
                'stop_on_error' => 0,
                'do' => $request_list
            ));
            if (!empty($result['result'])){
                foreach($result['result'] as $res){
                    if (empty($res['errors'])){
                        $obj = isset($res['obj']['obj']) ? $res['obj']['obj'] : $res['obj'];
                        $email = $obj['member']['email'];
                        if (!empty($subscribers_for_update[$email])) {
                            $subscribers_for_update[$email]->edit(array('need_update' => 0));
                        }
                    }
                }
            }
        }
        // Постановка задач на сабскрайбе
        $tasks = array();
        foreach(array('active', 'unconfirmed', 'removed') as $group_id){
            $tasks[$group_id] = array(
                'group_id' => $group_id,
                'track_id' => $controller->getMemberList($group_id, SubscribeController::RESULT_SAVE),
                'status' => SubscribeController::TASK_STATUS_NEW
            );
        }
        $data = array(
            'tasks' => $tasks
        );
        SendsaySynchronize::create(array('data' => $data), null, $error);
        $this->setComplete();
    }
}