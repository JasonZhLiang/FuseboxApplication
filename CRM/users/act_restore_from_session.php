<?php 

if ( ! empty($_SESSION['ADMIN_USER']['qs']['contacts.list'])) {
	//	restore the post that we stashed earlier ... & clear it out again.
	$_POST = $_SESSION['ADMIN_USER']['qs']['contacts.list']; 
	unset($_SESSION['ADMIN_USER']['qs']['contacts.list']);
}


?>