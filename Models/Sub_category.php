<?php

class Sub_category extends Model
{

    const TBL         = 'sub_category';

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

    public function getAll()
    {
        $result = $this->db->getAll(self::TBL);

        if(!empty($result))
            return $result;
        else
            return false;
    }

    /**
     * Выборка по ID
     * @param $id
     * @return bool
     */
    public function getByIdRec($id)
    {
        $result = $this->db->getById(self::TBL, $id);
        if(!empty($result))
            return $result;
        else
            return false;
    }

    /**
     * Получение списка категорий
     * @return bool
     */
    public function listSubCategory()
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
    public function listSubCategories()
    {
        $result = $this->db->getAll(self::TBL,'id, title, link');

        if(!empty($result))
            return $result;
        else
            return false;
    }

    public function getSubCategoriesById($id_category)
    {
        $result = $this->db->getAllByAttr(self::TBL, 'id_category', $id_category);

        if(!empty($result))
            return $result;
        else
            return false;
    }


    /**
     * Добавление новой категории
     */
    public function addSubCategories($data)
    {
        $priority = $this->lastId($this->db, self::TBL, $id='id');

        foreach($data as $c){

            $id_category = $this->db->getByAttr('category', 'title', toUpper($c["category"]));

            if(!isset($id_category))
                $id_category['id'] = 0;

            $linkCat = cleanUrl(translit($c["category"]));
            $linkSubCat = cleanUrl(translit($c["subcategory"]));

            $result = $this->db->insert(self::TBL, array(
                    "id_category",
                    "title",
                    "link",
                    "priority",
                    "disable"
                ),
                array(
                    'id_category'   => $id_category['id'],
                    'title'         => $c["subcategory"],
                    'link'          => $linkSubCat,
                    'priority'      => $priority,
                    'disable'       => 0
                ));

            if(!$result){
                App::ExceptionError('Error DB insert subcategory');
            }else{

                if(!is_dir(APP.'DataFiles'.DS.$linkCat.DS.$linkSubCat))
                    mkdir(APP.'DataFiles'.DS.$linkCat.DS.$linkSubCat);
            }
            $priority ++;
        }
    }



}