<?php


namespace app\controllers\actions\task;


use app\components\TasksComponent;
use app\models\Tasks;
use app\models\UsersReports;
use app\widgets\tasks\ArchiveTasksWidget;
use yii\base\Action;
use yii\web\HttpException;
use yii\web\Response;

class GetMonthsArchiveDataAction extends Action {
    public function run($month, $year) {

        if (\Yii::$app->user->isGuest) {
            return $this->controller->redirect('/');
        }
        if (!\Yii::$app->rbac->canViewOwnTask()) {
            throw new HttpException(403, 'Нет доступа' );
        }

        if (!\Yii::$app->request->isGet) {
            throw new HttpException(400, 'Некорректный запрос' );
        }
        \Yii::$app->response->format=Response::FORMAT_JSON;

        $compTasks = \Yii::createObject(['class' => TasksComponent::class,'modelClass' => Tasks::class]);
        $modelTasks = $compTasks->getModel();

        $yesterdayDate = date('d.m.Y', strtotime( "-1 day"));
        $beforeYesterdayDate = date('d.m.Y', strtotime( "-2 day"));
        $today = date('d.m.Y');
        $unixToday = strtotime($today);
        $date = \Yii::$app->request->post()['date'] ?? null;
        $unixDate = strtotime($date);

        $tasksData = $compTasks->getMonthsTasks($month, $year);

        //        $dateUTC = (new \DateTime(date($date)))->format('Y-m-d');
//        $archiveTasks = $comp->getArchiveTasksByDate($date);
//        $gradeModel = new UsersReports();
//        $userGrade = $gradeModel->findOne(['user_id' => \Yii::$app->user->getId(), 'date' => $dateUTC])->mentor_grade;
        if ($tasksData) {
            return [
                'result' => true,
                'archive' => $tasksData,
            ];
        } else {
            return ['result' => false, 'message' => 'Ошибка.'];
        }

    }
}