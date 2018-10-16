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
$Utils->authenticateAdminUser();

//		get args from the querystring  ... or Initialize
$display = (isset($_GET['disp'])) ? $_GET['disp'] : "";
$orderBy = (isset($_GET['order_by'])) ? $_GET['order_by'] : "";
$qsOrder = ($orderBy != "") ? "&order_by=" . $orderBy : "";
$qsFilter = (isset($display)) ? "&disp=" . $display : "";
switch ($orderBy) {
    case 'SIMcard_ID':
        $orderBy .= " DESC ";
        break;
    case 'ICCID':
        $orderBy .= " DESC ";
        break;
    case 'SIMstatus':
        $orderBy .= " ASC ";
        break;
    default:
        $orderBy .= " SIMcard_ID DESC ";
        break;
}

$strDateFilter = '';

$ispanTotal = 7;
function output($qryOrderBy, &$ispanTotal)
{
    global $DBUtils, $XFA;

    $sql = "SELECT
            sim.SIMcard_ID,
            sim.ICCID,
            sim.Provider AS P,
            sim.SIMstatus,
            sim.CreateBy,
            sim.CreateDate,
            lp.PkID AS provider_id,
            lp.Label_EN AS `Provider`,
            ls.PkID AS status_id,
            ls.Label_EN AS `SIMstatus`,
            u.User_ID,
            u.LastName,
            u.FirstName
            FROM
            erp_admin.SIMList AS sim
            INNER JOIN erp_admin._lookups AS lp ON lp.PkID = sim.Provider
            INNER JOIN erp_admin._lookups AS ls ON ls.PkID = sim.SIMstatus
            INNER JOIN erp_main.users AS u ON u.User_ID = sim.CreateBy
            WHERE sim.DeleteFlag = 0
            ORDER BY  " . $qryOrderBy . "";
    $rs = $DBUtils->queryDataSource($sql);
    $iRowsReturned = mysql_num_rows($rs);
    if ($iRowsReturned == 0) {
        $message = '<BR>No Records returned ...' . "";
        echo('<tr><td colspan="' . $ispanTotal . '">' . $message . '</td></tr>');
    } else {
        while ($row = mysql_fetch_assoc($rs)) {
            $thisID     = $row['SIMcard_ID'];
            $ICCID      = $row['ICCID'];
            $provider   = $row['Provider'];
            $SIMstatus  = $row['SIMstatus'];
            $FirstName  = $row['FirstName'];
            $LastName   = $row['LastName'];
            $createDate = $row['CreateDate'];
            $deleteLink = '<button class="btn btn-danger btn-xs js_deleteBtn" data-id="' . $thisID . '"><i class="fa fa-times ace-icon"></i></button>';
            $editLink   = '<a href="' . APP_URL . $XFA['edit'] . '&id=' . $thisID . '" title="Click here to Edit ' . SEC_TYPE . '" class="btn btn-primary btn-xs"><i class="fa fa-edit ace-icon"></i></a>';
            echo('
            <tr valign="top">
                <td align="center">' . $editLink . '</td>
                <td>' . $ICCID . '&nbsp;<span class="text-muted">['.$thisID.']</span></td>
                <td>' . $provider . '&nbsp;</td>
                <td>' . $SIMstatus . '&nbsp;</td>
                <td>' . $FirstName . " " . $LastName . '&nbsp;</td>          
                <td>' . $createDate . '&nbsp;</td> 
                <td>' . $deleteLink . '</td>                          
            </tr>
            ');
        }// wend
        mysql_free_result($rs);
    }
}// end Function

// BEGIN HTML CONTENT
?>
<script type="text/javascript" language="JavaScript">
    <!--
    //-->
</script>
<div class="headline">
    <table class="margin-6" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td>
                <strong>Assets Inventory</strong>
                <div class="muted">Manage <?php echo SEC_TYPE ?></div>
                <hr noshade size="1">
            </td>
        </tr>
        <tr>
            <td align="center">
                <a href="<?php echo(APP_URL . $XFA['add']); ?>&id=000"
                   title="Click here to add a <?php echo SEC_TYPE ?> entry" class=" btn btn-xs btn-success">
                    <i class="fa-plus fa ace-icon"></i>Create <?php echo SEC_TYPE ?>
                </a>
            </td>
        </tr>
    </table>
</div>
<div class="space-12"></div>
<table class="table table-hover">
    <thead>
    <tr>
        <th width="50">Edit</th>
        <th width="130"><a href="<?php echo(APP_URL . $XFA["list"]); ?>&order_by=ICCID<?php echo(QS_DEBUG); ?>">ICCID</a>
        </th>
        <th width="130">Provider</th>
        <th width="125">Status</th>
        <th width="160">Enter by</th>
        <th width="180">Enter date</th>
        <th width="50">Delete?</th>
    </tr>
    </thead>
    <?php output($orderBy, $ispanTotal); ?>
</table>

<div class="space-12"></div>
<form action="<?php echo(APP_URL . $XFA["delete"]); ?>" method="POST" id="deleteForm">
    <input type="hidden" name="DeleteID" id="DeleteID" value="">
</form>
<?php $Utils->displayIconHelp(); ?>
<div id="dialog-confirm" class="hide">
    <div class="alert alert-info bigger-110">
        This device will be removed.
    </div>
</div><!-- #dialog-confirm -->


<script>
    $(function () {
        $(".js_deleteBtn").on("click", function () {
            var id = $(this).data("id");
            $("#DeleteID").val(id);
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
        /*
        $("#deleteForm").on("submit", function(e){
            e.preventDefault();

            console.log($("#DeleteID").val())
        });
        */
    });
</script>