var base = basePath;
seajs.config({
  base: base + "/sea-modules",
  alias: {
    "$": "jquery/jquery/1.7.2/jquery.js",
    "calendar": "arale/calendar/1.0.0/calendar.js",
    "dialog": "arale/dialog/1.3.1/dialog.js",
    "K": base +　"/static/js/third/K.js",
    "server": base +　"/static/js/common/server.js",
    "dialogUi": base +　"/static/js/common/dialogUi.js",
    "static": base +　"/static/js/common/static.js",
    "timepicker": base + "/static/js/third/timepicker.js"
  }
});


define(function(require, exports, module){
  var $ = require('$'),
      K = require('K'),
      static = require('static'),
      dialogUi = require('dialogUi'),
      calendar = require('calendar'),
      timePicker = require('timepicker'),
      Dialog = require('dialog'), 
      server = require('server');

  var tip = function(text) {
    var dialog = new Dialog({content: '<div class="text-wrap">' + text + '</div>', width: 280});
    dialog.show();
    setTimeout(function(){
      if (dialog.attrs.visible.value === true) dialog.hide();
    }, 5000);
    return dialog;
  }

  var wait = function() {
    return dialogUi.wait();
  }

  var content = function(title, fn) {
    var contentEle = '<div class="create-schema">'  + 
                        '<p class="schema-tip">创建方案名:</p>' +
                        '<input type="text" id="schemaInput" value="'+ title +'" class="ui-input schema-name" />' + 
                        '<a href="javascript:;" id="schemaCreate" class="ui-button btn schema-btn">创建</a>'
                      '</div>';

    var dialog = new Dialog({content: contentEle, width: 250});
    var element = dialog.element;
    // mm = element;
    dialog.show();
    element.find('#schemaCreate').click(function(){
      var name = $.trim(element.find('#schemaInput').val());
      if (!name) {
        tip('不能为空');
      } else {
        fn(name);
      }
    })
    return dialog;
  }

  var luckDraw = function() {
    var contentEle =  '<div class="mask-wrap">' +
                        '<div class="container container2">' +
                          '<div class="cube">' +
                              '<div class="side side1">抽奖中...</div>' +
                              '<div class="side side2"></div>' +
                              '<div class="side side3"></div>' +
                              '<div class="side side4"></div>' +
                              '<div class="side side5"></div>' +
                              '<div class="side side6"></div>' +
                          '</div>' +
                        '</div>' +
                      '</div>';
    var mask = $('<div class="ui-mask" style="position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; z-index: 999; opacity: 0.8; display: block; background-color: rgb(0, 0, 0);"></div>');
    mask.html(contentEle);
    $('body').append(mask);
    return {
      destory: function() {
        mask.remove();
      }
    };
  }

  var activeListView = (function() {
    function instanceTableList(parms) {
      var table = new K.PaginationTable({
        ThList: [
          '活动名称', '活动状态', '分享数', '兴趣数', '添加活动方案', '查看'
        ],
        columnNameList: [
          'title',
          function(data) {
            return static.tStatus[data.t_status];
          },
          'shared_num',
          'loved_num',
          function(data) {
            return  '<a href="javascript:;" id="add" class="mr10">添加</a>';
          },
          function(data) {
            return  '<a href="javascript:;" id="watch" class="mr10">查看</a>';
          }
        ],
        //rowClass: 'abc',
        rowClass: function(index) {
          if (index%2 == 0) {
            return 'odd';
          } else {
            return 'even';
          }
        },
        //source: datas,
        source: function(o, pag, table) {
          dialog = wait();
          parms.page = o.currentPage;
          server.getActs(parms, function(resp){
            dialog.hide();
            if (resp.code == 0) {
              if (resp.body.acts.length) {
                pag({totalPage: Math.ceil(resp.body.total_num/static.actsPerNum)});
              }
              table(resp.body.acts);
            } else {
              tip(resp.msg || '查询商户列表出错');
            }
          })
        },
        perPageNums: static.actsPerNum
      });

      table.setEvents({
        'click #add': 'add',
        'click #watch': 'watch'
      },
      {
        add: function(e, row) {
          var dia = content(row.data.title, function(name){
            var waitlog = wait();
            server.addPrize({name: name, actId: row.data.id}, function(resp){
              resp = {code: 0};
              if (resp.code == 0) {
                dia.hide();
                waitlog.hide();
                prizeListView.render({actId: row.data.id});
              } else {
                tip ('添加抽奖方案失败');
              }
            })
          });
        },
        watch: function(e, row) {
          prizeListView.render({actId: row.data.id});
        }
      })

      table.on('errorSwitch', function(obj){
        //console.log(obj);
        if(obj.type == 'switch'){
          if(obj.page == 1){
            tip('已经是第一页')
          }else{
            tip('已经是最后一页')
          }
        }else if(obj.type == 'submit') {
          if(obj.page === ''){
            tip('不能为空')
          }else{
            tip('页码不正确');
          }
        }
      })

      table.run();
      return table;
    }

    function render(parms, fn) {
      parms.page = 1;
      parms.size = static.actsPerNum;;
      var table = instanceTableList(parms);
      fn(table); 
    }
    return {
      render: render
    }    
  })();

  var prizeListView = (function(){
    function instanceTableList(datas) {
      var table = new K.Table({
        ThList: ['名称', '状态', '操作'],
        columnNameList: [
          'name',
          function(data) {
            return data.status == -1 ? '已删除' : data.status == 0 ? '正常' : data.status == 1 ? '已结束' : '';
          },
          function(data) {
            return '<a href="javascript:;" class="btn btn-green" id="goToPrize">去抽奖</a>';
          }
        ],
        //rowClass: 'abc',
        rowClass: function(index) {
          if (index%2 == 0) {
            return 'odd';
          } else {
            return 'even';
          }
        },
        source: datas
      });

      table.setEvents(
        {
          'click #goToPrize': 'goToPrize'
        },

        {
          goToPrize: function(e, row) {
            prizeView.render(row.data);
          }
        }
      );
      table.run();
      return table;
    }
    function render(data) {
      pageStatus.prizeListStatus();
      var dia = wait();
      server.getPrizes({actId: data.actId, isOver: 0}, function(resp) {
        dia.hide();
        /*var resp = {
          code: 0,
          body: {
            prizes: [{
              create_time: "2014-12-23 03:51:01",
              id: "6",
              name: "六个时间-1",
              status: "0"
            }]
          } 
        }*/
        if (resp.code == 0) {
          var table = instanceTableList(resp.body.prizes);
          $('#prizeListWrap').html(table.El);
        } else {
          tip(resp.msg);
        }
      })
    }
    return {
      render: render
    }
  })();
  var curAwardId;
  var awardList=[];
  var prizeView = (function() {
    var curData  = null, saveAwardFlag = false;
    $('#prizeCon').on('click', '#prizeBtn', function() {
      //var luck = luckDraw();
      //luck.show();
      //server.makeAwardUser
      var checked = $('input[name="awards"]:checked');
      if (checked.length) {
        var name = $('input[name="awards"]:checked').attr('data-name');
        if (curData) {
          var luck = luckDraw();
          server.getAwards({prizeId: curData.id}, function(resp){
            luck.destory();
            if (resp.code == 0) {
              if (resp.body.awards.length) {
                awardList = resp.body.awards;
                $.each(resp.body.awards, function(i, award) {
                  if (award.name == name) {
                    var data = settingsCompontent.getData();
                    console.log(data);
                    data.awardId = award.id;
                    curAwardId = award.id;
                    server.makeAwardUser(data, function(resp){
                     /* var resp = {
                        code: 0,
                        body: {
                          user: {
                            id: 1,
                            nick_name: 'test',
                            sex: 1,
                            birth: '1997-01-01',
                            address: 'gansu',
                            email: '694413162@qq.com',
                            real_name: '刘永昌',
                            contact_qq: '1236547',
                            contact_phone: '15208238092',
                            head_img_url: '/s',
                            status: 1
                          }
                        }
                      };*/
                      if(resp.code == 0) {
                        userSureCompontent.create(resp.body.user);
                      } else {
                        tip(resp.msg || '请重新抽奖');
                      }
                    })
                    return false;
                  }
                })
              }
            } else {
              tip('系统出了点小问题，请重新抽奖');
            }
          })        
        }
      } else {
        tip('先选择一个奖项名称');
      }
    });
    $('#prizeCon').on('click', '#saveAward', function() {
      var name = $.trim($('#awardName').val()),
          saveAwardBtn = $('#saveAward');
      if (name && !saveAwardFlag) {
        saveAwardFlag = true;
        saveAwardBtn.text('保存中...');
        server.addAward({prizeId: curData.id, name: name}, function(resp){
          saveAwardBtn.text('保存奖项');
          saveAwardFlag = false;
          if (resp.code == 0) {
            $('#awardName').val('');
            $('#awardNameList').append('<p><input data-name="'+name+'" name="awards" type="radio"><label for="">'+ name +'</label></p>');
          } else {
            tip(resp.msg || '保存奖项失败');
          }
        })
      } else if(!name) {
        tip('输入不能为空');
      }
    });
    $('#prizeCon').on('click', '#setting', function() {
      settingsCompontent.show();
    });
    function render(data) {
      curData = data;
      pageStatus.prizeStatus();
      var htm = '<div class="">' +
                  '<div class="clearfix">' + 
                    '<a href="javascript:;" id="setting" class="btn prize-setting">设置</a>' +
                  '</div>' +
                  '<p class="prize-title">抽奖方案：' + data.name + '</p>' + 
                  '<p class="award-has-tip">已设置奖项：</p>' +
                  '<div class="award-name-list" id="awardNameList"></div>' +
                  '<div class="prize-award">' + 
                      '<input type="text" id="awardName" class="prize-name-input"><a id="saveAward" class="btn" href="javascript:;">保存奖项</a>' +
                      '<a href="javascript:;" id="prizeBtn" class="btn btn-green prize-btn">去抽奖</a>' +
                  '</div>' + 
                '</div>';
      $('#prizeCon').html(htm);
      server.getAwards({prizeId: data.id}, function(resp){
        if (resp.code == 0) {
          if (resp.body.awards.length) {
            awardList = resp.body.awards;
            prizeUserCompontent.init(resp.body.awards);
            $.each(resp.body.awards, function(i, award){ 
              $('#awardNameList').append('<p><input data-name="'+award.name+'" name="awards" type="radio"><label for="">'+ award.name +'</label></p>');
            })
          }
          //$('')
        } else {
          tip(resp.msg || '获取奖项列表失败');
        }
      });
      settingsCompontent.initData();
    }
    return {
      render: render
    }
  })();

  var settingsCompontent = (function() {
    $('#savaSet').click(function(){
      result.hide();
    });
    $('#cancelSet').click(function(){
      result.initData();
      result.hide();
    });  
    var result = {
      getData: function() {
        var needUserInfo = $('#needUserInfor:checked').length ? 1 : 0,
            includeWinners = $('#includeWinners:checked').length ? 1 : 0,
            startTime, endTime;
            //startTime = $('#beginTimeYear').val() + ' ' + ($('#beginTimeHour').val() ? $('#beginTimeHour').val() :'00:00:00'),
           // endTime = $('#endTimeYear').val() + ' ' + ($('#endTimeHour').val() ? $('#endTimeHour').val() : '23:59:59');
        var result = {};
        result.needUserInfo = needUserInfo;
        result.includeWinners = includeWinners;
        if ($('#beginTimeYear').val()) {
          result.startTime = $('#beginTimeYear').val() + ' ' + ($('#beginTimeHour').val() ? $('#beginTimeHour').val() :'00:00:00');
        }
        if ($('#endTimeYear').val()) {
          result.endTime = $('#endTimeYear').val() + ' ' + ($('#endTimeHour').val() ? $('#endTimeHour').val() : '23:59:59');
        }
        return result;
      },
      initData: function() {
        $('#needUserInfor').attr('checked', false);
        $('#includeWinners').attr('checked', false);
        $('#beginTimeYear').val('');
        $('#beginTimeHour').val('');
        $('#endTimeYear').val('');
        $('#endTimeHour').val(''); 
      },
      show: function() {
        $('.setting-wrap').css('top', 0);
      },
      hide: function() {
        $('.setting-wrap').css('top', -280);
      },
      init: function() {
        new calendar({
            trigger: '#beginTimeYear'
        });
        new calendar({
            trigger: '#endTimeYear'
        });
        console.log($('#beginTimeHour').timePicker);
        $('#beginTimeHour').timePicker();
        $('#endTimeHour').timePicker();
      }
    }  

    return result;

  })()

  function createUser(data) {
    var eleString = 
      '<div class="ui-creator">' +
        '<p class="creator-name">' + data.real_name + '</p>' +
        '<div class="detail-container">' + 
          '<div class="creator-img">' +
            '<img src=' + data.head_img_url + ' alt="">' +
          '</div>' +
          '<div class="creator-info">' + 
            '<p class="creator-info-name">' +
              '<span>昵称: ' + data.nick_name + '</span> <i class="icon vip-icon"></i>' +
            '</p>' +
            '<p class="creator-info-address">' +
              '性别：' + (data.sex == 1 ? '男' : '女') +
            '</p>' +
            '<p class="creator-info-step">' +
              '<span>联系电话：'+ data.contact_phone +'</span>' +
            '</p>' +
          '</div>' +
        '</div>' +
      '</div>';
    return eleString;
  }

  var userSureCompontent = (function() {
    var parent = $('.userSure-container'),
        contain = parent.find('#userSureWrap'),
        curData = null;
    parent.on('click', '#userSureSave', function(){
      var parms = {};
      curData && (parms.userId = curData.id);
      parms.awardId = curAwardId;
      console.log(parms);
      server.saveAwardUser(parms, function(resp){
        /*var resp = {
          code: 0
        };*/
        if (resp.code == 0) {
          prizeUserCompontent.add(curAwardId, curData);
          result.hide();
          contain.html('');
        } else {
          tip(resp.msg || '')
        }
      })
    });
    parent.on('click', '#userSureCancel', function(){
      result.hide();
      contain.html('');
    });    
    var result = {
      create: function(data) {
        curData = data;
        contain.html(createUser(data));
        this.show();
      },
      show: function() {
        parent.css({top: 0});
      },
      hide: function() {
        parent.css({top: -280});
      }      
    }

    return result;

  })()

  var prizeUserCompontent = (function(){
    var list = [];
    var PrizeUser = function(model) {
      this.initialize(model);
    }
    PrizeUser.prototype = {
      constructor: PrizeUser,
      initialize: function(model) {
        this.model = model;
        this.El = $('<div class="prizeUser-wrap"><p class="prizeUser-name">'+ model.name +'</p><div class="prizeUser-users" id="prizeUsers"></div></div>')
        this.getUsers();
      },
      getUsers: function() {
        var awardId = this.model.id;
        var _this = this;
        server.getAwardUsers({awardId: awardId}, function(resp){
          /*var resp = {
            code: 0,
            body: {
              users: [{
                      id: 1,
                      nick_name: 'test',
                      sex: 1,
                      birth: '1997-01-01',
                      address: 'gansu',
                      email: '694413162@qq.com',
                      real_name: '刘永昌',
                      contact_qq: '1236547',
                      contact_phone: '15208238092',
                      head_img_url: '/assets/cfe7fc55/static/images/a.jpg',
                      status: 1
                    }]
            }
          }*/
          if (resp.code == 0) {
            if (resp.body.users) {
              _this.El.find('#prizeUsers').html('');
              $.each(resp.body.users, function(i, user){
                _this.createContent(user);
              })
            }
          } 
        })
      },
      createContent: function(user) {
        this.El.find('#prizeUsers').append(createUser(user));
      }
    }
    function insert(award) {
      var prizeUser = new PrizeUser(award);
      list.push(prizeUser);
      $('#prizeUserCons').append(prizeUser.El);
    }
    return {
      init: function(awards) {
        for (var i=0; i<awards.length; i++) {
          insert(awards[i]);
        }
      }, 
      add: function(awardId, data) {
        var flag = false;
        for (var i=0;i<list.length;i++) {
          if (list[i].model.id == awardId) {
            flag = true;
            list[i].createContent(data);
          }
        }
        if (!flag) {
          $.each(awardList, function(i, award){
            if (award.id == awardId) {
              insert(award);
            }
          })
        }
      }
    }

  })()
//tip('5534');
  var pageStatus = {
    mainEl: $('#mainCon'),
    prizeListEl: $('#prizeListCon'),
    prizeEl: $('#prizeCon'),
    prizeUserConEl: $('#prizeUserCon'),
    tableStatus: function() {
      this.mainEl.show();
      this.prizeListEl.hide();
      this.prizeEl.hide();
      this.prizeUserConEl.hide();
    },
    prizeListStatus: function() {
      this.mainEl.hide();
      this.prizeListEl.show();
      this.prizeEl.hide();
      this.prizeUserConEl.hide();
    },
    prizeStatus: function() {
      this.mainEl.hide();
      this.prizeListEl.hide();
      this.prizeEl.show();
      this.prizeUserConEl.show();
    }
  }

  var page = {
    init: function() {  
      this.setEvents();
      $('#searchBtn').trigger('click');
      //prizeListView.render({actId: 65});
      settingsCompontent.init();
    },
    setEvents: function() {
      $('#searchBtn').click(function() {
        var keyWords = $('#keyWords').val(),
            actStatus = $('#activeStatus').val();
        var parms = {};
        if (actStatus!='all') parms.actStatus = actStatus;
        activeListView.render(parms, function(table){
          $('#tableCon').html(table.El);
        });   
      })
    }
  }

  page.init();

})