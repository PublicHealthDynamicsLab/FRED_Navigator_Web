<?php
require_once('util/Constants.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    
    <title>FRED Navigator Web: Contact Us</title>
    <link rel="stylesheet" type="text/css" href="css/fn_style.css" />
    <link rel="stylesheet" type="text/css" href="javascript/jquery-ui-1.10.3.custom/css/redmond/jquery-ui-1.10.3.custom.css" />
    <link rel="stylesheet" type="text/css" href="css/ext-jquery-ui.css" />
    <style>           
      ul.experiment_list {
        font-family:Verdana, Arial, Helvetica, sans-serif;
        font-size:12pt;
        font-weight:bold;
        letter-spacing:.05em;
        text-transform:uppercase;
        padding:8px;  
        text-align:center;
      }
      
      ul.experiment_list a:link {color:#24598C; text-decoration:none;}      /* unvisited link */
      ul.experiment_list a:visited {color:#24598C; text-decoration:none;}  /* visited link */
      ul.experiment_list a:hover {color:#420078; text-decoration:underline;}  /* mouse over link */
      ul.experiment_list a:active {color:#420078; text-decoration:underline;}  /* selected link */ 
           
      /*for the tooltip box*/
      .ui-tooltip, .arrow:after {
        border:2px solid #76A7D1;
      }
      .ui-tooltip {
        padding:10px 20px;
        border-radius:20px;
        text-transform:uppercase;
        box-shadow:0 0 7px #76A7D1;
      }
      .arrow {
        width:70px;
        height:16px;
        overflow:hidden;
        position:absolute;
        left:50%;
        margin-left:-35px;
        bottom:-16px;
      }
      .arrow.top {
        top:-16px;
        bottom:auto;
      }
      .arrow.left {
        left:80%;
      }
      .arrow:after {
        content:"";
        position:absolute;
        left:20px;
        top:-20px;
        width:25px;
        height:25px;
        box-shadow:6px 5px 9px -9px black;
        -webkit-transform:rotate(45deg);
        -moz-transform:rotate(45deg);
        -ms-transform:rotate(45deg);
        -o-transform:rotate(45deg);
        tranform:rotate(45deg);
      }
      .arrow.top:after {
        bottom:-20px;
        top:auto;
      }
    </style>
    
    <script type="text/javascript" src="javascript/jquery-2.0.3.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
    
    <script>
    $(function() {
      $(document).tooltip({
        position: {
          my: 'center bottom-20',
          at: 'center top',
          using: function(position, feedback) {
            $(this).css(position);
            $('<div>') 
              .addClass('arrow')
              .addClass(feedback.vertical)
              .addClass(feedback.horizontal)
              .appendTo(this);
              
          }
        },
        show: { effect: "fade" },
        hide: { effect: "fade"}
      });
    });
    </script>
</head>

<body>

<div id="pagewidth">

    <!-- This is the Pitt Banner. The right-hand links are listed at the bottom of the HTML page. -->
    <!-- ======================================================================================== -->
    <div id="pitt-header" class="white">
        <a href="http://www.pitt.edu/" title="University of Pittsburgh" id="p-link">University of Pittsburgh</a>
    </div><!-- end pitt-header -->

    <div id="nav_menu">
        <div id="fred-links">
	        <p>
	            <a href="<?php echo Constants::getSiteBase(); ?>">Home</a> &nbsp;&nbsp;|&nbsp;&nbsp;
	            <a href="http://fred.publichealth.pitt.edu">FRED Home</a> &nbsp;&nbsp;|&nbsp;&nbsp;
	            <a href="<?php echo Constants::getSiteBase() . '/contact_us.php'?>">Contact Us</a>
            </p>
        </div>
    </div><!-- #nav_menu -->
    
    <div id="wrapper" class="clearfix">

        <div id="maincol">
            <p class="center"><span class="heading">FRED Navigator Contact Information</span></p>
            <hr />
            <p><span class="subheading">For technical questions regarding this website, contact:</span></p>
		
		    <p>David Galloway<br/>
		    Graduate School of Public Health<br/>
		    University of Pittsburgh<br/>
		    130 DeSoto Street<br/>
		    717 Parran Hall<br/>
		    Pittsburgh, PA 15261<br/>
		    Email: <a href="mailto:ddg5@pitt.edu?subject=FRED Navigator Website">ddg5@pitt.edu</a><br/><br/></p>

		    <p><span class="subheading">For any other questions related to the FRED project, please contact:</span></p>

		    <p>Dr. John Grefenstette, PhD<br/>
		    Graduate School of Public Health<br/>
		    University of Pittsburgh<br/>
		    130 DeSoto Street<br/>
		    702A Parran Hall<br/>
		    Pittsburgh, PA 15261<br/>
		    Email: <a href="mailto:gref@pitt.edu?subject=FRED Navigator Website">gref@pitt.edu</a><br/>
		    Tel: 412-648-9920<br/>
		    </p>
            
        </div><!-- #maincol -->

        <div id="leftcol">
    
        </div><!-- #leftcol -->    

    </div><!-- #wrapper -->

    <div id="footer">
        <div>
            <img src="image/fredNav-pro.png" alt="FRED Navigator: Professional" height="100" width="220" style="position:absolute; left:.5em; top:1.2em;" />
            <img src="image/midas_logo_text.jpg" alt="MIDAS" height="100" width="80" style="position:absolute; left:49%; top:.5em;" />
            <img src="image/PPH_Mark_Web-med.png" alt="Pitt Public Health" height="100" width="466" style="position:absolute; right:.5em; top:.8em;" />
        </div>
        <div style="position:absolute; left:42%; bottom:.4em;">
            <span class="grey small">&copy; 2013 <a href="http://www.phdl.pitt.edu" target="_blank" alt="Public Health Dynamics Laboratory" 
               title="Public Health Dynamics Laboratory">Public Health Dynamics Laboratory</a>, University of Pittsburgh</span>
        </div>
    </div><!-- #footer --> 
    
    <!-- PITT BANNER RIGHT-HAND LINKS -->
    <!-- ======================================================================================== -->
    <ul id="pitt-links">
	    <li id="p-home"><a href="http://www.as.pitt.edu/">Arts and sciences</a> | <a href="http://www.pitt.edu/">Pitt Home</a> | <a href="http://www.pitt.edu/findpeople.html" title="University of Pittsburgh Directory">Find People</a></li>
    </ul>
    
</div><!-- #pagewidth -->
</body>
</html>

</html>