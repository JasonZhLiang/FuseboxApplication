<?php
////////////////////////////////////////////////////////////
// File: dsp_list
//
// Description:
//		Displays list of items
//
// Information:
//		Date	- 2018-02-02
//		Author	- Jasonzh L
//		Version	- 1.0
//
// History:
//		- v1.0 initial development
//
////////////////////////////////////////////////////////////
// get args from the query string  ... or Initialize
define('DAMAGED_SIM', 6);
$Utils->checkVerificationHash(@$_GET['mv'], @$_GET['mid']);
$orderBy = (isset($_GET['order_by'])) ? $_GET['order_by'] : "";
$modemID = $_GET['mid'];
?>
<div class="headline">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <strong>SIMcard Management<?php echo(' for Modem # '. $_GET['mid']) ?></strong>
                <div class="muted">Manage SIM cards</div>
                <hr noshade size="1">
            </td>
        </tr>
    </table>
</div>
<div class="space-12"></div>
<div class="row">
    <div class="text-center">
        <label><span class="lbl infobox-blue">List of SIMs Assigned to Current Modem</span></label>
    </div>
</div>
<?php renderSimList ($orderBy,$modemID,true)?>
<div class="space-12"></div>
<div class="row">
    <div class="text-center">
        <label><span class="lbl infobox-blue">List of Unassigned SIMs</span></label>
    </div>
</div>
<?php renderSimList ($orderBy,$modemID,false)?>
<div class="row">
    <div class="col-xs-12 text-center">
        <a class="btn btn-xs" href="<?php echo(APP_URL . $XFA["return"].'&fromSite=1'); ?>"><i class="ace-icon fa fa-rotate-right"></i> <?php $trans->et('Return') ?></a>
    </div>
</div>

<div class="space-12"></div>

<div id="dialog-toggle-addconfirm" class="hide">
    <p>This action will associate this SIM card with the current modem. Do you wish to continue?</p>
</div>

<div id="dialog-toggle-deleteconfirm" class="hide">
    <p>This action will disassociate this SIM card from the current modem. Do you wish to continue?</p>
</div>

<div class="modal fade" id="linkPop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="ace-icon fa fa-exclamation-triangle orange"></span>
                    This modem already has a SIM card associated with it.
                </h4>
            </div>
            <div class="modal-body">
                <p>Before associating a new SIM card, first disassociate the currently assigned SIM card.</p>
                <p>To unlink the exist SIM card, click the unlink icon on the right side of SIM card info row.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.js-toggleAdd').on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
            var href = $(this).attr('href');
            $('#dialog-toggle-addconfirm')
                .removeClass('hide')
                .dialog({
                    modal: true,
                    width: 400,
                    title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-warning ace-icon'></i> Add SIM to Modem</h4></div>",
                    title_html: true,
                    buttons: [
                        {
                            html: "<i class='fa ace-icon fa-undo bigger-110'></i> Cancel",
                            "class" : "btn btn-default pull-right btn-xs",
                            click: function() {
                                $( this ).dialog( "close" );
                            }
                        },
                        {
                            html: "<i class='ace-icon fa fa-certificate bigger-110'></i>&nbsp;Confirm",
                            "class" : "btn btn-xs btn-primary",
                            click: function() {
                                $( this ).dialog( "close" );
                                window.location.href = href;
                            }

                        }
                    ]
                });
        });
        $('.js-toggleDelete').on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
            var href = $(this).attr("href");
            $('#dialog-toggle-deleteconfirm')
                .removeClass('hide')
                .dialog({
                    modal: true,
                    width: 400,
                    title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-warning ace-icon red'></i> Delete SIM from Modem</h4></div>",
                    title_html: true,
                    buttons: [
                        {
                            html: "<i class='fa ace-icon fa-undo bigger-110'></i> Cancel",
                            "class" : "btn btn-default pull-right btn-xs",
                            click: function() {
                                $( this ).dialog( "close" );
                            }
                        },
                        {
                            html: "<i class='ace-icon fa fa-certificate bigger-110'></i>&nbsp;Confirm",
                            "class" : "btn btn-xs btn-primary",
                            click: function() {
                                $( this ).dialog( "close" );
                                window.location.href = href;
                            }

                        }
                    ]
                });
        });

        var $_GET = {};
        if(document.location.toString().indexOf('?') !== -1) {
            var query = document.location
                .toString()
                // get the query string
                .replace(/^.*?\?/, '')
                // and remove any existing hash string (thanks, @vrijdenker)
                .replace(/#.*$/, '')
                .split('&');

            for(var i=0, l=query.length; i<l; i++) {
                var aux = decodeURIComponent(query[i]).split('=');
                $_GET[aux[0]] = aux[1];
            }
        }

        var popUnlink =$_GET['simExist'];

        if (popUnlink == 1){
            $('#linkPop').modal();
            return false;
        }

        function getUrlVars() {
            var vars = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                vars[key] = value;
            });
            return vars;
        }
    });
</script>
<?php
function renderSimList ($orderBy,$modemID,$assignedToModems){
    global $Utils,$PDOdb,$XFA;
    $modelsA = [];
    switch ($orderBy) {
        case 'ICCID':
            $orderBy = "ICCID DESC ";
            break;
        case 'SIMstatus':
            $orderBy = "SIMstatus ASC ";
            break;
        case 'CreateDate':
            $orderBy = "CreateDate DESC ";
            break;
        case 'Provider':
            $orderBy = "Provider ASC ";
            break;
        default:
            $orderBy = "SIMcard_ID DESC ";
            break;
    }
    //option 1:
    $sql = "SELECT
                sim.SIMcard_ID,
                sim.ICCID,
                DATE_FORMAT(sim.CreateDate, '%Y-%m-%d') AS CreateDate,
                lp.Label_EN AS `Provider`,
                ls.Label_EN AS `SIMstatus`,
                u.FirstName,
                u.LastName,
                x.SIMcard_ID AS SID,
                x.Modem_ID
                FROM
                erpcorp_admin.SIMList AS sim
                INNER JOIN erpcorp_admin._lookups AS lp ON lp.PkID = sim.Provider
                INNER JOIN erpcorp_admin._lookups AS ls ON ls.PkID = sim.SIMstatus
                INNER JOIN users AS u ON u.User_ID = sim.CreateBy
                LEFT JOIN erpcorp_admin.site_asset_xref AS x ON sim.SIMcard_ID = x.SIMcard_ID
                    AND x.DeleteFlag = 0
                WHERE sim.DeleteFlag = 0
                AND sim.SIMstatus < ".DAMAGED_SIM."
                ORDER BY  " . $orderBy . "";

    $PDOdb->prepare($sql);
    $PDOdb->execute();
    if ($PDOdb->rowCount() > 0) {
        while ($row = $PDOdb->getRow()) {
            $row['EnteredBy'] = $row['FirstName'] .' '. $row['LastName'];
            $row['vHash']     = $Utils->createVerificationHash($row['SIMcard_ID']);
            $row['bIsLinked'] = empty($row['SID'])?false:true;
            $row['bIsLinkedToCurrentModem'] = ($row['Modem_ID']==$modemID)?true:false;
            $modelsA[]         = $row;
        }
    }
    $models = array();
    if (!empty($modelsA)){
        foreach ($modelsA as $model){
            if($assignedToModems){
                if ($model['bIsLinked']&&$model['bIsLinkedToCurrentModem']){
                    $models[] = $model;
                }
            }else{
                if (!$model['bIsLinked']){
                    $models[] = $model;
                }
            }
        }
    }
    ?>
    <?php if (empty($models)) { ?>
        <div class="row">
            <div class="col-xs-offset-3 col-xs-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h3 class="text-center">No Records Found</h3>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <table class="table table-hover">
            <thead>
            <tr>
                <th width="150" class="text-center">Link</th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=ICCID<?= QS_DEBUG ?>">ICCID</a>
                </th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=Provider<?= QS_DEBUG ?>">Provider</a>
                </th>
                <th width="125">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=SIMstatus<?= QS_DEBUG ?>">Status</a>
                </th>
                <th width="160">Entered by</th>
                <th width="130">
                    <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=CreateDate<?= QS_DEBUG ?>">Entered Date</a>
                </th>
                <th width="120" class="text-center">Assigned Modem_ID</th>
                <th width="150" class="text-center">Unlink</th>
            </tr>
            </thead>
            <?php foreach ($models as $model) {
//                if (in_array($model['SIMcard_ID'], $sims) == false){?>
                    <tr valign="top">
                        <td align="center">
                            <?php if (!$model['bIsLinked'])  { ?>
                                <a class="js-toggleAdd" href="<?= APP_URL . $XFA['toggleLink'] . '&sim=' . $model['SIMcard_ID'] . '&v=' . $model['vHash'] . '&mid=' . $_GET['mid']. "&mv=" . md5(SEED . $_GET['mid']). '&state=0'?>">
                                    <i class="ace-icon fa fa-chain green text-center"></i>
                                </a>
                            <?php } ?>
                        </td>
                        <td><?= $model['ICCID'] ?>&nbsp;<small class="text-muted">[ID:<?= $model['SIMcard_ID'] ?>]</small></td>
                        <td><?= $model['Provider'] ?></td>
                        <td><?= $model['SIMstatus'] ?></td>
                        <td><?= $model['EnteredBy'] ?></td>
                        <td><?= $model['CreateDate'] ?></td>
                        <td class="text-center"><?= $model['Modem_ID'] ?></td>
                        <td align="center">
                            <?php if ($model['bIsLinkedToCurrentModem']) { ?>
                                <a class="js-toggleDelete" href="<?= APP_URL . $XFA['toggleLink'] . '&sim=' . $model['SIMcard_ID'] . '&v=' . $model['vHash'] .'&mid=' . $_GET['mid']. "&mv=" . md5(SEED . $_GET['mid']). '&state=1'?>">
                                    <i class="ace-icon fa fa-chain-broken red"></i>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
            //}?>
        </table>
    <?php } ?>
<?php } ?>