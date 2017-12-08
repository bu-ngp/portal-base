<?php

use common\widgets\ActiveForm\ActiveForm;
use common\widgets\Breadcrumbs\Breadcrumbs;
use ngp\assets\OfomsAsset;
use ngp\helpers\RbacHelper;
use rmrevin\yii\fontawesome\FA;
use common\widgets\GridView\GridView;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel \ngp\services\models\search\OfomsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

Breadcrumbs::root();
$this->title = Yii::t('ngp/ofoms', 'Ofoms Portal');
?>
    <div class="ofoms-index content-container">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="form-group pmd-textfield pmd-textfield-floating-label search-ofoms-group">
            <?= Html::label(Yii::t('ngp/ofoms', 'Search'), 'ofoms_search') ?>
            <?= Html::input('text', 'OfomsSearch[search_string]', $searchModel->search_string, ['id' => 'ofoms_search', 'class' => 'form-control']) ?>
            <p class="help-block"><?= Yii::t('ngp/ofoms', 'Type to search input and press Enter button') ?></p>
        </div>

        <?= GridView::widget([
            'id' => 'ofomsGrid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'minHeight' => 400,
            'columns' => [
                [
                    'attribute' => 'ofomsStatus',
                    'format' => 'html',
                ],
                'fam',
                'im',
                'ot',
                'dr',
                'enp',
                [
                    'attribute' => 'spol',
                    'visible' => false,
                ],
                'npol',
                [
                    'attribute' => 'att_doct_amb',
                    'visible' => false,
                ],
                'ofomsVrach',
                [
                    'attribute' => 'att_lpu_amb',
                    'visible' => false,
                ],
                [
                    'attribute' => 'att_lpu_stm',
                    'visible' => false,
                ],
                [
                    'attribute' => 'dt_att_stm',
                    'visible' => false,
                ],
                [
                    'attribute' => 'w',
                    'visible' => false,
                ],
                [
                    'attribute' => 'opdoc',
                    'visible' => false,
                ],
                [
                    'attribute' => 'polis',
                    'visible' => false,
                ],
                [
                    'attribute' => 'dbeg',
                    'visible' => false,
                ],
                [
                    'attribute' => 'dend',
                    'visible' => false,
                ],
                [
                    'attribute' => 'q',
                    'visible' => false,
                ],
                [
                    'attribute' => 'q_name',
                    'visible' => false,
                ],
                [
                    'attribute' => 'rstop',
                    'visible' => false,
                ],
                [
                    'attribute' => 'ter_st',
                    'noWrap' => false,
                ],
            ],
            'customActionButtons' => array_merge(
                Yii::$app->getUser()->can(RbacHelper::OFOMS_PRIK) ? ['prik' => function ($url, $model) {
                    if ($model['dend']) {
                        return '';
                    }

                    return Html::a('<i class="fa fa-2x fa-paperclip"></i>', ['ofoms/attach',
                        'enp' => $model['enp'],
                        'fam' => $model['fam'],
                        'im' => $model['im'],
                        'ot' => $model['ot'],
                        'dr' => $model['dr'],
                        'vrach_inn' => $model['att_doct_amb'],
                    ], [
                        'title' => Yii::t('ngp/ofoms', 'Attach'),
                        'class' => 'btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-primary wk-ofoms-doc-attach',
                        'data-pjax' => '0'
                    ]);
                }] : []),
            'toolbar' => [
                Html::errorSummary($searchModel, ['class' => 'wk-ofoms-errors']),
            ],
            'leftBottomToolbar' => Html::a(Yii::t('ngp/ofoms', 'Attach with list'), ['ofoms/attach-list'], ['class' => 'btn pmd-btn-flat pmd-ripple-effect btn-success', 'data-pjax' => '0']),
            'rightBottomToolbar' => Html::button(Yii::t('ngp/ofoms', 'Rules'), [
                'class' => 'btn pmd-btn-flat pmd-ripple-effect btn-default',
                'data-target' => '#ofoms-rules-dialog',
                'data-toggle' => 'modal',
                'id' => 'ofoms_rules',
            ]),
            'panelHeading' => [
                'icon' => FA::icon(FA::_HOME),
                'title' => Yii::t('ngp/ofoms', 'Ofoms search'),
            ],
        ]) ?>

    </div>

<?php
Modal::begin([
    'id' => 'ofoms-rules-dialog',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h2 class="pmd-card-title-text">' . Yii::t('ngp/ofoms', 'Ofoms search rules') . '</h2>',
    'footer' => '<button data-dismiss="modal" class="btn pmd-btn-flat pmd-ripple-effect btn-default" type="button">' . Yii::t('ngp/ofoms', 'Close') . '</button>',
    'footerOptions' => [
        'class' => 'pmd-modal-action pmd-modal-bordered text-right',
    ],
]);
echo $this->render('_rules');
Modal::end();

OfomsAsset::register($this) ?>