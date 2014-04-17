<?php
/**
 * Class Auth осуществляет авторизацию пользователя
 * по информации что попадает через аргумнты метода identity()
 * сохраняет в глобальные статические свойства $id и $user
 * информацию о пользователе что может быть проверена с
 * любой части приложения;
 *
 * Инициализация класса должна происходить из под глобальной области
 * например общего контролера, файла functions.php или другого, Обычно это
 * файл Classes/Ctrl.php общий контролер демо приложений
 */

class Auth {

    /** @var  Сохраняет id пользователя */
    public static $id = false;

    /** @var array Сохраняет расширеную информацию о пользователе */
    public static $user = false;

    /**
     * Метод запуска проверки авторизации пользователя
     * Должен быть обявлен в коде глобальной области
     * например файл Classes/Ctrl.php общий контролер
     */
    public static function run()
    {
        if (isset($_COOKIE['id']))
            self::$id = App::getCookie('id');
        if (isset($_COOKIE['user']))
            self::$user = unserialize(App::getCookie('user'));
        return true;
    }

    /**
     * Метод сохранения информации пользователя, уже провереного и
     * переданых данных как аргументы даного метода
     *
     * @param number    $userId     id пользователя
     * @param array     $userData   массив другой информации, необходимой разработчику
     */
    public static function identity($userId, array $userData=array())
    {
        self::$id = $userId;
        App::setCookie('id', $userId );

        if(!empty($userData)){
            self::$user = $userData;
            App::setCookie('user', serialize($userData) );
        }
    }

    /**
     * Выход пользователя способом удаления куков
     */
    public static function out()
    {
        if (isset($_COOKIE['id'])){
            self::$id = false;
            App::deleteCookie('id');
        }

        if (isset($_COOKIE['user'])){
            self::$user = false;
            App::deleteCookie('user');
        }
    }

} 