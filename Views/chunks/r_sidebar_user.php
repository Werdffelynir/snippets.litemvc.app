
<?php if($this->user): ?>

	<h4>Вы авторизированы как <b><?php echo $this->user['name']; ?></b></h4>
	<p>
	    <li><a href="<?php App::url()?>/index/logout">Log out</a></li>
	</p>

<?php else: ?>

	<h4>Авторизация</h4>
	<p>
	    <li><a href="<?php App::url()?>/index/login">Login</a></li>
	</p>

<?php endif; ?>
