<?php

namespace domain\services\base;

use domain\models\base\Parttime;
use domain\repositories\base\ParttimeRepository;
use domain\services\BaseService;

class ParttimeService extends BaseService
{
    private $parttimeRepository;

    public function __construct(
        ParttimeRepository $parttimeRepository
    )
    {
        $this->parttimeRepository = $parttimeRepository;

        parent::__construct();
    }

    public function create($person_id, $dolzh_id, $podraz_id, $build_id, $parttime_begin, $parttime_end, $created_at, $updated_at, $created_by, $updated_by)
    {
        $parttime = Parttime::create($person_id, $dolzh_id, $podraz_id, $build_id, $parttime_begin, $parttime_end, $created_at, $updated_at, $created_by, $updated_by);
        $this->parttimeRepository->add($parttime);

        return true;
    }

    public function update($id, $person_id, $dolzh_id, $podraz_id, $build_id, $parttime_begin, $parttime_end, $created_at, $updated_at, $created_by, $updated_by)
    {
        $parttime = $this->parttimeRepository->find($id);

        $parttime->editData($person_id, $dolzh_id, $podraz_id, $build_id, $parttime_begin, $parttime_end, $created_at, $updated_at, $created_by, $updated_by);
        $this->parttimeRepository->save($parttime);

        return true;
    }

    public function delete($id)
    {
        $parttime = $this->parttimeRepository->find($id);
        $this->parttimeRepository->delete($parttime);
    }
}