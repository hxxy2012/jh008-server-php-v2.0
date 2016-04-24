<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>集合啦官方站</title>
	<!-- core CSS -->
    <link href="<?php echo $this->module->assetsUrl ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $this->module->assetsUrl ?>/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo $this->module->assetsUrl ?>/css/animate.min.css" rel="stylesheet">
    <link href="<?php echo $this->module->assetsUrl ?>/css/prettyPhoto.css" rel="stylesheet">
    <link href="<?php echo $this->module->assetsUrl ?>/css/main.css" rel="stylesheet">
    <link href="<?php echo $this->module->assetsUrl ?>/css/responsive.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->       
    <!--<link rel="shortcut icon" href="<?php //echo $this->module->assetsUrl ?>/images/ico/favicon.ico">-->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $this->module->assetsUrl ?>/images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $this->module->assetsUrl ?>/images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $this->module->assetsUrl ?>/images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo $this->module->assetsUrl ?>/images/ico/apple-touch-icon-57-precomposed.png">
</head>
<body>

    <div class="header-home">
        <div class="headerWrap">
            <h1 class="logo"></h1>
            <ul class="nav-list">
                <li class="nav-list-item">
                    <a href="<?php echo $this->createUrl('index'); ?>">首页</a>
                </li>
                <li class="nav-list-item nav-list-item-selected">
                    <a href="<?php echo $this->createUrl('aboutus'); ?>">关于我们</a>
                </li>
                <li class="nav-list-item">
                    <a href="<?php echo $this->createUrl('product'); ?>">产品</a>
                </li>
                <li class="nav-list-item">
                    <a href="<?php echo $this->createUrl('contactus'); ?>">联系我们</a>
                </li>
            </ul>
        </div>
    </div>

    <div id="about-us" class="about-us">
        <div class="container">
            <div class="center wow fadeInDown main-tip">
                <h2>从零开始,创造一切</h2>
            </div>
            
            <!-- about us slider -->
            <div id="about-slider">
                <div id="carousel-slider" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators visible-xs">
                        <li data-target="#carousel-slider" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-slider" data-slide-to="1"></li>
                        <li data-target="#carousel-slider" data-slide-to="2"></li>
                    </ol>

                    <div class="carousel-inner">
                        <div class="item active">
                            <img src="<?php echo $this->module->assetsUrl ?>/images/about/guanyu1.jpg" class="img-responsive" alt=""> 
                       </div>
                       <div class="item">
                            <img src="<?php echo $this->module->assetsUrl ?>/images/about/guanyu2.jpg" class="img-responsive" alt=""> 
                       </div> 
                       <div class="item">
                            <img src="<?php echo $this->module->assetsUrl ?>/images/about/guanyu3.jpg" class="img-responsive" alt=""> 
                       </div> 
                    </div>
                    
                    <a class="left carousel-control hidden-xs" href="#carousel-slider" data-slide="prev">
                        <i class="fa fa-angle-left"></i> 
                    </a>
                    
                    <a class=" right carousel-control hidden-xs"href="#carousel-slider" data-slide="next">
                        <i class="fa fa-angle-right"></i> 
                    </a>
                </div> <!--/#carousel-slider-->
            </div><!--/#about-slider-->

            <div class="skill-wrap clearfix">
            
                <div class="center wow fadeInDown animated" style="visibility: visible; -webkit-animation: fadeInDown;">
                    <h2>关于零创</h2>
                    <p class="lead about-text">创立于2014年的成都零创科技是由一群仍然富有激情的8090组成，主攻移动互联网APP方向，努力地探索和开发全新的应用APP类型，便利用户的生活。我们相信APP应用市场的巨大潜力，由零开始，创造一切。</p>
                </div>
    
            </div>

        </div><!--/.container-->
    </div><!--/about-us-->

    <div id="footer" class="midnight-blue">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    蜀ICP备14010211号-2
                </div>
                <div class="col-sm-6">
                    <ul class="pull-right">
                        <li><a href="<?php echo $this->createUrl('index'); ?>">主页</a></li>
                        <li><a href="<?php echo $this->createUrl('aboutus'); ?>">关于我们</a></li>
                        <li><a href="<?php echo $this->createUrl('product'); ?>">产品</a></li>
                        <li><a href="<?php echo $this->createUrl('contactus'); ?>">联系我们</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div><!--/#footer-->

    <script src="<?php echo $this->module->assetsUrl ?>/js/jquery.js"></script>
    <script src="<?php echo $this->module->assetsUrl ?>/js/bootstrap.min.js"></script>
    <script src="<?php echo $this->module->assetsUrl ?>/js/jquery.prettyPhoto.js"></script>
    <script src="<?php echo $this->module->assetsUrl ?>/js/jquery.isotope.min.js"></script>
    <script src="<?php echo $this->module->assetsUrl ?>/js/main.js"></script>
    <script src="<?php echo $this->module->assetsUrl ?>/js/wow.min.js"></script>
</body>
</html>