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
