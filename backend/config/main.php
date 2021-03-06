<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'name' => "R&D Modules(Chat)",
    'basePath' => dirname(__DIR__),    
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'backend\modules\v1\Module',
            'viewPath' =>'@backend/modules/v1/views'
        ]
    ],
    'components' => [        
        'user' => [
            'identityClass' => 'backend\modules\v1\models\Users',
            'enableAutoLogin' => false,
            'enableSession'  => false,
            'loginUrl' => null
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'request' => [
             'class' => '\yii\web\Request',
             'enableCookieValidation' => false,
             'parsers' => [
             'application/json' => 'yii\web\JsonParser',
             ],
        ],
        'response' => [
                 'format' => yii\web\Response::FORMAT_JSON,
                 'charset' => 'UTF-8',
   
        ],
        'MyComponent' => [
            'class' => 'backend\modules\v1\components\MyComponent',
        ],
     
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
               [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/users',
                      'extraPatterns'=> [
                  
                            'PUT,POST,OPTIONS <id:\d+>' => 'update',
                            'POST,OPTIONS' =>'create',
                            'POST,OPTIONS registration-token' =>'request-registration-token',
                            'GET,OPTIONS {id}/contacts' =>'view',
                            'POST,OPTIONS login' => 'login',
                            'POST,OPTIONS socialmedialogin' => 'social-media-login',
                            'POST,OPTIONS {id}/invitation' => 'invitation',
                            //'GET,OPTIONS {id}/invitation' => 'show-invitations',
                            'GET,OPTIONS {id}/search' => 'search-user',
                            'POST,OPTIONS reset-password-request' => 'request-password-reset',
                            'POST,OPTIONS reset-password' => 'reset-password',
                            'POST,OPTIONS verify-token' => 'verify-token',
                            'GET,OPTIONS verify-registration' => 'verify-registration',
                            'POST,OPTIONS change-password' => 'change-password'
                    ],
                    'tokens' => [
                            
                            '{id}' => '<id:\\d+>' 
                    ]
                    
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user-invitations',
                      'extraPatterns'=> [
                  
                            'PUT,POST,OPTIONS {id}' => 'update',
                            'POST,OPTIONS {id}/invitation' =>'create',
                            'GET,OPTIONS {id}/contacts' =>'view',
                            //'POST,OPTIONS {id}/invitation' => 'invitation',
                            'GET,OPTIONS {id}/invitation' => 'index',
                    ],
                    'tokens' => [
                            
                            '{id}' => '<id:\\d+>' 
                    ]
                    
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user-notifications',
                      'extraPatterns'=> [      
                            'PUT,POST,OPTIONS {id}' => 'update',
                            'POST,OPTIONS {id}/invitation' =>'create',
                            'GET,OPTIONS {id}/contacts' =>'view',
                            'GET,OPTIONS {id}/notification' => 'index',
                            'GET,OPTIONS sendnotification' =>'send-notification'
                ],
                    'tokens' => [
                            
                            '{id}' => '<id:\\d+>' 
                    ]
                    
                ]
            ],        
        ]
    ],
    'params' => $params,
];