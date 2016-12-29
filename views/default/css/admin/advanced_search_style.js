/**
 * Created by niels on 9/11/16.
 */

// style
var popUser = document.getElementById('info');
$( "div.head" ).hover(function () {
    $( this ).css("backgroundColor", "#d7d7d7");
}, function () {
    $( this ).css("backgroundColor", "#cecece");
});

$( "div.foot" ).hover(function ( event ) {
    popUser.style.top = event.clientY+'px';
    popUser.style.left = event.clientX+'px';
    popUser.style.position = 'fixed';
    popUser.style.display = 'block';
}, function ( event ) {
    popUser.style.display = 'none';
});

// Renew page upon sort change
var sort = $( "#sortDrop" );
sort.val(' '); // Makes the Sort on option value empty so you can change back too
sort.change(function () {
    var changeto = $( "#sortDrop" ).val();
    var url = window.location.toString();
    console.log(url);
    console.log(changeto);
    window.location = url.replace(/sort=(.|\n)*?&/g, 'sort='+ changeto +'&');
});

if(!$( "#advancedResults").find("li" ).length) {
    $( "#noItems" ).css('display','block');
}

var users = JSON.parse($( ".hiddenUsers").val());
$("#userAuto").autocomplete({
    source: users
});
var count = 0;
$( "#openDateMenu" ).click(function ( event ) {
    if(count == 0){
        count++;
        $( "tr.dateSets" ).removeClass('hidden');
    } else {
        count = 0;
        $( "tr.dateSets" ).addClass('hidden');
    }
});
$( ".advancedPage" ).click(function ( event ) {
    console.log(this.textContent);
});