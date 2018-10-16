<style>
    /* Only show if > sm */
    @media (min-width: 768px) {
        .erp-right-border{
            border-right:  solid 1px black;
        }
    }
</style>
<!--  BRANDING  -->
    <strong>Admin Access Right</strong>
    <div class="muted">Access Right Editor</div>
    <hr noshade size="1">
    <div class="text-left" >
        &nbsp; <a href="<?php echo( APP_URL . $XFA['return'] ); ?>"  title="Return to List; abandon changes." >&lt;&lt; Return To List</a>
    </div>
    <div class="space-12"></div>
<!--  // BRANDING  -->
<!-- CHECK BOXES -->
<form onsubmit="//event.preventDefault();" action="<?= APP_URL . $XFA['process'] ?>&id=<?php echo($id);?>&v=<?php echo($v);?>"  class="form-horizontal" method="post" name="frmInput">
    <input type="hidden" name="inputFormSubmitted" value="1"/>
    <div class="row">
        <div class="col-xs-12">
            <fieldset>
                <legend>Access Rights for: <?php echo($output['Name']); ?></legend>
                <div class="row">
                    <?php
                    //$User_Access_Options
                    echo renderUserAccessOptions($User_Access_Options, $output['User_Access']);
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 text-center margin-top-5">
            <button name="btnSubmit" value="Submit" class="btn btn-sm btn-success" title="Click to save"><i
                        class="fa ace-icon fa-floppy-o"></i> Click to save
            </button>

            <button name="btnCancel" value="Cancel" class="btn btn-sm js_cancel"
                    data-href="<?= APP_URL . $XFA['return'] ?>" title="Cancel, Abandon Changes"><i
                        class="fa ace-icon fa-rotate-left"></i> Cancel
            </button>
        </div>
    </div>
</form>
<script>
    $(":input").keypress(function(e){
        if (e.which == '10' || e.which == '13') {
            e.preventDefault();
            var focus = $(":focusable");
            var current = focus.index(this),
                next = focus.eq(current+1).length ? focus.eq(current+1) : focus.eq(0);
            next.focus();
        }
    });

    $(function () {
        $(".js_cancel").on("click", function (e) {
            e.stopPropagation();
            e.preventDefault();

            var href = $(this).data("href");
            window.location.href = href;
        });
    });
</script>
