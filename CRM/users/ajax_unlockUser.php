<?php
$result = [];
if (empty($_POST['id']) || ! is_numeric($_POST['id'])) {
    trigger_error('Invalid User ID', E_USER_ERROR);
}
if (empty($_POST['v']) || $_POST['v'] != md5(SEED . $_POST['id'])) {
    trigger_error('Failed validation on User ID Access Log', E_USER_ERROR);
}
if ( ! empty($result) ) {
    trigger_error('Missing Data for unlocking user account');
} else {
    $id      = $_POST['id'];
    $result['success'] = 0;
    $sql = "UPDATE users SET
                  LoginAttempts = 0
              WHERE User_ID = :User_ID
    ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('User_ID', $id);
    $PDOdb->execute();
    $sql = "SELECT isActive FROM users
              WHERE User_ID = :User_ID
    ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('User_ID', $id);
    $PDOdb->execute();
    if ($PDOdb->rowCount() == 1) {
        $row = $PDOdb->getRow();
    }
    if ($row['isActive'] == 1){
        sendUnlockEmail($id);
    }
    $result['success'] = 1;
}
echo(json_encode($result));

function sendUnlockEmail ($id) {
    global $PDOdb;
    $sql = "SELECT LastName, FirstName, Email, Lang FROM users WHERE User_ID = :User_ID";
    $PDOdb->prepare($sql);
    $PDOdb->bind('User_ID', $id);
    $PDOdb->execute();

    if ($PDOdb->rowCount() > 0) {

        $sqlUnlock = "SELECT MsgContent_EN, MsgContent_FR, Subject_EN, Subject_FR, SendFrom, ReplyTo FROM system_emails WHERE MessageCode = 'USER_UNLOCK_ACCOUNT'";
        $PDOdb->prepare($sqlUnlock);
        $PDOdb->execute();
        if ($PDOdb->rowCount() == 0) {
            echo('error-check: system_email database');
        } else {
            $aMsgTemplate = $PDOdb->getRow();
            $PDOdb->prepare($sql);
            $PDOdb->bind('User_ID', $id);
            $PDOdb->execute();
            while ($row = $PDOdb->getRow()) {
                $dataArr = array(
                    'Email'     => $row["Email"],
                    'Subject'   => $aMsgTemplate["Subject" . $row["Lang"]],
                    'Body'      => $aMsgTemplate["MsgContent" . $row["Lang"]],
                    'FirstName' => $row["FirstName"],
                    'LastName'  => $row["LastName"],
                    'ReplyTo'   => $aMsgTemplate['ReplyTo'],
                    'SendFrom'  => $aMsgTemplate['SendFrom']
                );
                sendEmail($dataArr);
            }
        }
    }
}

function sendEmail($dataArr=[]){
    global $Utils, $EmailService;
    $FirstName 		= empty($dataArr["FirstName"])	? "": $dataArr["FirstName"];
    $LastName 		= empty($dataArr["LastName"]) 	? "": $dataArr["LastName"];
    $body			= $dataArr['Body'];
    $body = str_replace('{SHOW_PERSONAL_SALUTATION}', $Utils->formatName('', $FirstName, $LastName) , $body);
    $EmailService->sendPhpMailier($dataArr['Email'], $dataArr['Subject'], $mailerTEXT='', $mailerHTML = $body, $myAttachments='', $dataArr['SendFrom'], $dataArr['ReplyTo'], $mailerCC='', $mailerBCC='', $blnSendTracking=false);
}