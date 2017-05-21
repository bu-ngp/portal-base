<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\assets\AppCommonAsset;
use common\widgets\Alert;

if (file_exists(Yii::getAlias('@app') . '/views/layouts/assets.php')) {
    $this->beginContent('@app/views/layouts/assets.php');
    $this->endContent();
} else {
    AppCommonAsset::register($this);
}
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
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrapper" style="opacity: 0;">
    <div class="container">
        <?php
        NavBar::begin([
            'brandLabel' => 'My Company',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-inverse navbar-fixed-top pmd-navbar pmd-z-depth',
            ],
        ]);
        $menuItems = [
            ['label' => 'Главная', 'url' => Yii::$app->urlManagerFrontend->createUrl(['/']), 'linkOptions' => ['class' => 'pmd-ripple-effect']],
        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Войти', 'url' => ['/login'], 'linkOptions' => ['class' => 'pmd-ripple-effect']];
        } else {
            $menuItems[] = '<li>'
                . Html::beginForm(Yii::$app->urlManagerFrontend->createUrl(['/site/logout']), 'post')
                . Html::submitButton(
                    'Выйти (' . Yii::$app->user->identity->person_username . ')',
                    ['class' => 'btn btn-link logout', 'style' => 'padding-top: 20px; padding-bottom: 20px; line-height: 24px;']
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
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

</div>
<?php $this->endBody() ?>
<script>

    function eventsApply() {
        $('td.wk-nowrap').unbind('mouseenter').bind('mouseenter', function () {
            if (parseInt($(this).children('span').css('max-width')) == $(this).children('span').outerWidth()) {
                $(this).tooltip({
                    container: $(this).children('span'),
                    title: $(this).text()
                }).tooltip('show');
            }
        });

        $('table.kv-grid-table').find('td').not('td:has(input.kv-row-checkbox)').unbind('click').click(function () {
            $(this).parent('tr').find('input.kv-row-checkbox').trigger('click');
        });

        $('table.kv-grid-table').find('td').not('td:has(input.kv-row-checkbox)').unbind('dblclick').dblclick(function () {
//        $(this).css('background-color','red');
        });

        $('#w0').find('input.select-on-check-all').unbind('click').click(function () {
            var obj1 = $.parseJSON(localStorage.selectedRows);
            obj1[$this[0].id].checkAll = $(this).prop('checked');
            obj1[$this[0].id].included = [];
            obj1[$this[0].id].excluded = [];
            localStorage.selectedRows = JSON.stringify(obj1);
        });

        $('#w0').find('input.kv-row-checkbox').unbind('click').click(function () {
            saveToStorageSelectedRow($this[0].id, $(this));
        });
    }


    var $this = $('#w0');
    if (typeof localStorage.selectedRows == 'undefined') {
        var selectedRows = {};
        selectedRows[$this[0].id] = {
            checkAll: false,
            included: [],
            excluded: []
        };
        localStorage.selectedRows = JSON.stringify(selectedRows);
    }


    function saveToStorageSelectedRow(gridid, $checkbox) {
        var $grid = $('#' + gridid);
        var obj1 = $.parseJSON(localStorage.selectedRows);

        if (obj1[$grid[0].id].checkAll) {
            if ($checkbox.prop('checked')) {
                var ind1 = obj1[$grid[0].id].excluded.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
                if (ind1 >= 0) {
                    obj1[$grid[0].id].excluded.splice(ind1, 1);
                }
            } else {
                obj1[$grid[0].id].excluded.push($checkbox.parent('td').parent('tr').attr('data-key'));
            }
        } else {
            if ($checkbox.prop('checked')) {
                obj1[$grid[0].id].included.push($checkbox.parent('td').parent('tr').attr('data-key'));
            } else {
                var ind2 = obj1[$grid[0].id].included.indexOf($checkbox.parent('td').parent('tr').attr('data-key'));
                if (ind2 >= 0) {
                    obj1[$grid[0].id].included.splice(ind2, 1);
                }
            }

        }

        localStorage.selectedRows = JSON.stringify(obj1);
    }

    function selectRowsFromStorage(gridid, $checkboxes) {
        var obj1 = $.parseJSON(localStorage.selectedRows);
        var $this = $('#' + gridid);

        if (obj1[$this[0].id].checkAll) {
            $checkboxes.parent('td').parent('tr').addClass('info');
            $checkboxes.prop('checked', true);

            $.each($checkboxes, function () {
                if (obj1[$this[0].id].excluded.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parent('td').parent('tr').removeClass('info');
                    $(this).prop('checked', false);
                }
            });

            if (($checkboxes.length - $checkboxes.not(':checked').length) == $checkboxes.length) {
                $this.find('input.select-on-check-all').prop('checked', true);
            }
        } else {
            $.each($checkboxes, function () {
                if (obj1[$this[0].id].included.includes($(this).parent('td').parent('tr').attr('data-key'))) {
                    $(this).parent('td').parent('tr').addClass('info');
                    $(this).prop('checked', true);
                }
            });
        }
    }

    selectRowsFromStorage($this[0].id, $('#w0').find('input.kv-row-checkbox'));

    $(document).on('pjax:complete', function () {
        selectRowsFromStorage($this[0].id, $('#w0').find('input.kv-row-checkbox'));

        eventsApply();
    });

    eventsApply();

</script>
</body>
</html>
<?php $this->endPage() ?>
