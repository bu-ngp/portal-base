<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\bootstrap\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm <?= preg_replace('/(.*)(models)(.*)/','$1forms$3',ltrim($generator->modelClass, '\\') . "Form") ?> */

$this->title = <?= $generator->generateString('Create ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">

    <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>

    <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

        <?= "<?php " ?>$form = ActiveForm::begin(); ?>

<?php
    /** @var \yii\db\ActiveRecord $model */
    $model = new $generator->modelClass();
    $safeAttributes = $model->safeAttributes();
    if (empty($safeAttributes)) {
        $safeAttributes = $model->attributes();
    }
?>
        <?php foreach ($generator->getColumnNames() as $attribute) {
            if (in_array($attribute, $safeAttributes)) {
                echo "<?= " . preg_replace('/\(\$model/','($modelForm',$generator->generateActiveField($attribute))  . " ?>\n\n";
            }
        } ?>
        <div class="form-group">
            <?= "<?= " ?>Html::submitButton(Yii::t('common', 'Create'), ['class' => 'btn btn-success']) ?>
        </div>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>
</div>