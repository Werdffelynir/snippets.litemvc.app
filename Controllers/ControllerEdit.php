<?php


class ControllerEdit extends Ctrl
{

    public $category;

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
    }

    public function after()
    {
        parent::after();

        $this->category = ($this->urlArgs())?$this->urlArgs():'bag';

        $snippetMenu = $this->modelFiles->snippetList();
        if(!is_array($snippetMenu) || empty($snippetMenu))
            $snippetMenu = array();

        $listFull = array();
        foreach($snippetMenu as $sm){
            if(toUpper($sm['category']) != toUpper($this->category))
                continue;

            $listFull[$sm['sub_category']][] = array(
                'link'=>$sm['link'],
                'title'=>$sm['title'],
                'category'=>$sm['category'],
            );
        }

        $this->setChunk('menuSnippets','chunksMenus/menuSnippetsCat', array('snippetMenu'=>$listFull));

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

        $this->render('OUT', 'main', array(
            'content'=>$content
        ));
    }

    /**
     * Обработчик страницы "Главная"
     */
    function actionNew()
    {
        $this->setChunk("breadcrumbs", "chunks/breadcrumbs", array('title'=>'New Snippet'));
        $content = $this->partial('edit/formEditSnippet',array(
            'typeRecord'=>'new'
        ));

        // вывод в Views/main.php
        $this->render('OUT', 'main', array(
            'content'=>$content
        ));

    }

    function actionSnippet()
    {
        $this->setChunk("settingContent", "chunksMenus/settingContent");

        $dataText = file_get_contents(APP.'DataFiles'.DS.$this->snippetData['files']["link_full"].'.html');
        $dataCubCat = $this->modelSubCategory->getSubCategoriesById($this->snippetData["files"]["id_category"]);
        $sub_category = '';

        if(is_array($dataCubCat))
            foreach($dataCubCat as $scItem){
                $selected = ($scItem["id"]== $this->snippetData["files"]["id_sub_category"] )?'selected="selected"':'';
                $sub_category .= '<option '.$selected.' value="'.$scItem["id"].'">'.$scItem["title"].'</option>';
            }

        $content = $this->partial('edit/formEditSnippet', array(
                'data'=>array(
                    'sub_category'=> $sub_category,
                    'id'=> $this->snippetData["files"]["id"],
                    'title'=> $this->snippetData["files"]["title"],
                    'link_full'=> $this->snippetData["files"]["link_full"],
                    'text'=> $dataText
                ),
                'typeRecord'=>'update'
            )
        );

        // вывод в Views/main.php
        $this->render('OUT', 'main', array(
            'content'=>$content
        ));
    }

    function actionSaveSnippet()
    {

        if($_POST["type_record"] == 'new'){

            $data = array(
                'newCategory' => (isset($_POST["new_category"]))?$_POST["new_category"]:false,
                'newCategoryLink' => (isset($_POST["new_category"]))?$this->convertToUrl($_POST["new_category"]):false,
                'newSubCategory' => (isset($_POST["new_sub_category"]))?$_POST["new_sub_category"]:false,
                'newSubCategoryLink' => (isset($_POST["new_sub_category"]))?$this->convertToUrl($_POST["new_sub_category"]):false,
                'title' => $_POST["title"],
                'id_category' => $_POST["id_category"],
                'id_sub_category' => (isset($_POST["id_sub_category"]))?$_POST["id_sub_category"]:0,
                'text' => $_POST["text"],
                'titleLink' =>  $this->convertToUrl($_POST["title"])
            );

            $resultRecord = $this->modelFiles->addNewSnippet($data);

            if($resultRecord)
                App::redirect($resultRecord['relink']);

        }else{
            $result = file_put_contents(APP.'DataFiles'.DS.trim($_POST["link_full"]).'.html', $_POST["text"]);

            if($result==true) {
                $data = array(
                    'id' => clean($_POST["id"]),
                    'title' => clean($_POST["title"]),
                    'id_category' => clean($_POST["id_category"]),
                    'id_sub_category' => clean($_POST["id_sub_category"])
                );

                $resultUpdate = $this->modelFiles->updateSnippet($data);

                if($resultUpdate)
                    App::redirect($_SERVER["HTTP_REFERER"]);

            } else {
                App::ExceptionError('Error save file: '.APP.'DataFiles'.DS.trim($_POST["link_full"]).'.html');
            }
        }

    }


    function actionGetCategory()
    {
        if($this->isAjax()){
            $sub_category = $_POST['sub_category'];
            $dataCubCat = $this->modelSubCategory->getSubCategoriesById($sub_category);
            $result = '';

            foreach($dataCubCat as $scItem){
                $result .= '<option value="'.$scItem["id"].'">'.$scItem["title"].'</option>';
            }

            $result .= '<option value="newSubCategory">Новая суб-категория</option>';

            echo $result;
        }
    }

    function actionExample()
    {
        $lastSnippetId = App::getCookie('lastSnippetId');

        if($lastSnippetId==null)
            App::redirect(URL.'');

        $this->setChunk("breadcrumbs", "chunks/breadcrumbs", array('title'=>'View Example'));
        $this->setChunk("settingContent", "chunksMenus/settingContent");

        $dataText = '';

        if(!empty($lastSnippetId)){
            $data = $this->model('Examples')->getByAttrRec('id_file',$lastSnippetId);
            $styles = '<style type="text/css">.inIFrame{ color: #FFFFFF; font-family: \'AleksandraC\', Consolas, Arial; }.inIFrame a{ color: #ffc20b; }.inIFrame a:hover{ color: #8F6B00; }</style>';
            if($data){
                $dataText = $data['text'];
                file_put_contents(APP.'Views'.DS.'_temp_example.php', $styles.'<div class="inIFrame">'.$dataText.'</div>');
            }else{
                file_put_contents(APP.'Views'.DS.'_temp_example.php', $styles.'<div class="inIFrame"><span style="color:#ffc20b">Не создан еще.</span></div>');
            }
        }

        $content = $this->partial('edit/frameEditExample',array(
            'data'=>array(
                'id'=> $lastSnippetId,
                'text'=> $dataText,
            )
        ));

        $this->render('OUT', 'main', array(
            'content'=>$content
        ));

    }

    function actionExampleEdit()
    {

        $lastSnippetId = App::getCookie('lastSnippetId');

        if($lastSnippetId==null)
            App::redirect(URL.'');

        $this->setChunk("breadcrumbs", "chunks/breadcrumbs", array('title'=>'Edit Example'));
        $this->setChunk("settingContent", "chunksMenus/settingContent");

        $dataText = '';
        $type = 'new';

        if(isset($_POST['text'])){

            if($_POST['type'] == 'new'){
                $data = $this->model('Examples')->insertRec(array(
                    'id_file' => $lastSnippetId,
                    'text'=>$_POST['text'],
                ));

                if($data){
                    App::redirect(URL.'/edit/example/');
                }
            }else{
                $data = $this->model('Examples')->updateRec(array(
                    'id_file'=>$lastSnippetId,
                    'text'=>$_POST['text'],
                ));
                if($data){
                    App::redirect(URL.'/edit/example/');
                }
            }

        }else{

            $data = $this->model('Examples')->getByAttrRec('id_file',$lastSnippetId);
            if($data){
                $dataText = $data['text'];
                $type = 'exists';
            }

        }

        $content = $this->partial('edit/formEditExample',array(
            'data'=>array(
                'id'=> $lastSnippetId,
                'text'=> $dataText,
                'type'=> $type
            )
        ));

        // вывод в Views/main.php
        $this->render('OUT', 'main', array(
            'content'=>$content
        ));

    }


    function actionShow()
    {
        echo $this->partial('_temp_example');
    }
}
