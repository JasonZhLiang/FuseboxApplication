<?php
/* ************Working Test Cases*******************
 *
 *
 * ************Not Working Test Cases***************
 * K1A 0K2
 * On SearchTerm, we get Text = K1A 0K2 and Descrition = Ottawa - 40 Addresses
 * On LastId, we get the following: Ottawa, ON, K1A 0K2 <- working (good)
 *                                  Ottawa, ON <- Will not parse properly (trash)
 *                                  Ottawa, ON, K1A <- will parse but cannot be used to match address fields (trash)
 * Cases such as these must be handled where trash data is skipped over
 *
 * ****************************************/


/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-29
 * Time: 4:58 PM
 */

$objFormFieldData = new PendingClientData($PDOdb, $PDOdbPending);

$client = $_GET['c'];
// get pendingClient data from table
$inputFormFields = $objFormFieldData->getRecordBy_ID($client);
// generate data object from record for display

?>

<style>
    .msg-matches {
        width: 500px;
        height: 150px;
        overflow: auto;
        background-color: #eee;
        margin-bottom: 1em;
        margin-right: 10px;
    }
    .msg-suggestions {
        width: 500px;
        height: 250px;
        overflow: auto;
        background-color: #eee;
        margin-bottom: 1em;
        margin-right: 10px;
    }
    .result {
        line-height: 25px;
    }
</style>
<strong>Pending Clients</strong>
<div class="muted">Accept or Reject Pending Client</div>
<hr noshade size="1">
<div class="margin-bottom-20">
    <a href="<?php echo(APP_URL . $XFA['return']); ?>">
        <span class="ace-icon fa fa-angle-double-left"></span>
        Return
    </a>
</div>
<div class="row">
    <div class="col-xs-offset-3 col-xs-6">
        <div class="row">
            <h4 class="text-center">Validate Address against Canada Post</h4>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-4 col-xs-offset-1">
            <form class="form-horizontal form-font" action="#" method="POST">
                <input type="hidden" name="inputFormSubmitted" value="1">
                <fieldset class="signup-border">

                    <legend><?php $trans->et('Input Data Fields'); ?></legend>

                    <div class="row">
                        <label for="SiteAddress_2"
                               class="control-label"><?php $trans->et('Suite Number'); ?></label>
                        <input type="text" class="form-control" id="SiteAddress_2" name="SiteAddress_2"
                               value="<?php echo $inputFormFields['SiteAddress_2']; ?>">
                    </div>

                    <div class="row">
                        <label for="SiteAddress_1"
                               class="control-label required"><?php $trans->et('Address'); ?></label>
                        <input type="text" class="form-control" id="SiteAddress_1" name="SiteAddress_1"
                               value="<?php echo $inputFormFields['SiteAddress_1']; ?>">
                    </div>


                    <div class="row">
                        <label for="SiteCity"
                               class="control-label required"><?php $trans->et('City'); ?></label>
                        <input type="text" class="form-control" id="SiteCity" name="SiteCity"
                               value="<?php echo $inputFormFields['SiteCity']; ?>">
                    </div>


                    <div class="row">
                        <label for="SiteProvince"
                               class="control-label required"><?php $trans->et('Province / State'); ?></label>
                        <input type="text" class="form-control" id="SiteProvince" name="SiteProvince"
                               value="<?php echo $inputFormFields['SiteProvince']; ?>">
                    </div>

                    <div class="row">
                        <label for="SitePcode"
                               class="control-label required"><?php $trans->et('Postal / Zip Code'); ?></label>
                        <input type="text" class="form-control" id="SitePcode" name="SitePcode"
                               value="<?php echo $inputFormFields['SitePcode']; ?>">
                    </div><br>


                    <div class="row">
                        <div class="hide" id="reRunCityCheck-msg">
                            <p><b>The retrieved city does not match the value in the input field.</b> Please correct spelling
                                if necessary and click on <b>"Re-Run City Check"</b>. If the retrieved city is incorrect,
                                please abort and inform Admin.</p>
                        </div>

                        <div class="hide" id="noCityProvided-msg">
                            <p>Unable to retrieve City at this time, please review fields and continue</p>
                        </div>

                        <div class="col-xs-4 col-xs-offset-4">
                            <button class="btn btn-submit hide"
                                    id="reRunCityCheck-btn"><?php $trans->et('Re-Run City Check'); ?></button>
                        </div>

                        <div class="col-xs-4 col-xs-offset-4">
                            <button class="btn btn-success hide"
                                    id="dispAddress-btn"><?php $trans->et('Display Addresses'); ?></button>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4">
                            <button class="btn btn-submit" id="validatePCode-btn"><?php $trans->et('Validate Postal Code'); ?></button>
                        </div>
                    </div><br><br>
                </fieldset>
            </form>
        </div>
        <div class="col-xs-6 col-xs-offset-1" style="background-color: #ffffff;">
            <fieldset class="signup-border">
                <legend><?php $trans->et('Match(es) Found'); ?></legend>
                <div class=row>
                    <div class="col-xs-6 hide" id="matches">

                    </div>
                </div>
                <div id="message" class="msg-matches">

                    <div class="hide margin-top-5" id="pCodeSuccess">The postal code search was successful, ensure that all address fields are correct and click on <b>Display Addresses</b> to see lists of address suggestions and/or matches.</div>

                    <div class="hide" id="reRunCheck-msg">No matches found, possible options below. Edit fields and try again. Click on <b>Re-Run Address Check</b> to continue</div>
                    <button class="btn btn-danger hide" id="reRunCheck-btn">Re-Run Address Check</button>


                </div>
            </fieldset>

            <fieldset class="signup-border">
                <legend><?php $trans->et('Suggestion(s) Found'); ?></legend>
                <div class=row>
                    <div class="col-xs-6 hide" id="matches">

                    </div>
                </div>
                <div id="messageSuggestions" class="msg-suggestions">
                </div>
            </fieldset>
        </div>
    </div>
</div>

<div class="container">
    <div class="text-center">
        <a href="<?php echo(APP_URL . $XFA['return']); ?>" class="btn btn-sm btn-default">
            <span class="ace-icon fa fa-undo"></span>
            Return
        </a>
        <button class="btn btn-sm btn-warning js_abortAndSave" title="save current work and continue">
            Skip Validation and Continue
            <span class="ace-icon fa fa-chevron-right"></span>
        </button>
    </div>
</div>
<!-- Dialogs -->
<div id="dialog-confirm" class="hide">
    <p>An error has occurred</p>
    <p id="dialog-confirm-msg"></p>
</div>


<script>
    $(function () {

        console.log('dom loaded');
        var
            Key = '<?php echo(CANADA_POST_API_KEY) ?>',
            SearchTerm = '',
            LastId = '',
            SearchFor = 'Everything',
            Country = 'CAN',
            LanguagePreference = 'EN',
            MaxSuggestions = 7,
            MaxResults = 100,
            parsed1stAddressData    = [],
            rawData2ndAddressQry,
            parsed2ndAddressData    = [],
            addressMatchArray       = [],
            count                   = 0,
            logIDs                  = [];

        // set up handelers
        $("#validatePCode-btn").on('click', handleValidateCode);
        $("#dispAddress-btn").on('click',handleDisplayAddress);
        $("#reRunCityCheck-btn").on('click', reRunCityCheck);
        $("#reRunCheck-btn").on('click', reRunAddressCheck);
        $('.js_abortAndSave').on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();

            var logIDobj = [];
            logIDobj['logID_1'] = '';
            logIDobj['logID_2'] = '';

            if (logIDs.length > 0) {
                $.each(logIDs, function (index, value) {
                    logIDobj['logID_'+(index+1)] = value;
                });
            }

            var formFields = gatherFieldValues();
            formFields['logID_1']               = logIDobj['logID_1'];
            formFields['logID_2']               = logIDobj['logID_2'];
            formFields['CP_isValidated']        = 0;

            saveInputsIntoDatabase(formFields);
        });

         function handleValidateCode(evt){
            parsed1stAddressData    = [];
            $(".result").remove();
            SearchTerm = $("#SitePcode").val();
            var callback = initialQueryLengthCheck;
            AddressComplete_Interactive_Find_v2_10(callback, Key, SearchTerm, LastId, SearchFor, Country, LanguagePreference, MaxSuggestions, MaxResults);
            return false; // do not submit the form
        }

        function handleDisplayAddress(evt){
            $("#pCodeSuccess").addClass("hide");
            $("#noCityProvided-msg").addClass("hide");
            $("#dispAddress-btn").addClass("disabled");
            parsed1stAddressData.forEach(function (item){
                LastId = (item['Id']);
            });
            var callback = parse2ndAddressQry;
            rawData2ndAddressQry = AddressComplete_Interactive_Find_v2_10(callback, Key, SearchTerm, LastId, SearchFor, Country, LanguagePreference, MaxSuggestions, MaxResults);
            return false; // do not submit the form
        }

        function AddressComplete_Interactive_Find_v2_10(callback, Key, SearchTerm, LastId, SearchFor, Country, LanguagePreference, MaxSuggestions, MaxResults) {
            // get 'SearchTerm' from form input box;

            console.log('Call AddressComplete_Interactive_Find_v2_10');
                $.getJSON("https://ws1.postescanada-canadapost.ca/AddressComplete/Interactive/Find/v2.10/json3.ws?callback=?",
                {
                    Key: Key,
                    SearchTerm: SearchTerm,
                    LastId: LastId,
                    SearchFor: SearchFor,
                    Country: Country,
                    LanguagePreference: LanguagePreference,
                    MaxSuggestions: MaxSuggestions,
                    MaxResults: MaxResults
                },
                function (data) {
                    console.log('return', data);
                    if (data.Items.length == 1 && typeof(data.Items[0].Error) != "undefined") {
                        alert(data.Items[0].Description);
                    } else {
                        if (data.Items.length == 0) {
                            alert("Sorry, there were no results");
                        }else{
                            callback(data);
                        }
                    }
                });
        }

        function initialQueryLengthCheck(obj){
            if ((obj.Items.length == 1)) {  //Note: the retrieved postal code will not generally equal the fieldInputPostalCode because the Canada Post API outputs a reformated postal code
                parseInitialAddressQry(obj, true);
            }else{
                parseInitialAddressQry(obj, false);
            }
        }

        function parseInitialAddressQry(obj, singleItem) {
            console.log('parseInitialAddressQry with: ', obj);
            var dataObj, retrieveDesc, parse1, parse2, parse3, retrieveCity, retrieveNumAddresses, incorrectCodeWarning;

            if (singleItem) {    <?php //This is the case when only 1 matching result is found ?>
                obj.Items.forEach(function (item) {
                    // init structure
                    dataObj = {
                        'Id': item['Id'],
                        'Description': item['Description'],
                        'Text': item['Text'],
                        'NoOfAddresses': '',
                        'City': '',
                        'cityFound': false
                    };
                    retrieveDesc = item['Description'];
                    parse1 = retrieveDesc.split('-');
                    parse2 = parse1[0].split(',');
                    if (!parse1[1]) {//failed to parse on hyphen in order to retrieve city - test case Stafford Center
                        console.log('Unsuccessful parse on hyphen....................................', parse1);
                        // nothing to do
                    }else if ((!parse2[1])){//failed to parse on comma in order to retrieve city
                        console.log('Unsuccessful parse on comma....................................', parse2);
                    }else{
                        retrieveCity = parse2[1].trim();
                        parse3 = parse1[1].split(' '); // on space
                        parse3 = parse3[0].trim();
                        retrieveNumAddresses = parse3;
                        dataObj['NoOfAddresses'] = retrieveNumAddresses;
                        dataObj['City']          = retrieveCity;
                        dataObj['cityFound']     = true;
                    }

                    parsed1stAddressData.push(dataObj);
                    logIDs.push(item['Id']);
                });
                ValidateCity(parsed1stAddressData);
                renderAddressQry(parsed1stAddressData);

            } else {    <?php //This is the case when the postal code is entered wrong and more than one suggestion is returned ?>
                obj.Items.forEach(function (item) {
                    parsed1stAddressData.push({
                        'cityFound': true,
                        'Id': item['Id'],
                        'Description': item['Description'],
                        'Text': item['Text']
                    });

                });
                incorrectCodeWarning = '<div class = "result">' + '<p><b>No retrievable addresses found</b> for this postal code - see below suggestions</p></div>';
                renderAddressQry(parsed1stAddressData);
                $(incorrectCodeWarning).prependTo("#message");
            }
        }

        function renderAddressQry(parsed1stAddressData) {
            console.log('renderAddressQry');
            var cityInData, html = "";
            parsed1stAddressData.forEach(function (item) {

                html += '<div class ="result">' + "<strong>" + item['Description'] + ",</strong> " + item['Text'];
                html += "</div>";
                cityInData = item['cityFound'];
            });
            $(html).prependTo("#message");
            if (cityInData ==false) {
                $("#validatePCode-btn").addClass("hide");
                //show these
                $("#noCityProvided-msg").removeClass("hide");
                $("#dispAddress-btn").removeClass("hide");
            }
        }

        function ValidateCity(parsed1stAddressData){
            var retrievedCity, cityInData;
            parsed1stAddressData.forEach(function (item){
                retrievedCity = item['City'];
                cityInData = item['cityFound'];
            });
            if (cityInData){
                if(($("#SiteCity").val()) == retrievedCity){
                    //hide these
                    $("#validatePCode-btn").addClass("hide");
                    $("#reRunCityCheck-msg").addClass("hide");
                    //show these
                    $("#dispAddress-btn").removeClass("hide");
                    $("#pCodeSuccess").removeClass("hide");

                }else{
                    //show these
                    $("#reRunCityCheck-msg").removeClass("hide");
                    $("#reRunCityCheck-btn").removeClass("hide");
                    //hide these
                    $("#validatePCode-btn").addClass("hide");

                }
            }
        }

        function reRunCityCheck(evt){
            $("#reRunCityCheck-btn").addClass("hide");
            ValidateCity(parsed1stAddressData);
            return false; // do not submit the form
        }

        function parse2ndAddressQry(obj){

            obj.Items.forEach(function (item) {
                var suiteNo, streetNo, streetName, streetType, parse1, parse2, parse3, retrieveText, city, prov, pCode, Id2;

                if (item['Next']=='Retrieve'){
                    console.log('------ this is a Retrieve ------');
                    retrieveText = item['Text'];
                    parse1 = retrieveText.split(' ');
                    parse2 = parse1[0].split('-');
                    if (parse2.length ==2) {
                        suiteNo = parse2[0];
                        streetNo = parse2[1];
                    }else{
                        suiteNo = '';
                        streetNo=parse2[0];
                    }
                    streetName = parse1[1];
                    streetType = parse1[2];
                    var address = streetNo + ' ' + streetName + ' ' + streetType;

                    retrieveText = item['Description'];
                    parse3 = retrieveText.split(',');
                    //console.log(parse3);
                    city = parse3[0];
                    prov = parse3[1].trim();
                    pCode = parse3[2].trim();
                    Id2 = item['Id'];
                    parsed2ndAddressData.push({
                        'SuiteNumber': suiteNo,
                        'Id': Id2,
                        'Number': streetNo,
                        'Name': streetName,
                        'Type': streetType,
                        'Address': address,
                        'City': city,
                        'Province': prov,
                        'PostalCode': pCode
                    });
                }
            });
            validateAddress(parsed2ndAddressData);
        }

        function validateAddress(parsed2ndAddressData){
            var SiteAddress_2   = $('#SiteAddress_2').val(),
                SiteAddress_1   = $('#SiteAddress_1').val(),
                SiteCity        = $('#SiteCity').val(),
                SiteProvince    = $('#SiteProvince').val(),
                SitePostalCode  = $('#SitePcode').val(),
                msg             = '';
            if (count == 0){    <?php //only render the parsed suggestions stored in memory once?>
                renderMatchAddresses(parsed2ndAddressData);
                count++;
            }
            parsed2ndAddressData.forEach(function(item) { <?php //check for an exact address match?>
                if (!item['SuiteNumber']){
                    if ((item['Address'] == SiteAddress_1)
                        && (item['City'] == SiteCity)
                        && (item['Province'] == SiteProvince)
                        && (item['PostalCode'] == SitePostalCode))
                    {
                        item['logID_2'] = item['Id'];
                        addressMatchArray.push(item);
                        logIDs.push(item['Id']);
                    }
                }else{
                    if ((item['SuiteNumber'] == SiteAddress_2)
                        && (item['Address'] == SiteAddress_1)
                        && (item['City'] == SiteCity)
                        && (item['Province'] == SiteProvince)
                        && (item['PostalCode'] == SitePostalCode))
                    {
                        item['logID_2'] = item['Id'];
                        addressMatchArray.push(item);
                        logIDs.push(item['Id']);
                    }
                }
            });

            arrlength =addressMatchArray.length;
            if (arrlength >= 1){    <?php //This means a match has been found and the length of the array is 1 or more?>
                renderMatchAddresses(null, addressMatchArray);
                $("#dispAddress-btn").addClass("disabled");
            }else{
                $("#reRunCheck-btn").removeClass("hide");
                $("#reRunCheck-msg").removeClass("hide");
            }
        }

        function reRunAddressCheck(evt){
            $("#reRunCheck-btn").addClass("hide");
            $("#reRunCheck-msg").addClass("hide");
            validateAddress(parsed2ndAddressData);
            return false; // do not submit the form
        }

        function renderMatchAddresses(storedSuggestions,finalMatchedAddresses){
            var html, arrayLength, matchId;
            if (finalMatchedAddresses){
                arrayLength = finalMatchedAddresses.length;

                    html = "";
                    $.each(finalMatchedAddresses, function (idx, item) {
                        if (!item['SuiteNumber']) {
                            html += '<div class="result js_storeData clickable" data-id="' + idx + '"><button class="btn btn-xs btn-success"><span class="fa fa-check"></span></button> ' + "<strong>" + item['Address'] + ",</strong> " + item['City'] + ", " + item['Province'] + " " + item['PostalCode'];
                        } else {
                            html += '<div class="result js_storeData clickable" data-id="' + idx + '"><button class="btn btn-xs btn-success"><span class="fa fa-check"></span></button> ' + "<strong>" + item['SuiteNumber'] + " - " + item['Address'] + ",</strong> " + item['City'] + ", " + item['Province'] + " " + item['PostalCode'];
                        }
                    });
                    html += "</div><br>";
                    $("#message").html(html);
                    attachStoreDataListener();
                }else{

                }

            if (storedSuggestions) {
                html = "";
                storedSuggestions.forEach(function (item) {
                    if (!item['SuiteNumber']) {
                        html += "<div class = 'result'>" + "<strong>" + item['Address'] + ",</strong> " + item['City'] + ", " + item['Province'] + " " + item['PostalCode'] + " <br>";
                    } else {
                        html += "<div class = 'result'>" + "<strong>" + item['SuiteNumber'] + " - " + item['Address'] + ",</strong> " + item['City'] + ", " + item['Province'] + " " + item['PostalCode'] + " <br>";
                    }
                });
                html += "</div>";
                $("#messageSuggestions").html(html);
            }

        }

        function gatherFieldValues(){
            var formFields = {};
            formFields.SiteAddress_2   = $('#SiteAddress_2').val();
            formFields.SiteAddress_1   = $('#SiteAddress_1').val();
            formFields.SiteCity        = $('#SiteCity').val();
            formFields.SiteProvince    = $('#SiteProvince').val();
            formFields.SitePostalCode  = $('#SitePcode').val();

            console.log(formFields);

            return formFields;
        }

        function attachStoreDataListener(){
            $('.js_storeData').on('click', function(e){
                e.stopPropagation();
                e.preventDefault();

                var idx = $(this).data('id');
                // TODO: parse for possible instance of multiple selections

                var formFields = gatherFieldValues();

                $.each(formFields, function (key, value) {
                    if (addressMatchArray[idx][key] && addressMatchArray[idx][key] != value ) {
                        formFields[key] = addressMatchArray[idx][key];
                    }
                });

                formFields['logID_1'] = addressMatchArray[idx]['Id'];
                formFields['logID_2'] = logIDs[idx];
                formFields['CP_isValidated'] = 1;

                saveInputsIntoDatabase(formFields);

            });
        }

        function saveInputsIntoDatabase(fields){
            var request = $
                .ajax({
                    url         : '<?php echo(APP_URL . $XFA['storeMatches']) ?>',
                    dataType    : 'json',
                    data        : {match: fields, client: '<?php echo($client); ?>', v: '<?php echo(md5(SEED . $client)); ?>'},
                    method      : 'post'
                })
                .then(function(response){
                    if (response.success == false) {
                        showFailureMessage(response.message);
                    } else {
                        window.location.href = '<?php echo(APP_URL . $XFA['continue']); ?>&c=<?php echo($client); ?>';
                    }
                })
            ;

            request.fail(function (jqXHR, text) {
                showFailureMessage (text);
            });
        }

        function showFailureMessage (text) {

            $("#dialog-confirm-msg").html(text);

            $("#dialog-confirm").removeClass('hide').dialog({
                resizable: false,
                width: 420,
                modal: true,
                title_html: true,
                title: '<div class="widget-header widget-header-small"><h4 class="smaller"><i class="icon-warning-sign red"></i> Save Error</h4></div>',
                buttons: [
                    {
                        html: "<i class='fa fa-undo ace-icon bigger-110'></i> Close",
                        "class" : "btn btn-xs pull-right",
                        click: function() {
                            $( this ).dialog( "close" );
                            $("#dialog-confirm-msg").html('');
                        }
                    }
                ]
            })
        }

    });//END jQuery
</script>
