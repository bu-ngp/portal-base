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

$ns = preg_replace('/(.*?\\\\)(\w+)(\\\\.*)/', '$1services$3', $generator->ns);
$nsRepositories = preg_replace('/(.*?\\\\)(\w+)(\\\\.*)/', '$1repositories$3', $generator->ns);

$safeAttributes = array_filter(array_keys($tableSchema->columns), function($value) use ($tableSchema) {
    return !$tableSchema->columns[$value]->isPrimaryKey;
});

$attributes = [];
$attributesString = "";
foreach ($tableSchema->columns as $column) {
    if (in_array($column->name, $safeAttributes)) {
        $attributes[]= '$' . $column->name;
    }
}

if ($attributes) {
    $attributesString = implode(", ", $attributes);
}

echo "<?php\n";
?>

namespace <?= $ns ?>;

use <?= $generator->ns ?>\<?= $generator->modelClass ?>;
use <?= $nsRepositories ?>\<?= $generator->modelClass ?>Repository;
use domain\services\BaseService;

class <?= $generator->modelClass ?>Service extends BaseService
{
    private $<?= lcfirst($generator->modelClass) ?>Repository;

    public function __construct(
        <?= $generator->modelClass ?>Repository $<?= lcfirst($generator->modelClass) ?>Repository
    )
    {
        $this-><?= lcfirst($generator->modelClass) ?>Repository = $<?= lcfirst($generator->modelClass) ?>Repository;

        parent::__construct();
    }

    public function create(<?= $attributesString ?>)
    {
        $<?= lcfirst($generator->modelClass) ?> = <?= $generator->modelClass ?>::create(<?= $attributesString ?>);
        $this-><?= lcfirst($generator->modelClass) ?>Repository->add($<?= lcfirst($generator->modelClass) ?>);

        return true;
    }

    public function update($id, <?= $attributesString ?>)
    {
        $<?= lcfirst($generator->modelClass) ?> = $this-><?= lcfirst($generator->modelClass) ?>Repository->find($id);

        $<?= lcfirst($generator->modelClass) ?>->editData(<?= $attributesString ?>);
        $this-><?= lcfirst($generator->modelClass) ?>Repository->save($<?= lcfirst($generator->modelClass) ?>);

        return true;
    }

    public function delete($id)
    {
        $<?= lcfirst($generator->modelClass) ?> = $this-><?= lcfirst($generator->modelClass) ?>Repository->find($id);
        $this-><?= lcfirst($generator->modelClass) ?>Repository->delete($<?= lcfirst($generator->modelClass) ?>);
    }
}