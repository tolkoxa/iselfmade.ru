<?php


namespace app\commands;


use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class RbacController extends Controller {

    public function actionGen() {
        \Yii::$app->rbac->generateRbac();
        \Yii::$app->rbac->addMentorRole();
    }
    public function actionAddMentorRole() {
        \Yii::$app->rbac->addMentorRole();
    }
    public function actionAddReportPermission() {
        \Yii::$app->rbac->addReportPermission();
    }
    public function actionAddModeratorChildes() {
        \Yii::$app->rbac->addModeratorChildes();
    }
    public function actionAddViewReportPermission() {
        \Yii::$app->rbac->viewUserReportPermission();
    }

    public function actionSetAllRolesToUser() {
        if (!\Yii::$app->rbac->setAllRolesToUser()) {
            $this->stdout("Произошла ошибка!\n", Console::BG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $this->stdout("Done!\n", Console::BOLD);
        return ExitCode::OK;
    }

    public function actionSetAdminRole($id) {
        if (\Yii::$app->rbac->setAdminRole($id)) {
            $this->stdout("Done!\n", Console::BOLD);
            return ExitCode::OK;
        }
    }

    public function actionSetUserRole($id) {
        if (\Yii::$app->rbac->setUserRole($id)) {
            $this->stdout("Done!\n", Console::BOLD);
            return ExitCode::OK;
        }
    }

    public function actionSetCuratorRole($id) {
        if (\Yii::$app->rbac->setCuratorRole($id)) {
            $this->stdout("Done!\n", Console::BOLD);
            return ExitCode::OK;
        }
    }

    public function actionSetBuddyRole($id) {
        if (\Yii::$app->rbac->setBuddyRole($id)) {
            $this->stdout("Done!\n", Console::BOLD);
            return ExitCode::OK;
        }
    }

    public function actionSetModeratorRole($id) {
        if (\Yii::$app->rbac->setModeratorRole($id)) {
            $this->stdout("Done!\n", Console::BOLD);
            return ExitCode::OK;
        }
    }



}