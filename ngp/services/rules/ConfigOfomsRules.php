<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 02.11.2017
 * Time: 16:28
 */

namespace ngp\services\rules;


class ConfigOfomsRules
{
    public static function client()
    {
        return
            [
                [['config_ofoms_active'], 'default', 'value' => 0],
                [['config_ofoms_active'], 'required'],
                [['config_ofoms_active'], 'boolean'],
                [['config_ofoms_url', 'config_ofoms_url_prik', 'config_ofoms_remote_host_name', 'config_ofoms_login', 'config_ofoms_password'], 'string', 'max' => 255],
                [['config_ofoms_url', 'config_ofoms_login', 'config_ofoms_password'], 'required', 'when' => function ($model) {
                    return $model->config_ofoms_active;
                }, 'enableClientValidation' => false],
            ];
    }
}