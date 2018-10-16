<?php
////////////////////////////////////////////////////////////
// File: dsp_confirmPwd.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2013-02-14
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		
//
////////////////////////////////////////////////////////////

$aForm = [
        'curUser_ID'    => $_SESSION['ADMIN_USER']['User_ID'],
        'user_ID'       => '',
        'user_VID'      => '',
        'fullName'      => $_SESSION['ADMIN_USER']['FirstName'].' '.$_SESSION['ADMIN_USER']['LastName'],
        'error'         => '',
];

$UserObj->processConfirmCurrentPwd($_POST, $aForm);

?>

<div class="container">
    <?php $Components->confirmCurrentPasswordForm($aForm); ?>
</div>
