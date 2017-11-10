<?php

namespace ngp\services\models;

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

    const OPDOC_POLIS_OLD = 1;
    const OPDOC_VREM_SVID = 2;
    const OPDOC_POLIS_ENP = 3;
    const OPDOC_NON_POLIS = 4;
    const OPDOC_NON_VREM_SVID = 5;

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
    /** @var string Вычислительное поле (Прикреплен, не прикреплен, снят с учета) */
    public $ofomsStatus;
    /** @var string Вычислительное поле с ФИО и должностью врача */
    public $ofomsVrach;

    public function attributeLabels()
    {
        return [
            'att_doct_amb' => 'Код врача из регионального регистра врачей',
            'att_lpu_amb' => 'Код МО из регионального регистра МО',
            'dt_att_amb' => 'Дата прикрепления в амбулатории',
            'att_lpu_stm' => 'Код МО из регионального регистра МО',
            'dt_att_stm' => 'Дата прикрепления в стоматологии',
            'fam' => 'Фамилия',
            'im' => 'Имя',
            'ot' => 'Отчество',
            'dr' => 'Дата рождения',
            'w' => 'Пол',
            'enp' => 'Единый номер полиса (ЕНП)',
            'opdoc' => 'Код вида полиса',
            'polis' => 'Наименование вида полиса',
            'spol' => 'Серия бланка полиса',
            'npol' => 'Номер бланка полиса',
            'dbeg' => 'Дата выдачи полиса',
            'dend' => 'Дата прекращения страхования в субъекте',
            'q' => 'Код СМО',
            'q_name' => 'Наименование СМО',
            'rstop' => 'Причина прекращения страхования',
            'ter_st' => 'Территория страхования',
            'ofomsStatus' => 'Статус',
            'ofomsVrach' => 'Врач',
        ];
    }

    public static function items()
    {
        return [
            'w' => [
                self::MALE => 'Мужской',
                self::FEMALE => 'Женский',
            ],
            'opdoc' => [
                self::OPDOC_POLIS_OLD => 'Полис ОМС старого образца',
                self::OPDOC_VREM_SVID => 'Временное свидетельство, подтверждающее оформление полиса обязательного медицинского страхования',
                self::OPDOC_POLIS_ENP => 'Полис ОМС единого образца',
                self::OPDOC_NON_POLIS => 'Состояние на учёте без полиса ОМС',
                self::OPDOC_NON_VREM_SVID => 'Состояние на учёте без временного свидетельства при приёме заявления в иную организацию',
            ],
        ];
    }

}