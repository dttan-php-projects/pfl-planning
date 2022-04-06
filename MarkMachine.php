<?php 
    require("./Module/Template.php");
    InitPage("PFLMarkMachine","Mark Machine");
?>

<script>
    var LayoutMain, ToolbarBot, ToolbarCalendar, GridMainTemp;
    var UserString = UserVNRIS.replace(".","_");
    var DateFrom = getUrl("F");
    var DateTo = getUrl("T");
    function DocumentStart()
    {
        if(DateFrom == undefined || DateTo == undefined) {
            DateFrom = "<?php echo date("Y-m-d", strtotime("- 3 days")); ?>";
            DateTo = "<?php echo date("Y-m-d"); ?>";
        }
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "2U",
            offsets: {
                top: 65, bottom: 40
            },
            cells: [
                {id: "a", header: true, text: "Information"},
                {id: "b", header: true, text: "Packing List History"}
            ]
        });
        ToolbarMain.addText("text", null, "JOBJACKET: ");
        ToolbarMain.addInput("JOBJACKET", null, "", 200);
        ToolbarMain.addButton("Find", null, "Find Item", "save.gif");+
        ToolbarMain.addButton("Reload", null, "Reload", "save.gif");+
        // ToolbarMain.addButton("ImportExcel", null, "Import Excel", "save.gif");
        ToolbarMain.attachEvent("onClick", function(name) {
            if(name == "ImportExcel") {
                ImportExcelFile();
            } else if(name == "Find"){
                GridMain.clearAll();
                GridMain.load("Data.php?EVENT=LOADMARKMACHINE&F=" + DateFrom + "&T=" + DateTo + "&JOBJACKET=" + ToolbarMain.getValue("JOBJACKET").trim(), function(){
                    if(confirm("Bạn có muốn chỉnh sửa toàn bộ SO Line này")) {
                        GridMainTemp.clearAll();
                        GridMain.forEachRow(function(id){
                            GridMainTemp.addRow(id,[
                                this.cells(id,0).getValue(),
                                this.cells(id,1).getValue(),
                                this.cells(id,2).getValue(),
                                this.cells(id,3).getValue(),
                                this.cells(id,4).getValue(),
                                this.cells(id,5).getValue(),
                                this.cells(id,6).getValue(),
                                this.cells(id,7).getValue(),
                                this.cells(id,8).getValue(),
                                this.cells(id,9).getValue()
                            ]);
                        });
                        GridMainTemp.addRow(id + "XXXX",[""]);
                    }
                });
            } else if(name == "Reload"){
                GridMain.clearAll();
                GridMain.load("Data.php?EVENT=LOADMARKMACHINE&F=" + DateFrom + "&T=" + DateTo);
            }
        });

        ToolbarMain.attachEvent("onEnter", function(id, value) {
            if (id == "JOBJACKET") {
                GridMain.clearAll();
                GridMain.load("Data.php?EVENT=LOADMARKMACHINE&F=" + DateFrom + "&T=" + DateTo + "&JOBJACKET=" + value, function(){
                    if(confirm("Bạn có muốn chỉnh sửa toàn bộ SO Line này")) {
                        GridMainTemp.clearAll();
                        GridMain.forEachRow(function(id){
                            GridMainTemp.addRow(id,[
                                this.cells(id,0).getValue(),
                                this.cells(id,1).getValue(),
                                this.cells(id,2).getValue(),
                                this.cells(id,3).getValue(),
                                this.cells(id,4).getValue(),
                                this.cells(id,5).getValue(),
                                this.cells(id,6).getValue(),
                                this.cells(id,7).getValue(),
                                this.cells(id,8).getValue(),
                                this.cells(id,9).getValue()
                            ])
                        });
                        GridMainTemp.addRow(id + "XXXX",[""]);
                    }
                });
            }
        });

        InitGrid();

        ToolbarBot = new dhtmlXToolbarObject({
            parent: "ToolbarBottomBot",
            align: "right",
            icons_path: "./Module/dhtmlx/common/imgs/"
        });
        ToolbarBot.addText("text_from", null, "From");
        ToolbarBot.addInput("date_from", null, "", 75);
        ToolbarBot.addText("text_to", null, "To");
        ToolbarBot.addInput("date_to", null, "", 75);
        ToolbarBot.addButton("LoadData", null, "Load Data", "save.gif");
        input_from = ToolbarBot.getInput("date_from");
        input_from.setAttribute("readOnly", "false");
        input_from.onclick = function(){ setSens(input_till,"max"); }
        
        input_till = ToolbarBot.getInput("date_to");
        input_till.setAttribute("readOnly", "false");
        input_till.onclick = function(){ setSens(input_from,"min"); }
        ToolbarCalendar = new dhtmlXCalendarObject([input_from,input_till]);
        ToolbarCalendar.setDateFormat("%Y-%m-%d");
        ToolbarBot.setValue("date_from",DateFrom);
        ToolbarBot.setValue("date_to",DateTo);
        ToolbarBot.attachEvent("onClick", function(name){
            location.href = "?F=" + ToolbarBot.getValue("date_from") + "&T=" + ToolbarBot.getValue("date_to") + "&P=" + name + "&R=" + $("#Process").val();
        });

        $(window).keydown(function(event) {
            if(event.ctrlKey && event.keyCode == 68) { 
                event.preventDefault(); 
            } else if(event.ctrlKey && event.keyCode == 83) { 
                event.preventDefault(); 
            }
        });

        document.addEventListener('paste',function (event) {
            CT = event.clipboardData.types;
            CopyClipBoard = event.clipboardData.getData('Text');
        });
    }

    function ImportExcelFile(){
        if(dhxWins.isWindow("WindowsDetail")) return;
        var id = "WindowsDetail";
        var w = 1100;	var h = 630;	var x = Number(($(window).width()-w)/2);	var y = Number(($(window).height()-h)/2);
        var Popup = dhxWins.createWindow(id, x, y, w, h);
        dhxWins.window(id).setText("Order List");
        Popup.attachHTMLString("<iframe style='width:100%;height:100%' id='WindowsLoad' onLoad='LoadDataExcel(this.contentWindow);' src='UploadIndex.php'></iframe>");
    }

    var DD;
    function LoadDataExcel(L) {
        var URL = String(L.location);
        if(URL.indexOf("UploadData.php") !== -1){
            var DataParse = JSON.parse(L.document.body.innerText);
            console.log(DataParse);
            DD = DataParse;
            GridMainTemp.clearAll();
            for(var i = 0; i < DataParse.length; i++) {
                GridMainTemp.addRow(GridMainTemp.getRowsNum() + 1,DataParse[i]);
            }
            GridMainTemp.addRow(GridMainTemp.getRowsNum() + 1,[""]);

            dhxWins.window("WindowsDetail").close();

        }
    }

    function setSens(inp, k) {
        if (k == "min") {
            ToolbarCalendar.setSensitiveRange(inp.value, null);
        } else {
            ToolbarCalendar.setSensitiveRange(null, inp.value);
        }
    }

    var MaxRowID, MaxRowLot, MaxColLot, EditMode;

    dhtmlXGridObject.prototype.SelectCellEditor=function(){
        if(this.editor && this.editor.obj){
            this.editor.obj.select();
        }
    };

    function InitGrid(){
        GridMainTemp = LayoutMain.cells("a").attachGrid();
        GridMainTemp.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridMainTemp.setHeader("ID,JOBJACKET,Print Machine,Cut Machine,Plan Print,Material,SEQ Print,Plan Cut,SEQ Cut,Created Date");
        GridMainTemp.setInitWidths("50,*,130,130,130,130,100,100,100,100");
        GridMainTemp.setColumnMinWidth("50,130,130,130,130,130,100,100,100,100");
        GridMainTemp.setColAlign("left,left,left,left,left,left,left,left,left,left");
        GridMainTemp.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        GridMainTemp.setColSorting("str,str,str,str,str,str,str,str,str,str");
        GridMainTemp.enableBlockSelection(true);
        GridMainTemp.enableMultiselect(true);
        GridMainTemp.setStyle(
            "",
            "","background:limegreen;",""
        );
        GridMainTemp.init();
        var i = 10;
        while(i>0) {
            GridMainTemp.addRow(i,[""]);
            i--;
        }


        GridMainTemp.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            if(stage == 2) {

            } else if(stage == 1) {
                this.SelectCellEditor();
            } else if(stage == 0){
                
            }
            return true;
        });	 

        GridMainTemp.attachEvent("onKeyPress", function(code,cFlag,sFlag){
            var LastRow = this.getRowsNum();
            if(this.getSelectedBlock() == null) return;
            var top_row = this.getSelectedBlock().LeftTopRow;
            var IndexRow = top_row;
            if(typeof top_row == "string") IndexRow = this.getRowIndex(top_row);
            var bottom_row = this.getSelectedBlock().RightBottomRow;
            var left_column = this.getSelectedBlock().LeftTopCol;
            var right_column = this.getSelectedBlock().RightBottomCol;
            if(!cFlag && !sFlag && code == 13) { //Enter
                if(IndexRow + 1 == LastRow) this.addRow(LastRow + 1,[""]);
                this.selectCell(IndexRow + 1,left_column,false,false,false,true);
                return false;
            } else if(!cFlag && !sFlag && code == 38) { //Up
                if(IndexRow - 1 < 0) return;
                this.selectCell(IndexRow - 1,left_column,false,false,false,true);
                return false;
            } else if(!cFlag && !sFlag && code == 40) { //Down
                if(IndexRow + 1 == LastRow) this.addRow(LastRow + 1,[""]);
                this.selectCell(IndexRow + 1,left_column,false,false,false,true);
                return false;
            }else if((!cFlag && !sFlag && code == 39) || (!sFlag && code == 9)) { //Right
                if(left_column + 1 > this.getColumnsNum()) return;
                this.selectCell(IndexRow,left_column + 1,false,false,false,true);
                return false;
            } else if((!cFlag && !sFlag && code == 37) || (sFlag && code == 9)) { //Left
                if(left_column - 1 < 0) return;
                this.selectCell(IndexRow,left_column - 1,false,false,false,true);
                return false;
            } else if(!this.editor && (!cFlag) && ((code > 64 && code < 91) || (code > 47 && code < 59) || (code > 95 && code < 113))) { // Any Key
                if(typeof top_row == "string") {
                    this.editCell();
                } else { 
                    this.editCell();
                }
                return false;
            } else if(cFlag && !sFlag && code == 83) { //Ctrl + Save
                var DataSend = [];
                for(var i = 0; i < this.getRowsNum(); i++) {
                // this.forEachRow(function(id){
                    if(this.cells2(i,1).getValue() != "") {
                        DataSend.push([
                            this.cells2(i,1).getValue(),
                            this.cells2(i,2).getValue(),
                            this.cells2(i,3).getValue(),
                            this.cells2(i,4).getValue(),
                            this.cells2(i,5).getValue(),
                            this.cells2(i,6).getValue(),
                            this.cells2(i,7).getValue(),
                            this.cells2(i,8).getValue()
                        ]);
                    }
                // });
                }
                var DataTable = {"EVENT":"INSERTMARKMACHINE"};
                DataTable.DATA = JSON.stringify(DataSend);
                DataTable.IDCODE = GetTimeKey() + UserString;
                var Result = AjaxAsync("Event.php",DataTable,"POST","HTML");       
                if(Result == DataTable.IDCODE) {
                    GridMain.clearAll();
                    GridMain.load("Data.php?EVENT=LOADMARKMACHINE&F=" + DateFrom + "&T=" + DateTo + "&IDCODE=" + Result, function(){
                        if(GridMain.getRowsNum() != 0) {
                            alert("Đã lưu " + GridMain.getRowsNum() + " dòng vào hệ thống");
                        }
                    });
                }
                return true;
            } else if(!cFlag && !sFlag && code == 46) { //Del
                    if(typeof top_row == "string") {
                        this.cells(top_row,left_column).setValue("");
                    } else { 
                        for(var i = top_row; i <= bottom_row; i++){
                            for(var j = left_column; j <= right_column; j++) {
                                this.cells2(i,j).setValue("");
                            }
                        }
                    }
                return true;
            } else if(!cFlag && sFlag && code == 46) { //Shift + Del
                    if(typeof top_row == "string") {
                        this.deleteRow(top_row);
                    } else {
                        for(var i = bottom_row; i >= top_row; i--){
                            this.deleteRow(this.getRowId(i));
                        }
                    }
                return true;
            } else if(cFlag && !sFlag && code == 68) { //Ctrl + D
                for(var i = top_row + 1; i <= bottom_row; i++){
                    for(var j = left_column; j <= right_column; j++) {
                        this.cells2(i,j).setValue(this.cells2(top_row,j).getValue());
                    }
                }
            } else if(cFlag && !sFlag && code == 67) { //Copy
                var DataCB = [];
                var DataCB1 = "";
                if(typeof top_row == "string") {
                    DataCB = this.cells(top_row,left_column).getValue() + "\n";
                    copyTextToClipboard(DataCB);
                    this.SelectCellEditor();
                } else {
                    for(var i = top_row; i <= bottom_row; i++){
                        for(var j = left_column; j <= right_column; j++) {
                            DataCB1 += "\t" + this.cells2(i,j).getValue().replace("\n","");
                        }
                        if(DataCB1 != "") DataCB.push(DataCB1.substring(1));
                        DataCB1 = "";
                    }
                    copyTextToClipboard(DataCB.join("\r\n"));
                    
                }
                return true;
            } else if(cFlag && !sFlag && code == 86) { //Paste
                if(!this.editor) {
                    this.AddClipBoard();
                }
                return true;
            }
            return true;

        });
        
        GridMain = LayoutMain.cells("b").attachGrid();
        GridMain.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridMain.setHeader("ID,JOBJACKET,Print Machine,Cut Machine,Plan Print,Material,SEQ Print,Plan Cut,SEQ Cut,Created Date");
        GridMain.setInitWidths("50,*,130,130,130,130,130,100,100,100");
        GridMain.setColumnMinWidth("50,130,130,130,130,130,130,100,100,100");
        GridMain.setColAlign("left,left,left,left,left,left,left,left,left,left");
        GridMain.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        GridMain.setColSorting("str,str,str,str,str,str,str,str,str,str");
        GridMain.enableBlockSelection(true);
        GridMain.enableMultiselect(true);
        GridMain.setStyle(
            "",
            "","background:limegreen;",""
        );
        GridMain.init();
        GridMain.load("Data.php?EVENT=LOADMARKMACHINE&F=" + DateFrom + "&T=" + DateTo);
        dp = new dataProcessor("Data.php?EVENT=LOADMARKMACHINE&F=" + DateFrom + "&T=" + DateTo);
        dp.init(GridMain);

        GridMain.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
            if(stage == 2) {

            } else if(stage == 1) {
                this.SelectCellEditor();
            } else if(stage == 0){
                
            }
            return true;
        });	 

        GridMain.attachEvent("onKeyPress", function(code,cFlag,sFlag){
            var LastRow = this.getRowsNum();
            if(this.getSelectedBlock() == null) return;
            var top_row = this.getSelectedBlock().LeftTopRow;
            var IndexRow = top_row;
            if(typeof top_row == "string") IndexRow = this.getRowIndex(top_row);
            var bottom_row = this.getSelectedBlock().RightBottomRow;
            var left_column = this.getSelectedBlock().LeftTopCol;
            var right_column = this.getSelectedBlock().RightBottomCol;
            if(!cFlag && !sFlag && code == 13) { //Enter
                if(IndexRow + 1 == LastRow) return;
                this.selectCell(IndexRow + 1,left_column,false,false,false,true);
                return false;
            } else if(!cFlag && !sFlag && code == 38) { //Up
                if(IndexRow - 1 < 0) return;
                this.selectCell(IndexRow - 1,left_column,false,false,false,true);
                return false;
            } else if(!cFlag && !sFlag && code == 40) { //Down
                if(IndexRow + 1 == LastRow) return;
                this.selectCell(IndexRow + 1,left_column,false,false,false,true);
                return false;
            }else if((!cFlag && !sFlag && code == 39) || (!sFlag && code == 9)) { //Right
                if(left_column + 1 > this.getColumnsNum()) return;
                this.selectCell(IndexRow,left_column + 1,false,false,false,true);
                return false;
            } else if((!cFlag && !sFlag && code == 37) || (sFlag && code == 9)) { //Left
                if(left_column - 1 < 0) return;
                this.selectCell(IndexRow,left_column - 1,false,false,false,true);
                return false;
            } else if(!this.editor && (!cFlag) && ((code > 64 && code < 91) || (code > 47 && code < 58))) { // Any Key
                if(typeof top_row == "string") {
                    this.editCell();
                } else { 
                    this.editCell();
                }
                return false;
            } else if(cFlag && !sFlag && code == 83) { //Ctrl + Save
                this.forEachRow(function(id){
                    console.log(id);
                });
                return true;
            } else if(!cFlag && !sFlag && code == 46) { //Del
                    if(typeof top_row == "string") {
                        this.cells(top_row,left_column).setValue("");
                    } else { 
                        for(var i = top_row; i <= bottom_row; i++){
                            for(var j = left_column; j <= right_column; j++) {
                                this.cells2(i,j).setValue("");
                            }
                        }
                    }
                return true;
            } else if(!cFlag && sFlag && code == 46) { //Shift + Del
                    if(typeof top_row == "string") {
                        this.deleteRow(top_row);
                    } else {
                        for(var i = bottom_row; i >= top_row; i--){
                            this.deleteRow(this.getRowId(i));
                        }
                    }
                return true;
            } else if(cFlag && !sFlag && code == 68) { //Ctrl + D
                for(var i = top_row + 1; i <= bottom_row; i++){
                    for(var j = left_column; j <= right_column; j++) {
                        this.cells2(i,j).setValue(this.cells2(top_row,j).getValue());
                        dp.setUpdated(GridMain.getRowId(i),true);
                    }
                }
            } else if(cFlag && !sFlag && code == 67) { //Copy
                var DataCB = [];
                var DataCB1 = "";
                if(typeof top_row == "string") {
                    DataCB = this.cells(top_row,left_column).getValue() + "\n";
                    copyTextToClipboard(DataCB);
                    this.SelectCellEditor();
                } else {
                    for(var i = top_row; i <= bottom_row; i++){
                        for(var j = left_column; j <= right_column; j++) {
                            DataCB1 += "\t" + this.cells2(i,j).getValue().replace("\n","");
                        }
                        if(DataCB1 != "") DataCB.push(DataCB1.substring(1));
                        DataCB1 = "";
                    }
                    copyTextToClipboard(DataCB.join("\r\n"));
                    
                }
                return true;
            }
            return true;

        });
        
    }

    var CopyClipBoard;
    var CT;
    function GetTimeKey() {return (new Date()).valueOf() + "_";}
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
                        if(SplitString[j][0] == '"' && SplitString[j][SplitString[j].length - 1] == '"') {
                            SplitString[j] = SplitString[j].substring(1,SplitString[j].length - 2);
                        }
                        ArrAdd.push(SplitString[j]);
                    }
                    this.addRow(this.getRowsNum() + 1,ArrAdd);
                    TurnAdd = true;
                } else {
                    for(var j = 0; j < SplitString.length; j++) {
                        if(SplitString[j][0] == '"' && SplitString[j][SplitString[j].length - 1] == '"') {
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
    
    function doOnLoad(){
        dhxWins = new dhtmlXWindows();
        dhxWins.attachViewportTo(document.body);
    };

    function doOnUnload(){
        if (dhxWins != null && dhxWins.unload != null) {
            dhxWins.unload();
            dhxWins = null;
        }
    };
</script>
<body onload="doOnLoad();" onunload="doOnUnload();">
    <div style="position:absolute;width:100%;height:35px;bottom:0;background:white">
		<div id="ToolbarBottomBot" ></div> 
	</div>
</body>
</html>