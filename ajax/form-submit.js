/************************************************************************************************************
Ajax form submit
Copyright (C) 2007  DTHMLGoodies.com, Alf Magne Kalleland

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
written by Alf Magne Kalleland.

Alf Magne Kalleland, 2007
Owner of DHTMLgoodies.com


************************************************************************************************************/	

var DHTMLSuite = new Object();

DHTMLSuite.formUtil = function()
{
	
	
	
}

DHTMLSuite.getEl = function(elRef){
	if(typeof elRef=='string'){
		if(document.getElementById(elRef))return document.getElementById(elRef);
		if(document.forms[elRef])return document.forms[elRef];
		if(document[elRef])return document[elRef];
		if(window[elRef])return window[elRef];
	}
	return elRef;	// Return original ref.
	
}
	
DHTMLSuite.formUtil.prototype = 
{
	// {{{ getFamily
    /**
     *	Return an array of elements with the same name
     *	@param Object el - Reference to form element
     *	@param Object formRef - Reference to form
     *
     * @public
     */		
	getFamily : function(el,formRef)
	{
		var els = formRef.elements;
		var retArray = new Array();
		for(var no=0;no<els.length;no++){
			if(els[no].name == el.name)retArray[retArray.length] = els[no];
		}
		return retArray;		
	}
	// }}}
	
	,
	// {{{ hasFileInputs()
    /**
     *	Does the form has file inputs?
     *	@param Object formRef - Reference to form
     *
     * @public
     */		
	hasFileInputs : function(formRef)
	{
		var els = formRef.elements;
		for(var no=0;no<els.length;no++){
			if(els[no].tagName.toLowerCase()=='input' && els[no].type.toLowerCase()=='file')return true;	
		}
		return false;
	}
	// }}}
	,	
	// {{{ getValuesAsArray()
    /**
     *	Return value of form as associative array
     *	@param Object formRef - Reference to form
     *
     * @public
     */		
	getValuesAsArray : function(formRef)
	{
		var retArray = new Object();
		formRef = DHTMLSuite.getEl(formRef);
		var els = formRef.elements;
		for(var no=0;no<els.length;no++){
			if(els[no].disabled)continue;
			var tag = els[no].tagName.toLowerCase();
			switch(tag){
				case "input": 
					var type = els[no].type.toLowerCase();
					if(!type)type='text';
					switch(type){
						case "text":
						case "image":
						case "hidden":
						case "password":
							retArray[els[no].name] = els[no].value;
							break;
						case "checkbox":
							var boxes = this.getFamily(els[no],formRef);
							if(boxes.length>1){
								retArray[els[no].name] = new Array();
								for(var no2=0;no2<boxes.length;no2++){
									if(boxes[no2].checked){
										var index = retArray[els[no].name].length;
										retArray[els[no].name][index] = boxes[no2].value;
									}
								}								
							}else{
								if(els[no].checked)retArray[els[no].name] = els[no].value;
							}
							break;	
						case "radio":
							if(els[no].checked)retArray[els[no].name] = els[no].value;
							break;		
						
					}	
					break;	
				case "select":
					var string = '';			
					var mult = els[no].getAttribute('multiple');
					if(mult || mult===''){
						retArray[els[no].name] = new Array();
						for(var no2=0;no2<els[no].options.length;no2++){
							var index = retArray[els[no].name].length;
							if(els[no].options[no2].selected)retArray[els[no].name][index] = els[no].options[no2].value;	
						}
					}else{
						retArray[els[no].name] = els[no].options[els[no].selectedIndex].value;
					}
					break;	
				case "textarea":
					retArray[els[no].name] = els[no].value;
					break;					
			}			
		}
		return retArray;		
	}
	// }}}
	,	
	// {{{ getValue()
    /**
     *	Return value of form element
     *	@param Object formEl - Reference to form element
     *
     * @public
     */
	getValue : function(formEl)
	{
		switch(formEl.tagName.toLowerCase()){
			case "input":
			case "textarea": return formEl.value;
			case "select": return formEl.options[formEl.selectedIndex].value;			
		}
		
	}
	// }}}
	,	
	// {{{ areEqual()
    /**
     *	Check if two form elements have the same value
     *	@param Object input1 - Reference to form element
     *	@param Object input2 - Reference to form element
     *
     * @public
     */	
	areEqual : function(input1,input2)
	{
		input1 = DHTMLSuite.getEl(input1);
		input2 = DHTMLSuite.getEl(input2);	
		if(this.getValue(input1)==this.getValue(input2))return true;
		return false;		
	}	
}
	
/************************************************************************************************************
*	Form submission class
*
*	Created:						March, 6th, 2007
*	@class Purpose of class:		Ajax form submission class
*			
*	Css files used by this script:	form.css
*
*	Demos of this class:			demo-form-validator.html
*
* 	Update log:
*
************************************************************************************************************/


/**
* @constructor
* @class Form submission
* Demo: <a href="../../demos/demo-form-validator.html" target="_blank">demo-form-validator.html</a>		
*
* @param Associative array of properties, possible keys: <br>
*	formRef - Reference to form<br>
*	method - How to send the form, "GET" or "POST", default is "POST"
*	reponseEl - Where to display response from ajax
*	action - Where to send form data
*	responseFile - Alternative response file. This will be loaded dynamically once the script receives response from the file specified in "action".
*		
* @version				1.0
* @version 1.0
* @author	Alf Magne Kalleland(www.dhtmlgoodies.com)
**/

DHTMLSuite.variableStorage = new Object();
DHTMLSuite.variableStorage.arrayDSObjects = new Array();

DHTMLSuite.form = function(propArray)
{
	var formRef;
	var method;
	var responseEl;
	var action;
	var responseFile;
	
	var formUtil;
	var objectIndex;
	var sackObj;
	var coverDiv;
	var layoutCSS;
	var iframeName;
	
	this.method = 'POST';
	this.sackObj = new Array();
	this.formUtil = new DHTMLSuite.formUtil();
	this.layoutCSS = 'form.css';
	
	
		
	this.objectIndex = DHTMLSuite.variableStorage.arrayDSObjects.length;
	DHTMLSuite.variableStorage.arrayDSObjects[this.objectIndex] = this;	
		
	
	if(propArray)this.__setInitProperties(propArray);
	
}
DHTMLSuite.form.prototype = 
{
	// {{{ submit()
    /**
     *	Submits the form
     *
     * @public
     */		
	submit : function()
	{
		this.__createCoverDiv();
		var index = this.sackObj.length;
		if(this.formUtil.hasFileInputs(this.formRef)){
			this.__createIframe();
			this.formRef.submit();
			
		}else{
			this.__createSackObject(index);			
			this.__populateSack(index);			
			this.sackObj[index].runAJAX();

		}
		this.__positionCoverDiv();
		return false;
	}
	// }}}
	,
	__createIframe : function()
	{
		if(this.iframeName)return;
		var ind = this.objectIndex;
		var div = document.createElement('DIV');
		document.body.appendChild(div);
		this.iframeName = 'DHTMLSuiteForm' + this.getUniqueId();
		div.innerHTML = '<iframe style="visibility:hidden;width:5px;height:5px" id="' + this.iframeName + '" name="' + this.iframeName + '" onload="parent.DHTMLSuite.variableStorage.arrayDSObjects[' + ind + '].__getIframeResponse()"></iframe>'; 
		this.formRef.method = this.method;
		this.formRef.action = this.action;
		this.formRef.target = this.iframeName;	
		if(!this.formRef.enctype)this.formRef.enctype = 'multipart/form-data';
			
	}
	,
	// {{{ getUniqueId()
    /**
     *
     *  Returns a unique numeric id
     *
     *
     * 
     * @public
     */		
	getUniqueId : function()
	{
		var no = Math.random() + '';
		no = no.replace('.','');		
		var no2 = Math.random() + '';
		no2 = no2.replace('.','');		
		return no + no2;		
	}	
	// }}}
	,
	// {{{ __getIframeResponse()
    /**
     *	Form has been submitted to iframe - move content from iframe
     *
     * @private
     */	
	__getIframeResponse : function()
	{
		if(this.responseEl){		
			if(this.responseFile){
				if(!this.responseEl.id)this.responseEl.id = 'DHTMLSuite_formResponse' + DHTMLSuite.getUniqueId();
				var dynContent = new DHTMLSuite.dynamicContent();
				dynContent.loadContent(this.responseEl.id,this.responseFile);				
			}else{			
				this.responseEl.innerHTML = self.frames[this.iframeName].document.body.innerHTML;	
				this.__evaluateJs(this.responseEl);
				this.__evaluateCss(this.responseEl);	
			}						
		}	
		this.coverDiv.style.display='none';
		this.__handleCallback('onComplete');
	}
	// }}}
	,
	// {{{ __positionCoverDiv()
    /**
     *	Position cover div
     *
     * @private
     */	
	__positionCoverDiv : function()
	{
		if(!this.responseEl)return;
		try{
			var st = this.coverDiv.style;
			st.left = this.getLeftPos(this.responseEl) + 'px';	
			st.top = this.getTopPos(this.responseEl) + 'px';	
			st.width = this.responseEl.offsetWidth + 'px';	
			st.height = this.responseEl.offsetHeight + 'px';	
			st.display='block';
		}catch(e){
		}
	}
	// }}}
	,
	// {{{ __createCoverDiv()
    /**
     *	Submits the form
     *
     * @private
     */		
	__createCoverDiv : function()
	{	
		if(this.coverDiv)return;
		this.coverDiv = document.createElement('DIV');
		var el = this.coverDiv;
		el.style.overflow='hidden';
		el.style.zIndex = 1000;
		el.style.position = 'absolute';

		document.body.appendChild(el);
		
		var innerDiv = document.createElement('DIV');
		innerDiv.style.width='105%';
		innerDiv.style.height='105%';
		innerDiv.className = 'DHTMLSuite_formCoverDiv';
		innerDiv.style.opacity = '0.2';
		innerDiv.style.filter = 'alpha(opacity=20)';		
		el.appendChild(innerDiv);
		
		var ajaxLoad = document.createElement('DIV');
		ajaxLoad.className = 'DHTMLSuite_formCoverDiv_ajaxLoader';
		el.appendChild(ajaxLoad);		
	}
	// }}}
	,
	// {{{ __createSackObject()
    /**
     *	Create new sack object
     *
     * @private
     */		
	__createSackObject : function(ajaxIndex)
	{		
		var ind = this.objectIndex;
		this.sackObj[ajaxIndex] = new sack();
		this.sackObj[ajaxIndex].requestFile = this.action;	
		this.sackObj[ajaxIndex].method = this.method;		
		this.sackObj[ajaxIndex].onCompletion = function(){ DHTMLSuite.variableStorage.arrayDSObjects[ind].__getResponse(ajaxIndex); }
	}
	// }}}
	,
	// {{{ __getResponse()
    /**
     *	Get response from ajax
     *
     * @private
     */	
	__getResponse : function(ajaxIndex)
	{

		if(this.responseEl){			
			if(this.responseFile){
				if(!this.responseEl.id)this.responseEl.id = 'DHTMLSuite_formResponse' + DHTMLSuite.getUniqueId();
				var dynContent = new DHTMLSuite.dynamicContent();
				dynContent.loadContent(this.responseEl.id,this.responseFile);				
			}else{			
				this.responseEl.innerHTML = this.sackObj[ajaxIndex].response;
				this.__evaluateJs(this.responseEl);
				this.__evaluateCss(this.responseEl);	
			}				
		}	
		
		this.coverDiv.style.display='none';
		this.sackObj[ajaxIndex] = null;
		this.__handleCallback('onComplete');
	}
	,
	// {{{ isArray()
    /**
     * Return true if element is an array
     *
     * @param Object el = Reference to HTML element
     * @public
     */		
	isArray : function(el)
	{
		if(el.constructor.toString().indexOf("Array") != -1)return true;
		return false;
	}	
	// }}}
	,
	// {{{ __populateSack()
    /**
     *	Populate sack object with form data
     *	@param ajaxIndex - index of current sack object
     *
     * @private
     */	
	__populateSack : function(ajaxIndex)
	{
		var els = this.formUtil.getValuesAsArray(this.formRef);		
		for(var prop in els){
			if(this.isArray(els[prop])){
				for(var no=0;no<els[prop].length;no++){
					var name = prop + '[' + no + ']';
					if(prop.indexOf('[')>=0){ // The name of the form field is already indicating an array
						name = prop.replace('[','[' + no);	
					}
					this.sackObj[ajaxIndex].setVar(name,els[prop][no]);	
				}
			}else{
				this.sackObj[ajaxIndex].setVar(prop,els[prop]);			
			}			
		}		
	}
	// }}}
	,
	// {{{ __setInitProperties()
    /**
     *	Fill object with data sent to the constructor
     *	@param Array props - Associative Array("Object") of properties
     *
     * @private
     */		
	__setInitProperties : function(props)
	{
		if(props.formRef)this.formRef = DHTMLSuite.getEl(props.formRef);
		if(props.method)this.method = props.method;
		if(props.responseEl)this.responseEl = DHTMLSuite.getEl(props.responseEl);
		if(props.action)this.action = props.action;
		if(props.responseFile)this.responseFile = props.responseFile;
		if(props.callbackOnComplete)this.callbackOnComplete = props.callbackOnComplete;
		if(!this.action)this.action = this.formRef.action;
		if(!this.method)this.method = this.formRef.method;
	}	
	// }}}
	,
	// {{{ __handleCallback()
    /**
     *	Execute callback
     *	@param String action - Which callback action
     *
     * @private
     */	
	__handleCallback : function(action)
	{
		var callbackString = '';
		switch(action){
			case "onComplete":
				callbackString = this.callbackOnComplete;
				break;	
			
			
		}	
		if(callbackString){
			if(callbackString.indexOf('(')==-1)callbackString = callbackString + '("' + this.formRef.name + '")';
			eval(callbackString);
		}
		
	}
	,	
	// {{{ __evaluateJs()
    /**
     * Evaluate Javascript in the inserted content
     *
     * @private
     */	
	__evaluateJs : function(obj)
	{
		obj = DHTMLSuite.getEl(obj);
		
		var scriptTags = obj.getElementsByTagName('SCRIPT');
		var string = '';
		var jsCode = '';
		for(var no=0;no<scriptTags.length;no++){	
			if(scriptTags[no].src){
		        var head = document.getElementsByTagName("head")[0];
		        var scriptObj = document.createElement("script");
		
		        scriptObj.setAttribute("type", "text/javascript");
		        scriptObj.setAttribute("src", scriptTags[no].src);  	
			}else{
				if(DHTMLSuite.clientInfoObj.isOpera){
					jsCode = jsCode + scriptTags[no].text + '\n';
				}
				else
					jsCode = jsCode + scriptTags[no].innerHTML;	
			}			
		}
		if(jsCode)this.__installScript(jsCode);
	}
	// }}}
	,
	// {{{ __installScript()
    /**
     *  "Installs" the content of a <script> tag.
     *
     * @private        
     */		
	__installScript : function ( script )
	{		
		try{
		    if (!script)
		        return;		
	        if (window.execScript){        	
	        	window.execScript(script)
	        }else if(window.jQuery && jQuery.browser.safari){ // safari detection in jQuery
	            window.setTimeout(script,0);
	        }else{        	
	            window.setTimeout( script, 0 );
	        } 
		}catch(e){
			
		}
	}	
	// }}}
	,
	// {{{ __evaluateCss()
    /**
     *  Evaluates css
     *
     * @private        
     */	
	__evaluateCss : function(obj)
	{
		obj = DHTMLSuite.getEl(obj);
		var cssTags = obj.getElementsByTagName('STYLE');
		var head = document.getElementsByTagName('HEAD')[0];
		for(var no=0;no<cssTags.length;no++){
			head.appendChild(cssTags[no]);
		}	
	}	
	// }}}
	,
	// {{{ getLeftPos()
    /**
     * This method will return the left coordinate(pixel) of an HTML element
     *
     * @param Object el = Reference to HTML element
     * @public
     */	
	getLeftPos : function(el)
	{	 
		/*
		if(el.getBoundingClientRect){ // IE
			var box = el.getBoundingClientRect();	
			return (box.left/1 + Math.max(document.body.scrollLeft,document.documentElement.scrollLeft));
		}
		*/
		if(document.getBoxObjectFor){
			if(el.tagName!='INPUT' && el.tagName!='SELECT' && el.tagName!='TEXTAREA')return document.getBoxObjectFor(el).x
		}		 
		var returnValue = el.offsetLeft;
		while((el = el.offsetParent) != null){
			if(el.tagName!='HTML'){
				returnValue += el.offsetLeft;
				if(document.all)returnValue+=el.clientLeft;
			}
		}
		return returnValue;
	}
	// }}}
	,
	// {{{ getTopPos()
    /**
     * This method will return the top coordinate(pixel) of an HTML element/tag
     *
     * @param Object el = Reference to HTML element
     * @public
     */	
	getTopPos : function(el)
	{	
		/*
		if(el.getBoundingClientRect){	// IE
			var box = el.getBoundingClientRect();	
			return (box.top/1 + Math.max(document.body.scrollTop,document.documentElement.scrollTop));
		}
		*/	
		if(document.getBoxObjectFor){
			if(el.tagName!='INPUT' && el.tagName!='SELECT' && el.tagName!='TEXTAREA')return document.getBoxObjectFor(el).y
		}
		
		var returnValue = el.offsetTop;
		while((el = el.offsetParent) != null){
			if(el.tagName!='HTML'){
				returnValue += (el.offsetTop - el.scrollTop);
				if(document.all)returnValue+=el.clientTop;
			}
		} 
		return returnValue;
	}	
	
}