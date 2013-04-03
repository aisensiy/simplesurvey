<?php require_once('ti.php'); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <!--<meta property="wb:webmaster" content="7cb966ae2a9fafdb" />-->
    <meta property="qc:admins" content="2435425355045352651631611006375" />
    <title><?php emptyblock('title') ?></title>
    <meta name="description" content="简单问卷 -- 一个用于制作在线调查问卷的工具" />
    <link rel="stylesheet/less" href="/static/less/custom.less" media="all" type="text/css" />
    <script src="http://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>
    <script src="http://lib.sinaapp.com/js/jquery-ui/1.8.9/jquery-ui.min.js"></script>
    <script src="http://staticlib.sinaapp.com/lib/js/jquery/jquery.validate.1.9.0.js"></script>
    <script src="http://lib.sinaapp.com/js/bootstrap/2.0.3/js/bootstrap.min.js"></script>
    <script src="http://staticlib.sinaapp.com/lib/js/underscore/underscore-min.js"></script>
    <script src="http://staticlib.sinaapp.com/lib/js/less/less-1.3.0.min.js"></script>
    <script src="http://staticlib.sinaapp.com/lib/js/backbone/backbone-0.9.1-min.js"></script>
    <script src="http://staticlib.sinaapp.com/lib/js/ace/ace-template.js"></script>
    <script src="/static/js/jquery.poshytip.js" type="text/javascript" charset="utf-8"></script>
    <script src="/static/js/bootstrap-alert.js" type="text/javascript" charset="utf-8"></script>
    <script src="/static/js/jquery.utility.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="/static/css/tip-twitter/tip-twitter.css" type="text/css" />
    <link type="text/css" rel="stylesheet" href="http://lib.sinaapp.com/js/bootstrap/2.0.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="http://staticlib.sinaapp.com/lib/js/jquery/jquery-ui-1.8.17.custom.css" type="text/css" />
    <script type="text/javascript">
      $(function() {
        //$('footer').setfooter();
      });
    </script>
    <!--[if IE]>
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-31932181-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>
  </head>
  <body>
    <?php startblock('navi') ?>
    <!-- 顶级导航 -->
    <div class="navbar navbar-fixed-top" style="display: none">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a>
          <a class="brand" href="#">简单问卷</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active">
                <a href="/index.php/page">主页</a>
              </li>
              <li class="login">
                <a href="/index.php/survey/create">创建问卷</a>
              </li>
              <li class="login">
                <a href="/index.php/page/mysurvey">我的问卷</a>
              </li>
            </ul>
            <!--
            <form class="form-search navbar-search pull-left">
                <input name="search" type="text" class="search-query span2" placeholder="search">
                <button type="submit" class="btn-small">搜索</button>
            </form>
            -->
            <ul class="nav pull-right">
              <li class="logout"><a href="/index.php/auth/login" class="">登录</a></li>
              <li class="logout"><a href="/index.php/auth/signup" class="">注册</a></li>
              <!--<li class="divider-vertical"></li>-->
              <li class="dropdown profile login">
                
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <!--End 顶级导航 -->

    <script type="text/template" id="profile_tmp">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <img src="#{this.src}" width="25" height="25"/>
        #{this.nickname}
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li><a href="#">查看个人资料</a></li>
        <li><a href="#">邀请朋友</a></li>
        <li class="divider"></li>
        <li><a href="/index.php/auth/logout">退出</a></li>
      </ul>
    </script>
    <?php endblock() ?>
    
    <div class="container" id="main">
      <?php emptyblock('container') ?>
    </div>

    <footer class="">
      <div class="container">
        <div class="row">
          <div class="power span10 offset1 clear">
            <p>感谢以下项目的支持</p>
            <a href="http://sae.sina.com.cn" target="_blank" title="Sina SAE"><img src="/static/imgs/power/sinasae.png" alt=""></a><br>
            <a href="http://backbonejs.org" target="_blank" title="BackboneJS"><img src="/static/imgs/power/backbone.gif" alt=""></a>
            <a href="http://documentcloud.github.com/underscore/" target="_blank" title="underscore"><img src="/static/imgs/power/underscore.gif" alt=""></a>
            <a href="http://jquery.com" target="_blank" title="jQuery"><img src="/static/imgs/power/jquery.gif" alt=""></a>
            <a href="http://jqueryui.com" target="_blank" title="jQuery UI"><img src="/static/imgs/power/jqueryui.gif" alt=""></a>
            <a href="http://twitter.github.com/bootstrap/" target="_blank" title="bootstrap"><img src="/static/imgs/power/bootstrap.gif" alt=""></a>
            <a href="http://lesscss.org/" target="_blank" title="lesscss"><img src="/static/imgs/power/less.png" alt=""></a>
            <br>
            <a href="http://codeigniter.com/" target="_blank" title="CodeIgniter"><img src="/static/imgs/power/codeigniter.gif" alt=""></a>
            <a href="http://www.phpactiverecord.org/" target="_blank" title="php.activerecord"><img src="/static/imgs/power/phpactiverecord.png" alt=""></a>
            <a href="http://www.denglu.cc/" target="_blank" title="灯鹭"><img src="/static/imgs/power/denglu.gif" alt=""></a>
            
          </div>
          <div class="author span10 offset1">
            <span>在这些地方可以找到作者: </span>
            <a href="http://weibo.com/alistapart" target="_blank">@燃辉</a> <iframe style="height: 24px; margin-bottom: -8px" width="63" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling="no" border="0" src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=63&height=24&uid=1313608362&style=1&btn=light&dpc=1"></iframe> |
            <a href="http://aisensiy.sinaapp.com" target="_blank">A.I</a> |
            <a href="http://www.renren.com/231261692" target="_blank">xushanchuan</a>
          </div>
          <div class="other span10 offset1">
            <p>bug反馈，建议提交请联系作者的微博或者邮箱aisensiy[at]163.com。谢谢支持:-)</p>
          </div>
          <div class="copyright span10 offset1">
            <p>Copyright &copy; 2012 简单问卷 simplesurvey All rights reserved.</p>
          </div>
      </div>
    </footer>
    
    <?php startblock('js') ?>
      <script src="/static/js/model.js" type="text/javascript" charset="utf-8"></script>
      <script src="/static/js/view.js" type="text/javascript" charset="utf-8"></script>
      <script type="text/javascript">
        window.navibar = new NaviBarView;
      </script>
    <?php endblock() ?>
  </body>
</html>