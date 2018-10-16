<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-04-04
 * Time: 3:56 PM
 */

$objFormFieldData = new PendingClientData($PDOdb, $PDOdbPending);

$status = ['success' => false];

$match    = $_POST['match'];
//$arrLog_ID  = $_POST['id'];

if (empty($_POST['client']) || empty($_POST['v'])) {
    trigger_error('No Client Record provided', E_USER_ERROR);
}

if ( ! is_numeric($_POST['client']) || $_POST['v'] != md5(SEED . $_POST['client'])) {
    trigger_error('Failed validation check', E_USER_ERROR);
}

$client_ID = $_POST['client'];

if (is_array($match)) {
    $datetime = date('Y-m-d H:i:s');
    $Admin_ID = $_SESSION['ADMIN_USER']['User_ID'];
    // TODO: validation of inputs

    $inputFormFields = [
        'PkID'          => $client_ID,
        'SiteAddress_2' => $match['SiteAddress_2'],
        'SiteAddress_1' => $match['SiteAddress_1'],
        'SiteCity'      => $match['SiteCity'],
        'SiteProvince'  => $match['SiteProvince'],
        'SitePcode'     => $match['SitePostalCode'],
        'Status'        => 'verification',
        'CP_isValidated'=> $match['CP_isValidated'],
        'CP_Search_ID_1'=> $match['logID_1'],
        'CP_Search_ID_2'=> $match['logID_2']
    ];
    if ($match['CP_isValidated'] == 1) {
        $inputFormFields['CP_ValidatedDate'] = $datetime;
        $inputFormFields['CP_ValidatedBy'] = $Admin_ID;
    } else {
        $inputFormFields['CP_ValidateAbortDate'] = $datetime;
        $inputFormFields['CP_ValidateAbortBy'] = $Admin_ID;
    }


    $validator = $objFormFieldData->validateFormInputs(1, new Validate_fields(), $inputFormFields);

    if ( ! $validator->validation() ) {
        $status['message'] = $validator->create_msg();
    } else {
        $objFormFieldData->updateRecord($inputFormFields, SECTION_ADMIN_FIELDS);
        $status['success'] = true;
    }

}

echo(json_encode($status));

session_write_close();
exit();