<!DOCTYPE html>
<script type="text/javascript" src="jquery.js"></script>
<script type='text/javascript' src=javascript.js></script>
<link rel="stylesheet" type="text/css" href="css.css" />
<div style="border:1px solid black;width:100%" class="headerDiv" id="headerDiv">
    <table style="width:100%;">
    <tr><td colspan = 2><div class="MainHeader">Archdiocese Of Delhi</div></td></tr>    
    <?php
        
        include_once 'constants.php';
        if($_SESSION["userId"] == NULL) {
            echo  "
            <tr><td colspan = 2><div class=\"subHeader\">&nbsp;</div></td></tr>
            <tr><td></td>
            <td><div id=\"authLink\" style=\"text-align:right\">
            <a href=\"#\" onclick= \"javascript:DisplayModalPopup('Logindiv');\" class=\"headerLink\">Login</a></div></td></tr>";
       } else {
            echo  "
            <tr><td colspan = 2><div class=\"subHeader\">".$_SESSION["subHeading"]."</div></td></tr>
            <tr><td><div id=\"welcomeDiv\">".$_SESSION["displayName"]."</div></td>
            <td>                
                <div id=\"authLink\" style=\"text-align:right\">                    
                    <a href=\"./myProfile.php\" class=\"headerLink\">My Profile</a>
                    <a href=\"logout.php\" class=\"headerLink\">Logout</a>
                </div>
            </td></tr>";
       }   
    ?>  
</table> 
</div>
   
<?php
$perm = $_SESSION["userPermission"];  

if($perm) {
echo "<div id=cssmenu style=\"width:100%\">
<ul>
   <li class='active'><a href='./homePage.php'><span>Home</span></a></li>".($perm & 2 ? "
   <li><a href='#'><span>Registers</span></a><ul>"
   .
                            getRegister("bap", $perm,'line1').
                            getRegister("mar", $perm,'line2').
                            getRegister("bur", $perm,'line3')."</ul></li>":"").
                            getRegister("war", $perm,'line4').
                            getRegister("fam", $perm,'line5').
                            getRegister("per", $perm,'line6').
                            getRegister("usr", $perm,'line7').
                            getRegister("par", $perm,'line8').
   "
   <form id=\"search\" action=\"./search.php\" method=\"get\">
    <input type=\"text\" placeholder=\"Search records\" style=\"float:right;right:80px; \" title=\"search for records with date, id etc.\"  name=\"q\" value=\"\"><span id=nav_search_icon><img src=\"icons/search.gif\" style=\"cursor:pointer\" onclick=\"document.getElementById('search').submit();\"></span>
</form>
</ul>
</div>";
}
?>
 

