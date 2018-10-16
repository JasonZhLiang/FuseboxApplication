<?php
////////////////////////////////////////////////////////////
// File: dsp_edit.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2017-08-24
//		Author		- HO
//		Version	    - 3.0
//
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm by Author TBS on 2013-10-25
//		- v2.0 A multitude of changes have been made - now renders fields for camera integration
//		- v3.0 A multitude of new changes and a significant feature: {describe here...}
//
//
////////////////////////////////////////////////////////////?>


<div class="widget-box transparent">

    <h3 class="header smaller lighter blue"><?php $trans->et('Add / Edit Camera') ?></h3>

    <?php if (!empty($error)) { ?>
        <div class="row">
            <div class="col-sm-offset-3 col-xs-6">
                <div class="alert alert-danger">
                    <button class="close" data-dismiss="alert" type="button">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <div class="text-center">
                        <strong>
                            <i class="ace-icon fa fa-times"></i>
                            <?php $trans->et('Error') ?>!
                        </strong><br>
                        <?php echo($error); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <form action="<?php echo(APP_URL . $XFA['process'] . "&sid=" . $curSite . "&vid=" . md5(SEED . $curSite)); ?>"
          method="post" class="form-horizontal">
        <input type="hidden" name="camInputFormSubmitted" value="TRUE"/>
        <input type="hidden" name="PkID" value="<?php echo(htmlspecialchars($camInputFormFields["PkID"])); ?>">
        <input type="hidden" name="order" value="<?php echo(htmlspecialchars($camInputFormFields["SortOrder"])); ?>">
        <input type="hidden" name="prevGroup"
               value="<?php echo(htmlspecialchars($camInputFormFields["prevGroup"])); ?>">
        <?php
        $objCamRenderer = New CameraListRenderer($Components, $trans, $XFA, $userType);
        $objCamRenderer->renderCameraInputFields($camInputFormFields, $HTMLreq, $areGroups, $optCamGroups);
        ?>
        <div class="row">
            <div class="col-sm-12 text-center">
                <a href="#" id="ping" class="btn btn-xs btn-info"><?php $trans->et('Ping Camera') ?></a>
                <a href="#" id="ping-dev-info" class="btn btn-xs btn-info"><?php $trans->et('Ping and Get Info') ?></a>
                <a href="#" id="snapshot-url" class="btn btn-xs btn-info"><?php $trans->et('Get Snapshot Url') ?></a>
                <input type="submit" class="btn btn-xs btn-info" name="saveChanges" value="Save Changes">
                <a href="<?php echo(APP_URL . $XFA["return"] . "&sid=" . $curSite . "&vid=" . md5(SEED . $curSite)); ?>"
                   class="btn btn-xs"><?php $trans->et('Cancel') ?></a>
            </div>
        </div>
    </form>
</div>

<style>
    .onvif-data{
        color:#ff0000;
        margin-top: -10px;
        cursor:pointer;
        text-align: left;
    }
</style>

<script>
    $( document ).ready(function() {
        $("#ping").on('click', function(e){
            e.stopPropagation();
            e.preventDefault();

            var action = 'ping';
            var camScheme = $("#CamScheme").val();
            var camHost = $("#CamHost").val();
            var camPort = parseInt($("#CamPort").val());
            var camUser = '';
            var camPass = '';
            assemblePkg(action, camScheme, camHost, camPort, camUser, camPass);
        });
        $("#ping-dev-info").on('click', function(e){
            e.stopPropagation();
            e.preventDefault();

            var action = 'pingDevInfo';
            var camScheme = $("#CamScheme").val();
            var camHost = $("#CamHost").val();
            var camPort = parseInt($("#CamPort").val());
            var camUser = $("#CamUser").val();
            var camPass = $("#CamPass").val();
            assemblePkg(action, camScheme, camHost, camPort, camUser, camPass);
        });
        $("#snapshot-url").on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
            var action = 'getSnapshotUrl';
            var camScheme = $("#CamScheme").val();
            var camHost = $("#CamHost").val();
            var camPort = parseInt($("#CamPort").val());
            var camUser = $("#CamUser").val();
            var camPass = $("#CamPass").val();
            assemblePkg(action, camScheme, camHost, camPort, camUser, camPass);
        });

        assemblePkg = function (action, camScheme, camHost, camPort, camUser, camPass){
            var item = {};
            item = {
                'action' : action,
                'camScheme' : camScheme,
                'camHost' : camHost,
                'camPort' : camPort,
                'camUser' : camUser,
                'camPass' : camPass
            };
//            console.log('packaged data:', item);
            fireAjaxRequest(item);
        };

        fireAjaxRequest = function(item){
            $.ajax({
                //cache: false,
                url: "<?php echo(APP_URL . $XFA["pingCam"]) ?>",
                //contentType: "application/json",
                //type: "POST",
                //processData: false,
                dataType: 'json',
                data: item,
                //data        : {match: sortedCamera}, works
                method: 'post',
                //data: JSON.stringify(sortedCamera),
                //data: sortedCamera,
                success: function (data) {
                    $("#place-holder").addClass('hide');

                    if (data['action'] == 'ping') {
                        $("#Firmware-Onvif").addClass('hide');
                        $("#Model-Onvif").addClass('hide');
                        $("#Make-Onvif").addClass('hide');
                        $("#SerialNumber-Onvif").addClass('hide');
                        $("#HardwareID-Onvif").addClass('hide');
                        $("#Query-Onvif").addClass('hide');
                        $("#Path-Onvif").addClass('hide');
                        $("#InternalHost-Onvif").addClass('hide');
                        $("#InternalPort-Onvif").addClass('hide');
                        if (data['status'] == 'fail01') {
                            $(".status-ping > h5 > i").text("Communication Error - camera 'ping' unsuccessful:");
                            $(".status-ping-info").text(data['response']);
                            $(".status-ping > h5 > i").css('color', '#ff0000');
                            $(".status-ping").removeClass("hide");

//                            window.alert("Communication Error - Could not 'ping' camera:\n\n\n" + data['response']);
                        } else if (data['status'] == 'success') {
                            $(".status-ping > h5 > i").text("Camera 'Ping' Successful!");
                            $(".status-ping > h5 > i").css('color', '#008000');
                            var dateTimeRes = data['response']['pingDT']['result'];
                            var camDateTime = 'Date: ' + dateTimeRes['Date']['Year'] + '/' + dateTimeRes['Date']['Month']
                                + '/' + dateTimeRes['Date']['Day'] + '   Time: ' + dateTimeRes['Time']['Hour'] + ':' +
                                dateTimeRes['Time']['Minute'] + ':' + dateTimeRes['Time']['Second'];
                            $(".status-ping-info").text("\nCamera: " + camDateTime);
                            $(".status-ping").removeClass("hide");
//                            console.log('hey.............  ', camDateTime);
//                            window.alert("Camera 'ping' successful!\n\n\n" + data['response']);
                        }
                    } else if (data['action'] == 'pingDevInfo') {
                        console.log('full returned data set', data);
                        $("#Query-Onvif").addClass('hide');
                        $("#Path-Onvif").addClass('hide');
                        $("#InternalHost-Onvif").addClass('hide');
                        $("#InternalPort-Onvif").addClass('hide');
                        if (data['status'] == 'fail01') {
                            $("#Firmware-Onvif").addClass('hide');
                            $("#Model-Onvif").addClass('hide');
                            $("#Make-Onvif").addClass('hide');
                            $("#SerialNumber-Onvif").addClass('hide');
                            $("#HardwareID-Onvif").addClass('hide');
                            $(".status-ping > h5 > i").text("Communication Error - camera 'ping' unsuccessful:");
                            $(".status-ping-info").text(data['response']);
                            $(".status-ping > h5 > i").css('color', '#ff0000');
                            $(".status-ping").removeClass("hide");
//                            window.alert("Communication Error - Could not 'ping' camera:\n\n\n" + data['response']);
                        } else if (data['status'] == 'fail02') {
                            $("#Firmware-Onvif").addClass('hide');
                            $("#Model-Onvif").addClass('hide');
                            $("#Make-Onvif").addClass('hide');
                            $("#SerialNumber-Onvif").addClass('hide');
                            $("#HardwareID-Onvif").addClass('hide');
                            $(".status-ping > h5 > i").text("Communication Error:");
                            $(".status-ping-info").text(data['response']);
                            $(".status-ping > h5 > i").css('color', '#ff0000');
                            $(".status-ping").removeClass("hide");
//                            window.alert("Communication Error:\n\n\n" + data['response']);
                        } else if (data['status'] == 'success') {
                            $(".status-ping > h5 > i").text("Onvif Request Successful!");
                            $(".status-ping > h5 > i").css('color', '#008000');
                            $(".status-ping-info").text("\nClick on fetched data to update corresponding fields");
                            $(".status-ping").removeClass("hide");
                            var make =null, model =null, firmwareOnvif = null, serialNumber = null, hardwareID = null;
                            make = data['response']['pingDevInfo']['result']['Manufacturer'];
                            model = data['response']['pingDevInfo']['result']['Model'];
                            firmwareOnvif = data['response']['pingDevInfo']['result']['FirmwareVersion'];
                            serialNumber = data['response']['pingDevInfo']['result']['SerialNumber'];
                            hardwareID = data['response']['pingDevInfo']['result']['HardwareId'];

                            checkColor();
                            function checkColor() {
                                if ($('#Make').val() != make) {
                                    $("#Make-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#Make-Onvif-Data").css('color', '#008000');
                                }
                                if ($('#Model').val() != model) {
                                    $("#Model-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#Model-Onvif-Data").css('color', '#008000');
                                }
                                if ($('#Firmware').val() != firmwareOnvif) {
                                    $("#Firmware-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#Firmware-Onvif-Data").css('color', '#008000');
                                }
                                if ($('#SerialNumber').val() != serialNumber) {
                                    $("#SerialNumber-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#SerialNumber-Onvif-Data").css('color', '#008000');
                                }
                                if ($('#HardwareID').val() != hardwareID) {
                                    $("#HardwareID-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#HardwareID-Onvif-Data").css('color', '#008000');
                                }
                            }

                            $("#Make-Onvif-Data").text('Onvif Make: ' + make);
                            $("#Make-Onvif").removeClass('hide');
                            $("#Model-Onvif-Data").text('Onvif Model: ' + model);
                            $("#Model-Onvif").removeClass('hide');
                            $("#Firmware-Onvif-Data").text('Onvif Firmware Version: ' + firmwareOnvif);
                            $("#Firmware-Onvif").removeClass('hide');
                            $("#SerialNumber-Onvif-Data").text('Onvif Serial Number: ' + serialNumber);
                            $("#SerialNumber-Onvif").removeClass('hide');
                            $("#HardwareID-Onvif-Data").text('Onvif Hardware ID: ' + hardwareID);
                            $("#HardwareID-Onvif").removeClass('hide');

                            $("#Make-Onvif-Data").on('click', function () {
                                $('#Make').val(make);
                                checkColor();
                            });
                            $("#Model-Onvif-Data").on('click', function () {
                                $('#Model').val(model);
                                checkColor();
                            });
                            $("#Firmware-Onvif-Data").on('click', function () {
                                $('#Firmware').val(firmwareOnvif);
                                checkColor();
                            });
                            $("#SerialNumber-Onvif-Data").on('click', function () {
                                $('#SerialNumber').val(serialNumber);
                                checkColor();
                            });
                            $("#HardwareID-Onvif-Data").on('click', function () {
                                $('#HardwareID').val(hardwareID);
                                checkColor();
                            });

                            $("input").on("change", function () {
                                    checkColor();
                                }
                            );
                        } else {
                            //todo: handle other types of errors
                        }
                    }
                    else if (data['action'] == 'getSnapshotUrl') {
                        $("#Firmware-Onvif").addClass('hide');
                        $("#Model-Onvif").addClass('hide');
                        $("#Make-Onvif").addClass('hide');
                        $("#SerialNumber-Onvif").addClass('hide');
                        $("#HardwareID-Onvif").addClass('hide');
                        console.log('full data set: ', data);
                        if (data['status'] == 'Error_Fatal') {
                            $("#Query-Onvif").addClass('hide');
                            $("#Path-Onvif").addClass('hide');
                            $("#InternalHost-Onvif").addClass('hide');
                            $("#InternalPort-Onvif").addClass('hide');
                            $(".status-ping > h5 > i").text("Communication Error - onvif request unsuccessful:");
                            $(".status-ping > h5 > i").css('color', '#ff0000');
                            $(".status-ping-info").text("\nAn error was encountered in fetching the snapshot url - try 'Ping/Ping and Get Info' first and check all fields for accuracy");
                            $(".status-ping").removeClass("hide");

                        }else if (data['status'] == 'success') {
                            $(".status-ping > h5 > i").text("Onvif Request Successful!");
                            $(".status-ping > h5 > i").css('color', '#008000');
                            $(".status-ping-info").text("\nClick on fetched data to update corresponding fields");
                            $(".status-ping").removeClass("hide");
//                            var parsedSnapshotUrl = url
                            var path = null, query = null, scheme = null, host = null, port = null;
                            path = data['response']['snapshotUrl']['result']['path'];
                            scheme = data['response']['snapshotUrl']['result']['sheme'];
                            host = data['response']['snapshotUrl']['result']['host'];

                            if ((typeof data['response']['snapshotUrl']['result']['port'] !== 'undefined') &&
                                (data['response']['snapshotUrl']['result']['port'] != null)){
                                port = data['response']['snapshotUrl']['result']['port'];
                            }else{
                                if (scheme === "http" ){
                                    port = 80;
                                }else{
                                    port = 443;
                                }
                            }
                            if ((typeof data['response']['snapshotUrl']['result']['query'] !== 'undefined') &&
                                (data['response']['snapshotUrl']['result']['query'] != null)){
                                query = data['response']['snapshotUrl']['result']['query'];
                            }else {
                                query = false;
                            }

                            checkColor();
                            function checkColor() {
                                if ($('#CamPath').val() != path) {
                                    $("#Path-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#Path-Onvif-Data").css('color', '#008000');
                                }
                                if ($('#CamQuery').val() != query) {
                                    $("#Query-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#Query-Onvif-Data").css('color', '#008000');
                                }
                                if ($('#CamInternalHost').val() != host) {
                                    $("#InternalHost-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#InternalHost-Onvif-Data").css('color', '#008000');
                                }
                                if ($('#CamInternalPort').val() != port) {
                                    $("#InternalPort-Onvif-Data").css('color', '#ff0000');
                                } else {
                                    $("#InternalPort-Onvif-Data").css('color', '#008000');
                                }
                            }

                            $("#Path-Onvif-Data").on('click', function () {
                                $('#CamPath').val(path);
                                checkColor();
                            });
                            $("#Query-Onvif-Data").on('click', function () {
                                $('#CamQuery').val(query);
                                checkColor();
                            });

                            $("#InternalHost-Onvif-Data").on('click', function () {
                                $('#CamInternalHost').val(host);
                                checkColor();
                            });
                            $("#InternalPort-Onvif-Data").on('click', function () {
                                $('#CamInternalPort').val(port);
                                checkColor();
                            });
                            $("input").on("change", function () {
                                checkColor();
                            });

                            $("#Path-Onvif-Data").text('Onvif Path: ' + path);
                            $("#Path-Onvif").removeClass('hide');
                            if(!query){
                                $("#Query-Onvif-Data").text('Onvif Query: ' + 'NA');
                                $("#Query-Onvif-Data").off("click");

                            }else{
                                $("#Query-Onvif-Data").text('Onvif Query: ' + query);
                            }
                            $("#Query-Onvif").removeClass('hide');

                            $("#InternalHost-Onvif-Data").text('Onvif Internal Host: ' + host);
                            $("#InternalHost-Onvif").removeClass('hide');

                            $("#InternalPort-Onvif-Data").text('Onvif Internal Port: ' + port);
                            $("#InternalPort-Onvif").removeClass('hide');
                        }
                    }
                },
                error: function() {
                    console.log('failed');
                }
            });
        };
    });
</script>

