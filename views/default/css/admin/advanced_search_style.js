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
$( "#openDateMenu" ).click(function () {
    if(count == 0){
        count++;
        $( "tr.dateSets" ).removeClass('hidden');
    } else {
        count = 0;
        $( "tr.dateSets" ).addClass('hidden');
    }
});
$( ".advancedPage" ).click(function () {
    pagination(this.textContent);
});

//  Make functions to add/remove hidden classes depending on their id

function pagination (page){
    var maxpp = 10;

    var i;
    var pageItems;
    var item;

    //  Removes old items
    for(i=0;i<10;i++){
        var htmlCollection = document.getElementsByClassName("advancedItem");
        var items = Array.prototype.slice.call( htmlCollection );
        items.forEach(function (e) {
            if(e.classList == "advancedItem"){
                e.classList = "advancedItem hidden";
            }
        });
    }

    //  Adds new items
    for(i=0;i<10;i++){
        pageItems = i + 1 + ((page - 1) * maxpp);
        item = [document.getElementById(pageItems.toString())];
        item.forEach(function (e) {
            if(e != null) {
                e.classList = "advancedItem";
            }
        });
    }
}