<?php

class Files extends Model
{

    const TBL         = 'files';

    //const COL_ID      = 'id';

    /**
     * @param string $className
     * @return mixed
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

	/** USE **/

    public function getAll()
    {
        $result = $this->db->getAll(self::TBL);
        if(!empty($result)) return $result;
        else return false;
    }

    public function createNewSnippets($data)
    {
        foreach($data as $file){

            $linkCat = cleanUrl(translit($file["CATEGORY"]));
            $linkSubCat = cleanUrl(translit($file["SUBCATEGORY"]));
            $linkFile = cleanUrl(translit($file["TITLE"]));
            $link_full = $linkCat.'/'.$linkSubCat.'/'.$linkFile;
            $priority =  $this->lastId($this->db, self::TBL, $id='id');

            $id_category = $this->db->getByAttr('category', 'link', $linkCat);
            if(!$id_category)
                $id_category['id'] = 0;

            $id_sub_category = $this->db->getByAttr('sub_category', 'link', $linkSubCat);
            if(!$id_category)
                $id_sub_category['id'] = 0;

            $result = $this->db->insert(self::TBL, array(
                    "id_category",
                    "id_sub_category",
                    "title",
                    "link",
                    "link_full",
                    "priority"
                ),
                array(
                    'id_category' => $id_category['id'],
                    'id_sub_category' => $id_sub_category['id'],
                    'title' => $file['TITLE'],
                    'link' => $linkFile,
                    'link_full' => $link_full,
                    'priority' => $priority,
                ));

            if(!$result)
                App::ExceptionError('Error DB insert NewSnippet: '.$file['TITLE']);
            else{

                if (!rename($file["FULLPATH"], APP.'DataFiles'.DS.$link_full.'.html')) {
                    App::ExceptionError('Error! не удалось скопировать: '.$file['TITLE']);
                }

            }
        }
    }


    public function snippetList($cat=null)
    {

        if($cat != null){
            $sql = "SELECT f.id, f.link, f.title, cat.title as category, scat.title as sub_category
                    FROM files as f
                    JOIN category cat ON cat.id=f.id_category
                    JOIN sub_category scat ON scat.id=f.id_sub_category
            ";
        }else{
            $sql = "SELECT f.id, f.link, f.title, cat.title as category, scat.title as sub_category
                    FROM files as f
                    JOIN category cat ON cat.id=f.id_category
                    JOIN sub_category scat ON scat.id=f.id_sub_category
            ";
        }
        $result = $this->db->query($sql)->all();

        if(!empty($result))
            return $result;
        else
            return false;

    }


    public function getPage($link)
    {
        $result = $this->db->getByAttr(self::TBL, 'link', $link);
        if(!empty($result))
            return $result;
        else
            return false;
    }



    public function getSnippet($linkCat,$linkSnipp)
    {
        $result = $this->db->getByAttr(self::TBL, 'link', $linkSnipp);
        if(!empty($result))
            return $result;
        else
            return false;
    }

    public function getSnippetActive($linkCat, $linkSnippet)
    {
        $getCategory = $this->db->getByAttr('category', 'link', $linkCat);
        $getFiles = $this->db->getByAttr(self::TBL, 'link', $linkSnippet, null, "AND id_category='".$getCategory['id']."'");
        $getSubCategory = $this->db->getByAttr('sub_category', 'id', $getFiles['id_sub_category']);

        if(!empty($getFiles))
            return array(
                'files'=>$getFiles,
                'category'=>$getCategory,
                'sub_category'=>$getSubCategory
            );
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

    public function updateSnippet($data)
    {

        $result = $this->db->update(
            self::TBL,
            array("title", "id_category", "id_sub_category"),
            array(
                'title' => $data['title'],
                'id_category' => $data['id_category'],
                'id_sub_category' => $data['id_sub_category']
            ),
            "id=".$data['id']
        );

        if(!empty($result))
            return true;
        else
            return false;

    }


    public function addNewSnippet($data)
    {
        if($data['newCategory']){

            $id_category = $this->lastId($this->db, 'category', $id='id');
            $data['id_category'] = $id_category +1;
            $SQL = "INSERT INTO 'category' ('title', 'link', 'priority')
                    VALUES ('".$data['newCategory']."','".$data['newCategoryLink']."','".$id_category."')";
            $result = $this->db->exec($SQL);
            if(!$result)
                App::ExceptionError("ERROR! SQL insert into TABLE 'category'", $SQL);
        }


        if($data['newSubCategory']){

            $id_sub_category = $this->lastId($this->db, 'sub_category', $id='id');
            $data['id_sub_category'] = $id_sub_category +1;
            $SQL = "INSERT INTO 'sub_category' ('id_category', 'title', 'link', 'priority')
                    VALUES ('".$data['id_category']."', '".$data['newSubCategory']."','".$data['newSubCategoryLink']."','".$id_sub_category."')";

            $result = $this->db->exec($SQL);

            if(!$result)
                App::ExceptionError("ERROR! SQL insert into TABLE 'category'", $SQL);
        }


        $resultLinks = $this->db->query('SELECT scat.link as sub_cst_link, cat.link as cat_link
                                            FROM sub_category scat
                                            JOIN category cat ON cat.id = scat.id_category
                                            WHERE scat.id='.$data["id_sub_category"].'')->row();

        if(!is_dir( APP.'DataFiles'.DS.$resultLinks['cat_link'] ))
            mkdir( APP.'DataFiles'.DS.$resultLinks['cat_link'] );
        if(!is_dir( APP.'DataFiles'.DS.$resultLinks['cat_link'].DS.$resultLinks['sub_cst_link'] ))
            mkdir( APP.'DataFiles'.DS.$resultLinks['cat_link'].DS.$resultLinks['sub_cst_link'] );

        $link_full = $resultLinks['cat_link'].'/'.$resultLinks['sub_cst_link'].'/'.$data['titleLink'];

        $fullFilePath = APP.'DataFiles'.DS.$link_full.'.html';
        $putResult = file_put_contents($fullFilePath, $data["text"]);

        $priority =  $this->lastId($this->db, self::TBL, $id='id');

        if($putResult>0) {

            $result = $this->db->insert(self::TBL, array(
                    "id_category",
                    "id_sub_category",
                    "title",
                    "link",
                    "link_full",
                    "priority"
                ),
                array(
                    'id_category' => (int) $data['id_category'],
                    'id_sub_category' => (int) $data['id_sub_category'],
                    'title' => $data['title'],
                    'link' => $data['titleLink'],
                    'link_full' => $link_full,
                    'priority' => (int) $priority,
                ));

            if(!empty($result))
                return array(
                    'relink' => URL.'/edit/snippet/'.$resultLinks['cat_link'].'/'.$data['titleLink']
                );
            else
                return false;

        }else{
            App::ExceptionError();
        }

    }


}
