<?php

    //require_once(PATH_DOMAIN . "blackjack.php");
    require_once('blackjack.php');
    
    $game_inst = new Blackjack();
    
    $echo_test = $game_inst->test();
    
    echo $echo_test;
?>
