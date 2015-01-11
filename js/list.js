/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    getItems();
});

function getItems() {
    $.ajax({
        type: "GET",
        url: "php/get.php",
        dataType: "json",
        data: {action: "getItemsByCursor"},
        error: function(){
            alert('list fault');
        },
        success: function(data){
            var alter = false;
            for(itemIndex = 0; itemIndex<data.length; itemIndex++) {
                addItemToList(alter, 
                data[itemIndex]["item_id"], 
                data[itemIndex]["item_id"], 
                data[itemIndex]["user_id"], 
                timestampToString(data[itemIndex]["item_updated_time"]), 
                timestampToString(data[itemIndex]["item_created_time"])
                        );
                alter = !alter;
            }
        }
    });
}

function addItemToList(alter, itemId, attr1, attr2, arrt3, attr4) {
    if(alter) {
        var syntax = '<tr class="alt"';
    } else {
        var syntax = '<tr';
    }
    syntax += ' id="'+itemId+'"><td class="iattr1">'+attr1+'</td>'
            +'<td class="iattr2">'+attr2+'</td>'
            +'<td class="iattr3">'+arrt3+'</td>'
            +'<td class="iattr4">'+attr4+'</td>'
            +'</tr>';
    $("table.widefat tbody").append(syntax);
    var tmp = itemId.split("_");
    $("#"+itemId).click(function(){
        window.location.href='https://www.facebook.com/'+tmp[0]+'/posts/'+tmp[1];
    });
}

function timestampToString(timestamp) {
    var d = new Date(timestamp*1000);
    return d.toLocaleDateString()+" "+d.toLocaleTimeString();
}