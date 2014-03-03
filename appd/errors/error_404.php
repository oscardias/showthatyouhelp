<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>404 Error - showthayouhelp</title>

<link rel="shortcut icon" href="http://www.showthatyouhelp.com/favicon.ico" />
<style type="text/css">
/*
    Section: General
*/
html { height: 100% }
body {
    background: #020 url(images/background.png);
    font-family: Arial,sans-serif; font-size: 12px;
    height:100%; margin: 0; padding: 0;
}
a { color: #151; text-decoration: underline; }
a:visited { color: #151; }
a:hover { color: #0a0; }
p{margin: 0.5em 0}
/*
    Navigation bar
*/
#top-navigation{
    position:fixed;
    top:0;
    left:50%;
    z-index:100;
    height:30px;
    width:960px;
    margin:0 -480px auto;
    box-shadow: 0 0 5px #000;
    border: 1px solid #151;
    border-top:0;
    border-radius:0 0 5px 5px;
    background: #1e6d08; /* Old browsers */
    background: -moz-linear-gradient(top,  #2b9b0c 0%, #1e6d08 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#2b9b0c), color-stop(100%,#1e6d08)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #2b9b0c 0%,#1e6d08 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #2b9b0c 0%,#1e6d08 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #2b9b0c 0%,#1e6d08 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #2b9b0c 0%,#1e6d08 100%); /* W3C */
}
#top-navigation a{
    color:#fff;
    text-shadow:1px 1px 0 #000;
    text-decoration: none;
    line-height: 30px;
}
a.navigation-home{
    padding-left: 30px;
    background: url(images/icon/favicon.png) no-repeat 6px center;
}
/*
    Landing page
*/
.landing{
    min-height:500px;
    height:100%;
    position:relative;
}
.landing-wrap{
    position:absolute;
    top:50%;
    left:50%;
    width:960px;
    height:360px;
    margin:-180px 0 0 -480px;
    border-radius: 20px 0 20px 0;
    border:1px solid #000;
    box-shadow: 0 0 30px #5a5;
}
.landing-image{
    width:660px;
    height:100%;
    border-radius: 20px 0 0 0;
    position:relative;
    background-color:#000;
    background-repeat:no-repeat;
    background-position:center center;
}
.landing-logo{
    width:660px;
    height:100%;
    background:url(images/icon/alert_large.png) no-repeat center center;
    border-radius: 20px 0 0 0;
}
.landing-info{
    position:absolute;
    top:0;
    right:0;
    margin:10px 10px 5px;
    width:256px;
    height:316px;
    border-radius: 0 0 20px 0;
    border:2px solid #f00;
    text-align:center;
    padding:10px;
    font-size:1.4em;
    text-shadow:1px 1px 0 #fff;
    background: #ddd; /* Old browsers */
    background: -moz-linear-gradient(top,  #eeeeee 0%, #cccccc 100%); /* FF3.6+ */
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#eeeeee), color-stop(100%,#cccccc)); /* Chrome,Safari4+ */
    background: -webkit-linear-gradient(top,  #eeeeee 0%,#cccccc 100%); /* Chrome10+,Safari5.1+ */
    background: -o-linear-gradient(top,  #eeeeee 0%,#cccccc 100%); /* Opera 11.10+ */
    background: -ms-linear-gradient(top,  #eeeeee 0%,#cccccc 100%); /* IE10+ */
    background: linear-gradient(to bottom,  #eeeeee 0%,#cccccc 100%); /* W3C */
}
/*
    Footer
*/
.bottom-navigation{
    position:fixed;
    bottom:0;
    left:50%;
    width:960px;
    margin:0 -480px auto;
    padding:2px 0;
    z-index:1000;
    text-align:center;
    text-shadow:1px 1px 0 #000;
    background: rgba(0,0,0,0.75);
    border:1px solid rgba(200,255,200,0.3);
    border-bottom: 0;
    border-radius:5px 5px 0 0;
    color:rgba(255,255,255,0.75);
}
.bottom-navigation a{
    color:rgba(255,255,255,0.75);
}
</style>

<!--[if lte IE 8]>
<style type="text/css">
.bottom-navigation{
    background: #333;
    border:1px solid #343;
    color:#ccc;
}
.bottom-navigation a{
    color:#ccc;
}
</style>
<![endif]-->

<meta name="description" content="showthatyouhelp.com is a web site dedicated to ecology, sustainability and green initiatives." />
<meta name="keywords" content="showthatyouhelp, earth, ecology, sustainability, green, nature" />

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-34576670-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body>
    <div id="top-navigation">
        <a href="http://www.showthatyouhelp.com/" class="navigation-home">showthatyouhelp.com</a>
    </div>
    
<div id="main" class="landing">
    <div class="landing-wrap">
        <div class="landing-image">
            <div class="landing-logo">
            </div>
        </div>
        <div class="landing-info">
            <h1><?php echo $heading; ?></h1>
            <p><?php echo $message; ?></p>
            <p>Visit our <a href="http://www.showthatyouhelp.com/">home page</a>.</p>
        </div>
    </div>
</div>

    <div class="bottom-navigation">
        <a href="http://www.showthatyouhelp.com/about">About</a>
        |
        <a href="http://www.showthatyouhelp.com/about/terms">Terms of Service</a>
        |
        <a href="http://www.showthatyouhelp.com/about/privacy">Privacy Policy</a>
        |
        &copy;<?php echo date('Y'); ?> <a href="http://www.softerize.com">Softerize</a>
    </div>
</body>
</html>