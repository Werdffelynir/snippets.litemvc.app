<?php

// устанавка переменной с началом отсчета отработки системы
list($microtime, $sec) = explode(chr(32), microtime());
$timeLoader = $sec + $microtime;

// отображение всех ошибок
ini_set("display_errors",1);
error_reporting(E_ALL);




/**  *************************************************************************************
Запуск системы
 *************************************************************************************  */

// Рабочие константы
define('DS', DIRECTORY_SEPARATOR);

// Директория приложения
define('APP', __DIR__.DS);

// Директория системы
define('SYSTEM', __DIR__.DS.'Lib'.DS);

// Директория шаблона
define('LAYOUT', APP.'Views'.DS.'layout'.DS);


// Подключение ядра
include( SYSTEM.'App.php' );


//Конфигурационные настройки приложения
if(file_exists(APP.'config.php')){
    $config = include( APP.'config.php' );
    $config['path'] = __DIR__;
}else
    die('<h1 style="color:#CC0000; text-align:center; margin-top: 100px">App is die! File "config.php" not found!<h1>');

// Инициализация основного класса системы
$App = new App();
$App->getConfig($config);

// Подключение вайла для пользовательских функций.
if(file_exists(APP.'functions.php'))
    include( APP.'functions.php' );

// Пуск
$App->run();