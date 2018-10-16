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

if (empty($_GET['s']) || empty($_GET['v'])) {
    trigger_error('Mssing Site or Validation key', E_USER_ERROR);
}
if ( ! is_numeric($_GET['s']) || $_GET['v'] != md5(SEED . $_GET['s']) ) {
    trigger_error('Validation Failure', E_USER_ERROR);
}

$Site_ID    = $_GET['s'];
$site       = $_SESSION['siteList'][$Site_ID];
$siteUsers  = [];

$sql = "
    SELECT
        u.User_ID,
        u.LastName,
        u.FirstName,
        sx.isBroadCast,
        sx.isBroadCastVoice,
        sx.isBroadCastSMS,
        sx.AccessLevel
    FROM users AS u
    INNER JOIN site_user_xref AS sx ON sx.User_ID = u.User_ID
    WHERE sx.Site_ID = :Site_ID
    ORDER BY sx.AccessLevel ASC
";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID', $Site_ID);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $row['Fullname'] =  $row['FirstName'].' '.$row['LastName'];
        $row['isNotified'] = $row['isBroadCast'] || $row['isBroadCastVoice'] || $row['isBroadCastSMS'];
        $siteUsers[] = $row;
    }
}

$colspan = IS_TEST_SERVER ? 4: 3;

?>
<strong>Sites List</strong>
<div class="muted">List of Site Users</div>
<hr noshade size="1">
<div class="margin-bottom-10">
    <a href="<?php echo(APP_URL . $XFA['return']); ?>">
        <span class="ace-icon fa fa-angle-double-left"></span>
        Return
    </a>
</div>
<fieldset>
    <legend>Registered Users for <?php echo($site['SiteName']); ?></legend>
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2">
            <table class="table">
                <tr>
                    <?php if (IS_TEST_SERVER) { // This is a DEV TOOL ONLY to be used on DEV SERVER ONLY ?>
                        <th width="40"></th>
                    <?php } ?>
                    <th>Name</th>
                    <th>Access Level</th>
                    <th>Notified</th>
                </tr>
                <?php if ( ! empty($siteUsers) ) { ?>
                    <?php foreach ($siteUsers as $siteUser) { ?>
                        <tr>
                            <td>
                                <a href="<?php echo($XFA['proxyLogin'] .'&c='. $siteUser['User_ID'] .'&v='. md5(SEED . $siteUser['User_ID'])); ?>"
                                   class="btn btn-yellow btn-xs"
                                   title="Login as this person"
                                   target="_blank"
                                >
                                    <span class="ace-icon fa fa-eye"></span>
                                </a>
                            </td>
                            <td><?php echo($siteUser['Fullname']); ?></td>
                            <td><?php echo($siteUser['AccessLevel']); ?></td>
                            <td><?php echo($siteUser['isNotified'] ? '<span class="ace-icon fa fa-check green"></span>': ''); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="<?php echo($colspan); ?>" class="text-center text-italic text-muted">
                            No users associated with this site
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</fieldset>
