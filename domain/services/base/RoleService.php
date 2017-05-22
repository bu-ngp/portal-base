<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

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

    public function create($name, $description, $type, array $assignedKeys)
    {
        $authItem = AuthItem::create($name, $description, $type);
        $authItemChild = AuthItemChild::create($authItem, $assignedKeys);

        $this->transactionManager->execute(function () use ($authItem, $authItemChild) {
            $this->roleRepository->add($authItem);
            $role = \Yii::$app->authManager->getRole($authItem->name);
            $this->authItemChildRepository->add($authItemChild);
        });

        return $authItem;
    }

}