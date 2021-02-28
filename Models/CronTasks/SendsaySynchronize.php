<?php
/**
 * Created by PhpStorm.
 * User: manson
 * Date: 06.11.15
 * Time: 17:51
 */

namespace Models\CronTasks;


use Models\SubscribeManagement\SubscribeController;

class SendsaySynchronize extends Task
{
    const TITLE = 'Синхронизация Sendsay';
    const MANUAL = TRUE;
//    const STOPPABLE = TRUE;
//    const CANCELABLE = TRUE;

    /**
     * создать задачу
     *
     * @param array $params
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile|string $FILE
     * @param string $error
     */
    public static function create($params = array(), $FILE = NULL, &$error = NULL){
        $params['type'] = self::getType();
        Task::add($params);
    }

    public static function createAndStart(){
        return;
    }
    public function start(){
        if ($this['status'] == Task::STATUS_NEW) {
            $this->setStart();
        }
        $last_task = Task::search(array(
            'type' => self::getType(),
            'status' => Task::STATUS_COMPLETE
        ), $count, 0, 1);
        $last_task = reset($last_task);
        $last_sync_date = !empty($last_task) ? $last_task['time_start'] : NULL;
        $controller = SubscribeController::getInstance();
        $tasks = $this['data']['tasks'];
        $complete_count = 0;
        foreach($tasks as $id => $task){
            if ($task['status'] != SubscribeController::TASK_STATUS_SUCCESS){
                $track_info = $controller->getTrackInfo($task['track_id'], $status);
                if ($status == SubscribeController::TASK_STATUS_SUCCESS){
                    $filename = $track_info['param']['report_file'];
                    $controller->importSendsayUsersCsv($task['group_id'], $filename, $last_sync_date);
                    $tasks[$id]['status'] = SubscribeController::TASK_STATUS_SUCCESS;
                    $tasks[$id]['filename'] = $filename;
                    $complete_count++;
                }
            } else {
                $complete_count++;
            }
        }
        if ($complete_count == 3) {
            $this->update(array('data' => array('tasks' => $tasks)));
            $this->setComplete();
        } else {
            $this->setPercent(10 + 30 * $complete_count,
                array('tasks' => $tasks)
            );
        }
    }
}