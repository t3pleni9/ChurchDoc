<title>Search</title>
<?php
    function printTable($query, $header, $javascript, $con) {
         $returnHTML = "<table class=\"hovertable\" style=\"width:100%\"><thead><tr>";
        if($getResult = mysqli_query($con,$query)) {
            $i = 0;
            
            foreach($header as $value) {               
                    $returnHTML .= "<th>".$value."</th>";   
               
            }
            $returnHTML .= "</tr></thead><tbody>";
            if(mysqli_num_rows($getResult) == 0) {
                $returnHTML .= "<tr><td colspan=".(count($header)).">No Rows</td></tr>";
            } else {        
                while($getRow = mysqli_fetch_array($getResult)){
                    $returnHTML .= "<tr onmouseover=\"this.style.backgroundColor='#c0ffee';\" onmouseout=\"this.style.backgroundColor='#d4e3e5';\" onclick=\"".$javascript."\">";
                    for($i=0; $i < count($getRow)/2 ; $i++) {                      
                            $returnHTML .= "<td>".$getRow[$i]."</td>";                               
                    } 
                    $returnHTML .= "</tr> ";
                }
            }
        } else {
           $returnHTML .= "<tr><td colspan=".(count($header)).">".mysqli_error($con)."</td></tr>";
        } 
        $returnHTML .= "</tbody></table></div>";
        echo $returnHTML;//.mysqli_error($con);
    }
    
    require_once 'header.php';
    $auth = validateSession();
    echo "<div id=\"page\">";
    if($auth == true) {  
        $q = $_GET['q'];
        $temp = date("Y-m-d",strtotime($q));       
        if($temp == date("Y-m-d","12-12") or $temp == date("Y-m-d") ) {
            $temp = $q;
        }                
        $personId = $_GET["personId"];
        
        $baptismQuery = "SELECT  baptismId ,dateOfBaptism ,dateOfBirth,  firstName ,  lastName ,  fathersName ,  mothersName ,  placeOfBaptism 
                            FROM  vw_baptismRecord 
                            WHERE  dateOfBaptism LIKE  '%".$q."%'
                            OR  dateOfBirth LIKE  '%".$q."%'
                            OR  baptismId LIKE  '%".$q."%' OR dateOfBaptism LIKE  '%".$temp."%'
                            OR  dateOfBirth LIKE  '%".$temp."%'
                            OR  baptismId LIKE  '%".$temp."%'";
        $marriageQuery = "SELECT marriageId, marriageDate, groomName, groomSurname, brideName, brideSurname 
                            FROM marriage 
                            WHERE marriageId like '%".$q."%'
                            OR marriageDate like '%".$q."%'OR
                            marriageId like '%".$temp."%'
                            OR marriageDate like '%".$temp."%'";
                            
        $baptismHeader = array("Baptism ID", "Date Of Baptism", "Date of Birth", "First Name", "Last Name", "Father's Name", "Mother's Name", "Place Of Baptism");
        $marriageHeader = array("Marriage ID", "Date Of Marriage", "Groom's Name", "Groom's Surname", "Bride's Name", "Bride's Surname");
         
        $baptismJavascript = "javascript:navigateToRecorde(this, 'BAP');";
        $marriageJavascript = "javascript:navigateToRecorde(this, 'MAR');";
        echo"<table style=\"width:100%;\">   
                                <tr> <th colspan=2> Search Query : ".$q."</th></tr>                         
                                <tr>
                                    <th style=\"text-align:left\">Baptism Records</th>
                                    <th style=\"text-align:right;display:block\"><input divName=\"baptismExpander\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                                </tr>                            
                                <tr>
                                    <td colspan = 2>
                                        <div id=\"baptismExpander\" style=\"display:block;width:100%\"> <hr>";
        printTable($baptismQuery, $baptismHeader, $baptismJavascript, $con); 
        echo "</div></td>
                                </tr>
                                <tr>
                                    <th style=\"text-align:left\">Marriage Records</th>
                                    <th style=\"text-align:right;display:block\"><input divName=\"marriageExpander\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                                </tr>
                            </thead>
                            <tbody>
                            
                                <tr>
                                    <td colspan = 2>
                                        <div id=\"marriageExpander\" style=\"display:block;width:100%\"> <hr>";
        printTable($marriageQuery, $marriageHeader, $marriageJavascript, $con); 
       echo "</div></td></tr></table>";
    }
    mysqli_close($con);

         
    
?>

