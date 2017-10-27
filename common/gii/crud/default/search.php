<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();
/** @var \yii\db\ActiveRecord $modelAR */
$modelAR = new $generator->modelClass;

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;
use domain\services\SearchModel;

class <?= $searchModelClass ?> extends SearchModel
{
    public static function activeRecord()
    {
        return new <?= $modelClass ?>;
    }

    public function attributes()
    {
        return [
<?php
        foreach ($searchAttributes as $attribute) {
            if ($modelAR->isAttributeSafe($attribute)) {
                echo "            '$attribute',\n";
            }
        }
?>
        ];
    }

<?php
foreach ($attributes = $modelAR->safeAttributes() as $attribute) {
    if (!in_array($attribute, array_keys($modelAR->getPrimaryKey(true)))  ) {
        echo "    public function defaultSortOrder()\n";
        echo "    {\n";
        echo "        return ['$attribute' => SORT_ASC];\n";
        echo "    }\n\n";
        break;
    }
}
?>
    public function filter()
    {
        return [

        ];
    }
}
