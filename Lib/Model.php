<?php
	
class Model
{

	/**
	 * Возможный обект PDO
	 * @var object $db
	 */
	public $db;
	
    public $dbConfConName = array();
    
    public $dbConfConSett = array();
    
	/**
	 * Имя таблицы БД. Возможность установить универсальный доступ к таблице.
	 * Константа должна быть установлена для работы некоторых методов этого класса
	 * @const TBL
	 */
    const TBL = '';

    /**
	 * Список полей таблицы. Возможность установить универсальный доступ к полям таблицы.
	 * Константа COL_ID должна быть установлена для работы некоторых методов этого класса
	 * @const
	 */
    const COL_ID = '';
    #const COL_OTHER = '';
    # ... other constants ...
    
    /**
	 * Хранит екземпляры моделей
	 * @var array $_models
	 */
    public static $_models;

	
	/**
	 *
	 */
    public function __construct()
    {
        $this->before();

        //if(isset($this->db))


        $this->dbConfConSett = ( isset(App::$config['dbConnects']) AND !empty(App::$config['dbConnects']) ) ? App::$config['dbConnects'] : null;

        if (!is_null($this->dbConfConSett)) {

            $ConnectKeyName = key($this->dbConfConSett);
            $this->dbConfConName[] = $ConnectKeyName;
            $dbConfigConnects = array_shift($this->dbConfConSett);

            if (class_exists('SimplePDO'))
                $this->$ConnectKeyName = new SimplePDO($dbConfigConnects);

        }

        $this->after();
    }

    private function __clone(){}

    /**
     * Метод инициализируется перед соединением с ДБ
     */
    public function before(){}

    /**
     * Метод инициализируется после соединением с ДБ
     */
    public function after(){}


    /**
     * Дополнительные соединения с БД
     * Имя дополнительного соединения должно быть одноименным с конфигурационным.
     *
     * @param   string  $dbNameConnect  Имя дополнительного соединения
     * @return  bool|SimplePDO
     */
    public function connect($dbNameConnect)
    {
        if (array_key_exists($dbNameConnect, $this->dbConfConSett) AND !in_array($dbNameConnect, $this->dbConfConName)) 
        {
            $this->dbConfConName[] = $dbNameConnect;
            $dbConfigConnects = array_shift($this->dbConfConSett);
            
            if (class_exists('SimplePDO')){
                return $this->$dbNameConnect = new SimplePDO($dbConfigConnects);
            }

        } else {
            return false;
        }
    }
    

    /**
     * Метод универсального доступу к моделя, метод переписываеться в создаваемых маделях обезательно
     *
     * @param   string  $className  Имя класса модели
     * @return  mixed
     */
    public static function model($className = __CLASS__)
    {
        if (isset(self::$_models[$className])) {
            return self::$_models[$className];
        } else {
            $model = self::$_models[$className] = new $className();
            return $model;
        }
    }

    /**
     * Подсчет количества записй в таблице
     *
     * @param string    $db     Екземпляр соединения
     * @param string    $tbl    таблица
     * @return mixed
     */
    public function сounter($db, $tbl = self::TBL)
	{
		#$tbl = (is_null($tbl) OR empty($tbl)) ? self::TBL : $tbl;
		$result = $db->query("SELECT COUNT(*) as counter FROM $tbl")->all();
        if(!isset($result[0]['counter']))
		    return $result[0]['counter'];
        else
            return false;
	}

    /**
     * Определение последнего ID в таблице
     *
     * @param string    $db     Екземпляр соединения
     * @param string    $tbl    таблица
     * @param string    $id     имя столбца для подсчета
     * @return mixed
     */
    public function lastId($db, $tbl, $id='id')
	{
		$result = $db->query("SELECT $id FROM $tbl ORDER BY $id DESC ")->all();
        if(isset($result[0][$id]))
            return $result[0][$id];
        else
            return 1;
	}

    /*Не реализовано еще*/
	public function createTable($db = 'db', $tbl){}
    public function createColumn($db = 'db', $tbl = null){}
    public function removeColumn($db = 'db', $tbl = null){}
    public function deleteTable($db = 'db', $tbl = null){}
	
} // END CLASS 'Model'