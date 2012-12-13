// JavaScript Document

//top
	$(window).scroll(function(){//注册滑动条滑动时的动作
 
//滚动条到顶部的距离
var scrTop = $(window).scrollTop();
 
//回到顶部按钮距离窗口右侧的距离，
//.midfix 是我页面最外层div
//这句比较难理解，用到了三元运算，不懂的百度下。
var myWidth = ($(window).width() > $("#wrapper").width()) ?(($(window).width() - $("#wrapper").width())/2 - 80):0; 
 
//窗口高度
var windowTop = $(window).height();
 
if ((windowTop-300)<scrTop){
//滚动高度大于一页
$(".goTop").css("top",(scrTop + windowTop -100)).css("right",myWidth).fadeIn("slow");
}else{
//滚动高度小于一页
$(".goTop").css("top",(scrTop + windowTop -100)).css("right",myWidth).fadeOut("slow");
}
});
 
//按钮被点击后，滑动到顶部。
$('#goToTop').click(function(){$('html,body').animate({scrollTop: '0px'}, 800);});

$("#like").click(function(){
	var hidden_str = $("#hidden_photo_id").html();
	var str_arr = hidden_str.split("|");
	var photo_id = str_arr[1];
	var ajax_url = str_arr[0];
	var like_num = str_arr[2];
	$.getJSON(ajax_url+'?photo_id='+photo_id, function(data){
		if(data.result){
			$("#like").html("喜欢("+(parseInt(like_num)+1)+")");	
		}else{
			alert(data.error);
		}
	});

});

$("#add_friends").click(function(){
		var hidden_str = $("#hidden_photo_id").html();
		var str_arr = hidden_str.split("|");
		var user_id = str_arr[3];
		$.ajax({
			type: "GET",
			url: "/member/add_friends.html",
			data: "friend_id="+user_id,
			success: function(msg){
				alert(msg );
			}
		});
});

$("#index_more").click(function(){
	var hidden_str = $("#hidden_photo_num").html();
	var str_arr = hidden_str.split("|");
	var photo_star_num = str_arr[0];
	var photo_type = str_arr[1];
	var ajax_url = str_arr[2];
	$.getJSON(ajax_url+'?photo_star_num='+photo_star_num+'&photo_type='+photo_type, function(data){
		$("#main").append(data.html);
		if(!data.more_photos){
			$("#index_more").css('display','none');
			$("#hidden_photo_num").html();
		}
		str_arr=new Array(data.photo_star_num,photo_type,ajax_url);
		hidden_str=str_arr.join("|");
		$("#hidden_photo_num").html(hidden_str);
		return false;
	});
});


$("#myUpload").click(function(){
						 bigDivShow();  
						setTimeout(function(){$("#upload_box").fadeIn();},500)});
						$(".close").click(function(){$("#upload_box").fadeOut();
						setTimeout(function(){
											$("#bigDiv").css("display","none");},500);
						});
	
$("#nav_type_but").hover(function(){
							  	$("#nav_type").fadeIn(1000);
							  }, function(){
								  $("#nav_type").fadeOut(0);
								  })
$("#nav_type").hover(function(){
							  	$("#nav_type").fadeIn(0);
							  }, function(){
								  $("#nav_type").fadeOut();
								  });
$("#upload_type_but").hover(function(){
							  	$("#upload_type").fadeIn(1000);
							  }, function(){
								  $("#upload_type").fadeOut(0);
								  })
$("#upload_type").hover(function(){
							  	$("#upload_type").fadeIn(0);
							  }, function(){
								  $("#upload_type").fadeOut();
								  })
$("#upload_type li").click(function(){
										var liHtml=$(this).html()
										$("#upload_type_but").html(liHtml);
									})
$("#imgSrc").change(function(){
							 	if($("#imgSrc").val()==""){
									$("#upload_detail_img").html("图片预览");
									}else{
										var innerImg=$("<img />");
										var fileVal=$("#imgSrc").val();
										innerImg.attr( "src",fileVal );
										$("#upload_detail_img").empty().append(innerImg);
									}
							 })
function bigDivShow(){
	var sHeight;
						   if(window.screen.availHeight > document.body.scrollHeight){
							   //当高度少于一屏
							   sHeight = window.screen.availHeight-140;
							   }else{
								   //当高度大于一屏
								   sHeight = document.body.scrollHeight+15;
								   }
						$("#bigDiv").css("height",sHeight+"px");
						$("#bigDiv").css("display","block");
	};

	$("#jiang").click(function(){
						 bigDivShow();  
						setTimeout(function(){$("#active").fadeIn();},500)});
						$(".close").click(function(){$("#active").fadeOut();
						setTimeout(function(){
											$("#bigDiv").css("display","none");},500);
								   });
$(document).ready(function() {
	$("#ture_img").mousemove(function(e){  
		 var positionX=e.originalEvent.x||e.originalEvent.layerX||0;
		 if(positionX<=$(this).width()/2){           
				this.style.cursor='url("/images/photo/pre.cur"),auto';
				$(this).attr('title','点击查看上一张');
				$(this).parent().attr('href',$(this).attr('left')); 
		  }else{  
				this.style.cursor='url("/images/photo/next.cur"),auto';
				$(this).attr('title','点击查看下一张');
				$(this).parent().attr('href',$(this).attr('right'));
		  } 
	});
	$("#ture_img").click( function(e){
		 var positionX=e.originalEvent.x||e.originalEvent.layerX||0;
		 var hidden_str = $("#hidden_photo_id").html();
		 var str_arr = hidden_str.split("|");
		 var photo_id = str_arr[1];
		 if(positionX<=$(this).width()/2){           
			location.href="/photo/photo/"+photo_id+".html?action=0";
		 }else{  
			location.href="/photo/photo/"+photo_id+".html?action=1";
		 } 
	});
	$('#my_app').hover(function(){
			$('#my_app ul').removeClass('none').addClass('block');
		},function(){
			$('#my_app ul').removeClass('block').addClass('none');
		});
});
