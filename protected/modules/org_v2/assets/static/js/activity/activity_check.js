$(document).ready(function()
{
//分页
laypage({
    cont: $('#page2'), //容器。值支持id名、原生dom对象，jquery对象,
    pages: 11, //总页数
    skin: '#FF9934', //皮肤
    groups: 5,//连续显示分页数
    first: '首页', //若不显示，设置false即可
    last: '尾页', //若不显示，设置false即可
    prev: '<', //若不显示，设置false即可
    next: '>' //若不显示，设置false即可
});
//分页
laypage({
    cont: $('#page1'), //容器。值支持id名、原生dom对象，jquery对象,
    pages: 11, //总页数
    skin: '#FF9934', //皮肤
    groups: 5,//连续显示分页数
    first: '首页', //若不显示，设置false即可
    last: '尾页', //若不显示，设置false即可
    prev: '<', //若不显示，设置false即可
    next: '>' //若不显示，设置false即可
});


//切换
$('.wsh').click(function()
{
    $('.wsh_lr').css({'display':'block'});
    $('.ybm_lr').css({'display':'none'});
})
$('.ybm').click(function()
{
    $('.wsh_lr').css({'display':'none'});
    $('.ybm_lr').css({'display':'block'});  
})

    
})
