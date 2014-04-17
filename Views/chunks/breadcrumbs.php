<?php

$cat = ($this->snippetData["category"]["title"]) ? toUpper($this->snippetData["category"]["title"].' / ') : '';
$subCat = ($this->snippetData["sub_category"]["title"]) ? $this->snippetData["sub_category"]["title"].' / ' : '';
$file = ($this->snippetData["files"]["title"]) ? $this->snippetData["files"]["title"] : '';

if(isset($title)){
    $breadcrumbs = '<span style="color:#B3AA0C"><b>Title::</b></span> '.$title;
}else{
    $breadcrumbs = '<span style="color:#B3AA0C"><b>Path::</b></span> '.$cat.$subCat.$file;
}
?>

<span style="color:#66CCFF"><?=$breadcrumbs?></span>
