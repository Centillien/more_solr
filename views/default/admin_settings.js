/**
 * Created by niels on 5/12/16.
 */

//  Get the tables
var hideStpThem = document.getElementById("stopwordTable");
var hideSynThem = document.getElementById("synwordTable");

//  Input forms to add/change words
var newStop = document.getElementById("newStop");
var addStop = document.getElementById("addStop");
var newSyn = document.getElementById("newSyn");
var addSyn = document.getElementById("addSyn");

//  Buttons to open input forms to add/change words
var stpPhoto = document.getElementsByClassName("hideStpTable");
var synPhoto = document.getElementsByClassName("hideSynTable");

//  Add/edit buttons
var synAdd = document.getElementById("syn-add");
var synAddAddField = document.getElementById("syn-add-add");
var synEdd = document.getElementById("syn-edit");
var synEdAddField = document.getElementById("syn-add-edd");
var synDel = document.getElementsByClassName("synDelWord");
var stpAdd = document.getElementById("stp-add");
var stpEdd = document.getElementById("stp-edit");
var stpDel = document.getElementsByClassName("stpDelWord");

//  Back buttons
var backStp = document.getElementsByClassName("stp-back");
var backSyn = document.getElementsByClassName("syn-back");

//  Input fields
var synInp = document.getElementById("synEddInput");
var stpInp = document.getElementById("stpEddInput");

var oldSyn;
var oldStp;

//  Counter of synonyms, max amount of synonyms per input
var counterEd = 1;
var counter = 1;
var maxvalue = 5;

//  Show Edit || new of Synonyms || Stopwords
for (var i=0; i < stpPhoto.length; i++) {
    stpPhoto[i].onclick = function(obj){
        hideStpThem.style.display = 'none';
        switch(this.innerHTML){
            case 'New word':
                newStop.style.display = 'block';
                break;
            case 'Add':
                oldStp = obj.srcElement.closest('td').previousSibling.previousSibling.innerHTML;
                addStop.style.display = 'block';
                stpInp.value = oldStp;
                break;
        }
    }
}
for (i=0; i < synPhoto.length; i++) {
    synPhoto[i].onclick = function(obj){
        hideSynThem.style.display = 'none';
        switch(this.innerHTML){
            case 'New word':
                newSyn.style.display = 'block';
                break;
            case 'Add':
                oldSyn = "";
                oldSyn = obj.srcElement.closest('td').previousSibling.previousSibling.innerHTML;
                addSyn.style.display = 'block';
                oldSyn = oldSyn.split(",");
                oldSyn.forEach(function (item, index, arr) {
                    document.getElementsByName("eddSins["+counterEd+"]")[0].value = arr[index];
                    counterEd++;
                    eddField(counterEd, maxvalue);
                });
                break;
        }
    }
}

//  Back buttons for syn/stp add/edit
for (i=0; i < backStp.length; i++) {
    backStp[i].onclick = function () {
        hideStpThem.style.display = 'block';
        newStop.style.display = 'none';
        addStop.style.display = 'none';
    };
}
for (i=0; i < backSyn.length; i++) {
    backSyn[i].onclick = function () {
        var elements = document.getElementsByClassName('extraInput');
        var myControls = document.querySelectorAll("input[name^='eddSins[']");
        for (var i = 0; i < myControls.length; i++) {
            Array.prototype.forEach.call(elements, function (item, index, arr) {
                document.getElementById('eddInputList').removeChild(arr[index]);
                counterEd = 1;
            });
        }
        hideSynThem.style.display = 'block';
        newSyn.style.display = 'none';
        addSyn.style.display = 'none';
    };
}
//  Add/edit calls
//  Synonyms
synAdd.onclick = function () {
    var addWords = [];
    var myControls = document.querySelectorAll("input[name^='addSins[']");
    for (var i = 0; i < myControls.length; i++) {
        addWords.push(myControls[i].value);
    }
    elgg.action('action/word_handler', {
        data:   {   type: "synonym",
            method: "add",
            input: addWords
        },
        success: function (msg) {
            hideSynThem.style.display = 'block';
            newSyn.style.display = 'none';
        }
    });
};
synEdd.onclick = function () {
    var eddWords = [];
    var myControls = document.querySelectorAll("input[name^='eddSins[']");
    for (var i = 0; i < myControls.length; i++) {
        eddWords.push(myControls[i].value);
    }
    elgg.action('action/word_handler', {
        data:   {   type: "synonym",
            method: "edd",
            input: eddWords,
            old: oldSyn
        },
        success: function (msg) {
            hideSynThem.style.display = 'block';
            addSyn.style.display = 'none';
        }
    });
};
for(var i = 0; i < synDel.length; i++) {
    var del = synDel[i];
    del.onclick = function (obj) {
        var old = obj.srcElement.closest('td').previousSibling.previousSibling.innerHTML;
        elgg.action('action/word_handler', {
            data:   {   type: "synonym",
                method: "del",
                old:  old
            }
        });
    };
}

//  Stopwords
stpAdd.onclick = function () {
    elgg.action('action/word_handler', {
        data:   {   type: "stopword",
            method: "add",
            input: document.getElementsByName("params[stpAdd]")[0].value
        },
        success: function (msg) {
            hideStpThem.style.display = 'block';
            newStop.style.display = 'none';
        }
    });
};
stpEdd.onclick = function () {
    elgg.action('action/word_handler', {
        data:   {   type: "stopword",
            method: "edd",
            input: document.getElementById("stpEddInput").value,
            old: oldStp
        },
        success: function (msg) {
            hideStpThem.style.display = 'block';
            addStop.style.display = 'none';
        }
    });
};
for(var i = 0; i < stpDel.length; i++) {
    var del = stpDel[i];
    del.onclick = function (obj) {
        var old = obj.srcElement.closest('td').previousSibling.previousSibling.innerHTML;
        elgg.action('action/word_handler', {
            data:   {   type: "stopword",
                method: "del",
                old:  old
            }
        });
    };
}

synAddAddField.onclick = function () {
    counter++;
    addField(counter, maxvalue);
};
synEdAddField.onclick = function () {
    counterEd++;
    eddField(counterEd, maxvalue);
};

function addField (counter, maxvalue) {
    if(counter <= maxvalue){
        var newdiv = document.createElement('input');
        newdiv.setAttribute("type", "text");
        newdiv.setAttribute("name", "addSins["+counter+"]");
        newdiv.setAttribute("class", "elgg-input-thin elgg-input-text listInputs extraInput");
        document.getElementById('addInputList').appendChild(newdiv);
        document.getElementById('addcounter').innerHTML = counter + " / " + maxvalue;
    }
}

function eddField (counterEd, maxvalue) {
    if(counterEd <= maxvalue){
        var newdiv = document.createElement('input');
        newdiv.setAttribute("type", "text");
        newdiv.setAttribute("name", "eddSins["+counterEd+"]");
        newdiv.setAttribute("class", "elgg-input-thin elgg-input-text listInputs extraInput");
        document.getElementById('eddInputList').appendChild(newdiv);
        document.getElementById('eddcounter').innerHTML = counterEd + " / " + maxvalue;
    }
}