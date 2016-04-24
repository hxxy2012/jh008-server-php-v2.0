var base = basePath;
seajs.config({
  base: base + "/sea-modules",
  //debug: 2,
  alias: {
    "$": "jquery/jquery/1.7.2/jquery.js",
    "upload": "arale/upload/1.1.2/upload.js",
    "calendar": "arale/calendar/1.0.0/calendar.js",
    "dialog": "arale/dialog/1.3.1/dialog.js",
    "K": base +　"/static/js/third/K.js",
    "activeEdit": base +　"/static/js/main/activeEdit.js",
    "server": base +　"/static/js/common/server.js",
    "static": base +　"/static/js/common/static.js",
    "dialogUi": base +　"/static/js/common/dialogUi.js",
    "main": base +　"/static/js/main/main.js",
    "timepicker": base + "/static/js/third/timepicker.js",
    "common": base + '/static/js/common/common.js',
    'remark': base + '/static/js/main/remark.js',
    "informationEdit": base + '/static/js/main/informationEdit.js'
  }
});