<?php

namespace domain\services\base;

use domain\forms\base\PodrazForm;
use domain\models\base\Podraz;
use domain\repositories\base\PodrazRepository;
use domain\services\WKService;

class PodrazService extends WKService
{
    private $podrazRepository;

    public function __construct(
        PodrazRepository $podrazRepository
    )
    {
        $this->podrazRepository = $podrazRepository;
    }

    public function create(PodrazForm $form)
    {
        $podraz = Podraz::create($form->podraz_name);
        if (!$this->validateModels($podraz, $form)) {
            return false;
        }

        return  $this->podrazRepository->add($podraz);
    }

    public function update($id, PodrazForm $form)
    {
        $podraz = $this->podrazRepository->find($id);
        $podraz->editData($form->podraz_name);
        if (!$this->validateModels($podraz, $form)) {
            return false;
        }

        return $this->podrazRepository->save($podraz);
    }

    public function delete($id)
    {
        $podraz = $this->podrazRepository->find($id);
        $this->podrazRepository->delete($podraz);
    }
}