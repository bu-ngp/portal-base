<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 9:25
 */

namespace ngp\helpers;

class RbacHelper
{
    /**
     * @var string Разрешение 'Редактирование плиток на главной странице'
     */
    const TILES_EDIT = 'tilesEdit';
    /**
     * @var string Роль 'Оператор плиток на главной странице'
     */
    const TILES_OPERATOR = 'tilesOperator';
    /**
     * @var string Разрешение 'Разрешение проверки полисов на портале ОФОМС'
     */
    const OFOMS_VIEW = 'ofomsView';
    /**
     * @var string Роль 'Проверка полисов на портале ОФОМС'
     */
    const NGP_OFOMS_VIEW = 'ofomsViewNGP';
    /**
     * @var string Разрешение 'Разрешение прикрепления пациентов к врачам ЛПУ на портале ОФОМС'
     */
    const OFOMS_PRIK = 'ofomsPrik';
    /**
     * @var string Роль 'Прикрепление пациентов к врачам ЛПУ на портале ОФОМС'
     */
    const NGP_OFOMS_PRIK = 'ofomsPrikNGP';
    /**
     * @var string Разрешение 'Разрешение прикрепления списком пациентов к врачам ЛПУ на портале ОФОМС'
     */
    const OFOMS_PRIK_LIST = 'ofomsPrikList';
    /**
     * @var string Роль 'Пакетное прикрепление списком пациентов к врачам ЛПУ на портале ОФОМС'
     */
    const NGP_OFOMS_PRIK_LIST = 'ofomsPrikListNGP';
}