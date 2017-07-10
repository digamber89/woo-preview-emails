<?php
/*Not the WordPress way but i'm trying to avoid any accidental CSS that would effect how the email template looks*/
?>
<link rel="stylesheet" type="text/css" href="<?php echo $my_plugin_url.'/assets/css/select2.min.css'; ?>" >
<style type="text/css">
	#search-description{
		display: none;
	}
	#tool-options{
		width: 590px;
		background: #fff;
		border-style: solid;
		border-width: 2px 2px 2px 0px;	
		position: fixed;
		top:30%;
		left:-590px;
		transition: all 0.8s ease-in-out;

	}

	#tool-options.active{
		left:0px;
	}


	#tool-wrap{ position: relative; }
	#show_menu{
		text-decoration: none;
		    padding: 10px;
		    color: #000;
		    position: absolute;
		    right: -72px;
		    top: 42%;
		    background: #fff;
		    transform: rotate(-90deg);
		    border: 2px solid;
	}
</style>