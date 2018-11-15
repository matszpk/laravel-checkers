/**
 * 
 */

setArraySingle = function(arr, v)
{
    arr.splice(0, arr.length, v);
};

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
