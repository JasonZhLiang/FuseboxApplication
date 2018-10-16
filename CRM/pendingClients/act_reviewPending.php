<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-22
 * Time: 10:37 AM
 */

$reporting = [];
$objFormFieldData = new PendingClientData($PDOdb, $PDOdbPending);

if ( ! empty($_POST['deleteFormSubmitted']) ) {
    //  Process deletion of pending Client

    if (empty($_POST['PkID']) || ! is_numeric($_POST['PkID'])) {
        trigger_error('Missing / Invalid PC_ID', E_USER_ERROR);
    }
    
    if (empty($_POST['v']) || $_POST['v'] != md5(SEED . $_POST['PkID']) ) {
        trigger_error('Missing / Invalid PC_ID', E_USER_ERROR);
    }
    
    $Delete_ID = $_POST['PkID'];
    $Admin_ID  = $_SESSION['ADMIN_USER']['User_ID'];
    
    $sql = "UPDATE pending_client
    SET DeleteFlag = 1, DeleteBy = :DeleteBy, DeleteDate = NOW()
    WHERE PkID = :PkID";

    $PDOdbPending->prepare($sql);
    $PDOdbPending->bind('DeleteBy', $Admin_ID);
    $PDOdbPending->bind('PkID', $Delete_ID);
    $PDOdbPending->execute();

    $reporting[] = 'Deleted Pending Client';
    // redirect to list page
    session_write_close();
    header('Location: '. APP_URL . $XFA['return']);
    exit();

}

if ( ! empty($_POST['pushToCollectDetails']) ) {
    // process pending submission - push to checkAddress
//    $reporting[] = 'Processed Pending Client';

    if (empty($_POST['PkID']) || ! is_numeric($_POST['PkID'])) {
//        trigger_error('Form Field hack attempt', E_USER_ERROR);
    }
    $PkID = $_POST['PkID'];

//    $reporting[] = 'Update Status field to "verification"';
    $sql = "UPDATE pending_client SET Status = 'validation' WHERE PkID = :PkID";

    $PDOdbPending->prepare($sql);
    $PDOdbPending->bind('PkID', $PkID);
    $PDOdbPending->execute();

//    $reporting[] = 'Redirect to Check Address page';
    // redirect to Check Address page
    session_write_close();
    header('Location: '. APP_URL . $XFA['continue'] .'&c='.$PkID);
    exit();

} else {
    // validate pendingClient ID
    if (empty($_GET['c'])) {
        session_write_close();
        header('Location: '. APP_URL . $XFA['return']);
        exit();
    }

    $client = $_GET['c'];
    // get pendingClient data from table
    $inputFormFields = $objFormFieldData->getRecordBy_ID($client);
    // generate data object from record for display
    $reporting[] = 'Fallthrough, no form submission';

}

$strReporting = implode('<br>', $reporting);

