<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 9:25
 */
namespace frontend\helpers;

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
}