/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var lastItemId;
var msgArray = new Array();
var alter = false;
var filter;

var doSearch = function(){
    $("").replaceAll(".list-item");
    resetTail();
    var tmp = $.trim($("#front-search .text").val()).split(" ");
    for(var i = 0; i<tmp.length; i++) {
        tmp[i] = "%" + tmp[i] + "%";
    }
    appendItems(0, tmp);
    $("#list-tail").unbind();
    $("#list-tail").click(function(){
        appendItems(lastItemId, tmp);
    });
};

function appendItems(cursor, filter) {
    var jsonFilter = JSON.stringify(filter);
    $.post(
        "php/get.php",
        {action: "getItemsByCursor", cursor: cursor, filter: jsonFilter},
        function(data){
            for(itemIndex = 0; itemIndex<data.length; itemIndex++) {
                addItemToList(
                        alter, 
                        data[itemIndex]["item_id"], 
                        data[itemIndex]["user_id"], 
                        data[itemIndex]["item_message"], 
                        data[itemIndex]["item_updated_time"], 
                        data[itemIndex]["item_created_time"]
                        );
                alter = !alter;
            }
            if(data.length === 0) {
                $("#list-tail p").replaceWith("<p>All items are listed</p>");
            }
        },
        "json"
    );
}

function addItemToList(alter, itemId, attr1, attr2, arrt3, attr4) {
    if(alter) {
        var syntax = '<tr class="list-item alt"';
    } else {
        var syntax = '<tr class="list-item"';
    }
    syntax += ' id="'+itemId+'">'
            +'<td class="iattr1">'+attr1+'</td>'
            +'<td class="iattr2">'+shrinkMessage(attr2)+'</td>'
            +'<td class="iattr3">'+timestampToString(arrt3)+'</td>'
            +'<td class="iattr4">'+timestampToString(attr4)+'</td>'
            +'</tr>';
    $("table.widefat tbody").append(syntax);
    lastItemId = itemId;
    
    var url = 'https://www.facebook.com/'+lastItemId;
    $("#"+itemId).click(function(){
        window.open(url);
    });
    $("#"+itemId).on("shrinkMessage", function(){
        $("#"+itemId+" .iattr2").replaceWith('<td class="iattr2">'+shrinkMessage(attr2)+'</td>');
    });
    $("#"+itemId).hover(
            function(){
                $(".full-msg").trigger("shrinkMessage");
                $("#"+itemId+" .iattr2").replaceWith('<td class="iattr2 full-msg">'+attr2+'</td>');
            }, 
            function(){
                $("#"+itemId+" .iattr2").trigger("shrinkMessage");
            });
}

function timestampToString(timestamp) {
    var ONE_MIN = 1000*60;
    var ONE_HOUR = ONE_MIN*60;
    var ONE_DAY = ONE_HOUR*24;
    
    var date = new Date(timestamp*1000); 
    var now = new Date();
    var diff = now - date;
    
    var leftDays = Math.floor(diff/ONE_DAY);
    if(leftDays > 0) diff = diff - (leftDays * ONE_DAY);
    var leftHours = Math.floor(diff/ONE_HOUR);
    if(leftHours > 0) diff = diff - (leftHours * ONE_HOUR);
    var leftMins = Math.floor(diff/ONE_MIN);
    
    if(leftDays === 0) {
        if(leftHours === 0) {
            if(leftMins === 0) {
                return "few secs ago";
            } else {
                return leftMins+" mins ago";
            }
        } else {
            return leftHours+" hours ago";
        }
    } else {
        return date.toLocaleDateString()+" "+date.toLocaleTimeString();
    }
}

function shrinkMessage(message) {
    return message.substr(0, 25)+"...";
}

function resetTail() {
    $("#list-tail p").replaceWith("<p>Load More...</p>");
    $("#list-tail").unbind();
    $("#list-tail").click(function(){
        appendItems(lastItemId);
    });
}