<?php

 function actMenu($link){
     if(isset(App::$args[1]) AND $link == App::$args[1])
        return ($link == App::$args[1])? "activeMenu":"";
 }


?>
<ul class="left_menu" id="left_ul">



    <?php foreach($snippetMenu as $sKey=>$sItems): ?>

        <li><a class="collapsed" href="#"> <?=$sKey;?> </a>
            <ul style="display: none;">

        <?php foreach($sItems as $sItem): ?>
                <li class="<?php echo actMenu($sItem['link'])?>">
                    <?php echo '<a class="" href="'.URL.'/category/select/'.toLower($sItem['category']).'/'.toLower($sItem['link']).'"> '.$sItem['title'].' </a>';?>
                </li>
        <?php endforeach; ?>

            </ul>
        </li>

    <?php endforeach; ?>


    
<!--
    <li><a class="collapsed collapsible" href="#0"> CSS</a>
        <ul style="display: none;">
            <li><a href="#">Box-shadow</a></li>
            <li><a href="#">Btn Hover Active</a></li>
            <li><a href="#">IE FIX</a></li>
            <li><a href="#">List of useful tagse</a></li>
            <li><a href="#">Lock copy in page</a></li>
            <li><a href="#">Menus menus</a></li>
        </ul>
    </li>
    <li><a class="collapsed collapsible" href="#0"> PHP</a>
        <ul style="display: none;">
            <li><a href="#">Array Sort functions</a></li>
            <li><a href="#">Directories Processing</a></li>
            <li><a href="#">Files Processin</a></li>
            <li><a href="#">HEREDOC and NOWDOC</a></li>
            <li><a href="#">Path and Url (part 2)</a></li>
            <li><a href="#">Pattern Observer</a></li>
            <li><a href="#">Pattern Prototype</a></li>
            <li><a href="#">Read big files (parts)</a></li>
            <li><a href="#">Regular Expressions DOCX</a></li>
            <li><a href="#">Validations with PHP</a></li>
            <li><a href="#">Wrap text (limit)</a></li>
        </ul>
    </li>
    <li><a class="collapsed collapsible" href="#0"> SQL</a>
        <ul style="display: none;">
            <li><a href="#">CREATE TABLE</a></li>
            <li><a href="#">Directories Processing</a></li>
            <li><a href="#">Files Processin</a></li>
            <li><a href="#">HEREDOC and NOWDOC</a></li>
            <li><a href="#">Path and Url (part 2)</a></li>
            <li><a href="#">Pattern Observer</a></li>
            <li><a href="#">Pattern Prototype</a></li>
            <li><a href="#">Read big files (parts)</a></li>
            <li><a href="#">Regular Expressions DOCX</a></li>
            <li><a href="#">Validations with PHP</a></li>
            <li><a href="#">Directories Processing</a></li>
            <li><a href="#">Files Processin</a></li>
            <li><a href="#">HEREDOC and NOWDOC</a></li>
            <li><a href="#">Path and Url (part 2)</a></li>
            <li><a href="#">Pattern Observer</a></li>
            <li><a href="#">Pattern Prototype</a></li>
            <li><a href="#">Read big files (parts)</a></li>
            <li><a href="#">Regular Expressions DOCX</a></li>
            <li><a href="#">Validations with PHP</a></li>
        </ul>
    </li>
    <li><a class="collapsed collapsible" href="#0"> Последние просмотриные</a>
        <ul style="display: none;">
            <li><a href="#">CREATE TABLE</a></li>
            <li><a href="#">Directories Processing</a></li>
            <li><a href="#">Files Processin</a></li>
            <li><a href="#">HEREDOC and NOWDOC</a></li>
            <li><a href="#">Path and Url (part 2)</a></li>
            <li><a href="#">Pattern Observer</a></li>
            <li><a href="#">Pattern Prototype</a></li>
            <li><a href="#">Read big files (parts)</a></li>
            <li><a href="#">Regular Expressions DOCX</a></li>
            <li><a href="#">Validations with PHP</a></li>
            <li><a href="#">Directories Processing</a></li>
            <li><a href="#">Files Processin</a></li>
            <li><a href="#">HEREDOC and NOWDOC</a></li>
            <li><a href="#">Path and Url (part 2)</a></li>
            <li><a href="#">Pattern Observer</a></li>
            <li><a href="#">Pattern Prototype</a></li>
            <li><a href="#">Read big files (parts)</a></li>
            <li><a href="#">Regular Expressions DOCX</a></li>
            <li><a href="#">Validations with PHP</a></li>
        </ul>
    </li>
-->


</ul>
