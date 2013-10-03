<html>
<head>   
</head>
    
    <title>Register-My Profile</title>
    <body>        
        <?php 
            require_once "header.php";
            $auth = validateSession();
            if($auth == true) {
                $userName = $_SESSION["userId"];
                $displayName = $_SESSION["displayName"];
                $permissions = $_SESSION["userPermission"];
                echo "                    
                    <div id=\"body\">
                    </br>
                    <div id=\"profileViewDiv\" style=\"display:inline-block;text-align:center;width:50%;border:1px solid #d1c7ac;\">
                        <table style=\"width:100%;\">
                            <thead>
                                <tr>
                                    <th colspan = 2 style=\"text-align:left\">User Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan=2><hr></td></tr>                               
                                <tr>
                                    <td class=\"innerText\">Login Id</td>
                                    <td><input id=\"userNameValue\" class=\"tbNoBorder10\" type=\"text\" size=\"50\" value=".$userName." tabindex=-1 readonly=\"readonly\"/></td>
                                </tr>
                                <tr>
                                     <td class=\"innerText\">Name</td>
                                    <td><input id=\"displayNameValue\" class=\"tbNoBorder10\" type=\"text\" size=\"50\" value=\"".$displayName."\" tabindex=-1 readonly=\"readonly\" required/></td>
                                </tr>
                                <tr>
                                    <td colspan=2><input id=\"changePassword\" type=button class=\"linkButton\" value=\"Change Password\" onclick=\"window.location.href ='./changePassword.php'\"></td>
                                </tr>
                            </tbody>
                        </table>
                        <table style=\"width:100%;\">
                            <thead>
                                <tr>
                                    <th style=\"text-align:left\">Permissions</th>
                                    <th style=\"text-align:right;display:block\"><input divName=\"expanderSection\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"+\" /></th>
                                </tr>
                            </thead>
                            <tbody>
                            
                                <tr>
                                    <td colspan = 2>
                                        <div id=\"expanderSection\" style=\"display:none;width:100%\">
                                            <hr>
                                            <table style=\"width:100%\">
                                                ".getRoleTDTRText($permissions)."
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                    <div style=\"text-align:right\">
                        <input id=\"editButton\" type=button onclick=\"javascript:editProfile(true);\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Edit\"/>
                         <input type=\"button\" id=\"saveButton\" style=\"border:1px solid #d1c7ac;display:none\" class=\"tbNoBorder10\" onclick=\"javascript:saveMyProfile();\" value=\"Save\"/>
                         <input id=\"cancelButton\" type=button onclick=\"javascript:editProfile(false);\" style=\"border:1px solid #d1c7ac;display:none\" class=\"tbNoBorder10\" value=\"Cancel\"/>
                     </div>
                     <input id=\"oldDisplayName\" type=\"hidden\" value=\"\"/>
                     <input id=\"parentUrl\" type=\"hidden\" value=\"myProfile.php\"/>                     
                </div></div></div></div>
               
              ";
            }
            mysqli_close($con);
        ?>                
    </body>
</html>
