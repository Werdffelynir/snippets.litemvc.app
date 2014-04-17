<?php

class Original extends Model
{
    /**
     * Константы назначаються для универсального доступа к полям и таблице
     */
    const TBL         = 'docs';
    //const COL_ID      = 'id';

    /**
     * Метод необходим для связи с контолеров или других включенных скриптов к данной модели
     *
     * @param string $className
     * @return mixed
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


	/*********************************************************************
     *                              USE
     ****************************************************************** **/

    /**
     * Выбрать все записи
     *
     * @return array|bool
     */
    public function getAllRec()
    {
        $result = $this->db->getAll(self::TBL);
        if(!empty($result))
            return $result;
        else
            return false;
    }


    /**
     * Выборка по ID
     *
     * @param $id
     * @param $data
     * @return array|bool
     */
    public function getByIdRec($id, $data=null)
    {
        $result = $this->db->getById(self::TBL, $id, $data);
        if(!empty($result))
            return $result;
        else
            return false;
    }

    /**
     * Выборка по атребуту
     *
     * @param $title
     * @param $value
     * @param null $data
     * @return array|bool
     */
    public function getByAttrRec($title, $value, $data=null)
    {
        $result = $this->db->getByAttr(self::TBL, $title, $value, $data);
        if(!empty($result))
            return $result;
        else
            return false;
    }

    /**
     * Выборка многоих по атребуту
     *
     * @param $title
     * @param $value
     * @param null $data
     * @return array|bool
     */
    public function getAllByAttrRec($title, $value, $data=null)
    {
        $result = $this->db->getAllByAttr(self::TBL, $title, $value, $data);
        if(!empty($result))
            return $result;
        else return
            false;
    }


    /**
     * Новая запись
     *
     * @param $data
     * @return array|bool
     */
    public function insertRec($data){

        $result = $this->db->insert(self::TBL,
            array("val_1","val_2","val_3"),
            array(
                'val_1'=>$data['__'],
                'val_2'=>$data['__'],
                'val_3'=>$data['__']
            ));

        if(!empty($result)) return $result;
        else return false;
    }


    /**
     * Обновление существующей записи
     *
     * @param $data
     * @param $id
     * @return array|bool
     */
    public function updateRec($data, $id){

        $result = $this->db->update(self::TBL,
            array("val_1","val_2","val_3"),
            array(
                'val_1'=>$data['__'],
                'val_2'=>$data['__'],
                'val_3'=>$data['__']
            ),
            array("id=:updId",
                array('updId'=>$id)
            )
        );

        if(!empty($result)) return $result;
        else return false;
    }


    /**
     * Удаление записи по ID
     *
     * @param $id
     * @return array|bool
     */
    public function deleteRec($id)
    {
        $result = $this->db->delete(self::TBL,
            array('id=:id', array('id'=>$id))
        );
        if(!empty($result)) return $result;
        else return false;
    }


    /**
     * Последняя запись этой таблицы, ID по умолчанию
     *
     * @param $table
     * @param $id
     * @return bool|mixed
     */
    public function lastIdRec($table=null, $id=null)
    {
        $table = ($table==null)? self::TBL : $table;
        $id = ($table==null)? self::COL_ID : $id;

        $result = $this->lastId($this->db, $table, $id);

        if(!empty($result)) return $result;
        else return false;
    }


    /**
     * Количество записей в данной таблицк или указаной
     *
     * @param $table
     * @return bool|mixed
     */
    public function countRec($table=null)
    {
        $table = ($table==null)? self::TBL : $table;
        $result = $this->lastId($this->db, $table, 'id');

        if(!empty($result)) return $result;
        else return false;
    }

}
