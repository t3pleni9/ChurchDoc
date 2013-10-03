<?php
    session_start();
    function CreateConnection() {
        $con = mysqli_connect("localhost","root","deathhaswings","ChurchDoc");
        return $con;
    }
    
    $con = CreateConnection();
    // create connection
    

    // Check connection
    if (mysqli_connect_errno($con)){
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }
    
    $user = $_SESSION["userId"];
    
    function replaceSpecialCharecters(&$string) {
        $search  = array('\'', '\"', ';');
        $replace = array('\\\'', '\\\"', '\;');
        $string =  str_replace($search, $replace, $string);
    }
    
    function getRoleArray() {
        $perm[1] = "Retrieve Registration Records";
        $perm[2] = "Add Registration Records";        
        $perm[4] = "Add/Retrieve parish level User";
        $perm[8] = "Remove parish level User";
        $perm[16] = "Add parish";
        $perm[32] = "Add parish Admin User";
        $perm[64] = "Remove parish Admin User";
        $perm[128] = "Add diocese level User";
        $perm[256] = "Add diocese level User";  
        
        return $perm;
    }
   
    function getRegister($formName, $perm, $id) {
        $returnText = "";
        
        $addPer = 2;
        $retrievePer = 1;
        
        $prop['bap'] = "Baptism";
        $prop['mar'] = "Marriage";
        $prop['bur'] = "Burial";
        $prop['war'] = "Ward";
        $prop['fam'] = "Family";
        $prop['per'] = "Person";
        $prop['usr'] = "User";
        $prop['par'] = "Parish";
        
        if($formName == 'bap'|| $formName == 'mar'||$formName == 'bur'){
            $addPer = 2;
            $retrievePer = 512;
            if(($perm & 2)){            
                $returnText = "<li><a href=\"./renderForm.php?cat=".$formName."&subCat=add\"><span>".$prop[$formName]."</span></a></li>";
            }
        } else if($formName == 'war'||$formName == 'fam') {
            $addPer = 2;
            $retrievePer = 1;
            if(($perm & $addPer) |($perm & $retrievePer)){
            
            $returnText = "<li><a href='#'><span>".$prop[$formName]."</span></a><ul>".
                                        (($perm & $addPer) != 0 ? "<li><a href=\"./renderForm.php?cat=".$formName."&subCat=add\"><span>Add</span></a></li>":"").
                                        (($perm & $retrievePer) != 0 ? "<li><a href=\"./renderForm.php?cat=".$formName."&subCat=ret\"><span>Retrieve</span></a></li>":"")
                                        ."                                       
                                    </ul></li>";
            }
        }else if($formName == 'per') {
             $addPer = 2;
            $retrievePer = 1;
            if(($perm & $addPer)){
            
            $returnText = "<li><a href='#'><span>".$prop[$formName]."</span></a><ul>".
                                        (($perm & $addPer) != 0 ? "<li><a href=\"./renderForm.php?cat=".$formName."&subCat=add\"><span>Add</span></a></li>":"")."                                       
                                    </ul></li>";
            }
        
        } else if($formName == 'usr') {
            $addPer = 4;
           
            if(($perm & $addPer)){            
                $returnText = "<li><a href='#'><span>".$prop[$formName]."</span></a><ul>".
                (($perm & $addPer) != 0 ? "<li><a href=\"./renderForm.php?cat=".$formName."&subCat=add\"><span>Add</span></a><li>":"")."
                </ul></li>";
            }
        } else if($formName == 'par') {
            $addPer = 16;
            $retrievePer = 1;
            if(($perm & $addPer) |($perm & $retrievePer)){
            
            $returnText = "<li><a href='#'><span>".$prop[$formName]."</span></a><ul>".
                                        (($perm & $addPer) != 0 ? "<li><a href=\"./renderForm.php?cat=".$formName."&subCat=add\"><span>Add</span></a></li>":"").
                                        (($perm & $retrievePer) != 0 ? "<li><a href=\"./renderForm.php?cat=".$formName."&subCat=ret\"><span>Retrieve</span></a></li>":"")
                                        ."                                       
                                    </ul></li>";
            }
        } 
        
        return $returnText;
    }
    
    function getRoleTDTRText($userPerm) {
        $perm = getRoleArray();        
        $clearenceLevel[1] = "Parish Level";
        $clearenceLevel[16] = "Diocese Level";
        
        $returnText = "";
        $nextLevel = 1;
        for($i = 1; $i <= 1024; $i = $i * 2) {
            if(($userPerm & $i) != 0) {
                if($i == $nextLevel) {
                    $returnText = $returnText."<tr><td class=\"innerText\">".$clearenceLevel[$i]."</td><td></td></tr>";
                    $nextLevel = 16;
                }
                
                $returnText = $returnText."<tr><td></td><td>".$perm[$i]."</td></tr>";
            }
        }
        return $returnText;
    }
    
    function validateSession() {
        if($_SESSION["userId"] == NULL) {
            echo "
            <META HTTP-EQUIV=\"refresh\" CONTENT=\"2;URL=.\">
            <div id=\"errorDiv\">Login to access this page. Redirecting to <a href=\".\">home</a> page.</div>
            ";      
            return false;          
        } else {
            $user = $_SESSION["userId"];
            return true;
        }
    }
   
    
    function getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } 
        
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
        
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $ub,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
    function addUser() {
        $sacraments = array();
        $userPermission = $_SESSION["userPermission"];
        $roles = getRoleArray();
        $j = 0;
        for($i = 2; $i < 512 ; $i *= 2) {
            if($userPermission & $i) {
                $sacraments[$j] = "<input type=checkbox value=".$i." class=sacrament />".$roles[$i];
                $j++;
            }
        }
        echo "
            <form name=\"parishNode\">
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">User Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"PerInfoExpander\" divName=\"parishInformation\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;display:inline-block;\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"parishInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=userNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"11\" value=\"User Id\" tabindex=-1 readonly/></td>
                        <td><Input id=\"userName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" placeholder=\"User ID\" form=\"submit\" form=\"submit\" required /><td>
                    </tr>                                            
                    <tr>
                        <td><input name=passwordLabel class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Password\" tabindex=-1 readonly/></td>
                        <td><Input id=\"password\" class=\"tb10\" type=\"password\" value=\"\" style=\"width:100%\" form=\"submit\" placeholder=\"password\" form=\"submit\" required /></td>
                    </tr> 
                     <tr>
                        <td><input name=passwordLabel class=\"tbNoBorder10\"  type=\"text\" size=\"31\" value=\"Re-enter Password\" tabindex=-1 readonly/></td>
                        <td><Input id=\"Repassword\" class=\"tb10\" type=\"password\" value=\"\" style=\"width:100%\" onblur=\"if(this.value != (element =document.getElementById('password')).value) { this.value = '';element.className = 'tbError10';}else{element.className = 'tb10';}\"  placeholder=\"password\" form=\"submit\" required />
                        <input id=\"addedBy\" type=\"hidden\" value=\"".$_SESSION['userId']."\" form=\"submit\">
                        </td>
                    </tr>   
                    <tr>
                        <td><input name=permissionLabel class=\"tbNoBorder10\"  type=\"text\" size=\"20\" value=\"Permissions\" tabindex=-1 readonly/></td>
                        <td><Input id=\"permissions\" type=\"hidden\" value=\"1\" form=\"submit\" /><div id=\"something\" data-targetElement=\"permissions\">
                        <ul id=\"dropdown\" style=\"font-size:12px;width:100%\" ><li>User Permissions".GenerateDropDown($sacraments)."</li></ul></div></td>
                    </tr>  
                    <tr>
                        <td><input id=accParishLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Accociated Parish\" tabindex=-1 readonly/></td>
                        <td>".getParish("ddlParish", "javascript:setValue(this.options[this.selectedIndex].value,'accParish');")."
                        <input id=\"accParish\" type=\"hidden\" value=\"\" form=\"submit\">
                        <td>    
                    </tr>                
                    <tr>
                        <td style=\"text-align:left\"></td>
                        <td colspan=2 style=\"text-align:right\">
                        <input id=\"AddUser\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                        </td>
                     </tr>  
                      
                                   
                     </table></div>
                    </td>                    
                </tr>
                <tr><td><div id=\"serverMessage\"></div></td></tr>
                </tbody>
                
            </table>
            </form>";
    
    
    }
    
    function addParish() {
         echo "
            <form name=\"parishNode\">
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Parish Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"PerInfoExpander\" divName=\"parishInformation\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;display:inline-block;\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"parishInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=parishNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"11\" value=\"Parish Name\" tabindex=-1 readonly/></td>
                        <td><Input id=\"parishName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" placeholder=\"Parish Name\" form=\"submit\" form=\"submit\" required /><td>
                    </tr>                                            
                    <tr>
                        <td><input name=PlaceLabel class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Place\" tabindex=-1 readonly/></td>
                        <td><Input id=\"place\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" placeholder=\"Place\" form=\"submit\" required /></td>
                    </tr>                   
                    <tr>
                        <td style=\"text-align:left\"></td>
                        <td colspan=2 style=\"text-align:right\">
                        <input id=\"AddParish\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                        </td>
                     </tr>                     
                     </table></div>
                    </td>                    
                </tr>
                <tr><td><div id=\"serverMessage\"></div></td></tr>
                </tbody>
                
            </table>
            </form>";
        
    
    
    }
    
   /**********************************************************
                        BAPTISM SECTION
    **********************************************************/
    
    function addPerson($endNode) {
        echo "
            <form ".(($endNode == true)?"name=\"addEndNode\"":"").">
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Personal Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"PerInfoExpander\" divName=\"personalInformation\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;display:none;\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"personalInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=fNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"11\" value=\"First Name\" tabindex=-1 readonly/></td>
                        <td><Input id=\"fName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" firstDiv=\"firstDiv\" form=\"submit\" required /><td>
                    </tr>                                            
                    <tr>
                        <td><input name=mNameLabel class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Middle Name\" tabindex=-1 readonly/></td>
                        <td><Input id=\"mName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" firstDiv=\"firstDiv\" form=\"submit\"/></td>
                    </tr>
                    <tr>
                        <td><input id=lNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"11\" value=\"Last Name\" tabindex=-1 readonly/></td>
                        <td><Input id=\"lName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" firstDiv=\"firstDiv\" form=\"submit\" required/><td>
                    </tr>                                            
                    <tr>
                        <td><input name=dobLabel class=\"tbNoBorder10\" type=\"text\" size=\"11\" value=\"Date Of Birth\" tabindex=-1 readonly/></td>
                        <td><Input id=\"dob\" class=\"tb10\" type=\"date\" value=\"".date("Y-m-d")."\"  placeholder=\"yyyy/mm/dd\" style=\"width:100%\" firstDiv=\"firstDiv\" form=\"submit\" required/></td>
                    </tr>
                    <tr>
                        <td><input name=genderLabel class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Gender\" tabindex=-1 readonly/></td>
                        <td><input id=\"genderValue\" type=\"hidden\" value=\"M\" form=\"submit\"/>
                            <select id=\"gender-0\" onchange=\"javascript:setValue(this.options[this.selectedIndex].value,'genderValue');\">
                                <option value=\"M\">Male</option>
                                <option value=\"F\">Female</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style=\"text-align:left\"></td>
                        <td colspan=2 style=\"text-align:right\">
                        <input id=\"AddNode\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                        <input id=\"editButton\" type=button style=\"border:1px solid #d1c7ac;display:none\" class=\"tbNoBorder10\" value=\"Edit\"/>
                        </td>
                     </tr>                     
                     </table></div>
                    </td>
                </tr>
                </tbody>
                
            </table>
            </form>";
        
    }
    
    function updateOtherInfo() {
     $sacraments = array("<input type=checkbox value=2 class=sacrament />Confirmation","<input type=checkbox value=4 class=sacrament />Marriage");
     echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Other Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"otherInfoExpander\" divName=\"otherInformation\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"otherInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=ministerNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Minister's Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"ministerName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=doBapLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Date Of Baptism\" tabindex=-1  readonly/></td>
                        <td><Input id=\"doBap\" class=\"tb10\" type=\"date\" value=\"".date("Y-m-d")."\" placeholder=\"yyyy/mm/dd\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>                                            
                    <tr>
                        <td><input name=otherSacramentLabel class=\"tbNoBorder10\"  type=\"text\" size=\"20\" value=\"Sacraments\" tabindex=-1 readonly/></td>
                        <td><Input id=\"prog\" type=\"hidden\" value=\"1\" form=\"submit\" /><div id=\"something\" data-targetElement=\"prog\">
                <ul id=\"dropdown\" class=\"tbNoBorder10\" style=\"font-size:12px;\" ><li>Sacraments".GenerateDropDown($sacraments)."</li></ul></div></td>
                    </tr>     
                     <tr>
                        <td><input id=remarksLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Remarks\" tabindex=-1  readonly/></td>
                        <td><textarea id=\"remarks\" class=\"tb10\" style=\"width:100%\" cols='60' rows='8'  form=\"submit\"></textarea><td>
                    </tr>                  
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"AddOther\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                        </td>
                     </tr>
                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
    
    
    function updateParents() {
     echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Parent Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"parInfoExpander\" divName=\"parentInformation\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"parentInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=fatherNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"11\" value=\"Father's Name\" tabindex=-1  readonly/></td>
                        <td><input id=\"fatherId\" type=\"hidden\" value=\"\" form=\"submit\"/>
                        <Input id=\"fatherName\" class=\"triggertb10\" type=\"text\" value=\"\" style=\"width:100%\" readonly required gender=\"M\" targetId=\"fatherId\"/><td>
                    </tr>                                            
                    <tr>
                        <td><input name=motherNameLabel class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Mother's Name\" tabindex=-1 readonly/></td>
                        <td><input id=\"motherId\" type=\"hidden\" value=\"\" form=\"submit\"/>
                        <Input id=\"motherName\" class=\"triggertb10\" type=\"text\" value=\"\" style=\"width:100%\" readonly required gender=\"F\" targetId=\"motherId\"/></td>
                    </tr>                    
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"AddParentInfo\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                         </td>
                     </tr>
                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
    
     function updateGodParents() {
     echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">God Parent Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"godParInfoExpander\" divName=\"godParentInformation\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"godParentInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=godFatherNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"God Father's Name\" tabindex=-1  readonly/></td>
                        <td>
                        <Input id=\"godFatherName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>  
                    <tr>
                        <td><input id=godFatherAddLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"God Father's Address\" tabindex=-1  readonly/></td>
                        <td><textarea id=\"godFatherAdd\" class=\"tb10\" style=\"width:100%\" cols='60' rows='8' form=\"submit\" required ></textarea><td>
                    </tr>                                            
                    <tr>
                        <td><input name=godMotherNameLabel class=\"tbNoBorder10\"  type=\"text\" size=\"21\" value=\"God Mother's Name\" tabindex=-1 readonly/></td>
                        <td>
                        <Input id=\"godMotherName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required/></td>
                    </tr>
                    <tr>
                        <td><input id=godMotherAddLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"God Mother's Address\" tabindex=-1  readonly/></td>
                        <td><textarea id=\"godMotherAdd\" class=\"tb10\" style=\"width:100%\" cols='60' rows='8' form=\"submit\" required ></textarea><td>
                    </tr>                    
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"AddGodParentInfo\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                         </td>
                     </tr>
                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
    
    function getModeOfInput($type) {
        echo "
            <div id=\"blackdiv\"></div>
            <div id=\"modediv\" style=\"border-color:Black;border-style:solid;width:25%;hieght:50%; margin-right: -10px;margin-top: 10px;\" class=\"hidecontrol\"> 
            <div style=\"text-align:right;background-color:#c3dde0;hieght:3%\" ><input type=\"button\" title=\"Create and link record manually.\" style=\"font-family:tahoma, arial, sans-serif;background-color:#333333t;border:1px solid #d1c7ac;\" onclick=\"javascript:HideModalPopup('modediv');window.location.href = 'homePage.php';\"  value=\"&#10006;\" ></div>
            <div style=\"text-align:center;width:100%\">   
                <div style=\"display:inline-block\">
                
                    <table><tr><th colSpan=2 style=\"font-family:tahoma, arial, sans-serif;color: #404853;font-size: 15px;\" >
                        Select Mode of Input
                    </th></tr><tr></tr>                    
                    <td>
                    <input id=\"manualButton\" type=\"button\" title=\"Create and link record manually.\"  onclick=\"javascript:SelectMode('MAN');javascript:HideModalPopup('modediv');\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Manual\" >
                    </td><td>
                    <input id=\"linkedButton\" type=\"button\" title=\"Create a linked record.\" onclick=\"javascript:SelectMode('LIN');javascript:HideModalPopup('modediv');\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Linked\" >
                    </td></tr></table>                    
                    <input id=\"selectedOption\" type=\"hidden\" value=\"\">                   
                </div>                             
            </div>               
            </div>   
        ";
    }
    
     function getWardList() {
        echo "
            <div id=\"blackdiv\"></div>
            <div id=\"wardListDiv\" style=\"border-color:Black;border-style:solid;width:25%;hieght:50%; margin-right: -10px;margin-top: 10px;\" class=\"hidecontrol\"> 
            <div style=\"text-align:right;background-color:#c3dde0;hieght:3%\" ><input type=\"button\" style=\"font-family:tahoma, arial, sans-serif;background-color:#333333t;border:1px solid #d1c7ac;\" onclick=\"javascript:HideModalPopup('wardListDiv');window.location.href = 'homePage.php';\"  value=\"&#10006;\" ></div>
            <div id=\"getUserDiv\" style=\"\">   
                <div id=\"requestDiv\" >    
                    <input id=\"userName\" type=\"hidden\" value=\"\"/>                
                    <input id=\"controlId\" type=\"hidden\" value=\"\">
                    <input id=\"gender\" type=\"hidden\" value=\"\">
                </div>
                <div id=\"resultDiv\" style=\"width:100%;height:224px;overflow:auto;\"></div>                
            </div>                          
            </div>   
        ";
    }
    
    function getParishList() {
        echo "
            <div id=\"blackdiv\"></div>
            <div id=\"parishListDiv\" style=\"border-color:Black;border-style:solid;width:25%;hieght:50%; margin-right: -10px;margin-top: 10px;\" class=\"hidecontrol\"> 
            <div style=\"text-align:right;background-color:#c3dde0;hieght:3%\" ><input type=\"button\" style=\"font-family:tahoma, arial, sans-serif;background-color:#333333t;border:1px solid #d1c7ac;\" onclick=\"javascript:HideModalPopup('parishListDiv');window.location.href = 'homePage.php';\"  value=\"&#10006;\" ></div>
            <div id=\"getUserDiv\" style=\"\">   
                <div id=\"requestDiv\" >    
                    <input id=\"userName\" type=\"hidden\" value=\"\"/>                
                    <input id=\"controlId\" type=\"hidden\" value=\"\">
                    <input id=\"gender\" type=\"hidden\" value=\"\">
                </div>
                <div id=\"resultDiv\" style=\"width:100%;height:224px;overflow:auto;\"></div>                
            </div>                          
            </div>   
        ";
    }
    
    /**********************************************************
                        MARRIAGE SECTION
     *********************************************************/
    function witnessInputSection($witnessNumber) {
     echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">The ".$witnessNumber." witness' Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"".$witnessNumber."InfoExpander\" divName=\"".$witnessNumber."Information\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"".$witnessNumber."Information\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=".$witnessNumber."NameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"".$witnessNumber." witness' Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$witnessNumber."Name\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=".$witnessNumber."SurNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Surname\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$witnessNumber."SurName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>                                                                                     
                    <tr>
                        <td><input name=".$witnessNumber."DomicileLabel class=\"tbNoBorder10\"  type=\"text\" size=\"21\" value=\"Domicile\" tabindex=-1 readonly/></td>
                        <td>
                        <Input id=\"".$witnessNumber."Domicile\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required/></td>
                    </tr>                                                      
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"Add".$witnessNumber."Info\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                         </td>
                     </tr>
                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";    
    
    }  
    
    function personInputSection($gender, $onclickFunction, $personResultRow) {          
        $statusText['Groom'] = array("Bachelor","Widower");
        $statusText['Bride'] = array("Spinster","Widow");  
        $searchGender['Groom'] = "M";
        $searchGender['Bride'] = "F";
        echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">".$gender."'s Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"".$gender."InfoExpander\" divName=\"".$gender."Information\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"".$gender."Information\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=".$gender."NameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"".$gender."'s Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$gender."Name\" class=\"".$onclickFunction."\" type=\"text\" value=\"".$personResultRow['firstName']." ".$personResultRow['middleName']."\" onclick=\"\" style=\"width:100%\" form=\"submit\" required gender=\"".$searchGender[$gender]."\" />
                        <Input id=\"".$gender."Id\" type=\"hidden\" value=\"".$personResultRow['personId']."\" style=\"width:100%\" form=\"submit\" /><td>
                    </tr> 
                    <tr>
                        <td><input id=".$gender."SurNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Surname\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$gender."SurName\" class=\"tb10\" type=\"text\" value=\"".$personResultRow['lastName']."\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=".$gender."FatherNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Father's Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$gender."FatherName\" class=\"tb10\" type=\"text\" value=\"".$personResultRow['fathersName']."\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=".$gender."MotherNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Mother's Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$gender."MotherName\" class=\"tb10\" type=\"text\" value=\"".$personResultRow['mothersName']."\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> ";
                        echo "
                    <tr>
                        <td><input id=".$gender."AgeLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Age\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$gender."Age\" class=\"tb10\" type=\"number\" onblur=\"if(this.value < 0) { this.value = '';}\" value=\"".(intval(date("Y")) - intval(date("Y",strtotime($personResultRow['dateOfBirth']))))."\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=".$gender."ProfessionLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Profession\" tabindex=-1  readonly/></td>
                        <td><Input id=\"".$gender."Profession\" class=\"tb10\" type=\"text\" value=\"".$personResultRow['profession']."\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>                                                                 
                    <tr>
                        <td><input name=".$gender."DomicileLabel class=\"tbNoBorder10\"  type=\"text\" size=\"21\" value=\"Domicile\" tabindex=-1 readonly/></td>
                        <td>
                        <Input id=\"".$gender."Domicile\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required/></td>
                    </tr> 
                    <tr>
                        <td><input name=".$gender."StatusLabel class=\"tbNoBorder10\"  type=\"text\" size=\"26\" value=\"Whether ".$statusText[$gender][0]." or ".$statusText[$gender][1]."\" tabindex=-1 readonly/></td>
                        <td><input id=\"".$gender."Status\" type=\"hidden\" value=\"0\" form=\"submit\"/>
                            <select id=\"".$gender."Status-0\" onchange=\"javascript:setValue(this.options[this.selectedIndex].value,'".$gender."Status');\">
                                <option value=\"0\">".$statusText[$gender][0]."</option>
                                <option value=\"1\">".$statusText[$gender][1]."</option>
                            </select>
                        </td>
                    </tr>                                    
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"Add".$gender."Info\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                         </td>
                     </tr>
                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
    
    function otherInputSection() {     
     echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Other Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"otherInfoExpander\" divName=\"otherInformation\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"otherInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=ministerNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Minister's Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"ministerName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=doMarLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Date Of Marriage\" tabindex=-1  readonly/></td>
                        <td><Input id=\"doMar\" class=\"tb10\" type=\"date\" value=\"".date("Y-m-d")."\"  placeholder=\"yyyy/mm/dd\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>                                                                    
                     <tr>
                        <td><input id=remarksLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Remarks\" tabindex=-1  readonly/></td>
                        <td><textarea id=\"remarks\" class=\"tb10\" style=\"width:100%\" cols='60' rows='8'  form=\"submit\"></textarea><td>
                    </tr>                  
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"AddOther\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                        </td>
                     </tr>                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
    
    function manualMarriageInput($personRow) {
        $gender = $personRow['gender'];        
        $tempArray['dateOfBirth']= date("Y-m-d");
        echo "        
        <div style=\"width:100%;text-align:center\">
        <div style=\"width:50%;display:inline-block\">
        <section id=groomInfoSection style=\"width:100%\">";      
        if($gender == "M") {  
            personInputSection("Groom","tb10",$personRow);                     
        } else {
            personInputSection("Groom","tb10",$tempArray);                     
        }
        echo"</section>";   
        if($gender == "F") {  
            echo "<section id=brideInfoSection style=\"width:100%;\">";              
            personInputSection("Bride","tb10",$personRow);                     
        } else {
            echo "<section id=brideInfoSection style=\"width:100%;display:none;\">";
            personInputSection("Bride","tb10",$tempArray);                     
        }            
        echo"</section>"; 
        echo "<section id=firstInfoSection style=\"width:100%;display:none;\">";
        witnessInputSection("First");                 
        echo"</section><section id=secondInfoSection style=\"width:100%;display:none;\">";   
        witnessInputSection("Second");      
        echo"</section><section id=otherInfoSection style=\"width:100%;display:none;\">";   
        otherInputSection();
        echo"</section>";
        echo"<div id=\"submitDiv\" style=\"text-align:right;width:100%;display:none\"><input type=\"button\" id=\"SubmitMarriage\" value=\"Submit Form\" >
        </div></div></div>";   
    }
    
    function linkedMarriageInput() {
        retrieveUser();
        $tempArray['dateOfBirth']= date("Y-m-d");
        echo "
        <input id=\"selectedFamilyId\" type=\"hidden\" value=\"\" > 
        <input id=\"searchType\" type=\"hidden\" value=\"getFullList\"/>
        <div style=\"width:100%;text-align:center\">
        <div style=\"width:50%;display:inline-block\">        
        <section id=groomInfoSection style=\"width:100%\">";
        personInputSection("Groom","triggertb10",$tempArray);                     
        echo"</section>";   
        echo "<section id=brideInfoSection style=\"width:100%;display:none;\">";
        personInputSection("Bride","triggertb10",$tempArray);               
        echo"</section>"; 
        echo "<section id=firstInfoSection style=\"width:100%;display:none;\">";
        witnessInputSection("First");                 
        echo"</section><section id=secondInfoSection style=\"width:100%;display:none;\">";   
        witnessInputSection("Second");
        echo"</section>"; 
        echo"</section><section id=otherInfoSection style=\"width:100%;display:none;\">";   
        otherInputSection();
        echo"</section>";
        echo"<div id=\"submitDiv\" style=\"text-align:right;width:100%;display:none\"><input type=\"button\" id=\"SubmitMarriage\" value=\"Submit Form\" >
        </div></div></div>";   
    }
    
    /*********************************************************
            B U R I A L     S E C T I O N
    *********************************************************/
    
    function burialInput() {
        retrieveUser();
        echo "<input id=\"searchType\" type=\"hidden\" value=\"burrialList\"/>
         <div style=\"width:100%;text-align:center\">
        <div style=\"width:50%;display:inline-block\">        
        <section id=personInfoSection style=\"width:100%\">";
        personBurialSection("Second");
        echo"</section>"; 
        echo"</section><section id=otherInfoSection style=\"width:100%;display:none;\">";   
        otherBurialSection();
        echo"</section>";
        echo"<div id=\"submitDiv\" style=\"text-align:right;width:100%;display:none\"><input type=\"button\" id=\"SubmitBurrial\" value=\"Submit Form\" >
        </div></div></div>"; 
    
    }
    
    function otherBurialSection() {     
        echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Other Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"otherInfoExpander\" divName=\"otherInformation\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"otherInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>                    
                    <tr>
                        <td><input id=causeLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Cause Of Death\" tabindex=-1  readonly/></td>
                        <td><Input id=\"cause\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=dateOfDeathLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Date Of Death\" tabindex=-1  readonly/></td>
                        <td><Input id=\"dateOfDeath\" class=\"tb10\" type=\"date\" value=\"".date("Y-m-d")."\" placeholder=\"yyyy/mm/dd\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=placeOfBurrialLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Place Of Burrial\" tabindex=-1  readonly/></td>
                        <td><Input id=\"placeOfBurrial\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=ministerNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Minister's Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"ministerName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=doBurLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Date Of Burrial\" tabindex=-1  readonly/></td>
                        <td><Input id=\"doBur\" class=\"tb10\" type=\"date\" value=\"".date("Y-m-d")."\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>                                                                    
                     <tr>
                        <td><input id=remarksLabel class=\"tbNoBorder10\" type=\"text\" size=\"20\" value=\"Remarks\" tabindex=-1  readonly/></td>
                        <td><textarea id=\"remarks\" class=\"tb10\" style=\"width:100%\" cols='60' rows='8'  form=\"submit\"></textarea><td>
                    </tr>                  
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"AddOther\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                        </td>
                     </tr>                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
    
    function personBurialSection() {                 
        echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Person Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"personInfoExpander\" divName=\"personInformation\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"personInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=personNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Name\" tabindex=-1  readonly/></td>
                        <td><Input id=\"personName\" class=\"triggertb10\" type=\"text\" value=\"\" onclick=\"\" style=\"width:100%\" form=\"submit\" required />
                        <Input id=\"personId\" type=\"hidden\" value=\"\" style=\"width:100%\" form=\"submit\" /><td>
                    </tr> 
                    <tr>
                        <td><input id=personSurNameLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Surname\" tabindex=-1  readonly/></td>
                        <td><Input id=\"personSurName\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>                    
                    <tr>
                        <td><input id=personAgeLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Age\" tabindex=-1  readonly/></td>
                        <td><Input id=\"personAge\" class=\"tb10\" type=\"number\" onblur=\"if(this.value < 0) { this.value = '';}\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr> 
                    <tr>
                        <td><input id=personProfessionLabel class=\"tbNoBorder10\" type=\"text\" size=\"21\" value=\"Profession\" tabindex=-1  readonly/></td>
                        <td><Input id=\"personProfession\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required /><td>
                    </tr>                                                                 
                    <tr>
                        <td><input name=personDomicileLabel class=\"tbNoBorder10\"  type=\"text\" size=\"21\" value=\"Domicile\" tabindex=-1 readonly/></td>
                        <td>
                        <Input id=\"personDomicile\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" form=\"submit\" required/></td>
                    </tr>                                                     
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"AddpersonInfo\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                         </td>
                     </tr>
                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
   
    
    function updateFamily() {
     echo "
            <form>
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Family Information</th>
                        <th style=\"text-align:right;display:block\"><input id=\"famInfoExpander\" divName=\"familyInformation\" type=button style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"expander\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"familyInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
                                     
                    <tr>
                        <td colspan=3><hr><br/>
                    </tr>
                    <tr>
                        <td><input id=addressLabel class=\"tbNoBorder10\" type=\"text\" size=\"11\" value=\"Address\" tabindex=-1  readonly/></td>
                        <td><textarea id=\"addressText\" class=\"tb10\" style=\"width:100%\" cols='60' rows='8' required readonly onclick=\"javascript:AddressTextClick();\"></textarea><td>
                    </tr>                                                                                 
                    <tr>
                        <td colspan=3 style=\"text-align:right\">
                        <input id=\"AddFamilyNode\" type=\"button\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                        <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>
                         <input id=\"editButton\" type=button style=\"border:1px solid #d1c7ac;display:none\" class=\"tbNoBorder10\" value=\"Edit\"/>
                         </td>
                     </tr>
                     
                     </table></div></td></tr></tbody>
                
            </table>
            </form>";
              
    }
        
    function retrieveUser() {
        echo "
            <div id=\"blackdiv\"></div>
            <div id=\"searchdiv\" style=\"border-color:Black;border-style:solid;width:50%;hieght:50%\" class=\"hidecontrol\"> 
            <div style=\"text-align:right;background-color:#c3dde0;hieght:3%\" ><input type=\"button\" title=\"Create and link record manually.\" style=\"font-family:tahoma, arial, sans-serif;background-color:#333333t;border:1px solid #d1c7ac;\" onclick=\"javascript:HideModalPopup('searchdiv');\"  value=\"&#10006;\" ></div>            
            <div id=\"getUserDiv\" style=\"display:none;\">   
                <div id=\"requestDiv\" >
                    <table><tr><td colspan=2>
                    <input id=\"userName\" class=\"tb10\" type=\"text\" onkeyup=\"document.getElementById('searchButton').click();\" placeholder=\"Search\" value=\"\" size=\"60\">
                    </td><td>
                    <input id=\"searchButton\" type=\"button\" onclick=\"javascript:GetUser('getUsr');\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Search\" >
                    </td></tr></table>                    
                    <input id=\"controlId\" type=\"hidden\" value=\"\">
                    <input id=\"gender\" type=\"hidden\" value=\"\">
                </div>
                <div id=\"resultDiv\" style=\"width:100%;height:224px;overflow:auto;\"></div>                
            </div>               
            </div>                             
                
        ";
    }
    
    function retrieveMarriage($personName, $gender) {
        echo "
            <div id=\"blackdiv\"></div>
            <div id=\"searchdiv\" style=\"border-color:Black;border-style:solid;width:50%;hieght:50%\" class=\"hidecontrol\"> 
            <div style=\"text-align:right;background-color:#c3dde0;hieght:3%\" ><input type=\"button\" title=\"Close.\" style=\"font-family:tahoma, arial, sans-serif;background-color:#333333t;border:1px solid #d1c7ac;\" onclick=\"javascript:HideModalPopup('searchdiv');\"  value=\"&#10006;\" ></div>            
            <div id=\"getUserDiv\">   
                <div id=\"requestDiv\" >
                    <table><tr><td colspan=2>
                    <input id=\"searchText\" class=\"tb10\" type=\"text\" onkeyup=\"document.getElementById('searchButton').click();\" placeholder=\"Marriage Date, Marriage register Number etc.\" value=\"\" size=\"60\">
                    </td><td>
                    <input id=\"searchButton\" type=\"button\" onclick=\"javascript:GetUser('getMar');\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Search\" >
                    </td></tr></table>                    
                    <input id=\"userName\" type=\"hidden\" value=\"".$personName."\">
                    <input id=\"gender\" type=\"hidden\" value=\"$gender\">
                </div>
                <div id=\"resultDiv\" style=\"width:100%;height:224px;overflow:auto;\"></div>                
            </div>               
            </div>                             
                
        ";
    }

    function retrieveFamily($onClick, $retrieve, $title = "Select Family") {
        echo "
            <div id=\"blackdiv\"></div>
            <div id=\"searchFamDiv\" style=\"border-color:Black;border-style:solid;width:50%;hieght:50%\" class=\"hidecontrol\"> 
             <div style=\"text-align:right;background-color:#c3dde0;hieght:3%\" ><input type=\"button\" title=\"Create and link record manually.\" style=\"font-family:tahoma, arial, sans-serif;background-color:#333333t;border:1px solid #d1c7ac;\" onclick=\"javascript:HideModalPopup('searchFamDiv');\"  value=\"&#10006;\" ></div>             
            <div id=\"getFamDiv\" style=\"display:none;width:100%;\">   
                <div id=\"requestDiv\" >
                    <table>
                    <tr><th colspan=3>".$title."</th></tr>
                    <tr><td>
                    <input id=\"houseNo\" class=\"tb10\" type=\"text\" placeholder=\"House No.\"value=\"\">
                    </td>
                    <td id=\"wardTd\">".getWardDropDown()."</td>".($retrieve?"
                    <td>".getParish("ddlParish", "javascript:UpdateWard('wardTd');")."<td>":"")."
                    <td>
                    <input id=\"searchFamilyButton\" type=\"button\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Search\" onclick=\"".$onClick."\" >
                    </td></tr></table>                    
                    <input id=\"selectedFamilyId\" type=\"hidden\" value=\"\" form=\"submit\" >                    
                </div>
                <div id=\"familyResultDiv\" style=\"width:100%;\"></div>                
            </div>                 
            </div>                             
                
        ";
    }
    
    function addFamily() {    
        echo " 
            <form name=\"addFamily\">   
            <table cellSpacing=\"0\" style=\"width:100%\">
                <thead>
                    <tr>
                        <th style=\"text-align:left\">Address</th>
                        <th style=\"text-align:right;display:block\"><input id=\"PerInfoExpander\" divName=\"personalInformation\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;display:none;\" value=\"-\" /></th>
                    </tr>
                <tbody>                
                    <tr>
                    <td colspan = 2>
                    <div id=\"familyInformation\" style=\"width:100%\">   
                    <table  style=\"width:100%\">
    
                        <tr>
                            <td><input name=houseNoLabel class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"House No.\" tabindex=-1 readonly/></td>
                            <td><Input id=\"newHouseNo\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" required/></td>
                        </tr>
                        <tr>
                            <td><input name=address1Label class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Address Line 1\" tabindex=-1 readonly/></td>
                            <td><Input id=\"newAddress1\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" required/></td>
                        </tr>
                         <tr>
                            <td><input name=address2Label class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Address Line 2\" tabindex=-1 readonly/></td>
                            <td><Input id=\"newAddress2\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" /></td>
                        </tr>
                        <tr>
                            <td><input name=originParishLabel class=\"tbNoBorder10\"  type=\"text\" size=\"21\" value=\"Origin Parish and Diocese\" tabindex=-1 readonly/></td>
                            <td><Input id=\"originParish\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" required /></td>
                        </tr>
                        <tr>
                            <td><input name=phoneLabel class=\"tbNoBorder10\"  type=\"text\" size=\"18\" value=\"Home Contact Number\" tabindex=-1 readonly/></td>
                            <td><Input id=\"phone\" class=\"tb10\" type=\"text\" value=\"\" style=\"width:100%\" required /></td>
                        </tr>
                        <tr>
                            <td><input name=wardLabel class=\"tbNoBorder10\"  type=\"text\" size=\"11\" value=\"Ward\" tabindex=-1 readonly/></td>
                            <td>".getWardDropDown("ddlnewFamilyWard")."</td>
                        </tr>
                    </table>
                    </div></td></tr></tbody></table></form>
        ";
    }
            
    function addWard() {
        echo "
                        
            <div id=\"wardDiv\" style=\"border-color:Black;border-style:solid;width:50%;hieght:50%;display:inline-block;\"> 
            <div id=\"getwardDiv\">   
                <div id=\"requestDiv\" >
                    <table><tr><th colspan = 2 style=\"text-align:left\">Add Ward</th></tr><tr><td colspan=2>
                    <input id=\"wardName\" class=\"tb10\" type=\"text\" placeholder=\"Ward Name\" value=\"\" size=\"60\"/>
                    </td><td>
                    <input id=\"addWard\" type=\"button\" style=\"border:1px solid #d1c7ac;display:inline-block\" onclick=\"javascript:addWard();\" class=\"tbNoBorder10\" value=\"Add\" />
                    </td></tr>
                    <tr><td><div id=\"serverResponse\" class=\"tbNoBorder10\"></div></td></tr>
                    </table>                              
                </div>                
            </div>                 
            </div>
        ";        
    }
    
    function displayFamilyDetails($familyId) {                  
        echo "<div id=\"familyInfoDiv\" style=\"width:100%;border-color:Black;border-style:solid;\">
        <input id=\"familyId\" type=hidden value=\"".$familyId."\"/>";  
        echo "<table style=\"width:100%\"><tr><th style=\"text-align:left;\">Family Information</th>
        <th style=\"text-align:right;\">
         <input id=\"famExpander\" divName=\"familyDiv\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;;\" value=\"-\" />
        </th></tr></table>";          
        echo displayFamily($familyId);                
        echo "<table style=\"width:100%\"><tr><th style=\"text-align:left;\">Member Information</th>
        <th style=\"text-align:right;\">
            <input id=\"famInfoExpander\" divName=\"membersDiv\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;;\" value=\"-\" />
        </th></tr></table>";
        echo displayFamilyMemebers($familyId);
        echo "<div style=\"text-align:right\">
        <input type=\"button\" value=\"Update\" class=\"tbNoBorder10\" style=\"border:1px solid #d1c7ac;display:none\" id=\"AddPerson\"/></div>";        
    }
    
    function addPersonForm($familyId) {
        echo "<div id=\"familyInfoDiv\" style=\"width:100%;border-color:Black;border-style:solid;\">
        <input id=\"familyId\" type=hidden value=\"".$familyId."\"/>";  
        echo "<table style=\"width:100%\"><tr><th style=\"text-align:left;\">Family Information<th><th style=\"text-align:right;\"><input id=\"famExpander\" divName=\"familyDiv\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;;\" value=\"-\" /></th></tr></table>";
        echo displayFamily($familyId);
        echo "<table style=\"width:100%\"><tr><th style=\"text-align:left;\">Member Information<th><th style=\"text-align:right;\"><input id=\"famInfoExpander\" divName=\"membersDiv\" class=\"expander\" type=button style=\"border:1px solid #d1c7ac;;\" value=\"-\" /></th></tr></table>";
        echo addFamilyMemeber($familyId);
        echo "</div>";    
    }
    
    function displayFamily($familyId) {
    
        $con = CreateConnection();
        $returnString = "";
        
        if($familyResult = mysqli_query($con, "SELECT  houseNo ,  addressLine1 ,  addressLine2 ,  originParish,contactNumber, WardName FROM  family , Ward
                                               WHERE family.ward = Ward.WardId AND familyId = '".$familyId."'")) {
            if(mysqli_num_rows($familyResult) == 0) {
                return "Unkown Error. Error id : FAM-RET1";
            } else {        
                while($familyRow = mysqli_fetch_array($familyResult)){                 
                    $returnString .= "
                        <div id=\"familyDiv\" style=\"width:100%\"><hr>
                            <table>
                                <tr>                            
                                    <td><input name=houseNoLabel class=\"tbNoBorder10\"  type=\"text\"  value=\"House No.\" tabindex=-1 readonly/></td>
                                    <td><Input id=\"houseNo\" class=\"tbNoBorder10\" type=\"text\" originalValue=\"\" size=\"50\" value=\"".$familyRow['houseNo']."\" style=\"width:100%;display:inline-block\" readonly/></td>
                                </tr>
                                <tr>
                                    <td><input name=address1Label class=\"tbNoBorder10\"  type=\"text\" value=\"Address Line 1\" tabindex=-1 readonly/></td>
                                    <td><Input id=\"address1\" class=\"tbNoBorder10\" type=\"text\" originalValue=\"\" size=\"50\" value=\"".$familyRow['addressLine1']."\" style=\"width:100%;display:inline-block\" readonly/></td>
                                </tr>
                                 <tr>
                                    <td><input name=address2Label class=\"tbNoBorder10\"  type=\"text\"  value=\"Address Line 2\" tabindex=-1 readonly/></td>
                                    <td><Input id=\"address2\" class=\"tbNoBorder10\" type=\"text\" originalValue=\"\" size=\"50\" value=\"".$familyRow['addressLine2']."\" style=\"width:100%;display:inline-block\" /readonly></td>
                                </tr>
                                 <tr>
                                    <td><input name=address2Label class=\"tbNoBorder10\"  type=\"text\" size=\"35\" value=\"Origin Parish and Diocese\" tabindex=-1 readonly/></td>
                                    <td><Input id=\"address2\" class=\"tbNoBorder10\" type=\"text\" originalValue=\"\" size=\"50\" value=\"".$familyRow['originParish']."\" style=\"width:100%;display:inline-block\" /readonly></td>
                                </tr>
                                 <tr>
                                    <td><input name=address2Label class=\"tbNoBorder10\"  type=\"text\"  value=\"Home Contact Number\" tabindex=-1 readonly/></td>
                                    <td><Input id=\"address2\" class=\"tbNoBorder10\" type=\"text\" originalValue=\"\" size=\"50\" value=\"".$familyRow['contactNumber']."\" style=\"width:100%;display:inline-block\" /readonly></td>
                                </tr>
                                <tr>
                                    <td><input name=wardLabel class=\"tbNoBorder10\"  type=\"text\" value=\"Ward\" tabindex=-1 readonly/></td>
                                    <td><Input id=\"ward\" class=\"tbNoBorder10\" type=\"text\" size=\"50\" value=\"".$familyRow['WardName']."\" style=\"width:100%;display:inline-block\"readonly /></td>
                                </tr>
                            </table></div>";
                }
            }
        }
        mysqli_close($con);
        
        return $returnString;
    }
    
    function printMemberLine($personList, $editAction = false) {
        if(mysqli_num_rows($personList) == 0) {
            $returnString .= "<tr><td colspan=8>No rows to display.</td></tr>";
        } else {        
            while($personRow = mysqli_fetch_array($personList)){   
                $regStage = $personRow['registeredStages'];      
                $sacraments = array("<input type=checkbox onclick=\"return false\" value=\"1\" ".(($regStage & 1) ? "checked":"")."/>Baptism","<input type=checkbox onclick=\"return false\" value=\"2\" ".(($regStage & 2) ? "checked":"")." />Confirmation","<input type=checkbox onclick=\"return false\" value=\"4\" ".(($regStage & 4) ? "checked":"")." />Marriage");
                $returnString .= "
                <tr ".($editAction?"onclick=\"javascript:personDetail(this)\" onmouseover=\"this.style.backgroundColor='#c0ffee';\" onmouseout=\"this.style.backgroundColor='#d4e3e5';\"":"").">
                    <td style=\"display:none;\">".$personRow['personId']."</td>
                    <td>".$personRow['firstName']."</td>
                    <td>".$personRow['middleName']."</td>
                    <td>".$personRow['lastName']."</td>
                    <td>".($personRow['gender'] == "M"?"Male":"Female")."</td>
                    <td>".date("d-M-Y",strtotime($personRow['dateOfBirth']))."</td>
                    <td>".$personRow['profession']."</td>
                    <td><div id=\"something\" data-targetElement=\"prog\" originalValue=\"".$regStage."\"><ul id=\"dropdown\" ><li>Sacraments".GenerateDropDown($sacraments)."</li></ul></div></td>".($editAction?"
                    <td><input type=\"Button\" class=\"EditRow\" title=\"Edit Row\"></td>":"<td></td>")."                     
                </tr>";
            }
        }
        return $returnString;
    }
    
    function displayFamilyMemebers($familyId) {
        $returnString = "";
        $query = "SELECT personId, firstName, middleName, lastName, gender, dateOfBirth, registeredStages, profession FROM person WHERE familyId = '".$familyId."'";        
        $con = CreateConnection();
        $personList = mysqli_query($con, $query);        
        $returnString .= "
            <form name=\"familyMemebers\">                      
            <div id=\"membersDiv\" style=\"width:100%;\"><hr>   
                <input id=\"selectedFamilyId\" type=\"hidden\" value=\"-1\"/>                
                <input id=\"count\" type=\"hidden\" value=\"-1\"/>
                <input id=\"toggleCount\" type=\"hidden\" value=\"0\"/>
                <table id=\"memberGrid\"  style=\"width:100%;\" class=\"hovertable\">
                    <tr style=\"text-align:left;\">
                        <th style=\"display:none;\">PersonId</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Date Of Birth</th>
                        <th>Profession</th>
                        <th>Advanced Details</th>  
                        <th></th>                      
                        
                    </tr>              
                ";
            if($personList) {
               $returnString .= printMemberLine($personList, true);
            } else {
                $returnString .= "<tr><td colspan=7>No rows to display.</td></tr>";
            }                    
            $returnString .= "</table>                
            </form></div>                
            ";  
            mysqli_close($con);
            return $returnString; 
    
    }
    
    function addFamilyMemeber($familyId) {
        $returnString = "";
        $query = "SELECT personId, firstName, middleName, lastName, gender, dateOfBirth, registeredStages, profession FROM person WHERE familyId = '".$familyId."'";        
        $con = CreateConnection();
        $personList = mysqli_query($con, $query);        
        $returnString .= "
            <form name=\"familyMemebers\">                      
            <div id=\"membersDiv\" style=\"width:100%;\"><hr>          
                <input id=\"count\" type=\"hidden\" value=\"0\"/>
                <table id=\"memberGrid\"  style=\"width:100%;\" class=\"hovertable\">
                    <tr style=\"text-align:left;\">
                        <th style=\"display:none;\">PersonId</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Date Of Birth</th>
                        <th>Profession</th>
                        <th>Advanced Details</th>  
                        <th></th>                      
                    </tr>              
                ";
            if($personList) {
                $returnString .= printMemberLine($personList);
            } 
            $sacraments = array("<input type=checkbox value=1 class=sacrament />Baptism","<input type=checkbox value=2 class=sacrament />Confirmation","<input type=checkbox value=4 class=sacrament />Marriage");
            $returnString .= "<tr id=\"0\">
                <td><Input id=\"fName-0\" class=\"tb10\" type=\"text\" value=\"\"  required /></td>
                <td><Input id=\"mName-0\" class=\"tb10\" type=\"text\" value=\"\" /></td>
                <td><Input id=\"lName-0\" class=\"tb10\" type=\"text\" value=\"\"  required /></td>
                <td><select id=\"gender-0\" style=\"background:transparent;\"><option value=\"M\">Male</option><option value=\"F\">Female</option></select></td>
                <td><Input id=\"dob-0\" class=\"tb10\" type=\"date\"  value=\"".date("Y-m-d")."\"  required /></td>
                <td><Input id=\"prfsn-0\" class=\"tb10\" type=\"text\" value=\"\" required /></td>
                <td><Input id=\"prog-0\" type=\"hidden\" value=\"0\" /><div id=\"something\" data-targetElement=\"prog-0\">
                <ul id=\"dropdown\" ><li>Sacraments".GenerateDropDown($sacraments)."</li></ul></div>
                </td>
                <td><input type=\"Button\" class=\"AddRow\" title=\"Add Row\"></td>
            </tr>";
                                
            $returnString .= "</table>                
            </form>                
            ";  
            mysqli_close($con);
            return $returnString;     
    }
    
    function getWardDropDown($dropDownID = "ddlWard") {
        $parishId = $_SESSION["parishId"];
        $returnString = "<select id=\"".$dropDownID."\" size=1>";
        $con = CreateConnection();
        $wardResult = mysqli_query($con,"select WardId, WardName from Ward where parishId = ".$parishId);
        if(mysqli_num_rows($wardResult) == 0) {
            //return "Unkown Error. Error id : WARD-RET1";
        } else {        
            while($wardRow = mysqli_fetch_array($wardResult)){
                $returnString = $returnString."<option value=\"".$wardRow[0]."\">".$wardRow['WardName']."</option>";
            }
        }    
        mysqli_close($con);
        $returnString = $returnString. "</select>";
        return $returnString;
    }
    
    function getWardDropDownWithParish($parishId, $dropDownID) {
        $returnString = "<select id=\"".$dropDownID."\" size=1>";
        $con = CreateConnection();
        $wardResult = mysqli_query($con,"select WardId, WardName from Ward where parishId = ".$parishId);
        if(mysqli_num_rows($wardResult) == 0) {
           // return "Unkown Error. Error id : WARD-RET1";
        } else {        
            while($wardRow = mysqli_fetch_array($wardResult)){
                $returnString = $returnString."<option value=\"".$wardRow[0]."\">".$wardRow['WardName']."</option>";
            }
        }    
        mysqli_close($con);
        $returnString = $returnString. "</select>";
        return $returnString;
    }
    
    function getParish($dropDownId, $javascript) {        
        $returnString = "<select id=\"".$dropDownId."\" onchange=\"".$javascript."\" size=1>";
        $con = CreateConnection();
        $parishResult = mysqli_query($con, "SELECT parishId, parishName, locality FROM parish");
        if(mysqli_num_rows($parishResult) == 0) {
            return "Unkown Error. Error id : WARD-RET1";
        } else {        
            while($parishRow = mysqli_fetch_array($parishResult)){
                $returnString .= "<option value=\"".$parishRow['parishId']."\" ".(($_SESSION["parishId"] == $parishRow['parishId'])?"selected":"").">".$parishRow['parishName'].", ".$parishRow['locality']."</option>";
            }
        }    
        mysqli_close($con);
        $returnString .= "</select>";
        return $returnString;    
    }
    
    function addFamilyMembers() {
        $sacraments = array("<input type=checkbox value=1 class=sacrament />Baptism","<input type=checkbox value=2 class=sacrament />Confirmation","<input type=checkbox value=4 class=sacrament />Marriage");
        echo "<Input id=\"AddFamilyMemebers\" style=\"text-decoration: underline;cursor:pointer;background:transparent\" class=\"tbNoBorder10\" type=\"button\" value=\"Add Family members\"  readonly />
            <form name=\"familyMemebers\">                      
            <div id=\"membersDiv\" style=\"width:100%;display:none;\">   
                <input id=\"selectedFamilyId\" type=\"hidden\" value=\"-1\"/>
                <input id=\"expectedCounts\" type=\"hidden\" value=\"\"/>        
                <input id=\"count\" type=\"hidden\" value=\"-1\"/>
                <table id=\"memberGrid\"  style=\"width:100%;\" class=\"hovertable\">
                    <tr style=\"text-align:left;\">
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Date Of Birth</th>
                        <th>Profession</th>
                        <th>Advanced Details</th>
                        <th></th>
                    </tr>
                    <tr id=\"0\">
                        <td><Input id=\"fName-0\" class=\"tb10\" type=\"text\" value=\"\"  required /></td>
                        <td><Input id=\"mName-0\" class=\"tb10\" type=\"text\" value=\"\" /></td>
                        <td><Input id=\"lName-0\" class=\"tb10\" type=\"text\" value=\"\"  required /></td>
                        <td><select id=\"gender-0\" style=\"background:transparent;\"><option value=\"M\">Male</option><option value=\"F\">Female</option></select></td>
                        <td><Input id=\"dob-0\" class=\"tb10\" type=\"date\" value=\"\"  required /></td>
                        <td><Input id=\"prfsn-0\" class=\"tb10\" type=\"text\" value=\"\" required /></td>
                        <td><Input id=\"prog-0\" type=\"hidden\" value=\"0\" /><div id=\"something\" data-targetElement=\"prog-0\">
                        <div id=\"dropdown\"><ul id=\"dropdown\" ><li>Sacraments".GenerateDropDown($sacraments)."</li></ul></div>
                        </td>
                        <td><input type=\"Button\" class=\"AddRow\" title=\"Add Row\"></td>
                    </tr>                    
                </table>                
                </form></div>                
                ";   
    
    }
    
    function GenerateDropDown($valueArray) {
        $returnString = "<ul>";
        foreach($valueArray as $value) {
            $returnString .= "<li>".$value."</li>";
        }
        $returnString .= "</ul>";
        return $returnString;
    }
    /*
                        <tr>
                            <td style=\"text-align:left\">".
                                (($endNode == true)?
                                "<input type=\"button\" id=\"back\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Back\" onclick=\"javascript:navigation('addFamilyDiv','getFamDiv');\"/>":
                                "").
                            "</td>
                            <td colspan=2 style=\"text-align:right\">
                                 <input id=\"AddFamily\" type=\"button\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Add\"/>
                                 <input type=\"reset\" id=\"Clear\" style=\"border:1px solid #d1c7ac;display:inline-block\" class=\"tbNoBorder10\" value=\"Clear\"/>".
                                (($endNode == true)?"":
                                "<input id=\"editButton\" type=button onclick=\"javascript:editProfile(true);\" style=\"border:1px solid #d1c7ac;display:none\" class=\"tbNoBorder10\" value=\"Edit\"/>").
                            "</td>
                        </tr>*/
    
?>
