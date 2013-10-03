<title>Personal Information</title>
<?php
    require_once 'header.php';
    $auth = validateSession();
    echo "<div id=\"page\">";
    if($auth == true) {  
        $personId = $_GET["personId"];
        echo "<input type=\"hidden\" id=\"personId\" value=\"".$personId."\"/>";
        replaceSpecialCharecters($personId);
        $type = $_GET["type"];
        if($type == null) {                        
            $query = "SELECT person.personId, person.gender, CONCAT( firstName,' ', middleName, ' ', lastName ) AS name,familyId, registeredStages, baptismId, conformationId, marriageId, burrialId
                        FROM person
                        LEFT JOIN baptism  ON ( person.personId = baptizeeId ) 
                        LEFT JOIN conformation ON (conformation.personId = person.personId)
                        LEFT JOIN LinkedMarriage ON ( groomId = person.personId OR brideId = person.personId ) 
                        LEFT JOIN burrial ON (burrial.personId = person.personId)
                        where person.personId = '".$personId."' ";
            if($personResult = mysqli_query($con,$query)) {                
                if(mysqli_num_rows($personResult) == 0) {
                    $echoString = "No data";
                } else {     
                    while($personRow = mysqli_fetch_array($personResult)){
                        $regStages = $personRow['registeredStages'];
                        $echoString = "
                        <div  style=\"width:100%;border-color:Black;border-style:solid;\">
                        <table style=\"width:100%\">
                            <tr>
                                <th style=\"text-align:left;\">".$personRow['name']."<th>
                                <th style=\"text-align:right;\"></th>
                            </tr>
                        </table>
                        <hr>
                        <table style=\"width:50%\">".
                            (($regStages & 1) ?"
                            <tr>
                                <td>".($personRow['baptismId']?
                                "<Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Baptism Details\" onclick=\"window.location.href='retrieveRegisterRecord.php?type=BAP&id=".$personRow['baptismId']."';\"/>":"<Input  class=\"tbNoBorder10\" size=35 type=\"text\" value=\"No Baptism record available\"/>")."</td>
                            </tr>":"").(($regStages & 2) ?"
                            <tr>
                                <td>".($personRow['conformationId']?"<Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Conformation Details\" onclick=\"window.location.href='retrieveRegisterRecord.php?type=CON&id=".$personRow['conformationId']."';\"/>":"<Input  class=\"tbNoBorder10\" size=35 type=\"text\" value=\"No Conformation record available\"/>")."</td>
                            </tr>":"<tr><td><Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Add Conformation Record\" readonly/></td><td></td></tr>").
                            (($regStages & 4) ?"
                            <tr>
                                <td>".($personRow['marriageId']?"<Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Marriage Details\" onclick=\"window.location.href='retrieveRegisterRecord.php?type=MAR&id=".$personRow['marriageId']."';\"/>":"<Input  class=\"tbNoBorder10\" size=35 type=\"text\" value=\"No Marriage record available\"/>&nbsp;<Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Link to existing record\" onclick=\"javascript:DisplayModalPopup('searchdiv');\" />")."</td>
                            </tr>":"<tr><td><Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Add Marriage Record\" onclick=\"window.location.href='personDetails.php?type=MAR&personId=".$personId."';\"/>/<Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Link Existing Record\" onclick=\"javascript:DisplayModalPopup('searchdiv');\"/></td><td></td></tr>").
                            (($regStages & 8) ?"
                            <tr>
                                <td>".($personRow['burrialId']?"<Input id=\"NavBaptism\" class=\"linkButton\" type=\"button\" value=\"Burial Details\" onclick=\"window.location.href='retrieveRegisterRecord.php?type=BUR&id=".$personRow['burrialId']."';\"/>":"")."</td>
                            </tr>":"<tr><td></td><td></td></tr>")."                        
                            <tr>
                                <td><span class=\"tbNoBorder10\" id=\"back\" style=\"cursor:pointer;\" title=\"Back\"><img src=\"icons/back_arrow.png\"></span></td>
                            </tr>
                                
                        </table>
                        </div>
                        ";                        
                        retrieveMarriage($personRow['name'],$personRow['gender']);
                        
                    }
                }
            } else {
                $echoString = mysqli_error($con);
            }
            
            echo $echoString;
        } else if($type == "MAR") {
            $query = "SELECT * FROM vw_personDetail WHERE personId = '".$personId."'";            
            if($result = mysqli_query($con, $query)) {
                echo "<section id=\"FormSection\">"; 
                $row = mysqli_fetch_array($result);                          
                manualMarriageInput($row);
                echo "</section>";
            }else {
                $echoString = mysqli_error($con);
            }
            
            echo $echoString;
        }
    }
    echo "</div>";
?>
