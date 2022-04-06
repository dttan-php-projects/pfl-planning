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
        text: "MASTER ITEM"
      }, ]
    });
  }


  var MasterItemGrid;

  function MasterItemGrid() {
    LayoutMain.cells("a").progressOn();
    MasterItemGrid = LayoutMain.cells("a").attachGrid();

    MasterItemGrid.setImagePath("../Module/dhtmlx/skins/skyblue/imgs/");
    MasterItemGrid.setRowTextStyle("1", "background-color: red; font-family: arial;");
    MasterItemGrid.init();

    // MasterItemGrid.enableAutoWidth(true);
    MasterItemGrid.enableSmartRendering(true); // false to disable

    MasterItemGrid.loadXML("./Handle.php?EVENT=MASTERDATAGRID", function() {
      LayoutMain.cells("a").progressOff();
    });

  }

  var MasterItemToolbar;

  function MasterItemToolbar() {
    MasterItemToolbar = new dhtmlXToolbarObject({
      parent: "MasterItemToolbar",
      icons_path: "../Module/dhtmlx/common/imgs/",
      align: "left"
    });

    MasterItemToolbar.addText("", 1, "<a style='font-size:20pt;font-weight:bold'>PFL MASTER FILE</a>");
    MasterItemToolbar.addButton("spacer", 2, "", "");
    MasterItemToolbar.addSpacer("spacer");
    
    MasterItemToolbar.addButton("Home", 3, "Quay về làm lệnh", "");

    MasterItemToolbar.addText("", 4, " | ");
    var mf_opts = [
        ['Imports_Master_Data', 'obj', '1. Imports (.XLSX)', 'xlsx.gif'],
        ['sep01', 'sep', '', ''],
        ['Exports_Master_Data', 'obj', '2. Exports (.XLSX)', 'xlsx.gif'],
        ['sep02', 'sep', '', ''],
        ['Sample_File', 'obj', '3. Download Sample File (Imports)', 'xlsx.gif'],
        ['sep03', 'sep', '', '']
    ];
    MasterItemToolbar.addButtonSelect("Master_Data", 5, "Master Data", mf_opts, "database.gif");
    MasterItemToolbar.addText("", 6, " | ");

    var printSpeed_opts = [
        ['Update_Print_Speed', 'obj', '1. Update Print Speed (.XLSX)', 'xlsx.gif'],
        ['sep05', 'sep', '', ''],
        ['Sample_File_Print_Speed', 'obj', '2. Download Sample File', 'xlsx.gif'],
        ['sep06', 'sep', '', '']
    ];
    MasterItemToolbar.addButtonSelect("Print_Speed", 7, "Print Speed", printSpeed_opts, "database.gif");

    var fod_item_opts = [
        ['Fod_Imports', 'obj', '1. Imports (.XLSX)', 'xlsx.gif'],
        ['sep01', 'sep', '', ''],
        ['Fod_Sample_File', 'obj', '2. Download Sample File (Imports)', 'xlsx.gif'],
        ['sep03', 'sep', '', '']
    ];
    MasterItemToolbar.addButtonSelect("item_fod", 5, "Item FOD", fod_item_opts, "database.gif");

    MasterItemToolbar.addText("", 8, " | ");
    // MasterItemToolbar.addButton("Imports_Master_Data", 8, "<a style='font-size:9pt;font-weight:bold'>1. Imports (.XLSX)</a>", "");
    // MasterItemToolbar.addButton("Delete_Master_Data", 10, "<a style='font-size:9pt;font-weight:bold;color:blue;'>3. Delete Multiple (Selected)</a>", "");

    MasterItemToolbar.addText("", 17, " ||| ");
    
    // attach
    MasterItemToolbar.attachEvent("onClick", function(name) {

      //console.log(name);
      if (name == "Home" ) {
        location.href = "../ProductionRecord.php";
      } else if (name == "Imports_Master_Data") { // insert or update
        Imports();
      } else if (name == "Exports_Master_Data") {
        // UploadFile();
      } else if (name == "Delete_Master_Data") {
        // importDelFile();
      } else if (name == "Sample_File" ) {
        exportSample();
      } else if (name == "Update_Print_Speed" ) {
        PrintSpeedImports();
      } else if (name == "Sample_File_Print_Speed" ) {
        exportSamplePrintSpeed();
      } else if (name == "Fod_Imports" ) {
        ItemFODImports();
      } else if (name == "Fod_Sample_File" ) {
        location.href = "https://docs.google.com/spreadsheets/d/1xuOM2Mm37zofelkh3uidGwz_krP6DxiFUYTnLU0IN8M/edit?usp=sharing";
      }

      item_fod

    });
  }

  // imports
  function Imports() {
    var conf = confirm("Vui lòng sử dụng File Excel .XLSX để Import dữ liệu");
    if (!conf) location.reload();

    var dhxWins;
    if (!dhxWins) {
      dhxWins = new dhtmlXWindows();
    }

    var id = "WindowsDetail";
    var w = 400;
    var h = 100;
    var x = Number(($(window).width() - 400) / 2);
    var y = Number(($(window).height() - 50) / 2);
    var Popup = dhxWins.createWindow(id, x, y, w, h);
    dhxWins.window(id).setText("Imports (.XLSX)");
    Popup.attachHTMLString(
      '<div style="width:500%;margin:20px">' +
      '<form action="./uploadMasterData.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
      '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
      '<input type="submit" name="submit" value="Import" id="importfile-id" class="btn btn-block btn-primary"  />' +
      '</form>' +
      '</div>'
    );
  }

  function exportSample()
  {
    // location.href = './excel/PFL_Master_Data_Sample.xlsx';
    var url = 'https://docs.google.com/spreadsheets/d/1f9EdTaw9LBYrV1_6jGhmo_S_qtJqXZNjd8A_dWfi4CI/edit?usp=sharing';
    window.open(url,'_blank');
    // window.open(url);
  }

  function PrintSpeedImports() {
    var conf = confirm("Vui lòng sử dụng File Excel .XLSX để Import dữ liệu");
    if (!conf) location.reload();

    var dhxWins;
    if (!dhxWins) {
      dhxWins = new dhtmlXWindows();
    }

    var id = "WindowsDetail";
    var w = 400;
    var h = 100;
    var x = Number(($(window).width() - 400) / 2);
    var y = Number(($(window).height() - 50) / 2);
    var Popup = dhxWins.createWindow(id, x, y, w, h);
    dhxWins.window(id).setText("Imports (.XLSX)");
    Popup.attachHTMLString(
      '<div style="width:500%;margin:20px">' +
      '<form action="./uploadPrintSpeed.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
      '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
      '<input type="submit" name="submit" value="Import" id="importfile-id" class="btn btn-block btn-primary"  />' +
      '</form>' +
      '</div>'
    );
  }

  function ItemFODImports() {
    var conf = confirm("Vui lòng sử dụng File Excel .XLSX để Import dữ liệu");
    if (!conf) location.reload();

    var dhxWins;
    if (!dhxWins) {
      dhxWins = new dhtmlXWindows();
    }

    var id = "WindowsDetail";
    var w = 400;
    var h = 100;
    var x = Number(($(window).width() - 400) / 2);
    var y = Number(($(window).height() - 50) / 2);
    var Popup = dhxWins.createWindow(id, x, y, w, h);
    dhxWins.window(id).setText("Imports (.XLSX)");
    Popup.attachHTMLString(
      '<div style="width:500%;margin:20px">' +
      '<form action="./uploadItemFOD.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
      '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
      '<input type="submit" name="submit" value="Import" id="importfile-id" class="btn btn-block btn-primary"  />' +
      '</form>' +
      '</div>'
    );
  }

  

  function exportSamplePrintSpeed()
  {
    var url = 'https://docs.google.com/spreadsheets/d/1NDRoOlb-cYcZvHTnd0BBwnag5C2yU51FGGeHJ37QFeg/edit?usp=sharing';
    window.open(url,'_blank');
  }

  // load
  MasterItemToolbar();
  initLayout();
  MasterItemGrid();
</script>