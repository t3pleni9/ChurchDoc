<?php 
    include_once "constants.php";
    $auth = validateSession();
    if($auth == true) {
        $recordType = $_POST["type"];        
        if($recordType == "BAP"){     
            $fName = $_POST["fName"];
            $mName = $_POST["mName"];
            $lName = $_POST["lName"];
            $dob = $_POST["dob"];
            $gender = $_POST["genderValue"];
            $familyId = $_POST["selectedFamilyId"];
            $fatherId = $_POST["fatherId"];
            $motherId = $_POST["motherId"];
            $godFatherName = $_POST["godFatherName"];
            $godFatherAddress = $_POST["godFatherAdd"];
            $godMotherName = $_POST["godMotherName"];
            $godMotherAddress = $_POST["godMotherAdd"];
            $ministerName = $_POST["ministerName"];
            $dateOfBap = $_POST["doBap"];
            $prog = $_POST["prog"];
            $remarks = $_POST["remarks"];
            
            replaceSpecialCharecters($fName);
            replaceSpecialCharecters($mName);
            replaceSpecialCharecters($lName);
            replaceSpecialCharecters($dob);
            replaceSpecialCharecters($gender);
            replaceSpecialCharecters($familyId);
            replaceSpecialCharecters($fatherId);
            replaceSpecialCharecters($motherId);
            replaceSpecialCharecters($godFatherName);
            replaceSpecialCharecters($godFatherAddress);
            replaceSpecialCharecters($godMotherName);
            replaceSpecialCharecters($godMotherAddress);
            replaceSpecialCharecters($ministerName);
            replaceSpecialCharecters($dateOfBap);
            replaceSpecialCharecters($prog);
            replaceSpecialCharecters($remarks);
            $nationality ="";
            $profsn = "";
            $personId = "";
            
            if($familyId  == NULL || $fName == NULL || $dob == NULL || $prog == NULL || $gender == NULL) {
                $echoString = "Undefined values";
            } else  {        
                $nationalityResult =  mysqli_query($con, "SELECT nationality from person where personId = '".$fatherId."'"); 
                if($nationalityResult) {
                    while($nationalityRow = mysqli_fetch_array($nationalityResult)) {
                        $nationality = $nationalityRow['nationality'];
                    }
                }
                $parishId = $_SESSION["parishId"];
                if($parishIdResult = mysqli_query($con, "SELECT parishId from family where familyId = '".$familyId."'")) {
                    while($parishRow = mysqli_fetch_array($parishIdResult)) {
                        $parishId = $parishRow['parishId'];
                    }                 
                }
                
                $maxResult = mysqli_query($con, "SELECT MAX(personId) from person where familyId = '".$familyId."'");
                $maxPersonId = "";
                while($maxRow = mysqli_fetch_array($maxResult)) {
                    $maxPersonId = $maxRow['MAX(personId)'];
                }                              
                if($personResult = mysqli_query($con,"call AddPerson(".$parishId.", '".$familyId."', '".$fName."','".$mName."','".$lName."','".$gender."','".$dob."', ".$prog.", '".$profsn."','".$nationality."')")) {
                    if(mysqli_num_rows($personResult) == 0) {
                        $echoString = "Error id : REC-ADD-BAP1-  ".mysqli_error($con);
                    } else {        
                        while($personRow = mysqli_fetch_array($personResult)){
                            $personId = $personRow['MAX(personId)'];
                            if($personId == $maxPersonId) {
                                $echoString .= "Record not added";
                            }
                        }                        
                    }                    
                }  
                mysqli_close($con);
                $con = CreateConnection();                
                
                if($baptismResult = mysqli_query($con,"call AddBaptismRecord(".$_SESSION["parishId"].", ".date("Y").", '".$personId."', '".$dateOfBap."',  '".$fatherId."',  '".$motherId."',  '".$godFatherName."',  '".$godFatherAddress."',  '".$godMotherName."',  '".$godMotherAddress."',  '".$ministerName."',  '".$remarks."' )")) {
                    if(mysqli_num_rows($baptismResult) == 0) {
                        $echoString = "Error id : BAP-ADD1 ".mysqli_error($con);
                    } else {        
                        while($baptismRow = mysqli_fetch_array($baptismResult)){
                            $baptismId = $baptismRow['@concatValue'];                            
                            if($baptismId == NULL) {
                                $echoString .= "Record not added";
                            } else {
                                $echoString = "Loading...<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=retrieveRegisterRecord.php?type=BAP&id=".$baptismId."\">";
                            }
                        }                        
                    }                    
                } else {
                    $echoString = "Error id : REC-ADD-BAP2- ".mysqli_error($con);          
                }
            }    
        } else if($recordType == "BUR"){   
        
        /*personName=a a&personId=PE1-3-5&personSurName=a&personAge=0&personProfession=q&personDomicile=q&cause=q&dateOfDeath=2013-08-21&place
OfBurrial=q&ministerName=q&doBur=2013-08-21&remarks=&*/
                  
            $personId = $_POST["personId"];
            $doBur = $_POST["doBur"];
            $personDomicile = $_POST["personDomicile"];
            $cause = $_POST["cause"];
            $dateOfDeath = $_POST["dateOfDeath"];
            $placeOfBurrial = $_POST["placeOfBurrial"];           
            $ministerName = $_POST["ministerName"];           
            $remarks = $_POST["remarks"];
            //var_dump($_POST);
            
            replaceSpecialCharecters($personId);
            replaceSpecialCharecters($doBur);
            replaceSpecialCharecters($personDomicile);
            replaceSpecialCharecters($cause);           
            replaceSpecialCharecters($dateOfDeath);
            replaceSpecialCharecters($placeOfBurrial);
            replaceSpecialCharecters($ministerName);
            replaceSpecialCharecters($remarks);
            
            if($personId  == NULL || $doBur == NULL || $personDomicile == NULL || $dateOfDeath == NULL || $cause == NULL || $placeOfBurrial == NULL) {
                $echoString = "Undefined values";
            } else  {  
                if($burialResult = mysqli_query($con,"call AddBurialRecord(".$_SESSION["parishId"].", ".date("Y").", '".$personId."', '".$doBur."',  '".$personDomicile."',  '".$cause."',  '".$placeOfBurrial."',  '".$dateOfDeath."','".$ministerName."',  '".$remarks."')")) {
                    if(mysqli_num_rows($burialResult) == 0) {
                        $echoString = "Error id : BUR-ADD1 ".mysqli_error($con);
                    } else {        
                        while($burrialRow = mysqli_fetch_array($burialResult)){
                            $burialId = $burrialRow['@concatValue'];                            
                            if($burialId == NULL) {
                                $echoString .= "Record not added";
                            } else {
                                mysqli_close($con);
                                $con = CreateConnection();    
                                mysqli_query($con, "call UpdateRegisteredStages('".$personId."',8)");
                                
                                $echoString = "Loading...<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=retrieveRegisterRecord.php?type=BUR&id=".$burialId."\">";
                            }
                        }                        
                    }                    
                } else {
                    $echoString = "Error id : REC-ADD-BUR2- ".mysqli_error($con);          
                }
            }    
        } else if($recordType == "MAR") {   
            
        
            $GroomName = $_POST["GroomName"];
            $GroomId = $_POST["GroomId"];
            $GroomSurName = $_POST["GroomSurName"];
            $GroomFatherName = $_POST["GroomFatherName"];
            $GroomMotherName = $_POST["GroomMotherName"];
            $GroomAge = $_POST["GroomAge"];
            $GroomProfession = $_POST["GroomProfession"];
            $GroomDomicile = $_POST["GroomDomicile"];
            $GroomStatus = $_POST["GroomStatus"];
            $BrideName = $_POST["BrideName"];
            $BrideId = $_POST["BrideId"];
            $BrideSurName = $_POST["BrideSurName"];
            $BrideFatherName = $_POST["BrideFatherName"];
            $BrideMotherName = $_POST["BrideMotherName"];
            $BrideAge = $_POST["BrideAge"];
            $BrideProfession = $_POST["BrideProfession"];
            $BrideDomicile = $_POST["BrideDomicile"];
            $BrideStatus = $_POST["BrideStatus"];
            $FirstDomicile = $_POST["FirstDomicile"];
            $FirstName = $_POST["FirstName"];
            $FirstSurName = $_POST["FirstSurName"];
            $SecondName = $_POST["SecondName"];
            $SecondSurName = $_POST["SecondSurName"];
            $SecondDomicile = $_POST["SecondDomicile"];
            $ministerName = $_POST["ministerName"];
            $doMar = $_POST["doMar"];            
            $remarks = $_POST["remarks"];
            
            
            
            replaceSpecialCharecters($GroomName);
            replaceSpecialCharecters($GroomId);
            replaceSpecialCharecters($GroomSurName);
            replaceSpecialCharecters($GroomFatherName );
            replaceSpecialCharecters($GroomMotherName);
            replaceSpecialCharecters($GroomAge);
            replaceSpecialCharecters($GroomProfession);
            replaceSpecialCharecters($GroomDomicile);
            replaceSpecialCharecters($GroomStatus);
            replaceSpecialCharecters($BrideName);
            replaceSpecialCharecters($BrideId);
            replaceSpecialCharecters($BrideSurName);
            replaceSpecialCharecters($BrideFatherName);
            replaceSpecialCharecters($BrideMotherName);
            replaceSpecialCharecters($BrideAge);
            replaceSpecialCharecters($BrideProfession);
            replaceSpecialCharecters($BrideDomicile);
            replaceSpecialCharecters($BrideStatus);
            replaceSpecialCharecters($FirstDomicile);
            replaceSpecialCharecters($FirstName);
            replaceSpecialCharecters($FirstSurName);
            replaceSpecialCharecters($SecondName);
            replaceSpecialCharecters($SecondSurName);
            replaceSpecialCharecters($SecondDomicile);
            replaceSpecialCharecters($ministerName);
            replaceSpecialCharecters($doMar);
            replaceSpecialCharecters($remarks);
            
            $nationality ="";
            $profsn = "";
            $marriageId = "";
            
            if($GroomName  == NULL || $GroomSurName == NULL || $BrideName == NULL || $BrideSurName == NULL || $doMar == NULL) {
                $echoString = "Undefined values";
            } else  {        
                $query = "call AddMarriageRecord(".$_SESSION["parishId"].",".date("Y").", '".$GroomName."', '".$GroomSurName."','".$GroomFatherName."', '".$GroomMotherName."',".$GroomAge.",'".$GroomDomicile."','".$GroomProfession."', ".$GroomStatus.",'".$BrideName."', '".$BrideSurName."','".$BrideFatherName."','".$BrideMotherName."',".$BrideAge.",'".$BrideDomicile."','".$BrideProfession."','".$BrideStatus."','', '".$doMar."', '".$FirstName."', '".$FirstSurName."' , '".$FirstDomicile."', '".$SecondName."', '".$SecondSurName."', '".$SecondDomicile."' , '".$ministerName."', '".$remarks."')";
                if($marriageResult = mysqli_query($con, $query)) {
                    while($marriageRow = mysqli_fetch_array($marriageResult)){
                        $marriageId = $marriageRow['@concatValue'];
                    }                    
                    if($marriageId != null) {
                        $echoString = "Loading...<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=retrieveRegisterRecord.php?type=MAR&id=".$marriageId."\"/>";
                    }
                    
                } else {
                    $echoString = "Error id : REC-ADD-MAR1- ".mysqli_error($con);    
                }                
                
                if($GroomId != null || $BrideId != null) {                  
                
                    mysqli_close($con);
                    $con = CreateConnection();                
                    $query = "INSERT INTO ChurchDoc.LinkedMarriage (marriageId, groomId, brideId) VALUES ('".$marriageId."', '".$GroomId."', '".$BrideId."')";
                    if($insertResult = mysqli_query($con, $query)) {
                        mysqli_query($con, "call UpdateRegisteredStages('".$GroomId."',4)");
                        mysqli_close($con);
                        $con = CreateConnection();    
                        mysqli_query($con, "call UpdateRegisteredStages('".$BrideId."',4)");
                    } else {
                        $echoString = "Error id : REC-ADD-MAR2- ".mysqli_error($con); 
                    }
                }             
            }   
        }      
        echo $echoString;
    }
    mysqli_close($con);
?>
