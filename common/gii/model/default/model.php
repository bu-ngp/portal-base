<?php
/**
 * This is the template for generating the model class of a specified table.
 */
use common\classes\mysql\Schema;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

$safeAttributes = array_filter(array_keys($tableSchema->columns), function($value) use ($tableSchema) {
    return !$tableSchema->columns[$value]->isPrimaryKey;
});

$uuidPrimaryKeys = array_filter(array_keys($tableSchema->columns), function ($value) use ($tableSchema) {
    return $tableSchema->columns[$value]->isPrimaryKey && $tableSchema->columns[$value]->type === Schema::TYPE_BINARY && $tableSchema->columns[$value]->size === 16;
});

$timestampColumns = array_filter(array_keys($tableSchema->columns), function ($value) use ($tableSchema) {
    return in_array($tableSchema->columns[$value]->name, ['created_at', 'updated_at']);
});

$blameableColumns = array_filter(array_keys($tableSchema->columns), function ($value) use ($tableSchema) {
    return in_array($tableSchema->columns[$value]->name, ['created_by', 'updated_by']);
});

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
<?php if ($uuidPrimaryKeys): ?>
use wartron\yii2uuid\behaviors\UUIDBehavior;
<?php endif; ?>
<?php if (count($timestampColumns) === 2): ?>
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
<?php endif; ?>
<?php if (count($timestampColumns) === 2): ?>
use common\classes\BlameableBehavior;
<?php endif; ?>
/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
        ];
    }
<?php if ($uuidPrimaryKeys || count($timestampColumns) === 2 || count($blameableColumns) === 2): ?>

    public function behaviors()
    {
        return [
<?php if ($uuidPrimaryKeys): ?>
            [
                'class' => UUIDBehavior::className(),
                'column' => '<?= $uuidPrimaryKeys[0] ?>',
            ],
<?php endif; ?>
<?php if (count($timestampColumns) === 2): ?>
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
<?php endif; ?>
<?php if (count($blameableColumns) === 2): ?>
            [
                'class' => BlameableBehavior::className(),
            ],
<?php endif; ?>
        ];
    }
<?php endif; ?>

    public static function create(<?php
        $properties = [];

        foreach ($tableSchema->columns as $column) {
            if (in_array($column->name, $safeAttributes)) {
                $properties[]= '$' . $column->name;
            }
        }

        if ($properties) {
            echo implode(', ', $properties);
        }
?>)
    {
        return new self([
<?php
        $values = [];

        foreach ($tableSchema->columns as $column) {
            if (in_array($column->name, $safeAttributes)) {
                $values[] = "            '{$column->name}' => \${$column->name}";
            }
        }

        if ($values) {
            echo implode(",\n", $values) . ',';
        }
?>

        ]);
    }

    public function editData(<?php
$properties = [];

foreach ($tableSchema->columns as $column) {
    if (in_array($column->name, $safeAttributes)) {
        $properties[] = '$' . $column->name;
    }
}

if ($properties) {
    echo implode(', ', $properties);
}
?>)
    {
<?php
$values = [];

foreach ($tableSchema->columns as $column) {
    if (in_array($column->name, $safeAttributes)) {
        $values[] = "        \$this->{$column->name} = \${$column->name}";
    }
}

if ($values) {
    echo implode(";\n", $values) . ';';
}
?>

    }

<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
