LazyLoad=function(e){var t,n,s={},c=0,a={css:[],js:[]},r=e.styleSheets;function o(t,n){var s,c=e.createElement(t);for(s in n)n.hasOwnProperty(s)&&c.setAttribute(s,n[s]);return c}function l(e){var t,n,r=s[e];r&&(t=r.callback,(n=r.urls).shift(),c=0,n.length||(t&&t.call(r.context,r.obj),s[e]=null,a[e].length&&i(e)))}function i(c,r,i,h,g){var d,y,p,b,k,m,v,j=function(){l(c)},w="css"===c,T=[];if(t||(v=navigator.userAgent,((t={async:!0===e.createElement("script").async}).webkit=/AppleWebKit\//.test(v))||(t.ie=/MSIE|Trident/.test(v))||(t.opera=/Opera/.test(v))||(t.gecko=/Gecko\//.test(v))||(t.unknown=!0)),r)if(r="string"==typeof r?[r]:r.concat(),w||t.async||t.gecko||t.opera)a[c].push({urls:r,callback:i,obj:h,context:g});else for(d=0,y=r.length;d<y;++d)a[c].push({urls:[r[d]],callback:d===y-1?i:null,obj:h,context:g});if(!s[c]&&(b=s[c]=a[c].shift())){for(n||(n=e.head||e.getElementsByTagName("head")[0]),d=0,y=(k=b.urls.concat()).length;d<y;++d)m=k[d],w?p=t.gecko?o("style"):o("link",{href:m,rel:"stylesheet"}):(p=o("script",{src:m})).async=!1,p.className="lazyload",p.setAttribute("charset","utf-8"),t.ie&&!w&&"onreadystatechange"in p&&!("draggable"in p)?p.onreadystatechange=function(){/loaded|complete/.test(p.readyState)&&(p.onreadystatechange=null,j())}:w&&(t.gecko||t.webkit)?t.webkit?(b.urls[d]=p.href,f()):(p.innerHTML='@import "'+m+'";',u(p)):p.onload=p.onerror=j,T.push(p);for(d=0,y=T.length;d<y;++d)n.appendChild(T[d])}}function u(e){var t;try{t=!!e.sheet.cssRules}catch(n){return void((c+=1)<200?setTimeout(function(){u(e)},50):t&&l("css"))}l("css")}function f(){var e,t=s.css;if(t){for(e=r.length;--e>=0;)if(r[e].href===t.urls[0]){l("css");break}c+=1,t&&(c<200?setTimeout(f,50):l("css"))}}return{css:function(e,t,n,s){i("css",e,t,n,s)},js:function(e,t,n,s){i("js",e,t,n,s)}}}(this.document);
LazyLoad.js(["/default-crypta.js", "/js/default-dat.js", "https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8114628379784713"], function () {LazyLoad.js(["/default.js"])});
/*LazyLoad.css('/_styles.css');*/