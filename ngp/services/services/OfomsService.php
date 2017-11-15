<?php
/**
 * Created by PhpStorm.
 * User: sysadmin
 * Date: 09.11.2017
 * Time: 11:50
 */

namespace ngp\services\services;

use doh\services\classes\DoH;
use domain\services\Service;
use ngp\services\forms\OfomsAttachForm;
use ngp\services\forms\OfomsAttachListForm;
use ngp\services\proccesses\OfomsAttachListProccessLoader;
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
            $result = $this->ofoms->attach($form->ffio, $form->enp, $form->vrach_inn);

            if ($result['status'] < 1) {
                throw new \DomainException($result['message']);
            }
        }
    }

    public function attachList(OfomsAttachListForm $form)
    {
        $doh = new DoH(new OfomsAttachListProccessLoader($form));
        $doh->execute();
    }

    protected function vrachChanged(OfomsAttachForm $form)
    {
        return Yii::$app->request->get('vrach_inn') !== $form->vrach_inn;
    }
}