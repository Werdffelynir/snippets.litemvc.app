<?php


/**
 * Class App
 */
class App
{
    /**
     * содержит массив установленых параметров преложения
     * @var array $config
     */
    public static $config;

    /**
     * включения дебага
     * @var bool
     */
    public static $debug = true;

    /**
     * дебагер, отображает результат реРоута
     * @var array $regOut
     */
    public static $regOut = array();

    /**
     * содержит название атуального контролера
     * @var string $controller
     */
    public static $controller;

    /**
     * содержит название атуального метода
     * @var string $method
     */
    public static $method;

    /**
     * содержит часть строки запроса после $controller/$method,
     * Что опредены в системе как аргументы
     * @var array $args
     */
    public static $args = array();

    /**
     * собержит то же что и $args но в виде строки
     * @var string $requestLine
     */
    public static $requestLine;

    /**
     * url адреса предлжения
     * @var string $urlNude пример: site.com
     * @var string $url пример: http://site.com
     * @var string $urlTheme пример: http://site.com/app/views/theme
     */
    public static $urlNude;
    public static $url;
    public static $urlTheme;

    /**
     * @var string $appDir Абсолютный путь к приложению
     */
    public static $appPath;

    /**
     * Массив содержит зарегестрированые хуки
     * @var array
     */
    public static $_hookBind = array();

    /**
     * Массив содержит зарегестрированые фильтры
     * @var array
     */
    private static $_filterBind = array();

    /**
     * Массив содержит зарегестрированые флеш сообщения
     * @var array
     */
    private static $_flashStorage = array();

    /**
     * Срок хранения кук
     * @var array
     */
    protected static $expireCookTime;

    /**
     * Куки, кодировать значения
     * @var array
     */
    protected static $decodeCook = true;

    /**
     * @var string $langCode Установка языка 'ru', 'en' например
     * @var array $langData Днные перевода
     * @var bool $identifyLan Определять язык клиента по умолчанию
     */
    public static $langCode = null;
    private static $langData = null;
    protected static $identifyLan = true;

    /**
     * @var $_  \\системыне свойства, отработка запросов
     */
    private $_request;
    private $_requestLine;
    private $_controller;
    private $_method;
    private $_args = array();
    public static $_helpers = array();

    public function __construct()
    {
        $this->autoloadClasses();
        $this->findUrl();
    }

    /**
     * Загрузка конфигурационных параметров приложения
     * @param $config
     */
    public function getConfig(array $config)
    {
        self::$config = (!empty($config)) ? $config : die('CONFIG DATA NOT FOUND!');
    }

    /**
     * Инициализация системы
     */
    public function run()
    {

        $parts = explode('/', $_SERVER['REQUEST_URI']); // '/index/test' || 'testFolder/mvclite/app/index/test'

        $appPathArray = explode(DS, APP);
        array_pop( $appPathArray );
        $appPath = end( $appPathArray );

        $appPathCount = substr_count($_SERVER['REQUEST_URI'], $appPath);
        self::$config['appDir'] = $appPath;

        //var_dump( $appPathCount );

        if($appPathCount==0){
            $parts = array_values(array_diff($parts,array("")));
        }else{
            foreach ($parts as $k => $v) {
                if ($v != $appPath) {
                    unset($parts[$k]);
                } else {
                    unset($parts[$k]);
                    break;
                }
            }
        }

        //var_dump($parts);

        // Установка определения языка
        if (isset(self::$config['identifyLanguage']))
            self::$identifyLan = self::$config['identifyLanguage'];

        // Установка параметра debug
        if (isset(self::$config['debug']))
            self::$debug = self::$config['debug'];

        // Установка файлов автоподгрузки helpers
        if (isset(self::$config['autoloadHelpers']))
            self::$_helpers = self::$config['autoloadHelpers'];

        // Установка времини жизник кук
        if (isset(self::$config['cookieLife']))
            self::$expireCookTime = self::$config['cookieLife'];
        else
            self::$expireCookTime = 600;

        // Определение частей запроса
        $this->_request = (is_array($parts)) ? $parts : false;
        $this->_requestLine = join('/', $parts);
        $this->_controller = ($c = array_shift($parts)) ? $c : 'index';
        $this->_method = ($m = array_shift($parts)) ? $m : 'index';
        $this->_args = (isset($parts)) ? $parts : array();

        // если указаны реРоутинги, происходит отловка их
        if (!empty(self::$config['reRouter'])) {
            foreach (self::$config['reRouter'] as $regex => $class) {
                $regex = str_replace('/', '\/', $regex);
                $regex = '^' . $regex . '\/?$';
                self::$regOut = array(
                    'regex' => $regex,
                    'request' => $this->_requestLine,
                );

                if (preg_match("|$regex|i", $this->_requestLine, $matches)) {
                    if (strpos($class, '/'))
                        $parts = explode('/', $class);
                    $this->_controller = ($c = array_shift($parts)) ? $c : 'index';
                    $this->_method = ($m = array_shift($parts)) ? $m : 'index';
                    $this->_args = (array_shift($matches)) ? $matches : array();

                    self::$regOut["result"] = $matches;
                }
            }
        }

        self::$controller = $this->_controller;
        self::$method = $this->_method;
        self::$args = $this->_args;
        self::$requestLine = $this->_requestLine;

        //var_dump(self::$controller.' / '.self::$method);

        // lang install settings
        /**/
        if (self::$langCode == null) {

            if (App::getCookie('lang') != null) {
                #echo 'getCookie';
                self::$langCode = App::getCookie('lang');

            } elseif (self::$identifyLan) {
                #echo '$identifyLan';
                self::$langCode = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));

            } elseif (isset(App::$config['language'])) {
                #echo 'language';
                self::$langCode = strtolower(App::$config['language']);

            } else {
                #echo 'def';
                self::$langCode = 'ru';
            }

        }


        $this->runController();
    }

    /**
     * Запуск запрашиемого контролера
     */
    private function runController()
    {
        // Авто загрузка файлов хелперов установленных в конфигурации
        self::autoloadHelpers();

        // определение названия класса контролера
        $controller = 'Controller' . ucfirst(self::$controller);

        // определение названия метода контролера
        $method = 'action' . ucfirst(self::$method);

        // определение пути к классу контролера
        $cPath = APP . 'Controllers' . DS . $controller . '.php';

        if (file_exists($cPath)) {
            include $cPath;
            $controllerObj = new $controller();

            // Назначения возможных actions с контролера
            $actions = $controllerObj->actions();

            if (method_exists((object)$controllerObj, $method)) {
                if (empty(self::$args)) {
                    $controllerObj->$method();
                } else {
                    call_user_func_array(array($controllerObj, $method), self::$args);
                }

                // Проверка на существование actions
            } elseif (!empty($actions) AND array_key_exists(self::$method, $actions)) {
                // если actions в контролере определен и файл вызова существует вызываем его
                $cationPath = APP . $actions[self::$method] . '.php';
                if (file_exists($cationPath))
                    require_once($cationPath);
                else
                    $controllerObj::error404('layout/error404', array('title' => 'Error 404. Page not found!', 'text' => 'Actions request <b>[' . self::$method . ']</b> file <b> ' . $cationPath . '()</b> not exist!'));

            } else {

                // вывод страницы 404. Метод не найден
                $controllerObj::error404('layout/error404', array('title' => 'Error 404. Page not found!', 'text' => 'Method <b>function ' . $method . '()</b> not exist!'));
            }

        } else {
            // вывод страницы 404. Файла контролера нет.
            Controller::error404('layout/error404', array('title' => 'Error 404. Page not found!', 'text' => 'Controller file <b>Controllers/' . $cPath . '</b> not exist!'));
        }
    }

    /**
     * Авто загрузка классов с директорий системы и приложения.
     */
    private function autoloadClasses()
    {
        spl_autoload_register(array($this, 'systemsClasses'));
        spl_autoload_register(array($this, 'classesClasses'));
        spl_autoload_register(array($this, 'modelsClasses'));

    }

    /**
     * Регистрация автозагрузки классов системы
     *
     * @param $className
     * @return bool
     */
    private function systemsClasses($className)
    {
        // Класс App не нужно загружать
        if ($className == 'App')
            return false;

        set_include_path(SYSTEM);
        spl_autoload_extensions(".php");
        spl_autoload($className);
    }


    /**
     * Автозагрузка файлов хелперов
     */
    private static function autoloadHelpers()
    {
        if (!empty(self::$_helpers)) {

            $autoload = self::$_helpers;
            $path = APP . 'Helpers' . DS;

            foreach ($autoload as $file)
                if (file_exists($path . $file . '.php'))
                    include $path . $file . '.php';
        }
    }

    /**
     * Регистрация автозагрузки классов приложения
     *
     * @param $className
     */
    private function classesClasses($className)
    {
        set_include_path(APP.'Classes');
        spl_autoload_extensions(".php");
        spl_autoload($className);
    }


    /**
     * Авто загрузка моделей приложения.
     */
    private function modelsClasses($className)
    {
        set_include_path(APP . 'Models');
        spl_autoload_extensions(".php");
        spl_autoload($className);
    }


    /**
     * Инициализация языквого файла, установка языковых параметров к cookies, переназначение языка
     * @param bool $langCode
     * @param bool $cookie
     */
    public static function initLang($langCode = false, $cookie = false)
    {

        if ($langCode) {
            // Обновление параметра
            self::$langCode = $langCode;
        } else {
            $langCode = self::$langCode;
        }

        // Пишем в кукисы, если ее не существует
        if ($cookie OR App::getCookie('lang') == null)
            App::setCookie('lang', $langCode);

        //$file = App::$appPath.'Languages'.DS.'base'.DS.$langCode.'.php';
        $file = APP . 'Languages' . DS . $langCode . '.php';

        if (file_exists($file))
            $langData = include $file;
        else
            if (self::$debug)
                App::ExceptionError('Headers sent. Redirect impossible! ');

        self::$langData = array(
            'name' => $langData['name'],
            'code' => $langData['code'],
            'image' => $langData['image'],
            'words' => $langData['words']
        );

    }


    /**
     * Достать перевод, или параметр
     */
    public static function lang($key, $e = false)
    {
        if ($key == 'name')
            return self::$langData['name'];
        if ($key == 'code')
            return self::$langData['code'];
        if ($key == 'image')
            return self::$langData['image'];

        if ($e) echo (isset(self::$langData['words'][$key])) ? self::$langData['words'][$key] : null;
        else return (isset(self::$langData['words'][$key])) ? self::$langData['words'][$key] : null;
    }


    /**
     * Определение действительных URl аресов
     * Метод системный.
     */
    private function findUrl()
    {
        $httpHost = $_SERVER['HTTP_HOST'];

        $scrNamArr = explode("/", trim($_SERVER['SCRIPT_NAME']));
        array_pop($scrNamArr);

        // Удаление пустых ячеек массива
        $scrNamArr = array_filter($scrNamArr, function ($el) {
                return !empty($el);
            }
        );

        $pathFolder = join('/', $scrNamArr);

        if (empty($pathFolder)) {
            $httpHostFull = $httpHost;
        } else {
            $httpHostFull = $httpHost . "/" . $pathFolder;
        }

        self::$urlNude = $pathFolder;
        self::$url = "http://" . $httpHostFull;
        self::$urlTheme = "http://" . $httpHostFull . "/" . self::$config["appDir"] . "Views/layout";

        //var_dump( self::$urlNude, self::$url, self::$urlTheme);


    }


    /**
     *
     * @param string $url Переадресация на URL
     * @param int $delay Редирек с задержкой с секунднах
     * @param int $code HTTP код заголовка; по умолчанию 302
     * @return bool
     */
    public static function redirect($url, $delay = 0, $code = 302)
    {
        if (!headers_sent()) {
            if ($delay)
                header('Refresh: ' . $delay . '; url=' . $url, true);
            else
                header('Location: ' . $url, true, $code);
            exit();
        } else {
            if (self::$debug)
                App::ExceptionError('Headers sent. Redirect impossible! ');
        }
    }


    /**
     * Пренудительный редирект, обходит отправленые заголовки
     *
     * @param string $url Переадресация на URL
     */
    public static function redirectForce($url)
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
        } else {
            echo "<html><head><title>REDIRECT</title></head><body>";
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0; url=' . $url . '" />';
            echo '</noscript>';
            echo "</body></html>";
        }
        echo "<!--Headers already!\n-->";
        echo "</body></html>";
        exit;
    }


    /**
     * Регистрация или вызов обработчиков событий, хука, в областе видемости. Первый аргумент
     * имя хука, второй анонимная функция или название метода в контролере, трений
     * задает аргументы для назначиного обработчика в втором аргументе. Если указан только первый
     * аргумент возвращает екземпляр этого хука, если имя не зарегестрировано возвращает NULL.
     *
     * <pre>
     * Пример:
     *  $this->hookRegister('hook-00', function(){ echo '$this->hookRegister'; });
     *  //showEvent - функция или класс с мтодом array('className','method')
     *  $this->hookRegister('hook-01', 'showEvent');
     *  $this->hookRegister('hook-02', 'showName', array('param1'));
     *  $this->hookRegister('hook-03', 'showNameTwo', array('param1','param2'));
     * </pre>
     *
     * @param string $event Название евента
     * @param null $callback Обработчик события обратного вызова
     * @param array $params Передаваемые параметры
     * @return array
     */
    public static function hookRegister($event, $callback = null, array $params = array())
    {
        if (func_num_args() > 2) {
            self::$_hookBind[$event] = array($callback, $params);
        } elseif (func_num_args() > 1) {
            self::$_hookBind[$event] = array($callback);
        } else {
            return self::$_hookBind;
        }
    }

    /**
     * Тригер для зарегестрированого евента. Первый аргумент зарегестрированый ранее хук
     * методом hookRegister(), второй параметры для зарегестрированого обработчика.
     * Возвращает исключение в случае если хук не зарегестрирован
     *
     * <pre>
     * Пример:
     *  $this->hookTrigger('hook-01');
     *  $this->hookTrigger('hook-02', array('param1'));
     *  $this->hookTrigger('hook-03', array('param1','param2'));
     * </pre>
     *
     * @param string $event
     * @param array $params
     * @throws RuntimeException
     */
    public static function hookTrigger($event, array $params = array())
    {

        if (isset(self::$_hookBind[$event]) AND $handlers = self::$_hookBind[$event]) {

            $handlersParam = (isset($handlers[1])) ? $handlers[1] : false;
            $handlersParam = (!empty($params)) ? $params : $handlersParam;

            if (is_callable($handlers[0])) {
                call_user_func($handlers[0]);
            } elseif (method_exists(__CLASS__, $handlers[0]) AND $handlersParam) {
                call_user_func_array(array(__CLASS__, $handlers[0]), $handlersParam);
            } elseif (method_exists(__CLASS__, $handlers[0])) {
                call_user_func(array(__CLASS__, $handlers[0]));
            } else {
                if (self::$debug)
                    App::ExceptionError('Invalid callable');
            }
        }
    }


    /**
     * Регистрация фильтра.
     *
     * @param string $filterName Имя фильтра
     * @param callable $callable солбек, функция или класс-метод
     * @param int $acceptedArgs количество принимаюих аргументов
     */
    public static function filterRegister($filterName, $callable, $acceptedArgs = 1)
    {
        if (is_callable($callable)) {
            self::$_filterBind[$filterName]['callable'] = $callable;
            self::$_filterBind[$filterName]['args'] = $acceptedArgs;
        }
    }

    /**
     * Тригер для зарегестрированого фильтра.
     *
     * @param string $filterName Имя фильтра
     * @param string|array $args входящие аргументы
     * @throws Exception
     */
    public static function filterTrigger($filterName, $args)
    {
        if (isset(self::$_filterBind[$filterName])) {
            if (is_string($args)) {
                call_user_func(array(__CLASS__, self::$_filterBind[$filterName]['callable']), $args);
            } elseif (is_array($args) AND self::$_filterBind[$filterName]['args'] == sizeof($args)) {
                call_user_func_array(array(__CLASS__, self::$_filterBind[$filterName]['callable']), $args);
            }
        } else {
            if (self::$debug)
                App::ExceptionError('Invalid callable or invalid num arguments');
        }
    }


    /**
     * Отображает полный URL преложения. метод упрощения
     *
     * <pre>
     * Пример:
     *  App::url();          // возвратит URL приложения, корень
     *  App::url('current'); // URL страницы на которой вызывается
     *  App::url('full');    // URL полный учитывая контроллер и метод
     *  App::url('theme');   // URL layout
     * </pre>
     *
     * @param string $type Тип адреса '', 'current', 'full', 'theme'
     * @param bool $e если FALSE возвратит строку
     * @return string
     */
    public static function url($type = '', $e = true)
    {
        if (empty($type)) {
            if ($e) echo App::$url;
            else return App::$url;
        } elseif ($type == 'current') {
            if ($e) echo App::$url . '/' . App::$controller . '/' . App::$method;
            else return App::$url . '/' . App::$controller . '/' . App::$method;
        } elseif ($type == 'full') {
            if ($e) echo App::$url . '/' . App::$requestLine;
            else return App::$url . '/' . App::$requestLine;
        } elseif ($type == 'theme') {
            if ($e) echo App::$urlTheme;
            else return App::$urlTheme;
        }
    }


    /**
     * Локальный include фалой в каталогк
     *
     * @param string $file Путь к файлоу от каталога app/__file__
     * @param array $data массив данных для распаковки в файле
     * @return bool
     */
    public static function includeFile($file, array $data = null)
    {
        if (file_exists($fileName = APP. $file . '.php')) {

            if ($data != null)
                extract($data);

            include $fileName;

        } else {
            if (self::$debug)
                App::ExceptionError('File not exists', $fileName);
        }
    }


    /**
     * Метод вызова ошибки исключения
     *
     * @param string $errorMsg Сообщения о ошибке
     * @param null $fileName Конкретные данные, например имя файла
     * @param bool $die
     */
    public static function ExceptionError($errorMsg = 'File not exists', $fileName = null, $die = true)
    {
        try {
            throw new Exception("TRUE.");
        } catch (Exception $e) {
            echo "
<div style='padding: 10px; font-family: Verdana; font-size: 11px; color:#FFF; background: #0033FF'>

    <h2 style='font-size: 14px; color:#FF9900;'>Warning! throw Exception. </h2>

    <h2>Message: " . $errorMsg . " </h2>";

            if ($fileName != null):
                echo "<code style='display: block; padding: 10px; font-size: 12px; font-weight: bold; font-family: Consolas, Courier New, monospace; color:#CBFEFF; background: #000066'>"
                    . $fileName .
                    "</code>";
            endif;

            echo "<div style='display: block; padding: 10px; color:#828282; '>
        Function setup: " . $e->getFile() . "
        <br>
        Line: " . $e->getLine() . "
    </div>

    <h3>Trace As String: </h3>
    <code style='display: block; padding: 10px; font-size: 12px; font-weight: bold; font-family: Consolas, Courier New, monospace; color:#CBFEFF; background: #000066'>
        " . str_replace('#', '<br>#', $e->getTraceAsString()) . "<br>
    </code>

    <h3>Code: </h3>
    <code style='display: block; padding: 10px; font-size: 12px; font-weight: bold; font-family: Consolas, Courier New, monospace; color:#CBFEFF;  background: #000066'>
        " . $e->getCode() . "
    </code>

</div>";
            if ($die)
                die();
        }
    }


    /**
     * Сохранение данных в куках браузера, по упрощенной и надежной схеме.
     * Хранение кук происходит в шифрованом виде base64, парамент шифрования
     * настраевается в конфигурационном файле приложения, по умолчанию включено.
     *
     * @param string $key Имя
     * @param string $value Значение
     * @param null $expire Время хранения
     * @param null $path Путь
     * @param null $domain Домен
     * @return bool
     */
    public static function setCookie($key, $value, $expire = null, $path = null, $domain = null)
    {
        if ($expire == null) {
            $expire = time() + self::$expireCookTime;
        }

        if ($domain == null) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        if ($path == null) {
            $path = '/' . App::$urlNude . '/';
        }
        if (self::$decodeCook)
            $value = base64_encode($value);
        return setcookie($key, $value, $expire, $path, $domain);
    }

    public static function updateCookie($key, $value, $expire = null, $path = null, $domain = null)
    {
        return self::setCookie($key, $value, $expire, $domain, $path);
    }

    public static function getCookie($key)
    {
        if (isset($_COOKIE[$key])) {
            if (self::$decodeCook)
                return base64_decode($_COOKIE[$key]);
            else
                return $_COOKIE[$key];
        } else {
            return null;
        }
    }

    public static function deleteCookie($key, $domain = null, $path = null)
    {

        if ($domain === null) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        if ($path === null) {
            $path = '/' . App::$urlNude . '/';
        }

        return setcookie($key, false, time() - 3600, $path, $domain);
    }


    /**
     * Выводит или регистрирует флеш сообщения для даной страницы или следующей переадрисации.
     * Указать два аргумента для регистрации сообщения, один для вывода. Если указать претий аргумент
     * в FALSE, сообщение будет удалено поле первого вывода.
     *
     * <pre>
     * Регистрация сообщения:
     * App::flash('edit','Запись в базе данных успешно обновлена!');
     * Вывод после переадрисации:
     * App::flash('edit');
     * </pre>
     *
     * @param string $key Ключ флеш сообщения
     * @param mixed $value Значение
     * @param bool $keep Продлить существования сообщения до следущего реквкста; по умолчанию TRUE
     *
     * @return mixed
     */
    public static function flash($key = null, $value = null, $keep = true)
    {
        if (!isset($_SESSION)) session_start();
        $flash = '_flash';

        if (func_num_args() > 1) {
            $old = isset($_SESSION[$flash][$key]) ? $_SESSION[$flash][$key] : null;
            if (isset($value)) {
                $_SESSION[$flash][$key] = $value;
                if ($keep) {
                    self::$_flashStorage[$key] = $value;
                } else {
                    unset(self::$_flashStorage[$key]);
                }
            } else {
                unset(self::$_flashStorage[$key]);
                unset($_SESSION[$flash][$key]);
            }
            return $old;
        } elseif (func_num_args()) {
            $flashMessage = isset($_SESSION[$flash][$key]) ? $_SESSION[$flash][$key] : null;
            unset(self::$_flashStorage[$key]);
            unset($_SESSION[$flash][$key]);
            return $flashMessage;
        } else {
            return self::$_flashStorage;
        }
    }


    /**
     * Подгрузка файлов хелперов, загружает файлы с каталога "Helpers"
     * активного приложения, определяя ранее включенные файлы.
     * <pre>
     * App::helper('String');
     * </pre>
     *
     * @param string $file файл хелпер
     * @return bool
     */
    public static function helper($file)
    {
        if (in_array($file, self::$_helpers)) {
            return true;
        } else {

            $toInclude = './Helpers' . DS . $file . '.php';

            if (file_exists($toInclude)) {
                self::$_helpers[] = $file;
                include $toInclude;
                return true;
            } else {

                if (App::$debug) {
                    App::ExceptionError('ERROR! File not exists!', $toInclude);
                } else {
                    return false;
                }
            }
        }

    }


} // END CLASS 'App'