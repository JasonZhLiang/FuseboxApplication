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

// Init

$disableEdit    = '';
$error = [];
$models = [];//use to hold every single model row array,

$sql = "SELECT
            sim.SIMcard_ID,
            sim.IMSI,
            sim.Provider,
            sim.SIMstatus,
            u.FirstName,
            u.LastName,
            sim.CreateDate
        FROM
            erp_admin.SIMList AS sim
        INNER JOIN erp_main.users AS u ON sim.CreateBy = u.User_ID
        WHERE sim.DeleteFlag = 0
        ";
$rs = $DBUtils->queryDataSource($sql);

while ($row = mysql_fetch_assoc($rs)) {
    $models[] = $row;//push row to rowset;
}// wend





/*//test connect
$servername = "192.168.1.110";
$username = "jliang";
$password = "7zpm9528";
$dbname = "erp_admin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT
            sim.SIMcard_ID,
            sim.IMSI,
            sim.Provider,
            sim.SIMstatus,
            u.FirstName,
            u.LastName,
            sim.CreateDate
        FROM
            erp_admin.SIMList AS sim
        INNER JOIN erp_main.users AS u ON sim.CreateBy = u.User_ID
        WHERE sim.DeleteFlag = 0
        ";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";

        $models[] = $row;//push
    }

} else {
    echo "0 results";
}
$conn->close();*/
//end test





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
                <strong>Web Content - Blog Scroller</strong>
                <div class="muted">Manage <?php echo SEC_TYPE ?> items</div>
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
        <th>SIM card ID</th>
        <th>IMSI / Link</th>
        <th>Provider</th>
        <th>SIM card status</th>
        <th>Enter by</th>
        <th>Enter date</th>
        <th>Delete?</th>
    </tr>
    </thead>
    <?php if (empty($models)) { ?>
        <tr>
            <td class="text-center text-italic text-muted" colspan="7">
                No SIM available
            </td>
        </tr>
    <?php } else { ?>
        <?php
        foreach ($models as $rec) { ?>
            <tr>
                <td width="25">
                    <a href="<?php echo(APP_URL . $XFA['edit'] . "&id=" . $rec['SIMcard_ID']); ?>"
                       class="btn btn-xs btn-info">
                        <i class="ace-icon fa fa-edit"></i>
                    </a>
                </td>

<!--                <td>-->
<!--                    --><?//= $rec['IMSI'] ?>
<!--                </td>-->

                <td width="25">
                    <a href="<?php echo(APP_URL . $XFA['edit'] . '&id=' . $rec['SIMcard_ID'] . '&v=' . md5(SEED . $rec['SIMcard_ID'])); ?>"
                       class="btn btn-xs btn-info"><?= $rec['IMSI'] ?></a>
                </td>

                <td>
                    <?= $rec['Provider'] ?>
                </td>

                <td>
                    <?= $rec['SIMstatus'] ?>
                </td>

                <td>
                    <?= $rec['FirstName']." ".$rec['LastName']  ?>
                </td>

                <td>
                    <?= $rec['CreateDate'] ?>
                </td>

                <td>
                    <button class="btn btn-danger btn-xs js_deleteBtn <?php echo($disableEdit); ?>" data-client="<?php echo($rec['SIMcard_ID']); ?>" data-v="<?php echo(md5(SEED . $rec['SIMcard_ID'])); ?>">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>

<div class="space-12"></div>
<form action="<?php echo(APP_URL . $XFA['delete']); ?>" method="POST" id="deleteForm">
    <input type="hidden" name="DeleteID" id="DeleteID" value="">
</form>
<?php //$Utils->displayIconHelp(); ?>
<div id="dialog-confirm" class="hide">
    <div class="alert alert-info bigger-110">
        This item will be removed. nnn
    </div>
</div><!-- #dialog-confirm -->


<script>
    $(function(){
        $(".js_deleteBtn").on("click", function(){
            var id = $(this).data("id");
            $("#DeleteID").val(id);
            console.log();
            $( "#dialog-confirm" )
                .removeClass('hide')
                .dialog({
                    modal: true,
                    title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='fa fa-check ace-icon'></i> Delete News Item</h4></div>",
                    title_html: true,
                    buttons: [
                        {
                            html: "<i class='fa ace-icon fa-trash bigger-110'></i> Delete",
                            "class" : "btn btn-danger btn-xs",
                            click: function() {

                                $( this ).dialog( "close" );
                                $("#deleteForm").submit();
                            }
                        },
                        {
                            html: "<i class='fa fa-times ace-icon bigger-110'></i> Cancel",
                            "class" : "btn btn-xs",
                            click: function() {
                                $( this ).dialog( "close" );
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