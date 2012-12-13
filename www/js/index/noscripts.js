// JavaScript Document
$(document).ready(function(){
	$("a#activities").mouseover(function(){
		$("div#back").stop();
		$("a#activities").css("z-index","3");
		$("div#back").css("z-index","2");
		$("div#back").animate({opacity:"0.7"},'slow');
		$("a#spring").css("z-index","1");
		$("a#blog").css("z-index","1");
		});

	$("a#activities").mouseout(function(){
		$("div#back").stop();
		$("a#activities").css("z-index","1");
		$("div#back").animate({opacity:"0.0"},'slow');
		$("div#back").css("z-index","-1");
		$("a#spring").css("z-index","1");
		$("a#blog").css("z-index","1");
		
		
		});
		
		
		$("a#spring").mouseover(function(){
		$("div#back").stop();
		$("a#spring").css("z-index","3");
		$("div#back").css("z-index","2");
		$("div#back").animate({opacity:"0.7"},'slow');
		$("a#activities").css("z-index","1");
		$("a#blog").css("z-index","1");
		});

	$("a#spring").mouseout(function(){
		$("div#back").stop();
		$("a#spring").css("z-index","1");
		$("div#back").animate({opacity:"0.0"},'slow');
		$("a#activities").css("z-index","1");
		$("a#blog").css("z-index","1");
		$("div#back").css("z-index","-1");
		
		});
		
		
		
		
		
		$("a#blog").mouseover(function(){
		$("div#back").stop();
		$("a#blog").css("z-index","3");
		$("div#back").css("z-index","2");
		$("div#back").animate({opacity:"0.7"},'slow');
		$("a#spring").css("z-index","1");
		$("a#activities").css("z-index","1");
		});

	$("a#blog").mouseout(function(){
		$("div#back").stop();
		$("a#blog").css("z-index","1");
		$("div#back").animate({opacity:"0.0"},'slow');
		$("a#spring").css("z-index","1");
		$("a#activities").css("z-index","1");
		$("div#back").css("z-index","-1");
		
		});
});