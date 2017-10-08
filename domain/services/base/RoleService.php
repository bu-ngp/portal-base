<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use common\widgets\NotifyShower\NotifyShower;
use domain\forms\base\RoleForm;
use domain\forms\base\RoleUpdateForm;
use domain\models\base\AuthItem;
use domain\models\base\AuthItemChild;
use domain\repositories\base\AuthItemChildRepository;
use domain\repositories\base\RoleRepository;
use domain\services\TransactionManager;
use domain\services\WKService;

class RoleService extends WKService
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
    }

    /**
     * Добавление новой пользовательской роли с выбранными подчиненными ролями
     *
     * @param RoleForm $form
     * @return bool
     * @throws \Exception
     */
    public function create(RoleForm $form)
    {
        $assignedKeys = $this->guardAssignRoles($form);

        $authItem = AuthItem::create($form->name, $form->description, $form->ldap_group);
        if (NotifyShower::hasErrors() || !$this->validateModels($authItem, $form)) {
            return false;
        }

        $authItemChild = AuthItemChild::create($authItem, $assignedKeys);

        return $this->transactionManager->execute(function () use ($authItem, $authItemChild) {
            $this->roleRepository->add($authItem);

            foreach ($authItemChild as $item) {
                $this->authItemChildRepository->add($item);
            }
        });
    }

    /**
     * Обновление названия роли на форме редактирования роли
     *
     * @param string $id Идентификатор обновляемой роли
     * @param RoleUpdateForm $form
     * @return bool
     */
    public function update($id, RoleUpdateForm $form)
    {
        $authItem = $this->roleRepository->find($id);

        if ($this->roleRepository->isEmptyChildren($authItem)) {
            NotifyShower::message(\Yii::t('common/roles', 'Need add roles'));
        }

        $authItem->editData($form->description, $form->ldap_group);

        if (NotifyShower::hasErrors() || !$this->validateModels($authItem, $form)) {
            return false;
        }

        return $this->roleRepository->save($authItem);
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

    private function guardAssignRoles($form)
    {
        if (!is_string($form->assignRoles) || ($assignedKeys = json_decode($form->assignRoles)) === null) {
            NotifyShower::message(\Yii::t('common/roles', 'Error when recognizing selected items'));

            return false;
        }

        if (!$assignedKeys) {
            NotifyShower::message(\Yii::t('common/roles', 'Need add roles'));
        }

        return $assignedKeys;
    }
}