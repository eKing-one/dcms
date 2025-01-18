/**
 * 这个函数将表情符号插入到文本框中的光标位置。
 * @param {string} text - 要插入的表情符号文本。
 */
function emoticon(text) {
	var txtarea = document.message.msg;  // 获取文本区域
	text = ' ' + text + ' ';  // 在表情符号前后加上空格
	if (txtarea.createTextRange && txtarea.caretPos) {
		// 如果浏览器支持 TextRange（IE浏览器）
		var caretPos = txtarea.caretPos;
		// 如果光标位置的最后一个字符是空格，则添加表情符号和空格，否则直接添加表情符号
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();  // 聚焦到文本框
	} else {
		// 对于不支持 TextRange 的浏览器（如现代浏览器）
		txtarea.value  += text;  // 将表情符号直接添加到文本框内容
		txtarea.focus();  // 聚焦到文本框
	}
}

/**
 * 这个函数用于保存文本框的光标位置，方便以后恢复。
 * @param {HTMLElement} textEl - 要保存光标位置的文本元素。
 */
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();  // 对IE浏览器，保存当前光标位置
}

/**
 * 这个函数用于切换表情符号面板的显示和隐藏。
 * @param {string} vis - 显示或隐藏表情符号面板，'show'表示显示，其他值表示隐藏。
 */
function smiles(vis) {
	if (vis == 'show') {
		// 显示表情符号面板，并隐藏其他面板
		document.getElementById("smiles_0").style.display = "none";
		document.getElementById("smiles_1").style.display = "block";
	} else {
		// 隐藏表情符号面板，并显示原始面板
		document.getElementById("smiles_1").style.display = "none";
		document.getElementById("smiles_0").style.display = "block";
	}
}
