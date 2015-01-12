<!DOCTYPE html>
<html>
    <head>
        <title>《成大限定》Market版 二手交流買賣</title>
        <?php
        $pageType = 'list';
        require 'php/header_gen.php';
        ?>
    </head>
    
    <body id="front-page">
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : _ncku2hand.appId,
                    xfbml      : false,
                    version    : 'v2.2'
                });
                FB.getLoginStatus(function(response) {
                    if (response.status === 'connected') {
                        resetTail();
                        appendItems(0);
                        $("#front-search .inputButton").click(doSearch);
                        FB.api('/10204480122937657', function(response) {
                            $(".iattr1").each(function(){
                                var $each = $(this);
                                FB.api('/'+$each.text(), function(response) {
                                    $each.replaceWith('<td class="iattr1">'+response.name+'</td>');
                                });
                            });
                        });
                    } else {
                        document.location.href="error.php?err=101";
                    }
                });
            };
            
            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/zh_TW/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        
        <?php
        require 'php/toolbar_gen.php';
        ?>
        
        <!-- topbar head-->
        <div id="header" class="logged-out">
            <div style="max-width: 100%; width: 100px;">
                <img src="img/1.png" alt="自己的" style="width: 30%;">
                <img src="img/2.png" alt="東西" style="width: 30%;">
                <img src="img/3.png" alt="自己" style="width: 20%;">
                <img src="img/4.png" alt="買" style="width: 15%;">
            </div>
        </div>
        <!-- topbar end-->
        
        <!-- content head-->
        <div id="wrapper" class="pagebody">
            <div id="content" class="widecolumn">
                <div id="notes">
                    <p>
                        Find Your Treasure 
                    </p>
                    <form id="front-search">
                        <p>
                            <input class="text" size="30" maxlength="100" type="text">
                            <input value="Search" class="inputButton" type="button">
                        </p>
                    </form>
                </div>
                <div class="threequarters">
                    <table class="widefat">
                        <!-- item head-->
                        <thead>
                            <tr class="head">
                                <th style="width: 20%;">Seller</th>
                                <th style="width: 40%;">Item ID</th>
                                <th style="width: 20%;">Update time</th>
                                <th style="width: 20%;">Create time</th>
                            </tr>
                        </thead>
                        <tbody id="list"><!-- ajax will fill here--></tbody>
                        <!-- item end-->
                    </table>
                </div>
                <div id="list-tail">
                    <p></p>
                </div>
            </div>
            
        </div>
        <!-- content end-->
        <div id="fb-root"></div>
    </body>

</html>