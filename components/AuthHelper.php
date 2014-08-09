<?php

namespace webvimark\modules\UserManagement\components;


use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Inflector;
use yii\helpers\Url;

class AuthHelper
{

	/**
	 * Return '/some/another/here'
	 *
	 * @param string|array $route
	 *
	 * @return string
	 */
	public static function unifyRoute($route)
	{
		return '/' . ltrim(Url::to($route), '/');
	}


	/**
	 * Select items that has "/" in permissions
	 *
	 * @param array $allPermissions - \Yii::$app->authManager->getPermissions()
	 *
	 * @return object
	 */
	public static function separateRoutesAndPermissions($allPermissions)
	{
		$arrayOfPermissions = $allPermissions;

		$routes = [];
		$permissions = [];

		foreach ($arrayOfPermissions as $id => $item)
		{
			if ( strpos($item->name, '/') !== false )
			{
				$routes[$id] = $item;
			}
			else
			{
				$permissions[$id] = $item;
			}
		}

//		sort($routes);

		return (object)compact('routes', 'permissions');
	}

	/**
	 * @return array
	 */
	public static function getAllModules()
	{
		$result = [];

		$currentEnvModules = \Yii::$app->getModules();

		foreach ($currentEnvModules as $moduleId => $uselessStuff)
		{
			$result[$moduleId] = \Yii::$app->getModule($moduleId);
		}

		return $result;
	}


	/**
	 * @return array
	 */
	public static function getRoutes()
	{
		$result = [];
		self::getRouteRecursive(Yii::$app, $result);

		return array_reverse(array_combine($result, $result));
	}

	/**
	 * @param \yii\base\Module $module
	 * @param array            $result
	 */
	private static function getRouteRecursive($module, &$result)
	{
		foreach ($module->getModules() as $id => $child)
		{
			if ( ($child = $module->getModule($id)) !== null )
			{
				self::getRouteRecursive($child, $result);
			}
		}
		/* @var $controller \yii\base\Controller */
		foreach ($module->controllerMap as $id => $value)
		{
			$controller = Yii::createObject($value, [
				$id,
				$module
			]);
			self::getActionRoutes($controller, $result);
			$result[] = '/' . $controller->uniqueId . '/*';
		}

		$namespace = trim($module->controllerNamespace, '\\') . '\\';
		self::getControllerRoutes($module, $namespace, '', $result);

		if ( $module->uniqueId )
		{
			$result[] = '/'. $module->uniqueId . '/*';
		}
		else
		{
			$result[] = $module->uniqueId . '/*';
		}
	}

	/**
	 * @param \yii\base\Controller $controller
	 * @param Array                $result all controller action.
	 */
	private static function getActionRoutes($controller, &$result)
	{
		$prefix = '/' . $controller->uniqueId . '/';
		foreach ($controller->actions() as $id => $value)
		{
			$result[] = $prefix . $id;
		}
		$class = new \ReflectionClass($controller);
		foreach ($class->getMethods() as $method)
		{
			$name = $method->getName();
			if ( $method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions' )
			{
				$result[] = $prefix . Inflector::camel2id(substr($name, 6));
			}
		}
	}

	/**
	 * @param \yii\base\Module $module
	 * @param $namespace
	 * @param $prefix
	 * @param $result
	 */
	private static function getControllerRoutes($module, $namespace, $prefix, &$result)
	{
		try
		{
			$path = Yii::getAlias('@' . str_replace('\\', '/', $namespace));
		}
		catch (InvalidParamException $e)
		{
			$path = $module->getBasePath() . '/controllers';
		}

		foreach (scandir($path) as $file)
		{
			if ( strpos($file, '.') === 0 )
			{
				continue;
			}

			if ( is_dir($path . '/' . $file) )
			{
				self::getControllerRoutes($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
			}
			elseif ( strcmp(substr($file, -14), 'Controller.php') === 0 )
			{
				$id = Inflector::camel2id(substr(basename($file), 0, -14));
				$className = $namespace . Inflector::id2camel($id) . 'Controller';
				if ( strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller') )
				{
					$controller = new $className($prefix . $id, $module);
					self::getActionRoutes($controller, $result);
					$result[] = '/' . $controller->uniqueId . '/*';
				}
			}
		}
	}
} 