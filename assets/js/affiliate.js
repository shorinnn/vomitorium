function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
    }
    return "";
}

function imacoach_loadExternalJS(TARGET_URL){
    var xhr = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : new XMLHttpRequest();
    xhr.open('GET', TARGET_URL, false);
    xhr.send(null);
    var code = xhr.responseText;
    var dScript = document.createElement('script');
    try {
        dScript.appendChild(document.createTextNode(code));
        document.body.appendChild(dScript);
    } 
    catch (e) {
        dScript.text = code;
        document.getElementsByTagName('head')[0].appendChild(dScript);
    }
    xhr = null;
}
var imacoach_QueryString = function () {
  // This function is anonymous, is executed immediately and 
  // the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    	// If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = pair[1];
    	// If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]], pair[1] ];
      query_string[pair[0]] = arr;
    	// If third or later entry with this name
    } else {
      query_string[pair[0]].push(pair[1]);
    }
  } 
    return query_string;
} ();

function imacoach_init(){
    if (typeof jQuery === 'undefined') {
      imacoach_loadExternalJS('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
    }
    $(function(){
        $('.imacoach-affiliate-update').each(function(){
            key = $(this).attr('data-affiliate-key');
            affiliate = imacoach_QueryString[key];
            if(affiliate!='' && typeof(affiliate) != 'undefined') setCookie('affiliate',affiliate, 90);
            if($.trim(getCookie('affiliate'))!='') affiliate = getCookie('affiliate');
            console.log(getCookie('affiliate'));
            if($.trim(getCookie('affiliate'))=='') return;
            if($(this).attr('href').indexOf('?')==-1){
                $(this).attr('href', $(this).attr('href')+'?a='+affiliate);
            }
            else{
                if($(this).attr('href').indexOf('a=')==-1){
                    $(this).attr('href', $(this).attr('href')+'&a='+affiliate);
                }
                else{
                    if($(this).attr('href').indexOf('?a=')==-1){
                        url = $(this).attr('href').split('&a=');
                        if(url[1].indexOf('&')==-1){
                            url[1] = affiliate;
                            url = url[0]+'&a='+url[1];
                            $(this).attr('href', url);
                        }
                        else{
                            console.log(url[1]);
                            subURL = url[1].split('&');
                            subURL[0] = affiliate;
                            url[1] = subURL.join('&');
                            url = url[0]+'&a='+url[1];
                            $(this).attr('href', url);
                        }
                    }
                    else{
                        url = $(this).attr('href').split('?a=');
                        subURL = url[1].split('&');
                        subURL[0] = affiliate;
                        url[1] = subURL.join('&');
                        url = url[0]+'?a='+url[1];
                        $(this).attr('href', url);
                    }
                }
            }
        });
    });
}
imacoach_init();