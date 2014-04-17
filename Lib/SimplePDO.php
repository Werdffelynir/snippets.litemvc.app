<?php
/*
$config Массив должен иметь:
#Настройки подключения к базе данных. через PDO sqlite
"dbTwo" => array(
    "driver"  => "sqlite",
    "path"    => dirname(__DIR__)."/app/DataBase/myDataBase.sqlite",
),
#Настройки подключения к базе данных. через PDO MySQL
"dbMySql" => array(
    "driver"    => "mysql",
    "host"      => "localhost",
    "dbname"    => "myDataBase",
    "user"      => "root",
    "password"  => "",
),
"dbMySql" => array(
    "driver"    => "oci",
    "dbh"      => "...",
    "user"      => "user",
    "password"  => "password",
),
*/
class SimplePDO
{
	/**
	 * Обект соединения
	 * @var PDO $dbh
	 */
	public $dbh;

	/**
	 * Держит последний рабочий запрос
	 * @var $sth
	 */
	public $sth;

    /**
     * Держит последний запрос ввиде строки. для отладки
     * @var $sth
     */
    public static $sql;

    /**
     * @var string
     */
    private $port;

    /**
     * @var array
     */
    private $config;

	/**
	 * Класспринимет конфиг приложения, создает создет екземпляры PDO
	 * @param array $config
	 */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->init();
    }
	public function init(array $config=null)
    {
        if($config==null)
            $config = $this->config;

        $this->port = (isset($config['port'])) ? "port=" . $config['port'] . ";" : "";
        if (!empty($config['driver'])) {
	        
            try {
                if ($config['driver'] == "sqlite") {
                    $this->dbh = new PDO("sqlite:".$config['path']);
                } elseif ($config['driver'] == "mysql") {
                    $this->dbh = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . '', $config['user'], $config['password']);
				} elseif ($config['driver'] == "oci") {
                    $this->dbh = new PDO('oci:dbname='. $config['dbname'] . '', $config['user'], $config['password']);
                } else {
                    $this->dbh = new PDO('' . $config['driver'] . ':host=' . $config['host'] . $this->port . ';dbname=' . $config['dbname'] . '', $config['user'], $config['password']);
                }
                
            } 
            catch (PDOException $e) 
            {
                echo $e->getMessage();
            }

        }

    }


    /**
     * Классический не безопасный метод выполнения запросов
     *
     * @param $sql
     * @return mixed Колчество затронутых строк

    public function exec($sql)
    {
        if ($this->dbh == null)
            App::ExceptionError("Connection with DataBase closed!");

        self::$sql = $sql;

        try {
            return $this->dbh->exec($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    } */
    public function exec($sql)
    {
        if ($this->dbh == null)
            die("Connection with DataBase closed!");

        self::$sql = $sql;

        try {
            $count = $this->dbh->exec($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        if(!$count)
            App::ExceptionError('Error SQL string!', 'Check your query string, error can be names :<br><span style="color:red">'.$sql.'</span>');

        return $count;
    }



    /**
     * Базовый метод запросов к базе данных.
     * Использует стандартный метод execute() через обертку, принимает sql запрос,
     * или если указан второй параметр происходит выполнение через метод prepare()
     * возвращает екземпляр обекта
     *
     * Запросы осуществляються:
     * <pre>
     * ->query( "INSERT INTO blog (title, article, date) values (:title, :article, :date)",
     *      array(
     *          'title' => $title,
     *          'article' => $article,
     *          'date' => time()
     *          )
     *      )
     *
     * ->query( "INSERT INTO blog (title, article, date) values (?, ?, ?)",
     *      array(
     *          $title,
     *          $article,
     *          $date
     *          )
     *      )
     * ->query( "SELECT title, article, date FROM blog WHERE id=:id",
     *      array('id'=> '215')
     *      )
     * ->row()
     * ->all()
     * <pre>
     * @param string $sql Принимает открытый SQL запрос или безопасный
     * @param array $data Значения для безопасного запроса
     * @return $this        Возвращает екземпляр обекта

    public function query($sql, array $data = null)
    {

	    
        if ($this->dbh == null) {
            //QmError("Connection with DataBase closed!", "Соединение с Базой данны не закрыто или не существует");
            die("Connection with DataBase closed!");
        }

        self::$sql = $sql;

        if (is_null($data)) {
            $this->sth = $this->dbh->prepare($sql);
            $this->sth->execute();
        } else {
            $this->sth = $this->dbh->prepare($sql);
            $this->sth->execute($data);
        }
        return $this;
    }*/
    public function query($sql, array $data = null)
    {
        if ($this->dbh == null) {
            die("Connection with DataBase closed!");
        }

        self::$sql = $sql;

        if (is_null($data)) {
            $this->sth = $this->dbh->prepare($sql);
            if(!$this->sth)
                App::ExceptionError('Error SQL string!', 'Check your query string, error can be names :<br><span style="color:red">'.$sql.'</span>');
            $this->sth->execute();
        } else {
            $this->sth = $this->dbh->prepare($sql);
            if(!$this->sth)
                App::ExceptionError('Error SQL string!', 'Check your query string, error can be names :<br><span style="color:red">'.$sql.'</span>');
            $this->sth->execute($data);
        }
        return $this;

    }


    /**
     * Извлечь строку с запроса
     *
     * Выберает типы: assoc, class, obj
     * @param  string	$type 	использует FETCH_ASSOC, FETCH_CLASS, и FETCH_OBJ.
     * @return mixed
     */
	public function row($type = 'assoc')
    {
        if ($type == "assoc") $this->sth->setFetchMode(PDO::FETCH_ASSOC);
        if ($type == "obj") $this->sth->setFetchMode(PDO::FETCH_OBJ);
        if ($type == "class") $this->sth->setFetchMode(PDO::FETCH_CLASS);
        return $this->sth->fetch();
    }

    /**
     * Извлечь несколько строк
     *
     * @param  $type
     * @return array
     */
    public function all($type = 'assoc')
    {
        if ($type == "assoc") $this->sth->setFetchMode(PDO::FETCH_ASSOC);
        if ($type == "obj") $this->sth->setFetchMode(PDO::FETCH_OBJ);
        if ($type == "class") $this->sth->setFetchMode(PDO::FETCH_CLASS);

        $result = array();

        while ($rows = $this->sth->fetch()) {
            $result[] = $rows;
        };
        return $result;
    }

    /**
     * Обертка INSERT
     * <pre>
     * ->insert("pages", array("title","link","content","datetime","author"),
     *      array(
     *          'title'     =>'SOME TITLE',
     *          'link'      =>'SOME LINK',
     *          'content'   =>'SOME CONTENT',
     *          'datetime'  =>'SOME DATETIME',
     *          'author'    =>'SOME AUTHOR',
     *      ));
     * С генерирует SQL запрос:
     * "INSERT INTO pages (title,link,content,datetime,author)
     *      VALUES (:title,:link,:content,:datetime,:author)"
     * и подставит необходимые значения.
     * </pre>
     *
     * @param $table - Имя таблицы
     * @param array $dataColumn - Масив названий колонок для обновлеия
     * @param array $dataValue - Массив значений для установленных $dataColumn
     * @return bool
     */
    public function insert($table, array $dataColumn, array $dataValue)
    {
        if (count($dataColumn) == count($dataValue)) {
            $constructSql = "INSERT INTO " . $table . " (";
            $constructSql .= implode(", ", $dataColumn);
            $constructSql .= ") VALUES (";
            $constructSql .= ':' . implode(", :", $dataColumn);
            $constructSql .= ")";

            //$resultUpdate = $this->dbh->query($constructSql, $dataValue);

            $this->sth = $this->dbh->prepare($constructSql);
            //var_dump($dataValue);
            //die;
            $resultInsert = $this->sth->execute($dataValue);
            return $resultInsert;
        } else {
            return false;
        }
    }


    /**
     * Метод обертка UPDATE
     * <pre>
     * ->update( 'table', array('column'),array('data'), 'id=50' || array('id=:id', array('id'=>50)) );
     * 
     * ->update(
     *		"pages", 
     *		array("type","link","category","title","content","datetime","author"),
     *  	array(
     *          'type'     =>'SOME DATA TITLE',
     *          'link'     =>'SOME DATA LINK',
     *          'category' =>'SOME DATA CATEGORY',
     *          'title'    =>'SOME DATA TITLE',
     *          'content'  =>'SOME DATA CONTENT',
     *          'datetime' =>'SOME DATA TIME',
     *          'author'   =>'SOME DATA AUTHOR',
     *      	),
     *  	"id=13"
     *  );
     *
     * ->update(
     *		"pages", 
     *		array("type","link","category","title","content","datetime","author"),
     *      array(
     *          'type'     =>'SOME DATA TITLE',
     *          'link'     =>'SOME DATA LINK',
     *          'category' =>'SOME DATA CATEGORY',
     *          'title'    =>'SOME DATA TITLE',
     *          'content'  =>'SOME DATA CONTENT',
     *          'datetime' =>'SOME DATA TIME',
     *          'author'   =>'SOME DATA AUTHOR',
     *      ),
     *      array("id=:updId AND title=:updTitle", array('updId'=>13, 'updTitle'=>'SOME TITLE'))
     *  );
     * Сгенерирует: "UPDATE pages SET title=:title, type=:type, link=:link, category=:category, subcategory=:subcategory, content=:content, datetime=:datetime WHERE id=:updId AND title=:updTitle;"
     * </pre>
     *
     * @param $table - Имя таблицы
     * @param array $dataColumn - Масив названий колонок для обновлеия
     * @param array $dataValue - Массив значений для установленных $dataColumn
     * @param $where - определение, строка НЕ безопасно "id=$id", или безопасный вариант array( "id=:updId", array('updId'=>$id))
     * @return bool
     */
    public function update($table, array $dataColumn, array $dataValue, $where = null)
    {
        if (count($dataColumn) == count($dataValue)) {
            $constructSql = "UPDATE " . $table . " SET ";

            for ($i = 0; $i < count($dataColumn); $i++) {
                if ($i < count($dataColumn) - 1) {
                    $constructSql .= $dataColumn[$i] . "=:" . $dataColumn[$i] . ", ";
                } else {
                    $constructSql .= $dataColumn[$i] . "=:" . $dataColumn[$i] . " ";
                }
            }

            if (is_string($where)) {
                $constructSql .= " WHERE " . $where;
            } elseif (is_array($where) AND is_array($where[1])) {
                $constructSql .= " WHERE " . $where[0];
                $dataValue = array_merge($dataValue, $where[1]);
            }

            $this->sth = $this->dbh->prepare($constructSql);
            $resultUpdate = $this->sth->execute($dataValue);

            return $resultUpdate;
        } else {
            return false;
        }
    }


	/**
	 * Обертка удаления
	 * ->delete( 'table', 'key=val' || array('key=:key', array('key'=>val));
	 * ->delete('Users','id=21');
	 * ->delete('Users', array('id=:id', array('id'=>'21'));
	 *
	 */
    public function delete($table,  $where = null)
    {
        $dataValue = null;

	    $constructSql = "DELETE FROM " . $table;
	    
        if (is_string($where)) {
            $constructSql .= " WHERE " . $where;
        } elseif (is_array($where) AND is_array($where[1])) {
            $constructSql .= " WHERE " . $where[0];
            $dataValue = $where[1];
        }

        $this->sth = $this->dbh->prepare($constructSql);
        $resultUpdate = $this->sth->execute($dataValue);

        return $resultUpdate;
    }


    /**
     * Выбирает все записи с указанной таблицы.
     * Если указан второй аргумент выбирает только те поля что вказаны в нем
     *
     * <pre>
     * Например:
     *
     * ->getAll("table");
     *
     * ->getAll("table", "title, content, author");
     *
     * ->getAll("table", array(
     *      "title",
     *      "content",
     *      "author"
     * ));
     *
     * </pre>
     *
     * @param string            $tbl    название таблицы
     * @param null|string|array $data   если string через запятую, выберает указаные поля,
     *                                  если array по значених выберает указаные
     * @param string            $where  Часть запроса SQL where
     * @param string            $order  Часть запроса SQL order
     * @return mixed
     */
    public function getAll($tbl, $data = null, $where = '', $order='')
    {
        $sql = '';
        if ($data == null) {
            $sql = "SELECT * FROM " . $tbl;
        } elseif (is_string($data)) {
            $sql = "SELECT " . $data . " FROM " . $tbl;
        } elseif (is_array($data)) {
            $column = implode(", ", $data);
            $sql = "SELECT " . $column . " FROM " . $tbl;
        }
        $sql .= (!empty($where)) ? ' WHERE ' . $where : '';
        $sql .= (!empty($order)) ? ' ORDER BY ' . $order : '';
        return $this->query($sql)->all();
    }


    /**
     * Выберает все с указаной таблицы по id
     * <pre>
     * Например:
     *
     * ->getById("table", 215);
     *
     * ->getById("table", 215, "title, content, author");
     *
     * ->getById("table", 215, array(
     *      "title",
     *      "content",
     *      "author"
     * ));
     *
     * </pre>
     * @param $tbl      название таблицы
     * @param $id       id записи
     * @param $data (string|array)
     *                  если string через запятую, выберает указаные,
     *                  если array по значених выберает указаные
     * @return mixed
     */
    public function getById($tbl, $id, $data = null)
    {
        if ($data == null) {
            $sql = "SELECT * FROM " . $tbl . " WHERE id='" . $id . "'";
        } elseif (is_string($data)) {
            $sql = "SELECT " . $data . " FROM " . $tbl . " WHERE id='" . $id . "'";
        } elseif (is_array($data)) {
            $column = implode(", ", $data);
            $sql = "SELECT " . $column . " FROM " . $tbl . " WHERE id='" . $id . "'";
        }

        return $this->query($sql)->row();
    }


    /**
     * Выберает одну запись с указаной таблицы по названию колонки
     *
     * <pre>
     * Например:
     *
     * ->getByAttr("table", "column", "column_value");
     *
     * ->getByAttr("table", "column", "column_value", "title, content, author");
     *
     * ->getByAttr("table", "column", "column_value", array(
     *      "title",
     *      "content",
     *      "author"
     * ));
     *
     * ->getByAttr("table", "column", "column_value", null, "AND link='my_link'");
     *
     * </pre>
     *
     * @param string            $tbl        название таблицы
     * @param string            $attr       название колонки
     * @param string            $attrVal    значение в колонке
     * @param string            $andWhere   AND WHERE
     * @param string|array      $data       если string через запятую, выберает указаные, если array по значених выберает указаные
     * @return array
     */
    public function getByAttr($tbl, $attr, $attrVal, $data = null, $andWhere=null)
    {
        $setWhere = ($andWhere!=null) ? $andWhere : '';

        if ($data == null) {
            $sql = "SELECT * FROM " . $tbl . " WHERE " . $attr . "='" . $attrVal . "' ".$setWhere." ";
        } elseif (is_string($data)) {
            $sql = "SELECT " . $data . " FROM " . $tbl . " WHERE " . $attr . "='" . $attrVal . "' ".$setWhere."";
        } elseif (is_array($data)) {
            $column = implode(", ", $data);
            $sql = "SELECT " . $column . " FROM " . $tbl . " WHERE " . $attr . "='" . $attrVal . "' ".$setWhere."";
        }

        return $this->query($sql)->row();
    }


    /**
     * Выберает все с указаной таблицы по названию колонки
     *
     * <pre>
     * Например:
     *
     * ->getAllByAttr("table", "column", "column_value");
     *
     * ->getAllByAttr("table", "column", "column_value", "title, content, author");
     *
     * ->getAllByAttr("table", "column", "column_value", array(
     *      "title",
     *      "content",
     *      "author"
     * ));
     *
     * </pre>
     * @param string        $tbl        Таблица
     * @param string        $attr       По атрибуту, колонке
     * @param string        $attrVal    Значение $attr по которому делается поиск
     * @param string        $andWhere
     * @param string|array  $data       Поля что  нужно выбрать
     *                                      если string через запятую, выберает указаные,
     *                                      если array по значених выберает указаные
     * @return mixed
     */
    public function getAllByAttr($tbl, $attr, $attrVal, $data=null, $andWhere=null)
    {
		$setWhere = ($andWhere!=null) ? $andWhere : '';
	    
        if ($data == null) {
            $sql = "SELECT * FROM " . $tbl . " WHERE " . $attr . "='" . $attrVal . "' ".$setWhere." ";
        } elseif (is_string($data)) {
            $sql = "SELECT " . $data . " FROM " . $tbl . " WHERE " . $attr . "='" . $attrVal . "' ".$setWhere." ";
        } elseif (is_array($data)) {
            $column = implode(",", $data);
            $sql = "SELECT " . $column . " FROM " . $tbl . " WHERE " . $attr . "='" . $attrVal . "' ".$setWhere." ";
        }
        return $this->query($sql)->all();
    }


    /**
     * Закрыть соединение
     */
    public function close()
    {
        $this->dbh = null;
        unset($this->dbh);
    }

    public function reset()
    {
        $this->sth = null;
        $this->dbh = null;
        $this->init();
    }

} // END CLASS 'SimplePDO'