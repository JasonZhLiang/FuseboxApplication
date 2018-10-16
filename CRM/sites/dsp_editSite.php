<?php
////////////////////////////////////////////////////////////
// File: dsp_edit
//
// Description:
//		Displays edit/add of items
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
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            <strong>Sites Tool</strong>
            <div class="muted">Site Edit</div>
            <hr noshade size="1">
        </td>
    </tr>
    <tr>
        <td class="sectionNavBar">
            <a href="<?= APP_URL . $XFA['list'] ?>" title="Return to List; abandon changes."> << Return To Search</a>
        </td>
    </tr>
</table>
<div class="row">
    <div class="col-xs-offset-3 col-xs-6">
        <div class="row">
            <h4 class="text-center">Site Edit</h4>
            <hr noshade size="1">
        </div>
    </div>
</div>
<?php if (!empty($error)) { ?>
    <div class="row">
        <div class="col-xs-offset-3 col-xs-6">
            <div class="alert alert-danger">
                <div class="row">
                    <div class="col-xs-8 col-xs-offset-2"><?= $error ?></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-1">
        <form action="<?= APP_URL . $XFA['process'] ?>" class="form-horizontal" method="post" name="frmInput">
            <input type="hidden" name="inputFormSubmitted" value="1"/>
            <input type="hidden" name="id" value="<?= $site['Site_ID'] ?>"/>
            <input type="hidden" name="v" value="<?= $site['vHash'] ?>"/>
<!--            <input type="hidden" name="s" value="--><?//= $site['s'] ?><!--"/>-->
<!--            <input type="hidden" name="q" value="--><?//= $site['q'] ?><!--"/>-->
            <div class="form-group">
                <label for="PkID" class="col-sm-4 control-label">ID:</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="text" class="form-control date-picker" name="PkID" id="PkID" value="<?= $site['Site_ID'] ?>" readonly>
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-lock"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="SiteName" class="col-xs-4 control-label required"> Site Name:</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="SiteName" name="SiteName" value="<?php echo($site['SiteName']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="SiteDescription" class="col-xs-4 control-label required"> Site Description:</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="SiteDescription" name="SiteDescription" value="<?php echo($site['SiteDescription']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="Address_1" class="col-xs-4 control-label required"> Address:</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="Address_1" name="Address_1" value="<?php echo($site['Address_1']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4 required"> Max Number of Users:</label>
                <div class="col-xs-8">
                    <label for="PaidNumUsers" class="col-xs-2 control-label small no-padding-right blue"> Subscribed </label>
                    <div class="col-xs-2">
                        <select class="form-control" id="PaidNumUsers" name="PaidNumUsers">
                            <?php echo($selPaidNumUsers); ?>
                        </select>
                    </div>
                    <label for="MaxNumUsers" class="col-xs-2 control-label small no-padding-right blue"> Allowed </label>
                    <div class="col-xs-2">
                        <input type="text" class="form-control" id="MaxNumUsers"
                               name="MaxNumUsers" value="<?php  if (!empty($site['MaxNumUsers'])) echo($site['MaxNumUsers']); else echo(DEFAULT_MAX_NUM_USERS);  ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-xs-4 required"> Max Number of Tenant Contacts:</label>
                <div class="col-xs-8">
                    <label for="PaidNumContacts" class="col-xs-2 control-label small no-padding-right blue"> Subscribed </label>
                    <div class="col-xs-2">
                        <select class="form-control" id="PaidNumContacts" name="PaidNumContacts">
                            <?php echo($selPaidNumContacts); ?>
                        </select>
                    </div>
                    <label for="MaxNumContacts" class="col-xs-2 control-label small no-padding-right blue"> Allowed </label>
                    <div class="col-xs-2">
                        <input type="text" class="form-control" id="MaxNumContacts"
                               name="MaxNumContacts" value="<?php if (!empty($site['MaxNumContacts'])) echo($site['MaxNumContacts']); else echo(DEFAULT_TENANTS_MAX_CONTACTS); ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="Stories" class="col-xs-4 control-label required"> Stories Above Grade:</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="Stories" name="Stories" value="<?php echo($site['Stories']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="StoriesBelowGrade" class="col-xs-4 control-label required"> Stories Below Grade:</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="StoriesBelowGrade" name="StoriesBelowGrade" value="<?php echo($site['StoriesBelowGrade']); ?>">

                </div>
            </div>
            <div class="form-group">
                <label for="SquareFootage" class="col-xs-4 control-label required"> Square Footage:</label>
                <div class="col-xs-8">
                    <input type="text" class="form-control" id="SquareFootage" name="SquareFootage" value="<?php echo($site['SquareFootage']); ?>">
                </div>
            </div>
            <div class="col-sm-offset-6">
                <button name="btnSubmit" value="Submit" class="btn btn-sm btn-success" title="Click to save">
                    <i class="fa ace-icon fa-floppy-o"></i> Click to save
                </button>
                <button name="btnCancel" value="Cancel" class="btn btn-sm js_cancel" data-href="<?= APP_URL . $XFA['return']."&id=". $site["Site_ID"]."&v=".$site['vHash']; ?>" title="Cancel, Abandon Changes">
                    <i class="fa ace-icon fa-rotate-left"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<br>
<?php $Utils->displayIconHelp(); ?>
<script>
    $(function () {
        $(".js_cancel").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();
            var href = $(this).data("href");
            window.location.href = href;
        });
        $('#PaidNumUsers').on('change', function () {
            var susers = $(this).val();
            if ( ! susers) return;
            $('#MaxNumUsers').val(susers);
        });
        $('#PaidNumContacts').on('change', function () {
            var scontacts = $(this).val();
            if ( ! scontacts) return;
            $('#MaxNumContacts').val(scontacts);
        });
    });
</script>