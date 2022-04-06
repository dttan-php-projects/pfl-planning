<?php 
    require("./Module/Template.php");
    InitPage("Artwork","Production Record");
?>

<script>
    var DatePrint = '<?php echo date("Y-m-d") ?>';
    var LayoutMain, LayoutLeft;
    var FOD = "";
    var FRStatus = "";
    function DocumentStart() {
        if(UserVNRIS == "Guest") {
            alert("Đăng nhập rồi làm nhé");
        }
        FOD = getUrl("FOD");
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "2U",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: false, text:"SO Line Table"},
                {id: "b", header: true, text:"Size Prepress", width: 610},
            ]
        });

        LayoutLeft = LayoutMain.cells("a").attachLayout({
                pattern: "4U",
                offsets: {
                    top: 2,
                    bottom: 0
                },
                cells: [
                    {id: "a", header: true, text:"SO Line Table"},
                    {id: "b", header: true, text:"SO Line Select"},
                    {id: "c", header: true, text:"Size Prepress", width: 250},
                    {id: "d", header: true, text:"Size SO Line"}
                    ]
                });

        $(window).keydown(function(event) {
            if(event.ctrlKey && event.keyCode == 82) { 
                var DataSend = {};
                DataSend.EVENT = "GETLASTREMARK";
                DataSend.ITEM = DataPEForm.getItemValue("ItemCode", true).toString();
                var Result = AjaxAsync("Data.php", DataSend);
                if(Result != "") DataPEForm.setItemValue("RemarkJJ", Result);
                event.preventDefault(); 
            } else if(event.ctrlKey && event.keyCode == 83) { 
                if(DataPEForm.getItemValue("NumSize", true).toString().indexOf("N") !== -1) {
                    dhtmlx.alert("Vui Lòng Nhập Số Size");
                    return;
                }
                ToolbarMain.disableItem("Save");
                ToolbarMain.disableItem("SavePrint");
                var JobJacket = SaveOrder();

                ToolbarMain.enableItem("Save");
                ToolbarMain.enableItem("SavePrint");
                if(JobJacket.indexOf("-") !== -1) {
                    dhtmlx.confirm({
                        title: "Xác nhận",
                        type:"confirm-warning",
                        text: "Bạn có muốn Reload để tạo đơn mới",
                        callback: function(result) {
                            if(result) location.reload();
                        }
                    });
                }
                event.preventDefault(); 
            }
        });

        LayoutLeft.cells("a").setWidth(LayoutLeft.cells("d").getWidth() - 440);
        ToolbarMain.addButton("Save", null, "<a style='font-size:16pt;font-weight:bold'>Lưu</a>", "save.gif");
        ToolbarMain.addButton("SavePrint", null, "Lưu và In", "save.gif");
        ToolbarMain.addButton("Print", null, "In", "save.gif");
        ToolbarMain.addButton("Modify", null, "Sửa Đơn", "save.gif");
		ToolbarMain.addButton("DeleteJobJacket", null, "Delete JobJacket", "save.gif");
        if(FOD == undefined) ToolbarMain.addButton("FODNULL", null, "NOT FOD", "save.gif");
        else ToolbarMain.addButton("FOD", null, "<a style='font-size:20pt; font-weight:bold'>FOD</a>", "save.gif");  
        ToolbarMain.attachEvent("onClick", function(id) {
            if(id == "Modify") {
                var JobJacket = prompt("Vui Lòng Nhập Mã Đơn Cần Sửa");
                if(JobJacket != "") {
                    GridSOSelect.clearAll();
                        GridSOSelect.loadXML("Data.php?EVENT=LOADSOFROMJOB&JOBJACKET=" + JobJacket,function(){
                            GridPrepress.clearAll();
                            GridPrepress.loadXML("Data.php?EVENT=LOADSIZE&ITEM=" + GridSOSelect.cells2(0,1).getValue(),function(){
                                if(GridPrepress.getRowsNum() != 0) {
                                    
                                    DataPEForm.setItemValue("LengthLabel", GridPrepress.cells2(0,0).getValue().replaceAll("mm",""));
                                    DataPEForm.setItemValue("WidthLabel", GridPrepress.cells2(0,1).getValue().replaceAll("mm",""));
                                    DataPEForm.setItemValue("Teeth", GridPrepress.cells2(0,2).getValue());
                                } else {
                                    InsertNewSize(GridSOSelect.cells(id,1).getValue());
                                }

                                FillData(GridSOSelect.cells2(0,1).getValue());
                                ReloadSize();
                                var DataRaw = AjaxAsync("Data.php", {"EVENT":"LOADJOBJACKET","JOBJACKET":JobJacket},"GET","JSON");
                                if(DataRaw.length == 1) {
                                    for (const [key, value] of Object.entries(DataRaw[0])) DataPEForm.setItemValue(key, value);
                                }
                                
                            });
                        });
                }
            } else if(id == "FOD") {
                if(confirm("Hủy chế độ FOD")) {
                    window.location.href = "ProductionRecord.php";
                }
            } else if(id == "FODNULL") {
                if(confirm("Bạn có muốn chuyển sang chế độ FOD")) {
                    window.location.href = "ProductionRecord.php?FOD=1";
                }
            } else if(id == "DeleteJobJacket") {
                var XXX = prompt("Vui lòng nhập JobJacket: vào ô bên dưới để xóa dữ liệu");
                if(XXX != null || XXX != "") {
                    var Result = AjaxAsync("Event.php",{"EVENT":"DELETEJJ","JOBJACKET":XXX});
                }
            } else if(id == "Print") {
                var JobJacket = DataPEForm.getItemValue("JobJacket", true);
                if(JobJacket != "") window.open("PrintPage.php?JJ=" + JobJacket, '_blank'); 
                else dhtmlx.alert("Chưa làm lệnh không được in :P");
            } else if(id == "Save") {
                if(DataPEForm.getItemValue("NumSize", true).toString().indexOf("N") !== -1) {
                    dhtmlx.alert("Vui Lòng Nhập Số Size");
                    return;
                }
                ToolbarMain.disableItem("Save");
                ToolbarMain.disableItem("SavePrint");
                var JobJacket = SaveOrder();

                ToolbarMain.enableItem("Save");
                ToolbarMain.enableItem("SavePrint");
                if(JobJacket.indexOf("-") !== -1) {
                    dhtmlx.confirm({
                        title: "Xác nhận",
                        type:"confirm-warning",
                        text: "Bạn có muốn Reload để tạo đơn mới",
                        callback: function(result) {
                            if(result) location.reload();
                        }
                    });
                }
                // alert("Xong rồi đại dương ơi :P");

                // location.reload();
            } else  if(id == "SavePrint") {
                if(DataPEForm.getItemValue("NumSize", true).toString().indexOf("N") !== -1) {
                    dhtmlx.alert("Vui Lòng Nhập Số Size");
                    return;
                }
                ToolbarMain.disableItem("Save");
                ToolbarMain.disableItem("SavePrint");
                var JobJacket = SaveOrder();
                DataPEForm.setItemValue("JobJacket", JobJacket);
                window.open("PrintPage.php?JJ=" + JobJacket, '_blank'); 
                ToolbarMain.enableItem("Save");
                ToolbarMain.enableItem("SavePrint");
                if(JobJacket.indexOf("-") !== -1) {
                    dhtmlx.confirm({
                        title: "Xác nhận",
                        type:"confirm-warning",
                        text: "Bạn có muốn Reload để tạo đơn mới",
                        callback: function(result) {
                            if(result) location.reload();
                        }
                    });
                }
                // alert("Xong rồi đại dương ơi :P");

                // location.reload();
            }
        });

        ToolbarMainData = LayoutMain.cells("a").attachToolbar({icons_path: "./Module/dhtmlx/common/imgs/"});
        ToolbarMainData.addSeparator("Space", null);
        ToolbarMainData.addText("text", null, "SO: ");
        // ToolbarMainData.addInput("SO", null, "42103846-8", 200);
        ToolbarMainData.addInput("SO", null, "", 200);
        input_so = ToolbarMainData.getInput("SO");
        input_so.focus(); // set focus
        ToolbarMainData.addButton("Find", null, "Find Item", "save.gif");
        ToolbarMainData.addButton("SelectAll", null, "Select All", "save.gif");

        ToolbarMainData.addButton("ClearSO", null, "Clear SO", "save.gif");
        ToolbarMainData.addButton("InputSize", null, "Thêm kích thước nhãn", "save.gif");
        ToolbarMainData.addButton("InsertItem", null, "Modify Item", "save.gif");
        ToolbarMainData.addSpacer("SelectAll");

        ToolbarMainData.attachEvent("onEnter", function(id, value) {
            if (id == "SO") {
                LoadSO(value);
            }
        });
        ToolbarMainData.attachEvent("onClick", function(id){
            if(id == "InputSize") {
                var ItemC = DataPEForm.getItemValue("ItemCode", true);
                if(DataPEForm.getItemValue("ItemCode", true) == "") {
                    ItemC = prompt("Item Code đang rỗng, vui lòng chọn 1 SO Line");
                    if(ItemC == "" || ItemC == null){
                        dhtmlxalert("Không thể thêm Size");
                        return;
                    }
                }
                InsertNewSize(ItemC);
            } else if(id == "Find") {
                LoadSO(ToolbarMainData.getValue("SO"));
            }  else if(id == "SelectAll") {
                var CountS = 0;
                var CountC = 0;
                var SOLine = "";

                GridSO.forEachRow(function(id){
                    SOLine = GridSO.cells(id,0).getValue();
                    if(GridSO.cells(id,9).getValue().indexOf("-") === -1) {
                        CountC++;
                        if(GridSO.cells(id,5).getValue().trim() == "") CountS++;
                    }
                });
                if(CountC == 0) dhtmlx.alert("Không có đơn nào được chọn");
                else {
                    GridSO.forEachRow(function(id){
                        if(GridSO.cells(id,9).getValue().indexOf("-") === -1) {
                            var TurnR = false;

                            for (var i=0; i< GridSOSelect.getRowsNum(); i++){
                                if(GridSO.cells(id,1).getValue() != GridSOSelect.cells2(i,1).getValue()) {
                                    dhtmlx.message({
                                        text: "Không thể Combine Khác Item",
                                        expire: 3000
                                    })
                                    TurnR = true;
                                    break;
                                }
                                if(id == GridSOSelect.cells2(i,0).getValue()) {
                                    dhtmlx.message({
                                        text: "SO Line: " + id + " đã được chọn",
                                        expire: 3000
                                    })
                                    TurnR = true;
                                    break;
                                }
                            };

                            if(TurnR) return;

                            var NewRow = [];
                            var SOSplit = GridSO.cells(id,0).getValue().split("-");

                            for(var i = 0; i < 10; i++) {
                                NewRow.push(GridSO.cells(id,i).getValue())
                            }
                            NewRow.push(SOSplit[0]);
                            NewRow.push(SOSplit[1]);

                            GridSOSelect.addRow(id,NewRow);
                            if(GridSOSelect.getRowsNum() == 1) {
                                GridPrepress.clearAll();
                                GridPrepress.loadXML("Data.php?EVENT=LOADSIZE&ITEM=" + GridSO.cells(id,1).getValue(),function(){
                                    if(GridPrepress.getRowsNum() != 0) {
                                        
                                        DataPEForm.setItemValue("LengthLabel", GridPrepress.cells2(0,0).getValue().replaceAll("mm",""));
                                        DataPEForm.setItemValue("WidthLabel", GridPrepress.cells2(0,1).getValue().replaceAll("mm",""));
                                        DataPEForm.setItemValue("Teeth", GridPrepress.cells2(0,2).getValue());
                                    } else {
                                        InsertNewSize(GridSO.cells(id,1).getValue());
                                    }
                                    
                                    DataPEForm.setItemValue("ItemCode", GridSO.cells(id,1).getValue());
                                    DataPEForm.setItemValue("ReceivingDate", DatePrint);
                                    DataPEForm.setItemValue("Dueday", GridSO.cells(id,4).getValue());
                                    DataPEForm.setItemValue("RequestDate", GridSO.cells(id,4).getValue());
                                    DataPEForm.setItemValue("ItemCodeName", GridSO.cells(id,2).getValue());
                                    DataPEForm.setItemValue("RBO", GridSO.cells(id,6).getValue());
                                    FillData(GridSO.cells(id,1).getValue());
                                });
                            }
                        }
                    });
                    ReloadSize();
                }
            } else if (id == "ClearSO") {
                GridSOSelect.clearAll();
                GridPrepress.clearAll();
                GridSize.clearAll();
                UnlockItem();
            } else if(id == "InsertItem") InsertNewItem(DataPEForm.getItemValue("ItemCode", true));
        });
        InitGrid();
        InitForm();
    }
    var GridSO;
    function InitGrid(){
        GridSO = LayoutLeft.cells("a").attachGrid();
        GridSO.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridSO.setHeader("SO Line, Item, Customer, Qty, Request, Size, RBO, R, P,JOB,Remark");		
        GridSO.setInitWidths("130,140,150,80,150,70,150,100,80,*,100");
        GridSO.setColumnMinWidth("130,140,150,80,150,70,150,100,80,100,100");
        GridSO.setColAlign("center,center,center,center,center,center,center,center,center,center,center");
        GridSO.setColTypes("ro,ro,ro,ed,ed,ed,ro,ro,ro,ro,ro");
        GridSO.setColSorting("str,str,str,str,str,str,str,str,str,str,str");	
        GridSO.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridSO.entBox.id = "GridMain";
        GridSO.init();
		GridSO.setColumnHidden(4,true);
		GridSO.setColumnHidden(6,true);
		GridSO.setColumnHidden(7,true);
		// GridSO.setColumnHidden(8,true);
        GridSO.attachEvent("onRowDblClicked", function(rId,cInd){
            AddSO(rId);
        });
        GridSOSelect = LayoutLeft.cells("b").attachGrid();
        GridSOSelect.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridSOSelect.setHeader("SO Line, Item, Customer, Qty, Request, Size, RBO, R, P, Remark,O,L");
        GridSOSelect.setInitWidths("80,90,100,80,80,50,150,100,80,*,100,100")
        GridSOSelect.setColAlign("center,center,center,center,center,center,center,center,center,center,center");
        GridSOSelect.setColTypes("ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
        GridSOSelect.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str");	
        GridSOSelect.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridSOSelect.entBox.id = "GridMain";
        GridSOSelect.init();
		GridSOSelect.setColumnHidden(2,true);
		GridSOSelect.setColumnHidden(4,true);
		GridSOSelect.setColumnHidden(6,true);
		GridSOSelect.setColumnHidden(7,true);
		GridSOSelect.setColumnHidden(8,true);
        GridSOSelect.attachEvent("onRowDblClicked", function(rId,cInd){
            this.deleteRow(rId);
            if(this.getRowsNum() == 0) {
                GridPrepress.clearAll();
                GridSize.clearAll();
                UnlockItem();
            } else{
                ReloadSize();
            }
        })

        GridPrepress = LayoutLeft.cells("c").attachGrid();
        GridPrepress.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
        GridPrepress.setHeader("Length, Width, Teeth, Last Use");
        GridPrepress.setInitWidths("60,60,60,120")
        GridPrepress.setColAlign("center,center,center,center");
        GridPrepress.setColTypes("ro,ro,ro,ro");
        GridPrepress.setColSorting("str,str,str,str");	
        GridPrepress.setRowTextStyle("1", "background-color: red; font-family: arial;");
        GridPrepress.init();

        GridPrepress.attachEvent("onRowSelect", function(id,ind){
			DataPEForm.setItemValue("LengthLabel", GridPrepress.cells(id,0).getValue().replaceAll("mm",""));
            DataPEForm.setItemValue("WidthLabel", GridPrepress.cells(id,1).getValue().replaceAll("mm",""));
            DataPEForm.setItemValue("Teeth", GridPrepress.cells(id,2).getValue());
            ComputeIndex();
		});

        GridSize = LayoutLeft.cells("d").attachGrid();
    }

    var DataPEForm,GridSize;
    function InitForm(){
        DataPEForm = LayoutMain.cells("b").attachForm();
        DataPEForm.loadStruct(DataForm, "json");
        DataPEForm.attachEvent("onChange", function(name,value,is_checked){
            
        });
    }


function ComputeIndex() { // Tính Scrap
    let SizeNum = DataPEForm.getItemValue("NumSize", true);
    SizeNum = parseInt(SizeNum).toString();
    if(SizeNum == "" || SizeNum == "0" || SizeNum == "NaN") {
        dhtmlx.message({
            text: "<a style='font-weight:bold'>Vui Lòng nhập số Size " + DataPEForm.getItemValue("SOLine", true) + "</a>",
            expire: 5000,
        });
    }
    let ItemCode = DataPEForm.getItemValue("ItemCode", true);
    let Qty = DataPEForm.getItemValue("Qty", true);
    let Length = parseInt(DataPEForm.getItemValue("LengthLabel", true).replaceAll("mm","").replaceAll(" ",""));
    let PrintSide = DataPEForm.getItemValue("PrintMethod", true);	
    if(PrintSide == "Two Side") PrintSide = 2; // Mặt in
    else if(PrintSide == "NONE") PrintSide = 0;
    else PrintSide = 1;
    let NumInk = DataPEForm.getItemValue("InkNum", true); // Số lượng màu in
    let Teeth = DataPEForm.getItemValue("Teeth", true); // Số Teeth
    let Scrap = "";
    let QtyNeed = "";
    let Description = DataPEForm.getItemValue("ItemDescription", true);
    var FScrap = 0;

    if(Qty < 101) FScrap = 0.21;
    else if(Qty > 100 && Qty < 201) FScrap = 0.12;
    else if(Qty > 200 && Qty < 301) FScrap = 0.08; 
    else if(Qty > 300 && Qty < 501) FScrap = 0.06;
    else if(Qty > 500 && Qty < 801) FScrap = 0.04;
    else if(Qty > 800 && Qty < 1001) FScrap = 0.03;
    else if(Qty > 1000 && Qty < 2001) FScrap = 0.03; // @tandoan: sửa lại FScrap = 0.006 thành FScrap = 0.03 (3%). 
    else if(Qty > 2000 && Qty < 30001) FScrap = 0.02; // 2%
    else if(Qty > 30000 && Qty < 40001) FScrap = 0.015; // 1.5%
    else if(Qty > 40000 && Qty < 50001) FScrap = 0.01; // 1%
    else if(Qty > 50000) FScrap = 0.006;// 0.6%

    var A = 0;
    var B = 15; // Thay đổi B=30 thành 
    var C = 2;
    var F = 0;
    var H = 0;
    var D = 4; // Thay đổi D=3 thành 4
    var G = 0.5;
    var E = 4;
    var K = 0;
    var L = 0;

    if(Length > 100) L = 0;

    switch(ItemCode) {
        case "CB394883A": K = 1.05; break;
        case "CB470666A": K = 1.05; break;
        case "CB464993A": K = 1.05; break;
        case "CB400930A": K = 1.05; break;
        case "CB95386": K = 1.1; break;
        case "CB403805A": K = 1.1; break;
        default: K = 1; break;
    }

    if(Description.indexOf("UNIQLO") !== -1) {
        // A = 6;
        A = 3; // @tandoan: Thay đổi từ 6 thành 3
        F = 1.2;
    } else if(Description.indexOf("MIZUNO") !== -1){
        // A = 6;
        A = 3; // @tandoan: Thay đổi từ 6 thành 3
        F = 1.1;
    } else {
        A = 3;
        F = 1;
    }
    if(Teeth.indexOf("SILK") !== -1 && Teeth.indexOf("SILK 2 MAT") === -1) {
        // A = 6;
        A = 3; // @tandoan: Thay đổi từ 6 thành 3
        F = 1.15;
    } else if(Teeth.indexOf("SILK 2 MAT") !== -1) {
        // A = 6;
        A = 3; // @tandoan: Thay đổi từ 6 thành 3
        F = 1.35;
    }

console.log(Teeth, A, F);

    /** @TanDoan 20200821: Đổi công thức tính Scrap. @Thi.LeBich yêu cầu, mail: SCRAP FORMULA CHANGE */        
        // // QtyNeed = Math.ceil((
        // //     (
        // //         (
        // //             (Qty*(1 + FScrap) + Math.ceil(Qty/500) * A) * F + B
        // //         ) * K * Length/1000
        // //     ) + (SizeNum - 1) * C + (
        // //         (H + D * SizeNum * PrintSide)*Length/1000 + G
        // //     ) * Math.ceil(Length*Qty/300000) + (E * NumInk * PrintSide) + (L*Qty*(1 + FScrap))
        // // )/0.9144);


    
        // // Scrap = Math.ceil((
        // //     (
        // //         (
        // //             (Qty*(FScrap) + Math.ceil(Qty/500) * A) * F + B
        // //         ) * K * Length/1000
        // //     ) + (SizeNum - 1) * C + (
        // //         (H + D * SizeNum * PrintSide)*Length/1000 + G
        // //     ) * Math.ceil(Length*Qty/300000) + E * NumInk * PrintSide + (L*Qty*(FScrap))
        // // )/0.9144);

        A = 3; // Thay đổi thành 3 cho tất cả
        var tmpScrap1 = ( (Qty*(FScrap) + Math.ceil(Qty/500) * A) + B ) * (Length/1000);
        var tmpScrap2 = ((SizeNum - 1) * C * NumInk) + ((SizeNum-1) * 0.5);
        var tmpScrap3 = ( (D * SizeNum ) * (Length/1000) ) + ( 0.5 * (Length/1000) * (Qty/300) );
        var tmpScrap4 = ( E * NumInk * PrintSide );

        Scrap = Math.ceil( (tmpScrap1 + tmpScrap2 + tmpScrap3 + tmpScrap4) / 0.9144 );
           
    QtyNeed = ( ((Length * Qty )/1000) / 0.9144) + Scrap ;
    Scrap = Math.round(Scrap);
    QtyNeed = Math.ceil(QtyNeed);
    
    console.log('Scrap 111: '+Scrap);
    console.log('length 111: '+Length);
    console.log('Qty 111: '+Qty);

    console.log('QtyNeed 111: '+QtyNeed);

    if(FOD != undefined) {
        if(DataPEForm.getItemValue("InkCode", true).toUpperCase().trim() == "BLACK" || DataPEForm.getItemValue("InkCode", true).toUpperCase().trim() == "WHITE") QtyNeed = QtyNeed + 200;
        else QtyNeed = QtyNeed + 500;
    }

    console.log('QtyNeed 222: '+QtyNeed);
    
    DataPEForm.setItemValue("QtyScrap", Scrap);
    DataPEForm.setItemValue("RateScrap", (QtyNeed-Scrap != 0 ? Math.round(Scrap*100/(QtyNeed-Scrap)) : "N/A ") + "%");
    DataPEForm.setItemValue("QtyNeed", QtyNeed);
    if(QtyNeed-Scrap == 0 && Qty > 100) {
        ComputeIndex();
    }
}


function InsertNewSize (itemcode) {
    var id = "WindowsDetail";
    var w = 330;	var h = 300;	var x = Number(($(window).width()-200)/2);	var y = Number(($(window).height()-200)/2);
    var Popup = dhxWins.createWindow(id, x, y, w, h);
    dhxWins.window(id).setText("Thêm kích thước con nhãn");

    var DataForm = [
        {type: "settings", position: "label-left", labelWidth: 90, inputWidth: 130},
        {type: "fieldset", offsetLeft: 20, offsetTop: 20, label: "Main Information", width: "auto", blockOffset: 10, list: [
            {type: "input", label: "Item Code", value: "", name: "ItemCode"},
            {type: "input", label: "Length", value: "", name: "Length"},
            {type: "input", label: "Width", value: "", name: "Width"},
            {type: "input", label: "Teeth", value: "", name: "Teeth"},
            {type: "button", value: "Save", name: "Save"}
        ]}
    ];

    DimensionForm = Popup.attachForm();
    DimensionForm.loadStruct(DataForm, "json");
    DimensionForm.setItemValue("ItemCode",itemcode);
    DimensionForm.attachEvent("onButtonClick", function(name){
        var DataSend = {"EVENT":"SAVESIZEITEM"};
        DataSend.ITEMCODE = DimensionForm.getItemValue("ItemCode",true);
        DataSend.LENGTH = DimensionForm.getItemValue("Length",true);
        DataSend.WIDTH = DimensionForm.getItemValue("Width",true);
        DataSend.TEETH = DimensionForm.getItemValue("Teeth",true);

        $Result = AjaxAsync("Event.php",DataSend,"POST");
        if($Result == "OK") {
            GridPrepress.clearAll();
                GridPrepress.loadXML("Data.php?EVENT=LOADSIZE&ITEM=" + itemcode,function(){
                    GridPrepress.forEachRow(function(id){
                        if(GridPrepress.cells(id,0).getValue() == 1) {
                            DataPEForm.setItemValue("LengthLabel", GridPrepress.cells(id,1).getValue().replaceAll("mm",""));
                            DataPEForm.setItemValue("WidthLabel", GridPrepress.cells(id,2).getValue().replaceAll("mm",""));
                            DataPEForm.setItemValue("Teeth", GridPrepress.cells(id,3).getValue());
                        }
                    });
                });
                dhtmlx.alert("Đã Thêm");
                doOnUnload();
                doOnLoad();
        }
    });
}



function FillData(val){
    var DataSend = {"EVENT":"LOADITEM"};
    DataSend.ITEM = val;
    var Result = AjaxAsync("Data.php", DataSend, "GET", "JSON");
    if(Result.length == 0) {
        if(confirm("Item Không có trong hệ thống, bạn có muốn thêm vào")) InsertNewItem(val);
    } else {
        Data = Result[0];
        if(Data["MaterialCode"] == null){
            if(confirm("Item Chưa cập nhật đủ thông tin. Bạn muốn cập nhất")) InsertNewItem(val);
            return;
        } else {
            for (const [key, value] of Object.entries(Data)) DataPEForm.setItemValue(key, value);
        }
    }
}

function ReloadSize() {
    var SOLineTotal = "";
    var CountNum = 0;
    var count = 0;
    var QtySum = 0;
    var SOL = "";
    var RequestDate = new Date();
    var PromiseDate = new Date();
    var TurnDate = true;
    GridSOSelect.sortRows(10,"str","asc");
    GridSOSelect.sortRows(11,"int","asc");
    for (var i=0; i< GridSOSelect.getRowsNum(); i++){
        SOLineTotal = SOLineTotal + ",'" + GridSOSelect.cells2(i,0).getValue() + "'";
        CountNum = CountNum > GridSOSelect.cells2(i,5).getValue() ? CountNum : GridSOSelect.cells2(i,5).getValue();
        QtySum = QtySum + parseInt(GridSOSelect.cells2(i,3).getValue());
        if(SOL == "") SOL = GridSOSelect.cells2(i,0).getValue();
        else {
            if(SOL.indexOf(GridSOSelect.cells2(i,10).getValue()) !== -1) SOL = SOL + "-" + GridSOSelect.cells2(i,11).getValue();
            else SOL = SOL + "," + GridSOSelect.cells2(i,0).getValue();
        }
        if(RequestDate > ConvertDate(GridSOSelect.cells2(i,4).getValue()) || TurnDate) RequestDate = ConvertDate(GridSOSelect.cells2(i,4).getValue());
        if(PromiseDate > ConvertDate(GridSOSelect.cells2(i,8).getValue()) || TurnDate) {
            if(GridSOSelect.cells2(i,8).getValue() != "") PromiseDate = ConvertDate(GridSOSelect.cells2(i,8).getValue());
            else PromiseDate = '';
        }
        TurnDate = false;
    }
    
    SOLineTotal = SOLineTotal.substr(1);
    DataPEForm.setItemValue("Qty", QtySum);
    DataPEForm.setItemValue("SOLine", SOL + "-B");
    var DataSend = {"EVENT" : "LOADSIZESO"};
    DataSend.SO = SOLineTotal;
    var Result = AjaxAsync("Data.php", DataSend);

    GridSize.clearAll();
    GridSize.parse(Result,"xml");
    count = GridSize.getRowsNum();
    if(count > CountNum) DataPEForm.setItemValue("NumSize", count);
    else DataPEForm.setItemValue("NumSize", CountNum);

    DataPEForm.setItemValue("RequestDate", FormatDate(RequestDate));
    if(PromiseDate != '' && PromiseDate != "Invalid Date") DataPEForm.setItemValue("PromiseDate", FormatDate(PromiseDate));

    ComputeIndex();
}

function InsertNewItem (itemcode) {
    var id = "WindowsDetail";
    var w = 650;	var h = 650;	var x = Number(($(window).width()-650)/2);	var y = Number(($(window).height()-620)/2);
    var Popup = dhxWins.createWindow(id, x, y, w, h);
    dhxWins.window(id).setText("Insert/Modify Item Information");
    ItemForm = Popup.attachForm();
    ItemForm.loadStruct(DataFormInsert, "json");
    ItemForm.setItemValue("CutMethod", "NONE");
    ItemForm.setItemValue("FoldMethod", "NONE");
    ItemForm.setItemValue("ItemCode",itemcode);
    
    var DataSend = {"EVENT":"LOADITEM"};
    DataSend.ITEM = itemcode;
    var Result = AjaxAsync("Data.php", DataSend, "GET", "JSON");
    if(Result.length == 0) {
        if(!confirm("Item Không có trong hệ thống, bạn có muốn thêm vào")) {
            doOnUnload();
            doOnLoad();
        }
    } else {
        Data = Result[0];
        if(Data["MaterialCode"] == null){
            if(confirm("Item Chưa cập nhật đủ thông tin. Bạn muốn cập nhất")) InsertNewItem(itemcode);
            return;
        } else {
            for (const [key, value] of Object.entries(Data)) ItemForm.setItemValue(key, value);
        }
    }

    if(ItemForm.getItemValue("RemarkBot",true) == "") ItemForm.setItemValue("RemarkBot","Lay mau: 15pcs/size bat ky\nDa bu hao vat tu tren don hang");

    ItemForm.attachEvent("onButtonClick", function(name){
        let ItemCode = ItemForm.getItemValue("ItemCode", true);		
        if(name == "SaveItem") {
            var DataSend = {"EVENT":"SAVEITEMCODE"};
            DataSend.Data = JSON.stringify({
                "Item_Code" : ItemForm.getItemValue("ItemCode", true),
                "Material_Code" : ItemForm.getItemValue("MaterialCode", true),
                "Dry" : ItemForm.getItemValue("Drying", true),
                "Heat" : ItemForm.getItemValue("Temp", true),
                "Print_Type" : ItemForm.getItemValue("PrintMethod", true),
                "Cut_Type" : ItemForm.getItemValue("CutMethod", true),
                "Fold_Type" : ItemForm.getItemValue("FoldMethod", true),
                "RemarkTop" : ItemForm.getItemValue("RemarkTop", true),
                "RemarkBot" : ItemForm.getItemValue("RemarkBot", true),
                "NumInk" : ItemForm.getItemValue("InkNum", true),
                "Ink" : ItemForm.getItemValue("InkCode", true)
            });
            var Result = AjaxAsync("Event.php", DataSend, "POST");
                FillData(ItemCode);
                doOnUnload();
                doOnLoad();
        } 
        // else if(name == "FindItem")
        // {
        //      window.open("http://147.121.59.138/Intranet/ArtWork/Index.php?KEYWORD=" + ItemCode + "*VN", '_blank'); 
        // }
        
    });
}

var ItemForm;
function SaveOrder(){
    var DataTable = {"EVENT":"CREATEDJJ"};
        DataTable.MAIN = JSON.stringify(DataPEForm.getFormData());
        if(FOD == undefined) FOD = "";
        DataTable.FOD = FOD;
        DataTable.REMARK = "DON BU";

    GridSOSelect.forEachRow(function(id){
        DataTable.SO += "|" + this.cells(id,0).getValue();
    })
    return AjaxAsync("Event.php",DataTable,"POST");
}

function FormatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [day, month, year].join('-');
}
    
function LoadSO(value){
    if(value == "") dhtmlx.alert("ID Không được rỗng");
    else {						
        var SOLINE = value;
            GridSO.clearAll();
            GridSO.loadXML("Data.php?EVENT=LOADSO&SO=" + SOLINE,function(){
                if(GridSO.getRowsNum() == 0){
                    dhtmlx.alert("Không tìm thấy SO");
                    return;
                }

                NewRow = [];
                if(SOLINE.indexOf("-") !== -1) {
                    GridSO.forEachRow(function(id){
                        if(id == SOLINE) {
                            var rID = id;
                            for (var i=0; i< GridSOSelect.getRowsNum(); i++){
                                if(GridSO.cells(id,1).getValue() != GridSOSelect.cells2(i,1).getValue()) {
                                    dhtmlx.message({
                                        text: "Không thể Combine Khác Item",
                                        expire: 3000
                                    })
                                    return;
                                }
                                if(id == GridSOSelect.cells2(i,0).getValue()) {
                                    dhtmlx.message({
                                        text: "SO Line: " + id + " đã được chọn",
                                        expire: 3000
                                    })
                                    return;
                                }
                            }

                            for(var i = 0; i < 10; i++) NewRow.push(GridSO.cells(id,i).getValue())
                            NewRow.push(id.split("-")[0]);
                            NewRow.push(id.split("-")[1]);
                            GridSOSelect.addRow(id,NewRow);
                            if(GridSOSelect.getRowsNum() == 1) {
                                GridPrepress.clearAll();
                                GridPrepress.loadXML("Data.php?EVENT=LOADSIZE&ITEM=" + GridSO.cells(id,1).getValue(),function(){
                                    if(GridPrepress.getRowsNum() != 0) {
                                        
                                        DataPEForm.setItemValue("LengthLabel", GridPrepress.cells2(0,0).getValue().replaceAll("mm",""));
                                        DataPEForm.setItemValue("WidthLabel", GridPrepress.cells2(0,1).getValue().replaceAll("mm",""));
                                        DataPEForm.setItemValue("Teeth", GridPrepress.cells2(0,2).getValue());
                                    } else {
                                        InsertNewSize(GridSO.cells(id,1).getValue());
                                    }

                                    DataPEForm.setItemValue("ItemCode", GridSO.cells(id,1).getValue());
                                    DataPEForm.setItemValue("ReceivingDate", DatePrint);
                                    DataPEForm.setItemValue("RequestDate", GridSO.cells(id,4).getValue());
                                    DataPEForm.setItemValue("Dueday", GridSO.cells(id,4).getValue());
                                    DataPEForm.setItemValue("CustomerItem", GridSO.cells(id,2).getValue());
                                    DataPEForm.setItemValue("RBO", GridSO.cells(id,6).getValue());
                                    FillData(GridSO.cells(id,1).getValue());
                                    ReloadSize();
                                });
                            } else {
                                ReloadSize();
                            }
                        }
                    })
                }
            });
    }
    ToolbarMainData.setValue("SO","");
}

function AddSO(id){
    NewRow = [];
    var rID = id;
    for (var i=0; i< GridSOSelect.getRowsNum(); i++){
        if(GridSO.cells(id,1).getValue() != GridSOSelect.cells2(i,1).getValue()) {
            dhtmlx.message({
                text: "Không thể Combine Khác Item",
                expire: 3000
            })
            return;
        }
        if(id == GridSOSelect.cells2(i,0).getValue()) {
            dhtmlx.message({
                text: "SO Line: " + id + " đã được chọn",
                expire: 3000
            })
            return;
        }
    }

    for(var i = 0; i < 10; i++) NewRow.push(GridSO.cells(id,i).getValue())
    NewRow.push(id.split("-")[0]);
    NewRow.push(id.split("-")[1]);
    GridSOSelect.addRow(id,NewRow);
    if(GridSOSelect.getRowsNum() == 1) {
        GridPrepress.clearAll();
        GridPrepress.loadXML("Data.php?EVENT=LOADSIZE&ITEM=" + GridSO.cells(id,1).getValue(),function(){
            if(GridPrepress.getRowsNum() != 0) {
                
                DataPEForm.setItemValue("LengthLabel", GridPrepress.cells2(0,0).getValue().replaceAll("mm",""));
                DataPEForm.setItemValue("WidthLabel", GridPrepress.cells2(0,1).getValue().replaceAll("mm",""));
                DataPEForm.setItemValue("Teeth", GridPrepress.cells2(0,2).getValue());
            } else {
                InsertNewSize(GridSO.cells(id,1).getValue());
            }

            DataPEForm.setItemValue("ItemCode", GridSO.cells(id,1).getValue());
            DataPEForm.setItemValue("ReceivingDate", DatePrint);
            DataPEForm.setItemValue("RequestDate", GridSO.cells(id,4).getValue());
            DataPEForm.setItemValue("Dueday", GridSO.cells(id,4).getValue());
            DataPEForm.setItemValue("CustomerItem", GridSO.cells(id,2).getValue());
            DataPEForm.setItemValue("RBO", GridSO.cells(id,6).getValue());
            FillData(GridSO.cells(id,1).getValue());
            ReloadSize();
        });
    } else {
        ReloadSize();
    }
}

function UnlockItem(){
    DataPEForm.setItemValue("SOLine", "");
    DataPEForm.setItemValue("JobJacket", "");
    DataPEForm.setItemValue("ItemCode", "");
    DataPEForm.setItemValue("Line", "");
    DataPEForm.setItemValue("Qty", "");
    DataPEForm.setItemValue("ReceivingDate", "");
    DataPEForm.setItemValue("Dueday", "");
    DataPEForm.setItemValue("CustomerItem", "");
    DataPEForm.setItemValue("RBO", "");
    DataPEForm.setItemValue("RequestDate", "");
    DataPEForm.setItemValue("PromiseDate", "");
    DataPEForm.setItemValue("LengthLabel", "");
    DataPEForm.setItemValue("WidthLabel", "");
    DataPEForm.setItemValue("Teeth", "");
    DataPEForm.setItemValue("QtyScrap", "");
    DataPEForm.setItemValue("Rate", "");
    DataPEForm.setItemValue("QtyNeed", "");
    DataPEForm.setItemValue("NumSize", "");
    DataPEForm.setItemValue("PrintMethod", "");
    DataPEForm.setItemValue("CutMethod", "");
    DataPEForm.setItemValue("FoldMethod", "");
    DataPEForm.setItemValue("ItemDescription", "");
    DataPEForm.setItemValue("InkCode", "");
    DataPEForm.setItemValue("InkNum", "");
    DataPEForm.setItemValue("MaterialCode", "");
    DataPEForm.setItemValue("RateScrap", "");
    DataPEForm.setItemValue("Drying", "");
    DataPEForm.setItemValue("Temp", "");
    DataPEForm.setItemValue("RemarkTop", "");
    DataPEForm.setItemValue("RemarkBot", "");
}
function ConvertDate(D){
    var parts = D.split('-');
    return new Date(parts[2], parts[1] - 1, parts[0]); 
}


var ToolbarMainData;
var DataForm = [
    {type: "settings", position: "label-left", labelWidth: 90, inputWidth: 130},
        {_idd: "162", type: "block", offsetTop: "10", width: "auto", blockOffset: 10, list: [
            {type: "settings", labelWidth: "130", offsetLeft: "10"},
            {_idd: "193", type: "input", label: "Remark", value: "", name: "RemarkJJ"},
            {_idd: "193", type: "input", label: "TEETH", value: "", name: "Teeth"},
            {_idd: "296", type: "input", label: "Lệnh sản xuất:", value: "", name: "JobJacket", readonly: true},
            {_idd: "200", type: "newcolumn"},
            {_idd: "374", type: "checkbox", value: "", label: "FR", name: "ORDER_TYPE", checked: false},
            {_idd: "626", type: "input", label: "RBO:", value: "", name: "RBO"},
            {_idd: "310", type: "input", label: "Tên Sản Phẩm:", value: "", name: "CustomerItem"}
        ]},
        {type: "block", width: "auto", blockOffset: 10, list: [
            {type: "settings", labelWidth: "130", offsetLeft: "10"},
            {_idd: "303", type: "input", label: "Mã Hàng Hóa:", value: "", name: "SOLine"},
            {_idd: "353", type: "input", label: "Ngày Nhận Đơn Hàng:", value: "", name: "ReceivingDate"},
            {_idd: "360", type: "input", label: "Ngày Request:", value: "", name: "RequestDate"},
            {_idd: "367", type: "input", label: "Ngày Giao Hàng:", value: "", name: "Dueday"},
            {_idd: "374", type: "input", value: "", label: "Ngày Promise", name: "PromiseDate"},
            {_idd: "443", type: "input", label: "Độ Dài In:", value: "", name: "LengthLabel"},
            {_idd: "456", type: "input", label: "Chất Liệu Vải:", value: "", name: "MaterialCode", readonly:true},
            {_idd: "463", type: "input", label: "Mực:", value: "", name: "InkNum"},
            {_idd: "526", type: "input", value: "", label: " ", name: "InkCode"},
            {_idd: "557", type: "newcolumn"},
            {_idd: "578", type: "input", label: "Mã Sản Phẩm:", value: "", name: "ItemCode"},
            {_idd: "591", type: "input", label: "Số Lượng Nhãn", value: "", name: "Qty"},
            {_idd: "730", type: "input", label: "Số Lượng Size <button onClick='ComputeIndex()'>C</button>:", value: "", name: "NumSize"},
            {_idd: "598", type: "input", label: "Bù hao theo yard:", offsetTop: 0, value: "", name: "QtyScrap"},
            {type: "input", label: "Rate:", value: "", name: "RateScrap"},
            {_idd: "612", type: "input", value: "", name: "WidthLabel", label: "Width"},
            {_idd: "619", type: "input", label: "Số Lượng Cần (Yard):", value: "", name: "QtyNeed"},
            {_idd: "626", type: "input", label: "Item Description:", value: "", name: "ItemDescription"},
            {_idd: "209", type: "input", label: "LINE", value: "", name: "Line"},
        ]},
        {_idd: "1071", type: "block", width: "auto", blockOffset: "label-right", list: [
            {type: "settings", labelWidth: "120", position: "label-right"},
            {type: "label", label: "Phương Pháp In:", labelWidth: "130"},
            {_idd: "2083", type: "newcolumn"},
            {_idd: "1076", type: "radio", label: "In Mặt Trước", value: "Front Side", name: "PrintMethod"},
            {_idd: "1083", type: "newcolumn"},
            {_idd: "1085", type: "radio", label: "Không In", value: "NONE", name: "PrintMethod"},
            {_idd: "1099", type: "newcolumn"},
            {_idd: "1101", type: "radio", label: "In Hai Mặt", value: "Two Side", name: "PrintMethod"}
        ]},
        {_idd: "1108", type: "block", width: "auto", blockOffset: "label-right", list: [
            {type: "settings", labelWidth: "120", position: "label-right"},
            {type: "label", label: "Phương Pháp Cắt:", value: "", labelWidth: "130"},
            {_idd: "2083", type: "newcolumn"},
            {_idd: "1113", type: "radio", label: "Nóng", value: "HOT CUT", name: "CutMethod"},
            {_idd: "1120", type: "radio", label: "Cao Tần", value: "SONIC CUT", name: "CutMethod"},
            {_idd: "1127", type: "radio", label: "Thẳng Cao Tần", value: "SINGLE LASER CUT", name: "CutMethod"},
            {_idd: "1134", type: "newcolumn"},
            {_idd: "1136", type: "radio", label: "Nguội", value: "COLD CUT", name: "CutMethod"},
            {_idd: "1143", type: "radio", label: "Thẳng Nóng", value: "ROLLS", name: "CutMethod"},
            {_idd: "1143", type: "radio", label: "Lazer", value: "LAZER CUT", name: "CutMethod"},
            {_idd: "1150", type: "newcolumn"},
            {_idd: "1152", type: "radio", label: "Nóng Nguội", value: "COLD HOT CUT", name: "CutMethod"},
            {_idd: "1159", type: "radio", label: "Thẳng Nguội", value: "DIE CUT", name: "CutMethod"},
            {_idd: "1163", type: "radio", label: "Lazer + Hot Cut", value: "LAZER HOT CUT", name: "CutMethod"},
        ]},
        {_idd: "1728", type: "block", width: "auto", blockOffset: 20, list: [
            {type: "settings", position: "label-right", labelWidth: "120"},
            {type: "label", label: "Phương Pháp Gấp:", value: "", labelWidth: "130"},
            {_idd: "2083", type: "newcolumn"},
            {_idd: "1733", type: "radio", label: "Gấp Giữa", value: "CENTER FOLD", name: "FoldMethod"},
            {_idd: "1740", type: "radio", label: "Gấp Sách Lệch", value: "UNEVEN BOOKLET FOLD", name: "FoldMethod"},
            {_idd: "1747", type: "radio", label: "Giao Dạng Cuộn", value: "ROLL", name: "FoldMethod"},
            {_idd: "1786", type: "radio", label: "Không Gấp", value: "NONE", name: "FoldMethod"},
            {_idd: "1754", type: "newcolumn"},
            {_idd: "1756", type: "radio", label: "Gấp Lệch", value: "UNVEN END FOLD", name: "FoldMethod"},
            {_idd: "1763", type: "radio", label: "Gấp Sách", value: "BOOKLET FOLD", name: "FoldMethod"},
            {_idd: "1763", type: "radio", label: "Gấp Giữa + Hai Đầu", value: "CENTER END FOLD", name: "FoldMethod"},
            {_idd: "1763", type: "radio", label: "Gấp Một Đầu", value: "END LEFT FOLD", name: "FoldMethod"},
            {_idd: "1777", type: "newcolumn"},
            {_idd: "1779", type: "radio", label: "Gấp Hai Bên", value: "TOP AND BOTTOM FOLD", name: "FoldMethod"},
            {_idd: "1786", type: "radio", label: "Gấp Hai Manhata", value: "MANHATTAN FOLD", name: "FoldMethod"},
            {_idd: "1786", type: "radio", label: "Gấp MITRE", value: "MITRE FOLD", name: "FoldMethod"},
        ]},
        {type: "block", width: "auto", blockOffset: 20, list: [
            {type: "settings", position: "label-left", labelWidth: "120"},
            {type: "input", label: "Sấy (Phút):", value: "", name: "Drying"},
            {type: "newcolumn"},
            {type: "input", offsetLeft: 10, label: "Độ Nóng:", value: "", name: "Temp"}
        ]},
        {type: "block", width: "auto", blockOffset: 20, list: [
            {type: "settings", position: "label-left", labelWidth: "120", inputWidth: "400"},
            {type: "input", label: "Ghi Chú Trên:", value: "", name: "RemarkTop", rows: 3},
            {type: "input", label: "Ghi Chú Dưới:", value: "", name: "RemarkBot", rows: 3}
        ]}
];

var DataFormInsert = [
    {type: "settings", position: "label-left", labelWidth: 90, inputWidth: 130},
    {type: "fieldset", offsetLeft:20, offsetTop: 20, label: "Main Information", width: "auto", blockOffset: 0, list: [
        {type: "block", width: "auto", blockOffset: 10, list: [
            {type: "settings", labelWidth: "120", offsetLeft: "10"},
            {_idd: "456", type: "input", label: "Chất Liệu Vải:", value: "", name: "MaterialCode"},
            {_idd: "463", type: "input", label: "Số Mực:", value: "", name: "InkNum"},
            {_idd: "557", type: "newcolumn"},
            {_idd: "463", type: "input", label: "Item Code:", labelWidth: "80", inputWidth: "170", value: "", name: "ItemCode"},
            {_idd: "526", type: "input", value: "", labelWidth: "80", inputWidth: "170", label: "Tên Mực", name: "InkCode"},
        ]},
        {_idd: "1071", type: "block", width: "auto", blockOffset: "label-right", list: [
            {type: "settings", labelWidth: "110", position: "label-right"},
            {type: "label", label: "Phương Pháp In:", labelWidth: "110"},
            {_idd: "2083", type: "newcolumn"},
            {_idd: "1076", type: "radio", label: "In Mặt Trước", value: "Front Side", name: "PrintMethod"},
            {_idd: "1083", type: "newcolumn"},
            {_idd: "1085", type: "radio", label: "In Mặt Sau", value: "Back Side", name: "PrintMethod"},
            {_idd: "1099", type: "newcolumn"},
            {_idd: "1101", type: "radio", label: "In Hai Mặt", value: "Two Side", name: "PrintMethod"}
        ]},
        {_idd: "1108", type: "block", width: "auto", blockOffset: "label-right", offsetTop: 10, list: [
            {type: "settings", labelWidth: "110", position: "label-right"},
            {type: "label", label: "Phương Pháp Cắt:", value: "", labelWidth: "110"},
            {_idd: "2083", type: "newcolumn"},
            {_idd: "1113", type: "radio", label: "Nóng", value: "HOT CUT", name: "CutMethod"},
            {_idd: "1120", type: "radio", label: "Cao Tần", value: "SONIC CUT", name: "CutMethod"},
            {_idd: "1127", type: "radio", label: "Thẳng Cao Tần", value: "SINGLE LASER CUT", name: "CutMethod"},
            {_idd: "1163", type: "radio", label: "Lazer + Hot Cut", value: "LAZER HOT CUT", name: "CutMethod"},
            {_idd: "1134", type: "newcolumn"},
            {_idd: "1136", type: "radio", label: "Nguội", value: "COLD CUT", name: "CutMethod"},
            {_idd: "1143", type: "radio", label: "Thẳng Nóng", value: "ROLLS", name: "CutMethod"},
            {_idd: "1143", type: "radio", label: "Lazer", value: "LAZER CUT", name: "CutMethod"},
            {_idd: "1150", type: "newcolumn"},
            {_idd: "1152", type: "radio", label: "Nóng Nguội", value: "COLD HOT CUT", name: "CutMethod"},
            {_idd: "1159", type: "radio", label: "Thẳng Nguội", value: "DIE CUT", name: "CutMethod"},
            {_idd: "1159", type: "radio", label: "Không Cắt", value: "NONE", name: "CutMethod"}
        ]},
        {_idd: "1728", type: "block", width: "auto", blockOffset: 20, offsetTop: 10, list: [
            {type: "settings", position: "label-right", labelWidth: "110"},
            {type: "label", label: "Phương Pháp Gấp:", value: "", labelWidth: "110"},
            {_idd: "2083", type: "newcolumn"},
            {_idd: "1733", type: "radio", label: "Gấp Giữa", value: "CENTER FOLD", name: "FoldMethod"},
            {_idd: "1740", type: "radio", label: "Gấp Sách Lệch", value: "UNEVEN BOOKLET FOLD", name: "FoldMethod"},
            {_idd: "1747", type: "radio", label: "Giao Dạng Cuộn", value: "ROLL", name: "FoldMethod"},
            {_idd: "1786", type: "radio", label: "Không Gấp", value: "NONE", name: "FoldMethod"},
            {_idd: "1754", type: "newcolumn"},
            {_idd: "1756", type: "radio", label: "Gấp Lệch", value: "MITRE FOLD", name: "FoldMethod"},
            {_idd: "1763", type: "radio", label: "Gấp Sách", value: "BOOKLET FOLD", name: "FoldMethod"},
            {_idd: "1763", type: "radio", label: "Gấp Giữa + Hai Đầu", value: "CENTER END FOLD", name: "FoldMethod"},
            {_idd: "1763", type: "radio", label: "Gấp Một Đầu", value: "END LEFT FOLD", name: "FoldMethod"},
            {_idd: "1777", type: "newcolumn"},
            {_idd: "1779", type: "radio", label: "Gấp Hai Bên", value: "TOP AND BOTTOM FOLD", name: "FoldMethod"},
            {_idd: "1786", type: "radio", label: "Gấp Hai Manhata", value: "MANHATTAN FOLD", name: "FoldMethod"},
            {_idd: "1786", type: "radio", label: "Gấp MITRE", value: "MITRE FOLD", name: "FoldMethod"},

        ]},
        {type: "block", width: "auto", blockOffset: 20, list: [
            {type: "settings", position: "label-left", labelWidth: "120"},
            {type: "input", label: "Sấy (Phút):", value: "", name: "Drying"},
            {type: "newcolumn"},
            {type: "input", offsetLeft: 10, inputWidth: 136, label: "Độ Nóng:", value: "", name: "Temp"}
        ]},
        {type: "block", width: "auto", blockOffset: 20, list: [
            {type: "settings", position: "label-left", labelWidth: "120", inputWidth: "400"},
            {type: "input", label: "Ghi Chú Trên:", value: "", name: "RemarkTop", rows: 3},
            {type: "input", label: "Ghi Chú Dưới:", value: "", name: "RemarkBot", rows: 3}
        ]},
        {type: "block", width: "auto", blockOffset: 20, list: [
            {type: "settings", position: "label-left", labelWidth: "120", inputWidth: "400"},
            {type: "button", value: "Save", name: "SaveItem", rows: 3},
            {type: "newcolumn"},
            {type: "button", value: "Find Item", name: "FindItem", rows: 3}
        ]}
    ]}
];    

function doOnLoad() {
    dhxWins = new dhtmlXWindows();
    dhxWins.attachViewportTo(document.body);
}

function doOnUnload() {
    if (dhxWins != null && dhxWins.unload != null) {
        dhxWins.unload();
        dhxWins = null;
    }
}

var dhxWinsNew

</script>
    <body onload="doOnLoad();" onunload="doOnUnload();">
    </body>
</html>