<?php


if (empty($_GET['id']) || empty($_GET['v'])) {
    trigger_error('No ID or validation hash on Query string', E_USER_ERROR);
}

if ( ! is_numeric($_GET['id']) || $_GET['v'] != md5(SEED . $_GET['id'])) {
    trigger_error('Failed validation', E_USER_ERROR);
}

$cam_ID     = $_GET['id'];
$_SESSION["ADMIN_USER"]["SiteCameras"]['curCamera'] = $cam_ID;
$curSite    = $_SESSION['ADMIN_USER']['Site_ID'];



$objCameraRenderer = new CameraComponent();
$objCameraWidget   = new CameraData($PDOdb, $objCameraRenderer);
?>

<!--<div id="breadcrumbs" class="breadcrumbs">-->
<!--	<ul class="breadcrumb">-->
<!--		--><?php //createBreadcrumb(); ?>
<!--	</ul>-->
<!--</div>-->



<?php $objCameraWidget->renderCameraWidget($curSite, $cam_ID); ?>

<div class="text-center">
    <a href="<?php echo(APP_URL . $XFA["return"]."&sid=" . $curSite ."&vid=". md5(SEED.$curSite)); ?>" title="Return to Camera List" class="btn btn-xs">
        <i class="success ace-icon fa fa-rotate-left"></i>
        <?php $trans->et('Return') ?>
    </a>
</div>