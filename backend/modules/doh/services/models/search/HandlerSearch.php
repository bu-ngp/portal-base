<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 29.09.2017
 * Time: 20:55
 */

namespace doh\services\models\search;

use doh\services\models\Handler;
use domain\services\SearchModel;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class handlerSearch extends SearchModel
{
    public static function activeRecord()
    {
        return new Handler;
    }

    public function attributes()
    {
        return [
            'handler_id',
            'identifier',
            'handler_name',
            'handler_description',
            'handler_at',
            'handler_percent',
            'handler_status',
            'handler_done_time',
            'handler_used_memory',
            'handler_short_report',
            'handler_files',
        ];
    }

    public function defaultSortOrder()
    {
        return ['handler_at' => SORT_DESC];
    }

    public function filter()
    {
        return [
            ['handler_status', SearchModel::STRICT],
            ['handler_at', SearchModel::DATETIME],
            ['handler_description', SearchModel::CONTAIN],
        ];
    }
}