<?php
////////////////////////////////////////////////////////////
// File: dsp_viewSiteUsers.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2017-04-03
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in PhpStorm
//		
//
////////////////////////////////////////////////////////////

if (empty($_GET['o']) || empty($_GET['v'])) {
    trigger_error('Mssing FR Org or Validation key', E_USER_ERROR);
}
if ( ! is_numeric($_GET['o']) || $_GET['v'] != md5(SEED . $_GET['o']) ) {
    trigger_error('Validation Failure', E_USER_ERROR);
}

$FR_ID    = $_GET['o'];
$org       = $_SESSION['orgList'][$FR_ID];
$orgUsers  = [];

$sql = "
    SELECT
        u.User_ID,
        u.LastName,
        u.FirstName,
        u.AccessLevel
    FROM users AS u
    WHERE u.FR_ID = :FR_ID
    ORDER BY u.AccessLevel ASC
";
$PDOdb->prepare($sql);
$PDOdb->bind('FR_ID', $FR_ID);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $row['Fullname'] =  $row['FirstName'].' '.$row['LastName'];
        $orgUsers[]      = $row;
    }
}

$colspan = IS_TEST_SERVER ? 4: 3;

?>
<strong>Sites List</strong>
<div class="muted">List of First Responders</div>
<hr noshade size="1">
<div class="margin-bottom-10">
    <a href="<?php echo(APP_URL . $XFA['return']); ?>">
        <span class="ace-icon fa fa-angle-double-left"></span>
        Return
    </a>
</div>
<fieldset>
    <legend>Registered Users for <?php echo($org['Desc_EN']); ?></legend>
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2">
            <table class="table">
                <tr>
                    <th width="40"></th>
                    <th>Name</th>
                    <th>Access Level</th>
                </tr>
                <?php if ( ! empty($orgUsers) ) { ?>
                    <?php foreach ($orgUsers as $orgUser) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo($XFA['proxyLogin'] .'&c='. $orgUser['User_ID'] .'&v='. md5(SEED . $orgUser['User_ID'])); ?>"
                                   class="btn btn-yellow btn-xs"
                                   title="Login as this person"
                                   target="_blank"
                                >
                                    <span class="ace-icon fa fa-eye"></span>
                                </a>
                            </td>
                            <td><?php echo($orgUser['Fullname']); ?></td>
                            <td><?php echo($orgUser['AccessLevel']); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="<?php echo($colspan); ?>" class="text-center text-italic text-muted">
                            No users associated with this organization
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</fieldset>
