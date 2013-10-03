<?php 
    include_once "constants.php";
    $auth = validateSession();
    if($auth == true) {
        $recordType = $_POST["recordType"];
        if($recordType == "ward") {              
            $wardName = $_POST["wardName"];
            $parishId = $_SESSION["parishId"];
            
            replaceSpecialCharecters($wardName);
            if($wardName == NULL || $parishId == NULL) {
                echo "Undefined values";
            } else {                
                $wardResult = mysqli_query($con,"call AddWard(".$parishId.",'".$wardName."')");
                if(mysqli_num_rows($wardResult) == 0) {
                    echo "Unkown Error. Error id : WAR-ADD";
                } else {        
                    while($wardRow = mysqli_fetch_array($wardResult)){
                        $count = $wardRow['count(*) - @count1'];
                        if($count >= 1) {
                            echo "suc";
                        } else {
                            echo "Unable to add ward.";
                        }
                    }
                }    
            }
        } else if($recordType == "Parish") {              
            $parishName = $_POST["parishName"];
            $place = $_POST["place"];
            
            replaceSpecialCharecters($parishName);
            replaceSpecialCharecters($place);
            
            if($place == NULL || $parishName == NULL) {
                echo "Undefined values";
            } else {                
                if($parishResult = mysqli_query($con,"insert into parish(parishId ,parishName ,locality ,dioceseId) VALUES (NULL ,  '".$parishName."',  '".$place."',  '0')")) {
                    echo "Parish Record added.";
                } else {
                    echo "ERROR: PAR-ADD-".mysqli_error($con);
                }
            }
        } else if($recordType == "family") {           
            $houseNo = $_POST["houseNo"];            
            $parishId = $_SESSION["parishId"];
            $wardId = $_POST["wardId"];
            $addressLine1 = $_POST["addressLine1"];
            $addressLine2 = $_POST["addressLine2"];
            $originParish = $_POST["originParish"];
            $phone = $_POST["phone"];
            replaceSpecialCharecters($houseNo);
            replaceSpecialCharecters($wardId);
            replaceSpecialCharecters($addressLine1);
            replaceSpecialCharecters($addressLine2);
             replaceSpecialCharecters($originParish);
            replaceSpecialCharecters($phone);
            if($wardId == NULL || $parishId == NULL || $houseNo == NULL || $addressLine1 == NULL || $originParish == NULL || $phone == NULL) {
                echo "Undefined values";
            } else /*if(substr_replace(explode("-",$wardId)[0], '', 0, 2) != $parishId){
                echo "Unauthorized opperation.";            
            }else*/ {
                $maxResult = mysqli_query($con, "SELECT MAX(familyId) from family where parishId = ".$parishId);
                $maxfamilyId = "";
                while($maxRow = mysqli_fetch_array($maxResult)) {
                    $maxfamilyId = $maxRow['MAX(familyId)'];
                }                              
                if($wardResult = mysqli_query($con,"call AddFamily(".$parishId.",'".$houseNo."', '".$addressLine1."', '".$addressLine2."','".$originParish."','".$phone."', '".$wardId."')")){
                    if(mysqli_num_rows($wardResult) == 0) {
                        echo "ERROR: FAM-ADD-".mysqli_error($con);
                    } else {        
                        while($wardRow = mysqli_fetch_array($wardResult)){
                            $familyId = $wardRow['MAX(familyId)'];
                            if($familyId != $maxfamilyId) {
                                echo $familyId;
                            } else {
                                echo "err";
                            }
                        }
                    }
                } else {
                    echo "ERROR: FAM-ADD1-".mysqli_error($con);
                } 
            }
        } else if($recordType == "user") {
            /*userName=Rose&password=deathhaswings&Repassword=deathhaswings&addedBy=Justin&permissions=1&accParish=11&*/
             $echoString = "";                                   
            $userName = $_POST["userName"];
            $password = $_POST["password"];
            $Repassword = $_POST["Repassword"];
            $addedBy = $_POST["addedBy"];
            $permissions = $_POST["permissions"];
            $accParish = $_POST["accParish"];
            
            if($password != $Repassword) {
                echo "ERROR: Password do not match.";                
            } else { 
                replaceSpecialCharecters($userName);
                replaceSpecialCharecters($password);
                replaceSpecialCharecters($addedBy);
                replaceSpecialCharecters($permissions);
                replaceSpecialCharecters($accParish);
                $pass = hash("sha256",$password); 
                
                $query = "INSERT INTO  ChurchDoc.User (userName ,pssword ,permissions ,displayName ,parishId ,addedBy) VALUES ('".$userName."','".$pass."',".$permissions.",".$accParish.",'".$addedBy."')";
                if($result = mysqli_query($con,$query)) {
                    echo "Successfully added User";
                } else {
                    echo "ERROR: ".mysqli_error($con);
                }
                
            }
        } else if($recordType == "marriageLink") {
        
            $marriageId= $_POST['marriageId'];
            $personId = $_POST['personId'];
            $gender = $_POST['gender'];
            replaceSpecialCharecters($marriageId);
            replaceSpecialCharecters($personId);
            replaceSpecialCharecters($gender);
            $query="call UpdateLinkedMarriage('".$marriageId."','".$personId."','".$gender."')";
            if($result = mysqli_query($con, $query)) {
                echo "Loading...<META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=personDetails.php?personId=".$personId."\">";
            } else {
                echo mysqli_error($con);
            }            
        
        } else if($recordType == "MemeberList") {
            $value = $_POST["idCounters"];                       
            $nationality = "Indian";
            $counter = 0;
            $sucCounter = 0;
            $echoString = "";                                   
            $familyId = $_POST["FamilyId"];
            $personId = $_POST["personId-".$value];
            $fName = $_POST["fName-".$value];
            $mName = $_POST["mName-".$value];
            $lName = $_POST["lName-".$value];
            $dob = $_POST["dob-".$value];
            $profsn = $_POST["prfsn-".$value];
            $prog = $_POST["prog-".$value];
            $gender = $_POST["gender-".$value];
            
            
            replaceSpecialCharecters($familyId);
            replaceSpecialCharecters($fName);
            replaceSpecialCharecters($mName);
            replaceSpecialCharecters($lName);
            replaceSpecialCharecters($dob);
            replaceSpecialCharecters($profsn);
            replaceSpecialCharecters($gender);
            echo $fName." ".$mName." ".$lName." ".$dob." ".$profsn." ".$prog." ".$gender;
            if($personId != NULL)  {
                    $query = "UPDATE  person SET profession =  '".$profsn."' WHERE  person.personId =  '".$personId."'";
                    $personResult = mysqli_query($con,$query);                   
            }else if($familyId  == NULL || $fName == NULL || $dob == NULL || $prog == NULL || $gender == NULL) {
                $echoString = "Undefined values";
            } else  {                
                $maxResult = mysqli_query($con, "SELECT MAX(personId) from person where familyId = '".$familyId."'");
                $maxPersonId = "";
                while($maxRow = mysqli_fetch_array($maxResult)) {
                    $maxPersonId = $maxRow['MAX(personId)'];
                }                              
                if($personResult = mysqli_query($con,"call AddPerson(".$_SESSION["parishId"].", '".$familyId."', '".$fName."','".$mName."','".$lName."','".$gender."','".$dob."', ".$prog.", '".$profsn."','".$nationality."')")) {
                    if(mysqli_num_rows($personResult) == 0) {
                        $echoString = "Error id : PER-ADD1 ".mysqli_error($con);
                    } else {        
                        while($personRow = mysqli_fetch_array($personResult)){
                            $personId = $personRow['MAX(personId)'];
                            if($personId == $maxPersonId) {
                                $echoString .= "Record not added";
                            }
                        }                        
                    }                    
                } else {
                    $echoString = "Error id : PER-ADD2 ".mysqli_error($con);
                }                 
            }                            
        }
    }
    mysqli_close($con);
?>
