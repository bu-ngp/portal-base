<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\exceptions\ServiceErrorsException;
use domain\models\base\AuthItem;
use domain\models\base\AuthItemChild;
use domain\repositories\base\AuthItemChildRepository;
use domain\repositories\base\RoleRepository;
use domain\services\BaseService;
use domain\services\TransactionManager;
use Exception;
use RuntimeException;

class RoleService extends BaseService
{
    private $roleRepository;
    private $transactionManager;
    private $authItemChildRepository;

    public function __construct(
        RoleRepository $roleRepository,
        AuthItemChildRepository $authItemChildRepository,
        TransactionManager $transactionManager
    )
    {
        $this->roleRepository = $roleRepository;
        $this->authItemChildRepository = $authItemChildRepository;
        $this->transactionManager = $transactionManager;

        parent::__construct();
    }

    public function create($name, $description, $type, $assignedKeys)
    {
        if (!is_string($assignedKeys) || ($assignedKeys = json_decode($assignedKeys)) === null) {
            throw new ServiceErrorsException('assignRoles', \Yii::t('common/roles', 'Error when recognizing selected items'));
        }

        $authItem = AuthItem::create($name, $description, $type);
        $authItemChild = AuthItemChild::create($authItem, $assignedKeys);

        $this->transactionManager->execute(function () use ($authItem, $authItemChild) {
            $this->roleRepository->add($authItem);
            $this->authItemChildRepository->add($authItemChild);
        });

        return true;
    }

    public function update($id, $description)
    {
        $authItem = $this->roleRepository->find($id);
        $authItem->rename($description);
        $this->roleRepository->save($authItem);

        return true;
    }
}