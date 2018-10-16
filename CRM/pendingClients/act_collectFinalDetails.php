<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-22
 * Time: 10:31 AM
 */



$error = [];
$reporting = [];
$objFormFieldData = new PendingClientData($PDOdb, $PDOdbPending);

if ( ! empty($_POST['inputFormSubmitted']) ) {
    $reporting[] = 'Processing Pending Client';
    // process pending submission - push to checkAddress
    $inputFormFields = $objFormFieldData->processFormSubmission(SECTION_OFFICE_INPUTS, $_POST);

    $reporting[] = 'Validate inputs from form';
    $validator = new Validate_fields();
    $validator = $objFormFieldData->validateFormInputs(SECTION_OFFICE_INPUTS, $validator, $inputFormFields); // section 1 -> client inputs for site and contacts

    if ( ! $validator->validation()) {
        $error[] = $validator->create_msg();
    }
    // Conditional validations
    $domainValidate = new Validate_fields();
    if ( ! empty($inputFormFields['isCorpDomain']) ) {
        $testEmail = 'test@'. $inputFormFields['CorpDomain'];
        $domainValidate->add_text_field('Corp Domain', $testEmail, 'email', 'y'); //testing the corporate domain email
    } else {
        if ( ! empty($inputFormFields['CorpDomain']) ) $error[] = 'You must check &apos;<b>Site Has Domain</b>&apos; to approve this domain.';
    }
    if ( ! empty($domainValidate) && ! $domainValidate->validation() ) {
        $error[] = 'The field <b>Corp Domain</b> is invalid';
    }

    if (isset($inputFormFields['PaidNumUsers'])){
        if ($inputFormFields['PaidNumUsers']>$inputFormFields['MaxNumUsers']){
            $error[] = 'The field <b>Max Number of Users</b> Allowed cannot be less than Subscribed';
        }
        if ($inputFormFields['PaidNumContacts']>$inputFormFields['MaxNumContacts']){
            $error[] = 'The field <b>Max Number of Tenant Contacts</b> Allowed cannot be less than Subscribed';
        }
    }

    $redirect = "List";
    $redirectToNext = false;
    if ( ! empty($_POST['publishPendingClient']) ) {
        $inputFormFields['Status'] = 'published';
        $reporting[] = 'Update Status to "Published"';
        $redirect = "Publish Site";
        $redirectToNext = true;
    }

    if (empty($error)) {
        $reporting[] = 'Update record in table with entered data';
        $objFormFieldData->updateRecord($inputFormFields, SECTION_OFFICE_INPUTS);

        $reporting[] = 'Redirect to '.$redirect.' page';
        if ($redirectToNext) {
            session_write_close();
            header('Location: '. APP_URL . $XFA['continue'] .'&c='.$inputFormFields['PkID']);
        } else {
            header('Location: '. APP_URL . $XFA['return'] .'&c='.$inputFormFields['PkID']);
        }
        exit();
    }

    // re-populate fields from DB table, preserve inputs
    $tableFieldValues = $objFormFieldData->getRecordBy_ID($inputFormFields['PkID']);
    $tableFieldValues = $objFormFieldData->processFormSubmission(SECTION_OFFICE_INPUTS, $tableFieldValues);
    $inputFormFields = array_merge($tableFieldValues, $inputFormFields);

} else {
    // validate pendingClient ID
    $reporting[] = 'Fallthrough, no form submission';
    $reporting[] = 'Pull site details from Site table *if* there is a Parent_ID or SiteMatch_ID';

    // validate pendingClient ID
    if (empty($_GET['c'])) {
        session_write_close();
        trigger_error('Missing pending client ID', E_USER_NOTICE);
        header('Location: '. APP_URL . $XFA['return']);
        exit();
    }

    $client = $_GET['c'];

    // get pendingClient data from table
    $inputFormFields = $objFormFieldData->getRecordBy_ID($client);

}

// generate data object from record for display
if($inputFormFields['PropertyType']==1){
    $inputFormFields['rcdSite_ID']           = $inputFormFields['SiteMatch_ID'];
}else{
    $inputFormFields['rcdSite_ID']           = 0;
}

$inputFormFields = $objFormFieldData->getBuildingDetailsOfSite($inputFormFields);


$error = implode('<br>', $error);
$strReporting = implode('<br>', $reporting);

