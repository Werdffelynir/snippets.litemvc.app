<?php

$data['id'] = (!empty($data['id']))?$data['id']:'';
$data['title'] = (!empty($data['title']))?$data['title']:'';
$data['link'] = (!empty($data['link']))?$data['link']:'';
$data['text'] = (!empty($data['text']))?$data['text']:"[h][/h]\n\n[code=php][/code]";
$data['link_full'] = (!empty($data['link_full']))?$data['link_full']:'';
$data['sub_category'] = (!empty($data['sub_category']))?$data['sub_category']:'';
$typeRecord = (!empty($typeRecord))?$typeRecord:'';
$category = $this->listCategory;
?>
<form class="editSnippet" action="<?=URL.'/edit/saveSnippet'?>" method="POST">
    <input type="submit" value="Сохраниеть" />
    <span class="btn"> <a href="#" onclick="$('.shortCode').slideToggle();">Опции</a> </span>
    <div class="shortCode clear full">
        <div class="lite_4 first">

            <span class="snippetItem">
                [h][/h]<br/>
                [a=site.com][/a]<br/>
                [meta=keys][/meta]<br/>
                [!][/!]<br/>
                [code][/code]<br/>
                [code=php][/code]<br/>
                [br]<br/>
                [warning][/warning]<br/>
                [notice][/notice]
            </span><br/>

        </div>
        <div class="lite_8">

            <input name="title" type="text" value="<?=$data['title']?>" required placeholder="Название сниппета" />


            <select class="category" name="id_category">
                <?php foreach($category as $cat): ?>
                <option <?=($this->urlArgs(1)==toLower($cat['title']))?'selected="selected"':''?> value="<?=$cat['id']?>"><?=toUpper($cat['title'])?></option>
                <?php endforeach; ?>
                <option value="newCategory">Новая категория</option>
            </select>

            <span class="new_category"></span>

            <select class="sel_sub_cat" name="id_sub_category">
                <?=$data['sub_category']?>
            </select>

            <span class="new_sub_category"></span>

        </div>
    </div>



    <textarea spellcheck="false" name="text" ><?php echo htmlspecialchars($data['text']); ?></textarea>

    <br/>
    <input type="text" hidden="hidden" name="id" value="<?=$data['id']?>" />
    <input type="text" hidden="hidden" name="type_record" value="<?=(isset($typeRecord))?$typeRecord:''?>" />
    <input type="text" hidden="hidden" name="link_full" value="<?=$data['link_full']?>" />
    <input type="submit" value="Сохраниеть" />
</form>

<script type="application/javascript">
    $('.category').change(function(){
        var url = '<?=URL?>';
        var value = $(this).val();

        if(value=='newCategory'){
            $('.new_category').html('<input type="text" name="new_category" value="" required placeholder="Новая категория" />');
            $('.sel_sub_cat').css('display','none');
            $('.new_sub_category').html('<input type="text" name="new_sub_category" value="" required placeholder="Новая суб категория" />');
        }else{
            $('.new_category').html('');
            $('.sel_sub_cat').css('display','block');
            $('.new_sub_category').html('');
            $.ajax({
                type: 'POST',
                url: url+'/edit/getCategory',
                data: 'sub_category='+value,
                success: function(html){
                    $('.sel_sub_cat').html(html);
                }
            });
        }
    });
    $('.sel_sub_cat').change(function(){
        var url = '<?=URL?>';
        var value = $(this).val();
        console.log(value);
        if(value=='newSubCategory'){
            $('.new_sub_category').html('<input type="text" name="new_sub_category" value="" required placeholder="Новая суб категория" />');
        }else{
            $('.new_sub_category').html('');
        }
    });
</script>