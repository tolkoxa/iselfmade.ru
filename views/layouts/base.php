<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

//Yii::$app->assetManager->bundles['yii\bootstrap\BootstrapAsset'] = [
//    'css' => [
//        'css/style.css',
//    ]
//];
AppAsset::register($this);

$avatarImage = \Yii::$app->user->getIdentity()->getAvatarName();
if (!$avatarImage) {
    $avatarImage = '/img/user_img.jpg';
} else {
    $avatarImage = '/users/ava/' . $avatarImage;
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container">
    <div class="custom_light">
        <div class="">
            <img class="user_img" src="<?= Html::encode($avatarImage) ?>" width="60" height="60">
        </div>
    </div>
    <div class="menu">
        <div class="date">
            <span class="date__day"><?php echo date('d') ?></span>
            <span class="date__month">.<?php echo date('m') ?></span>
            <p class="date__weekday"><?php echo \Yii::$app->formatter->asDate(date('Y-m-d'), 'php: l')?></p>
        </div>
        <div class="menu__list">
            <?php
            $url = \Yii::$app->request->url;
            echo Nav::widget([
                'options' => ['class' => 'menu__items'],
                'items' => [
                    (['label' => 'Отчет',
                        'url' => ['/report'],
                        'options' => ['class' => $url=='/report' ? 'main_menu-selected' : ''],
                        'linkOptions' => ['class' => 'menu__item'],
                    ]),
                    (['label' => 'Мои обещания',
                        'url' => ['/promises'],
                        'options' => ['class' => $url=='/promises' ? 'main_menu-selected' : ''],
                        'linkOptions' => ['class' => 'menu__item'],
                    ]),
//                    (['label' => 'Статистика',
//                        'url' => ['/statistics'],
//                        'options' => ['class' => $url=='/statistics' ? 'main_menu-selected' : ''],
//                        'linkOptions' => ['class' => 'menu__item'],
//                    ]),
//                    (['label' => 'Категории',
//                        'url' => ['/category'],
//                        'options' => ['class' => $url=='/category' ? 'main_menu-selected' : ''],
//                        'linkOptions' => ['class' => 'menu__item'],
//                    ]),
                    (['label' => 'Повторяющиеся задачи',
                        'url' => ['/repeated'],
                        'options' => ['class' => $url=='/repeats' ? 'main_menu-selected' : ''],
                        'linkOptions' => ['class' => 'menu__item'],
                    ]),
                    (['label' => 'Архив отчетов',
                        'url' => ['/archive'],
                        'options' => ['class' => $url=='/archive' ? 'main_menu-selected' : ''],
                        'linkOptions' => ['class' => 'menu__item'],
                    ]),
                    (['label' => 'Личный кабинет',
                        'url' => ['/profile'],
                        'options' => ['class' => $url=='/profile' ? 'main_menu-selected' : ''],
                        'linkOptions' => ['class' => 'menu__item'],
                    ]),
                    (
                        '<li class="menu__logout">'
                        . Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                            'Выход (' . Yii::$app->user->identity->email . ')',
                            ['class' => 'btn btn-link logout']
                        )
                        . Html::endForm()
                        . '</li>'
                    ),
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="main__data">
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->name . ' ' . date('Y') ?></p>
        <p class="pull-right"> Разработано PlanB </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
