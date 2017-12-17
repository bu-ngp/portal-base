<?php

/* @var $this \yii\web\View */

/* @var $content string */

use common\widgets\Breadcrumbs\Breadcrumbs;
use common\widgets\PropellerAssets\PropellerAsset;
use common\widgets\ReportLoader\ReportLoader;
use lo\modules\noty\Wrapper;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use common\assets\AppCommonAsset;

/*if (file_exists(Yii::getAlias('@app') . '/views/layouts/assets.php')) {
    $this->beginContent('@app/views/layouts/assets.php');
    $this->endContent();
} else {
    AppCommonAsset::register($this);
}*/
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl ?>/favicon.ico" type="image/x-icon"/>
    <base href="<?= Yii::$app->request->getBaseUrl() . '/' ?>">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrapper" style="opacity: 0;">
    <div class="container">
        <?php
        NavBar::begin([
            'brandLabel' => $this->render(Yii::$app->params['brandLabelView']),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-inverse navbar-fixed-top pmd-navbar pmd-z-depth',
            ],
        ]);
        $menuItems = [
            ['label' => 'Главная', 'url' => Yii::$app->urlManager->createUrl(['/']), 'linkOptions' => ['class' => 'pmd-ripple-effect']],
            [
                'label' => 'Система',
                'url' => '#',
                'linkOptions' => [
                    'class' => 'pmd-ripple-effect dropdown-toggle',
                    'data-sidebar' => 'true',
                ],
                'options' => ['class' => 'dropdown pmd-dropdown'],
                'items' => \yii\helpers\ArrayHelper::merge(
                    [
                        [
                            'label' => Yii::t('common/navbar', 'Requested reports'),
                            'url' => '#',
                            'linkOptions' => [
                                'class' => 'btn btn-sm pmd-btn-flat pmd-ripple-effect btn-default wk-widget-reports-loader',
                                'data-target' => '#wk-Report-Loader',
                                'data-toggle' => 'modal',
                            ],
                        ],
                        [
                            'label' => Yii::t('common/navbar', 'Handlers'),
                            'url' => Yii::$app->urlManagerAdmin->createUrl(['doh']),
                            'linkOptions' => ['class' => 'btn btn-sm pmd-btn-flat pmd-ripple-effect btn-default pmd-ripple-effect'],
                        ],
                        [
                            'label' => Yii::t('common/navbar', 'Updates'),
                            'url' => Yii::$app->urlManagerAdmin->createUrl(['updates']),
                            'linkOptions' => ['class' => 'btn btn-sm pmd-btn-flat pmd-ripple-effect btn-default pmd-ripple-effect'],
                        ],

                    ], Yii::$app->user->isGuest ? [] : [
                    '<li class="divider"></li>',
                    [
                        'label' => Yii::t('common/navbar', 'Change Password'),
                        'url' => Yii::$app->urlManagerAdmin->createUrl(['configuration/users/change-password']),
                        'linkOptions' => ['class' => 'btn btn-sm pmd-btn-flat pmd-ripple-effect btn-default pmd-ripple-effect'],
                    ],
                ]
                ),


            ],

        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Войти', 'url' => Yii::$app->urlManagerAdmin->createUrl('login'), 'linkOptions' => ['class' => 'pmd-ripple-effect']];
        } else {
            $menuItems[] = '<li>'
                . Html::beginForm(Yii::$app->urlManagerAdmin->createUrl(['site/logout']), 'post')
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->person_fullname . ')',
                    ['class' => 'btn btn-link pmd-ripple-effect logout']
                )
                . Html::endForm()
                . '</li>';
        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
        ?>

        <div class="wrap">
            <?php $this->context->id === "site" && $this->context->action->id === "login" ? Breadcrumbs::hide() : null ?>
            <?= Breadcrumbs::widget() ?>
            <?= $content ?>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container-footer">
        <p>
            &copy; <?= Yii::$app->get('config')->config_common_footer_company ?: 'My Company' ?> <?= date('Y') ?></p>
        <p> <?= Yii::$app->get('config')->config_common_footer_addition ?: 'Author Portal' ?></p>
    </div>
</footer>
<?php $dbName = preg_replace('/(.*)dbname=(\w+);?/', '$2', Yii::$app->db->dsn) ?>
<div class="wk-test-db"
     db="<?= isset(Yii::$app->params['productionDatabase']) && Yii::$app->params['productionDatabase'] !== $dbName ? 'dev' : 'prod' ?>">
    <div>
        <p>Тестовая</p>
        <p>База</p>
    </div>
    <div>
        <i class="fa fa-4x fa-warning"></i>
    </div>
</div>
<?= ReportLoader::widget(['id' => 'wk-Report-Loader']) ?>
<?= Wrapper::widget([
    'layerClass' => 'lo\modules\noty\layers\Toastr',
]) ?>

<?php
PropellerAsset::setWidget('yii\bootstrap\NavBar');
PropellerAsset::register($this);
AppCommonAsset::register($this);
$this->endBody()
?>
</body>
</html>
<?php $this->endPage() ?>
