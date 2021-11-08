$(document).ready(function(){

	      $(".button").click(function(){
 		    $(".body").css("overflow", "hidden");
   		    $(".fog").css("height", $(document).height());
 		    $(".fog").fadeIn("slow");
 		    $(".body2").fadeIn("slow");
	      });

 	      $(".body2,.button-close").click(function(){
		    $(".body2").fadeOut();
		    $(".fog").fadeOut();
   		    $(".body").css("overflow", "auto"); 
 	      });

	      $(".fon").click(function(){
		    $(".fog").css("background-color", "#000"); 
 		    $(".fon").fadeOut();
 		    $(".fonact").fadeIn("slow");
		    return false;
	      });

	      $(".fonact").click(function(){
		    $(".fog").css("background-color", "#c0c0c0"); 
 		    $(".fonact").fadeOut();
 		    $(".fon").fadeIn("slow");
		    return false;
	      });

 	      $(".infog").click(function(){
		    return false;
 	      });

	  });