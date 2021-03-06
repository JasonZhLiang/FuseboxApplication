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
$models = [];

$orderBy = (isset($_GET['order_by'])) ? $_GET['order_by'] : "";
switch ($orderBy) {
    case 'ICCID':
        $orderBySQL = "ICCID DESC ";
        break;
    case 'SIMstatus':
        $orderBySQL = "SIMstatus ASC ";
        break;
    case 'CreateDate':
        $orderBySQL = "CreateDate DESC ";
        break;
    case 'Provider':
        $orderBySQL = "Provider ASC ";
        break;
    default:
        $orderBySQL = "SIMcard_ID DESC ";
        break;
}
$listSwitch= (empty($_GET['show_delete'])) ? 0 : 1;

$sql = "SELECT
            sim.SIMcard_ID,
            sim.ICCID,
            DATE_FORMAT(sim.CreateDate, '%Y-%m-%d') AS CreateDate,
            lp.Label_EN AS `Provider`,
            ls.Label_EN AS `SIMstatus`,
            u.FirstName,
            u.LastName
            FROM
            erpcorp_admin.SIMList AS sim
            INNER JOIN erpcorp_admin._lookups AS lp ON lp.PkID = sim.Provider
            INNER JOIN erpcorp_admin._lookups AS ls ON ls.PkID = sim.SIMstatus
            INNER JOIN users AS u ON u.User_ID = sim.CreateBy
            WHERE sim.DeleteFlag = :listSwitch
            ORDER BY  $orderBySQL";

$PDOdb->prepare($sql);
$PDOdb->bind('listSwitch', $listSwitch);
$PDOdb->execute();

if ($PDOdb->rowCount() > 0) {
    while ( $row = $PDOdb->getRow()){
        $row['EnteredBy'] = $row['FirstName'] .' '. $row['LastName'];
        $row['vHash']     = $Utils->createVerificationHash($row['SIMcard_ID']);
        $models[]         = $row;
    }
}
?>

<div class="headline">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <strong>Assets Inventory</strong>
                <div class="muted">Manage <?= SEC_TYPE ?></div>
                <hr noshade size="1">
            </td>
        </tr>
        <tr>
            <td align="center">
                <a href="<?= APP_URL . $XFA['add'] ?>&id=000"
                   title="Click here to add a <?= SEC_TYPE ?> entry" class=" btn btn-xs btn-success">
                    <i class="fa-plus fa ace-icon"></i>Create <?= SEC_TYPE ?>
                </a>
            </td>
        </tr>
    </table>
</div>
<div class="space-12"></div>

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
            <th width="50">Edit</th>
            <th width="130">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=ICCID<?= QS_DEBUG ?>">
                    ICCID</a>
            </th>
            <th width="130">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=Provider<?= QS_DEBUG ?>">
                    Provider</a>
            </th>
            <th width="125">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=SIMstatus<?= QS_DEBUG ?>">
                    Status</a>
            </th>
            <th width="160">
                Enter by
            </th>
            <th width="180">
                <a href="<?= (APP_URL . $XFA["list"]); ?>&order_by=CreateDate<?= QS_DEBUG ?>">
                    Enter Date</a>
            </th>
            <th width="50" <?php if($listSwitch==1)echo("style ='display:none'"); ?>>
                Delete
            </th>
        </tr>
        </thead>

        <?php foreach ($models as $model) { ?>
            <tr valign="top">
                <td align="center">
                    <a href="<?=  APP_URL . $XFA['edit'] . '&id=' . $model['SIMcard_ID'] .'&v='. $model['vHash'] ?>"
                       class="btn btn-info btn-xs ">
                        <i class="ace-icon fa fa-edit"></i>
                    </a>
                </td>
                <td><?= $model['ICCID'] ?>&nbsp;<small class="text-muted">[ID:<?= $model['SIMcard_ID'] ?>]</small></td>
                <td><?= $model['Provider'] ?></td>
                <td><?= $model['SIMstatus'] ?></td>
                <td><?= $model['EnteredBy'] ?></td>
                <td><?= $model['CreateDate'] ?></td>
                <td width="50" <?php if($listSwitch==1)echo("style ='display:none'"); ?>>
                    <button class="btn btn-danger btn-xs js_deleteBtn "
                            data-id="<?= $model['SIMcard_ID'] ?>"
                            data-v="<?= $model['vHash'] ?>">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                </td>
            </tr>
        <?php } ?>

    </table>

    <div class="row">
        <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
            <?php if ($listSwitch==0) { ?>
            <tr>
                <td align="center">
                    <a href="<?= APP_URL . $XFA['list'] ?>&show_delete=1" class="btn btn-xs btn-success">
                        <i class="fa-eraser fa ace-icon"></i>Show Deleted SIM Cards</a>
                </td>
            </tr>
            <?php } else { ?>
            <tr>
                <td align="center">
                    <a href="<?= APP_URL . $XFA['list'] ?>&show_delete=0" class="btn btn-xs btn-success">
                        <i class="fa-reply fa ace-icon"></i>Back to available SIM Cards</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

<?php } ?>

<div class="space-12"></div>

<?= $Utils->displayIconHelp() ?>

<form action="<?= APP_URL . $XFA["delete"] ?>" method="POST" id="deleteForm">
    <input type="hidden" name="DeleteID" id="DeleteID" value="">
    <input type="hidden" name="v" id="v" value="">
</form>

<div id="dialog-confirm" class="hide">
    <div class="alert alert-info bigger-110">
        This device will be removed.
    </div>
</div><!-- #dialog-confirm -->

<script>
    $(function () {
        $(".js_deleteBtn").on("click", function () {
            var id = $(this).data("id");
            var v = $(this).data("v");
            $("#DeleteID").val(id);
            $("#v").val(v);
            console.log();
            $("#dialog-confirm")
                .removeClass('hide')
                .dialog({
                    modal: true,
                    title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-check ace-icon'></i> Delete SIM card?</h4></div>",
                    title_html: true,
                    buttons: [
                        {
                            html: "<i class='fa ace-icon fa-trash bigger-110'></i> Delete",
                            "class": "btn btn-danger btn-xs",
                            click: function () {
                                $(this).dialog("close");
                                //alert("id check -- " + id);
                                $("#deleteForm").submit();
                            }
                        },
                        {
                            html: "<i class='fa fa-times ace-icon bigger-110'></i> Cancel",
                            "class": "btn btn-xs",
                            click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ]
                })
            ;
        });

//        $("#deleteShow").click(function(){
//            //alert("Hello! I am an alert box!");
//            if ($(this).text() == "Show Deleted SIM Cards"){
//                $(this).text("Return to Normal list");
//                $("#showSwitch").val("1");
//                $a=$("#showSwitch").val();
//                alert($a);
//                //document.cookie = "showSwitch" + "=" + "1";
//                //alert(document.cookie);
//                //location.reload();
//            }else{
//                $(this).text("Show Deleted SIM Cards");
//                $("#showSwitch").val("0");
//                //document.cookie = "showSwitch" + "=" + "0";
//                //alert(document.cookie);
//                //location.reload();
//            }
//            //$("#showForm").submit();
//        });

        $("#deleteShow").click(function(){
            if ($(this).data("lbl") == "checked"){
                $(this).text("Return to Normal list");
                $(this).attr("href", "<?= APP_URL . $XFA['list'] ?>&show_delete=1");
                alert("yes");
                $(this).data("lbl","uncheck");
                alert("yessss");
            }else{
                $(this).text("Show Deleted SIM Cards");
                //$("#showSwitch").val("0");
                $(this).attr("href", "<?= APP_URL . $XFA['list'] ?>&show_delete=0");
                alert("no");
            }

        });

    });
</script>
