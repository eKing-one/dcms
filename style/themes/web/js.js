function emoticon(text) {
	var txtarea = document.message.msg;
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value  += text;
		txtarea.focus();
	}
}

function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

function smiles(vis) {
if (vis == 'show')
{
document.getElementById("smiles_0").style.display = "none";
document.getElementById("smiles_1").style.display = "block";
}
else
{
document.getElementById("smiles_1").style.display = "none";
document.getElementById("smiles_0").style.display = "block";

}

}