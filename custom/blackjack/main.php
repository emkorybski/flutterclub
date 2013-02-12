<?php

session_start();

$uid = $_POST["userId"];
//echo $uid;
echo '<p style="color:#0b280b">Please wait. The game is loading...</p>';

 if($uid == 0 || $uid == ''){
       
        header('location: /fc/login');
   }

if(isset($uid)){
echo '
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- saved from url=(0014)about:internet -->
        <meta charset="utf-8">
        <title>HTML5 Blackjack</title>
        <link rel="stylesheet" href="Blackjack.css">
        <link rel="shortcut icon" href="/fc/custom/images/favicon.ico" type="image/x-icon">
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!--[if lt IE 8]>
            <link rel="stylesheet" href="Blackjack-ie.css">
        <![endif]-->
        <!--[if gt IE 8]><!-->
            <link rel="stylesheet" href="Blackjack-hide-from-ie.css">
        <!--<![endif]-->
        <script type="text/javascript">
            if (self != top)
                top.location = self.location;
        </script>
        <script src="cufon-yui.js" type="text/javascript"></script>
		<script src="CoffeeTin_400.font.js" type="text/javascript"></script>
		<script type="text/javascript">
			Cufon.replace("h1");
		</script>
    </head>
    <body>
       
        

        <div id="blackjack_wrapper">
            

<div class="csspop">
<a href="#">Click for TERMS AND CONDITIONS
            <span>
            <img src="delete.png" />
                <strong>Your account:</strong><br/>
You can use your FB$ to play blackjack. When you enter the game you decide how may FB$ to bring
to the table. These will then be deducted from your FB$ balance.
<br/>
<br/>
The maximum bet is FB$ 50 per hand – the same as on sports betting.
<br/>
<br/>
You must exit the game correctly for your winnings to be reflected in your account balance and on
the leader board P&L - do so by clicking <em>Exit game</em> button. We will not be refunding you if you make this mistake!
<br/>
<br/>
<strong>Basic Rules:</strong><br/>
The object of Blackjack is very simple: to achieve a total that is greater than that of the dealer, and
which does not exceed 21. Even if other players are present at the table, the dealer is your only
opponent in the game.
<br/>
<br/>
There are relatively few decisions to make when playing Blackjack. You must consider your cards and
your dealer cards and remember, if you go over 21, you <em>bust</em>, that means you lose.
<br/>
<br/>
<strong>Play progresses as follows:</strong><br/>
A card is dealt face to you and then one to the dealer. The dealer card is face down and called
the <em>hole</em> card.
<br/>
<br/>
A second card is then dealt, again face up, to you. You must then decide whether to draw further
cards.
<br/>
<br/>
The Dealer proceeds to draw cards to complete his/her hand.
<br/>
<br/>
You win if:
 - Your total is higher than the Dealer total
 - The Dealer goes over 21 or <em>busts</em> (provided you have not previously busted yourself).
 - If your total is the same as the Dealer total it is a <em>stand-off</em> and you neither win nor lose.
- If you go over 21, or the Dealer total is greater, you lose.
<br/>
<br/>
These rules remain largely unchanged (with some minor variations) whether your are playing online
blackjack or sitting at a table in a casino.
<br/>
<br/>
<strong>The Values of the Cards:</strong><br/>

Picture Cards (Jack, Queen and King) each count as 10 points.
An Ace counts as 1 point or 11 points, whichever is better for owner of the hand.
All other cards have their numerical face value.
<br/>
<br/>
<strong>What is a Blackjack?</strong><br/>
Blackjack is a combination of an Ace and any 10 or picture card with your first two cards. It pays one
and a half times your bet unless the dealer also draws Blackjack, in which case you have a <em>stand-
off</em>.
<br/>
<br/>
<strong>Restrictions on the Dealer:</strong><br/>

The dealer plays according to a strict set of rules. Dealers must take another card if their hand totals
16 or less. Dealers must stand (not take any more cards) if their hand totals 17 or more.
<br/>
<br/>
<strong>Splitting:</strong><br/>
If your first two cards are of equal value you may split these to form up to two separate hands. Aces
may be split to form only two hands. You will receive an additional card for each hand, however a
wager equal to your original bet must be placed each time you split.
<br/>
<br/>
<strong>Doubling Down:</strong><br/>
You may place an additional bet (not exceeding your original bet) after you receive your first two
cards total. You will be dealt one additional card when you double.
            </span> 
        </a>
        
        </div>
   
                <span class="normal" id="money_ded">Use your account money to play:</span>
             <!--   <input type="number" id="bal_ded" value="0" min="0">
                <button id="set_bal">Set balance for the game</button> -->
                
                <br/>
                
                <input type="number" id="bal_inc" value="" min="10" placeholder="Type amount">
                <button id="inc_bal">Deduct money from account</button>

        <h1><img src="fc_casino.png" style="margin-right:30px"/><span>Blackjack</span></h1>
        <div id="cards">
            <div id="dealer">
                <div class="label" id="dealers_cards">
                    <span class="caps">D</span>ealer&rsquo;s&nbsp;<span class="caps">C</span>ards<br>
                    <span class="caps">S</span>core:&nbsp;<output id="dealers_score"></output>
                </div>
                <div class="card show"><img id="dealer_card1" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card show"><img id="dealer_card2" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="dealer_card3" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="dealer_card4" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="dealer_card5" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="dealer_card6" src="images/75/back-blue.png" width="75" height="107"></div>
            </div>
            <div id="player">
                <div class="label" id="players_cards">
                    <span class="caps">P</span>layer&rsquo;s&nbsp;<span class="caps">C</span>ards<br>
                    <span class="caps">S</span>core:&nbsp;<output id="players_score"></output>
                </div>
                <div class="card show"><img id="player_card1" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card show"><img id="player_card2" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="player_card3" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="player_card4" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="player_card5" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="player_card6" src="images/75/back-blue.png" width="75" height="107"></div>
            </div>
            <div id="split_hand">
                <div class="label" id="split_cards">
                    <span class="caps">P</span>layer&rsquo;s&nbsp;<span class="caps">C</span>ards<br>
                    <span class="caps">S</span>core:&nbsp;<output id="split_score"></output>
                </div>
                <div class="card show"><img id="split_card1" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card show"><img id="split_card2" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="split_card3" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="split_card4" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="split_card5" src="images/75/back-blue.png" width="75" height="107"></div>
                <div class="card"><img id="split_card6" src="images/75/back-blue.png" width="75" height="107"></div>
            </div>
        </div>
        <div id="buttons">
            <button id="Deal">Deal</button>
            <button id="Stand" disabled>Stand</button>
            <button id="Hit" disabled>Hit</button>
            <button id="Split" disabled>Split</button>
            <button id="Double" disabled>Double</button>
        </div>
        <div id="money">
            <span class="normal" id="money_label">Your money:</span>
            <span class="amount" id="money_amount">0</span>
            <span class="normal" id="bet_label">Your bet:</span>
            <input type="number" id="bet_amount" value="10" min="1">
        </div>
        <div id="controls">
            <input type="checkbox" id="play_sounds" checked>
            Play Sounds <small>(Requires an HTML5 audio supporting browser)</small>
        </div>
        <div class="over" id="dealer_over"></div>
        <div class="over" id="player_over"></div>
        <div class="over" id="split_over"></div>
        <audio id="shuffle" preload="auto">
            <source src="audio/shuffle.ogg" type="audio/ogg">
            <source src="audio/shuffle.mp3" type="audio/mp3">
            <source src="audio/shuffle.wav" type="audio/wav">
        </audio>
        <audio id="card_drop" preload="auto">
            <source src="audio/cardSlap.ogg" type="audio/ogg">
            <source src="audio/cardSlap.mp3" type="audio/mp3">
            <source src="audio/cardSlap.wav" type="audio/wav">
        </audio>
        <audio id="win" preload="auto">
            <source src="audio/win.ogg" type="audio/ogg">
            <source src="audio/win.mp3" type="audio/mp3">
            <source src="audio/win.wav" type="audio/wav">
        </audio>
        <audio id="lose" preload="auto">
            <source src="audio/lose.ogg" type="audio/ogg">
            <source src="audio/lose.mp3" type="audio/mp3">
            <source src="audio/lose.wav" type="audio/wav">
        </audio>
       
        <script src="Blackjack.js" async></script>
       
        <!-- Start of StatCounter Code -->
        <script type="text/javascript">
            var sc_project=6279744;
            var sc_invisible=1;
            var sc_security="ad9b94e3";
        </script>
        <button id="exit">Exit the game</button>
        <p style="color:#efefef;border-top:1px solid #efefef"><em>ACKNOWLEDGEMENTS:<br/>
        <small>The core of Blackjack game logic belongs to the creator of <strong><a href="http://www.html5blackjack.net">html5blackjack.net</a></strong>.<br/>
        The card images used on this site were created by Jesse Fuchs and Tom Hart from free SVG images created by David Bellot, and are themselves available for use under a Creative Commons License. The backs were created using the Game of Life (generation 477 from a 2x2 square seed under rule 234/-) The sounds used on this site modified from samples originally created by Milton Paredes, Mfish Productions, and "deathpie", and are themselves available for use under a Creative Commons Sampling Plus 1.0 License. Fonts used on this site: Coffee Tin by Rick Mueller, Chrome Yellow by Nicks Fonts, College Bold by "I Shot The Serif", Comfortaa by Johan Aakerlund, Giro Light by Marcelo Magalhães and Pricedown Black by Larabie Fonts.</small> 
</em></p>
        <input type="hidden" id="userid" name="userid" value=' . $uid . ' />
        
        </div>
    </body>
 </html>';


 $_SESSION["uid"] = $uid;

} else {echo "Sorry, UID not set.";}

?>