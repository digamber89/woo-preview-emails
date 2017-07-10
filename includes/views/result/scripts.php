<script src="<?php echo site_url().'/wp-includes/js/jquery/jquery.js'; ?>" type="text/javascript"></script>
<script src="<?php echo $my_plugin_url.'/assets/js/select2.min.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
	window.onload = function(){
		var show_menu = document.getElementById("show_menu");
		show_menu.addEventListener("click", function(e){
			var classes = document.getElementById("tool-options").classList;
			if ( classes[0] == undefined ){
				document.getElementById("tool-options").classList.add("active");
				show_menu.innerHTML = 'Hide Menu';
			}else{
				document.getElementById("tool-options").classList.remove("active");
				show_menu.innerHTML = 'Show Menu';
			}
		});

}
	
</script>