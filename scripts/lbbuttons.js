$(document).ready(function() {
    $('#killbutton').click(
        function(){
            $('#killresults').show();
            $('#deathresults').hide();
            $('#tkresults').hide();
        });

    $('#deathbutton').click(
        function(){
            $('#killresults').hide();
            $('#tkresults').hide();
            $('#deathresults').show();
        });

    $('#tkbutton').click(
        function(){
            $('#tkresults').show();
            $('#killresults').hide();
            $('#deathresults').hide();
        });
});
