var base = basePath;
seajs.config({
  base: base + "/sea-modules",
  //debug: 2,
  alias: {
    "$": "jquery/jquery/1.7.2/jquery.js",
    //"$": base +　"/static/js/third/jquery.js",
    "upload": "arale/upload/1.1.2/upload.js",
    "calendar": "arale/calendar/1.0.0/calendar.js",
    "dialog": "arale/dialog/1.3.1/dialog.js",
    //"confirmbox": "/arale/dialog/1.3.1/confirmbox.js",
    //"autocomplete": "/arale/autocomplete/1.3.2/autocomplete.js",
    "K": base +　"/static/js/third/K.js",
    "activeEdit": base +　"/static/js/main/activeEdit.js",
    "server": base +　"/static/js/common/server.js",
    "static": base +　"/static/js/common/static.js",
    //"address": base +　"/static/js/main/address.js",
    "dialogUi": base +　"/static/js/common/dialogUi.js",
    "main": base +　"/static/js/main/main.js",
    "timepicker": base + "/static/js/third/timepicker.js"
  }
});