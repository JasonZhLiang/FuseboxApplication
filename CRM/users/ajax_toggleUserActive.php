<?php
////////////////////////////////////////////////////////////
// File: ajax_toggleUserActive.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2016-06-01
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in PhpStorm
//		
//
////////////////////////////////////////////////////////////
// TODO needed validation
// Check inputs

$result = [];

if ( ! isset($_GET['user']) ) {
    $result['success'] = 0;
    $result['msg'] = 'Missing User';
}

if ( ! isset($_GET['state']) ) {
    $result['success'] = 0;
    $result['msg'] = 'Missing User';
}

if ( ! empty($result) ) {
    trigger_error('Missing Data for toggling FR User Active state');
} else {
    $state      = $_GET['state'];
    $User_ID    = $_GET['user'];

    $result['success'] = 0;

    switch ($state) {
        case 0:
            $result['state'] = 1;
            break;

        case 1:
            $result['state'] = 0;
            break;
    }

    $sql = "UPDATE users SET
                  isActive = :isActive
              WHERE User_ID = :User_ID
    ";

    $PDOdb->prepare($sql);
    $PDOdb->bind('isActive', $result['state']);
    $PDOdb->bind('User_ID', $User_ID);
    $PDOdb->execute();

    $result['success'] = 1;
}


echo(json_encode($result));