/*image amount*/
var overview_amount = 36;

/*box format*/
var container_format = [[1,5,2,4,3,3], [3,4,3,1,2,5], [4,4,5,2,1]];
var box_content = [4,3,2,1,1];
var info_display = [];

/*image format*/
var img_defaultZ = 10;

$(document).ready(function(){ // hover
    $("#slide1").hover(function(){$("#drop1").slideDown("fast");});
    $("#slide1").mouseleave(function(){$("#drop1").slideUp("fast");});
    $("#slide2").mouseenter(function(){$("#drop2").slideDown("fast");});
    $("#slide2").mouseleave(function(){$("#drop2").slideUp("fast");});	
});

$(document).ready(function() {
	overview_page(0);	//overview page
	$(".fancybox").fancybox({
    	openEffect	: 'elastic',
    	closeEffect	: 'elastic',

    	helpers : {
    		title : {
    			type : 'inside'
    		}
    	}
    });
	
	
});

function overview_page(offset){
	$.ajax({
		type: "POST",
		url: "php/getinfo.php",
                dataType: "json",
		error: function(){
			alert('overview fault');
		},
		success: function(data){
			var amount = 0;
			var $overview = $("#overview");
			var tags = "", info_tags="";
			$overview.append("<ul>");
			
			var i, j ,k;
			for(i=0; i<container_format.length; i++){
				if(amount >= data.length)break;//exception
				//$overview.append('<li class="box-container">');
				tags += '<li class="box-container">';
				
				for(j=0; j<container_format[i].length; j++){
					if(amount >= data.length)break;//exception
					//$overview.append('<div class="box-0'+container_format[i][j]+'">');
					tags += '<div class="box-0'+container_format[i][j]+'">';
					
					for(k=0; k<box_content[container_format[i][j]-1]; k++){ 
						if(amount >= data.length)break;//exception
						
						//for(l=0; l<3; l++)
							tags += '<a class="fancybox" href="#info"><img class="shrink z" src="php/image.php?id='+data[amount]["image_id"]+'"></a>';
						
						amount++;
					}
					tags += '</div>';
					
				}
				
				tags += '</li>';
			}
			
			tags += '</ul>';
			$overview.append(tags);
		}
	});
}
/*img display*/
function sw(id, num){
	var $block = $(id+''+num), 
		$ad = $block.find('img');
 
	$ad.css({
		opacity: 0,
		zIndex: img_defaultZ - 1
	}).eq(0).css({
		opacity: 1,
		zIndex: img_defaultZ
	});
	
 }
 
function add_info_button(id, num){
	var $block = $(id+''+num), 
		$ad = $block.find('img');
	
	var str = '';
	for(var i=0;i<$ad.length;i++){
		str += '<a href="javascript: info_click(' + num + ',' + i + ');">' + (i + 1) + '</a>';
	}
	$block.append($('<div class="control">' + str + '</div>').css('zIndex', img_defaultZ + 1)).find('.control a').eq(0).addClass('on');

}

function info_click(num,index){
	var $block = $('#info'+num),
		$ad = $block.find('img'),
		$control = $block.find('.control a');
	
	$ad.eq(index).stop().fadeTo(1000, 1).css('zIndex', img_defaultZ).siblings('img').stop().fadeTo(500, 0).css('zIndex', img_defaultZ-1);
		// 讓 a 加上 .on
	$control.eq(index).addClass('on').siblings().removeClass('on');	
}