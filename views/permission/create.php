<?php
/**
 *
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\webvimark\modules\UserManagement\forms\ItemForm $model
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Создание правила';
$this->params['breadcrumbs'][] = ['label' => 'Права', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<strong>
			<span class="glyphicon glyphicon-th"></span> <?= $this->title ?>
		</strong>
	</div>
	<div class="panel-body">
		<?= $this->render('_form', [
			'model'=>$model,
			'insert'=>true,
		]) ?>
	</div>
</div>