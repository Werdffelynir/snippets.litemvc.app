<?php


class ControllerCategory extends Ctrl
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

        $this->category = ($this->urlArgs())?$this->urlArgs():'PHP';

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

        App::setCookie('lastSnippetId', $this->snippetData["files"]["id"]);

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
        <style type="text/css"></style>
        <p>Повседневная практика показывает, что начало повседневной работы по формированию
        позиции требует от нас анализа новейших вариантов поиска решений.</p>
        <p>Товарищи, начало повседневной работы по формированию позиции обеспечивает широкому
        кругу (специалистов) участие в формировании системы массового участия.</p>
        <p>Таким  образом рамки и место обучения кадров создает все необходимые предпосылки для
        утверждения и анализу необходимых данных для разрешения ситуации в целом.</p>
        <p>Повседневная практика показывает, что сложившаяся ситуация ни коим образом не
        позволяет выполнить важные задания по разработке новых предложений.</p>';

        // вывод в Views/main.php
        $this->render('OUT', 'main', array(
            'content'=>$content
        ));
    }

    /**
     * Обработчик страницы "Главная"
     */
    function actionSelect()
    {

        $_link = ($this->urlArgs(2))?$this->urlArgs(2):false;
        if($_link){

            $this->setChunk("settingContent", "chunksMenus/settingContent");

            $_snippet = $this->modelFiles->getPage($_link);
            $content = nl2br($this->decodeShC(APP.'DataFiles/'.$_snippet["link_full"].'.html'));
        }else{
            $content = '';
        }

        $this->render('OUT', 'main', array(
            'content'=>$content
        ));
    }


}
