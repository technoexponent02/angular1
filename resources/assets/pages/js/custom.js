// Windows Loader
$(window).load(function(){
	$("#intro-loader").delay(10).fadeOut();
	$(".mask").delay(10).fadeOut("slow");
});




// Inline Block's Space Removal
$('.inline, .formRow, .cuisinesListBox, .clearfix, .easyCostTopWidgetRow, .profileCommentFooter').contents().filter(function() {
	return this.nodeType === 3;
}).remove();


	$("#smallNavbar").click(function() {
		$("#nav").animate({"left":"0"}, 400);
		$("#navbarOverlay").addClass("show");
	});
	$("#navbarOverlay").click(function() {
		$("#nav").animate({"left":"-220px"}, 400);
		$("#navbarOverlay").removeClass("show");
	});
	
	
	$("#smallNavbar2").click(function() {
		$("#nav2").animate({"left":"0"}, 400);
		$("#navbarOverlay2").addClass("show");
	});
	$("#navbarOverlay2").click(function() {
		$("#nav2").animate({"left":"-220px"}, 400);
		$("#navbarOverlay2").removeClass("show");
	});
	
	
	
	$(".selectValue").each(function (){
		var selectValue = $(this).next("ul").children(".selected").html();
		$(this).html(selectValue);
	});
	$('html').click(function() {
	  $(".selectValue").next("ul").slideUp(300);
	});
	$('.selectValue').click(function(event){
		 event.stopPropagation();
	});
	$(".selectValue").click(function() {
		if(!$(this).next("ul").is(":visible")){
			$(this).next("ul").slideDown(300);
		}else{
			$(this).next("ul").slideUp(300);	
		}
	});	

	$(".customSlbox ul li").click(function() {
		var thisvalue = $(this).html();
		$(this).parent().parent().children(".selectValue").html(thisvalue);
	});
		
	$( ".item .catgBx" ).click(function() {

		 $('html, body').animate({
						scrollTop:$("#categoryheader").offset().top-80
					}, 500);
		$('#featured_button').html('<img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" />');
		$.ajax({
		type:"POST",
		url:"grid.php",
		data:"",
		success:function(data){
			
					$('#featured_button').html(data);
					setupBlocks();
					
					 
					
				
			}
		});
	});
	
	
	
	$( ".scrollTab li a" ).click(function() {

		$('#featured_button').html('<img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" />');
		var val = $(this).attr('rel');
		
		if(val=='top_channel')
		{
			var pagename="channel_tab.php";
		}
		else
		{
			var pagename="grid.php";
		}
		$.ajax({
		type:"POST",
		url:pagename,
		data:"",
		success:function(data){
			
					$('#featured_button').html(data);
					
					if(val=='top_channel')
		{
					$('.inline, .formRow, .cuisinesListBox, .clearfix, .easyCostTopWidgetRow, .profileCommentFooter').contents().filter(function() {
	return this.nodeType === 3;
}).remove();
$(".postingformtype").addClass("loaded");
		}
		else
		{
			setupBlocks();
			$(".postingformtype").addClass("loaded");
		}
			tabWidthControl();	
			}
		});
	});
	
	
	
	function exploreload(){

		
		$.ajax({
		type:"POST",
		url:"grid.php",
		data:"",
		success:function(data){
			
					$('#featured_button').html(data);
					setupBlocks();
					$(".postingformtype").addClass("loaded");
					tabWidthControl();
					
				
			}
		});
	}
	
	
	
	$( ".feedfilter li a" ).click(function() {

		$('#feel_content').html('<img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" />');
		$( ".feedfilter li a" ).removeClass('active');
		
		$(this).addClass('active');
		$.ajax({
		type:"POST",
		url:"grid_feed.php",
		data:"",
		success:function(data){
			
					$('#feel_content').html(data);
					
					
				
			}
		});
	});
	
	
	$( "#hero-close-button .close-btn" ).click(function() {

		$('#hero-image').fadeOut();
	});
	
	function more_comments()
	{
		$.ajax({
		type:"POST",
		url:"more_comments.php",
		data:"",
		success:function(data){
			
					$('#profileCommentBox').append(data);
					
					
				
			}
		});
	}
	
	
	
	$( ".commentfilter li a" ).click(function() {

		$('#profileCommentBox_main').html('<img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" />');
		$( ".commentfilter li a" ).removeClass('active');
		
		$(this).addClass('active');
		$.ajax({
		type:"POST",
		url:"comment_feed.php",
		data:"",
		success:function(data){
			
					$('#profileCommentBox_main').html(data);
					
					
				
			}
		});
	});
	
	
	
	
	$( ".profilesubmenu li a" ).click(function() {

		$( ".profilesubmenu li a" ).removeClass('active');
		
		$(this).addClass('active');

		$('#profilefeedsection').html('<div style="text-align:center;margin:200px auto"><img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" /></div');
		$.ajax({
		type:"POST",
		url:"profile_feed.php",
		data:"",
		success:function(data){
			
					$('#profilefeedsection').html(data);
					
					
				
			}
		});
	});
	
	function collection_tab()
	{
		$( ".profileNav li a" ).removeClass('active');
		
		$('#collectionmenuID').addClass('active');
		
		$('#profile_main_content').html('<div style="text-align:center; margin:200px auto"><img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" /></div');
		$.ajax({
		type:"POST",
		url:"collection_tab.php",
		data:"",
		success:function(data){
			
					$('#profile_main_content').html(data);
					
					$('.inline, .formRow, .cuisinesListBox, .clearfix, .easyCostTopWidgetRow, .profileCommentFooter').contents().filter(function() {
	return this.nodeType === 3;
}).remove();
				
			}
		});
	}
	
	function profile_tab()
	{
		$( ".profileNav li a" ).removeClass('active');
		
		$('#profilemenuID').addClass('active');
		$('#profile_main_content').html('<div style="text-align:center; margin:200px auto"><img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" /></div');
		$.ajax({
		type:"POST",
		url:"profile_tab.php",
		data:"",
		success:function(data){
			
					$('#profile_main_content').html(data);
					$(".postingformtype").addClass("loaded");
					
					$('.inline, .formRow, .cuisinesListBox, .clearfix, .easyCostTopWidgetRow, .profileCommentFooter').contents().filter(function() {
	return this.nodeType === 3;
}).remove();
				
			}
		});
	}
	
	
		$( ".postingformtab li a" ).click(function() {

		$( ".postingformtab li a" ).removeClass('active');
		
		$(this).addClass('active');

		$('#posting_form').html('<div style="text-align:center;margin:200px auto"><img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" /></div');
		$.ajax({
		type:"POST",
		url:"posting_form.php",
		data:"",
		success:function(data){
			
					$('#posting_form').html(data);
					  $('#myTags').tagit();
					  $('.time').datetimepicker({
	datepicker:false,
	format:'H:i',
	step:5
});
$('.date').datetimepicker({
	yearOffset:222,
	lang:'en',
	timepicker:false,
	format:'d/m/Y',
	formatDate:'Y/m/d',
	minDate:'-1970/01/02', // yesterday is minimum date
	maxDate:'+1970/01/02' // and tommorow is maximum date calendar
});
					
				
			}
		});
	});
	
	
	$( ".postingformsubtab li a" ).click(function() {

		$( ".postingformsubtab li a" ).removeClass('active');
		
		$(this).addClass('active');

		$('#posting_form').html('<div style="text-align:center;margin:200px auto"><img src="pages/img/progress/progress-circle-success.svg" style="margin:auto" /></div');
		$.ajax({
		type:"POST",
		url:"posting_form.php",
		data:"",
		success:function(data){
			
					$('#posting_form').html(data);
					
					  $('#myTags').tagit();
					  $('.time').datetimepicker({
	datepicker:false,
	format:'H:i',
	step:5
});
$('.date').datetimepicker({
	yearOffset:222,
	lang:'en',
	timepicker:false,
	format:'d/m/Y',
	formatDate:'Y/m/d',
	minDate:'-1970/01/02', // yesterday is minimum date
	maxDate:'+1970/01/02' // and tommorow is maximum date calendar
});
				
			}
		});
	});
	
	