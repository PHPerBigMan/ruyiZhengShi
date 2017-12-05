<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],
        'img'=>[
            'driver'=>'local',
            'root'=>'/data/wwwroot/app/public/uploads'
        ],
        'fcz'=>[
            'driver'=>'local',
            'root'=>'/data/wwwroot/app/public/uploads'
        ],
        'yyzz'=>[
            'driver'=>'local',
            'root'=>'/data/wwwroot/app/public/uploads'
        ],
        'icon'=>[
            'driver'=>'local',
            'root'=>'/data/wwwroot/app/public/icon'
        ],
        'qiniu' => [
            'driver'     => 'qiniu',
            'access_key' => env('QINIU_ACCESS_KEY', '3UDVlK02YbuFZdjmfCuPkaQBvpUQnPTrG_XuPlW3'),
            'secret_key' => env('QINIU_SECRET_KEY', 'yhUpvpfXrIr54wKczPHucML-8DWFC3GxuJljAV4e'),
            'bucket'     => env('QINIU_BUCKET', 'myownImg'),
            'domain'     => env('QINIU_DOMAIN', 'up.qiniup.com'), // or host: https://xxxx.clouddn.com
        ],
    ],

];
