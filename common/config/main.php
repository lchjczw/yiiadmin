<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)).'/vendor',
    'runtimePath' => '@root/runtime',
    'timezone' => 'PRC',
    'language' => 'zh-CN',
    'bootstrap' => ['log', 'install'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'timeFormat' => 'HH:mm:ss',
            'decimalSeparator' => '.',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
        ],
        'assetManager' => [
//            'forceCopy' => YII_DEBUG,
            'bundles' => [
                'yii\web\YiiAsset' => [
                    'sourcePath' => '@common/static',
                    'depends' => [
                        'common\assets\ModalAsset'
                    ]
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                '*'=> [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath'=>'@common/messages',
                    'fileMap'=>[
                        'common'=>'common.php',
                        'backend'=>'backend.php',
                        'frontend'=>'frontend.php',
                    ],
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],
                /*'*'=> [
                    'class' => 'yii\i18n\DbMessageSource',
                    'sourceMessageTable'=>'{{%i18n_source_message}}',
                    'messageTable'=>'{{%i18n_message}}',
                    'enableCaching' => YII_ENV_DEV,
                    'cachingDuration' => 3600,
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],*/
            ],
        ],
        'config' => [ //动态配置
            'class' => 'common\\components\\Config',
            'localConfigFile' => '@common/config/main-local.php'
        ],
        'storage' => [
            'class' => 'common\\components\\Storage',
            'basePath' => '@storagePath/upload',
            'baseUrl' => '@storageUrl/upload'
        ],
        'queue' => [
            'class' => \common\components\Queue::className(),
        ],
        'log' => [
            'targets' => [
                'db'=>[
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['warning', 'error'],
                    'except'=>['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                    'prefix'=>function () {
                        $url = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                        return sprintf('[%s][%s]', Yii::$app->id, $url);
                    },
                    'logVars'=>[],
                    'logTable'=>'{{%system_log}}'
                ],
            ]
        ],
        'notify' => 'common\components\notify\Handler',
        'moduleManager' => [
            'class' => 'common\\components\\ModuleManager'
        ]
    ],
    'modules' => [
        'install' => 'install\\Module'
    ],
    'as locale' => [
        'class' => 'common\behaviors\LocaleBehavior',
        'enablePreferredLanguage' => true
    ],
    'on beforeAction' => function($event) {
        if (!Yii::$app->getModule('install')->checkInstalled() && Yii::$app->controller->module->id !== 'install' && Yii::$app instanceof \yii\web\Application) {
            \Yii::$app->getResponse()->redirect(['/install']);
            \Yii::$app->end();
        }
    }
];
