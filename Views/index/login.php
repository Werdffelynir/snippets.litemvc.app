
<form class="login" action="<?php App::url();?>/index/login" method="post">
    <div class="form_login">
        <div>
            <input type="text" name="login" value="<?=$login?>"> <span>Your login</span>
        </div>

        <div>
            <input type="text" name="password" value="<?=$password?>"> <span>Your password</span>
        </div>
            <input type="submit" name="contact_submitted" value="Login">
    </div>
</form>
<script type="text/javascript">
    /*$(document).ready(function(){
        $('.ajaxform').ajaxSlimBox({
            'hideScrollBar': false,
            'boxWin': 'ajaxLogin',
            'form': true
        });
    });*/
</script>