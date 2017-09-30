<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use common\widgets\ActiveForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $modelForm <?= preg_replace('/(.*)(models)(.*)/','$1forms$3',ltrim($generator->modelClass, '\\') . "Form") ?> */

$this->title = Yii::t('<?= $generator->messageCategory ?>', 'Update "{modelClass}": ', [
    'modelClass' => $modelForm-><?= $generator->getNameAttribute() ?>,
]);
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">

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
                echo "<?= " . preg_replace('/\(\$model/','($modelForm',$generator->generateActiveField($attribute)) . " ?>\n\n";
            }
        } ?>
        <div class="form-group">
            <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Update') ?>, ['class' => 'btn btn-primary']) ?>
        </div>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>
</div>