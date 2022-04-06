<?php 
    require("Database.php");
	function InitPage($CodePage, $Title, $CheckUser = 0) {
        if($CheckUser == 2) CheckRole($CodePage);
        else if($CheckUser == 1) if(!isset($_COOKIE["ZeroIntranet"])) header('Location: ./Module/Login/Index.php?URL='.$CodePage); 
        $HTMLStringTitle = " <!DOCTYPE html>
                            <html>
                            <head>
                                <title>$Title</title>
                                <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
                                <script src=\"./Module/dhtmlx/codebase/dhtmlx.js\" type=\"text/javascript\"></script> 
                                <link rel=\"STYLESHEET\" type=\"text/css\" href=\"./Module/dhtmlx/skins/skyblue/dhtmlx.css\">   
                                <script src=\"./Module/JS/jquery-1.10.1.min.js\"></script> 
                                <link rel=\"icon\" href=\"./Module/Images/Logo.ico\" type=\"image/x-icon\">
                            </head>
                            <style>
                                html, body {
                                    width: 100%;
                                    height: 100%;
                                    padding: 0;
                                    margin: 0;
                                    font-family: \"Source Sans Pro\",\"Helvetica Neue\",Helvetica;
                                    background-repeat: no-repeat;
                                    background-size: 100%;
                                }
                            
                            </style>";
                            
        $HTMLStringScript = '
            <script>
                '.GetHeaderTitle($CodePage).'
                var MainMenu;
                var ToolbarMain;
                var CopyClipBoard, CT;
                var TitleHeader = "' . $Title . '";
                $(document).ready(function(){
                    $("body").html("<div style=\"height: 30px;background:#205670;font-weight:bold\"><div id=\"menuObj\"></div></div><div style=\"position:absolute;width:100%;top:35;background:white\"><div id=\"ToolbarBottom\"></div></div>" + $("body").html());

                    MainMenu = new dhtmlXMenuObject({
                            parent: "menuObj",
                            icons_path: "./Module/dhtmlx/common/imgs_Menu/",
                            json: "./Module/MenuBar.php",
                            top_text: HeaderTile
                    });
        
                    MainMenu.attachEvent("onClick", function(id, zoneId, cas){
                        if(id != "Logo")
                        {
                            location.href = "./Module/Redirect.php?PAGE=" + id;
                        } else
                        {
                            location.href = "/Index.php";
                        }
                    });

                    ToolbarMain = new dhtmlXToolbarObject({
                        parent: "ToolbarBottom",
                        icons_path: "./Module/dhtmlx/common/imgs/",
                        align: "left",
                    });

                    ToolbarMain.addText("Title", null, "<a style=\'font-size:20pt;font-weight:bold\'>'. $Title .'</a>");
                    ToolbarMain.addSeparator("Space", null);
                    ToolbarMain.addSpacer("Title");
                    
                    document.addEventListener(\'paste\',function (event) {
                        CT = event.clipboardData.types;
                        CopyClipBoard = event.clipboardData.getData(\'Text\');
                    });

                    DocumentStart();
                });
                

                String.prototype.replaceAll = function(search, replacement) {
                    if(this.indexOf(search) !== -1)
                    {
                        var target = this;
                        return target.split(search).join(replacement);
                    } else return this;
                    
                };


                Date.prototype.addDate = function(n){
                    this.setDate(this.getDate() + n);
                    return this;
                };

                function download(filename, text) {
                    var element = document.createElement(\'a\');
                    var universalBOM = "\uFEFF";
                    element.setAttribute(\'href\', \'data:text/plain;charset=utf-8,\' + encodeURIComponent(universalBOM + text));
                    element.setAttribute(\'download\', filename);
                    element.style.display = \'none\';
                    document.body.appendChild(element);
                    element.click();
                    document.body.removeChild(element);
                }

                function getUrl(sParam) {
                    var sPageURL = window.location.search.substring(1),
                        sURLVariables = sPageURL.split(\'&\'),
                        sParameterName,
                        i;
            
                    for (i = 0; i < sURLVariables.length; i++) {
                        sParameterName = sURLVariables[i].split(\'=\');
            
                        if (sParameterName[0] === sParam) {
                            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                        }
                    }
                };

                function AjaxAsync(urlsend,dtsend,typeSend = "GET", datatype = "html") {
                    var it_works;

                    $.ajax({
                        url: urlsend,
                        type: typeSend.toUpperCase(),
                        dataType: datatype.toUpperCase(),
                        cache: false,
                        data: dtsend,
                        success: function(string){	
                            it_works = string;
                        },
                        error: function (){
                            it_works = \'ERROR\';
                        },
                        async: false
                    });
                    return it_works;
                }

                function AjaxNonAsync(urlsend,dtsend,typeSend = "GET", datatype = "html") {
                    $.ajax({
                        url: urlsend,
                        type: typeSend.toUpperCase(),
                        dataType: datatype.toUpperCase(),
                        cache: false,
                        data: dtsend,
                        success: function(string){	
                            console.log(string);
                        },
                        error: function (){
                            console.log("Error");
                        },
                        async: true
                    });
                }

                
                function getCookie(cname) {
                    var name = cname + "=";
                    var decodedCookie = decodeURIComponent(document.cookie);
                    var ca = decodedCookie.split(\';\');
                    for(var i = 0; i <ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == \' \') {
                            c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                            return c.substring(name.length, c.length);
                        }
                    }
                    return "";
                }
                
                Date.prototype.yyyymmdd = function() {
                    var mm = this.getMonth() + 1; // getMonth() is zero-based
                    var dd = this.getDate();
                    var hh = this.getHours();
                    var MM = this.getMinutes();
        
                    return [this.getFullYear(),(mm>9 ? \'\' : \'0\') + mm,(dd>9 ? \'\' : \'0\') + dd
                            ].join(\'-\') + " " + [(hh>9 ? \'\' : \'0\') + hh,(MM>9 ? \'\' : \'0\') + MM].join(\':\');
                };

                function copyTextToClipboard(text) {
                    var textArea = document.createElement("textarea");
            
                    //
                    // *** This styling is an extra step which is likely not required. ***
                    //
                    // Why is it here? To ensure:
                    // 1. the element is able to have focus and selection.
                    // 2. if element was to flash render it has minimal visual impact.
                    // 3. less flakyness with selection and copying which **might** occur if
                    //    the textarea element is not visible.
                    //
                    // The likelihood is the element won\'t even render, not even a
                    // flash, so some of these are just precautions. However in
                    // Internet Explorer the element is visible whilst the popup
                    // box asking the user for permission for the web page to
                    // copy to the clipboard.
                    //
            
                    // Place in top-left corner of screen regardless of scroll position.
                    textArea.style.position = "fixed";
                    textArea.style.top = 0;
                    textArea.style.left = 0;
            
                    // Ensure it has a small width and height. Setting to 1px / 1em
                    // doesn\"t work as this gives a negative w/h on some browsers.
                    textArea.style.width = "2em";
                    textArea.style.height = "2em";
            
                    // We don"t need padding, reducing the size if it does flash render.
                    textArea.style.padding = 0;
            
                    // Clean up any borders.
                    textArea.style.border = "none";
                    textArea.style.outline = "none";
                    textArea.style.boxShadow = "none";
            
                    // Avoid flash of white box if rendered for any reason.
                    textArea.style.background = "transparent";
            
            
                    textArea.value = text;
            
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
            
                    try {
                        var successful = document.execCommand("copy");
                        var msg = successful ? "successful" : "unsuccessful";
                        console.log("Copying text command was " + msg);
                    } catch (err) {
                        console.log("Oops, unable to copy");
                    }
            
                    document.body.removeChild(textArea);
                }

                dhtmlXGridObject.prototype.AddClipBoard=async function(){
                    await sleep(150);
                    var LastRow = this.getRowsNum();
                    var LastColumn = this.getColumnsNum();
                    var top_row = this.getSelectedBlock().LeftTopRow;
                    var IndexRow = this.getRowIndex(top_row);
                    var bottom_row = this.getSelectedBlock().RightBottomRow;
                    var left_column = this.getSelectedBlock().LeftTopCol;
                    var right_column = this.getSelectedBlock().RightBottomCol;
                    var clipText = CopyClipBoard;
                    var RowData = clipText.split("\r\n");
                    var newId = (new Date()).valueOf();
                    if(LastColumn - left_column < RowData[0].split("\t").length) {
                        dhtmlx.alert("Cột nhiều hơn, vui lòng dán đúng vị trí");
                        return;
                    } else {
                        var TurnAdd = false;
                        var SplitString = "";
                        var StringLength = 0;
                        var ArrAdd = [];
                        var IS = 1;
                        var EX = ["text/plain", "text/html", "text/rtf", "Files"];
                        if(EX.length == CT.length) {
                            for(var x = 0; x < CT.length; x++) {
                                if(EX[x] != CT[x]) {
                                    IS = 1;
                                    break;
                                }
                            }
                        } else {
                            IS = 0;
                        }
                        for(var i = 0; i < RowData.length - IS; i++){
                            SplitString = RowData[i].split("\t");
                            ArrAdd = [];
                            if(IndexRow + i == this.getRowsNum() && i != 0) {
                                for(var t = 0; t < left_column; t++) ArrAdd.push("");
                                for(var j = 0; j < SplitString.length; j++) {
                                    if(SplitString[j][0] == \'"\' && SplitString[j][SplitString[j].length - 1] == \'"\') {
                                        SplitString[j] = SplitString[j].substring(1,SplitString[j].length - 2);
                                    }
                                    ArrAdd.push(SplitString[j]);
                                }
                                this.addRow(this.getRowsNum() + 1,ArrAdd);
                                TurnAdd = true;
                            } else {
                                for(var j = 0; j < SplitString.length; j++) {
                                    if(SplitString[j][0] == \'"\' && SplitString[j][SplitString[j].length - 1] == \'"\') {
                                        SplitString[j] = SplitString[j].substring(1,SplitString[j].length - 2);
                                    }
                                    this.cells2(IndexRow + i,left_column + j).setValue(SplitString[j]);
                                }
                            }
                        }
                        if(TurnAdd) this.addRow(this.getRowsNum() + 1,[""]);
                    }
                }
                
                function sleep(ms) {
                    return new Promise(resolve => setTimeout(resolve, ms));
                }

                dhtmlXGridObject.prototype.SelectCellEditor=function(){
                    if(this.editor && this.editor.obj){
                        this.editor.obj.select();
                    }
                };
            </script>';
            echo $HTMLStringTitle . $HTMLStringScript;
    }

    function CheckRole($KeyUrl){
        
        if(!isset($_COOKIE["ZeroIntranet"])) header('Location: ./Module/Login/Index.php?URL='.$KeyUrl); 
        else {
			$row = MiQueryScalar("SELECT Username FROM intranet.intranet_permission WHERE Username = '" . $_COOKIE["ZeroIntranet"] . "' AND KeyURL = '$KeyUrl' AND Active = 1 LIMIT 1");
			if($row != "") header('Content-type: text/html; charset=utf-8');
			else header('Location: /Index.php?NotPer=true');
		}
	}
    
    function getAutomailUpdated()
    {
        $result = 'loading...';
        $data = MiQuery("SELECT `STATUS`, `CREATEDDATE` FROM autoload_log ORDER BY ID DESC;", _conn1('au_avery') );
        if (!empty($data[0]) ) {
            $data = $data[0];
            $status = $data['STATUS'];
            $created_date = $data['CREATEDDATE'];

            if ($status == 'OK' ) {
                $result = $created_date;
            } else {

                $dataOK = MiQuery("SELECT `STATUS`, `CREATEDDATE` FROM autoload_log WHERE `STATUS`='OK' ORDER BY ID DESC;", _conn1('au_avery') );
                $created_date_OK = '';
                if (!empty($dataOK) ) {
                    $dataOK = $dataOK[0];
                    $created_date_OK = $dataOK['CREATEDDATE'];
                }

                // 01: Không save được
				if ($status == 'ERR_01' ) {
					$result = "$created_date_OK. (ERR 01 (UPDATE) lúc $created_date)";
				} else if ($status == 'ERR_02' ) { // có rỗng dữ liệu PACKING,...
					$result = "$created_date_OK. (ERR 02 (EMPTY DATA) lúc $created_date)";
				} else if ($status == 'ERR_03' ) { // File không đọc được
					$result = "$created_date_OK. (ERR 03 (File Lỗi) lúc $created_date)";
				} 
            }
            
        }

        return $result;
    }
    
    function GetHeaderTitle($urlRedirect = '') {

        $automail_updated = 'Automail updated: ' . getAutomailUpdated();
        if (strpos($automail_updated, 'ERR') !== false ) {
            $automail_updated = '<span style=\"color:red; font-style:normal;font-size:12px;\">Automail updated: ' . getAutomailUpdated() . '</span>';
        }

        $automail_updated .= ' | ';

        

		if(!isset($_COOKIE["ZeroIntranet"])){
            if(!empty($urlRedirect)) return 'var HeaderTile = "<a style=\'color:blue;font-style:italic;padding-left:10px\'>'.$automail_updated.'Hi Guest | <a href=\"./Index.php?URL='.$urlRedirect.'\">Login</a></a>";var UserVNRIS = "";';
            else return 'var HeaderTile = "<a style=\'color:blue;font-style:italic;padding-left:10px\'>'.$automail_updated.'Hi Guest | <a href=\"./Index.php\">Login</a></a>";var UserVNRIS = "";';		
		} else return 'var HeaderTile = "<a style=\'color:blue;font-style:italic;padding-left:10px\'>'.$automail_updated.'Hi '.$_COOKIE["ZeroIntranet"].' | <a href=\"./Module/Login/Logout.php\">Logout</a></a>";var UserVNRIS = "'.$_COOKIE["ZeroIntranet"].'";';
    }
    // // function GetHeaderTitle($urlRedirect = '') {
	// // 	if(!isset($_COOKIE["ZeroIntranet"])){
    // //         if(!empty($urlRedirect)) return 'var HeaderTile = "<a style=\'color:blue;font-style:italic;padding-left:10px\'>Hi Guest | <a href=\"./Index.php?URL='.$urlRedirect.'\">Login</a></a>";var UserVNRIS = "";';
    // //         else return 'var HeaderTile = "<a style=\'color:blue;font-style:italic;padding-left:10px\'>Hi Guest | <a href=\"./Index.php\">Login</a></a>";var UserVNRIS = "";';		
	// // 	} else return 'var HeaderTile = "<a style=\'color:blue;font-style:italic;padding-left:10px\'>Hi '.$_COOKIE["ZeroIntranet"].' | <a href=\"./Module/Login/Logout.php\">Logout</a></a>";var UserVNRIS = "'.$_COOKIE["ZeroIntranet"].'";';
    // // }

    function getUser() 
    {
        $email1 = isset($_COOKIE["ZeroIntranet"]) ? trim($_COOKIE["ZeroIntranet"]) : "";
        $email2 = isset($_COOKIE["VNRISIntranet"]) ? trim($_COOKIE["VNRISIntranet"]) : "";
        $email = !empty($email1) ? $email1 : $email2;
        return $email;
    }

    function planning_user_statistics($email )
    {
        if (!empty($email) ) {
            $table = 'planning_user_statistics';
            $ip = $_SERVER['REMOTE_ADDR'];
            $program = 'PFL_Planning';

            $url = "http://" .$_SERVER["SERVER_ADDR"] .$_SERVER["REQUEST_URI"];

            $METADATA = "HTTP_COOKIE: " . $_SERVER["HTTP_COOKIE"]. "PATH: " .$_SERVER["PATH"]. "SERVER_ADDR" .$_SERVER["SERVER_ADDR"]. "SERVER_PORT" .$_SERVER["SERVER_PORT"]. "DOCUMENT_ROOT" .$_SERVER["DOCUMENT_ROOT"]. "SCRIPT_FILENAME" .$_SERVER["SCRIPT_FILENAME"];
            $METADATA = mysqli_real_escape_string(_conn1(), $METADATA);

            // update data
            $key = $email . $program;
            $updated = date('Y-m-d H:i:s');
            $check = MiQuery("SELECT `email` FROM $table WHERE CONCAT(`email`,`program`) = '$key';", _conn1('au_avery') );
            if (!empty($check) ) {
                $sql = "UPDATE $table SET `ip` = '$ip', `url` = '$url', `METADATA` = '$METADATA', `updated` = '$updated'  WHERE `email` = '$email' AND `program` = '$program';";
            } else {
                // Thêm mới. Tự động nên không trả về kết quả
                $sql = "INSERT INTO $table (`email`, `program`, `ip`, `url`, `METADATA`, `updated`) VALUE ('$email', '$program', '$ip',  '$url', '$METADATA', '$updated');";
            }

            return MiNonQuery( $sql,_conn1("au_avery"));
            
        }
        
        
    }
?>