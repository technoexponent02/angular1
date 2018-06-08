/** Register function
 * to get the Min value from an Array.
 */
Array.min = function (array) {
    return Math.min.apply(Math, array);
};
/**
 * Replace all occurrences in a string.
 */
String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};
/**
 * Replace all occurrences in a string by regex.
 */
String.prototype.regReplaceAll = function (search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

/**
 * Convert new lines to break tag.
 *
 * @param str
 * @returns {*}
 */
function nl2br (str) {
    // Some latest browsers when str is null return and unexpected null value
    if (typeof str === 'undefined' || str === null) {
        return ''
    }

    return (str + '').replace(/(\r\n|\n\r|\r|\n)/g, '<br>' + '$1');
}


/**
 *
 * @param url
 * @returns {string}
 */
function formatSourceUrl(url) {
    url = _.trimStart(url, '//');
    if (!/^(?:f|ht)tps?\:\/\//.test(url)) {
        url = "http://" + url;
    }
    return url;
}

/**
 * Get domain name from full url.
 *
 * @author Tuhin Subhra Mandal <tuhin.tsm.mandal@gmail.com>
 * @param  {[string]} url [full url]
 * @return {[string]}     [evaluated domain name]
 */
function getDomain(url) {
    // Replcace https and add http:// prefix if not present..
    url = url.replace(/^https:\/\//i, 'http://');
    var prefix = 'http://';
    if (url.substr(0, prefix.length) !== prefix) {
        url = prefix + url;
    }
    // get host name from URL..
    var hostname = url.toString().replace(/^(.*\/\/[^\/?#]*).*$/, "$1");
    var hostParts = hostname.split(".");
    var length = hostParts.length;
    var domain = hostParts[length - 2] + '.' + hostParts[length - 1];
    return domain.replace(/^http.*:\/\//i, '');
}

/**
 * Check whether valid url..
 *
 * @author Tuhin Subhra Mandal <tuhin.tsm.mandal@gmail.com>
 * @param  {[string]}  url
 * @return {Boolean}
 */
function isUrl(url) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
    return regexp.test(url);
}

/**
 * @author Tuhin Subhra Mandal <tuhin.tsm.mandal@gmail.com>
 * @param  {[string]} url
 * @return {[promise]}
 */
function getImage(url){
    return new Promise(function(resolve, reject){
        var img = new Image();
        img.onload = function(){
            resolve(url);
        };
        img.onerror = function(){
            reject(url);
        };
        img.src = url;
    })
}

/**
 * Check whether valid image url..
 *
 * @author Tuhin Subhra Mandal <tuhin.tsm.mandal@gmail.com>
 * @param  {[string]} url
 * @return {[type]}
 */
function checkImageUrl(url) {
    return (url.match(/\.(jpeg|jpg|gif|png|bmp|JPEG|JPG|GIF|PNG|BMP)$/) != null);
}

/**
 * Extract information from image url.
 * @author Tuhin Subhra Mandal <tuhin.tsm.mandal@gmail.com>
 * @param url
 * @returns {{valid: boolean, url: string, valid: boolean}}
 */
function getImageUrlInfo(url) {
    var formattedUrl = formatSourceUrl(url);
    var info = {
        'valid': false,
        'url': formattedUrl
    };
    if (!url) {
        return info;
    }
    // Check for https.
    info.secure = url.indexOf('https://') == 0;
    info.valid = url.match(/\.(jpeg|jpg|gif|png|bmp|JPEG|JPG|GIF|PNG|BMP)$/) != null;

    return info;
}

/**
 * Get embed video url from url..
 *
 * @author Tuhin Subhra Mandal <tuhin.tsm.mandal@gmail.com>
 * @param url  [url or iframe html code]
 * @returns {{status: string, url: string, useUserInput: boolean}}  [information about the video]
 */
function getEmbedUrlInfo(url) {
    var useUserInput = false;
    var info = {
        'status': 'failed',
        'url': '',
        'useUserInput': useUserInput
    };
    if (!url) {
        return info;
    }
    // List of supported video platform.
    var supportedList = ['youtube', 'vimeo', 'dailymotion'];
    // List of platform for which we will use user input as link to query api.
    var userInputList = ['dailymotion', 'giphy'];

    var embedUrl = '';
    // Check for iframe
    var iframePattern = /<iframe.*src="([^"]+)".*<\/iframe>/g;
    var match = iframePattern.exec(url);
    if (match) {
        embedUrl = match[1];
        var status = 'unsupported';
        /*if ($.inArray(embedUrl, supportedList)) {
         status = 'supported';
         }*/
        // Check if iframe is on supported list.
        supportedList.forEach(function (value, index) {
            if (embedUrl.indexOf(value) != -1) {
                status = 'supported';
            }
        });
        // Check whether to use user input as api link param.

        userInputList.forEach(function (value) {
            if (embedUrl.indexOf(value) != -1) {
                useUserInput = true;
            }
        });

        info = {
            'type': 'iframe',
            'status': status,
            'url': embedUrl,
            'useUserInput': useUserInput
        };
        // For dailymotion iframe.
        if (embedUrl.indexOf('dailymotion') != -1) {
            var dmEmbedPattern = /(?:dailymotion\.com(?:\/embed\/video))\/([0-9a-z]+)(?:[\-_0-9a-zA-Z]+#video=([a-z0-9]+))?/g;
            if (embedUrl.match(dmEmbedPattern)) {
                info.linkParam = 'https://www.dailymotion.com/video/' + RegExp.$1;
                info.url = '//www.dailymotion.com/embed/video/' + RegExp.$1;
            }
            else {
                info.status = 'failed';
            }
        }
        /*console.log('iframe');
        console.log(info);*/
        return info;
    }
    // Regex pattern for video resource websites..
    // var youtubePattern = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
    var youtubePattern = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    var vimeoPattern = /(?:http?s?:\/\/)?(?:www\.)?(?:vimeo\.com)\/?(.+)/g;
    var vinePattern = /(?:http?s?:\/\/)?(?:www\.)?(?:vine\.co\/v\/)\/?([^\/]+)/g;
    var dailymotionPattern = /(?:dailymotion\.com(?:\/video|\/hub)|dai\.ly)\/([0-9a-z]+)(?:[\-_0-9a-zA-Z]+#video=([a-z0-9]+))?/g;

    // For youtube.
    if (url.match(youtubePattern)) {
        embedUrl = 'https://www.youtube.com/embed/' + RegExp.$1;
        info = {
            'type': 'youtube',
            'status': 'supported',
            'url': embedUrl
        };
    }
    /*if(url.indexOf('vine') == -1 &&youtubePattern.test(url)) {
     var replacement = 'https://www.youtube.com/embed/$2';
     embedUrl = url.replace(youtubePattern, replacement);
     info = {
     'type': 'youtube',
     'status': 'supported',
     'url': embedUrl
     };
     }*/
    // For vimeo.
    else if (vimeoPattern.test(url)) {
        var replacement = 'https://player.vimeo.com/video/$1';
        embedUrl = url.replace(vimeoPattern, replacement);
        info = {
            'type': 'vimeo',
            'status': 'supported',
            'url': embedUrl
        };
    }
    // For dailymotion.
    else if (url.match(dailymotionPattern)) {
        /*var replacement = '//www.dailymotion.com/embed/video/$1';
         embedUrl = url.replace(dailymotionPattern, replacement).replace(/https?:\/\/(www\.)?/, '');*/
        embedUrl = '//www.dailymotion.com/embed/video/' + RegExp.$1;
        info = {
            'type': 'dailymotion',
            'status': 'supported',
            'url': embedUrl
        };
    }
    else if (url.match(vinePattern)) {
        // https://vine.co/v/5HVWqE3nagn/embed/simple
        embedUrl = 'https://vine.co/v/' + RegExp.$1 + '/embed/simple';
        info = {
            'type': 'vine',
            'status': 'unsupported',
            'url': embedUrl,
            'linkParam': 'https://vine.co/v/' + RegExp.$1
        };
    }
    // Check whether to use user input as api link param.
    userInputList.forEach(function (value, index) {
        if (embedUrl.indexOf(value) != -1) {
            useUserInput = true;
        }
    });
    info.useUserInput = useUserInput;
    // console.log(info);
    return info;
}

// Function to extraxt domain from url..
function extractDomain(url) {
    var domain;
    //find & remove protocol (http, ftp, etc.) and get domain
    if (url.indexOf("://") > -1) {
        domain = url.split('/')[2];
    }
    else {
        domain = url.split('/')[0];
    }
    //find & remove port number
    domain = domain.split(':')[0];
    return domain;
}

// parse a date in yyyy-mm-dd format
function parseDate(input) {
    var parts = input.match(/(\d+)/g);
    // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
    return new Date(parts[0], parts[1] - 1, parts[2]); // months are 0-based
}

// Speed up calls to hasOwnProperty
var hasOwnProperty = Object.prototype.hasOwnProperty;

function isEmpty(obj) {
    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}