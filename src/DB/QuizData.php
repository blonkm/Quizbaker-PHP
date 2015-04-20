<?php

/* This Source Code Form is subject to the terms of the Mozilla public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

// classes: options, score and response
Class QuizOptions {

    public $showUserScore;
    public $showPassingScore;
    public $showShowPassFail;
    public $showQuizReview;
    public $result;
    public $username;

    function toString() {
        $out = "";
        $out .= "showUserScore: " . $this->showUserScore . "<br/>";
        $out .= "showPassingScore:" . $this->showPassingScore . "<br/>";
        $out .= "showShowPassFail:" . $this->showShowPassFail . "<br/>";
        $out .= "showQuizReview:" . $this->showQuizReview . "<br/>";
        $out .= "result: " . $this->result . "<br/>";
        $out .= "username:" . $this->username . "<br/>";
        return $out;
    }

}

Class QuizScore {

    public $result;
    public $score;
    public $passingScore;
    public $minScore;
    public $maxScore;
    public $ptScore;
    public $ptMax;

    function toString() {
        $out = "";
        $out .= "result: " . $this->result . "<br/>";
        $out .= "score:" . $this->score . "<br/>";
        $out .= "passingScore:" . $this->passingScore . "<br/>";
        $out .= "minScore:" . $this->minScore . "<br/>";
        $out .= "maxScore: " . $this->maxScore . "<br/>";
        $out .= "ptScore:" . $this->ptScore . "<br/>";
        $out .= "ptMax:" . $this->ptMax . "<br/>";
        return $out;
    }

}

Class QuizResponse {

    public $questionNum;
    public $question;
    public $correctResponse;
    public $studentResponse;
    public $result;
    public $points;
    public $found;
    public $interactionId;
    public $objectiveId;
    public $questionType;
    public $latency;

    function toString() {
        $out = "";
        $out .= "questionNum: " . questionNum . "<br/>";
        $out .= "question:" . question . "<br/>";
        $out .= "correctResponse:" . correctResponse . "<br/>";
        $out .= "studentResponse:" . studentResponse . "<br/>";
        $out .= "result: " . result . "<br/>";
        $out .= "points:" . points . "<br/>";
        $out .= "found:" . found . "<br/>";
        $out .= "interactionId:" . interactionId . "<br/>";
        $out .= "objectiveId:" . objectiveId . "<br/>";
        $out .= "questionType:" . questionType . "<br/>";
        $out .= "latency:" . latency . "<br/>";
        return $out;
    }

}

?>
