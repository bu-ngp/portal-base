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

$ns = preg_replace('/(.*?services\\\\)(\w+)(.*)/', '$1forms$3', $generator->ns);

$safeAttributes = array_filter(array_keys($tableSchema->columns), function($value) use ($tableSchema) {
    return !$tableSchema->columns[$value]->isPrimaryKey;
});

echo "<?php\n";
?>

namespace <?= $ns ?>;

use <?= $generator->ns ?>\<?= $generator->modelClass ?>;
use yii\base\Model;

class <?= $generator->modelClass ?>Form extends Model
{
<?php
    $attributes = [];

    foreach ($tableSchema->columns as $column) {
        if (in_array($column->name, $safeAttributes)) {
            $attributes[]= '    public $' . $column->name;
        }
    }

    if ($attributes) {
        echo implode(";\n", $attributes) . ";\n";
    }
?>

    public function __construct(<?= $generator->modelClass ?> $<?= lcfirst($generator->modelClass) ?> = null, $config = [])
    {
        if ($<?= lcfirst($generator->modelClass) ?>) {
<?php
               foreach ($tableSchema->columns as $column) {
                   if (in_array($column->name, $safeAttributes)) {
                       echo "           \$this->{$column->name} = \$".lcfirst($generator->modelClass)."->{$column->name};\n";
                   }
               }
?>
        }

        parent::__construct($config);
    }

    public function attributeLabels()
    {
        return (new <?= $generator->modelClass ?>())->attributeLabels();
    }
}