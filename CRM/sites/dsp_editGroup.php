<?php
////////////////////////////////////////////////////////////
// File: dsp_editGroup.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2016-06-21
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in PhpStorm
//		
//
////////////////////////////////////////////////////////////
?>
<div class="widget-box transparent">

    <h3 class="header smaller lighter blue"><?php $trans->et('Add / Edit Group') ?></h3>

    <?php if ( ! empty($error)) { ?>
        <div class="row">
            <div class="col-sm-offset-3 col-xs-6">
                <div class="alert alert-danger">
                    <button class="close" data-dismiss="alert" type="button">
                        <i class="ace-icon fa fa-times"></i>
                    </button>
                    <div class="text-center">
                        <strong>
                            <i class="ace-icon fa fa-times"></i>
                            <?php $trans->et('Error') ?>!
                        </strong><br>
                        <?php echo($error); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <form action="<?php echo (APP_URL . $XFA['process']);?>" method="post" class="form-horizontal">
        <input type="hidden" name="inputFormSubmitted" value="TRUE" />
        <div class="form-group">
            <label class="col-sm-4 control-label no-padding-right" for="Name"><?php $trans->et('Group Name') ?>:</label>
            <div class="col-sm-6">
                <input type="text" class="col-sm-8" name="Name" id="Name" value="<?php echo (htmlspecialchars($group["GroupName"])); ?>" placeholder="" <?php echo($HTMLreq); ?> maxlength="255">
            </div>
        </div>
        <div class="col-sm-12 text-center">
            <input type="submit" class="btn btn-xs btn-info" name="saveChanges" value="Save Changes">
            <a href="<?php echo (APP_URL . $XFA["return"] ."&sid=" . $curSite ."&vid=". md5(SEED.$curSite)); ?>" class="btn btn-xs"><?php $trans->et('Cancel') ?></a>
        </div>
    </form>
</div>


