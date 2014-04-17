<?php


class ControllerIndex extends Ctrl
{

    public function actions()
    {
        return array(
            'one'=>'Components/actions/one',
            'two'=>'Components/actions/two',
        );
    }

    /**
	 * Обявление метода after() родительского класса Controller
	 * который выполняеться после инициализации всех компонентов
	 * Може использываться для переназначения неких параметров.
	 */
    public function before()
    {
        parent::before();

        # Code...

        // подключения чанков
        //$this->setChunk("menuSettings", "chunksMenus/menuSettings");
        //$this->setChunk("menuCategory", "chunksMenus/menuCategory");
        //$this->setChunk('menuSnippets','chunksMenus/menuSnippets');
        $this->setChunk("breadcrumbs", "chunks/breadcrumbs", array('title'=>'Snippets notes'));
    }

    public function after()
    {
        parent::after();
    }


    function actionLang()
    {
        // Переключение языка и редирект
        App::initLang($this->urlArgs(1),true);
        App::redirect($_SERVER["HTTP_REFERER"]);
    }

    /**
     * Обработчик страницы "Главная"
     */
    function actionIndex()
    {

        $content = '
        <h1>Критичные замечания по теме</h1>
        <p>Повседневная практика показывает, что начало повседневной работы по формированию
        позиции требует от нас анализа новейших вариантов поиска решений.</p>
        <p>Товарищи, начало повседневной работы по формированию позиции обеспечивает широкому
        кругу (специалистов) участие в формировании системы массового участия.</p>
        <p>Таким  образом рамки и место обучения кадров создает все необходимые предпосылки для
        утверждения и анализу необходимых данных для разрешения ситуации в целом.</p>
        <p>Повседневная практика показывает, что сложившаяся ситуация ни коим образом не
        позволяет выполнить важные задания по разработке новых предложений.</p>';

        // вывод в Views/main.php
	    $this->render('OUT', 'main', array(
                'content'=>$content
            ));
    }

    function actionTest()
    {
        echo "svdvs";
    }

    /**
     * Обработчик страницы "О скилетроне"
     */
    function actionInfo()
    {
        $content = $this->partial('index/info');

        // вывод в Views/main_one_column.php
	    $this->render('OUT', 'main_one_column', array(
                'content'=>$content
            ));
    }

    
    /**
     * Обработчик страницы "О системе"
     */
    function actionAbout()
    {
         $content = $this->partial('index/about');

        // вывод в Views/main.php
	    $this->render('OUT', 'main', array(
                'content'=>$content
            ));
    }



    /**
     * Обработчик страницы "Контакты"
     */
    function actionContacts()
    {
         $content = $this->partial('index/contacts');

        // вывод в Views/main.php
	    $this->render('OUT', 'main', array(
                'content'=>$content
            ));
    }

    /**
     * Страница вызов окна LOGIN
     */
    public function actionLogin()
    {
        // Формирование контента страницы
	    
        // подключения вида формы авторизации
	    $content = $this->partial("index/login", array(
            'login'=>(isset($_POST['login']))?$_POST['login']:'',
            'password'=>(isset($_POST['password']))?$_POST['password']:'',
        ));

        // Авторизация пользователя.
        // использует Classes: Auth::identity()
        // Принимает с формы $_POST['login'], $_POST['password']
        if(!empty($_POST['login']))
        {
            //$user = Users::model()->userAuth(htmlspecialchars(trim($_POST['login'])));
            $user = array(
                'id'=>'159',
                'login'=>'admin',
                'pass'=>'admin',
                'email'=>'admin@admin.com',
                'name'=>'Administrator',
            );

            if($user AND $user['pass'] == $_POST['password']){
                $publicUserInfo = array(
                    'login' => $user['login'],
                    'email' => $user['email'],
                    'name'  => $user['name']
                );

                Auth::identity($user['id'], $publicUserInfo);

                $content = '
                <div>
                	<h2>Воход в систему состоялся</h2>
                	<p>Вы вошли в систему как '.$user['name'].'.</p>
                	<p>Через 3 сек. страница будет автоматически перезагружена!</p>
                </div>
                ';

                $this->render('OUT', 'main', array('content'=>$content)); 

                App::redirect($_SERVER["HTTP_REFERER"], 3);
            }
        }

        // вывод, в основной шаблон
        $this->render('OUT', 'main', array('content'=>$content)); 

    }


    /**
     * Страница Вызов окна выхода
     */
    public function actionLogout()
    {
        // удаляение пользователя с сессии и редирект
        Auth::out();
        App::redirect(App::$url);
    }


    /** ******************************** */


    /**
     * Сканирование новых файлов
     */
    public function scanNewFiles()
    {
        $fArray = directoryToArray(APP.'DataFiles',false,'file');
        if(empty($fArray))
            return false;

        $fileSetting = array();
        $fileCount=0;
        foreach($fArray as $filePath){
            $file = file_get_contents($filePath);

            $pmResult = preg_match('/^<!--DECLARATION(.+)-->(.*)/sm',$file, $result);

            if($pmResult != true)
                continue;

            $fileSett = explode("\n",$result[1]);

            $fileSett = array_map(function($file){
                $file = str_replace(chr(13),'',$file);
                trim($file);
                return specFilter($file);
            }, $fileSett);

            $fileSett = array_values(array_diff($fileSett, array(""," ","\n")));

            foreach($fileSett as $f){
                $f = explode(':', $f);
                $f = array_map(function($n){return trim($n); }, $f);
                $fileSetting[$fileCount][$f[0]] = $f[1];
            }
            $fileSetting[$fileCount]['FULLPATH'] = $filePath;
            $fileCount++;
        }
        return $fileSetting;
    }




}