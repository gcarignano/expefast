/* Exposing selected functions using Revealing Module Pattern */
window.__gatewayIFrame = (function () {

  /* URL Parsing function for cross-browser
     source - https://gist.github.com/acdcjunior/9820040 */
  function ParsedUrl(url) {
    var parser = document.createElement("a");
    parser.href = url;

    /* IE 8 and 9 dont load the attributes "protocol" and "host" in case the source URL
       is just a pathname, that is, "/example" and not "http://domain.com/example". */
    parser.href = parser.href;

    /* IE 7 and 6 wont load "protocol" and "host" even with the above workaround,
       so we take the protocol/host from window.location and place them manually */
    if (parser.host === "") {
      var newProtocolAndHost = window.location.protocol + "//" + window.location.host;
      if (url.charAt(1) === "/") {
        parser.href = newProtocolAndHost + url;
      } else {
        /* the regex gets everything up to the last "/"
           /path/takesEverythingUpToAndIncludingTheLastForwardSlash/thisIsIgnored
           "/" is inserted before because IE takes it of from pathname */
        var currentFolder = ("/" + parser.pathname).match(/.*\//)[0];
        parser.href = newProtocolAndHost + currentFolder + url;
      }
    }

    /* copies all the properties to this object */
    var properties = ['host', 'hostname', 'hash', 'href', 'port', 'protocol', 'search'];
    for (var i = 0, n = properties.length; i < n; i++) {
      this[properties[i]] = parser[properties[i]];
    }

    /* pathname is special because IE takes the "/" of the starting of pathname */
    this.pathname = (parser.pathname.charAt(0) !== "/" ? "/" : "") + parser.pathname;
  }
  /* URL Parsing function end */

  /* Unique string generation function
     source - https://stackoverflow.com/a/6860916/9971482 */
  function guidGenerator() {
    var S4 = function () {
      return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    };
    return (S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4());
  }

  function generateForm(target, payload, action) {
    var form = document.createElement('form');
    var inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'data';
    inp.value = payload;
    form.appendChild(inp);
    form.action = action;
    form.method = 'POST';
    form.target = target;
    return form;
  }

  function getErrorObj(msg) {
    throw new Error(msg);
  }

  function validateAction(action) {
    var actionStringValidity = validateString(action, 'action');
    if (actionStringValidity !== true) {
      return actionStringValidity;
    }

    action = new ParsedUrl(action);

    if (action.pathname.indexOf('/payment') < 0) {
      /* pathname doesn't contains /payment */
      return getErrorObj("Invalid pathname in action");
    }
    return true;
  }

  function validateString(str, strName) {
    if (!(typeof str == 'string'))
      return getErrorObj(strName + " must be a string");
    if (!str)
      return getErrorObj(strName + " can not be an empty string");
    return true;
  }

  function initGatewayForm(target, payload, action) {
    var form = generateForm(target, payload, action);
    form.style.display = 'none';
    document.body.appendChild(form);
    return form;
  }

  function publicFunction(payload, action, iFrameWrapperClass) {
    var actionValidity = validateAction(action);
    if (actionValidity !== true) {
      return actionValidity;
    }
    var payloadValidity = validateString(payload, 'payload');
    if (payloadValidity !== true) {
      return payloadValidity;
    }
    iFrameWrapperClass = iFrameWrapperClass && typeof iFrameWrapperClass == 'string' ?
      iFrameWrapperClass :
      'gateway-iframe-wrapper';
    var iFrameWrapper = document.querySelector('div.' + iFrameWrapperClass);
    if (!iFrameWrapper) {
      return getErrorObj("No wrapper found with class name " + iFrameWrapperClass);
    }
    var iFrameName = guidGenerator();
    var iFrame = document.createElement('iframe');
    iFrame.name = iFrameName;
    iFrame.id = 'gateway-iframe-item';
    iFrameWrapper.appendChild(iFrame);
    var form = initGatewayForm(iFrameName, payload, action);
    form.submit();
    form.remove();
    return true;
  }

  return {
    "initPaymentIFrame": publicFunction
  };
})();
