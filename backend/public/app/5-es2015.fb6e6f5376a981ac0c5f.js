(window.webpackJsonp=window.webpackJsonp||[]).push([[5],{tmjD:function(e,t,s){"use strict";s.d(t,"a",function(){return f}),s.d(t,"b",function(){return d});var i=s("fXoL"),a=s("3Pt+"),r=s("ofXK");const n=new i.q("config"),h=new i.q("NEW_CONFIG"),l=new i.q("INITIAL_CONFIG"),c={suffix:"",prefix:"",thousandSeparator:" ",decimalMarker:".",clearIfNotMatch:!1,showTemplate:!1,showMaskTyped:!1,placeHolderCharacter:"_",dropSpecialCharacters:!0,hiddenInput:void 0,shownMaskExpression:"",separatorLimit:"",allowNegativeNumbers:!1,validation:!0,specialCharacters:["-","/","(",")",".",":"," ","+",",","@","[","]",'"',"'"],leadZeroDateTime:!1,patterns:{0:{pattern:new RegExp("\\d")},9:{pattern:new RegExp("\\d"),optional:!0},X:{pattern:new RegExp("\\d"),symbol:"*"},A:{pattern:new RegExp("[a-zA-Z0-9]")},S:{pattern:new RegExp("[a-zA-Z]")},d:{pattern:new RegExp("\\d")},m:{pattern:new RegExp("\\d")},M:{pattern:new RegExp("\\d")},H:{pattern:new RegExp("\\d")},h:{pattern:new RegExp("\\d")},s:{pattern:new RegExp("\\d")}}},o=["Hh:m0:s0","Hh:m0","m0:s0"],u=["percent","Hh","s0","m0","separator","d0/M0/0000","d0/M0","d0","M0"];let p=(()=>{class e{constructor(e){this._config=e,this.maskExpression="",this.actualValue="",this.shownMaskExpression="",this._formatWithSeparators=(e,t,s,i)=>{const a=e.split(s),r=a.length>1?`${s}${a[1]}`:"";let n=a[0];const h=this.separatorLimit.replace(/\s/g,"");h&&+h&&(n="-"===n[0]?"-"+n.slice(1,n.length).slice(0,h.length):n.slice(0,h.length));const l=/(\d+)(\d{3})/;for(;t&&l.test(n);)n=n.replace(l,"$1"+t+"$2");return void 0===i?n+r:0===i?n:n+r.substr(0,i+1)},this.percentage=e=>Number(e)>=0&&Number(e)<=100,this.getPrecision=e=>{const t=e.split(".");return t.length>1?Number(t[t.length-1]):1/0},this.checkAndRemoveSuffix=e=>{var t,s,i;for(let a=(null===(t=this.suffix)||void 0===t?void 0:t.length)-1;a>=0;a--){const t=this.suffix.substr(a,null===(s=this.suffix)||void 0===s?void 0:s.length);if(e.includes(t)&&(a-1<0||!e.includes(this.suffix.substr(a-1,null===(i=this.suffix)||void 0===i?void 0:i.length))))return e.replace(t,"")}return e},this.checkInputPrecision=(e,t,s)=>{if(t<1/0){const i=new RegExp(this._charToRegExpExpression(s)+`\\d{${t}}.*$`),a=e.match(i);a&&a[0].length-1>t&&(e=e.substring(0,e.length-(a[0].length-1-t))),0===t&&e.endsWith(s)&&(e=e.substring(0,e.length-1))}return e},this._shift=new Set,this.clearIfNotMatch=this._config.clearIfNotMatch,this.dropSpecialCharacters=this._config.dropSpecialCharacters,this.maskSpecialCharacters=this._config.specialCharacters,this.maskAvailablePatterns=this._config.patterns,this.prefix=this._config.prefix,this.suffix=this._config.suffix,this.thousandSeparator=this._config.thousandSeparator,this.decimalMarker=this._config.decimalMarker,this.hiddenInput=this._config.hiddenInput,this.showMaskTyped=this._config.showMaskTyped,this.placeHolderCharacter=this._config.placeHolderCharacter,this.validation=this._config.validation,this.separatorLimit=this._config.separatorLimit,this.allowNegativeNumbers=this._config.allowNegativeNumbers,this.leadZeroDateTime=this._config.leadZeroDateTime}applyMaskWithPattern(e,t){const[s,i]=t;return this.customPattern=i,this.applyMask(e,s)}applyMask(e,t,s=0,i=!1,a=!1,r=(()=>{})){if(null==e||void 0===t)return"";let n=0,h="",l=!1,c=!1,o=1,u=!1;e.slice(0,this.prefix.length)===this.prefix&&(e=e.slice(this.prefix.length,e.length)),this.suffix&&(null==e?void 0:e.length)>0&&(e=this.checkAndRemoveSuffix(e));const p=e.toString().split("");"IP"===t&&(this.ipError=!!(p.filter(e=>"."===e).length<3&&p.length<7),t="099.099.099.099");const m=[];for(let _=0;_<e.length;_++)e[_].match("\\d")&&m.push(e[_]);if("CPF_CNPJ"===t&&(this.cpfCnpjError=!(11===m.length||14===m.length),t=m.length>11?"00.000.000/0000-00":"000.000.000-00"),t.startsWith("percent")){if(e.match("[a-z]|[A-Z]")||e.match(/[-!$%^&*()_+|~=`{}\[\]:";'<>?,\/.]/)){e=this._stripToDecimal(e);const s=this.getPrecision(t);e=this.checkInputPrecision(e,s,this.decimalMarker)}if(e.indexOf(".")>0&&!this.percentage(e.substring(0,e.indexOf(".")))){const t=e.substring(0,e.indexOf(".")-1);e=`${t}${e.substring(e.indexOf("."),e.length)}`}h=this.percentage(e)?e:e.substring(0,e.length-1)}else if(t.startsWith("separator")){(e.match("[w\u0430-\u044f\u0410-\u042f]")||e.match("[\u0401\u0451\u0410-\u044f]")||e.match("[a-z]|[A-Z]")||e.match(/[-@#!$%\\^&*()_\xa3\xac'+|~=`{}\[\]:";<>.?\/]/)||e.match("[^A-Za-z0-9,]"))&&(e=this._stripToDecimal(e)),e=e.length>1&&"0"===e[0]&&e[1]!==this.decimalMarker?e.slice(1,e.length):e;const i=this._charToRegExpExpression(this.thousandSeparator),a=this._charToRegExpExpression(this.decimalMarker),r='@#!$%^&*()_+|~=`{}\\[\\]:\\s,\\.";<>?\\/'.replace(i,"").replace(a,""),n=new RegExp("["+r+"]");e.match(n)&&(e=e.substring(0,e.length-1));const l=this.getPrecision(t),u=(e=this.checkInputPrecision(e,l,this.decimalMarker)).replace(new RegExp(i,"g"),"");h=this._formatWithSeparators(u,this.thousandSeparator,this.decimalMarker,l);const p=h.indexOf(",")-e.indexOf(","),m=h.length-e.length;if(m>0&&","!==h[s]){c=!0;let e=0;do{this._shift.add(s+e),e++}while(e<m)}else 0!==p&&s>0&&!(h.indexOf(",")>=s&&s>3)||!(h.indexOf(".")>=s&&s>3)&&m<=0?(this._shift.clear(),c=!0,o=m,this._shift.add(s+=m)):this._shift.clear()}else for(let _=0,S=p[0];_<p.length&&n!==t.length;_++,S=p[_])if(this._checkSymbolMask(S,t[n])&&"?"===t[n+1])h+=S,n+=2;else if("*"===t[n+1]&&l&&this._checkSymbolMask(S,t[n+2]))h+=S,n+=3,l=!1;else if(this._checkSymbolMask(S,t[n])&&"*"===t[n+1])h+=S,l=!0;else if("?"===t[n+1]&&this._checkSymbolMask(S,t[n+2]))h+=S,n+=3;else if(this._checkSymbolMask(S,t[n])){if("H"===t[n]&&Number(S)>2){n+=1,this._shiftStep(t,n,p.length),_--,this.leadZeroDateTime&&(h+="0");continue}if("h"===t[n]&&"2"===h&&Number(S)>3){n+=1,_--;continue}if("m"===t[n]&&Number(S)>5){n+=1,this._shiftStep(t,n,p.length),_--,this.leadZeroDateTime&&(h+="0");continue}if("s"===t[n]&&Number(S)>5){n+=1,this._shiftStep(t,n,p.length),_--,this.leadZeroDateTime&&(h+="0");continue}const s=31;if("d"===t[n]&&(Number(S)>3&&this.leadZeroDateTime||Number(e.slice(n,n+2))>s||"/"===e[n+1])){n+=1,this._shiftStep(t,n,p.length),_--,this.leadZeroDateTime&&(h+="0");continue}if("M"===t[n]){const i=12,a=0===n&&(Number(S)>2||Number(e.slice(n,n+2))>i||"/"===e[n+1]),r=e.slice(n-3,n-1).includes("/")&&("/"===e[n-2]&&Number(e.slice(n-1,n+1))>i&&"/"!==e[n]||"/"===e[n]||"/"===e[n-3]&&Number(e.slice(n-2,n))>i&&"/"!==e[n-1]||"/"===e[n-1]),l=Number(e.slice(n-3,n-1))<=s&&!e.slice(n-3,n-1).includes("/")&&"/"===e[n-1]&&(Number(e.slice(n,n+2))>i||"/"===e[n+1]),c=Number(e.slice(n-3,n-1))>s&&!e.slice(n-3,n-1).includes("/")&&!e.slice(n-2,n).includes("/")&&Number(e.slice(n-2,n))>i,o=Number(e.slice(n-3,n-1))<=s&&!e.slice(n-3,n-1).includes("/")&&"/"!==e[n-1]&&Number(e.slice(n-1,n+1))>i;if(Number(S)>1&&this.leadZeroDateTime||a||r||l||c||o){n+=1,this._shiftStep(t,n,p.length),_--,this.leadZeroDateTime&&(h+="0");continue}}h+=S,n++}else-1!==this.maskSpecialCharacters.indexOf(t[n])?(h+=t[n],n++,this._shiftStep(t,n,p.length),_--):this.maskSpecialCharacters.indexOf(S)>-1&&this.maskAvailablePatterns[t[n]]&&this.maskAvailablePatterns[t[n]].optional?(p[n]&&"099.099.099.099"!==t&&"000.000.000-00"!==t&&"00.000.000/0000-00"!==t&&(h+=p[n]),n++,_--):"*"===this.maskExpression[n+1]&&this._findSpecialChar(this.maskExpression[n+2])&&this._findSpecialChar(S)===this.maskExpression[n+2]&&l||"?"===this.maskExpression[n+1]&&this._findSpecialChar(this.maskExpression[n+2])&&this._findSpecialChar(S)===this.maskExpression[n+2]&&l?(n+=3,h+=S):this.showMaskTyped&&this.maskSpecialCharacters.indexOf(S)<0&&S!==this.placeHolderCharacter&&(u=!0);h.length+1===t.length&&-1!==this.maskSpecialCharacters.indexOf(t[t.length-1])&&(h+=t[t.length-1]);let f=s+1;for(;this._shift.has(f);)o++,f++;let d=i?n:this._shift.has(s)?o:0;u&&d--,r(d,c),o<0&&this._shift.clear();let k=!1;a&&(k=p.every(e=>this.maskSpecialCharacters.includes(e)));let g=`${this.prefix}${k?"":h}${this.suffix}`;return 0===h.length&&(g=`${this.prefix}${h}`),g}_findSpecialChar(e){return this.maskSpecialCharacters.find(t=>t===e)}_checkSymbolMask(e,t){return this.maskAvailablePatterns=this.customPattern?this.customPattern:this.maskAvailablePatterns,this.maskAvailablePatterns[t]&&this.maskAvailablePatterns[t].pattern&&this.maskAvailablePatterns[t].pattern.test(e)}_stripToDecimal(e){return e.split("").filter((e,t)=>e.match("^-?\\d")||e.match("\\s")||"."===e||","===e||"-"===e&&0===t&&this.allowNegativeNumbers).join("")}_charToRegExpExpression(e){if(e){const t="[\\^$.|?*+()";return" "===e?"\\s":t.indexOf(e)>=0?"\\"+e:e}return e}_shiftStep(e,t,s){const i=/[*?]/g.test(e.slice(0,t))?s:t;this._shift.add(i+this.prefix.length||0)}}return e.\u0275fac=function(t){return new(t||e)(i.Vb(n))},e.\u0275prov=i.Fb({token:e,factory:e.\u0275fac}),e})(),m=(()=>{class e extends p{constructor(e,t,s,i){super(t),this.document=e,this._config=t,this._elementRef=s,this._renderer=i,this.maskExpression="",this.isNumberValue=!1,this.placeHolderCharacter="_",this.maskIsShown="",this.selStart=null,this.selEnd=null,this.writingValue=!1,this.onChange=e=>{}}applyMask(e,t,s=0,i=!1,a=!1,r=(()=>{})){if(!t)return e;if(this.maskIsShown=this.showMaskTyped?this.showMaskInInput():"","IP"===this.maskExpression&&this.showMaskTyped&&(this.maskIsShown=this.showMaskInInput(e||"#")),"CPF_CNPJ"===this.maskExpression&&this.showMaskTyped&&(this.maskIsShown=this.showMaskInInput(e||"#")),!e&&this.showMaskTyped)return this.formControlResult(this.prefix),this.prefix+this.maskIsShown;const n=e&&"number"==typeof this.selStart?e[this.selStart]:"";let h="";if(void 0!==this.hiddenInput){let t=this.actualValue.split("");""!==e&&t.length?"number"==typeof this.selStart&&"number"==typeof this.selEnd&&(e.length>t.length?t.splice(this.selStart,0,n):e.length<t.length&&(t.length-e.length==1?t.splice(this.selStart-1,1):t.splice(this.selStart,this.selEnd-this.selStart))):t=[],h=this.actualValue.length?this.shiftTypedSymbols(t.join("")):e}h=Boolean(h)&&h.length?h:e;const l=super.applyMask(h,t,s,i,a,r);if(this.actualValue=this.getActualValue(l),"."===this.thousandSeparator&&"."===this.decimalMarker&&(this.decimalMarker=","),this.maskExpression.startsWith("separator")&&!0===this.dropSpecialCharacters&&(this.maskSpecialCharacters=this.maskSpecialCharacters.filter(e=>e!==this.decimalMarker)),this.formControlResult(l),!this.showMaskTyped)return this.hiddenInput&&l&&l.length?this.hideInput(l,this.maskExpression):l;const c=l.length,o=this.prefix+this.maskIsShown;if(this.maskExpression.includes("H")){const e=this._numberSkipedSymbols(l);return l+o.slice(c+e)}return"IP"===this.maskExpression||"CPF_CNPJ"===this.maskExpression?l+o:l+o.slice(c)}_numberSkipedSymbols(e){const t=/(^|\D)(\d\D)/g;let s=t.exec(e),i=0;for(;null!=s;)i+=1,s=t.exec(e);return i}applyValueChanges(e=0,t,s,i=(()=>{})){const a=this._elementRef.nativeElement;a.value=this.applyMask(a.value,this.maskExpression,e,t,s,i),a!==this.document.activeElement&&this.clearIfNotMatchFn()}hideInput(e,t){return e.split("").map((e,s)=>this.maskAvailablePatterns&&this.maskAvailablePatterns[t[s]]&&this.maskAvailablePatterns[t[s]].symbol?this.maskAvailablePatterns[t[s]].symbol:e).join("")}getActualValue(e){const t=e.split("").filter((e,t)=>this._checkSymbolMask(e,this.maskExpression[t])||this.maskSpecialCharacters.includes(this.maskExpression[t])&&e===this.maskExpression[t]);return t.join("")===e?t.join(""):e}shiftTypedSymbols(e){let t="";return(e&&e.split("").map((s,i)=>{if(this.maskSpecialCharacters.includes(e[i+1])&&e[i+1]!==this.maskExpression[i+1])return t=s,e[i+1];if(t.length){const e=t;return t="",e}return s})||[]).join("")}showMaskInInput(e){if(this.showMaskTyped&&this.shownMaskExpression){if(this.maskExpression.length!==this.shownMaskExpression.length)throw new Error("Mask expression must match mask placeholder length");return this.shownMaskExpression}if(this.showMaskTyped){if(e){if("IP"===this.maskExpression)return this._checkForIp(e);if("CPF_CNPJ"===this.maskExpression)return this._checkForCpfCnpj(e)}return this.maskExpression.replace(/\w/g,this.placeHolderCharacter)}return""}clearIfNotMatchFn(){const e=this._elementRef.nativeElement;this.clearIfNotMatch&&this.prefix.length+this.maskExpression.length+this.suffix.length!==e.value.replace(/_/g,"").length&&(this.formElementProperty=["value",""],this.applyMask(e.value,this.maskExpression))}set formElementProperty([e,t]){Promise.resolve().then(()=>this._renderer.setProperty(this._elementRef.nativeElement,e,t))}checkSpecialCharAmount(e){return e.split("").filter(e=>this._findSpecialChar(e)).length}removeMask(e){return this._removeMask(this._removeSuffix(this._removePrefix(e)),this.maskSpecialCharacters.concat("_").concat(this.placeHolderCharacter))}_checkForIp(e){if("#"===e)return`${this.placeHolderCharacter}.${this.placeHolderCharacter}.${this.placeHolderCharacter}.${this.placeHolderCharacter}`;const t=[];for(let s=0;s<e.length;s++)e[s].match("\\d")&&t.push(e[s]);return t.length<=3?`${this.placeHolderCharacter}.${this.placeHolderCharacter}.${this.placeHolderCharacter}`:t.length>3&&t.length<=6?`${this.placeHolderCharacter}.${this.placeHolderCharacter}`:t.length>6&&t.length<=9?this.placeHolderCharacter:""}_checkForCpfCnpj(e){const t=`${this.placeHolderCharacter}${this.placeHolderCharacter}${this.placeHolderCharacter}.${this.placeHolderCharacter}${this.placeHolderCharacter}${this.placeHolderCharacter}.${this.placeHolderCharacter}${this.placeHolderCharacter}${this.placeHolderCharacter}-${this.placeHolderCharacter}${this.placeHolderCharacter}`,s=`${this.placeHolderCharacter}${this.placeHolderCharacter}.${this.placeHolderCharacter}${this.placeHolderCharacter}${this.placeHolderCharacter}.${this.placeHolderCharacter}${this.placeHolderCharacter}${this.placeHolderCharacter}/${this.placeHolderCharacter}${this.placeHolderCharacter}${this.placeHolderCharacter}${this.placeHolderCharacter}-${this.placeHolderCharacter}${this.placeHolderCharacter}`;if("#"===e)return t;const i=[];for(let a=0;a<e.length;a++)e[a].match("\\d")&&i.push(e[a]);return i.length<=3?t.slice(i.length,t.length):i.length>3&&i.length<=6?t.slice(i.length+1,t.length):i.length>6&&i.length<=9?t.slice(i.length+2,t.length):i.length>9&&i.length<11?t.slice(i.length+3,t.length):11===i.length?"":12===i.length?s.slice(17===e.length?16:15,s.length):i.length>12&&i.length<=14?s.slice(i.length+4,s.length):""}formControlResult(e){this.writingValue||(Array.isArray(this.dropSpecialCharacters)?this.onChange(this._toNumber(this._removeMask(this._removeSuffix(this._removePrefix(e)),this.dropSpecialCharacters))):this.onChange(this.dropSpecialCharacters?this._toNumber(this._checkSymbols(e)):this._removeSuffix(e)))}_toNumber(e){if(!this.isNumberValue)return e;const t=Number(e);return Number.isNaN(t)?e:t}_removeMask(e,t){return e?e.replace(this._regExpForRemove(t),""):e}_removePrefix(e){return this.prefix&&e?e.replace(this.prefix,""):e}_removeSuffix(e){return this.suffix&&e?e.replace(this.suffix,""):e}_retrieveSeparatorValue(e){return this._removeMask(this._removeSuffix(this._removePrefix(e)),this.maskSpecialCharacters)}_regExpForRemove(e){return new RegExp(e.map(e=>"\\"+e).join("|"),"gi")}_checkSymbols(e){if(""===e)return e;const t=this._retrieveSeparatorPrecision(this.maskExpression);let s=this._retrieveSeparatorValue(e);return"."!==this.decimalMarker&&(s=s.replace(this.decimalMarker,".")),this.isNumberValue?t?e===this.decimalMarker?null:this._checkPrecision(this.maskExpression,s):Number(s):s}_retrieveSeparatorPrecision(e){const t=e.match(new RegExp("^separator\\.([^d]*)"));return t?Number(t[1]):null}_checkPrecision(e,t){return e.indexOf("2")>0?Number(t).toFixed(2):Number(t)}}return e.\u0275fac=function(t){return new(t||e)(i.Vb(r.d),i.Vb(n),i.Vb(i.l),i.Vb(i.D))},e.\u0275prov=i.Fb({token:e,factory:e.\u0275fac}),e})(),f=(()=>{class e{constructor(e,t,s){this.document=e,this._maskService=t,this._config=s,this.maskExpression="",this.specialCharacters=[],this.patterns={},this.prefix="",this.suffix="",this.thousandSeparator=" ",this.decimalMarker=".",this.dropSpecialCharacters=null,this.hiddenInput=null,this.showMaskTyped=null,this.placeHolderCharacter=null,this.shownMaskExpression=null,this.showTemplate=null,this.clearIfNotMatch=null,this.validation=null,this.separatorLimit=null,this.allowNegativeNumbers=null,this.leadZeroDateTime=null,this._maskValue="",this._position=null,this._maskExpressionArray=[],this._justPasted=!1,this.onChange=e=>{},this.onTouch=()=>{}}ngOnChanges(e){const{maskExpression:t,specialCharacters:s,patterns:i,prefix:a,suffix:r,thousandSeparator:n,decimalMarker:h,dropSpecialCharacters:l,hiddenInput:c,showMaskTyped:o,placeHolderCharacter:u,shownMaskExpression:p,showTemplate:m,clearIfNotMatch:f,validation:d,separatorLimit:k,allowNegativeNumbers:g,leadZeroDateTime:_}=e;if(t&&(this._maskValue=t.currentValue||"",t.currentValue&&t.currentValue.split("||").length>1&&(this._maskExpressionArray=t.currentValue.split("||").sort((e,t)=>e.length-t.length),this._maskValue=this._maskExpressionArray[0],this.maskExpression=this._maskExpressionArray[0],this._maskService.maskExpression=this._maskExpressionArray[0])),s){if(!s.currentValue||!Array.isArray(s.currentValue))return;this._maskService.maskSpecialCharacters=s.currentValue||[]}i&&i.currentValue&&(this._maskService.maskAvailablePatterns=i.currentValue),a&&(this._maskService.prefix=a.currentValue),r&&(this._maskService.suffix=r.currentValue),n&&(this._maskService.thousandSeparator=n.currentValue),h&&(this._maskService.decimalMarker=h.currentValue),l&&(this._maskService.dropSpecialCharacters=l.currentValue),c&&(this._maskService.hiddenInput=c.currentValue),o&&(this._maskService.showMaskTyped=o.currentValue),u&&(this._maskService.placeHolderCharacter=u.currentValue),p&&(this._maskService.shownMaskExpression=p.currentValue),m&&(this._maskService.showTemplate=m.currentValue),f&&(this._maskService.clearIfNotMatch=f.currentValue),d&&(this._maskService.validation=d.currentValue),k&&(this._maskService.separatorLimit=k.currentValue),g&&(this._maskService.allowNegativeNumbers=g.currentValue,this._maskService.allowNegativeNumbers&&(this._maskService.maskSpecialCharacters=this._maskService.maskSpecialCharacters.filter(e=>"-"!==e))),_&&(this._maskService.leadZeroDateTime=_.currentValue),this._applyMask()}validate({value:e}){if(!this._maskService.validation||!this._maskValue)return null;if(this._maskService.ipError)return this._createValidationError(e);if(this._maskService.cpfCnpjError)return this._createValidationError(e);if(this._maskValue.startsWith("separator"))return null;if(u.includes(this._maskValue))return null;if(this._maskService.clearIfNotMatch)return null;if(o.includes(this._maskValue))return this._validateTime(e);if(e&&e.toString().length>=1){let t=0;for(const s in this._maskService.maskAvailablePatterns)if(this._maskService.maskAvailablePatterns[s].optional&&!0===this._maskService.maskAvailablePatterns[s].optional){if(this._maskValue.indexOf(s)!==this._maskValue.lastIndexOf(s)?t+=this._maskValue.split("").filter(e=>e===s).join("").length:-1!==this._maskValue.indexOf(s)&&t++,-1!==this._maskValue.indexOf(s)&&e.toString().length>=this._maskValue.indexOf(s))return null;if(t===this._maskValue.length)return null}if(1===this._maskValue.indexOf("{")&&e.toString().length===this._maskValue.length+Number(this._maskValue.split("{")[1].split("}")[0])-4)return null;if(1===this._maskValue.indexOf("*")||1===this._maskValue.indexOf("?"))return null;if(this._maskValue.indexOf("*")>1&&e.toString().length<this._maskValue.indexOf("*")||this._maskValue.indexOf("?")>1&&e.toString().length<this._maskValue.indexOf("?")||1===this._maskValue.indexOf("{"))return this._createValidationError(e);if(-1===this._maskValue.indexOf("*")||-1===this._maskValue.indexOf("?")){const s=this._maskService.dropSpecialCharacters?this._maskValue.length-this._maskService.checkSpecialCharAmount(this._maskValue)-t:this._maskValue.length-t;if(e.toString().length<s)return this._createValidationError(e)}}return null}onPaste(){this._justPasted=!0}onInput(e){const t=e.target;if(this._inputValue=t.value,this._setMask(),!this._maskValue)return void this.onChange(t.value);const s=1===t.selectionStart?t.selectionStart+this._maskService.prefix.length:t.selectionStart;let i=0,a=!1;if(this._maskService.applyValueChanges(s,this._justPasted,"Backspace"===this._code,(e,t)=>{this._justPasted=!1,i=e,a=t}),this.document.activeElement!==t)return;this._position=1===this._position&&1===this._inputValue.length?null:this._position;let r=this._position?this._inputValue.length+s+i:s+("Backspace"!==this._code||a?i:0);r>this._getActualInputLength()&&(r=this._getActualInputLength()),t.setSelectionRange(r,r),this._position=null}onBlur(){this._maskValue&&this._maskService.clearIfNotMatchFn(),this.onTouch()}onFocus(e){if(!this._maskValue)return;const t=e.target;null!==t&&null!==t.selectionStart&&t.selectionStart===t.selectionEnd&&t.selectionStart>this._maskService.prefix.length&&38!==e.keyCode&&this._maskService.showMaskTyped&&(this._maskService.maskIsShown=this._maskService.showMaskInInput(),t.setSelectionRange&&this._maskService.prefix+this._maskService.maskIsShown===t.value?(t.focus(),t.setSelectionRange(0,0)):t.selectionStart>this._maskService.actualValue.length&&t.setSelectionRange(this._maskService.actualValue.length,this._maskService.actualValue.length));const s=t.value&&t.value!==this._maskService.prefix?t.value:this._maskService.prefix+this._maskService.maskIsShown;t.value!==s&&(t.value=s),(t.selectionStart||t.selectionEnd)<=this._maskService.prefix.length?t.selectionStart=this._maskService.prefix.length:t.selectionEnd>this._getActualInputLength()&&(t.selectionEnd=this._getActualInputLength())}onKeyDown(e){var t;if(!this._maskValue)return;this._code=e.code?e.code:e.key;const s=e.target;if(this._inputValue=s.value,this._setMask(),38===e.keyCode&&e.preventDefault(),37===e.keyCode||8===e.keyCode||46===e.keyCode){if(8===e.keyCode&&0===s.value.length&&(s.selectionStart=s.selectionEnd),8===e.keyCode&&0!==s.selectionStart)if(this.specialCharacters=(null===(t=this.specialCharacters)||void 0===t?void 0:t.length)?this.specialCharacters:this._config.specialCharacters,this.prefix.length>1&&s.selectionStart<=this.prefix.length)s.setSelectionRange(this.prefix.length,this.prefix.length);else{if(this._inputValue.length!==s.selectionStart&&1!==s.selectionStart)for(;this.specialCharacters.includes(this._inputValue[s.selectionStart-1].toString())&&(this.prefix.length>=1&&s.selectionStart>this.prefix.length||0===this.prefix.length);)s.setSelectionRange(s.selectionStart-1,s.selectionStart-1);this.suffixCheckOnPressDelete(e.keyCode,s)}this.suffixCheckOnPressDelete(e.keyCode,s),this._maskService.prefix.length&&s.selectionStart<=this._maskService.prefix.length&&s.selectionEnd<=this._maskService.prefix.length&&e.preventDefault();const i=s.selectionStart;8!==e.keyCode||s.readOnly||0!==i||s.selectionEnd!==s.value.length||0===s.value.length||(this._position=this._maskService.prefix?this._maskService.prefix.length:0,this._maskService.applyMask(this._maskService.prefix,this._maskService.maskExpression,this._position))}this.suffix&&this.suffix.length>1&&this._inputValue.length-this.suffix.length<s.selectionStart?s.setSelectionRange(this._inputValue.length-this.suffix.length,this._inputValue.length):(65===e.keyCode&&!0===e.ctrlKey||65===e.keyCode&&!0===e.metaKey)&&(s.setSelectionRange(0,this._getActualInputLength()),e.preventDefault()),this._maskService.selStart=s.selectionStart,this._maskService.selEnd=s.selectionEnd}writeValue(e){return t=this,void 0,i=function*(){"object"==typeof e&&null!==e&&"value"in e&&("disable"in e&&this.setDisabledState(Boolean(e.disable)),e=e.value),void 0===e&&(e=""),"number"==typeof e&&(e=String(e),e="."!==this.decimalMarker?e.replace(".",this.decimalMarker):e,this._maskService.isNumberValue=!0),e&&this._maskService.maskExpression||this._maskService.maskExpression&&(this._maskService.prefix||this._maskService.showMaskTyped)?(this._maskService.writingValue=!0,this._maskService.formElementProperty=["value",this._maskService.applyMask(e,this._maskService.maskExpression)],this._maskService.writingValue=!1):this._maskService.formElementProperty=["value",e],this._inputValue=e},new((s=void 0)||(s=Promise))(function(e,a){function r(e){try{h(i.next(e))}catch(t){a(t)}}function n(e){try{h(i.throw(e))}catch(t){a(t)}}function h(t){var i;t.done?e(t.value):(i=t.value,i instanceof s?i:new s(function(e){e(i)})).then(r,n)}h((i=i.apply(t,[])).next())});var t,s,i}registerOnChange(e){this.onChange=e,this._maskService.onChange=this.onChange}registerOnTouched(e){this.onTouch=e}suffixCheckOnPressDelete(e,t){46===e&&this.suffix.length>0&&this._inputValue.length-this.suffix.length<=t.selectionStart&&t.setSelectionRange(this._inputValue.length-this.suffix.length,this._inputValue.length),8===e&&(this.suffix.length>1&&this._inputValue.length-this.suffix.length<t.selectionStart&&t.setSelectionRange(this._inputValue.length-this.suffix.length,this._inputValue.length),1===this.suffix.length&&this._inputValue.length===t.selectionStart&&t.setSelectionRange(t.selectionStart-1,t.selectionStart-1))}setDisabledState(e){this._maskService.formElementProperty=["disabled",e]}_repeatPatternSymbols(e){return e.match(/{[0-9]+}/)&&e.split("").reduce((t,s,i)=>{if(this._start="{"===s?i:this._start,"}"!==s)return this._maskService._findSpecialChar(s)?t+s:t;this._end=i;const a=Number(e.slice(this._start+1,this._end));return t+new Array(a+1).join(e[this._start-1])},"")||e}_applyMask(){this._maskService.maskExpression=this._repeatPatternSymbols(this._maskValue||""),this._maskService.formElementProperty=["value",this._maskService.applyMask(this._inputValue,this._maskService.maskExpression)]}_validateTime(e){const t=this._maskValue.split("").filter(e=>":"!==e).length;return null===e||0===e.length?null:0==+e[e.length-1]&&e.length<t||e.length<=t-2?this._createValidationError(e):null}_getActualInputLength(){return this._maskService.actualValue.length||this._maskService.actualValue.length+this._maskService.prefix.length}_createValidationError(e){return{mask:{requiredMask:this._maskValue,actualValue:e}}}_setMask(){this._maskExpressionArray.length>0&&this._maskExpressionArray.some(e=>{const t=this._maskService.removeMask(this._inputValue).length<=this._maskService.removeMask(e).length;if(this._inputValue&&t)return this._maskValue=e,this.maskExpression=e,this._maskService.maskExpression=e,t;this._maskValue=this._maskExpressionArray[this._maskExpressionArray.length-1],this.maskExpression=this._maskExpressionArray[this._maskExpressionArray.length-1],this._maskService.maskExpression=this._maskExpressionArray[this._maskExpressionArray.length-1]})}}return e.\u0275fac=function(t){return new(t||e)(i.Jb(r.d),i.Jb(m),i.Jb(n))},e.\u0275dir=i.Eb({type:e,selectors:[["input","mask",""],["textarea","mask",""]],hostBindings:function(e,t){1&e&&i.Yb("paste",function(){return t.onPaste()})("input",function(e){return t.onInput(e)})("blur",function(){return t.onBlur()})("click",function(e){return t.onFocus(e)})("keydown",function(e){return t.onKeyDown(e)})},inputs:{maskExpression:["mask","maskExpression"],specialCharacters:"specialCharacters",patterns:"patterns",prefix:"prefix",suffix:"suffix",thousandSeparator:"thousandSeparator",decimalMarker:"decimalMarker",dropSpecialCharacters:"dropSpecialCharacters",hiddenInput:"hiddenInput",showMaskTyped:"showMaskTyped",placeHolderCharacter:"placeHolderCharacter",shownMaskExpression:"shownMaskExpression",showTemplate:"showTemplate",clearIfNotMatch:"clearIfNotMatch",validation:"validation",separatorLimit:"separatorLimit",allowNegativeNumbers:"allowNegativeNumbers",leadZeroDateTime:"leadZeroDateTime"},features:[i.wb([{provide:a.e,useExisting:Object(i.S)(()=>e),multi:!0},{provide:a.d,useExisting:Object(i.S)(()=>e),multi:!0},m]),i.vb]}),e})(),d=(()=>{class e{static forRoot(t){return{ngModule:e,providers:[{provide:h,useValue:t},{provide:l,useValue:c},{provide:n,useFactory:k,deps:[l,h]},p]}}static forChild(){return{ngModule:e}}}return e.\u0275mod=i.Hb({type:e}),e.\u0275inj=i.Gb({factory:function(t){return new(t||e)}}),e})();function k(e,t){return Object.assign(Object.assign({},e),t instanceof Function?t():t)}const g="undefined"!=typeof globalThis?globalThis:"undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:{};g.KeyboardEvent||(g.KeyboardEvent=function(e,t){})}}]);