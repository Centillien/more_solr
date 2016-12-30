/**
 * Created by niels on 9/11/16.
 */

// style
var popUser = document.getElementsByClassName('info');
$( "div.head" ).hover(function () {
    $( this ).css("backgroundColor", "#d7d7d7");
}, function () {
    $( this ).css("backgroundColor", "#cecece");
});

$( "div.foot" ).hover(function ( event ) {
    var popup = event.currentTarget.childNodes[7];
    popup.style.top = event.clientY+'px';
    popup.style.left = event.clientX+'px';
    popup.style.position = 'fixed';
    popup.style.display = 'block';
}, function ( event ) {
    var popup = event.currentTarget.childNodes[7];
    popup.style.display = 'none';
});

// Renew page upon sort change
var sort = $( "#sortDrop" );
sort.change(function () {
    var changeto = $( "#sortDrop" ).val();
    var url = window.location.toString();
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

var htmlCollection = document.getElementsByClassName("advancedItem");
var items = Array.prototype.slice.call( htmlCollection );
items.forEach(function (e) {
    var child = findClass(e, "one");
    findClass(e, "info").textContent = child.textContent;
});

function findClass(element, className) {
    var foundElement = null, found;
    function recurse(element, className, found) {
        for (var i = 0; i < element.childNodes.length && !found; i++) {
            var el = element.childNodes[i];
            var classes = el.className != undefined? el.className.split(" ") : [];
            for (var j = 0, jl = classes.length; j < jl; j++) {
                if (classes[j] == className) {
                    found = true;
                    foundElement = element.childNodes[i];
                    break;
                }
            }
            if(found)
                break;
            recurse(element.childNodes[i], className, found);
        }
    }
    recurse(element, className, false);
    return foundElement;
}



