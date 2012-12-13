// JavaScript Document
$(document).ready(function(){
	car();
	setInterval(car,9000);
	

	
	$("a#activities").mouseover(function(){
		//$("div#back").stop();
		$("a#activities").css("z-index","3");
		$("div#back").css("z-index","2");
		$("div#back").fadeTo("slow",0.7);
		$("a#spring").css("z-index","1");
		$("a#blog").css("z-index","1");
		$("div#activ_tip").fadeIn();
		});

	$("a#activities").mouseout(function(){
		//$("div#back").stop();
		$("a#activities").css("z-index","1");
		//$("div#back").animate({opacity:"0.0"},'slow');
		$("div#back").css("z-index","-1");
		$("a#spring").css("z-index","1");
		$("a#blog").css("z-index","1");
		$("div#back").css("opacity","0.0");
		$("div#activ_tip").fadeOut();
		});
	
	
	
		$("a#spring").mouseover(function(){
		//$("div#back").stop();
		$("a#spring").css("z-index","3");
		$("div#back").css("z-index","2");
		$("div#back").animate({opacity:"0.7"},'slow');
		$("a#activities").css("z-index","1");
		$("a#blog").css("z-index","1");
		$("div#spring_tip").fadeIn();
		});

	$("a#spring").mouseout(function(){
		//$("div#back").stop();
		$("a#spring").css("z-index","1");
		$("div#back").css("opacity","0.0");
		$("a#activities").css("z-index","1");
		$("a#blog").css("z-index","1");
		$("div#back").css("z-index","-1");
		$("div#spring_tip").fadeOut();
		});
		
		
		
		
		
		$("a#blog").mouseover(function(){
		//$("div#back").stop();
		$("a#blog").css("z-index","3");
		$("div#back").css("z-index","2");
		$("div#back").animate({opacity:"0.7"},'slow');
		$("a#spring").css("z-index","1");
		$("a#activities").css("z-index","1");
		$("div#blog_tip").fadeIn();
		});

	$("a#blog").mouseout(function(){
		//$("div#back").stop();
		$("a#blog").css("z-index","1");
		$("div#back").css("opacity","0.0");
		$("a#spring").css("z-index","1");
		$("a#activities").css("z-index","1");
		$("div#back").css("z-index","-1");
		$("div#blog_tip").fadeOut();
		});
});

function car(){
	$("a#black_car").animate({
		top:'573px',
		left:'1081px'
		},9000);
	$("a#black_car").animate({
		top:'35px',
		left:'10px'
		},0);
	
	$("a#pink_car").animate({
		top:'140px',
		left:'1240px'
		},9000);	
	$("a#pink_car").animate({
		top:'635px',
		left:'225px'
		},0);	
}
s