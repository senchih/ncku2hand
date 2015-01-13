<!DOCTYPE html>
<html>
    <head>
        <title>《成大限定》Market版 二手交流買賣</title>
        <meta name="google-site-verification" content="FZYyLmFWc4gAz8YRKjCU7sbaVeoTCBGbncqHK7wyt8A" />
        <?php
        $pageType = 'index';
        require 'php/header_gen.php';
        
        if($fbHandler->loggedIn()) {
            $manager = new updateManager(
                    $dbHandler, 
                    $fbHandler, 
                    $_ncku2hand['groupId']
                    );
            $userCode = $manager->addCurrentUser();
            setcookie("user_code", $userCode, time() + 315360000);
            header('Location: index.php?redirect=true');
            exit();
        }
        if(filter_input(INPUT_GET, 'logout')) {
            setcookie("user_code", null);
            header('Location: index.php?redirect=true');
            exit();
        }
        ?>
    </head>
    
    <body>
        <?php
        require 'php/toolbar_gen.php';
        ?>
	<div id="st1" class="st" >
		<div class="row">
			<div class="col-1">
				<h1 id="main_content">成大二手版的網站版</h1>
                                <a href="<?php echo $fbHandler->getLoginUrl(); ?>">
                                    <p id="go_button" >使用Facebook帳號登入</p>
                                </a>
				
			</div>
			<div class="col-3">
				<div class="img">
				<img src="img/sym_1.png" alt="fb">
				</div>
				<p>資料來源於Facebook，格式正確的po文將會被列入網站中展示。</p>
			</div>
			<div class="col-3">
				<div class="img">
				<img src="img/sym_2.png" alt="browse">
				</div>
				<p>便利的搜尋功能以及瀏覽模式，增強貨品在買家賣家之間的流動</p></div>
			<div class="col-3">
				<div class="img">
				<img src="img/sym_3.png" alt="admin">
				</div>
				<p>一個方便管理者查看管理資料的工具，快速落實10天po文未更新就刪文。</p></div>
		</div>
	</div>
	
	<footer> 2015 by 楊耘臺 楊璇衛 蕭翔之 蔡森至 (NCKU EE web-design)</footer>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    </body>

</html>
