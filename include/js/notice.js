$(function(){
	function notice(){
		$('#notifs-button').show();
		$.ajax({
			url:window.location.href.indexOf("/admin") > -1 ? "../include/ajax/notice.php?"+ Math.random() : "include/ajax/notice.php?"+ Math.random(),
			dataType: 'html',
			success: function(data){
				if(data!=''){
					$('#notifs').html(data);
				}
			}
		});
	}
	function unread(){
		$.ajax({
			url:window.location.href.indexOf("/admin") > -1 ? "../include/ajax/notice.php?unread&"+ Math.random() : "include/ajax/notice.php?unread&"+ Math.random(),
			dataType: 'text',
			success: function(data){
				if(data>0){
					if($('#num').text()!=data){
						$('#num').addClass('new').text(data);
						notice();
					}
				}else{
					if($('#num').text()!=0){
						$('#num').removeClass('new').text(0);
						notice();
					}
				}
			}
		});
	}
	function read(){
		if($('#num').text()>0){
			$.ajax({
				url:window.location.href.indexOf("/admin") > -1 ? "../include/ajax/notice.php?read&"+ Math.random() : "include/ajax/notice.php?read&"+ Math.random()
			});
		}
	}

	$("#notifs-button").click(function(){
		$("#notifs").fadeToggle(300);
		$("#notifs").css({
			top: $('#notifs-button').offset().top + $('#notifs-button').outerHeight(),
			left: $('#notifs-button').offset().left + $('#notifs-button').outerWidth() - $('#notifs').outerWidth()
		});
		read();
	});

	setInterval(function(){
		unread();
	},3000);
	notice();
	unread();
	$('.content').before('<div id="notifs"></div>');
	$('#notifs-button').html('<a href="#">通知</a><span id="num">0</span>');
});