$(function() {

$(".checkers_comment_dolike").click(function(e) {
    var targetAttr = $.attr(e.target, 'id');
    var commentId = targetAttr.substring('.checkers_comment_dolike'.length);
    axios.post("{{ url('/') }}/comment/"+commentId+'/like')
                .then(function(response)
        {
            $('#checkers_comment_likes_'+commentId).text(response.data.likes);
        });
});

});
