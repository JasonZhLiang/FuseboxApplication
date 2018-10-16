<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-08-23
 * Time: 4:09 PM
 */

//trigger_error("contents of post" . print_r($_POST, 1), E_USER_ERROR);
//die('here');

$action = $_POST['action'];
$camScheme = $_POST['camScheme'];
$camHost = $_POST['camHost'];
$camPort = $_POST['camPort'];
$camUser = $_POST['camUser'];
$camPass = $_POST['camPass'];

$camIPAddr = $camScheme . $camHost .':'. $camPort;

//trigger_error("contents of $camIPAddr" . print_r($camIPAddr, 1), E_USER_ERROR);
//die('here');

$pingObj = new ErpOnvif\Onvif($camIPAddr, $camUser, $camPass);

//ping Date/time only
//$pingDT = $pingObj->getDeviceDateTime();
if ($action == 'ping'){
    $pingDT = $pingObj->getDeviceDateTime();
    $response = [
        'pingDT' => json_decode($pingDT,1)
    ];

    if ($response['pingDT']['success']){
        $msg = [
            'action' => 'ping',
            'status'=> 'success',
            'response'=>$response
        ];
    }elseif (!$response['pingDT']['success']){
        $msg = [
            'action' => 'ping',
            'status'=> 'fail01',
            'response'=>$response['pingDT']['result']
        ];
    }else{
        //todo: handle other types of potential errors - can't really see one right now
    }

}else if ($action == 'pingDevInfo') {
    $pingObj->initOnvifServices();
    $pingDT = $pingObj->getDeviceDateTime();
    $pingDevInfo = $pingObj->getDeviceInformation();

    $response = [
        'pingDT' => json_decode($pingDT, 1),
        'pingDevInfo' => json_decode($pingDevInfo, 1)
    ];

    if (($response['pingDT']['success']) && ($response['pingDevInfo']['success'])) {
        $msg = [
            'action' => 'pingDevInfo',
            'status' => 'success',
            'response' => $response
        ];
    } elseif (($response['pingDT']['success']) && (!$response['pingDevInfo']['success'])) {
        $msg = [
            'action' => 'pingDevInfo',
            'status' => 'fail02',
            'response' => $response['pingDevInfo']['result']
        ];
    } elseif (!$response['pingDT']['success']) {
        $msg = [
            'action' => 'pingDevInfo',
            'status' => 'fail01',
            'response' => $response['pingDT']['result']
        ];
    } else {
        //todo: handle other types of potential errors
    }
}else if ($action == 'getSnapshotUrl') {
    $pingObj->initOnvifServices();
    try{
        $devCaps = $pingObj->getDeviceCapabilities(false);
        $devCaps = json_decode($devCaps, 1);

        if (!$devCaps['success']){
            throw new Exception('Communication Error');
        }
        $snapshotUrl = $pingObj->getSnapshotUri();
        $response = [
            'snapshotUrl' => json_decode($snapshotUrl, 1)
        ];
        $msg = [
            'action' => 'getSnapshotUrl',
            'status' => 'success',
            'response' => $response,
        ];
    }catch(Exception $error){
        $msg = [
            'action' => 'getSnapshotUrl',
            'status' => 'Error_Fatal',
        ];
    }
}
echo(json_encode($msg));



//ping Date/time as well as get device info



//echo(json_encode(['status'=>'Okay', 'res'=>$result]));



//echo(json_encode($msg));
//exit;
//echo $result;

