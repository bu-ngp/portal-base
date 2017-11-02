<?php

namespace ngp\services\models;

use ngp\repositories\models\ConfigOfomsRepository;
use domain\services\Service;
use ngp\services\forms\ConfigOfomsUpdateForm;

class ConfigOfomsService extends Service
{
    private $configOfoms;

    public function __construct(
        ConfigOfomsRepository $configOfoms
    )
    {
        $this->configOfoms = $configOfoms;
    }

    public function get()
    {
        return $this->configOfoms->find();
    }

    public function update(ConfigOfomsUpdateForm $form)
    {
        $configOfoms = $this->configOfoms->find();
        $configOfoms->edit($form);
        if (!$this->validateModels($configOfoms, $form)) {
            throw new \DomainException();
        }

        $this->configOfoms->save($configOfoms);
    }
}