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
        if ($this->vrachChanged($form)) {
            $result = $this->ofoms->attach($this->getFfio($form), $form->enp, $form->vrach_inn);

            if ($result['status'] < 1) {
                throw new \DomainException($result['message']);
            }
        }
    }

    protected function getFfio(OfomsAttachForm $form)
    {
        return mb_substr($form->fam, 0, 3, 'UTF-8') . mb_substr($form->im, 0, 1, 'UTF-8') . mb_substr($form->ot, 0, 1, 'UTF-8') . mb_substr($form->dr, 8, 2, 'UTF-8');
    }

    protected function vrachChanged(OfomsAttachForm $form)
    {
        return Yii::$app->request->get('vrach_inn') !== $form->vrach_inn;
    }
}