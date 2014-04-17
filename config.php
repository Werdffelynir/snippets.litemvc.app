<?php
/**
 * Файл конфигурации приложения, настройки действительны только для даного приложения.
 * Можно устанавлевать свои параметры и получать к ним доступ с помощью статического метода App::$config['ключ']
 */


return array(

    /**Отладочный режим */
    'debug' => true,

    /**Название преложения */
    'appTitle' => 'My web application',

    /**Копирайт преложения */
    'appCopy' => 'Werdffelynir',

    /** Установить Язык */
    'language' => 'ru',

    /** Определять язык клиента и устанавить его по умолчанию */
    'identifyLanguage' => true,

    /**Файл вхождения в шаблон */
    'layout' => 'template',

    /**Файл вхождения в вид */
    'view' => 'main',

    /** реРоутинг, корекция видимых ссылок.
     * Работает на основе регулярных выражений, расход ресурсов незначительно увеличиваеться.
     * ключ [то что хотим видеть]
     * значение [реальный контроллер метод в фреймворке что вызывается (контролер/метод/парам)] */
    'reRouter' => array(
        // URL:login => controller:index method:login
        #'login' => 'index/login',
        // URL:page/154 => controller:page method:blog $this->param[1]:154
        #'page/(\d+)' => 'page/blog',
    ),

    /** Установка времени жизник кук */
    'cookieLife' => 3600 * 24,


  /** Автозагружаемые файлы хелперов, файлы должны находится в
   * директории "Helpers" активного приложения*/
    'autoloadHelpers' => array(
        'helperFileSystem',
        'helperText'
    ),

    /** Подключение к БД */
    "dbConnects" => array(

        /* Настройки подключения к базе данных. через PDO SQLite */
        "db" => array(
            "driver" => "sqlite",
            "path" => "./DataBase/database.db",
        ),

		/* Настройки подключения к базе данных. через PDO Oracle 
        "dbOracle" => array(
            "driver" => "oci",
            "dbname" => "//dev.mysite.com:1521/orcl.mysite.com;charset=UTF8",
            "user"=>"user",
            "password"=>"password"
        ),*/
        
        /* Настройки подключения к базе данных. через PDO MySQL
        "dbMySql" => array(
		    "driver"    => "mysql",
		    "host"      => "localhost",
		    "dbname"    => "db",
		    "user"      => "root",
		    "password"  => "",
		),*/
    ),
	
);
