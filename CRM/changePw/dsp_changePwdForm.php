<?php
////////////////////////////////////////////////////////////
// File: dsp_changePwdForm.php
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
    'user_ID'   => $_SESSION['ADMIN_USER']['User_ID'],
    'user_VID'  => '',
    'fullName'  => '',
    'error'     => '',
];

$UserObj->processChangePasswordForForm($_POST, $aForm);

?>
<div class="container">
    <?php $Components->passwordUpdateForm($aForm) ?>
</div>
