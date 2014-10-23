<?php
/**
 * @var $this yii\web\View
 * @var yii\widgets\ActiveForm $form
 * @var array $routes
 * @var array $childRoutes
 * @var array $permissions
 * @var array $childPermissions
 * @var yii\rbac\Permission $item
 */
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
$this->title = $item->name;
$this->params['breadcrumbs'][] = ['label' => 'Права', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h2>Настройки для правила: <b><?= $this->title ?></b></h2>
<br/>


<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<strong>
					<span class="glyphicon glyphicon-th"></span> Дочерние права
				</strong>
			</div>
			<div class="panel-body">

				<?= Html::beginForm(['set-child-permissions', 'id'=>$item->name]) ?>

				<?= Html::checkboxList(
					'child_permissions',
					ArrayHelper::map($childPermissions, 'name', 'name'),
					ArrayHelper::map($permissions, 'name', 'description'),
					['separator'=>'<br>']
				) ?>

				<hr/>
				<?= Html::submitButton(
					'<span class="glyphicon glyphicon-ok"></span> Сохранить',
					['class'=>'btn btn-primary btn-sm']
				) ?>

				<?= Html::endForm() ?>
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<strong>
					<span class="glyphicon glyphicon-th"></span> Routes

					<?= Html::a(
						'Refresh routes',
						['refresh-routes', 'id'=>$item->name],
						[
							'class' => 'btn btn-default btn-sm pull-right',
							'style'=>'margin-top:-5px',
						]
					) ?>

				</strong>
			</div>

			<div class="panel-body">

				<?= Html::beginForm(['set-child-routes', 'id'=>$item->name]) ?>

				<div class="row">
					<div class="col-sm-3">
						<?= Html::submitButton(
							'<span class="glyphicon glyphicon-ok"></span> Сохранить',
							['class'=>'btn btn-primary btn-sm']
						) ?>
					</div>

					<div class="col-sm-6">
						<input id="search-in-routes" autofocus="on" type="text" class="form-control input-sm" placeholder="Search route">
					</div>

					<div class="col-sm-3 text-right">
						<span id="show-only-selected-routes" class="btn btn-default btn-sm">
							<i class="fa fa-minus"></i> Show only selected
						</span>
						<span id="show-all-routes" class="btn btn-default btn-sm hide">
							<i class="fa fa-plus"></i> Show all
						</span>

					</div>
				</div>

				<hr/>

				<?= Html::checkboxList(
					'child_routes',
					ArrayHelper::map($childRoutes, 'name', 'name'),
					ArrayHelper::map($routes, 'name', 'name'),
					[
						'id'=>'routes-list',
						'separator'=>'<div class="separator"></div>',
						'item'=>function($index, $label, $name, $checked, $value) {
								return Html::checkbox($name, $checked, [
									'value' => $value,
									'label' => '<span class="route-text">' . $label . '</span>',
									'labelOptions'=>['class'=>'route-label'],
									'class'=>'route-checkbox',
								]);
						},
					]
				) ?>

				<hr/>
				<?= Html::submitButton(
					'<span class="glyphicon glyphicon-ok"></span> Сохранить',
					['class'=>'btn btn-primary btn-sm']
				) ?>

				<?= Html::endForm() ?>

			</div>
		</div>
	</div>
</div>

<?php
$js = <<<JS

var routeCheckboxes = $('.route-checkbox');
var routeText = $('.route-text');

// For checked routes
var backgroundColor = '#D6FFDE';

function showAllRoutesBack() {
	$('#routes-list').find('.hide').each(function(){
		$(this).removeClass('hide');
	});
}

//Make tree-like structure by padding controllers and actions
routeText.each(function(){
	var _t = $(this);

	var chunks = _t.html().split('/').reverse();
	var margin = chunks.length * 40 - 40;

	if ( chunks[0] == '*' )
	{
		margin -= 40;
	}

	_t.closest('label').css('margin-left', margin);

});

// Highlight selected checkboxes
routeCheckboxes.each(function(){
	var _t = $(this);

	if ( _t.is(':checked') )
	{
		_t.closest('label').css('background', backgroundColor);
	}
});

routeCheckboxes.on('change', function(){
	var _t = $(this);

	if ( _t.is(':checked') )
	{
		_t.closest('label').css('background', backgroundColor);
	}
	else
	{
		_t.closest('label').css('background', 'none');
	}
});


// Hide on not selected routes
$('#show-only-selected-routes').on('click', function(){
	$(this).addClass('hide');
	$('#show-all-routes').removeClass('hide');

	routeCheckboxes.each(function(){
		var _t = $(this);

		if ( ! _t.is(':checked') )
		{
			_t.closest('label').addClass('hide');
			_t.closest('div.separator').addClass('hide');
		}
	});
});

// Show all routes back
$('#show-all-routes').on('click', function(){
	$(this).addClass('hide');
	$('#show-only-selected-routes').removeClass('hide');

	showAllRoutesBack();
});

// Search in routes and hide not matched
$('#search-in-routes').on('change keyup', function(){
	var input = $(this);

	if ( input.val() == '' )
	{
		showAllRoutesBack();
		return;
	}

	routeText.each(function(){
		var _t = $(this);

		if ( _t.html().indexOf(input.val()) > -1 )
		{
			_t.closest('label').removeClass('hide');
			_t.closest('div.separator').removeClass('hide');
		}
		else
		{
			_t.closest('label').addClass('hide');
			_t.closest('div.separator').addClass('hide');
		}
	});
});

JS;

$this->registerJs($js);
?>