<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>早 - 飞鱼秀非官方回放</title>
        <link rel="stylesheet" href="/static/css/main.css">
        <link rel="stylesheet" href="/static/css/font-awesome.min.css">
        <script type="text/javascript" src="/static/js/jquery-2.1.4.min.js"></script>
        <!--[if lt IE 9]>
            <script src="http://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
            <script src="http://cdn.staticfile.org/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <header>
            <div class="sidebar">
                <img class="avatar" src="/static/img/avatar.png">
                <h2 class="description">小人们洗耳聆听吧！</h2>
                <nav>
                    <a href="/" title="首页"><i class="fa fa-home"></i></a>
                    <a href="/about" title="关于"><i class="fa fa-about"> </i></a>
                </nav>
            </div>
        </header>

        <div class="content">

            @yield('content')

            @yield('comment')

            <footer id="footer">
                © 2016 <a href="/">Zao~ao~ao~ao~ao.com</a> @yield('author')
            </footer>

            <div id="gotop">
                <span id="gotophover"></span>
                <img src="/static/img/top.png" alt="返回顶部">
            </div>
        </div>
        <script type="text/javascript" src="/static/js/main.js"></script>
    </body>
</html>
