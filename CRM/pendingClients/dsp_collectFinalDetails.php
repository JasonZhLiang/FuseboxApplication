<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-22
 * Time: 10:31 AM
 */
// TODO: render static pending client data
$objClientForm = new PendingClientForm();

?>
<strong>Pending Clients</strong>
<div class="muted">Collect Building Details of Pending Client Site</div>
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
            <h4 class="text-center">Collect Building Details of Pending Client Site</h4>
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
            <input type="hidden" name="inputFormSubmitted" value="1">
            <?php $objClientForm->renderClientInputForm('review', $inputFormFields); ?>

            <div class="text-center">
                <a href="<?php echo(APP_URL . $XFA['return']); ?>" class="btn btn-sm btn-default">
                    <span class="ace-icon fa fa-undo"></span>
                    Return
                </a>
                <button type="submit" class="btn btn-sm btn-info">
                    <span class="ace-icon fa fa-check"></span>
                    Save/Return
                </button>
                <button type="submit" class="btn btn-sm btn-success" name="publishPendingClient" value="1">
                    <span class="ace-icon fa fa-check"></span>
                    Save/Publish
                </button>
            </div>
        </form>
    </div>
</div>