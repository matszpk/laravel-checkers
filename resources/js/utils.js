/**
 * 
 */

setArraySingle = function(arr, v)
{
    arr.splice(0, arr.length, v);
}

arrayToSetObject = function(arr)
{
    var out={};
    for(v in arr)
        out[arr[v]] = true;
    return out;
}

uniqueArray = function(arr)
{
    var out = [];
    var prev = null;
    for (var i = 0; i < arr.length; i++)
        if (prev !== arr[i])
        {
            out.push(arr[i]);
            prev = arr[i];
        }
    return out;
}

arrayEqual = function(arr1, arr2) {
    if (arr1 === arr2) return true;
    if (arr1 == null || arr2 == null)
        return false;
    if (arr1.length != arr2.length)
        return false;
    for (var i = 0; i < arr1.length; i++)
        if (arr1[i] !== arr2[i])
            return false;
    return true;
}

displayMessageTimeout = null;
// type - 'error', 'normal'
displayMessage = function(text, type) {
    if (type == null)
        type = 'normal';
    var msgElem = $("#checkers_message");
    if (type == 'error')
        msgElem.addClass("checkers_error_message");
    else
        msgElem.removeClass("checkers_error_message");
    msgElem.text(text);
    // clear previous timeout, before them
    if (displayMessageTimeout)
        clearTimeout(displayMessageTimeout);
    displayMessageTimeout = null;
    // main fade in and fade out
    msgElem.fadeIn(500, function() {
        displayMessageTimeout = setTimeout(function() {
            msgElem.fadeOut(500);
        }, 4000);
    });
}

checkersAxiosError = function(error, errorCallback) {
    if (error.response) {
        if (error.response.data && error.response.data.error != null)
            displayMessage(error.response.data.error, 'error');
        else
            displayMessage(errorTrans['httpError']+error.response.status+": "+
                    error.response.statusText, 'error');
    } else if (error.request) {
        displayMessage(errorTrans['noResponse'], 'error');
    } else {
        displayMessage(errorTrans['errorInApp'], 'error');
        console.log(error);
    }
    if (errorCallback!=null)
        errorCallback(error);
}

checkersAxiosGet = function(url, callback, errorCallback) {
    axios.get(url).then(callback).catch(function(error) {
        checkersAxiosError(error, errorCallback);
    });
}

checkersAxiosPost = function(url, input, callback, errorCallback) {
    axios.post(url, input).then(callback).catch(function(error) {
        checkersAxiosError(error, errorCallback);
    });
}
