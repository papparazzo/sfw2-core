<?php
$host = $_SERVER['HTTP_HOST'];

return [
    'database' => [
        'host' => 'localhost',
        'user' => '',
        'pwd'  => '',
        'db'   => ''
    ],

    'site' => [
        'offline'            => true,
        'offlineBypassToken' => '9658ed3dfef655860934878aee7c96946ea0b0d73612c5'
    ],

    'debug' => [
        'on'          => false,
        'title'       => '[DEBUG New]'
    ],

    'defEMailAddr' => [
        'addr' => 'noreply@' . $host,
        'name' => 'noreply'
    ],

    'project' => [
        'title'          => '',
        'eMailWebMaster' => 'webmaster@' . $host,
    ],

    'path' => [
        'tmp'        => 'tmp/',
        'log'        => 'weblog/',
        'data'       => 'data/',
        'gallery'    => 'public/content/',

#  FIXME 'templatePath'    => 'web/templates/',
#        'templatePathSFW' => dirname(__FILE__) . '/Templates/',

#        'jsPath'          => 'public/js/',
#        'jsPathSFW'       =>  dirname(__FILE__) . '/Public/js/',

#        'cssPath'         => 'public/css/',
#        'cssPathSFW' => dirname(__FILE__) . '/Public/css/'
    ],

    'misc' => [
        'timeZone'    => 'Europe/Berlin',
        'locale'      => 'de_DE',
        'memoryLimit' => 256
    ]
];
