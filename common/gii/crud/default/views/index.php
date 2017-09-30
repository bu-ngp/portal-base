<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;
use <?= $generator->indexWidgetType === 'grid' ? "common\\widgets\\GridView\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>

    <?= $generator->enablePjax ? '<?php Pjax::begin(); ?>' : '' ?>
    <?= "<?= " ?>GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'exportGrid' => [
                'idReportLoader' => 'wk-Report-Loader',
            ],
            'columns' => [
    <?php
    $count = 0;
    if (($tableSchema = $generator->getTableSchema()) === false) {
        foreach ($generator->getColumnNames() as $name) {
            if (++$count < 6) {
                echo "            '" . $name . "',\n";
            } else {
                echo "            // '" . $name . "',\n";
            }
        }
    } else {
        foreach ($tableSchema->columns as $column) {
            if (!$column->isPrimaryKey) {
                $format = $generator->generateColumnFormat($column);
                if (++$count < 6) {
                    echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                } else {
                    echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                }
            }
        }
    }
    ?>
            ],
<?php $viewPath = preg_replace('/.*views\/(.*)/', '$1', $generator->getViewPath()); ?>
            'crudSettings' => [
                'create' => '<?= $viewPath ?>/create',
                'update' => '<?= $viewPath ?>/update',
                'delete' => '<?= $viewPath ?>/delete',
            ],
            'panelHeading' => [
                'icon' => FA::icon(FA::_BARS),
                'title' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>,
            ],
    ]); ?>

<?= $generator->enablePjax ? '<?php Pjax::end(); ?>' : '' ?>
</div>
