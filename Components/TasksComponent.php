<?php


namespace app\Components;


use app\base\BaseComponent;
use app\models\Tasks;
use app\models\User;
use yii\db\conditions\BetweenCondition;
use yii\web\UploadedFile;

class TasksComponent extends BaseComponent {
    public $modelClass;

    public function init() {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function getModel() {
        return new $this->modelClass;
    }

    public function getUserTasks() {
        $tasks = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                'type_id' => 1,
                'deleted' => 0,
                'finished' => 0,
            ])
            ->orderBy(['date_create' => SORT_DESC])
            ->all();
        return $tasks;
    }

    // возвращает задачи на сегодня, с планируемым завершением до конца дня
    public function getTodayUserTasks() {
        $tasks = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                'type_id' => 1,
                'deleted' => 0,
//                'finished' => 0,
            ])
            ->andWhere(['AND',
                ['>=', 'date_calculate', (new \DateTime(date(date('d.m.Y'))))->format('Y-m-d H:i:s')],
                ['<=', 'date_calculate', (new \DateTime(date('d.m.Y') . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->andWhere(['AND',
//                ['>=', 'date_start', (new \DateTime(date('d.m.Y')  . ' 00:00:00'))->format('Y-m-d H:i:s')],
                ['<=', 'date_start', (new \DateTime(date('d.M.Y') . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        return $tasks;
    }
    // возвращает задачи на завтра
    public function getTomorrowUserTasks() {
        $nextDay = strtotime("+1 day");
        $tasks = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                'type_id' => 1,
                'deleted' => 0,
//                'finished' => 0,
            ])
            ->andWhere(['AND',
                ['>=', 'date_calculate', (new \DateTime(date(date('d.m.Y', $nextDay))))->format('Y-m-d H:i:s')],
                ['<=', 'date_calculate', (new \DateTime(date('d.m.Y', $nextDay) . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->andWhere(['AND',
//                ['>=', 'date_start', (new \DateTime(date('d.m.Y', $nextDay)  . ' 00:00:00'))->format('Y-m-d H:i:s')],
                ['<=', 'date_start', (new \DateTime(date('d.M.Y', $nextDay) . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        return $tasks;
    }

    // возвращает задачи на месяц на текущий момент
    public function getTodayUserAims() {
        $tasks = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                'type_id' => 2,
                'deleted' => 0,
//                'finished' => 0,
            ])
            ->andWhere(['AND',
                ['>=', 'date_calculate', (new \DateTime(date('01.'.date('m.Y'))))->format('Y-m-d H:i:s')],
                ['<=', 'date_calculate', (new \DateTime(date('t', time()).date('.m.Y') . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->andWhere(['AND',
//                ['>=', 'date_start', (new \DateTime(date('01.'.date('m.Y'))))->format('Y-m-d H:i:s')],
                ['<=', 'date_start', (new \DateTime(date('t', time()).date('.m.Y') . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        return $tasks;
    }

    // возвращает задачи на год на текущий момент
    public function getTodayUserGoals() {
        $tasks = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                'type_id' => 3,
                'deleted' => 0,
//                'finished' => 0,
            ])
            ->andWhere(['AND',
                ['>=', 'date_calculate', (new \DateTime(date('01.01.'.date('Y'))))->format('Y-m-d H:i:s')],
                ['<=', 'date_calculate', (new \DateTime(date('31.12.'.date('Y')) . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->andWhere(['AND',
//                ['>=', 'date_start', (new \DateTime(date('01.01.'.date('Y'))))->format('Y-m-d H:i:s')],
                ['<=', 'date_start', (new \DateTime(date('31.12.'.date('Y')) . ' 23:59:59'))->format('Y-m-d H:i:s')]
            ])
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        return $tasks;
    }

    public function getAllTasksArr() {
        $tasks = Tasks::find()
            ->where(
                [
                    'user_id' => \Yii::$app->user->getId(),
                    'type_id' => 1,
                    'deleted' => 0,
                    'finished' => 0,
                ]
            )
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        $arr = [];
        foreach ($tasks as $task) {
            $arr += [$task->id => $task->task];
        }
        return $arr;
    }

    public function getAllAimsArr() {
        $tasks = Tasks::find()
            ->where(
                [
                    'user_id' => \Yii::$app->user->getId(),
                    'type_id' => 2,
                    'deleted' => 0,
                    'finished' => 0,
                ]
            )
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        $arr = [];
        foreach ($tasks as $task) {
            $arr += [$task->id => $task->task];
        }
        return $arr;
    }

    public function getAllGoalsArr() {
        $tasks = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                'type_id' => 3,
                'deleted' => 0,
                'finished' => 0,
            ])
//            ->andWhere(['AND',
//                    ['<=', 'date_calculate', (new \DateTime(date('01.'.date('m.Y'))))->format('Y-m-d')],
//                    ['>=', 'date_calculate', (new \DateTime(date('t', time()).'.'.date('m.Y')))->format('Y-m-d H:i:s')]
//                ])
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        $arr = [];
        foreach ($tasks as $task) {
            $arr += [$task->id => $task->task];
        }
        return $arr;
    }

    public function getAllUserTasks() {
        return \Yii::$app->dao->getAllUserTasks(\Yii::$app->user->getId());
    }

    public function getChildTasks($taskId, $typeId=null) {
        $targetProp = '';
        if (!$typeId) {
            $task = Tasks::findOne(['id' => $taskId, 'user_id' => \Yii::$app->user->getId()]);
            $typeId = $task->type_id;
        }

        if ($typeId == 3) {
            $targetProp = 'goal_id';
        } elseif ($typeId == 2) {
            $targetProp = 'aim_id';
        }

        $childesFromBD = $this->getChilds($targetProp, $taskId);
        $childTasks = [];

        foreach ($childesFromBD as $child) {
            if ($child->aim_id == null) {
                $childTasks += [$child->id => []];
            } else {
                if (!array_key_exists($child->aim_id, $childTasks)) {
                    $childTasks += [$child->aim_id => [$child->id => []]];

                } else {
                    $childTasks[$child->aim_id] += [$child->id => []];
                }
            }
        }
        return $childTasks;
    }

    private function getChilds($targetProp, $taskId) {
        $childes = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                $targetProp => $taskId,
                'deleted' => 0,
                'finished' => 0,
            ])
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        return $childes;
    }

    public function getDeletedTasks() {
        $tasks = Tasks::find()
            ->where([
                'user_id' => \Yii::$app->user->getId(),
                'deleted' => 1,
//                'finished' => 0,
            ])
            ->orderBy(['date_start' => SORT_DESC])
            ->all();
        return $tasks;
    }

    public function addTask(Tasks $task)
    {
//        $task->filesReal = UploadedFile::getInstances($task, 'filesReal');
//        $fileSaver = \Yii::createObject(['class' => FileSaverComponent::class]);
        if (!$task->user_id) {
            $task->user_id = \Yii::$app->user->getId();
        }


        if ($task->validate()) {

//            foreach ($task->filesReal as &$file) {
//                $file = $fileSaver->saveFile($file);
//                if (!$file) {
//                    return false;
//                }
//            }
//            $task->files = implode('|',$task->filesReal);
            // валидация + сохранение активности


//            echo '<pre>';
//            print_r($task->date_calculate);
//            echo '</pre>';
//            exit();
            if ($task->save()) {
                return $task;
            }
            \Yii::error($task->getErrors());
            return false;
        }
        //валидация файлов не прошла
        return false;
    }

    public function deleteTask(Tasks $task) {
        if ($task->validate()) {
            $task->deleted = 1;
            if ($task->save(false)) {
                return true;
            }
            return false;
        }

        return false;
    }

    public function finishTask(Tasks $task) {
        if ($task->validate()) {
            if ($task->finished == 1) {
                $task->finished = 0;
                $task->date_finish = null;
            } else {
                $task->finished = 1;
                $task->date_finish = date('Y-m-d H:i:s');
            }

            if ($task->save(false)) {
                return true;
            }
            return false;
        }

        return false;
    }

    public function restoreTask(Tasks $task) {
        if ($task->validate()) {
            $task->deleted = 0;
            if ($task->save(false)) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function hardDeleteTask(Tasks $task) {
        if ($task->delete()) {
            return true;
        }
//        \Yii::error($task->getErrors());
        return false;
    }

    public function findTodayNotifTask() {
        return Tasks::find()
            ->andWhere('useNotification = 1')
            ->andWhere('deleted = 0')
            ->andWhere('dateStart>=:date',[':date'=>date('Y-m-d')])
            ->andWhere('dateStart<=:date2',[':date2'=>date('Y-m-d'. ' 23:59:59')])->all();
    }

    public function renewLastUnfinishedTasks($type_id) {
        $prev = '';
        switch ($type_id) {
            case 1:
                $prev = strtotime("-1 day");
                $dateFrom = (new \DateTime(date('d.m.Y', $prev)  . ' 00:00:00'))->format('Y-m-d H:i:s');
                $dateTo = (new \DateTime(date('d.m.Y', $prev) . ' 23:59:59'))->format('Y-m-d H:i:s');
                break;
            case 2:
                $prev = strtotime("-1 month");
                $dateFrom = (new \DateTime(date('01.m.Y', $prev)  . ' 00:00:00'))->format('Y-m-d H:i:s');
                $dateTo = (new \DateTime(date('t', $prev).date('.m.Y', $prev) . ' 23:59:59'))->format('Y-m-d H:i:s');
                break;
            case 3:
                $prev = strtotime("-1 year");
                $dateFrom = (new \DateTime(date('01.01.'.date('Y',$prev))))->format('Y-m-d H:i:s');
                $dateTo = (new \DateTime(date('31.12.'.date('Y',$prev)) . ' 23:59:59'))->format('Y-m-d H:i:s');
                break;
        }
        $newDate = (new \DateTime(date('d.m.Y') . ' 23:59:59'))->format('Y-m-d H:i:s');
//        echo $newDate;
//        exit();
        $update = Tasks::updateAll(
            ['date_calculate' => $newDate],
                ['and',
                    ['user_id' => \Yii::$app->user->getId()],
                    ['type_id' => $type_id,],
                    ['deleted' => 0,],
                    ['finished' => 0,],
                    ['>=', 'date_calculate', $dateFrom],
                    ['<=', 'date_calculate', $dateTo],
//                    ['>=', 'date_start', (new \DateTime(date('d.m.Y', $prev)  . ' 00:00:00'))->format('Y-m-d H:i:s')],
//                    ['<=', 'date_start', (new \DateTime(date('d.M.Y', $prev) . ' 23:59:59'))->format('Y-m-d H:i:s')],
                ]
        );
        if ($update) {
            return true;
        }
        return false;
    }
}