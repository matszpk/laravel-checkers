$(function() {

$(".checkers_comment_dolike").click(function(e) {
    var targetAttr = $.attr(e.target, 'id');
    var commentId = targetAttr.substring('.checkers_comment_dolike'.length);
    checkersAxiosPost("{{ url('/') }}/comment/"+commentId+'/like',
        function(response) {
            $('#checkers_comment_likes_'+commentId).text(response.data.likes);
        });
});

});
