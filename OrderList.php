<?php 
    require("./Module/Template.php");
    InitPage("PFLOrderList","Order List");
?>

<script>
    var LayoutMain, ToolbarBot, ToolbarCalendar;
    var UserString = UserVNRIS.replace(".","_");
    var DateFrom = getUrl("F");
    var DateTo = getUrl("T");
    function DocumentStart()
    {
        if(DateFrom == undefined || DateTo == undefined) {
            DateFrom = "<?php echo date("Y-m-d", strtotime("- 1 days")); ?>";
            DateTo = "<?php echo date("Y-m-d"); ?>";
        }
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "2U",
            offsets: {
                top: 65, bottom: 40
            },
            cells: [
                {id: "a", header: false, text: "Worldon Information",width:250},
                {id: "b", header: false, text: "Worldon Information"}
            ]
        });
        ToolbarMain.addText("text", null, "JOBJACKET: ");
        ToolbarMain.addInput("JOBJACKET", null, "", 200);
        ToolbarMain.addButton("Find", null, "Find Item", "save.gif");+
        ToolbarMain.addButton("Reload", null, "Reload", "save.gif");+
        ToolbarMain.addButton("ExportExcel", null, "Export To Excel (Csv)", "save.gif");
        ToolbarMain.attachEvent("onClick", function(name) {
            if(name == "ExportExcel") {
                var Header1 = "";
                var X = 0;
				for(var i = 0; i< 100; i++)
				{
					Header1 = Header1 + GridMain.getColLabel(i) + ",";
					if(GridMain.getColLabel(i) == "") {
                        X++;
                        if(X > 3) break;
                    }
                    X = 0;
				}

                GridMain.csvParser = GridMain.csvExtParser;
                GridMain.setCSVDelimiter(',');
                GridMain.csv.row = "\r\n";
                var gridCsvData = GridMain.serializeToCSV();
				download("DataPackingList.csv", Header1 + "\r\n" + gridCsvData);
            } else if(name == "Find"){
                GridMain.clearAll();
                GridMain.load("Data.php?EVENT=LOADORDERLIST&F=" + DateFrom + "&T=" + DateTo + "&JOBJACKET=" + ToolbarMain.getValue("JOBJACKET").trim(), function(){
                    
                });
            } else if(name == "Reload"){
                GridMain.clearAll();
                GridMain.load("Data.php?EVENT=LOADORDERLIST&F=" + DateFrom + "&T=" + DateTo);
            }
        });

        ToolbarMain.attachEvent("onEnter", function(id, value) {
            if (id == "JOBJACKET") {
                GridMain.clearAll();
                GridMain.load("Data.php?EVENT=LOADORDERLIST&F=" + DateFrom + "&T=" + DateTo + "&JOBJACKET=" + value, function(){
                    
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

  
    var DD;
  

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
        var DataForm = [
				{type: "settings", position: "label-left", labelWidth: 200, inputWidth: 200,  position: "label-top"},
				{type: "block", width: "auto", blockOffset: 20, list: [
					{type: "select", name: "AuthorTeam", label: "Author Team", value: "", options:[
								{text: "", value: ""},
								{text: "thi.lebich", value: "thi.lebich"},
								{text: "son.n.nguyen", value: "son.n.nguyen"},
								{text: "tai.nguyenngoc", value: "tai.nguyenngoc"},
								{text: "dai.nguyen", value: "dai.nguyen"},
								{text: "anhthu.pham", value: "anhthu.pham"},
								{text: "thanh.le", value: "thanh.le"},
								{text: "hoa.vu", value: "hoa.vu"},
								{text: "thang.bui", value: "thang.bui"},
								{text: "giang.truong", value: "giang.truong"},
								{text: "nhung.le", value: "nhung.le"},
								{text: "toan.phan", value: "toan.phan"},
								{text: "na.nguyen", value: "na.nguyen"},
								{text: "trang.huynhphuong", value: "trang.huynhphuong"},
								{text: "thihieu.dang", value: "thihieu.dang"},
								{text: "hieu.tran", value: "hieu.tran"},
								{text: "tung.le", value: "tung.le"},
								{text: "binh.hoang", value: "binh.hoang"},
								{text: "hoa.truong", value: "hoa.truong"},
                                {text: "tan.doan", value: "tan.doan"}
							]}
				]},
				{type: "block", width: "auto", blockOffset: 20, list: [
					{type: "button", label: "New Input", value: "Clear"},
					{type: "newcolumn"},
					{type: "button", name: "Filter", label: "New Input", value: "Filter"}
				]}
			];

			var FilterForm = LayoutMain.cells("a").attachForm();
				FilterForm.loadStruct(DataForm, "json");
				FilterForm.attachEvent("onButtonClick", function(name, command){
                    if(name == "Filter") {
                        if(FilterForm.getItemValue("AuthorTeam") != "") {
                            GridMain.filterBy(19,function(a){ 
                                return (a == FilterForm.getItemValue("AuthorTeam").replaceAll("&","&amp;"));
                            })
                        } else GridMain.filterBy(19,"");
                    }
				})

        GridMain = LayoutMain.cells("b").attachGrid();
        GridMain.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridMain.setHeader(",ID,DATE,SO THU TU<BR/>CP OR PLANNING,DON HANG<BR/>CS SO# CS,MA HANG<BR/>ITEM CODE,TEN KHACH HANG<BR/>CUSTOMER,"
        + "NHAN<BR/>ORDER ITEM,SO LUONG CON NHAN<BR/>QTY-PCS,SO LUONG VAT TU CAN<BR/>QTY-YARD,SO SIZE,KICH THUOC NHAN<BR/>(DVT:mm),"
        + "MA VAT TU<BR/>ORACLE MATERIAL,VAT TU<BR/>MATERIAL,K/H YEU CAU<BR/>CUSTOMER REQUEST DATE,JOB NUMBER,TONG SO MAU MUC, TEETH,REMARK, PIC, UEE, NGÀY TẠO, P");
        GridMain.attachHeader(",,,#combo_filter,#combo_filter,#combo_filter,,,,,,,,,,,,,,,,,")
        GridMain.setInitWidths("40,50,100,100,100,100,100,100,100,100,100,100,100,*,*,100,100,100,100,100,70,50,70")
        GridMain.setColumnMinWidth("40,50,140,80,40,100,100,80,70,140,140,140,120,140,140,100,100,100,100,100,70,50,70")
        GridMain.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
        GridMain.setColTypes("ch,ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,txt,txt,txt,txt,txt,ed,ed,ed,ed,ed,ed");
        GridMain.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str")
        GridMain.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridMain.entBox.id = "GridMain";
        GridMain.enableBlockSelection(true);
        GridMain.enableMultiselect(true);
        GridMain.setStyle(
            "",
            "","background:limegreen;",""
        );
        GridMain.init();
        GridMain.load("Data.php?EVENT=LOADORDERLIST&F=" + DateFrom + "&T=" + DateTo);
        dp = new dataProcessor("Data.php?EVENT=LOADORDERLIST&F=" + DateFrom + "&T=" + DateTo);
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

    function PrintJJ (JobJacket) 
    {
        if(JobJacket != "") window.open("PrintPage.php?JJ=" + JobJacket, '_blank'); 
    }
</script>
<body onload="doOnLoad();" onunload="doOnUnload();">
    <div style="position:absolute;width:100%;height:35px;bottom:0;background:white">
		<div id="ToolbarBottomBot" ></div> 
	</div>
</body>
</html>