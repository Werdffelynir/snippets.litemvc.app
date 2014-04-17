<?php

class Category extends Model
{

    const TBL         = 'category';

    //const COL_ID      = 'id';

    /**
     * @param string $className
     * @return mixed
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

	/**
     * USE
     ********************************************************* */


    /**
     * Выборка по ID
     * @param $id
     * @param $data
     * @return bool
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
     * @param $title
     * @param $value
     * @param null $data
     * @return bool
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
     * @param $title
     * @param $value
     * @param null $data
     * @return bool
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
     * Получение списка для меню
     * @return bool
     */
    public function menuList()
    {
        $result = $this->db->getAll(self::TBL,null,null,'priority ASC');

        if(!empty($result)) {
            $listCat=array();
            foreach($result as $list){
                $count = $this->db->query("SELECT COUNT(*) as counter FROM files WHERE id_category='".$list['id']."' ")->all();
                $listCat[] = array(
                    'count'=> $count[0]['counter'],
                    'title'=> toLower($list['title']),
                    'link'=> $list['link'],
                );
            }
            return $listCat;
        } else
            return false;
    }

    /**
     * Получение списка категорий
     * @return bool
     */
    public function listCategory()
    {
        $result = $this->db->getAll(self::TBL,'id, title, link');
        if(!empty($result)) {
            $listCat=array();
            foreach($result as $list){

                $listCat[]=toLower($list['title']);
            }
            return $listCat;
        } else
            return false;
    }

    /**
     * @return array|bool
     */
    public function listCategories()
    {
        $result = $this->db->getAll(self::TBL,'id, title, link');
        if(!empty($result)) {
            return $result;
        } else
            return false;
    }

    /**
     * Добавление новой категории
     */
    public function addCategories($data)
    {
        if(is_array($data)){

            $priority = $this->lastId($this->db, self::TBL, $id='id');

            foreach($data as $f){

                $linkCat = cleanUrl(translit($f));

                $result = $this->db->insert(self::TBL, array(
                        "title",
                        "link",
                        "priority",
                        "disable"
                    ),
                    array(
                        'title'     => toUpper($f),
                        'link'      => $linkCat,
                        'priority'  => $priority,
                        'disable'   => 0
                    ));
                $priority ++;

                if(!$result)
                    return true;
                else{
                    if(!is_dir(APP.'DataFiles'.DS.$linkCat))
                        mkdir(APP.'DataFiles'.DS.$linkCat);
                }
            }
        }
    }



}
