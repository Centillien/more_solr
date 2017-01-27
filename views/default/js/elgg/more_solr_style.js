define(function(require) {
    var elgg = require('elgg');
    var $ = require('jquery');

    /**
     * Created by niels on 9/11/16.
     */
    var submit = document.getElementById('searchForm');
    var popup = document.getElementById('popup-module');
    var reqFields = document.getElementsByClassName('requiredFields');
    var spotter = 0;

    submit.onsubmit = function () {
        for (var i = 0; i < reqFields.length; i++) {
            if (reqFields[i].value) {
                spotter = 1
            }
        }
        if (spotter != 1) {
            popup.className = "elgg-module-popup hidden elgg-state-highlight";
            popup.style.display = 'none';
            elgg.register_error(elgg.echo('form:error:fields'));
            return false;
        }

        console.log($("#date").val() < $("#dateTo").val());

        if ($("#date").val() > $("#dateTo").val()) {
            popup.className = "elgg-module-popup hidden elgg-state-highlight";
            popup.style.display = 'none';
            elgg.register_error(elgg.echo('form:error:date'));
            return false;
        }
    };

    // style
    $("div.head").hover(function () {
        $(this).css("backgroundColor", "#d7d7d7");
    }, function () {
        $(this).css("backgroundColor", "#cecece");
    });

    var firstDivContent = document.getElementById('paginationFoot');
    var secondDivContent = document.getElementById('paginationHead');
    if (firstDivContent && secondDivContent) {
        secondDivContent.innerHTML = firstDivContent.innerHTML;
    }

    $("div.foot").hover(function (event) {
        var popup = event.currentTarget.childNodes[7];
        popup.style.top = event.clientY + 'px';
        popup.style.left = event.clientX + 'px';
        popup.style.position = 'fixed';
        popup.style.display = 'block';
    }, function (event) {
        var popup = event.currentTarget.childNodes[7];
        popup.style.display = 'none';
    });

    // Renew page upon sort change
    var sort = $("#sortDrop");
    sort.change(function () {
        var changeto = $("#sortDrop").val();
        var url = window.location.toString();
        window.location = url.replace(/sort=(.|\n)*?&/g, 'sort=' + changeto + '&');
    });

    if (!$("#advancedResults").find("li").length) {
        $("#noItems").css('display', 'block');
    }

    var users = $("#hiddenUsers").val();
    users = users.replace(/"/g, "");
    users = users.replace(/\[/g, "");
    users = users.replace(/]/g, "");
    users = users.split(",");
    $("#userAuto").autocomplete({
        source: users
    });
    var count = 0;

    $(".advancedPage, .currentPage").click(function () {
        var current = document.getElementById('currentPage');
        if (this.textContent == '<') {
            var prevPage = current.textContent - 1;
            console.log(prevPage);
            window.location.href = window.location.href.split("&page=")[0] + "&page=" + prevPage;
        } else if (this.textContent == '>') {
            var nextPage = parseInt(current.textContent, 10) + 1;
            console.log(nextPage);
            window.location.href = window.location.href.split("&page=")[0] + "&page=" + nextPage;
        } else {
            window.location.href = window.location.href.split("&page=")[0] + "&page=" + this.textContent;
        }
    });

    $(".resultItemLink").click(function () {
        elgg.register_error(elgg.echo('type:pageNotFound'));
    });

    var htmlCollection = document.getElementsByClassName("advancedItem");
    var items = Array.prototype.slice.call(htmlCollection);
    items.forEach(function (e) {
        var child = findClass(e, "one");
        if (child) {
            findClass(e, "info").textContent = child.textContent;
        }
    });

    function findClass(element, className) {
        var foundElement = null, found;

        function recurse(element, className, found) {
            for (var i = 0; i < element.childNodes.length && !found; i++) {
                var el = element.childNodes[i];
                var classes = el.className != undefined ? el.className.split(" ") : [];
                for (var j = 0, jl = classes.length; j < jl; j++) {
                    if (classes[j] == className) {
                        found = true;
                        foundElement = element.childNodes[i];
                        break;
                    }
                }
                if (found)
                    break;
                recurse(element.childNodes[i], className, found);
            }
        }

        recurse(element, className, false);
        return foundElement;
    }
});