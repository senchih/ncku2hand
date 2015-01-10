<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="utf-8" />
        <title>Main</title>
    </head>
    <body>
        <?php
        // include libs
        register_shutdown_function( "fatal_handler" );
        function fatal_handler() {
            print_r(error_get_last());
        }
        
        // initialize
        ?>
        
        <a href="box.html">box</a><br>
        <a href="list.html">list</a><br>
        <a href="index.php?clear=true">clear data(!!!NOT REVERSABLE!!!)</a><br>
        <br>
        <a href="http://localhost/phpmyadmin/">Database UI</a><br>
    </body>
</html>
