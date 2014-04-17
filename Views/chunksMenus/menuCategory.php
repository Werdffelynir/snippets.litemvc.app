<?php
function actCatMenu($link){
    if(isset(App::$args[0]) AND $link == App::$args[0])
        return "activeCatMenu";
}

?>
        <ul>
            <?php foreach($listMenu as $mItems): ?>

            <li>
                <span class="count"><?php echo $mItems['count'];?></span>
                <?php echo '<a class="'.actCatMenu($mItems['link']).'" href="'.URL.'/category/select/'.$mItems['link'].'"> '.$mItems['title'].' </a>';?></li>

            <?php endforeach; ?>

        </ul>