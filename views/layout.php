<html>

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $title; ?></title>
		<link href= "/css/ils/ui_mainpage.css" rel="stylesheet" type="text/css" />
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

 

        <!-- ** CSS ** -->

        <!-- base library -->

        <link rel="stylesheet" type="text/css" href="/js/ext34/resources/css/ext-all.css" />
        <!-- overrides to base library -->

</head>
<body>
<!-- ** Javascript ** -->
        <!-- ExtJS library: base/adapter -->
        <script type="text/javascript" src="/js/ext34/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="/js/commonjs/ExtCommon.js"></script>
        <!-- ExtJS library: all widgets -->
        <script type="text/javascript" src="/js/ext34/ext-all-debug.js"></script>
    






        
<div id="maincontent">

<img src="/images/ilsheader.jpg" />


    
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