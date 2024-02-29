'use strict';

/**
 * Infinite Ajax Scroll v3.1.0
 * Turn your existing pagination into infinite scrolling pages with ease
 *
 * Commercial use requires one-time purchase of a commercial license
 * https://infiniteajaxscroll.com/docs/license.html
 *
 * Copyright 2014-2023 Webcreate (Jeroen Fiege)
 * https://infiniteajaxscroll.com
 */
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):(t="undefined"!=typeof globalThis?globalThis:t||self).InfiniteAjaxScroll=e()}(this,(function(){"use strict";function t(t){return"object"==typeof window.Node?t instanceof window.Node:null!==t&&"object"==typeof t&&"number"==typeof t.nodeType&&"string"==typeof t.nodeName}function e(e,n){if(void 0===n&&(n=document),e instanceof Array)return e.filter(t);if(t(e))return[e];if(o=Object.prototype.toString.call(i=e),"object"==typeof window.NodeList?i instanceof window.NodeList:null!==i&&"object"==typeof i&&"number"==typeof i.length&&/^\[object (HTMLCollection|NodeList|Object)\]$/.test(o)&&(0===i.length||t(i[0])))return Array.prototype.slice.call(e);var i,o;if("string"==typeof e)try{var r=n.querySelectorAll(e);return Array.prototype.slice.call(r)}catch(t){return[]}return[]}var n=Object.prototype.hasOwnProperty,i=Object.prototype.toString,o=Object.defineProperty,r=Object.getOwnPropertyDescriptor,s=function(t){return"function"==typeof Array.isArray?Array.isArray(t):"[object Array]"===i.call(t)},l=function(t){if(!t||"[object Object]"!==i.call(t))return!1;var e,o=n.call(t,"constructor"),r=t.constructor&&t.constructor.prototype&&n.call(t.constructor.prototype,"isPrototypeOf");if(t.constructor&&!o&&!r)return!1;for(e in t);return void 0===e||n.call(t,e)},a=function(t,e){o&&"__proto__"===e.name?o(t,e.name,{enumerable:!0,configurable:!0,value:e.newValue,writable:!0}):t[e.name]=e.newValue},h=function(t,e){if("__proto__"===e){if(!n.call(t,e))return;if(r)return r(t,e).value}return t[e]},c=function t(){var e,n,i,o,r,c,p=arguments,u=arguments[0],d=1,f=arguments.length,m=!1;for("boolean"==typeof u&&(m=u,u=arguments[1]||{},d=2),(null==u||"object"!=typeof u&&"function"!=typeof u)&&(u={});f>d;++d)if(null!=(e=p[d]))for(n in e)i=h(u,n),u!==(o=h(e,n))&&(m&&o&&(l(o)||(r=s(o)))?(r?(r=!1,c=i&&s(i)?i:[]):c=i&&l(i)?i:{},a(u,{name:n,newValue:t(m,c,o)})):void 0!==o&&a(u,{name:n,newValue:o}));return u},p="undefined"!=typeof globalThis?globalThis:"undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:{},u="Expected a function",d=/^\s+|\s+$/g,f=/^[-+]0x[0-9a-f]+$/i,m=/^0b[01]+$/i,g=/^0o[0-7]+$/i,v=parseInt,y="object"==typeof self&&self&&self.Object===Object&&self,b="object"==typeof p&&p&&p.Object===Object&&p||y||Function("return this")(),x=Object.prototype.toString,w=Math.max,O=Math.min,I=function(){return b.Date.now()};function E(t,e,n){var i,o,r,s,l,a,h=0,c=!1,p=!1,d=!0;if("function"!=typeof t)throw new TypeError(u);function f(e){var n=i,r=o;return i=o=void 0,h=e,s=t.apply(r,n)}function m(t){return h=t,l=setTimeout(v,e),c?f(t):s}function g(t){var n=t-a;return void 0===a||n>=e||0>n||p&&t-h>=r}function v(){var t=I();if(g(t))return y(t);l=setTimeout(v,function(t){var n=e-(t-a);return p?O(n,r-(t-h)):n}(t))}function y(t){return l=void 0,d&&i?f(t):(i=o=void 0,s)}function b(){var t=I(),n=g(t);if(i=arguments,o=this,a=t,n){if(void 0===l)return m(a);if(p)return l=setTimeout(v,e),f(a)}return void 0===l&&(l=setTimeout(v,e)),s}return e=T(e)||0,S(n)&&(c=!!n.leading,r=(p="maxWait"in n)?w(T(n.maxWait)||0,e):r,d="trailing"in n?!!n.trailing:d),b.cancel=function(){void 0!==l&&clearTimeout(l),h=0,i=a=o=l=void 0},b.flush=function(){return void 0===l?s:y(I())},b}function S(t){var e=typeof t;return!!t&&("object"==e||"function"==e)}function T(t){if("number"==typeof t)return t;if(function(t){return"symbol"==typeof t||function(t){return!!t&&"object"==typeof t}(t)&&"[object Symbol]"==x.call(t)}(t))return NaN;if(S(t)){var e="function"==typeof t.valueOf?t.valueOf():t;t=S(e)?e+"":e}if("string"!=typeof t)return 0===t?t:+t;t=t.replace(d,"");var n=m.test(t);return n||g.test(t)?v(t.slice(2),n?2:8):f.test(t)?NaN:+t}var L=function(t,e,n){var i=!0,o=!0;if("function"!=typeof t)throw new TypeError(u);return S(n)&&(i="leading"in n?!!n.leading:i,o="trailing"in n?!!n.trailing:o),E(t,e,{leading:i,maxWait:e,trailing:o})},C={item:void 0,next:void 0,prev:void 0,pagination:void 0,responseType:"document",bind:!0,scrollContainer:window,spinner:!1,logger:!0,loadOnScroll:!0,negativeMargin:0,trigger:!1,prefill:!0},P=function(t,n){var i=e(t);if(i.length>1)throw Error('Expected single element for "'+n+'"');if(0===i.length)throw Error('Element "'+t+'" not found for "'+n+'"')},j=function(t,n){if(0===e(t).length)throw Error('Element "'+t+'" not found for "'+n+'"')},N=function(t){for(var e=[],n=arguments.length-1;n-- >0;)e[n]=arguments[n+1];try{t.apply(void 0,e)}catch(t){console&&console.warn&&console.warn(t.message)}};function F(t){if(t!==window)return{x:t.scrollLeft,y:t.scrollTop};var e=void 0!==window.pageXOffset,n="CSS1Compat"===(document.compatMode||"");return{x:e?window.pageXOffset:n?document.documentElement.scrollLeft:document.body.scrollLeft,y:e?window.pageYOffset:n?document.documentElement.scrollTop:document.body.scrollTop}}function _(t){var e;if(t!==window)e=t.getBoundingClientRect();else{var n=document.documentElement,i=document.body;e={top:0,left:0,right:n.clientWidth||i.clientWidth,width:n.clientWidth||i.clientWidth,bottom:n.clientHeight||i.clientHeight,height:n.clientHeight||i.clientHeight}}return e}var H="append",M="appended",R="prepend",B="prepended",D="binded",k="unbinded",A="hit",W="top",q="load",z="loaded",X="error",$="first",V="last",Y="next",G="nexted",U="prev",J="preved",K="ready",Q="scrolled",Z="resized",tt="page",et="prefill",nt="prefilled",it={y:0,x:0,deltaY:0,deltaX:0};function ot(t,e){var n=F(t);return n.deltaY=n.y-(e?e.y:n.y),n.deltaX=n.x-(e?e.x:n.x),n}function rt(){var t=this,e=t._lastScroll=ot(t.scrollContainer,t._lastScroll||it);this.emitter.emit(Q,{scroll:e})}function st(){var t=this,e=t._lastScroll=ot(t.scrollContainer,t._lastScroll||it);this.emitter.emit(Z,{scroll:e})}function lt(){}lt.prototype={on:function(t,e,n){var i=this.e||(this.e={});return(i[t]||(i[t]=[])).push({fn:e,ctx:n}),this},once:function(t,e,n){var i=this;function o(){i.off(t,o),e.apply(n,arguments)}return o._=e,this.on(t,o,n)},emit:function(t){for(var e=[].slice.call(arguments,1),n=((this.e||(this.e={}))[t]||[]).slice(),i=0,o=n.length;o>i;i++)n[i].fn.apply(n[i].ctx,e);return this},off:function(t,e){var n=this.e||(this.e={}),i=n[t],o=[];if(i&&e)for(var r=0,s=i.length;s>r;r++)i[r].fn!==e&&i[r].fn._!==e&&o.push(i[r]);return o.length?n[t]=o:delete n[t],this}};var at=lt;function ht(t){var n=this,i=n._lastResponse||document.body,o=e(n.options.next,i)[0];if(o)return n.load(o.href).then((function(o){i=n._lastResponse=o.xhr.response;var r=e(n.options.next,i)[0];return n.append(o.items).then((function(){return!!r})).then((function(e){return!e&&1>=t&&console&&console.warn&&console.warn('Element "'+n.options.next+'" not found for "options.next" on "'+o.url+'"'),e}))}));N(P,n.options.next,"options.next")}function ct(t){var n=this,i=n._prevEl||e(n.options.prev,document.body)[0];if(void 0!==n.options.prev){if(i)return n.load(i.href).then((function(t){var i=n._prevEl=e(n.options.prev,t.xhr.response)[0];return n.prepend(t.items).then((function(){return!!i}))}));N(P,n.options.prev,"options.prev")}}at.TinyEmitter=lt;var pt={element:void 0,hide:!1};var ut=function(t,e){this.options=c({},pt,function(t){return"string"==typeof t||"object"==typeof t&&t.nodeType===Node.ELEMENT_NODE?t={element:t,hide:!0}:"boolean"==typeof t&&(t={element:void 0,hide:t}),t}(e)),this.originalDisplayStyles=new WeakMap,this.options.hide&&(N(j,this.options.element,"pagination.element"),t.on(D,this.hide.bind(this)),t.on(k,this.restore.bind(this)))};ut.prototype.hide=function(){var t=this;e(this.options.element).forEach((function(e){t.originalDisplayStyles.set(e,window.getComputedStyle(e).display),e.style.display="none"}))},ut.prototype.restore=function(){var t=this;e(this.options.element).forEach((function(e){e.style.display=t.originalDisplayStyles.get(e)||"block"}))};var dt={element:void 0,delay:600,show:function(t){t.style.opacity="1"},hide:function(t){t.style.opacity="0"}};var ft=function(t,n){!1!==n&&(this.ias=t,this.options=c({},dt,function(t){return("string"==typeof t||"object"==typeof t&&t.nodeType===Node.ELEMENT_NODE)&&(t={element:t}),t}(n)),void 0!==this.options.element&&P(this.options.element,"spinner.element"),this.element=e(this.options.element)[0],this.hideFn=this.options.hide,this.showFn=this.options.show,t.on(D,this.bind.bind(this)),t.on(D,this.hide.bind(this)))};ft.prototype.bind=function(){var t,e,n=this,i=this.ias;i.on(Y,(function(){t=+new Date,n.show()})),i.on(V,(function(){n.hide()})),i.on(H,(function(i){e=Math.max(0,n.options.delay-(+new Date-t));var o=i.appendFn.bind({});i.appendFn=function(t,i,r){return new Promise((function(s){setTimeout((function(){n.hide().then((function(){o(t,i,r),s()}))}),e)}))}}))},ft.prototype.show=function(){return Promise.resolve(this.showFn(this.element))},ft.prototype.hide=function(){return Promise.resolve(this.hideFn(this.element))};var mt={hit:function(){console.log("Hit scroll threshold")},top:function(){console.log("Hit top scroll threshold")},binded:function(){console.log("Binded event handlers")},unbinded:function(){console.log("Unbinded event handlers")},next:function(t){console.log("Next page triggered [pageIndex="+t.pageIndex+"]")},nexted:function(t){console.log("Next page completed [pageIndex="+t.pageIndex+"]")},prev:function(t){console.log("Previous page triggered [pageIndex="+t.pageIndex+"]")},preved:function(t){console.log("Previous page completed [pageIndex="+t.pageIndex+"]")},load:function(t){console.log("Start loading "+t.url)},loaded:function(){console.log("Finished loading")},append:function(){console.log("Start appending items")},appended:function(t){console.log("Finished appending "+t.items.length+" item(s)")},prepend:function(){console.log("Start prepending items")},prepended:function(t){console.log("Finished prepending "+t.items.length+" item(s)")},last:function(){console.log("No more pages left to load")},first:function(){console.log("Reached first page")},page:function(t){console.log("Page changed [pageIndex="+t.pageIndex+"]")},prefill:function(t){console.log("Start prefilling")},prefilled:function(t){console.log("Finished prefilling")}};var gt=function(t,e){if(!1!==e){var n=function(t){return!0===t&&(t=mt),t}(e);Object.keys(n).forEach((function(e){t.on(e,n[e])}))}};var vt=function(t){this.ias=t,this.pageBreaks=[],this.currentPageIndex=t.pageIndex,this.currentScrollTop=0,t.on(D,this.binded.bind(this)),t.on(Y,this.next.bind(this)),t.on(U,this.prev.bind(this)),t.on(Q,this.scrolled.bind(this)),t.on(Z,this.scrolled.bind(this))};vt.prototype.binded=function(){this.ias.sentinel()&&this.pageBreaks.push({pageIndex:this.currentPageIndex,url:""+document.location,title:document.title,sentinel:this.ias.sentinel()})},vt.prototype.next=function(){var t=this,e=""+document.location,n=document.title,i=function(t){e=t.url,t.xhr.response&&(n=t.xhr.response.title)};this.ias.once(z,i),this.ias.once(G,(function(o){t.pageBreaks.push({pageIndex:o.pageIndex,url:e,title:n,sentinel:t.ias.sentinel()}),t.update(),t.ias.off(z,i)}))},vt.prototype.prev=function(){var t=this,e=""+document.location,n=document.title,i=function(t){e=t.url,t.xhr.response&&(n=t.xhr.response.title)};this.ias.once(z,i),this.ias.once(J,(function(o){t.pageBreaks.unshift({pageIndex:o.pageIndex,url:e,title:n,sentinel:t.ias.first()}),t.update(),t.ias.off(z,i)}))},vt.prototype.scrolled=function(t){this.update(t.scroll.y)},vt.prototype.update=function(t){this.currentScrollTop=t||this.currentScrollTop;var e=function(t,e,n){for(var i=e+_(n).height,o=t.length-1;o>=0;o--)if(i>t[o].sentinel.getBoundingClientRect().bottom+e)return t[Math.min(o+1,t.length-1)];return t[0]}(this.pageBreaks,this.currentScrollTop,this.ias.scrollContainer);e&&e.pageIndex!==this.currentPageIndex&&(this.ias.emitter.emit(tt,e),this.currentPageIndex=e.pageIndex)};var yt={element:void 0,when:function(t){return!0},show:function(t){t.style.opacity="1"},hide:function(t){t.style.opacity="0"}};var bt=function(t,n){var i=this;!1!==n&&(this.ias=t,this.options=c({},yt,function(t){if(("string"==typeof t||"function"==typeof t||"object"==typeof t&&t.nodeType===Node.ELEMENT_NODE)&&(t={element:t}),"function"==typeof t.element&&(t.element=t.element()),t.when&&Array.isArray(t.when)){var e=t.when;t.when=function(t){return-1!==e.indexOf(t)}}return t}(n)),void 0!==this.options.element&&P(this.options.element,"trigger.element"),this.element=e(this.options.element)[0],this.hideFn=this.options.hide,this.showFn=this.options.show,this.voter=this.options.when,this.showing=void 0,this.enabled=void 0,t.on(D,this.bind.bind(this)),t.on(k,this.unbind.bind(this)),t.on(A,this.hit.bind(this)),t.on(Y,(function(t){return i.ias.once(M,(function(){return i.update(t.pageIndex)}))})))};function xt(t,e,n){var i=n?n.nextSibling:null,o=document.createDocumentFragment();t.forEach((function(t){o.appendChild(t)})),e.insertBefore(o,i)}function wt(t,e,n){var i=document.createDocumentFragment();t.forEach((function(t){i.appendChild(t)})),e.insertBefore(i,n)}bt.prototype.bind=function(){this.hide(),this.update(this.ias.pageIndex),this.element.addEventListener("click",this.clickHandler.bind(this))},bt.prototype.unbind=function(){this.element.removeEventListener("click",this.clickHandler.bind(this))},bt.prototype.clickHandler=function(){this.hide().then(this.ias.next.bind(this.ias))},bt.prototype.update=function(t){this.enabled=this.voter(t),this.enabled?this.ias.disableLoadOnScroll():this.ias.enableLoadOnScroll()},bt.prototype.hit=function(){this.enabled&&this.show()},bt.prototype.show=function(){if(!this.showing)return this.showing=!0,Promise.resolve(this.showFn(this.element))},bt.prototype.hide=function(){if(this.showing||void 0===this.showing)return this.showing=!1,Promise.resolve(this.hideFn(this.element))};var Ot=window.ResizeObserver,It=function(t,e){this.el=t,this.listener=e};It.prototype.observe=function(){this.el.addEventListener("resize",this.listener)},It.prototype.unobserve=function(){this.el.removeEventListener("resize",this.listener)};var Et=function(t,e){this.el=t,this.listener=e,this.ro=new Ot(this.listener)};Et.prototype.observe=function(){this.ro.observe(this.el)},Et.prototype.unobserve=function(){this.ro.unobserve()};var St=function(t,e){this.el=t,this.listener=e,this.interval=null,this.lastHeight=null};St.prototype.pollHeight=function(){var t=Math.trunc(_(this.el).height);null!==this.lastHeight&&this.lastHeight!==t&&this.listener(),this.lastHeight=t},St.prototype.observe=function(){this.interval=setInterval(this.pollHeight.bind(this),200)},St.prototype.unobserve=function(){clearInterval(this.interval)};var Tt=function(t,e){this.ias=t,this.enabled=e};Tt.prototype.prefill=function(){var t=this;if(this.enabled)return this.ias.emitter.emit(et),Promise.all([this._prefillNext(),this._prefillPrev()]).then((function(){t.ias.emitter.emit(nt),t.ias.measure()}))},Tt.prototype._prefillNext=function(){var t=this;if(0>=this.ias.distance())return this.ias.next().then((function(e){if(e)return 0>t.ias.distance()?t._prefillNext():void 0}))},Tt.prototype._prefillPrev=function(){if(this.ias.options.prev)return this.ias.prev()};var Lt=function(t,n){var i,o,r,s=this;void 0===n&&(n={}),P(t,"container"),this.container=e(t)[0],this.options=c({},C,n),this.emitter=new at,this.options.loadOnScroll?this.enableLoadOnScroll():this.disableLoadOnScroll(),this.negativeMargin=Math.abs(this.options.negativeMargin),this.scrollContainer=this.options.scrollContainer,this.options.scrollContainer!==window&&(P(this.options.scrollContainer,"options.scrollContainer"),this.scrollContainer=e(this.options.scrollContainer)[0]),this.nextHandler=ht,this.prevHandler=ct,!1===this.options.next?this.nextHandler=function(){}:"function"==typeof this.options.next&&(this.nextHandler=this.options.next),!1===this.options.prev?this.prevHandler=function(){}:"function"==typeof this.options.prev&&(this.prevHandler=this.options.prev),this.resizeObserver=(i=this,o=this.scrollContainer,r=L(st,200).bind(i),o===window?new It(o,r):Ot?new Et(o,r):(console&&console.warn&&console.warn("ResizeObserver not supported. Falling back on polling."),new St(o,r))),this._scrollListener=L(rt,200).bind(this),this.ready=!1,this.bindOnReady=!0,this.binded=!1,this.paused=!1,this.pageIndexPrev=0,this.pageIndex=this.pageIndexNext=this.sentinel()?0:-1,this.on(A,(function(){s.loadOnScroll&&s.next()})),this.on(W,(function(){s.loadOnScroll&&s.prev()})),this.on(Q,this.measure),this.on(Z,this.measure),this.pagination=new ut(this,this.options.pagination),this.spinner=new ft(this,this.options.spinner),this.logger=new gt(this,this.options.logger),this.paging=new vt(this),this.trigger=new bt(this,this.options.trigger),this.prefill=new Tt(this,this.options.prefill),this.on(D,this.prefill.prefill.bind(this.prefill)),this.hitFirst=this.hitLast=!1,this.on(V,(function(){return s.hitLast=!0})),this.on($,(function(){return s.hitFirst=!0}));var l=function(){s.ready||(s.ready=!0,s.emitter.emit(K),s.bindOnReady&&s.options.bind&&s.bind())};"complete"===document.readyState||"interactive"===document.readyState?setTimeout(l,1):window.addEventListener("DOMContentLoaded",l)};return Lt.prototype.bind=function(){this.binded||(this.ready||(this.bindOnReady=!1),this.scrollContainer.addEventListener("scroll",this._scrollListener),this.resizeObserver.observe(),this.binded=!0,this.emitter.emit(D))},Lt.prototype.unbind=function(){this.binded?(this.resizeObserver.unobserve(),this.scrollContainer.removeEventListener("scroll",this._scrollListener),this.binded=!1,this.emitter.emit(k)):this.ready||this.once(D,this.unbind)},Lt.prototype.next=function(){var t=this;if(!this.hitLast){if(!this.binded)return this.ready?void 0:this.once(D,this.next);this.pause();var e=this.pageIndexNext+1;return this.emitter.emit(Y,{pageIndex:this.pageIndexNext+1}),Promise.resolve(this.nextHandler(e)).then((function(n){return t.pageIndexNext=e,n||t.emitter.emit(V),t.resume(),n})).then((function(e){return t.emitter.emit(G,{pageIndex:t.pageIndexNext}),e}))}},Lt.prototype.prev=function(){var t=this;if(this.binded&&!this.hitFirst){this.pause();var e=this.pageIndexPrev-1;return this.emitter.emit(U,{pageIndex:this.pageIndexPrev-1}),Promise.resolve(this.prevHandler(e)).then((function(n){return t.pageIndexPrev=e,t.resume(),n||t.emitter.emit($),n})).then((function(e){return t.emitter.emit(J,{pageIndex:t.pageIndexPrev}),e}))}},Lt.prototype.load=function(t){var n=this;return new Promise((function(i,o){var r=new XMLHttpRequest,s={url:t,xhr:r,method:"GET",body:null,nocache:!1,responseType:n.options.responseType,headers:{"X-Requested-With":"XMLHttpRequest"}};n.emitter.emit(q,s);var l=s.url,a=s.method,h=s.responseType,c=s.headers,p=s.body;for(var u in s.nocache||(l=l+(/\?/.test(l)?"&":"?")+(new Date).getTime()),r.onreadystatechange=function(){if(r.readyState===XMLHttpRequest.DONE)if(0===r.status);else if(200===r.status){var t=r.response;"document"===h&&(t=e(n.options.item,r.response)),n.emitter.emit(z,{items:t,url:l,xhr:r}),i({items:t,url:l,xhr:r})}else n.emitter.emit(X,{url:l,method:a,xhr:r}),o(r)},r.onerror=function(){n.emitter.emit(X,{url:l,method:a,xhr:r}),o(r)},r.open(a,l,!0),r.responseType=h,c)r.setRequestHeader(u,c[u]);r.send(p)}))},Lt.prototype.append=function(t,e){var n=this,i={items:t,parent:e=e||n.container,appendFn:xt};n.emitter.emit(H,i);return new Promise((function(o){window.requestAnimationFrame((function(){Promise.resolve(i.appendFn(i.items,i.parent,n.sentinel())).then((function(){o({items:t,parent:e})}))}))})).then((function(t){n.emitter.emit(M,t)}))},Lt.prototype.prepend=function(t,e){var n=this,i=this,o={items:t,parent:e=e||i.container,prependFn:wt};i.emitter.emit(R,o);return new Promise((function(r){window.requestAnimationFrame((function(){var s=i.first(),l=F(n.scrollContainer),a=s.getBoundingClientRect().top+l.y;Promise.resolve(o.prependFn(o.items,o.parent,i.first())).then((function(){var t=F(n.scrollContainer),e=s.getBoundingClientRect().top+t.y;n.scrollContainer.scrollTo(t.x,e-a)})).then((function(){r({items:t,parent:e})}))}))})).then((function(t){i.emitter.emit(B,t)}))},Lt.prototype.sentinel=function(){var t=e(this.options.item,this.container);return t.length?t[t.length-1]:null},Lt.prototype.first=function(){var t=e(this.options.item,this.container);return t.length?t[0]:null},Lt.prototype.pause=function(){this.paused=!0},Lt.prototype.resume=function(){this.paused=!1},Lt.prototype.enableLoadOnScroll=function(){this.loadOnScroll=!0},Lt.prototype.disableLoadOnScroll=function(){this.loadOnScroll=!1},Lt.prototype.distance=function(t,e){return this.distanceBottom(t,e)},Lt.prototype.distanceBottom=function(t,e){var n=t||_(this.scrollContainer),i=function(t,e,n){var i=n;if(!t)return-1*i.height;var o=e.y,r=t.getBoundingClientRect();return Math.trunc(o+r.bottom-i.top-(o+i.height))}(e||this.sentinel(),F(this.scrollContainer),n);return i-=this.negativeMargin},Lt.prototype.distanceTop=function(){return F(this.scrollContainer).y-this.negativeMargin},Lt.prototype.measure=function(){if(!(this.paused||this.hitFirst&&this.hitLast)){var t=_(this.scrollContainer);if(0!==t.height){if(!this.hitFirst){var e=this.distanceTop();e>0||this.emitter.emit(W,{distance:e})}if(!this.hitLast){var n=this.distanceBottom(t,this.sentinel());n>0||this.emitter.emit(A,{distance:n})}}}},Lt.prototype.on=function(t,e){this.emitter.on(t,e,this),t===D&&this.binded&&e.bind(this)()},Lt.prototype.off=function(t,e){this.emitter.off(t,e,this)},Lt.prototype.once=function(t,e){var n=this;return new Promise((function(i){n.emitter.once(t,(function(){Promise.resolve(e.apply(this,arguments)).then(i)}),n),t===D&&n.binded&&(e.bind(n)(),i())}))},Lt}));

/* FamilyJS, 1 Mar 2023 */
function setProperty(el, cssProperty, cssValue) {
    el.style[cssProperty] = cssValue;
}

function switchTo(el, displayValue) {
    var setValue = displayValue === 'show' ? 'block' : 'none';
    setProperty(el, 'display', setValue);
}

/* Menu toggle 27 Aug 2022 */
var toggle = document.querySelector('#toggle');
var menu = document.querySelector('#menu');

toggle.addEventListener('click', function () {
    menuToggle();
});

function menuToggle(value) {
    value = value ? value : menu.style.display === 'none' ? 'show' : 'hide';
    switchTo(menu, value);
}

/* Header toggle 30 Aug 2022 */
var header = document.querySelector('#header');
var triggerValue = 5, lastOffsetY = window.scrollY, headerHeight = header.clientHeight, isReady = !1;

function start() {
    // ready();
    setHeader('show');
    var itemEl = document.querySelectorAll('article') || '';
    itemEl && loadPrism(itemEl);
}

if (document.readyState === 'complete' || (document.readyState !== 'loading')) {
    start();
} else {
    // 初始的 HTML 文档被完全加载和解析完成之后，**DOMContentLoaded **事件被触发，无需等待样式表、图像和子框架的完全加载
    document.addEventListener('DOMContentLoaded', start);
}

function setHeader(value) {
    var setAnimation = 'top 200ms ', setValue;
    if (value === 'show') {
        setValue = '0px';
        setAnimation += 'linear';
    } else {
        setValue = '-' + headerHeight + 'px';
        setAnimation += 'ease-in';
    }
    var cssValue = 'top:' + setValue + ';transition:' + setAnimation;
    menuToggle('none');
    setProperty(header, 'cssText', cssValue);
}

function toggleHeader() {
    var offsetY = window.scrollY;
    var isHide = offsetY > lastOffsetY && offsetY > headerHeight / 2;
    var isShow = offsetY + window.innerHeight < document.body.offsetHeight;
    Math.abs(lastOffsetY - offsetY) > triggerValue && (isHide ? setHeader('hide') : isShow && setHeader('show'));
    lastOffsetY = offsetY;
}

// 修复 iOS Safari 环境下 focus 事件导致的菜单隐藏问题
var menuSearch = document.querySelector('#menu-search');
menuSearch.addEventListener('click', function () {
    isReady = !1;
});

// 延迟执行 toggle
window.addEventListener('scroll', function () {
    isReady ? toggleHeader() : isReady = !0;
});

// 加载 prism.js 文件
function loadPrism(itemArr) {
    for (var el of itemArr) {
        if (el.querySelector('pre code')) {
            var prismJS = document.querySelector('#prism'); // 找 id = 'prism' 的 js
            if (prismJS) {
                Prism.highlightAllUnder(el);
            } else {
                var group = 'basic', item = 'asset_uri',
                    reqObj = {group, item, url: '/api/v1/get_config'};
                callXHR(reqObj, prismCb);
				
                function prismCb(data) {
                    var res = JSON.parse(data.response);
					if (0 !== res['code']) {
						return;
					}
					var srcUrl = res['data'][item] + '/prism',
						prismCSS = document.createElement('link'), prismJS = document.createElement('script');
                    Object.assign(prismJS, {src: srcUrl + '.js', id: 'prism'});
                    Object.assign(prismCSS, {rel: 'stylesheet', href: srcUrl + '.css', media: 'all'});
                    document.body.append(prismJS);
                    document.head.append(prismCSS);
                }
                break; // 在当前加载的元素里找到第一个包含 pre code 的元素处理完即可跳出循环。
            }
        }
    }
}

/* infinite-ajax-scroll 11 Jun 2023 */
var isaEl = document.querySelector('.container');
if (isaEl) {
    var iasHtml = '<div class="ias-spinner more"><span class="animation"></span></div><div class="ias-trigger cur more"><a href="#">加载更多</a></div>';
    isaEl.insertAdjacentHTML('beforeend', iasHtml);

    var ias = new InfiniteAjaxScroll('.container', {
        item: '.item',
        pagination: '.pagination',
		next: '.next',
        spinner: {
            element: '.ias-spinner',
            delay: 600,
            show: function (el) {
                switchTo(el, 'show');
            },
            hide: function (el) {
                switchTo(el, 'hide');
            }
        },
        trigger: {
            element: '.ias-trigger',
            // 控制是否显示加载更多按钮, 当前的配置是自动加载前3页
            when: function (pageIndex) {
                return pageIndex > 2;
            },
            show: function (el) {
                switchTo(el, 'show');
            },
            hide: function (el) {
                switchTo(el, 'hide');
            }
        },
        // disable logger
        logger: false,
    });

    ias.on('last', function () {
        // 删除 container 元素下的含有 more 元素的节点, 并插入新节点(直接设置 className, 点击标签会报错)
        for (var el; el = document.querySelector('.container .more');) {
            el.remove();
        }
        iasHtml = '<div class="ias-noneleft more">已到结尾</div>';
        isaEl.insertAdjacentHTML('beforeend', iasHtml);
    });

    ias.on('load', function (event) {
        event.nocache = true;
        // 修复向下滚动到加载下一页,执行刷新触发自动加载,再向下滚动出现一次隐藏 header 的问题
        lastOffsetY = window.scrollY;
    });
    ias.on('appended', function (e) {
        loadPrism(e.items);
    });
	
	ias.on('page', (event) => {
		// update the title
		document.title = event.title;
		// update the url
		var state = history.state;
		history.replaceState(state, event.title, event.url);
	})

    // ias.on('next', function (event) {alert(`Page ${event.pageIndex+1} is loading...`)});
    // ias.on('error', function (event) {});
}

/* Like 28 Feb 2024 */
document.addEventListener('click', function (e) {
    for (var el = e.target; el && el !== document; el = el.parentNode) {
        if (el.matches('.sl-button')) {
            // 移除 sl-button 防止重复点
            el.classList.remove('sl-button');
            like(el);
            break;
        }
    }
});

function like(el) {
    var nonce = el.getAttribute('data-nonce'),
        postId = el.getAttribute('data-post-id'),
        commentId = el.getAttribute('data-iscomment'),
        likeClassName = '.sl-' + ('1' === commentId ? 'comment-' : '') + 'button-' + postId,
        likeElement = document.querySelector(likeClassName),
        animationElement = likeElement.nextSibling;

    // Loading animation
    animationElement.insertAdjacentHTML('beforeend', '<div class="loader">Loading...</div>');

    var likeObj = {like: 'Like', unlike: 'Unlike'};

    // 组装请求并调用接口
    var reqDate = {post_id: postId, nonce, is_comment: commentId, url: '/api/v1/post_like'};
    callXHR(reqDate, likeCb);

    function likeCb(data) {
        var res = JSON.parse(data.responseText),
			_data = res['data'];
			
        // delay remove animation
        setTimeout(removeAnimation, 50);

        function removeAnimation() {
            if (0 === res['code']) {
				var likeIcon = _data.icon, likeCount = _data.count;
				likeElement.innerHTML = likeIcon + likeCount;
				if ('unliked' === _data.status) {
					likeElement.setAttribute('title', likeObj.like);
					likeElement.classList.remove('liked');
				} else {
					likeElement.setAttribute('title', likeObj.unlike);
					likeElement.classList.add('liked');
				}
			}
			
            animationElement.firstChild.remove();
            // 加回 sl-button 以操作取消
            el.classList.add('sl-button');
        }
    }
}

/* 1 Mar 2023
 * 封装的调用 XMLHttpRequest 的方法
 * reqObj object
 * callback function 回调函数
 * return
**/

function callXHR(reqObj, callback) {
    var request = new XMLHttpRequest();
    var callUrl = reqObj['url'];
	delete reqObj.url;
    request.open('POST', callUrl, true); // false 同步, true 异步; 使用 false 动画会延迟显示
    request.setRequestHeader('Content-Type', 'application/json');
	request.setRequestHeader('X-WP-Nonce', _brave['nonce']);
    // Send request
	reqObj = JSON.stringify(reqObj);
    request.send(reqObj);

    request.onload = function () {
        var data = this;
        if (data.status === 200) {
            // Success!
            callback(data);
        } else {
            // 错误处理逻辑
        }
    };
    // return request; 使用同步方法需要返回 request
}