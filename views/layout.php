<html>

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $title; ?></title>
        <?php echo css_asset("all.css"); ?>
<!--		<link href= "/css/ils/ui_mainpage.css" rel="stylesheet" type="text/css" />-->
<!--[if lt IE 8]>



<style type="text/css">



#xouter{display:block}

#xcontainer{top:50%;display:block}

#xinner{top:-50%;position:relative}



</style>

<![endif]-->




<!--[if IE 7]>



<style type="text/css">



#xouter{

position:relative;

/*overflow:hidden;*/



}

</style>



<![endif]-->

</head>
<body>
<!-- ** Javascript ** -->
<script type="text/javascript">
    var baseurl = "<?php echo base_url(); ?>"
</script>
<?php echo js_asset("ext.js"); ?>
<?php echo js_asset("all.js"); ?>
<?php echo js_asset("components.js"); ?>
    






        
<div id="maincontent">

<?php echo image_asset('ilsheader.jpg')?>


    
</div>

<!--<div id="bar"></div>-->

<div id="userControls">

</div>

<?php include_once 'menu.php'; ?>
    
<?php echo $content_for_layout?>
<style type="text/css">

<!--

a:link {

	color: #000;

}

a:visited {

	color: #000;

}

a:hover {

	color: #000;

}

a:active {

	color: #000;

}


-->

</style><div id="mainfooter">

  <div align="center">ILS / COPYRIGHT &copy; <?php echo date("Y"); ?> Lithefire Solutions Inc.<br /></div>

  <div class ="footer" align="center">www.lithefire.com</div>

  

     

  </div>





  <div id="rightfooter">

    <div id="imagefooter"></div>

  </div>

    

</div>



</body>



</html>