<?php

namespace domain\services\base;

use domain\models\base\Podraz;
use domain\repositories\base\PodrazRepository;
use domain\services\BaseService;

class PodrazService extends BaseService
{
    private $podrazRepository;

    public function __construct(
        PodrazRepository $podrazRepository
    )
    {
        $this->podrazRepository = $podrazRepository;

        parent::__construct();
    }

    public function create($podraz_name)
    {
        $podraz = Podraz::create($podraz_name);
        $this->podrazRepository->add($podraz);

        return true;
    }

    public function update($id, $podraz_name)
    {
        $podraz = $this->podrazRepository->find($id);

        $podraz->editData($podraz_name);
        $this->podrazRepository->save($podraz);

        return true;
    }

    public function delete($id)
    {
        $podraz = $this->podrazRepository->find($id);
        $this->podrazRepository->delete($podraz);
    }
}