<?php


class Ctrl extends Controller
{

    // Установка свойства для авторизации
    public $id = false;
    public $user = false;

    public $snippetData = false;
    public $listCategory = false;
    public $listSubCategory = false;

    protected $modelCategory;
    protected $modelSubCategory;
    protected $modelFiles;


    /* Метод отрабатываеться до выполнения */
    public function before()
    {
        // Установка выяснения языка
        App::initLang();
        
        // Подключения класса авторизации Classes/Auth.php
        Auth::run();
        $this->id = Auth::$id;
        $this->user = Auth::$user;

        $this->modelCategory = Category::model();
        $this->modelSubCategory = Sub_category::model();
        $this->modelFiles = Files::model();

        // Определение активного снипета если существует
        $linkCat = $this->urlArgs(1);
        $linkSnippet = $this->urlArgs(2);
        if(isset($linkCat) AND isset($linkSnippet) ){
            $result = $this->modelFiles->getSnippetActive($linkCat, $linkSnippet);
            $this->snippetData = $result;
        }

        // Извлечение списка категорий для главного меню
        $listMenu = $this->modelCategory->menuList();
        if(!is_array($listMenu) || empty($listMenu))
            $listMenu = array();

        // Список категорий полный
        $listCategory = $this->modelCategory->listCategories();
        if(is_array($listCategory) || !empty($listCategory))
            $this->listCategory = $listCategory;

        // Извлечение списка левого меню снипетов
        $snippetMenu = $this->modelFiles->snippetList();
        if(!is_array($snippetMenu) || empty($snippetMenu))
            $snippetMenu = array();

        $listFull=array();
        foreach($snippetMenu as $sm){
            $listFull[$sm['category']][] = array(
                'link'=>$sm['link'],
                'title'=>$sm['title'],
                'category'=>$sm['category'],
            );
        }

        $this->setChunk('menuCategory','chunksMenus/menuCategory', array('listMenu'=>$listMenu));
        $this->setChunk('menuSnippets','chunksMenus/menuSnippets', array('snippetMenu'=>$listFull));
        $this->setChunk("menuSettings", "chunksMenus/menuSettings");
        $this->setChunk("breadcrumbs", "chunks/breadcrumbs");
        $this->setChunk("settingContent");
    }



    /** ****************************************************************
     * GLOBAL WORKING METHODS
    ****************************************************************** */

    public function decodeShC($file)
    {
        if(!file_exists($file))
            if(App::$debug)
                App::ExceptionError('File not exists!',$file);
            else
                return false;

        $eTextOne = file_get_contents($file);
        $eTextOne = preg_replace('/^<!--DECLARATION(.+)-->/sm','', $eTextOne);
        $eTextOneTrans = htmlspecialchars($eTextOne);
        $trans = array(
            "[meta=keys]" => "<div class=\"code_meta_keys\">",
            "[/meta]" => "</div>",
            "[warning]" => "<div class=\"code_warning\">",
            "[/warning]" => "</div>",
            "[notice]" => "<div class=\"code_notice\">",
            "[/notice]" => "</div>",
            "[h]" => "<div class=\"code_header\">",
            "[/h]" => "</div>",
            "[!]" => "<div class=\"code_info\">",
            "[/!]" => "</div>",
            "[br]" => "<br />",
            "[code]" => "<div class=\"code_block\"><pre><code>",
            "[code=php]" => "<div class=\"code_block\"><pre><code class=\"php\">",
            "[code=sql]" => "<div class=\"code_block\"><pre><code class=\"sql\">",
            "[code=css]" => "<div class=\"code_block\"><pre><code class=\"css\">",
            "[code=html]" => "<div class=\"code_block\"><pre><code class=\"html\">",
            "[code=xml]" => "<div class=\"code_block\"><pre><code class=\"xml\">",
            "[code=js]" => "<div class=\"code_block\"><pre><code class=\"js\">",
            "[code=as]" => "<div class=\"code_block\"><pre><code class=\"as\">",
            "[/code]" => "</div></pre></code>",
            "\t" => "&nbsp;&nbsp;"
        );
        $eTextOneTrans = strtr($eTextOneTrans, $trans);
        $eTextOneTrans = preg_replace('|\[a=(.+)\](.+)\[\/a\]|','<a class="code_link" href="http://$1">$2</a>', $eTextOneTrans);
        //$eTextOneTrans = wordwrap($eTextOneTrans, 80, "\n");
        return  $eTextOneTrans;
    }


    public function convertToUrl($str, $num=false, $limit=18)
    {
        $_u = limitChars(cleanUrl(toLower(translit($str))), $limit, '');

        if($num)
            return $_u.'_'.date("dmyhis");
        else
            return $_u;
    }

}