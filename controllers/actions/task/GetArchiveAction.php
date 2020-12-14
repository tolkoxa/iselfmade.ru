<?php


namespace app\controllers\actions\task;


use app\components\TasksComponent;
use app\components\UserComponent;
use app\models\Tasks;
use app\widgets\tasks\ArchiveTasksWidget;
use yii\base\Action;
use yii\web\HttpException;
use yii\web\Response;

class GetArchiveAction extends Action {
    public function run() {

        if (\Yii::$app->user->isGuest || !\Yii::$app->rbac->canViewOwnTask()) {
            throw new HttpException(403, 'Нет доступа' );
        }

        if (!\Yii::$app->request->isPost) {
            throw new HttpException(403, 'Нет доступа' );
        }
        \Yii::$app->response->format=Response::FORMAT_JSON;

        $comp = \Yii::createObject(['class' => TasksComponent::class,'modelClass' => Tasks::class]);
        $model = $comp->getModel();

        $yesterdayDate = date('d.m.Y', strtotime( "-1 day"));
        $beforeYesterdayDate = date('d.m.Y', strtotime( "-2 day"));
        $today = date('d.m.Y');
        $unixToday = strtotime($today);
        $date = \Yii::$app->request->post()['date'] ?? null;
        $unixDate = strtotime($date);
        if ($unixDate >= $unixToday) {
            return ['result' => false, 'message' => 'Можно просмотреть задачи только за прошедший преиод.'];
        }
        $title = '';
        if ($date === $yesterdayDate) {
            $title = 'Вчера';
        } elseif ($date === $beforeYesterdayDate) {
            $title = 'Позавчера';
        }

        $archiveTasks = $comp->getArchiveTasksByDate($date);

        if ($archiveTasks) {
            return ['result' => true, 'html' => ArchiveTasksWidget::widget([
                'title' => $title,
                'date' => $date,
                'tasks' => $archiveTasks,
                'block_id' => 0,
            ])];
        } else {
            return ['result' => false, 'message' => 'В выбранную дату задачи отсутствовали.'];
        }

    }
}