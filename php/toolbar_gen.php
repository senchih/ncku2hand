<?php
echo '
    <header class="toolbar wpcom-masterbar wpcom-forums wpcom-header" role="banner">
        <div class="wpcom-navigation site-navigation wpcom-nav" role="navigation">
            <h1 class="wpcom-title">
                <a href="index.php">
                    <span>NCKU2HAND</span>
                </a>
            </h1>
            <nav id="main-nav" class="wpcom-pages" role="navigation">
                <a class="menu-toggle">
                    Menu
                </a>
                <ul class="pages-menu">';

printButton("box.php", "圖片檢視", "PicView", $pageType=='box');
printButton("list.php", "清單檢視", "ListView", $pageType=='list');
printButton("box.php", "登出", "LogOut", false);

echo '
                </ul>
            </nav>
        </div>
    </header>';

function printButton($src, $title, $text, $current) {
    if($current) {
        $addClass = ' current';
    } else {
        $addClass = '';
    }
    echo '<li class="menu-btn'.$addClass.'"><a href="'.$src.'" title="'.$title.'">'.$text.'</a></li>';
}