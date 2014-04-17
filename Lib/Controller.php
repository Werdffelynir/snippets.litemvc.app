<?php

class Controller
{
    /**
     * Активный файл основного шаблона входящего потока
     * @var string $layout
     */
    public $layout = null;


    /**
     * Активный файл вида
     * @var string $view
     */
    public $view = null;


    /**
     * Свойство передачи в вид или шаблон части вида
     * Работет совместно с методом setChunk()
     */
    public $chunk;


    /**
     * Контролер устанавлевает конфиг-настройки и
     * определяет языковый файл та инициилизирует его.
     */
    public function __construct()
    {
        $this->before();
        
        if($this->layout == null)
        	$this->layout = App::$config['layout'];
        	
        if($this->view == null)
        	$this->view = App::$config['view'];
/*
        if(App::getCookie('lang') != null){
            self::$langCode = App::getCookie('lang');
        }elseif(isset(App::$config['language']) AND self::$langCode == null){
            self::$langCode = App::$config['language'];
        }
*/

        $this->after();

        // Методы относящиеся к категории использующих настройки.
        //$this->initLang();
    }

	/**
	 * Выполняеться до загрузки основного вида и конфигурация в контролер
	 */
	public function before() {}

	/**
	 * Выполняеться после загрузки основного вида и конфигурация в контролер
	 */
	public function after() { }

	/**
	 * Упрощения для AJAX запросов
	 * <pre>
	 * // Пример:
	 * return array(
	 * 		'gate' => 'Components/itemGet',				// go to Components/itemGet.php
	 * 		'edit'=> 'Controllers/Post/UpdateAction',	// go to Controllers/Post/UpdateAction.php
	 * 	);
	 * </pre>
	 * @return array
	 */
	public function actions(){
        return array();
    }


    /**
     * Метод render() реализовывает подключение в основной шаблон theme видов с контролера
     *
     *<pre>
     * Пример:
     * // Одиночный вывод
     * $this->render('__namePosition__', '__fileView__', array('variable'=>'My Data'));
     *
     * // массовый вывод
     * 'blockName_*' - перемнная в основном шаблоне 'main.php' по умолчанию,
     * 'viewLeft'    - вид в калоге Views
     * 'keyLeft'     - переменная которая будет видна в виде 'viewLeft'
     *
     * $this->render(
     *      array(
     *          '__namePosition_1__' => array('__fileView_1__', array('variable'=>'My data')),
     *          '__namePosition_2__' => array('__fileView_2__', array('variable'=>'My data')),
     *          '__namePosition_3__' => array('__fileView_3__', array('variable'=>'My data')),
     *      )
     * );
     *
     * //В шаблоне позиции назначать следующим образом:
     *  <?php echo $this->blockName_Left;
     * </pre>
     *
     * @param array|string  $dataPos    если array многовыводный режим, строка одна позиция позиция
     * @param bool|string   $view       если одиночный вид
     * @param array         $dataArr    если одиночный данные в виде массива
     */
    public function render( $dataPos, $view=false, array $dataArr=array() )
    {
        if(is_array($dataPos))
        {
            foreach ($dataPos as $keyBlockName => $dataView) {
                if(isset($dataView[1]) AND is_array($dataView[1]))
                    extract($dataView[1]);
                $keyInclude = './Views/'.$dataView[0].".php";
                ob_start();
                include $keyInclude;
                $this->$keyBlockName = ob_get_clean();
            }
        }
        elseif(is_string($dataPos))
        {
            if(!$view) $view = $this->view;
            if(!empty($dataArr)) extract($dataArr);
            $keyInclude = './Views/'.$view.".php";
            ob_start();
            include $keyInclude;
            $this->$dataPos = ob_get_clean();
        }
        
        include './Views/layout/'.$this->layout.".php";

    }

	
    /**
     * Метод вывода в шаблон вид, совметсный с методом render()
     *
     *<pre>
     * Пример:
     * $this->renderTheme( __POSITION_NAME__ );
     *
     * // аналогично
     * echo $this->__POSITION_NAME__;
     *</pre>
     *
     * @param string $renderPosition названеи позиции указаной в контролере методом render()
     */
    public function renderTheme( $renderPosition )
    {
        if(isset($this->$renderPosition))
            echo $this->$renderPosition;
    }


    /**
     * Обработка в указном виде, переданных данных, результат возвращает.
     *
     *<pre>
     * Пример:
     * $content = $this->partial("blog/topSidebar", array( "var" => "value" ));
     *</pre>
     *
     * @param   string  $viewName   путь к виду, особености:
     *                              	"partial/myview" сгенерирует "app/Views/partial/myview.php"
     *                              	"//partial/myview" сгенерирует "app/partial/myview.php"
     * @param   array   $data       массив данных для передачи в вид
     * @param   bool    $e
     * @param   bool    $ext
     * @return null|string
     */
  public function partial( $viewName, array $data=null, $e=null, $ext=false )
    {

        $ext = (!$ext)?'.php':$ext;

        if(empty($viewName)) {
            return null;
        }elseif(substr($viewName, 0, 2) == '//'){
			$viewName = substr($viewName, 2);
	        //$toInclude = ROOT.App::$config['appPath'].DS.$viewName.'.php';
	        //$toInclude = $viewName.'.php';
	        $toInclude = $viewName.$ext;
        }else{
	        //$toInclude = ROOT.App::$config['appPath'].DS.'Views'.DS.$viewName.'.php';
	        //$toInclude = 'Views'.DS.$viewName.'.php';
	        $toInclude = 'Views'.DS.$viewName.$ext;
        }
        
        if($data != null)
        	extract($data);
        	
        if(!is_file($toInclude)){
            if(App::$debug){
                App::ExceptionError('ERROR! File not exists!', $toInclude);
            } else {
            	return null;
            }
        }
            
        ob_start();
        include $toInclude;
        $getContents = ob_get_contents();
        ob_clean();

		if($e)
        	echo $getContents;
		else
        	return $getContents;
    }

    /**
     * Метод работает как и partial(), но подключает файлы с директории Languages (по умолчанию),
     * и выберает файл взаимозависимости от активного языка на данный момент
     * Файлы должны иметь в конце имени приставку названия языка (textLogo_ru, textLogo_ua, textLogo_en)
     * параметр метода имя файла без приставки partialLang('textLogo');
     *
     * Достать перевод документ
     */
    public static function partialLang($file, array $data=null, $e=false)
    {
		if(substr($file, 0, 2) == '//'){
			$file = substr($file, 2);
	        //$toInclude = ROOT.App::$config['appPath'].DS.$file.'.php';
	        $toInclude = $file.'_'.App::$langCode.'.php';
        }else{
	        //$toInclude = App::$appPath.'Languages'.DS.$file.'_'.App::$langCode.'.php';
	        $toInclude = 'Languages'.DS.$file.'_'.App::$langCode.'.php';
        }

        if(!is_file($toInclude))
            if(App::$debug)
                App::ExceptionError('ERROR! File not exists!', $toInclude);
            else return null;

        ob_start();

        if($data != null)
        	extract($data);
        	
        include $toInclude;
        $getContents = ob_get_contents();
        ob_clean();

		if($e)
        	echo $getContents;
		else
        	return $getContents;
    }


    /**
     *
     * Обработка в указаном виде, переданых данных, результат будет передан в основной вид или тему по указаному $chunkName,
     * также есть возможность вернуть результат в переменную указав четвертый параметр в true.
     *
     *<pre>
     * Пример:
     * $this->setChunk("topSidebar", "blog/topSidebar", array( "var" => "value" ));
     *
     * в вид blog/topSidebar.php передается  переменная $var с значением  "value".
     *
     * В необходимом месте основного вида или темы нужно обявить чанк
     * напрямую:
     * echo $this->chunk["topSidebar"];
     * или методом:
     * $this->chunk("topSidebar");
     *</pre>
     *
     * @param string    $chunkName  зарегестрированое имя
     * @param string    $chunkView  путь у виду чанка, установки путей к виду имеют следующие особености:
     *                              	"partial/myview" сгенерирует "app/Views/partial/myview.php"
     *                              	"//partial/myview" сгенерирует "app/partial/myview.php"
     * @param array     $dataChunk  передача данных в вид чанка
     * @param bool      $returned   по умочнию FALSE производится подключения в шаблон, если этот параметр TRUE возвращает контент
     * @return string
     */
    public function setChunk( $chunkName, $chunkView='', array $dataChunk=null, $returned=false )
    {
        // Если $chunkView передан как пустая строка, создается заглушка
        if(empty($chunkView)) {
            return $this->chunk[$chunkName] = '';
        }elseif(substr($chunkView, 0, 2) == '//'){
			$chunkView = substr($chunkView, 2);
	        //$viewInclude = ROOT.App::$config['appPath'].DS.$chunkView.'.php';
	        $viewInclude = $chunkView.'.php';
        }else{
	        //$viewInclude = ROOT.App::$config['appPath'].DS.'Views'.DS.$chunkView.'.php';
	        $viewInclude = 'Views'.DS.$chunkView.'.php';
        }
        
        // Если вид чанка не существует отображается ошибка
        if(!file_exists($viewInclude)){
            if(App::$debug) {
                App::ExceptionError('ERROR! File not exists!', $viewInclude);
            } else {
            	return null;
            }
        }

        ob_start();

        if(!empty($dataChunk)) 
        	extract($dataChunk);

        include $viewInclude;
        
        if(!$returned)
            $this->chunk[$chunkName] = ob_get_clean();
        else
            return ob_get_clean();
    }


    public function setChunkLang( $chunkName, $chunkView='', array $dataChunk=null, $returned=false )
    {
      // Если $chunkView передан как пустая строка, создается заглушка
      if(empty($chunkView)) {
        return $this->chunk[$chunkName] = '';
      }elseif(substr($chunkView, 0, 2) == '//'){
        $chunkView = substr($chunkView, 2);
        //$viewInclude = ROOT.App::$config['appPath'].DS.$chunkView.'.php';
        $viewInclude = $chunkView.'_'.App::$langCode.'.php';
      }else{
        //$viewInclude = ROOT.App::$config['appPath'].DS.'Views'.DS.$chunkView.'.php';
        $viewInclude = 'Languages'.DS.$chunkName.'_'.App::$langCode.'.php';
      }

      // Если вид чанка не существует отображается ошибка
      if(!file_exists($viewInclude)){
        if(App::$debug) {
          App::ExceptionError('ERROR! File not exists!', $viewInclude);
        } else {
          return null;
        }
      }

      ob_start();

      if(!empty($dataChunk))
        extract($dataChunk);

      include $viewInclude;

      if(!$returned)
        $this->chunk[$chunkName] = ob_get_clean();
      else
        return ob_get_clean();
    }


    /**
     * Вызов зарегистрированного чанка. Первый аргумент имя зарегестрированого чанка
     * второй тип возврата метода по умолчанию ECHO, если FALSE данные будет возвращены
     *
     * <pre>
     * Пример:
     *  $this->chunk("myChunk");
     * </pre>
     *
     * @param  string    $chunkName
     * @param  bool      $e
     * @return bool
     */
    public function chunk( $chunkName, $e=true )
    {
        if(isset($this->chunk[$chunkName])){
            if($e)
                echo $this->chunk[$chunkName];
            else
                return $this->chunk[$chunkName];
        }else{
            if(App::$debug)
                App::ExceptionError('ERROR Undefined chunk',$chunkName);
            else
                return null;
        }
    }


    /**
     * Метод для Вывода ошибки 404
     *
     * @param string    $file
     * @param array     $textData
     * @param bool      $e
     * @return string
     */
    public static function error404($file='layout/error404', array $textData=null, $e=true)
    {
	    $c = new self();
	    if($e)
	    	echo $c->partial($file, $textData);
	    else
        	return $c->partial($file, $textData);
    }


    /**
     * Метод для проверки являеться ли запрос AJAX
     * @return bool
     */
    public function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Метод возвращает параметры переданные через строку запроса.
    основное предназначение это передача неких параметров, но все же
    можно найти множество других применений для этого метода.
     *
     * <pre>
     * Например: http://site.loc/edit/page/id/215/article/sun-light
     * /edit/page/                  - это контролер и екшен, они пропускаются
        $this->urlArgs()            - id возвращает первый аргумент
        $this->urlArgs(1)           - id аналогично, но '1' != 1
        $this->urlArgs(3)           - возвращает третий аргумент article
        $this->urlArgs('id')        - возвращает следующий после указного вернет 215
        $this->urlArgs('article')   - sun-light
        $this->urlArgs('getArray')  - массив всех элементов "Array ( [1] => edit [2] => page [3] => id [4] => 215 [5]..."
        $this->urlArgs('getString') - строку всех элементов "edit/page/id/215/article/sun-light"
        $this->urlArgs('getController') - строку всех элементов "edit/page/id/215/article/sun-light"
        $this->urlArgs('getMethod') - строку всех элементов "edit/page/id/215/article/sun-light"
        $this->urlArgs('edit', 3)   - 215 (3 шага от 'edit')
     * </pre>
     * 
     * @param bool $param
     * @param int $element
     * @return array|string
     */
    public static function urlArgs($param=false, $element=1)
    {
        if(empty(App::$args)) return null;

		// отдает первый елемент
        if(!$param) {
            return App::$args[0];
            
        // отдает по номеру елемент
        }elseif( is_int($param) ){
	        $pNum = $param - 1;
            return (isset(App::$args[$pNum])) ? App::$args[$pNum] : null; 

        // отдает все елементы в массиве
        }elseif($param == 'getArray'){
            return App::$args;

        // отдает все елементы в строке
        }elseif($param == 'getString'){
            return App::$requestLine;

        }elseif($param == 'getController'){
            return App::$controller;

        }elseif($param == 'getMethod'){
            return App::$method;

        // отдает елемент следующий после указаного 
        }else{
	        if(in_array($param, App::$args)){
	            if($element > 0){
		            $keyElement = array_search($param, App::$args);
		            $key = $keyElement+$element;
	                return (isset(App::$args[$key]))?App::$args[$key]:null;
	            } else {
	                return $param;
	            }
		    }else{
			    return null;
		    }
        }
    }



	/**
	 * Метод для подключение моделей, параметром берет созданный раньше класс Модели
    Возвращает объект модели с ресурсом подключенным к базе данных
	 *
	 * @param   string          $modelName      Имя класса модели
	 * @return  bool|object
	 */
	public function model($modelName)
    {
        if (class_exists($modelName)) {
            //$model = new $modelName();
            //return (object) $model;
            $modelName = ucfirst($modelName);
            return $modelName::model();
        }else{
            if(App::$debug)
                App::ExceptionError('ERROR model class not exists!',$modelName);
        }
    }


    /**
     * Алис класса App{} initLang()
     * Инициализация языка.
     */
    public function initLang($langCode=false, $cookie=true)
	{
        App::initLang($langCode, $cookie);
	}

	/**
   * Алис класса App{} lang()
   * Доступк к переводу и атрибутам языка.
   */
	public function lang($key, $e=false)
    {
        return App::lang($key, $e);
	}

  /**
   * Алис класса App{} helper()
   * Подключение дополнительных файлов хелперов, что небыли указаны в
   * конфигурации приложения автозагрузки
   *
   * @param   string  $file   название файла в каталоге Helpers без расширения
   * @return  bool            false|true или при debug страницу ошибки
   */
  public function helper($file)
  {
    return App::helper($file);
  }




} // END CLASS 'Controller'