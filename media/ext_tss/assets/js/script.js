jQuery(function(){
jQuery('dl.tabs dt').click(function(){
jQuery(this)
.siblings().removeClass('selected').end()
.next('dd').andSelf().addClass('selected');
});
});

jQuery(document).ready(function(){
	jQuery(".sliders .title:first").addClass("active");
	jQuery(".sliders .desc:not(:first)").hide();

	jQuery(".sliders .title").click(function(){
		jQuery(this).next("div.desc").slideToggle("slow")
		.siblings("div.desc:visible").slideUp();
		jQuery(this).toggleClass("active");
		jQuery(this).siblings(".title").removeClass("active");
	});

    jQuery(".spoilers .title").click(function(){
		jQuery(this).next("div.desc").slideToggle("slow")
		jQuery(this).toggleClass("active");
		jQuery(this).siblings(".title").removeClass("active");
	});

});