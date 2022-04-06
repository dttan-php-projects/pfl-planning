<!DOCTYPE html>
<html>

<head>
    <title>Master Data</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="../Module/dhtmlx/codebase/dhtmlx.js" type="text/javascript"></script>
    <link rel="STYLESHEET" type="text/css" href="../Module/dhtmlx/skins/skyblue/dhtmlx.css">
    <script src="../Module/JS/jquery-1.10.1.min.js"></script>
    <link rel="icon" href="../Module/Images/Logo.ico" type="image/x-icon">
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            font-family: "Source Sans Pro", "Helvetica Neue", Helvetica;
            background-repeat: no-repeat;
            background-size: 100%;
        }
    </style>
</head>

<body>
    <div id="MasterItemToolbar" style="width:100%;"> </div>
</body>

</html>

<script>
    var LayoutMain;

    function initLayout() {
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "1C",
            offsets: {
                top: 30
            },
            cells: [{
                id: "a",
                header: true,
                text: "Vui lòng chọn Khoảng ngày để LOAD data. Sau đó mới có thể Exports (.CSV) data"
            }, ]
        });
    }


    var GridMain;
   

    function MasterItemGrid() {

        var DateFrom = MasterItemToolbar.getValue("from_date");
        var DateTo = MasterItemToolbar.getValue("to_date");

        if (DateFrom && DateTo ) {

            LayoutMain.cells("a").progressOn();
            GridMain = LayoutMain.cells("a").attachGrid();
            GridMain.setImagePath("./Module/dhtmlx/skins/skyblue/imgs/");
            GridMain.setHeader(",ID,DATE,SO THU TU,DON HANG,ITEM CODE,CUSTOMER,ORDER ITEM,QTY-PCS,QTY-YARD,SO SIZE,KICH THUOC NHAN,MA VAT TU,VAT TU,REQUEST DATE,JOB NUMBER,TONG SO MAU MUC, TEETH,REMARK, PIC, UEE, NGÀY TẠO, P");
            GridMain.setInitWidths("40,50,100,100,100,100,100,100,100,100,100,100,100,*,*,100,100,100,100,100,70,50,70")
            GridMain.setColumnMinWidth("40,50,140,80,40,100,100,80,70,140,140,140,120,140,140,100,100,100,100,100,70,50,70")
            GridMain.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");
            GridMain.setColTypes("ro,ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,txt,txt,txt,txt,txt,ed,ed,ed,ed,ed,ed");
            GridMain.setColSorting("str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str")
            GridMain.setRowTextStyle("1", "background-color: red; font-family: arial;");
            GridMain.entBox.id = "GridMain";
            GridMain.enableBlockSelection(true);
            GridMain.enableMultiselect(true);
            GridMain.setStyle(
                "",
                "", "background:limegreen;", ""
            );
            GridMain.init();

            // MasterItemGrid.enableAutoWidth(true);
            GridMain.enableSmartRendering(true); // false to disable


            GridMain.loadXML("./GetData.php?EVENT=LOADORDERLIST&F=" + DateFrom + "&T=" + DateTo, function() {
                LayoutMain.cells("a").progressOff();
            });
        } else {
            alert("Chọn ngày đến ngày");
        }

        

    }

    var MasterItemToolbar;

    function MasterItemToolbar() {
        MasterItemToolbar = new dhtmlXToolbarObject({
            parent: "MasterItemToolbar",
            icons_path: "../Module/dhtmlx/common/imgs/",
            align: "left"
        });

        MasterItemToolbar.addText("", 1, "<a style='font-size:20pt;font-weight:bold'>PFL - EXPORTS</a>");
        MasterItemToolbar.addButton("spacer", 2, "", "");
        MasterItemToolbar.addSpacer("spacer");

        MasterItemToolbar.addText("from_date_label", 11, "From");
        MasterItemToolbar.addInput("from_date", 12, "", 80);
        MasterItemToolbar.addText("to_date_label", 13, "to");
        MasterItemToolbar.addInput("to_date", 14, "", 80);
        MasterItemToolbar.addSeparator("separator_2", 15);

        // Init calendar, attach from date and to date
        from_date = MasterItemToolbar.getInput("from_date");
        to_date = MasterItemToolbar.getInput("to_date");

        myCalendar = new dhtmlXCalendarObject([from_date, to_date]);
        myCalendar.setDateFormat("%Y-%m-%d");
        
        MasterItemToolbar.addButton("load", 16, "<a style='font-size:11pt;font-weight:bold; color:red;'>1. LOAD</a>", "");
        MasterItemToolbar.addText("", 17, " | ");
        MasterItemToolbar.addButton("Exports", 20, "<a style='font-size:11pt;font-weight:bold;color:red;'>2. Exports</a>", "");
        MasterItemToolbar.addText("", 21, " | ");
        
        // MasterItemToolbar.addButton("Delete_Master_Data", 10, "<a style='font-size:9pt;font-weight:bold;color:blue;'>3. Delete Multiple (Selected)</a>", "");

        MasterItemToolbar.addText("", 25, " ||| ");

        // attach
        MasterItemToolbar.attachEvent("onClick", function(name) {

            //console.log(name);
            if (name == "Exports") {
                Exports();
            } else if (name == "load" ) {
                MasterItemGrid();
            }

        });
    }


    function Exports() {
        GridMain.enableCSVHeader(true);
        GridMain.setCSVDelimiter(',');
        var csv = GridMain.serializeToCSV();
        filename = 'Exports.csv';

        if (csv == null) return;
        if (!csv.match(/^data:text\/csv/i)) {
            csv = 'data:text/csv;charset=utf-8,' + csv;
        }

        // data = csv;
        data = encodeURI(csv);
        //data = CSVToArray(data,',');
        for (var k = 0; k <= 100; k++) {
            data = data.replace('&amp;', '&');
        }
        link = document.createElement('a');
        link.setAttribute('href', data);
        link.setAttribute('download', filename);
        link.click();

    }

    // load
    MasterItemToolbar();
    initLayout();

    
</script>