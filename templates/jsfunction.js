// init var
var mainMenu;
var mainToolbar;

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

        // menu
        mainMenu();

        // toolbar
        mainToolbar();

        //get Soline input
        mainToolbar.getInput("so_line_input").focus();

        inputData();
        onClickMainToolbar();
    });

}

// menu
function mainMenu() {
    mainMenu = new dhtmlXMenuObject({
        parent: "mainMenu",
        iconset: "awesome",
        json: "./Module/xml/main_menu.xml",
        top_text: '<img style="width:60px;" src = "./Module/Images/Logo.PNG">&nbsp; PFL JOBJACKET'
    });
    mainMenu.setAlign("right");

    mainMenu.attachEvent("onClick", function(id) {
        if (id !== "home") {

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
    mainToolbar.addButton("so_line_label", 3, "<span style='color:blue;font-weight:bold;font-size:13px;'>SOLine</span>", "fa fa-fire");
    mainToolbar.addInput("so_line_input", 4, "", 100);
    mainToolbar.addButton("item_label", 5, "<span style='color:blue;font-weight:bold;font-size:13px;'>Item</span>", "fa fa-info-circle fa-2x");
    mainToolbar.addInput("item_input", 6, "", 120);
    mainToolbar.addSeparator("separator_1", 7);
    mainToolbar.addText("automail", 8, "Automail updated: <span style='color:red;font-weight:bold;font-size:12px;'>" + automail_updated + "</span>");
    mainToolbar.addSpacer("automail");\
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
    var username = getCookie('plan_loginUser');
    if (username) {
        username_label = "<span style='color:blue;font-weight:bold;font-size:12px;'>" + username + "</span>";
    }

    myPop.attachEvent("onShow", function() {

        // check if myForm is not inited - call init once when popup shown 1st time
        // another way to check is if (myForm instanceof dhtmlXForm)
        if (!myAccountForm) {

            myAccountForm = myPop.attachForm([{
                    type: "settings",
                    position: "label-left",
                    width: 230
                },
                // {type: "label", label: username_label, offsetRight: 10 },
                {
                    type: "block",
                    width: 230,
                    list: [{
                            type: "button",
                            name: "changePass",
                            value: "Change Password",
                            width: 100,
                            offsetRight: 10
                        },
                        {
                            type: "newcolumn"
                        },
                        {
                            type: "button",
                            name: "logout",
                            value: "Logout",
                            width: 80,
                            offsetLeft: 20
                        }
                    ]
                }
            ]);

            myAccountForm.attachEvent("onButtonClick", function(name) {
                if (name == 'changePass') {
                    changeProfile(username);
                } else {
                    logout();
                }
                myPop.hide();
            });

        }

        myAccountForm.setFocusOnFirstActive();

    });

    mainToolbar.setItemText('my_account', username_label);

}