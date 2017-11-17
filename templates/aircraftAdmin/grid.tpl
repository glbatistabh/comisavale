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
        {#if isset($stats)#}
          <div class="stats">
            {#if isset($stats['danger'])#}
              <p class="stat">{#$stats['danger']['label2']#} <span class="label label-danger">{#$stats['danger']['label1']#}</span></p>
              {#/if#}
              {#if isset($stats['success'])#}
              <p class="stat">{#$stats['success']['label2']#} <span class="label label-success">{#$stats['success']['label1']#}</span></p>
              {#/if#}
              {#if isset($stats['info'])#}
              <p class="stat">{#$stats['info']['label2']#} <span class="label label-info">{#$stats['info']['label1']#}</span></p>
              {#/if#}
              {#if isset($stats['none'])#}
              <p class="stat">{#$stats['none']['label']#}</p>
            {#/if#}
          </div>
        {#/if#}

        <h1 class="page-title">{#$titulo#}&nbsp;<small>{#$subTitulo#}</smal></h1>
        {#*
        <ul class="breadcrumb">
        <li><a href="index.html">Home</a> </li>
        <li class="active">Users</li>
        </ul>
        *#}
      </div>

      {#if isset($filtros)#}
        <div class="panel panel-default">
          <a href="#widget1container" class="panel-heading collapsed" data-toggle="collapse">Filtros </a>
          <div id="widget1container" class="panel-body collapsed collapse">
            <form method="post">
              <input type="hidden" id="extras" name="extras" value="doPesquisa">
              {#for $idx=0 to count($filtros)-1 step 2#}
                <div class="row">
                  <div class="col-md-2"><label for="{#$filtros[$idx+0]['name']#}">{#$filtros[$idx+0]['label']#}</label></div>
                  <div class="col-md-4"><input class="form-control" type="text" id="{#$filtros[$idx+0]['name']#}" name="{#$filtros[$idx+0]['name']#}" value="{#$filtros[$idx+0]['value']#}"></div>
                    {#if isset($filtros[$idx+1])#}
                    <div class="col-md-2"><label for="{#$filtros[$idx+1]['name']#}">{#$filtros[$idx+1]['label']#}</label></div>
                    <div class="col-md-4"><input class="form-control" type="text" id="{#$filtros[$idx+1]['name']#}" name="{#$filtros[$idx+1]['name']#}" value="{#$filtros[$idx+1]['value']#}"></div>
                    {#/if#}
                </div>
              {#/for#}
              <div class="row">
                <div class="col-md-12 text-right">
                  <div class="btn-toolbar list-toolbar">
                    <button class="btn btn-danger" name="cancelPesquisa" value="cancelPesquisa">Cancelar</button>
                    <button class="btn btn-primary" name="doPesquisa" value="doPesquisa">Pesquisar</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      {#/if#}


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

        <table class="table table-hover table-bordered table-responsive">
          {#foreach $dados as $row#}
            {#if $row@first#}
              <thead>
                <tr>
                  {#foreach $row as $cell#}
                    <th class="text-{#$align[$cell@index]|default:'left'#}">{#$cell@key#}</th>
                    {#/foreach#}

                  {#if count($acoes[$row@key]) > 0#}
                    <th style="width: 3.5em;"></th>
                    {#/if#}
                </tr>
              </thead>
            {#/if#}

            {#if $row@first#}
              <tbody>
              {#/if#}
              <tr>
                {#foreach $row as $cell#}
                  {#$formatacao = $format[$cell@index]|default:'%s'#}
                  <td class="text-{#$align[$cell@index]|default:'left'#}">{#$cell|string_format:$formatacao#}&nbsp;</td>
                {#/foreach#}

                {#if count($acoes[$row@key]) > 0#}
                  <td class="text-right" style="white-space:nowrap">
                    {#foreach $acoes[$row@key] as $acao#}
                      <a href='{#$acao.href#}' title='{#$acao.title#}' ><i style="font-size: 18px" class='{#$acao.icon#} icon-large'></i></a>&nbsp;
                      {#/foreach#}
                  </td>
                {#/if#}

                {#*                 
                <td>
                <a href="user.html"><i class="fa fa-pencil"></i></a>
                <a href="#myModal" role="button" data-toggle="modal"><i class="fa fa-trash-o"></i></a>
                </td>
                *#}              
              </tr>              
            {#/foreach#}
          </tbody>
        </table>
        <p class="text-right">{#$resumo#}</p>


        {#*        
        <ul class="pagination">
        <li><a href="#">&laquo;</a></li>
        <li><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#">&raquo;</a></li>
        </ul>
        *#}

        {#if isset($paginacao) AND $paginacao['totalPagina'] > 1#}
          {#$ini = MAX(1, $paginacao['pagina'] - 6) #}
          {#$fim = MIN($paginacao['totalPagina'], $paginacao['pagina'] + 6) #}                    
          {#$conexao = (strpos($smarty.server.REQUEST_URI,'?')==0)?'?':'&'#}          
          {#*{#$ini#},{#$fim#},{#$paginacao['pagina']#}*#}
          <ul class="pagination">
            <li><a href="./{#$smarty.server.REQUEST_URI#}&pagina=0">&laquo;</a></li>
              {#for $pagina=$ini to $fim#}
                {#if $pagina == $paginacao['pagina']#}
                <li><a href="/{#$smarty.server.REQUEST_URI#}{#$conexao#}pagina={#$pagina#}"><b>{#$pagina#}</b></a></li>
                    {#else#}
                <li><a href="./{#$smarty.server.REQUEST_URI#}{#$conexao#}pagina={#$pagina#}">{#$pagina#}</a></li>
                {#/if#}
              {#/for#}
            <li><a href="./{#$smarty.server.REQUEST_URI#}{#$conexao#}pagina={#$paginacao['totalPagina']#}">&raquo;</a></li>
          </ul>
        {#/if#}


        {#*
        <div class="modal small fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Delete Confirmation</h3>
        </div>
        <div class="modal-body">
        <p class="error-text"><i class="fa fa-warning modal-icon"></i>Are you sure you want to delete the user?<br>This cannot be undone.</p>
        </div>
        <div class="modal-footer">
        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancel</button>
        <button class="btn btn-danger" data-dismiss="modal">Delete</button>
        </div>
        </div>
        </div>
        </div>
        *#}

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
