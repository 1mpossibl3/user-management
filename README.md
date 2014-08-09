User management module for Yii 2
=====
Provide:
* usage of relations in views and search
* image, sorter, status columns
* better views
* integrations with other my extensions
* autocompletion

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist webvimark/generators "*"
```

or add

```
"webvimark/generators": "*"
```

to the require section of your `composer.json` file.

Configuration
-------------

In your config/web.php

```php
	'components'=>[
		'user' => [
			'class' => 'webvimark\modules\UserManagement\components\UserConfig',
		],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
		],
	],

	'modules'=>[
		'user-management' => [
        		'class' => 'webvimark\modules\UserManagement\UserManagementModule',
        	],
	],
```

Usage
-----

Go to gii
