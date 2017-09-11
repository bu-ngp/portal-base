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

    /**
     * Добавление новой пользовательской роли с выбранными подчиненными ролями
     *
     * @param string $name Уникальное имя роли
     * @param string $description Название роли
     * @param integer $type
     * @param string $assignedKeys массив с первичными ключами выбранных записей
     * @return bool
     * @throws \Exception
     */
    public function create($name, $description, $type, $assignedKeys)
    {
        if (!is_string($assignedKeys) || ($assignedKeys = json_decode($assignedKeys)) === null) {
            throw new ServiceErrorsException('assignRoles', \Yii::t('common/roles', 'Error when recognizing selected items'));
        }

        if (!$assignedKeys) {
            throw new ServiceErrorsException('notifyShower', \Yii::t('common/roles', 'Need add roles'));
        }

        $authItem = AuthItem::create($name, $description, $type);
        $authItemChild = AuthItemChild::create($authItem, $assignedKeys);

        $this->transactionManager->execute(function () use ($authItem, $authItemChild) {
            $this->roleRepository->add($authItem);
            $this->authItemChildRepository->add($authItemChild);
        });

        return true;
    }

    /**
     * Обновление названия роли на форме редактирования роли
     *
     * @param string $id Идентификатор обновляемой роли
     * @param string $description Новое название роли
     * @return bool
     */
    public function update($id, $description)
    {
        $authItem = $this->roleRepository->find($id);

        if ($this->roleRepository->isEmptyChildren($authItem)) {
            throw new ServiceErrorsException('notifyShower', \Yii::t('common/roles', 'Need add roles'));
        }

        $authItem->rename($description);
        $this->roleRepository->save($authItem);

        return true;
    }

    /**
     * Удаление прикрепленной роли на форме редактирования роли
     *
     * @param string $parent Редактируемая роль
     * @param string $child Прикрепленная роль к редактируемой роли
     */
    public function removeRoleForUpdate($parent, $child)
    {
        $authItemChildModel = $this->authItemChildRepository->find(['parent' => $parent, 'child' => $child]);
        $this->authItemChildRepository->delete($authItemChildModel);
    }

    /**
     * Удаление роли со всеми прикрепленными ролями
     *
     * @param string $id Идентификатор удалемой роли
     * @throws \Exception
     */
    public function removeRole($id)
    {
        $authItem = $this->roleRepository->findByUser($id);

        $this->transactionManager->execute(function () use ($authItem) {
            $this->authItemChildRepository->removeChildren($authItem);
            $this->roleRepository->delete($authItem);
        });
    }
}