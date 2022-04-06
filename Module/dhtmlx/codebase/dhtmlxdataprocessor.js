//v.3.0 build 110704

/*
Copyright Dinamenta, UAB http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/
function dataProcessor(a){this.serverProcessor=a;this.action_param="!nativeeditor_status";this.object=null;this.updatedRows=[];this.autoUpdate=!0;this.updateMode="cell";this._tMode="GET";this.post_delim="_";this._waitMode=0;this._in_progress={};this._invalid={};this.mandatoryFields=[];this.messages=[];this.styles={updated:"font-weight:bold;",inserted:"font-weight:bold;",deleted:"text-decoration : line-through;",invalid:"background-color:FFE0E0;",invalid_cell:"border-bottom:2px solid red;",clear:"font-weight:normal;text-decoration:none;"};
this.enableUTFencoding(!0);dhtmlxEventable(this);return this}
dataProcessor.prototype={setTransactionMode:function(a,b){this._tMode=a;this._tSend=b},escape:function(a){return this._utf?encodeURIComponent(a):escape(a)},enableUTFencoding:function(a){this._utf=convertStringToBoolean(a)},setDataColumns:function(a){this._columns=typeof a=="string"?a.split(","):a},getSyncState:function(){return!this.updatedRows.length},enableDataNames:function(a){this._endnm=convertStringToBoolean(a)},enablePartialDataSend:function(a){this._changed=convertStringToBoolean(a)},setUpdateMode:function(a,
b){this.autoUpdate=a=="cell";this.updateMode=a;this.dnd=b},ignore:function(a,b){this._silent_mode=!0;a.call(b||window);this._silent_mode=!1},setUpdated:function(a,b,c){if(!this._silent_mode){var d=this.findRow(a),c=c||"updated",e=this.obj.getUserData(a,this.action_param);e&&c=="updated"&&(c=e);b?(this.set_invalid(a,!1),this.updatedRows[d]=a,this.obj.setUserData(a,this.action_param,c),this._in_progress[a]&&(this._in_progress[a]="wait")):this.is_invalid(a)||(this.updatedRows.splice(d,1),this.obj.setUserData(a,
this.action_param,""));b||this._clearUpdateFlag(a);this.markRow(a,b,c);b&&this.autoUpdate&&this.sendData(a)}},_clearUpdateFlag:function(){},markRow:function(a,b,c){var d="",e=this.is_invalid(a);e&&(d=this.styles[e],b=!0);if(this.callEvent("onRowMark",[a,b,c,e])&&(d=this.styles[b?c:"clear"]+d,this.obj[this._methods[0]](a,d),e&&e.details)){d+=this.styles[e+"_cell"];for(var f=0;f<e.details.length;f++)if(e.details[f])this.obj[this._methods[1]](a,f,d)}},getState:function(a){return this.obj.getUserData(a,
this.action_param)},is_invalid:function(a){return this._invalid[a]},set_invalid:function(a,b,c){c&&(b={value:b,details:c,toString:function(){return this.value.toString()}});this._invalid[a]=b},checkBeforeUpdate:function(){return!0},sendData:function(a){if(!this._waitMode||!(this.obj.mytype=="tree"||this.obj._h2)){this.obj.editStop&&this.obj.editStop();if(typeof a=="undefined"||this._tSend)return this.sendAllData();if(this._in_progress[a])return!1;this.messages=[];if(!this.checkBeforeUpdate(a)&&this.callEvent("onValidationError",
[a,this.messages]))return!1;this._beforeSendData(this._getRowData(a),a)}},_beforeSendData:function(a,b){if(!this.callEvent("onBeforeUpdate",[b,this.getState(b),a]))return!1;this._sendData(a,b)},serialize:function(a,b){if(typeof a=="string")return a;if(typeof b!="undefined")return this.serialize_one(a,"");else{var c=[],d=[],e;for(e in a)a.hasOwnProperty(e)&&(c.push(this.serialize_one(a[e],e+this.post_delim)),d.push(e));c.push("ids="+this.escape(d.join(",")));return c.join("&")}},serialize_one:function(a,
b){if(typeof a=="string")return a;var c=[],d;for(d in a)a.hasOwnProperty(d)&&c.push(this.escape((b||"")+d)+"="+this.escape(a[d]));return c.join("&")},_sendData:function(a,b){if(a){if(!this.callEvent("onBeforeDataSending",b?[b,this.getState(b),a]:[null,null,a]))return!1;b&&(this._in_progress[b]=(new Date).valueOf());var c=new dtmlXMLLoaderObject(this.afterUpdate,this,!0),d=this.serverProcessor+(this._user?getUrlSymbol(this.serverProcessor)+["dhx_user="+this._user,"dhx_version="+this.obj.getUserData(0,
"version")].join("&"):"");this._tMode!="POST"?c.loadXML(d+(d.indexOf("?")!=-1?"&":"?")+this.serialize(a,b)):c.loadXML(d,!0,this.serialize(a,b));this._waitMode++}},sendAllData:function(){if(this.updatedRows.length){this.messages=[];for(var a=!0,b=0;b<this.updatedRows.length;b++)a&=this.checkBeforeUpdate(this.updatedRows[b]);if(!a&&!this.callEvent("onValidationError",["",this.messages]))return!1;if(this._tSend)this._sendData(this._getAllData());else for(b=0;b<this.updatedRows.length;b++)if(!this._in_progress[this.updatedRows[b]]&&
!this.is_invalid(this.updatedRows[b])&&(this._beforeSendData(this._getRowData(this.updatedRows[b]),this.updatedRows[b]),this._waitMode&&(this.obj.mytype=="tree"||this.obj._h2)))break}},_getAllData:function(){for(var a={},b=!1,c=0;c<this.updatedRows.length;c++){var d=this.updatedRows[c];!this._in_progress[d]&&!this.is_invalid(d)&&this.callEvent("onBeforeUpdate",[d,this.getState(d)])&&(a[d]=this._getRowData(d,d+this.post_delim),b=!0,this._in_progress[d]=(new Date).valueOf())}return b?a:null},setVerificator:function(a,
b){this.mandatoryFields[a]=b||function(a){return a!=""}},clearVerificator:function(a){this.mandatoryFields[a]=!1},findRow:function(a){for(var b=0,b=0;b<this.updatedRows.length;b++)if(a==this.updatedRows[b])break;return b},defineAction:function(a,b){if(!this._uActions)this._uActions=[];this._uActions[a]=b},afterUpdateCallback:function(a,b,c,d){var e=a,f=c!="error"&&c!="invalid";f||this.set_invalid(a,c);if(this._uActions&&this._uActions[c]&&!this._uActions[c](d))return delete this._in_progress[e];this._in_progress[e]!=
"wait"&&this.setUpdated(a,!1);var g=a;switch(c){case "inserted":case "insert":b!=a&&(this.obj[this._methods[2]](a,b),a=b);break;case "delete":case "deleted":return this.obj.setUserData(a,this.action_param,"true_deleted"),this.obj[this._methods[3]](a),delete this._in_progress[e],this.callEvent("onAfterUpdate",[a,c,b,d])}this._in_progress[e]!="wait"?(f&&this.obj.setUserData(a,this.action_param,""),delete this._in_progress[e]):(delete this._in_progress[e],this.setUpdated(b,!0,this.obj.getUserData(a,
this.action_param)));this.callEvent("onAfterUpdate",[a,c,b,d])},afterUpdate:function(a,b,c,d,e){e.getXMLTopNode("data");if(e.xmlDoc.responseXML){for(var f=e.doXPath("//data/action"),g=0;g<f.length;g++){var h=f[g],i=h.getAttribute("type"),j=h.getAttribute("sid"),k=h.getAttribute("tid");a.afterUpdateCallback(j,k,i,h)}a.finalizeUpdate()}},finalizeUpdate:function(){this._waitMode&&this._waitMode--;(this.obj.mytype=="tree"||this.obj._h2)&&this.updatedRows.length&&this.sendData();this.callEvent("onAfterUpdateFinish",
[]);this.updatedRows.length||this.callEvent("onFullSync",[])},init:function(a){this.obj=a;this.obj._dp_init&&this.obj._dp_init(this)},setOnAfterUpdate:function(a){this.attachEvent("onAfterUpdate",a)},enableDebug:function(){},setOnBeforeUpdateHandler:function(a){this.attachEvent("onBeforeDataSending",a)},setAutoUpdate:function(a,b){a=a||2E3;this._user=b||(new Date).valueOf();this._need_update=!1;this._loader=null;this._update_busy=!1;this.attachEvent("onAfterUpdate",function(a,b,c,g){this.afterAutoUpdate(a,
b,c,g)});this.attachEvent("onFullSync",function(){this.fullSync()});var c=this;window.setInterval(function(){c.loadUpdate()},a)},afterAutoUpdate:function(a,b){return b=="collision"?(this._need_update=!0,!1):!0},fullSync:function(){if(this._need_update==!0)this._need_update=!1,this.loadUpdate();return!0},getUpdates:function(a,b){if(this._update_busy)return!1;else this._update_busy=!0;this._loader=this._loader||new dtmlXMLLoaderObject(!0);this._loader.async=!0;this._loader.waitCall=b;this._loader.loadXML(a)},
_v:function(a){return a.firstChild?a.firstChild.nodeValue:""},_a:function(a){for(var b=[],c=0;c<a.length;c++)b[c]=this._v(a[c]);return b},loadUpdate:function(){var a=this,b=this.obj.getUserData(0,"version"),c=this.serverProcessor+getUrlSymbol(this.serverProcessor)+["dhx_user="+this._user,"dhx_version="+b].join("&"),c=c.replace("editing=true&","");this.getUpdates(c,function(){var b=a._loader.doXPath("//userdata");a.obj.setUserData(0,"version",a._v(b[0]));var c=a._loader.doXPath("//update");if(c.length){a._silent_mode=
!0;for(var f=0;f<c.length;f++){var g=c[f].getAttribute("status"),h=c[f].getAttribute("id"),i=c[f].getAttribute("parent");switch(g){case "inserted":a.callEvent("insertCallback",[c[f],h,i]);break;case "updated":a.callEvent("updateCallback",[c[f],h,i]);break;case "deleted":a.callEvent("deleteCallback",[c[f],h,i])}}a._silent_mode=!1}a._update_busy=!1;a=null})}};

//v.3.0 build 110704

/*
Copyright Dinamenta, UAB http://www.dhtmlx.com
You allowed to use this component or parts of it under GPL terms
To use it on other terms or get Professional edition of the component please contact us at sales@dhtmlx.com
*/