<?php

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

$ns = preg_replace('/(.*?services\\\\)(\w+)(.*)/', '$1repositories$3', $generator->ns);

echo "<?php\n";
?>

namespace <?= $ns ?>;

use <?= $generator->ns ?>\<?= $generator->modelClass ?>;
use domain\exceptions\ServiceErrorsException;
use RuntimeException;
use Yii;

class <?= $generator->modelClass ?>Repository
{
    /**
     * @param $id
     * @return <?= $generator->modelClass . "\n" ?>
     */
    public function find($id)
    {
        if (!$<?= lcfirst($generator->modelClass) ?> = <?= $generator->modelClass ?>::findOne($id)) {
            throw new RuntimeException('Model not found.');
        }

        return $<?= lcfirst($generator->modelClass) ?>;
    }

    /**
     * @param <?= $generator->modelClass ?> $<?= lcfirst($generator->modelClass) . "\n" ?>
     */
    public function add($<?= lcfirst($generator->modelClass) ?>)
    {
        if (!$<?= lcfirst($generator->modelClass) ?>->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if (!$<?= lcfirst($generator->modelClass) ?>->insert(false)) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param <?= $generator->modelClass ?> $<?= lcfirst($generator->modelClass) . "\n" ?>
     */
    public function save($<?= lcfirst($generator->modelClass) ?>)
    {
        if ($<?= lcfirst($generator->modelClass) ?>->getIsNewRecord()) {
            throw new \DomainException(Yii::t('domain/base', 'Adding existing model.'));
        }
        if ($<?= lcfirst($generator->modelClass) ?>->update(false) === false) {
            throw new \DomainException(Yii::t('domain/base', 'Saving error.'));
        }
    }

    /**
     * @param <?= $generator->modelClass ?> $<?= lcfirst($generator->modelClass) . "\n" ?>
     */
    public function delete($<?= lcfirst($generator->modelClass) ?>)
    {
        if (!$<?= lcfirst($generator->modelClass) ?>->delete()) {
            throw new \DomainException(Yii::t('domain/base', 'Deleting error.'));
        }
    }
}