<?php

use common\widgets\GridView\services\GWItemsTrait;
use yii\base\Model;

/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 15:27
 */
class Ofoms extends Model
{
    use GWItemsTrait;

    const MALE = 1;
    const FEMALE = 2;

    /** @var string(14) Код врача из регионального регистра врачей */
    public $att_doct_amb;
    /** @var integer(6) Код МО из регионального регистра МО */
    public $att_lpu_amb;
    /** @var string Дата прикрепления в амбулатории */
    public $dt_att_amb;
    /** @var integer(6) Код МО из регионального регистра МО */
    public $att_lpu_stm;
    /** @var string Дата прикрепления в стоматологии */
    public $dt_att_stm;
    /** @var string(25) Фамилия */
    public $fam;
    /** @var string(25) Имя */
    public $im;
    /** @var string(25) Отчество */
    public $ot;
    /** @var string Дата рождения */
    public $dr;
    /** @var integer(1) Пол */
    public $w;
    /** @var integer(16) Единый номер полиса (ЕНП) */
    public $enp;
    /** @var integer(1) Код вида полиса */
    public $opdoc;
    /** @var string(64) Наименование вида полиса */
    public $polis;
    /** @var string(10) Серия бланка полиса */
    public $spol;
    /** @var string(16) Номер бланка полиса */
    public $npol;
    /** @var string Дата выдачи полиса */
    public $dbeg;
    /** @var string Дата прекращения страхования в субъекте */
    public $dend;
    /** @var string(5) Код СМО */
    public $q;
    /** @var string(254) Наименование СМО */
    public $q_name;
    /** @var string(254) Причина прекращения страхования */
    public $rstop;
    /** @var string(254) Территория страхования */
    public $ter_st;

    public function items()
    {
        return [
            'w' => [
                self::MALE => 'Мужской',
                self::FEMALE => 'Женский',
            ]
        ];
    }

}