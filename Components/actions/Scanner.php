<?php

class Scanner
{
    protected $modelCategory;
    protected $modelSubCategory;
    protected $modelFiles;

    public function run()
    {
        $this->modelCategory = Category::model();
        $this->modelSubCategory = Sub_category::model();
        $this->modelFiles = Files::model();

        $newFiles = $this->scanNewFiles();
        if(!is_array($newFiles) || empty($newFiles)) $newFiles = array();

        $listCat = $this->modelCategory->listCategory();
        if(!is_array($listCat) || empty($listCat)) $listCat = array();

        $listSubCat = $this->modelSubCategory->listSubCategory();
        if(!is_array($listSubCat) || empty($listSubCat)) $listSubCat = array();

        $newCat = array();
        $newSubCat = array();

        foreach($newFiles as $files){

            /** если категории не существует, создание каталога и зарписи в DB*/
            if(!in_array(toLower($files['CATEGORY']), $listCat)){
                $newCat[] = cleanUrl(toLower($files['CATEGORY']));
            }

            /** если субкатегории не существует, создание каталога и зарписи в DB */
            if(!in_array(toLower($files['SUBCATEGORY']), $listSubCat)){
                $newSubCat[] = array(
                    'title'         => $files['TITLE'],
                    'category'      => $files['CATEGORY'],
                    'subcategory'   => $files['SUBCATEGORY'],
                );
            }
        }

        $newCat = array_unique($newCat);

        $this->modelCategory->addCategories($newCat);
        $this->modelSubCategory->addSubCategories($newSubCat);
        $this->modelFiles->createNewSnippets($newFiles);
    }

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

$scan = new Scanner();
$scan->run();

