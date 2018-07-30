<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Console\Commands;

use App\Bots\TriviaBot;
use App\Game;
use App\Player;
use App\Question;
use App\QuestionSet;

$game = Game::first();
$timestamp = time();
if (($game->last_asked + $game->delay) <= $timestamp && $game->started == 1) {
    $game->last_asked = $timestamp;
    $game->round_month = date("n");
    $game->save();
    $bot = new TriviaBot("Trivia Bot");

    //check if there's a question being asked
    $question = $bot->getCurrentQuestion();
    if (!empty($question)) {
        $questiontext = $question->question;
        $hint = "*Hint {$question->current_hint}*: "; //this will hold our hint
        //get the (first) possible answer
        $answer = unserialize($question->answer)[0];
        $letters = str_split($answer);
        $previousLetter = " ";// the letter after a space are always shown pretend we're starting with one

        //show first letter of each word
        if ($question->current_hint == 1) {
            foreach ($letters as $letter) {
                if ($letter == " " || stripos("abcdefghijklmnopqrstuvwxyz1234567890", $letter) === false) {
                    $hint .= $letter;
                } else {
                    $hint .= "―";
                }
                $previousLetter = $letter;
            }
        } //show first 3 letters of first word
        elseif ($question->current_hint == 2) {
            foreach ($letters as $key => $letter) {
                if ($previousLetter == " " || $letter == " " || stripos("abcdefghijklmnopqrstuvwxyz1234567890", $letter) === false || $key <= 2) {
                    $hint .= $letter;
                } else {
                    $hint .= "―";
                }
                $previousLetter = $letter;
            }
        } //show all vowels
        elseif ($question->current_hint == 3) {
            foreach ($letters as $key => $letter) {
                if ($previousLetter == " " || $letter == " " || stripos("abcdefghijklmnopqrstuvwxyz1234567890", $letter) === false || $key <= 2 || stripos("aeiou", $letter) > -1) {
                    $hint .= $letter;
                } else {
                    $hint .= "―";
                }
                $previousLetter = $letter;
            }
        } else //by this time we just want to see the answer - no more clues!
        {
            $questiontext = "";
            $hint = "[b]Nobody got it![/b] The answer was [i]{$answer}[/i]";

            $question->current_hint = -1; // this gets incremented by 1 (to 0 - off) after these conditionals
            $game->questions_without_reply++;
            if ($game->questions_without_reply == 10) {
                $game->stopping = 1;
                $hint .= "\nNobody appears to be playing!";
            }
            if ($game->stopping == 1) {
                $game->started = 0;
                $game->stopping = 0;
                $hint .= "\n [b]GAME STOPPED[/b]";
            } else {
                $hint .= "\nNext question coming up...";
                //set up the next question
                $bot->start(); //this sets a random question's current_hint to 1
            }
        }
        $game->save();

        $question->current_hint++;
        $question->save();

        //send the question and hint/answer to channel
        $message = "{$questiontext}\n{$hint}";
        echo "Messsage: $message";
        $url = SLACK_INCOMING_WEBHOOK_URL;
        $data = ['payload' => $bot->sendMessageToChannel($message)];

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
    } else {
        $bot->start();
    }
}