<?php

namespace webvimark\modules\UserManagement\models;

use webvimark\modules\UserManagement\components\UserIdentity;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $confirmation_token
 * @property integer $status
 * @property integer $superadmin
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends UserIdentity
{
	/**
	 * @var string
	 */
	public $gridRoleSearch;

	/**
	* @inheritdoc
	*/
	public static function tableName()
	{
		return 'user';
	}

	/**
	* @inheritdoc
	*/
	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
		];
	}

	/**
	* @inheritdoc
	*/
	public function rules()
	{
		return ArrayHelper::merge(parent::rules(), [
			['username', 'required'],
			['username', 'unique'],
			[['username'], 'string', 'max' => 255],
		]);
	}

	/**
	* @inheritdoc
	*/
	public function attributeLabels()
	{
		return ArrayHelper::merge(parent::attributeLabels(),[
			'id' => 'ID',
			'username' => 'Логин',
			'superadmin' => 'Суперадмин',
			'confirmation_token' => 'Confirmation Token',
			'status' => 'Статус',
			'gridRoleSearch' => 'Роли',
			'created_at' => 'Создано',
			'updated_at' => 'Обновлено',
		]);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getRoles()
	{
		return $this->hasMany(Role::className(), ['name' => 'item_name'])
			->viaTable('auth_assignment', ['user_id'=>'id']);
	}
}
