<?php

namespace webvimark\modules\UserManagement\controllers;

use webvimark\components\BaseController;
use webvimark\modules\UserManagement\components\AuthHelper;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use yii\web\NotFoundHttpException;
use Yii;

class UserPermissionController extends BaseController
{
	/**
	 * Set layout from config
	 *
	 * @inheritdoc
	 */
	public function beforeAction($action)
	{
		if ( parent::beforeAction($action) )
		{
			$layouts = $this->module->layouts[$this->id];

			if ( isset($layouts[$action->id]) )
			{
				$this->layout = $layouts[$action->id];
			}
			elseif ( isset($layouts['*']) )
			{
				$this->layout = $layouts['*'];
			}

			return true;
		}

		return false;
	}

	/**
	 * @param int $id User ID
	 *
	 * @throws \yii\web\NotFoundHttpException
	 * @return string
	 */
	public function actionSet($id)
	{
		$user = User::findOne($id);

		if ( !$user )
		{
			throw new NotFoundHttpException('User not found');
		}

		return $this->renderIsAjax('set', compact('user'));
	}

	/**
	 * @param int $id - User ID
	 */
	public function actionSetRoles($id)
	{
		$oldAssignments = array_keys(Role::getUserRoles($id));

		// To be sure that user didn't attempt to assign himself some unavailable roles
		$newAssignments = array_intersect(Role::getAvailableRoles(true), Yii::$app->request->post('roles', []));

		$toAssign = array_diff($newAssignments, $oldAssignments);
		$toRevoke = array_diff($oldAssignments, $newAssignments);

		foreach ($toRevoke as $role)
		{
			User::revokeRole($id, $role);
		}

		foreach ($toAssign as $role)
		{
			User::assignRole($id, $role);
		}

		AuthHelper::invalidatePermissions();

		$this->redirect(['set', 'id'=>$id]);
	}
}
