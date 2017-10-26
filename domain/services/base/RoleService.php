<?php

/**
 * Created by PhpStorm.
 * User: VOVANCHO
 * Date: 13.05.2017
 * Time: 18:27
 */

namespace domain\services\base;

use domain\forms\base\RoleForm;
use domain\forms\base\RoleUpdateForm;
use domain\models\base\AuthItem;
use domain\models\base\AuthItemChild;
use domain\repositories\base\AuthItemChildRepository;
use domain\repositories\base\RoleRepository;
use domain\services\TransactionManager;
use domain\services\Service;
use Yii;

class RoleService extends Service
{
    private $roles;
    private $transactionManager;
    private $authItemChilds;

    public function __construct(
        RoleRepository $roles,
        AuthItemChildRepository $authItemChilds,
        TransactionManager $transactionManager
    )
    {
        $this->roles = $roles;
        $this->authItemChilds = $authItemChilds;
        $this->transactionManager = $transactionManager;
    }

    public function get($id) {
        return $this->roles->find($id);
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

        $authItem = AuthItem::create($form);
        if (!$this->validateModels($authItem, $form)) {
            throw new \DomainException();
        }

        $authItemChild = AuthItemChild::create($authItem, $assignedKeys);

        return $this->transactionManager->execute(function () use ($authItem, $authItemChild) {
            $this->roles->add($authItem);

            foreach ($authItemChild as $item) {
                $this->authItemChilds->add($item);
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
        $authItem = $this->roles->find($id);

        if ($this->roles->isEmptyChildren($authItem)) {
            throw new \DomainException(Yii::t('common/roles', 'Need add roles'));
        }

        $authItem->edit($form);

        if (!$this->validateModels($authItem, $form)) {
            throw new \DomainException();
        }

        $this->roles->save($authItem);
    }

    /**
     * Удаление прикрепленной роли на форме редактирования роли
     *
     * @param string $parent Редактируемая роль
     * @param string $child Прикрепленная роль к редактируемой роли
     */
    public function removeRoleForUpdate($parent, $child)
    {
        $authItemChildModel = $this->authItemChilds->find(['parent' => $parent, 'child' => $child]);
        $this->authItemChilds->delete($authItemChildModel);
    }

    /**
     * Удаление роли со всеми прикрепленными ролями
     *
     * @param string $id Идентификатор удалемой роли
     * @throws \Exception
     */
    public function removeRole($id)
    {
        $authItem = $this->roles->findByUser($id);

        $this->transactionManager->execute(function () use ($authItem) {
            $this->authItemChilds->removeChildren($authItem);
            $this->roles->delete($authItem);
        });
    }

    private function guardAssignRoles($form)
    {
        if (!is_string($form->assignRoles) || ($assignedKeys = json_decode($form->assignRoles)) === null) {
            throw new \DomainException(Yii::t('domain/base', 'Error when recognizing selected items'));
        }

        if (!$assignedKeys) {
            Yii::$app->session->addFlash('error', (Yii::t('common/roles', 'Need add roles')));
        }

        return $assignedKeys;
    }
}