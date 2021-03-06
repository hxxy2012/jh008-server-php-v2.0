var nicEditorConfig = {
	buttons : {
		'save' : {name : '保存', type : 'nicEditorSaveButton', tile : 1},
		'undo' : {name : '撤销', command : 'undo', noActive : true, tile : 23},
		'redo' : {name : '重做', command : 'redo', noActive : true, tile : 24},
		'bold' : {name : '加黑', command : 'Bold', tags : ['B','STRONG'], css : {'font-weight' : 'bold'}, tile : 2},
		'italic' : {name : '倾斜', command : 'Italic', tags : ['EM','I'], css : {'font-style' : 'italic'}, tile : 3},
		'underline' : {name : '下划线', command : 'Underline', tags : ['U'], css : {'text-decoration' : 'underline'}, tile : 4},
		'left' : {name : '左对齐', command : 'justifyleft', noActive : true, tile : 8},
		'center' : {name : '中间对齐', command : 'justifycenter', noActive : true, tile : 9},
		'right' : {name : '右对齐', command : 'justifyright', noActive : true, tile : 10},
		/*'ol' : {name : '有序列表', command : 'insertorderedlist', tags : ['OL'], tile : 12},
		'ul' : 	{name : '无序列表', command : 'insertunorderedlist', tags : ['UL'], tile : 13},*/
		'fontSize' : {name : '字体大小', type : 'nicEditorFontSizeSelect', command : 'fontsize'},
		'fontFamily' : {name : '字体样式', type : 'nicEditorFontFamilySelect', command : 'fontname'},
		'fontFormat' : {name : '字体格式', type : 'nicEditorFontFormatSelect', command : 'formatBlock'}
		/*'subscript' : {name : '下标', command : 'subscript', tags : ['SUB'], tile : 6, disabled : true},
		'superscript' : {name : '上标', command : 'superscript', tags : ['SUP'], tile : 5, disabled : true},
		'strikeThrough' : {name : '删除线', command : 'strikeThrough', css : {'text-decoration' : 'line-through'}, tile : 7, disabled : true},
		'indent' : {name : '减少缩进量', command : 'indent', noActive : true, tile : 20},
		'unindent' : {name : '增加缩进量', command : 'outdent', noActive : true, tile : 21},
		'hr' : {name : '水平线', command : 'insertHorizontalRule', noActive : true, tile : 22},
		'color' : {name : '更改颜色', type : 'nicEditorColorButton', tile : 25},
		'image' : {name : '添加图片', type : 'nicEditorImageButton', tile : 14},
		'html' : {name : '编辑 HTML', type : 'nicEditorHTMLButton', tile : 16},
		'link' : {name : '链接', type : 'nicEditorLinkButton', tile : 17}*/
	},
	iconsPath : iconsPath,
	fullPanel : false,
	onSubmit : null,
	buttonList : ['bold','italic','underline','left','center','right','ol','ul','indent','unindent','fontSize','fontFamily','image','link', 'superscript', 'subscript', 'strikeThrough'],
	toolTipOn : false,
	toolTipText : '点击编辑'
};
