<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-22
 * Time: 3:42 PM
 */
?>
<div class="row">
    <div class="col-xs-8 col-xs-offset-2">
        <div class="alert alert-success text-center">
            <h3 class="text-center text-bold">
                Processed!
            </h3>
            <div class="margin-bottom-10">
                You have published this client to ERP and can now view them through your admin login on the public side.
            </div>
            <div>
                <a href="<?php echo(APP_URL . $XFA['return']); ?>" class="btn btn-sm btn-default">
                    <span class="ace-icon fa fa-undo"></span>
                    Return
                </a>
            </div>
        </div>
    </div>
</div>

