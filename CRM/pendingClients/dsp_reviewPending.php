<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-22
 * Time: 10:37 AM
 */

// TODO: render static pending client data
$objClientForm = new PendingClientForm();

?>
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
            <h4 class="text-center">Accept or Reject Pending Client</h4>
        </div>
    </div>
</div>

<?php if (!empty($strReporting) && DISPLAY_REPORTING_MESSAGES) { ?>
    <div class="row">
        <div class="col-xs-offset-3 col-xs-6">
            <div class="alert alert-info">
                <div class="row">
                    <div class="col-xs-8 col-xs-offset-4"><?php echo($strReporting); ?></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if (!empty($error)) { ?>
    <div class="row">
        <div class="col-xs-offset-3 col-xs-6">
            <div class="alert alert-danger">
                <div class="row">
                    <div class="col-xs-4 text-right"><b>Error!</b></div>
                    <div class="col-xs-8"><?php echo($error); ?></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="row">

    <div class="col-sm-offset-2 col-sm-8">
        <form class="form-horizontal" action="<?php echo(APP_URL . $XFA['process']); ?>" method="POST"
              id="submitClient">
            <input type="hidden" name="PkID" value="<?php echo($inputFormFields['PkID']); ?>">
            <input type="hidden" name="v" value="<?php echo(md5(SEED . $inputFormFields['PkID'])); ?>">
            <input type="hidden" name="EULADate" value="<?php echo($inputFormFields['EULADate']); ?>">


            <?php $objClientForm->renderClientInputForm('pending', $inputFormFields); ?>

            <div class="text-center">
                <a href="<?php echo(APP_URL . $XFA['return']); ?>" class="btn btn-sm btn-default">
                    <span class="ace-icon fa fa-undo"></span>
                    Return
                </a>
                <button type="submit" class="btn btn-sm btn-danger" name="deleteFormSubmitted" value="1">
                    <span class="ace-icon fa fa-times"></span>
                    Reject
                </button>
                <?php if( ! empty($inputFormFields['EULADate']) ){ ?>
                    <button type="submit" class="btn btn-sm btn-success" name="pushToCollectDetails" value="1">
                        Validate Address
                        <span class="ace-icon fa fa-chevron-right"></span>
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
</div>