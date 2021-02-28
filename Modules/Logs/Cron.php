<?php
namespace Modules\Logs;

use Models\CronTasks\Task;
/**
 * Просмотр логов изменений
 *
 * @author olga
 */
class Cron extends \LPS\WebModule{
    const DEFAULT_ACCESS = \App\Configs\AccessConfig::ACCESS_NO_BROKERS;
	const PAGE_SIZE = 50;
    public function index(){
        $page = $this->request->query->get('page', 1);
        $sort = $this->request->query->get('sort');
		$tasks = Task::search(array('order' => $sort), $count, ($page-1)*self::PAGE_SIZE, self::PAGE_SIZE);
        if (empty($tasks) && $page!=1){
            return $this->redirect($this->getModuleUrl() . '/');
        }
        $shedule = Task::getShedule();
		$this->getAns()
            ->add('shedule', $shedule)
            ->add('task_title', array_column($shedule, 'title', 'type'))
            ->add('tasks', $tasks)
            ->add('pageNum', $page)
            ->add('pageSize', self::PAGE_SIZE)
            ->add('count', $count)
            ->add('segments', \App\Segment::getInstance()->getAll());
    }
    /**
	 * @ajax
	 * @throws \LogicException
	 */
	public function getErrors(){
		$this->setAjaxResponse();
		$task_id = $this->request->request->get('task_id');
		if (empty($task_id)){
			throw new \LogicException('Не задан id задачи "task_id"');
		}
        $task = Task::getById($task_id);
        if (empty($task)){
			throw new \LogicException('Задача id #' . $task_id . ' не найдена');
		}
		$this->getAns()->add('error_logs', $task->getErrors())
            ->add('task', $task)
            ->add('unsuccess_path', NULL);
	}
}
?>