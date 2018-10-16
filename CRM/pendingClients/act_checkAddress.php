<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-22
 * Time: 10:34 AM
 */


$error              = [];
$reporting          = [];
$disableContinue    = '';
$objFormFieldData   = new PendingClientData($PDOdb, $PDOdbPending);
$isMatch            = 0;


if ( ! empty($_POST['inputFormSubmitted']) ) {
    $reporting[] = 'Processed Pending Client';
    // process pending submission

    $isPostMatch = $_POST['isMatch'];

    $inputFormFields = $objFormFieldData->processFormSubmission(SECTION_CLIENT_INPUTS, $_POST);

    $validator = new Validate_fields();
    $validator = $objFormFieldData->validateFormInputs(SECTION_CLIENT_INPUTS, $validator, $inputFormFields); // section 1 -> client inputs for site and contacts

    if ( ! $validator->validation()) {
        $error[] = $validator->create_msg();
    }

    if ( isset($inputFormFields['PriceLevel']) && ! is_numeric($inputFormFields['PriceLevel']) ) {
        $error[] = 'You need to make a valid <b>Price Level</b> selection for the product you are interested in.';
    }

    $reporting[] = 'Validate inputs from form';

    // BEGIN Site Match Check
    $siteRecord  = $objFormFieldData->checkAddressAgainstSites($inputFormFields);

    if ($isPostMatch && empty($siteRecord)) {
        $reporting[] = 'Found a previous match, preventing saving and navigating away.';
        $error[] = 'You have made a change to the pending client that invalidated a previous site match. If this is correct, click Save or Save/Continue';
    }

    $arrSite_ID  = $objFormFieldData->checkExistingBaseSite($siteRecord, $inputFormFields);

    // Get record from DB, compare Office Only fields to potential sitematch
    $tableFieldValues = $objFormFieldData->getRecordBy_ID($inputFormFields['PkID']);

    $error       = $objFormFieldData->checkForProblemSite($siteRecord, $tableFieldValues, $error);
    $inputFormFields[$arrSite_ID['fieldName']] = $arrSite_ID['value'];
    // END Site Match Check

    $redirect    = "List";
    $reporting[] = 'Check address against existing sites';

    $redirectToNext = false;
    if ( ! empty($_POST['pushToCollectDetails']) ) {
        $inputFormFields['Status'] = 'review';
        $reporting[] = 'Update Status to "Review"';
        $redirect = "Collect Building Details";
        $redirectToNext = true;
    }

    $return = true;
    if ( ! empty($_POST['recheck']) ) {
        $reporting[] = 'Check address details, return to form';
        $return = false;
    }

    if (empty($error)) {
        $Admin_ID = $_SESSION['ADMIN_USER']['User_ID'];
        $datetime = date('Y-m-d H:i:s');

        $inputFormFields['ReviewDate'] = $datetime;
        $inputFormFields['ReviewBy']   = $Admin_ID;
//        echo('<pre>'.print_r($inputFormFields, 1).'<pre>');
//        echo('<pre>'..'</pre>');
        $reporting[] = 'Update record in table with entered data';
        $objFormFieldData->updateRecord($inputFormFields, SECTION_CLIENT_INPUTS);

        session_write_close();
        if ($redirectToNext) {
            $reporting[] = 'Redirect to '.$redirect.' page';
            header('Location: '. APP_URL . $XFA['continue'] .'&c='.$inputFormFields['PkID']);
            exit();
        } elseif ($return) {
            $reporting[] = 'Redirect to '.$redirect.' page';
            header('Location: '. APP_URL . $XFA['return']);
            exit();
        }
        $reporting[] = 'Fallthrough, show display page';
    }
} else {
    // validate pendingClient ID
    if (empty($_GET['c'])) {
        session_write_close();
        trigger_error('Missing pending client ID', E_USER_NOTICE);
        header('Location: '. APP_URL . $XFA['return']);
        exit();
    }

    $client = $_GET['c'];
    // get pendingClient data from table
    // generate data object from record for display
    $inputFormFields = $objFormFieldData->getRecordBy_ID($client);
    $siteRecord      = $objFormFieldData->checkAddressAgainstSites($inputFormFields);

    $error       = $objFormFieldData->checkForProblemSite($siteRecord, $inputFormFields, $error);
    if ( ! empty($siteRecord) && empty($error) ) {
        // set sitematch/parent id to Site_ID of resulting record
        $arrSite_ID                                 = $objFormFieldData->checkExistingBaseSite($siteRecord, $inputFormFields);
        $inputFormFields[$arrSite_ID['fieldName']]  = $arrSite_ID['value'];

        $objFormFieldData->updateRecord($inputFormFields, SECTION_CLIENT_INPUTS);
    }

    $reporting[] = 'Fallthrough, no form submission';

}

if($inputFormFields['PropertyType']==1){
    $inputFormFields['rcdSite_ID']           = $inputFormFields['SiteMatch_ID'];
}else{
    $inputFormFields['rcdSite_ID']           = $inputFormFields['Parent_ID'];
}

//echo('<pre>'. print_r($inputFormFields, 1) .'</pre>');

if ( ! empty($inputFormFields['rcdSite_ID']) ) {
    $isMatch = 1;
}

$inputFormFields = $objFormFieldData->getBuildingDetailsOfSite($inputFormFields);

if ( ! empty($inputFormFields['siteData']) && empty($inputFormFields['siteData']['isPlaceholder']) && ($inputFormFields['siteData'] == 1) ) {
    $disableContinue = 'disabled';
}

$error = implode('<br>', $error);
$strReporting = implode('<br>', $reporting);

