<?php

/* @var $this yii\web\View */

use yii\helpers\Inflector;

/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

$ns = preg_replace('/(.*?services\\\\)(\w+)(.*)/', '$1services$3', $generator->ns);
$nsRepositories = preg_replace('/(.*?services\\\\)(\w+)(.*)/', '$1repositories$3', $generator->ns);
$nsForms = preg_replace('/(.*?services\\\\)(\w+)(.*)/', '$1forms$3', $generator->ns);

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

$pluralizeRepo = Inflector::pluralize($generator->modelClass);

echo "<?php\n";
?>

namespace <?= $ns ?>;

use <?= $generator->ns ?>\<?= $generator->modelClass ?>;
use <?= $nsRepositories ?>\<?= $generator->modelClass ?>Repository;
use domain\services\Service;
use <?= $nsForms ?>\<?= $generator->modelClass ?>Form;

class <?= $generator->modelClass ?>Service extends Service
{
    private $<?= lcfirst($pluralizeRepo) ?>;

    public function __construct(
        <?= $generator->modelClass ?>Repository $<?= lcfirst($pluralizeRepo) . "\n" ?>
    )
    {
        $this-><?= lcfirst($pluralizeRepo) ?> = $<?= lcfirst($generator->modelClass) ?>;

        parent::__construct();
    }

    public function find($id)
    {
        return $this-><?= lcfirst($pluralizeRepo) ?>->find($id);
    }

    public function create(<?= $generator->modelClass ?>Form $form)
    {
        $<?= lcfirst($generator->modelClass) ?> = <?= $generator->modelClass ?>::create($form);
        if (!$this->validateModels($<?= lcfirst($generator->modelClass) ?>, $form)) {
            throw new \DomainException();
        }

        $this-><?= lcfirst($pluralizeRepo) ?>->add($<?= lcfirst($generator->modelClass) ?>);
    }

    public function update($id, <?= $generator->modelClass ?>Form $form)
    {
        $<?= lcfirst($generator->modelClass) ?> = $this-><?= lcfirst($pluralizeRepo) ?>->find($id);
        $<?= lcfirst($generator->modelClass) ?>->edit($form);
        if (!$this->validateModels($<?= lcfirst($generator->modelClass) ?>, $form)) {
            throw new \DomainException();
        }

        $this-><?= lcfirst($pluralizeRepo) ?>->save($<?= lcfirst($generator->modelClass) ?>);
    }

    public function delete($id)
    {
        $<?= lcfirst($generator->modelClass) ?> = $this-><?= lcfirst($pluralizeRepo) ?>->find($id);
        $this-><?= lcfirst($pluralizeRepo) ?>->delete($<?= lcfirst($generator->modelClass) ?>);
    }
}