<!doctype html>
<html lang="pt-br">
  <head>
    {#include file="_header.tpl"#}

    <script src="{#$APP_TEMPLATE#}/lib/flot/jquery.flot.js" type="text/javascript"></script>
    <script src="{#$APP_TEMPLATE#}/lib/flot/jquery.flot.stack.js" type="text/javascript"></script>
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



        var css_id = "#placeholder";
        var data = {#$bars['data']|json_encode#};
      {#*
      [
      {label: 'Não Recebidos', data: [[1, 300], [2, 300], [3, 300], [4, 300], [5, 300]]},
      {label: 'Recebidos', data: [[1, 800], [2, 600], [3, 400], [4, 200], [5, 0]]},
      {label: 'Recebidos Dias Diferente', data: [[1, 100], [2, 200], [3, 300], [4, 400], [5, 500]]},
      ];
      *#}
        var options = {
          series: {stack: 0,
            points: {show: false, fill: false},
            lines: {show: false, fill: true, steps: false},
            bars: {show: true, barWidth: 0.9, align: 'center', }, },
          xaxis: {ticks: {#$bars['ticks']|json_encode#} {#*[[1, 'One'], [2, 'Two'], [3, 'Three'], [4, 'Four'], [5, 'Five']]*#}},
        };
        $.plot($(css_id), data, options);

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
        {#*
        <div class="stats">
        <p class="stat"><span class="label label-info">5</span> Tickets</p>
        <p class="stat"><span class="label label-success">27</span> Tasks</p>
        <p class="stat"><span class="label label-danger">15</span> Overdue</p>
        </div>
        *#}

        <h1 class="page-title">Dashboard</h1>
        {#*       
        <ul class="breadcrumb">
        <li><a href="index.html">Home</a> </li>
        <li class="active">Dashboard</li>
        </ul>
        *#}      
      </div>

      <div class="main-content">

        <div class="panel panel-default">
          <a href="#page-stats" class="panel-heading" data-toggle="collapse">Estatísticas</a>
          <div id="page-stats" class="panel-collapse panel-body collapse in">

            <div class="row">
              {#foreach $stats as $item#}
                {#assign var="statsSize" value=(int)12/$stats|@count nocache#}
                <div class="col-md-{#$statsSize#} col-sm-{#$statsSize#}">
                  <div class="knob-container">
                    <input class="knob" data-width="200" data-min="{#$item['min']#}" data-max="{#$item['max']#}" data-displayPrevious="true" value="{#$item['value']#}" data-fgColor="#92A3C2" data-readOnly=true;>
                    <h3 class="text-muted text-center">{#$item['label']#}</h3>
                  </div>
                </div>
              {#/foreach#}
            </div>
          </div>
        </div>



        <div class="panel panel-default">
          <a href="#page-stats" class="panel-heading" data-toggle="collapse">Ultimos 10 dias</a>
          <div id="page-stats" class="panel-collapse panel-body collapse in">
            <div class="row">
              <div id="placeholder" style="height: 300px"></div>
            </div>
          </div>
        </div>


        {#*<div class="row">
        <div class="col-sm-6 col-md-9">
        <div class="panel panel-default">
        <div class="panel-heading no-collapse">Not Collapsible<span class="label label-warning">+10</span></div>
        <table class="table table-bordered table-striped">
        <thead>
        <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        </tr>
        </thead>
        <tbody>
        <tr>
        <td>Mark</td>
        <td>Tompson</td>
        <td>the_mark7</td>
        </tr>
        <tr>
        <td>Ashley</td>
        <td>Jacobs</td>
        <td>ash11927</td>
        </tr>
        <tr>
        <td>Audrey</td>
        <td>Ann</td>
        <td>audann84</td>
        </tr>
        <tr>
        <td>John</td>
        <td>Robinson</td>
        <td>jr5527</td>
        </tr>
        <tr>
        <td>Aaron</td>
        <td>Butler</td>
        <td>aaron_butler</td>
        </tr>
        <tr>
        <td>Chris</td>
        <td>Albert</td>
        <td>cab79</td>
        </tr>
        </tbody>
        </table>
        </div>
        </div>

        <div class="col-sm-6 col-md-">
        <div class="panel panel-default">
        <a href="#widget1container" class="panel-heading" data-toggle="collapse">Collapsible </a>
        <div id="widget1container" class="panel-body collapse in">
        <h2>Here's a Tip</h2>
        <p>This template was developed with <a href="http://middlemanapp.com/" target="_blank">Middleman</a> and includes .erb layouts and views.</p>
        <p>All of the views you see here (sign in, sign up, users, etc) are already split up so you don't have to waste your time doing it yourself!</p>
        <p>The layout.erb file includes the header, footer, and side navigation and all of the views are broken out into their own files.</p>
        <p>If you aren't using Ruby, there is also a set of plain HTML files for each page, just like you would expect.</p>
        </div>
        </div>
        </div>
        </div>*#}

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
