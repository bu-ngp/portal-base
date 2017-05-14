<?php
/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 14.05.2017
 * Time: 8:14
 */

namespace domain\repositories;

interface RepositoryInterface
{
    public function find($id);

    public function add($model);

    public function save($model);

    public function delete($model);
}