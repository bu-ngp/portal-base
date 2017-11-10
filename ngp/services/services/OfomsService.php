<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.11.2017
 * Time: 11:50
 */

namespace ngp\services\services;

use domain\services\Service;
use ngp\services\forms\OfomsAttachForm;
use ngp\services\repositories\OfomsRepository;
use Yii;

class OfomsService extends Service
{
    private $ofoms;

    public function __construct(
        OfomsRepository $ofoms
    )
    {
        $this->ofoms = $ofoms;
    }

    public function search($searchString)
    {
        return $this->ofoms->search($searchString);
    }

    public function attach(OfomsAttachForm $form)
    {

    }
}