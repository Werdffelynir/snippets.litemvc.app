<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    
    <link rel="stylesheet" type="text/css" href="<?php echo URL_THEME?>/fontello/css/fontello.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo URL_THEME?>/css/base.css" />

    <script type="text/javascript" src="<?php echo URL_THEME?>/highlight/highlight.pack.js"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo URL_THEME?>/highlight/styles/obsidian.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo URL_THEME?>/highlight/stylesCode.css" />

    <script type="text/javascript" src="<?php echo URL_THEME?>/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?php echo URL_THEME?>/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?php echo URL_THEME?>/js/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="<?php echo URL_THEME?>/js/scripts.js"></script>
    
    <title>{SN::<?php
        if(isset($this->snippetData["files"]["title"])){
            echo $this->snippetData["files"]["title"];
        }else{
            if(App::$controller=='index')
                echo 'Заметки кода';
            elseif(App::$controller == 'category')
                echo 'Категория '.toUpper($this->urlArgs(1));
            elseif(App::$controller == 'edit')
                echo 'New Snippet';
        };
        ?>}</title>

    <style type="text/css" rel="stylesheet" >
        .windows{
            display: none;
            width: 800px;
            min-height: 350px;
            margin-left: 50%;
            left: -400px;
            position: absolute;
            z-index: 9999;
        }
        .windows .winPanel{}
        .windows .winContent{}
        .windows .winPanel .winClose{
            float: right;
        }

    </style>

    <script type="text/javascript" >
        $(function(){

            $(".wineOpen").click(function(){
                $(".windows").toggle();
            });
            $(".windows").on('click','.winClose',function(){
                $(".windows").toggle();
            });

            $(".windows").on('click','.ajaxer',function(){
                var post = $(this).attr('data-post');
                var dump = $(this).attr('data-dump');
                var action = $(this).attr('data-action');
                if(action == undefined)
                    action = $(this).attr('href');

                console.log(post);
                console.log(action);
                console.log(dump);

                return false;

                //  onclick="return false;"
            });

        });
    </script>
</head>
<body>

<div class="box windows">

    <div class="winPanel clean">
        <div class="btn"><a class="ajaxer" href="index/index" data-post="" data-dump=""> Сканировать </a></div>
        <div class="btn winClose"><a href="#blockContent#"> <i class="icon-cancel"></i> </a></div>
    </div>
    <div class="winContent"></div>

</div>

<div id="blockWrapper" class="clear full">



    <div id="blockHeader" class="full">
        <div class="box">
            <span class="topLogo">
                <span style="color:#6699FF">Notes</span> 
                <span> <a  style="color:#FFCC00; text-decoration: none;" href="<?=URL?>">{SnippetNotes}</a> </span>
                <span style="color:#6699FF">Stack</span>
            </span>
            <span class="topSearch">
                <form name="" method="post" action="">
                    <input type="text" name="" value="">
                    <span class="btn-seach">
                        <a href="#"><i class="icon-search"></i></a>
                    </span>
                </form>
            </span>
            <span class="topMenuSett">
                <ul>
                    <li class="active"><a title="Добавить сниппеты" class="wineOpen" href="#"><i class="icon-plus"></i></a></li>
                    <li><a title="Загрузить файл с сниппетом" class="wineOpen" href="#"><i class="icon-inbox"></i></a></li>
                    <li><a title="Последние просмотренные" class="wineOpen" href="#"><i class="icon-fire"></i></a></li>
                    <li><a title="Запомнить снипет" class="wineOpen" href="#"><i class="icon-target"></i></a></li>
                    <li><a title="Настройки" class="wineOpen" href="#"><i class="icon-cog"></i></a></li>
                </ul>
            </span>
        </div>
    </div>

    <div id="blockMenu" class="full">
            <?php $this->chunk("menuCategory");?>
    </div>

    <div id="blockContent" class="clear full">
        <div class="left first lite_3 box">
            <?php $this->chunk("menuSnippets");?>
        </div>

        <div class="right lite_9 box">

            <div class="pageHeader full clear">
                <div class="path first lite_9"><?php $this->chunk("breadcrumbs");?></div>
                <div class="settings lite_3">
                    <a href="<?=URL.'/edit/new'?>" title="Новый snippet"><i class="icon-doc-new"></i></a>
                    <?php $this->chunk("settingContent");?>
                </div>
            </div>

            <div class="pageContent">
                <!--<html><head></head><body>--><!--</body></html>-->
                <div contentEditable="true"  spellcheck="false"></div>
	            <?php $this->renderTheme( 'OUT' );?>
            </div>

        </div>
    </div>

		<div id="footer" class="full">
	        <p>Copyright &copy; &mdash; 2014 SunLight, Inc. <a href="http://werd.id1945.com/"> OL Werdffelynir</a>. All rights reserved.
                <?php global $timeLoader; list($microtime, $sec) = explode(chr(32), microtime());
                echo 'Was compiled per: ' . round(($sec + $microtime) - $timeLoader, 4) . ' sec.'; ?>
            </p>
		</div><!--END footer-->

</div>


<style type="text/css">
    .scrollToTop {
        display: none;
        line-height: normal;
        position: fixed;
        z-index: 500;
        background: none repeat scroll 0 0 rgba(0, 0, 0, 0.3);
    }
    .scrollToTop a {
        border: medium none;
        display: block;
        font-size: 20px;
        opacity: 0.6;
        text-align: center;
        text-decoration: none;
    }
    .scrollToTop a:hover {
        background: none repeat scroll 0 0 rgba(0, 0, 0, 0.7);
    }
    .scrollToTop.scrollToTop_ribbon {
        bottom: 0;
        left: 0;
        max-width: 250px;
        top: 0;
        width: 6%;
    }
    .scrollToTop.scrollToTop_ribbon a {
        color: rgb(255, 255, 255);
        height: 100%;
        padding: 10px 0 0;
        width: 100%;
    }
    .scrollToTop.scrollToTop_ribbon a:hover {
        color: rgb(255, 255, 255);
    }
    .scrollToTop.scrollToTop_block {
        bottom: 10px;
        right: 10px;
    }
    .scrollToTop.scrollToTop_block a {
        background: none repeat scroll 0 0 rgb(0, 0, 0);
        border-radius: 3px;
        color: rgb(255, 255, 255);
        padding: 10px 15px;
    }
    .scrollToTop.scrollToTop_block a:hover {
        opacity: 0.6;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        var scrollToTop = '#scrollToTop';

        $(window).scroll(function() {
            if ($(this).scrollTop() > 200) {
                $(scrollToTop).fadeIn();
            } else {
                $(scrollToTop).fadeOut();
            }
        });
        $(scrollToTop).click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 400);
            return false;
        });

    });
</script>
<div class="scrollToTop scrollToTop_ribbon" id="scrollToTop" style="display: none;">
    <a href="#" title="Наверх">
        ↑
    </a>
</div>


</body>
</html>