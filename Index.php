<?php 
    require("./Module/Template.php");
    InitPage("PFLPrintJob","PFL Printing");
    $email = getUser();

?>

<script>
    function setCookie(cname,cvalue,exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires=" + d.toGMTString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

    
    var check_gg = 0;
    function DocumentStart(){
        var VNRISIntranet = '<?php echo isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : ""; ?>';
        console.log("ZeroIntranet: "+VNRISIntranet);
        if (!VNRISIntranet ) {
            var pr = prompt('Nhập tiền tố email trước @. Ví dụ: tan.doan', '');
            pr = pr.trim();
            if (!pr || pr.indexOf('@') !== -1 ) {
                alert('Bạn vui lòng nhập đúng tiền tố email là phần trước @');
            } else {
                // Save email đến bảng thống kê (au_avery.planning_user_statistics)
                setCookie('ZeroIntranet', pr, 30 );
                // setCookie('VNRISIntranet', pr, 30 );
                var VNRISIntranet = '<?php echo isset($_COOKIE["ZeroIntranet"]) ? $_COOKIE["ZeroIntranet"] : ""; ?>';
                var pr_s = '<?php echo planning_user_statistics($email); ?>';
                console.log('save planning_user_statistics: ' + pr_s);
                
                check_gg = 1;
            }
            
           
        }

        // if (check_gg) location.reload();
        if (check_gg ) {
            location.href= './';
        }
        
        dhtmlx.message({
                    type: "myCssNK",
                    text: "Nhập SO# muốn in",
                    expire: 5000
                });
                // dhtmlx.message({
                //     type: "myCss",
                //     text: "Đâu là đây",
                //     expire: 10000
                // });
                
                // dhtmlx.message({
                //     type: "error",
                //     text: "Là đâu đây",
                //     expire: 10000
                // });

        ToolbarMain.unload();
        ToolbarMain = null;
        // // LayoutMain.cells("a").attachObject("SearchBar");
        $("#SearchBar").css({"background": "url('Images/Background/B (<?php echo rand(1,63); ?>).jpg')", "background-size": "auto 100%",
            "background-repeat":"no-repeat", "background-position": "center center","background-color":"black"});
        $('#search').keydown(function (e) {
            if (e.keyCode == 13) {
                var JJ = $('#search').val();
                var Result = AjaxAsync("Data.php",{"EVENT":"LOADJOBPRINT","JOBJACKET":JJ});
                var Data = Result.split("|");
                if(Data[0] == "OK") window.open("PrintPage.php?JJ=" + Data[1]);
                else if(Data[0] == "NG") {
                        dhtmlx.message({
                            type: "error",
                            text: Data[1],
                            expire: 10000
                        });
                } else if(Data[0] == "NO") {
                        dhtmlx.message({
                            type: "myCss",
                            text: Data[1],
                            expire: 10000
                        });
                } else if(Data[0] == "NK") {
                        dhtmlx.message({
                            type: "myCssNK",
                            text: Data[1],
                            expire: 10000
                        });
                }
                $('#search').val("");
            }
        });
    }


    function OpenJob(){
        var JJ = $('#search').val();
        var Result = AjaxAsync("Data.php",{"EVENT":"LOADJOBPRINT","JOBJACKET":JJ});
        var Data = Result.split("|");
        if(Data[0] == "OK") window.open("PrintPage.php?JJ=" + Data[1]);
        else {
                dhtmlx.message({
                type: "error",
                text: Data[1],
                expire: 10000
            })
        }
        $('#search').val("");
    }

        
</script>

<link rel="stylesheet" href="./Module/css/bootstrap.min.css">
<script src="./Module/JS/bootstrap.min.js"></script>
<style type="text/css">
    .bs-example{
        bottom: 0;
        position: absolute;
        width: 100%;
    }
</style>

<style type="text/css">
    .dhtmlx-myCss{
        font-weight:bold !important;
        color:black !important;
        background-color:yellow !important;
    }

    .dhtmlx-myCssNK div{
        font-weight:bold !important;
        color:white !important;
        background-color:green !important;
    }
</style>

<body>
<div id="SearchBar" style="width:100%;height:95%">
    <div class="bs-example">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <input id="search" type="text" class="form-control mr-sm-2" placeholder="Vui lòng nhập SOLine muốn in lệnh sản xuất " autofocus>
                <button type="submit" style="width:10%;font-weight:bold" onClick="OpenJob()" class="btn btn-outline-light">Tìm in đơn</button>
            </div>
        </nav>
    </div>
</div>
</body>
</html>