<script>
    // init var
    var mainMenu;
    var mainToolbar;
    var myAccountForm;
    var automail_log = '<?php echo getAutomailLog(); ?>';

    // set cookie
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    // get cookie
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return false;
    }

    function AjaxAsync(urlsend, dtsend, typeSend = "GET", datatype = "html") {
        var it_works;

        $.ajax({
            url: urlsend,
            type: typeSend.toUpperCase(),
            dataType: datatype.toUpperCase(),
            cache: false,
            data: dtsend,
            success: function(string) {
                it_works = string;
            },
            error: function() {
                it_works = 'ERROR';
            },
            async: false
        });
        return it_works;
    }

    function getUser() {
        var username = (getCookie('ZeroIntranet')) ? getCookie('ZeroIntranet') : '';
        if (!username) {
            username = (getCookie('VNRISIntranet')) ? getCookie('VNRISIntranet') : '';
        }

        return username;
    }

    // onload
    function doOnLoad() {
        $(document).ready(function() {

            if (!getUser()) {
                var pr = prompt("Nhập email \(trước dấu @\) để đăng nhập", '');
                if (!pr || (pr.indexOf('@') !== -1)) {
                    alert('Bạn đã nhập sai email. Chỉ nhập các ký tự trước @. Ví dụ: tan.doan');
                    location.reload();
                }
            }

            // menu
            mainMenu();

            // toolbar
            mainToolbar();

            //get Soline input
            mainToolbar.getInput("so_line_input").focus();

            // inputData();
            // onClickMainToolbar();
        });

    }

    // menu
    function mainMenu() {

        mainMenu = new dhtmlXMenuObject({
            parent: "mainMenu",
            iconset: "awesome",
            json: "./Module/xml/main_menu.xml",
            top_text: '<img class="menu-img" src="./Module/Images/menu.png">&nbsp;<span class="top-text"> PFL JOBJACKET</span>'
        });
        mainMenu.setAlign("right");

        mainMenu.attachEvent("onClick", function(id) {
            if (id !== "home") {
                if (id == 'production_record') {
                    location.href = "./ProductionRecord.php";
                } else if (id == 'order_list') {

                }
            } else {
                if (!getUser()) {
                    alert('Bạn chưa đăng nhập');
                }

                location.href = "./";

            }
        });
    }

    function mainToolbar() {

        // attach to sidebar
        mainToolbar = new dhtmlXToolbarObject({
            parent: "mainToolbar",
            icons_size: 18,
            iconset: "awesome"
        });
        // init item
        mainToolbar.addButton("so_line_label", 3, "<span style='color:blue;font-weight:bold;font-size:13px;'>Tìm đơn in</span>", "fa fa-fire");
        mainToolbar.addInput("so_line_input", 4, "", 100);
        mainToolbar.addSeparator("separator_1", 7);
        mainToolbar.addText("automail", 8, "Automail updated: <span style='color:red;font-weight:bold;font-size:12px;'>" + automail_log + "</span>");
        mainToolbar.addSpacer("automail");
        mainToolbar.addText("from_date_label", 11, "Date: From");
        mainToolbar.addInput("from_date", 12, "", 80);
        mainToolbar.addText("to_date_label", 13, "to");
        mainToolbar.addInput("to_date", 14, "", 80);
        mainToolbar.addSeparator("separator_2", 15);
        mainToolbar.addButton("my_account", 16, "<span style='color:blue;font-weight:bold;font-size:12px;'>My Account</span>", "fa fa-sign-out");
        mainToolbar.addSeparator("separator_3", 20);

        // Init calendar, attach from date and to date
        from_date = mainToolbar.getInput("from_date");
        to_date = mainToolbar.getInput("to_date");

        myCalendar = new dhtmlXCalendarObject([from_date, to_date]);
        myCalendar.setDateFormat("%Y-%m-%d");

        // Init Popup and attach form my account   
        myPop = new dhtmlXPopup({
            toolbar: mainToolbar,
            id: "my_account"
        });

        var username_label = 'Loading...';
        var username = getUser();
        if (username) {
            username_label = "<span style='color:blue;font-weight:bold;font-size:12px;'>" + username + "</span>";
        }

        // Show user and logout
        myPop.attachEvent("onShow", function() {

            // check if myForm is not inited - call init once when popup shown 1st time
            // another way to check is if (myForm instanceof dhtmlXForm)
            if (!myAccountForm) {

                myAccountForm = myPop.attachForm([{
                        type: "settings",
                        position: "label-left",
                        width: 150
                    },
                    {
                        type: "block",
                        width: 120,
                        list: [{
                            type: "button",
                            name: "logout",
                            value: "Logout",
                            width: 80,
                            offsetLeft: 20
                        }]
                    }
                ]);

                myAccountForm.attachEvent("onButtonClick", function(name) {
                    if (name == 'logout') {
                        logout();
                    }
                    myPop.hide();
                });

            }

            myAccountForm.setFocusOnFirstActive();

        });

        mainToolbar.setItemText('my_account', username_label);

        // attach events
        mainToolbar.attachEvent("onEnter", function(name) {
            //get Soline input and item input
            so_line_input = mainToolbar.getInput("so_line_input");

            if (name == "so_line_input") {
                var JJ = so_line_input.value;
                var Result = AjaxAsync("Data.php", {
                    "EVENT": "LOADJOBPRINT",
                    "JOBJACKET": JJ
                });
                var Data = Result.split("|");
                if (Data[0] == "OK") window.open("PrintPage.php?JJ=" + Data[1]);
                else if (Data[0] == "NG") {
                    dhtmlx.message({
                        type: "error",
                        text: Data[1],
                        expire: 10000
                    });
                } else if (Data[0] == "NO") {
                    dhtmlx.message({
                        type: "myCss",
                        text: Data[1],
                        expire: 10000
                    });
                } else if (Data[0] == "NK") {
                    dhtmlx.message({
                        type: "myCssNK",
                        text: Data[1],
                        expire: 10000
                    });
                }

                // set focus
                so_line_input.focus();

            }
        });

    }
</script>