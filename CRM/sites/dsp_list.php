<?php
////////////////////////////////////////////////////////////
// File: dsp_loginTesting.php
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



$formInputs = [
        'City'       => '',
        'startsWith' => 'checked',
        'contains'   => ''
];
$qryConditions = '';
$errors = [];

if ($_SESSION['ADMIN_USER']['User_ID'] !== '2') {
    $qryConditions .= " AND s.isDemoSite = 1 ";
}

if ( ! empty($_POST['inputFormSubmitted']) ) {
    $formSearchCond = $_POST['form-qry-cond'];
    $City           = $_POST['City'];
    
    $formInputs['City'] = $City;
    
    // validate inputs
    $validator = new Validate_fields();
    
    $validator->add_text_field('City', $City, 'text', 'y', 255);
    
    if ( ! $validator->validation() ) {
        $errors[] = $validator->create_msg();
    }
    
    if ( ! empty($formSearchCond) && ! in_array($formSearchCond, ['S', 'C']) ) {
        trigger_error('Form tampering detected', E_USER_ERROR);
    }
    
    if (empty($errors)) {
        // build query based on search inputs
        $qryConditions = "AND City LIKE ";
        if ($formSearchCond == 'S') {
            $formInputs['startsWith'] = 'checked';
            // AND City LIKE '[term]%'
            $qryConditions .= "'".$City."%'";
        }
    
        if ($formSearchCond == 'C') {
            $formInputs['startsWith']   = '';
            $formInputs['contains']     = 'checked';
            // AND City LIKE '%[term]%'
            $qryConditions .= "'%".$City."%'";
        }
    }
    
}

// pull in sites list or search tool for sites
$sites   = [];
$tenants = [];

$sql = "SELECT
            s.Site_ID,
            s.Parent_ID,
            s.isBase,
            s.isPlaceholder,
            s.PropertyType,
            s.PriceLevel,
            s.SiteName,
            s.Address_1,
            s.City,
            COUNT(sx.User_ID) AS userCount
        FROM sites AS s
        LEFT JOIN site_user_xref AS sx ON s.Site_ID = sx.Site_ID
        WHERE
            s.DeleteFlag = 0
            ".$qryConditions."
        GROUP BY s.Site_ID
        ORDER BY
            s.Parent_ID ASC,
            s.SiteName ASC
";
$PDOdb->prepare($sql);
$PDOdb->execute();
if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        if ($row['isBase']) {
            if (empty($row['SiteName'])) {
                $row['SiteName'] = 'Placeholder for '.$row['Address_1'];
            }
            $sites[$row['Site_ID']] = $row;
        } else {
            $tenants[$row['Parent_ID']][] = $row;
        }
        $_SESSION['siteList'][$row['Site_ID']] = $row;
    }
}

$error = implode('<br>', $errors);

?>
<strong>Sites List</strong>
<div class="muted">List of Client Sites</div>
<hr>

<div class="margin-bottom-20 text-center">
    <button class="btn btn-purple btn-white btn-md" disabled>Sites</button>
    <a class="btn btn-purple" href="<?php echo(APP_URL . $XFA['listFR']); ?>">First Responders</a>
</div>
<?php if ( ! empty($error) ) { ?>
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <div class="alert alert-danger">
                <div class="row">
                    <div class="text-center">
                        <?php echo($error); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php /* ?>
<div class="row margin-bottom-20">
    <div class="col-xs-8 col-xs-offset-2">
        <form action="<?php echo(APP_URL . $XFA["process"]); ?>" method="POST" class="form-horizontal">
            <input type="hidden" name="inputFormSubmitted" value="1">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="form-group">
                        <label for="City" class="col-sm-2 col-xs-1 control-label no-padding-right">Search By City </label>
                        <div class="col-sm-6 col-xs-8">
                            <input type="text" class="col-xs-12 js_field" placeholder="City" id="City" name="City" value="<?php echo($formInputs['City']); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="text-center">
                    <label>
                        <input type="radio" class="ace" name="form-qry-cond" <?php echo($formInputs['startsWith']); ?> value="S">
                        <span class="lbl"> Starts With</span>
                    </label>
                    <label>
                        <input type="radio" class="ace" name="form-qry-cond" <?php echo($formInputs['contains']); ?> value="C">
                        <span class="lbl"> Contains</span>
                    </label>
                </div>
             </div>
            <div class="text-center">
                <button class="btn btn-xs btn-info" type="submit"><i class="fa ace-icon fa-search"></i> Go!</button>
                <button class="btn btn-xs js_clearFields"><i class="fa ace-icon fa-undo"></i> Clear</button>
            </div>
        </form>
    </div>
</div>
<?php */ ?>
<div class="row">
    <div class="col-xs-8 col-xs-offset-2">
        <table class="table">
            <tr>
                <th colspan="2">Base Site / Tenant</th>
                <th width="90">Price Level</th>
                <th width="25">Users</th>
                <th class="TblHeader text-center"><?php $trans->et('Cameras') ?></th>
                <th>City</th>
            </tr>
            <?php if ( ! empty($sites) ) { ?>
                <?php foreach ($sites as $site) {
                    $colour = $site['userCount'] > 0 ? 'badge-info': '';
                    $linkID	= "&sid=" . $site['Site_ID'] ."&vid=". md5(SEED.$site['Site_ID']);
                    $site['cameraLink'] = APP_URL . $XFA['camLink'] . $linkID;
                    ?>
                    <tr>
                        <td width="25">
                            <a href="<?php echo(APP_URL . $XFA['viewUsers'] ."&s=". $site['Site_ID'] ."&v=". md5(SEED.$site['Site_ID'])); ?>"
                               class="btn btn-xs btn-primary"
                               title="View list of users for this site"
                            >
                                <span class="ace-icon fa fa-eye"></span>
                            </a>
                        </td>
                        <td>
                            <?php echo($site['SiteName']); ?>
                        </td>
                        <td class="text-center">
                            <?php echo($site['PriceLevel']); ?>
                        </td>
                        <td>
                            <span class="badge <?php echo($colour); ?>"><?php echo($site['userCount']); ?></span>
                        </td>

                        <td class="TblRow1" width="100" align="center"><a href="<?php echo($site['cameraLink']); ?>" class="btn btn-xs btn-warning"><i class="ace-icon fa fa-video-camera"></i> </a></td>

                        <td>
                            <?php echo($site['City']); ?>
                        </td>
                    </tr>
                    <?php if (isset($tenants[$site['Site_ID']])) { ?>
                        <?php foreach($tenants[$site['Site_ID']] as $tenant) {
//                            echo "<pre>" . print_r($tenant,1) . "</pre>";
//                            die("here");
                            $colour = $tenant['userCount'] > 0 ? 'badge-info': '';
                            $linkID	= "&sid=" . $tenant['Site_ID'] ."&vid=". md5(SEED.$tenant['Site_ID']);
                            $site['cameraLink'] = APP_URL . $XFA['camLink'] . $linkID;
                            ?>
                            <tr>
                                <td>
                                </td>
                                <td>
                                    <a href="<?php echo(APP_URL . $XFA['viewUsers'] ."&s=". $tenant['Site_ID'] ."&v=". md5(SEED.$tenant['Site_ID'])); ?>"
                                       class="btn btn-xs btn-info"
                                       title="View list of users for this site"
                                    >
                                        <span class="ace-icon fa fa-eye"></span>
                                    </a>
                                    <?php echo($tenant['SiteName']); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo($tenant['PriceLevel']); ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo($colour); ?>"><?php echo($tenant['userCount']); ?></span>
                                </td>
                                <td class="TblRow1" width="100" align="center"><a href="<?php echo($site['cameraLink']); ?>" class="btn btn-xs btn-warning"><i class="ace-icon fa fa-video-camera"></i> </a></td>

                                <td><?php echo($tenant['City']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4" class="text-muted text-italic text-center">
                        No sites registered yet
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<script type="text/javascript" language="JavaScript">
    <!--
        $(function(){
            //$("#searchForm").focus();

            $("#City").focus();

            $(".js_clearFields").on("click", function(e){
                // prevent form submission
                e.preventDefault();
                e.stopPropagation();

                $(".js_field").val("");
            });

        });
    -->
</script>
