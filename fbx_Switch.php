<?php
switch($Fusebox["action"]) {
	default:
			//	2014-07-17 RoK:	for some reason the constant has vanished from the config file ....  
			//	TODO:  review how removing that constant affects other functionality.
			//	- 	the value "/"  will most likely be what I need, since I just want to go to the SITE ROOT .
			$redirectTo = SITE_ROOT ;
			header("Location: ". $redirectTo  ."");
			exit();
		break;
}
?>