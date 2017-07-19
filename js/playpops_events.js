
	"use strict";
	
	// JavaScript Document
	// 2. This code loads the IFrame Player API code asynchronously.]
	// -------------------------------------------------------------
	
	var tag = document.createElement('script');
	
	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);	
	
	// ---------------------------------------------------------------
	// Main
	// ---------------------------------------------------------------
	
	// 3. This function creates an <iframe> (and YouTube player)
	//    after the API code downloads.
	
	// Create Youtube player after api has downloaded
	var mmPlayer;
	var done = false;
	var currentTime = 0;
	var mmPlayerStatus = 0;
	var $ = jQuery.noConflict();
	
	var mmTriggerLength = $('#mmTriggerLength').html();
	
	
	function onYouTubeIframeAPIReady() 
	{
	
		// Create an object				
		mmPlayer = new YT.Player('mmPlayer', {
			events: {
			'onReady': onPlayerReady,
			'onStateChange': onPlayerStateChange
			}
		});
	}
	
	// 4. The API will call this function when the video player is ready.
	function onPlayerReady(event) 
	{
		event.target.playVideo();
	}
	
	// 5. The API calls this function when the player's state changes.
	//    The function indicates that when playing a video (state=1),
	//    the player should play for six seconds and then stop.
	var done = false;
	
	function stopVideo() 
	{
		player.stopVideo();
	}
	
	// ----------------------------------------
	  
	// Set timer to save video play location
	// Get the player element by Id tag
	//mmPlayer = document.getElementById(mmPlayer);
	
	// 4. The API will call this function when the video player is ready.
	function onPlayerReady(event) 
	{
		// Play video on Autoplay
		// event.target.playVideo();
		mmPlayVideo();
	}
	
	// 5. The API calls this function when the player's state changes.
	//    The function indicates that when playing a video (state=1),
	//    the player should play for six seconds and then stop.
		
	function onPlayerStateChange(event) 
	{
		
		//4.Once the video is played the script should look for the $TriggerLength value. After the video has passed that length it should activate a trigger event.
		
		//The event will only get triggered if the user pauses the video or when the video reaches the end.
		if( ( event.data == YT.PlayerState.ENDED || event.data == YT.PlayerState.PAUSED ) && currentTime > mmTriggerLength)
		{
			$("#playpops-overlay").show();
			$("#playpops").show();
		}
		else 
		{
			$("#playpops-overlay").hide();
			$("#playpops").hide();
		}
		
		// Save player staturs in global variable
		mmPlayerStatus = event.data
	}
	
	// Stop video
	function mmStopVideo() 
	{
		mmPlayer.stopVideo();
	}
	
	// Play video
	function mmPlayVideo() 
	{
		mmPlayer.playVideo();
	}
	
	function mmResizeGrid()
	{
		
		var mmDisplayStatus;
		var mmDisplayFBLarge;
		var mmDisplayFBSmall;
		var mmFrameSmallTop;
		var mmFrameLargeTop;
		var mmFrameLargeLeft;
		var mmFrameSmallLeft;
		
		// Get iframe dimensions
		var mmIframeWidth = $('#mmPlayer').width();
		var mmIframeHeight = $("#mmPlayer").height();
		
		// Get playpops dimensions
		var mmPlayPopsTop = $("#playpops").css("top");
		
		// Calcs			
		var mmIframeAspect = mmIframeHeight / mmIframeWidth;
		var mmPlayPopsWidth = mmIframeWidth - 20;	
		var mmPlayPopsHeight = mmIframeHeight * 0.6;
		
		mmPlayPopsTop = 36.4; 
		
		// Keep the Like box centered
		//if(mmIframeWidth > 520)
				
		if(mmPlayPopsWidth > 554)
		{
						
			// Keep the height fixed based on the height of the pagelike height	
			if(mmPlayPopsHeight < 270)
				mmPlayPopsHeight = 270;
		}
		
		console.log("youtube frame width : " + mmIframeWidth + ". playpops width : " + mmPlayPopsWidth);
		
		mmFrameSmallTop = mmFrameLargeTop = 40;
		mmFrameLargeLeft = (mmPlayPopsWidth /2) - 250;	
		mmFrameSmallLeft = (mmPlayPopsWidth /2) - 100;
		
		// Resize Playpops
		$("#playpops").css({
			"width": mmPlayPopsWidth, 
			"height": mmPlayPopsHeight, 
			"top": mmPlayPopsTop}); 
					 
		$("#playpops").css("box-sizing", "unset");
		
		// Resize fb Box Large
		$("#fbframelikeLarge").css("top", mmFrameLargeTop);
		$("#fbframelikeLarge").css("margin-top", "-10");
		
		// Resize fb Box Small
		$("#fbframelikeSmall").css("margin-top", "-10");
		$("#fbframelikeSmall").css("top", mmFrameSmallTop);
		
		
		// Choose which facebook iframe to show based on width
		if(mmPlayPopsWidth < 554)
		{
			console.log("width : " + mmPlayPopsWidth); // = 554;
			
			$("#fbframelikeLarge").hide();
			$("#fbframelikeSmall").show();
			
		}
		else
		{
			
			$("#fbframelikeLarge").show();
			$("#fbframelikeSmall").hide();
			
		}
		
		// Choose to show playpops
		// set Display
		if (mmPlayerStatus == YT.PlayerState.PAUSED)
			$("#playpops").show();
		else
			$("#playpops").hide();
					
	}
	
	// ------------------------------------------
	// End of Sub Functions 
	// ------------------------------------------
	
	// Get the mmPlayVideo click
	$('#mmClosePop').click(function()
	{
				
		// Check if player status is paused
		if (mmPlayerStatus == YT.PlayerState.PAUSED)
		{
			// resume video
			mmPlayVideo();
			
			// hide popup
			mmPlayPopUp("none"); 
		}
			
	});
	
	// event click
	$('#mmSkipVideo').click(function()
	{
		// Check if player status is paused
		if (mmPlayerStatus == YT.PlayerState.PAUSED)
		{
			// resume video
			mmPlayVideo();
			
			// hide popup
			mmPlayPopUp("none"); 
		}
		
	});
	
	
	// ----------------------------------------------------
	// Window resize to adjust the width of the popup
	// ----------------------------------------------------
	window.onload = function()
	{
		
		// Start a clock
		// -----------------------------
		setInterval(function() 
		{
			currentTime = mmPlayer.getCurrentTime();
			
			console.log("currentTime : " + currentTime + " Trigger : " + mmTriggerLength); 
		}, 1000);
	
		// Resize the popup to match original size
		mmResizeGrid();
	}
	
	// Resive event
	window.onresize = mmResizeGrid;