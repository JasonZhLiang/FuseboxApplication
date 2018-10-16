<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-03-22
 * Time: 10:31 AM
 */


$reporting = [];
$reporting[] = 'Publish Pending Client to ERP Portal, activate site, primary/secondary users, de-activate pending client record';

if (empty($_GET['c']) || ! is_numeric($_GET['c'])) {
    session_write_close();
    header('Location: '. APP_URL . $XFA['return']);
    exit();
}

$client     = $_GET['c'];
$datetime   = date('Y-m-d H:i:s');
$Admin_ID   = $_SESSION['ADMIN_USER']['User_ID'];
$User_ID    = 0;
$Site_ID    = 0;


$sql = "SELECT * FROM pending_client WHERE PkID = :PkID AND DeleteFlag = 0;";

$PDOdbPending->prepare($sql);
$PDOdbPending->bind('PkID', $client);
$PDOdbPending->execute();

if ($PDOdbPending->rowCount()== 1) {
    $row = $PDOdbPending->getRow();

    $PkID			    = $row['PkID'];
    $Email			    = $row['Email'];
    $FirstName			= $row['FirstName'];
    $LastName			= $row['LastName'];
    $PriceLevel			= $row['PriceLevel'];
    $PositionTitle		= $row['PositionTitle'];
    $Phone				= $row['Phone'];
    $Ext				= $row['PhoneExt'];
    $PhoneType			= $row['PhoneType'];
    $Language			= empty($row['Lang']) ? '_EN': '_'.$row['Lang'];
    $Alt_Email			= $row['Alt_Email'];
    $Alt_FirstName		= $row['Alt_FirstName'];
    $Alt_LastName		= $row['Alt_LastName'];
    $Alt_PositionTitle	= $row['Alt_PositionTitle'];
    $Alt_Phone			= $row['Alt_Phone'];
    $Alt_Ext			= $row['Alt_PhoneExt'];
    $Alt_PhoneType		= $row['Alt_PhoneType'];
    $Alt_Language		= empty($row['Alt_Lang']) ? '_EN': '_'.$row['Alt_Lang'];
    $SiteName			= $row['SiteName'];
    $Parent_ID          = $row['Parent_ID'];
    $PropertyType       = $row['PropertyType'];
    $HasExternalCameras = $row['HasExternalCameras'];
    $SiteMatch_ID       = $row['SiteMatch_ID'];
    $SiteAddress_1		= $row['SiteAddress_1'];
    $SiteAddress_2		= $row['SiteAddress_2'];
    $SiteCity			= $row['SiteCity'];
    $SiteProvince		= $row['SiteProvince'];
    $SitePcode			= $row['SitePcode'];
    $SiteCountry		= $row['SiteCountry'];
    $SiteDescription    = $row['SiteDescription'];
    $PaidNumUsers       = $row['PaidNumUsers'];
    $MaxNumUsers        = $row['MaxNumUsers'];
    $PaidNumContacts    = $row['PaidNumContacts'];
    $MaxNumContacts     = $row['MaxNumContacts'];
    $Stories		    = $row['Stories'];
    $StoriesBelowGrade	= $row['StoriesBelowGrade'];
    $SquareFootage      = $row['SquareFootage'];
    $isCorpDomain       = empty($row['isCorpDomain']) ? false: true;
    $CorpDomain         = $row['CorpDomain'];
    $EULADate           = $row['EULADate'];
    $isBase             = $PropertyType==1; //conditional leading to boolean assignment
    $User_ID = checkEmailReturnUserID ($row);

    if ( ! empty($row['Alt_Email']) ) {
        $altUser = [
            'Email'         => $row['Alt_Email'],
            'FirstName'     => $row['Alt_FirstName'],
            'LastName'      => $row['Alt_LastName'],
            'PositionTitle' => $row['Alt_PositionTitle'],
            'Phone'         => $row['Alt_Phone'],
            'PhoneExt'      => $row['Alt_PhoneExt'],
            'PhoneType'     => $row['Alt_PhoneType'],
            'Lang'          => $row['Alt_Lang']
        ];

        $AltUser_ID = checkEmailReturnUserID ($altUser);
    } else {
        $AltUser_ID = null;
    }

    if (($isBase)&&($SiteMatch_ID)) {// TODO: MJG thinks we check if placeholder is non-zero...otherwise we could overwrite valid data??

        $sql = "UPDATE sites SET
              isPlaceholder     = 0,
              SiteName          = :SiteName,
              HasExternalCameras= :HasExternalCameras,
              SiteDescription   = :SiteDescription,
              PaidNumUsers      = :PaidNumUsers,
              MaxNumUsers       = :MaxNumUsers,
              PaidNumContacts   = :PaidNumContacts,
              MaxNumContacts    = :MaxNumContacts,
              Stories           = :Stories,
              StoriesBelowGrade = :StoriesBelowGrade,
              SquareFootage     = :SquareFootage,
              `Domain`          = :sDomain,
              PriceLevel        = :PriceLevel,
              EULADate          = :EULADate,
              EULASignedBy      = :EULASignedBy,
              PropertyType      = :PropertyType
              WHERE Site_ID = :Site_ID";

        $PDOdb->prepare($sql);
        $PDOdb->bind('SiteName', $SiteName);
        $PDOdb->bind('HasExternalCameras', $HasExternalCameras);
        $PDOdb->bind('SiteDescription', $SiteDescription);
        $PDOdb->bind('PaidNumUsers', $PaidNumUsers);
        $PDOdb->bind('MaxNumUsers', $MaxNumUsers);
        $PDOdb->bind('PaidNumContacts', $PaidNumContacts);
        $PDOdb->bind('MaxNumContacts', $MaxNumContacts);
        $PDOdb->bind('Stories', $Stories);
        $PDOdb->bind('StoriesBelowGrade', $StoriesBelowGrade);
        $PDOdb->bind('SquareFootage', $SquareFootage);
        $PDOdb->bind('sDomain', $CorpDomain);       //  2018-08-24 RoK:  using sDomain because IDE was colourizing the word Domain
        $PDOdb->bind('PriceLevel', $PriceLevel);
        $PDOdb->bind('EULADate', $EULADate);
        $PDOdb->bind('EULASignedBy', $User_ID);
        $PDOdb->bind('PropertyType', $PropertyType);
        $PDOdb->bind('Site_ID', $SiteMatch_ID);
        $PDOdb->execute();
        $Site_ID = $SiteMatch_ID;
    }else{
        if ((  ! $isBase ) && empty($Parent_ID) ){ //if not a base property and no parent id exists on site match check, insert a placeholder base building / parent
            $sql = "INSERT INTO sites SET
                  isPlaceholder     = 1,
                  Parent_ID         = 0,
                  SiteName          = :SiteName,
                  HasExternalCameras= :HasExternalCameras,
                  SiteDescription   = :SiteDescription,
                  Address_1         = :Address_1,
                  Address_2         = :Address_2,
                  City              = :City,
                  Prov              = :Prov,
                  Pcode             = :Pcode,
                  Country           = :Country,
                  isBase		    = '1'
            ";
            $PDOdb->prepare($sql);
            $PDOdb->bind('SiteName', $SiteName);
            $PDOdb->bind('HasExternalCameras', $HasExternalCameras);
            $PDOdb->bind('SiteDescription', 'Base Property - Not Registered');
            $PDOdb->bind('Address_1', $SiteAddress_1);
            $PDOdb->bind('Address_2', $SiteAddress_2);
            $PDOdb->bind('City', $SiteCity);
            $PDOdb->bind('Prov', $SiteProvince);
            $PDOdb->bind('Pcode', $SitePcode);
            $PDOdb->bind('Country', $SiteCountry);
            $PDOdb->execute();
            $Parent_ID = $PDOdb->lastInsertId();
        }

        $sql = "INSERT INTO sites SET
              SiteName          = :SiteName,
              HasExternalCameras= :HasExternalCameras,
              SiteDescription   = :SiteDescription,
              PaidNumUsers      = :PaidNumUsers,
              MaxNumUsers       = :MaxNumUsers,
              PaidNumContacts   = :PaidNumContacts,
              MaxNumContacts    = :MaxNumContacts,
              Stories           = :Stories,
              StoriesBelowGrade = :StoriesBelowGrade,
              SquareFootage     = :SquareFootage,
              `Domain`          = :sDomain,
              PriceLevel        = :PriceLevel,
              Address_1         = :Address_1,
              Address_2         = :Address_2,
              City              = :City,
              Prov              = :Prov,
              Pcode             = :Pcode,
              Country           = :Country,
              EULADate          = :EULADate,
              EULASignedBy      = :EULASignedBy,
              Parent_ID         = :Parent_ID,
              PropertyType      = :PropertyType,
		      isBase		    = :isBase
    ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('SiteName', $SiteName);
        $PDOdb->bind('HasExternalCameras', $HasExternalCameras);
        $PDOdb->bind('SiteDescription', $SiteDescription);
        $PDOdb->bind('PaidNumUsers', $PaidNumUsers);
        $PDOdb->bind('MaxNumUsers', $MaxNumUsers);
        $PDOdb->bind('PaidNumContacts', $PaidNumContacts);
        $PDOdb->bind('MaxNumContacts', $MaxNumContacts);
        $PDOdb->bind('Stories', $Stories);
        $PDOdb->bind('StoriesBelowGrade', $StoriesBelowGrade);
        $PDOdb->bind('SquareFootage', $SquareFootage);
        $PDOdb->bind('sDomain', $CorpDomain);       //  2018-08-24 RoK:  using sDomain because IDE was colourizing the word Domain
        $PDOdb->bind('PriceLevel', $PriceLevel);
        $PDOdb->bind('Address_1', $SiteAddress_1);
        $PDOdb->bind('Address_2', $SiteAddress_2);
        $PDOdb->bind('City', $SiteCity);
        $PDOdb->bind('Prov', $SiteProvince);
        $PDOdb->bind('Pcode', $SitePcode);
        $PDOdb->bind('Country', $SiteCountry);
        $PDOdb->bind('EULADate', $EULADate);
        $PDOdb->bind('EULASignedBy', $User_ID);
        $PDOdb->bind('Parent_ID', $Parent_ID);
        $PDOdb->bind('PropertyType', $PropertyType);
        $PDOdb->bind('isBase', $isBase);
        $PDOdb->execute();
        $Site_ID = $PDOdb->lastInsertId();
    }

    // Set $User_ID as primary for site in xref
    $sql = "INSERT INTO site_user_xref SET
              Site_ID     = :Site_ID,
              User_ID     = :User_ID,
              AccessLevel = '20',
              CreateDate  = :CreateDate
    ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Site_ID', $Site_ID);
    $PDOdb->bind('User_ID', $User_ID);
    $PDOdb->bind('CreateDate', $datetime);
    $PDOdb->execute();

    // set site primary as is valid TODO: TEMP FIX, SHOULD BE ON WELCOME EMAIL LINK 2018-01-17 TBS
    $sql = "UPDATE users SET
                isCorpDomain = 1,
                Verified = 1,
                VerifiedDate = NOW()
            WHERE User_ID = :User_ID
    ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('User_ID', $User_ID);
    $PDOdb->execute();
    
    if ( ! is_null($AltUser_ID) ) {
        // Set $User_ID as primary for site in xref
        $sql = "INSERT INTO site_user_xref SET
              Site_ID     = :Site_ID,
              User_ID     = :User_ID,
              AccessLevel = '30',
              CreateDate  = :CreateDate
    ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('Site_ID', $Site_ID);
        $PDOdb->bind('User_ID', $AltUser_ID);
        $PDOdb->bind('CreateDate', $datetime);
        $PDOdb->execute();

        $sql = "UPDATE users SET
                isCorpDomain = 1,
                Verified = 1,
                VerifiedDate = NOW()
            WHERE User_ID = :User_ID
    ";
        $PDOdb->prepare($sql);
        $PDOdb->bind('User_ID', $AltUser_ID);
        $PDOdb->execute();
    }



    // Set the pending client to published
    $sql = "UPDATE pending_client SET
              Status        = 'published',
              Published     = '1',
              PublishedDate = :PublishedDate,
              PublishedBy   = :PublishedBy,
              PublishedSite_ID = :PublishedSite_ID,
              PublishedUser_ID = :PublishedUser_ID
            WHERE PkID = :PkID
    ";
    $PDOdbPending->prepare($sql);
    $PDOdbPending->bind('PublishedDate', $datetime);
    $PDOdbPending->bind('PublishedBy', $Admin_ID);
    $PDOdbPending->bind('PublishedSite_ID', $Site_ID);
    $PDOdbPending->bind('PublishedUser_ID', $User_ID);
    $PDOdbPending->bind('PkID', $client);
    $PDOdbPending->execute();

} else {
    // TODO: redirect
}

$strReporting = implode('<br>', $reporting);

function checkEmailReturnUserID ($record){
    global $Utils, $PDOdb;
    $User_ID = checkEmailForUserRecord($record['Email']);
    if ( ! $User_ID ) {
        $datetime = date('Y-m-d H:i:s');
        $Admin_ID = $_SESSION['ADMIN_USER']['User_ID'];
        $password = $Utils->generatePassword();

        $hasher = new PasswordHash();

        $password = $hasher->HashPassword($password);
    
        // TODO: 2017-04-27 : HACK - making sure Lang is compliant with LANG values
        $record['Lang'] = ($record['Lang'] == 'FR') ? '_FR': '_EN';

        $sql = "INSERT INTO users SET
                  Email         = :Email,
                  Password      = :Password,
                  Lang          = :Lang,
                  LastName      = :LastName,
                  FirstName     = :FirstName,
                  PositionTitle = :PositionTitle,
                  Phone_1       = :Phone_1,
                  PhoneType_1   = :PhoneType_1,
                  PhoneExt_1    = :PhoneExt_1,
                  CreateDate    = :CreateDate,
                  CreateBy      = :CreateBy,
                  ModifyDate    = :ModifyDate,
                  ModifyBy      = :ModifyBy
        ";

        $PDOdb->prepare($sql);
        $PDOdb->bind('Email', $record['Email']);
        $PDOdb->bind('Password', $password);
        $PDOdb->bind('Lang', $record['Lang']);
        $PDOdb->bind('LastName', $record['LastName']);
        $PDOdb->bind('FirstName', $record['FirstName']);
        $PDOdb->bind('PositionTitle', $record['PositionTitle']);
        $PDOdb->bind('Phone_1', $record['Phone']);
        $PDOdb->bind('PhoneType_1', $record['PhoneType']);
        $PDOdb->bind('PhoneExt_1', $record['PhoneExt']);
        $PDOdb->bind('CreateDate', $datetime);
        $PDOdb->bind('CreateBy', $Admin_ID);
        $PDOdb->bind('ModifyDate', $datetime);
        $PDOdb->bind('ModifyBy', $Admin_ID);
        $PDOdb->execute();
        $User_ID = $PDOdb->lastInsertId();
    }

    return $User_ID;
}

function checkEmailForUserRecord ($Email) {
    global $PDOdb;
    $User_ID = false;
    // search for duplicate email in user table
    $sql = "SELECT User_ID FROM users WHERE Email = :Email AND DeleteFlag = 0;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Email', $Email);
    $PDOdb->execute();
    if ($PDOdb->rowCount() > 0) {
        $row = $PDOdb->getRow();
        $User_ID = $row['User_ID'];
    }

    return $User_ID;
}
