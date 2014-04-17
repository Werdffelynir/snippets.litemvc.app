﻿<!DOCTYPE html>
<html>
<head>
    <meta charset=UTF-8" />
    <title>higlight.js example</title>
    <style type="text/css">
        /* fonts */
        @import "http://webfonts.ru/import/aleksandrac.css";
        /* font-family: 'AleksandraC'; */
        @import "http://webfonts.ru/import/arnamu.css";
        /* font-family: 'Arian AMU'; */
        @import "http://webfonts.ru/import/ubuntu.css";
        /* font-family: 'Ubuntu Mono'; */
        body{
            background-color: #333333;
            font-family: 'AleksandraC', Arian AMU, AleksandraC, Consolas, Courier, monospace, "Courier New";
            color: #BBBBBB;
            text-shadow: 0px 1px 0px #4F4F4F;
        }
        .page{
            width: 860px;
            margin: 0 auto;
            background-color: #282828;
            font-size: 12px;
            padding: 10px;
        }

        /* Parser style */
        .code_header{
            margin-top: 10px;
            font-size: 130%;
            color: #FF5D15;
            font-weight: bold;
        }
        .code_meta_keys{
            font-size: 90%;
            color: #979792;
            font-style: italic;
        }
        .code_notice{
            padding: 5px;
            background-color: #278200;
            color: #FFF;
        }
        .code_warning{
             padding: 5px;
             background-color: #D50015;
             color: #FFF;
         }
        .code_info{
            padding-left: 25px;
        }
        .code_block{
            overflow-y: auto;
            text-shadow: none;
        }
        .code_block code{
            display: inline-block;
            font-family: Consolas, "Courier New", Courier, monospace;
        }
        .code_link{
            color: #ff0f00;
        }
    </style>

    <script type="text/javascript" src="highlight.pack.js"></script>
    <link type="text/css" rel="stylesheet" href="styles/obsidian.css" />

    <script type="text/javascript" >
        hljs.initHighlightingOnLoad();
    </script>
</head>
<body>
<div class="page">
<?php



/*
[meta=keys] ... [/meta]
[h] ... [/h]
[a=site.com] some site [/a] // <a href="http://site.com">some site</a>
[!] ... [/!]
[br]
[code] ... [/code]
[notice] ... [/notice]
[warning] ... [/warning]
[code=php] ... [/code]
[code=css] ... [/code]
[code=js] ... [/code]
[code=sql] ... [/code]
[code=html] ... [/code]
[code=xml] ... [/code]
[code=as] ... [/code]

.code_header{}
.code_meta_keys{}
.code_notice{}
.code_warning{}
.code_link{}
.code_info{}
.code_block{}
.code_block pre {}
.code_block pre code{}
*/
$eTextOne = file_get_contents('demo_code.php');
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
$source  = preg_replace('|\[a=(.+)\](.+)\[\/a\]|','<a class="code_link" href="http://$1">$2</a>',$eTextOneTrans);
?>


<?=$source?>
<!--<pre><code class="xml"></code></pre>-->


</div>
</body>
</html>
