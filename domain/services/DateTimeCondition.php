<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 23.10.2017
 * Time: 9:37
 */

namespace domain\services;


use yii\db\Expression;
use domain\helpers\DateHelper;

class DateTimeCondition
{
    const PATTERN = '^(>=|<=|<>|<|>|=)?(\d{2}\.\d{2}\.\d{4})?(\s(\d{2}:\d{2}(:\d{2})?))?((\s+)?(\-)(\s+)?)?(\d{2}\.\d{2}\.\d{4})(\s(\d{2}:\d{2}(:\d{2})?))?';

    const INT = 'int';
    const DATE = 'date';

    private $attribute;
    private $value;
    private $type;

    private $znak;
    private $dateBegin;
    private $timeBegin;
    private $range;
    private $date;
    private $time;

    public function __construct($attribute, $value, $type)
    {
        $this->attribute = $attribute;
        $this->value = $value;
        $this->type = $type;

        preg_match("/" . self::PATTERN . "/", $this->value, $matches);
        $this->znak = empty($matches[1]) ? '=' : $matches[1];
        $this->dateBegin = $matches[2];
        $this->timeBegin = $matches[3];
        $this->range = $matches[8] === '-';
        $this->date = $matches[10];
        $this->time = $matches[11];
    }

    public function convert()
    {
        if (isset($this->value) && $this->value !== '') {
            if ($this->range) {
                switch ($this->type) {
                    case DateTimeCondition::INT:
                        return [
                            'and',
                            ['>=', $this->attribute, new Expression("UNIX_TIMESTAMP(:dateBegin)", [':dateBegin' => DateHelper::rus2iso($this->dateBegin . $this->timeBegin)])],
                            ['<', $this->attribute, new Expression("UNIX_TIMESTAMP(DATE_ADD(:date, INTERVAL 1 DAY))", [':date' => DateHelper::rus2iso($this->date . $this->time)])]
                        ];
                        break;
                    case DateTimeCondition::DATE:
                        return [
                            'and',
                            ['>=', $this->attribute, DateHelper::rus2iso($this->dateBegin . $this->timeBegin)],
                            ['<', $this->attribute, new Expression("DATE_ADD(:date, INTERVAL 1 DAY)", [':date' => DateHelper::rus2iso($this->date . $this->time)])]
                        ];
                        break;
                }
            } else {
                switch ($this->type) {
                    case DateTimeCondition::INT:
                        return [
                            $this->znak,
                            $this->attribute,
                            new Expression("UNIX_TIMESTAMP(:date)", [':date' => DateHelper::rus2iso($this->date . $this->time)])
                        ];
                        break;
                    case DateTimeCondition::DATE:
                        return [
                            $this->znak,
                            $this->attribute,
                            DateHelper::rus2iso($this->date . $this->time)
                        ];
                        break;
                }
            }
        }

        return [];
    }

    public function convertAsSql()
    {
        if (isset($this->value) && $this->value !== '') {
            if ($this->range) {
                switch ($this->type) {
                    case DateTimeCondition::INT:
                        return "{$this->attribute} >= UNIX_TIMESTAMP('" . DateHelper::rus2iso($this->dateBegin . $this->timeBegin) . ")" .
                            " AND {$this->attribute} < UNIX_TIMESTAMP(DATE_ADD('" . DateHelper::rus2iso($this->date . $this->time) . "', INTERVAL 1 DAY))";
                        break;
                    case DateTimeCondition::DATE:
                        return "{$this->attribute} >= '" . DateHelper::rus2iso($this->dateBegin . $this->timeBegin) . "'" .
                            " AND {$this->attribute} < DATE_ADD('" . DateHelper::rus2iso($this->date . $this->time) . "', INTERVAL 1 DAY)";
                        break;
                }
            } else {
                switch ($this->type) {
                    case DateTimeCondition::INT:
                        return "{$this->attribute} {$this->znak} UNIX_TIMESTAMP('" . DateHelper::rus2iso($this->date . $this->time) . "')";
                        break;
                    case DateTimeCondition::DATE:
                        return "{$this->attribute} {$this->znak} '" . DateHelper::rus2iso($this->date . $this->time) . "'";
                        break;
                }
            }
        }

        return '';
    }

}