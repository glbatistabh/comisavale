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

    {#include file="_menuTop.tpl"#}
    {#include file="_menuLeft.tpl"#}

    <div class="content">
      <div class="header">
        <h1 class="page-title">{#$titulo#}&nbsp;<small>{#$subTitulo#}</smal></h1>
        {#*
        <ul class="breadcrumb">
        <li><a href="index.html">Home</a> </li>
        <li class="active">Users</li>
        </ul>
        *#}
      </div>

      <div class="main-content">
        {#*        
        <div class="btn-toolbar list-toolbar">
        <button class="btn btn-primary"><i class="fa fa-plus"></i> New User</button>
        <button class="btn btn-default">Import</button>
        <button class="btn btn-default">Export</button>
        <div class="btn-group">
        </div>
        </div>
        *#} 
        {#foreach $erros as $errorMsg#}
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
          {#$errorMsg#}
        </div>
        {#/foreach#}

        <form id="myForm" method="POST">
          {#foreach $form as $campo#}
          {#if $campo['tipo'] == "text" || $campo['tipo'] == "email" || $campo['tipo'] == "password"#}
          <div class="form-group">
            <label for="{#$campo['nome']#}">{#$campo['descricao']#}</label>
            <input class="form-control" type="{#$campo['tipo']#}" value="{#$campo['valor']#}" name="{#$campo['nome']#}" id="{#$campo['nome']#}">
          </div>
          {#elseif $campo['tipo'] == "hidden"#}
          <input class="form-control" type="{#$campo['tipo']#}" value="{#$campo['valor']#}" name="{#$campo['nome']#}" id="{#$campo['nome']#}">
          {#elseif $campo['tipo'] == "textarea"#}
          <div class="form-group">
            <label for="{#$campo['nome']#}">{#$campo['descricao']#}</label>
            <textarea rows="3" class="form-control" id="{#$campo['nome']#}" name="{#$campo['nome']#}">{#$campo['valor']#}</textarea>
          </div>
          {#/if#}
          {#/foreach#}
          <div class="btn-toolbar list-toolbar">
            {#foreach $acao as $a#}
            <button class="btn btn-primary" name="{#$a['nome']#}" value="{#$a['valor']#}">{#$a['texto']#}</button>
            {#/foreach#}
          </div>
        </form>

        {#include file="_footer.tpl" #}
      </div>
    </div>


    <script src="{#$APP_TEMPLATE#}/lib/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript">
      $("[rel=tooltip]").tooltip();
      $(function() {
        $('.demo-cancel-click').click(function() {
          return false;
        });
      });
    </script>


  </body></html>
