<!doctype html>
<html lang="pt-br">
  <head>
    {#include file="_header.tpl"#}
  </head>

  <body class="{#$APP_THEME#}">

    <!-- Demo page code -->
    <script type="text/javascript">
      $(function() {
        var match = document.cookie.match(new RegExp('color=([^;]+)'));
        if (match)
          var color = match[1];
        if (color) {
          $('body').removeClass(function(index, css) {
            return (css.match(/\btheme-\S+/g) || []).join(' ')
          })
          $('body').addClass('theme-' + color);
        }

        $('[data-popover="true"]').popover({html: true});

      });
    </script>
    <style type="text/css">
      #line-chart {
        height:300px;
        width:800px;
        margin: 0px auto;
        margin-top: 1em;
      }
      .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover { 
        color: #fff;
      }
    </style>

    <script type="text/javascript">
      $(function() {
        var uls = $('.sidebar-nav > ul > *').clone();
        uls.addClass('visible-xs');
        $('#main-menu').append(uls.clone());
      });
    </script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">


    <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
    <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
    <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
    <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
    <!--[if (gt IE 9)|!(IE)]><!--> 

    <!--<![endif]-->

    <div class="navbar navbar-default" role="navigation">
      <div class="navbar-header">
        <a class="" href="index.php"><span class="navbar-brand"><span class="fa fa-paper-plane"></span> {#$APP_NAME#} <small>{#$APP_VERSION#}</small> </span></a></div>
      <div class="navbar-collapse collapse" style="height: 1px;">
      </div>
    </div>
  </div>



  <div class="dialog">
    {#if $objeto->hasError() #}
    <div class="alert alert-error alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
      {#$objeto->ErrorMsg()#}
    </div>
    {#*<div class="alert alert-danger" role="alert">{#$objeto->ErrorMsg()#}</div>*#}
    {#/if #}
    <div class="panel panel-default">
      <p class="panel-heading no-collapse" style="font-size: 16px">Acesso ao {#$APP_NAME#}</p>
      <div class="panel-body">
        <form method="POST">
          <div class="form-group">
            <label>Login</label>
            <input type="text" name="login" id="login" class="form-control span12">
          </div>
          <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" id="senha" class="form-control span12 form-control">
          </div>
          <input type="submit" name="acao" id="acao" class="btn btn-primary pull-right" value="Acessar">
          <div class="clearfix"></div>
        </form>
      </div>
    </div>
    <p class="pull-right" style=""><a href="http://www.stonetech.info" target="blank" style="font-size: .75em; margin-top: .25em;">Design by StoneTech</a></p>
    {#*<p><a href="reset-password.html">Forgot your password?</a></p>*#}
  </div>


  <script src="{#$APP_TEMPLATE#}/lib/bootstrap/js/bootstrap.js"></script>
  {#*  <script src="{#$APP_TEMPLATE#}/lib/jquery.maskedinput.min.js"></script> *#}
  <script type="text/javascript">
      $("[rel=tooltip]").tooltip();
      $(function() {
        $('.demo-cancel-click').click(function() {
          return false;
        });
      });
      //$("#login").mask("99.999.999/9999-99");
  </script>
</body></html>
