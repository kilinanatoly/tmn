<?php

namespace budyaga\users;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class UsersAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@budyaga/users/assets';

    public $js = [
        'js/users.js'
    ];
    public $css = [
        'css/style.css'
    ];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];
}
