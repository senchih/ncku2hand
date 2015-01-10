$(document).ready(function(){
	$("#slide1").hover(function(){
		$("#drop1").slideDown("fast");
			});

	$("#slide1").mouseleave(function(){
		 $("#drop1").slideUp("fast");
			});
	
	$("#slide2").mouseenter(function(){
		$("#drop2").slideDown("fast");
			});

	$("#slide2").mouseleave(function(){
		 $("#drop2").slideUp("fast");
			});
	
});
