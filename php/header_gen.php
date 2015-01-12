<?php
// print general information
echo '
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset = "UTF-8"/>
        <meta name = "keywords" content="二手,以物易物,賣東西,成大,找東西,MARKET,撿便宜,送東西,轉讓,學生優惠" />
        <meta name = "description" content ="成大二手交易平台">
        <link rel="shortcut icon" href="favicon.ico">';

// print general stylesheet & js
echo '
        <link rel="stylesheet" href="css/main.css" type="text/css">
        <link rel="stylesheet" href="css/header.css" type="text/css">';
echo '
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../inc/config.js"></script>
        <script type="text/javascript" src="js/main.js"></script>';

// print stylesheet & js for each page
switch ($pageType) {
    case 'box':
        echo '
            <link rel="stylesheet" href="css/style.css">
            <link rel="stylesheet" href="css/box.css">
            <link rel="stylesheet" href="css/hover.css">
            <script type="text/javascript" src="js/box.js"></script>';
        break;
    case 'sort':
        echo '
                <link rel="stylesheet" href="css/forums.css" type="text/css">
                <script type="text/javascript" src="js/list.js"></script>';
        break;
    case 'list':
        echo '
                <link rel="stylesheet" href="css/forums.css" type="text/css">
                <script type="text/javascript" src="js/list.js"></script>';
        break;
}

// print fancybox plug-in if needed
if($pageType=='box' || $pageType=='sort') {
    echo '
            <link rel="stylesheet" href="../inc/src/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
            <script type="text/javascript" src="../inc/src/fancybox/jquery.fancybox.pack.js"></script>
            <link rel="stylesheet" href="../inc/src/fancybox/helpers/jquery.fancybox-thumbs.css" type="text/css" media="screen" />
            <script type="text/javascript" src="../inc/src/fancybox/helpers/jquery.fancybox-thumbs.js"></script>';
}