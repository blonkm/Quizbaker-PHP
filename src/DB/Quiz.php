<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * save responses from a student on a quiz to database
 */
class Quiz {

    public $quizId;
    public $summaryId;
    public $username;
    public $title;

    public function getDB() {
        // connect
        $db = new Database();
        $db->connect();
        return $db;
    }

    public function getName($id) {
        $db = $this->getDB();

        // retrieve quiz record
        $sql = "SELECT quiz_name from quiz WHERE id=" . intval($id);
        $rs = $db->getRs($sql);
        $row = $rs->fetch();
        return $row->quiz_name;
    }

    public function getUser() {
        $q = Req('quiz');

        // create user record with summary of results
        // $user = $q['oOptions']['strName'];
        $user = Svr('LOGON_USER');
        if (empty($user)) {
            $user = $q['oOptions']['strName'];
        }
        if (empty($user)) {
            $user = Svr('REMOTE_ADDR');
        }
        return $user;
    }

    // if quiz already exists, find it's id
    function getQuiz($name = '') {
        $db = $this->getDB();

        $sql = "SELECT * from quiz WHERE quiz_name='{$name}'";
        $rs = $db->query($sql);
        $row = $rs->fetch();

        return $row->id;
    }

    function getSummary($id) {
        $db = $this->getDB();

        $sql = "SELECT * from quiz_summary WHERE id='{$id}'";
        $rs = $db->query($sql);
        $row = $rs->fetch();

        return $row;
    }

    // if user already exists, find id
    function getDetails($summaryId) {
        $db = $this->getDB();

        $sql = "SELECT * from quiz_detail WHERE summary_id=@summaryId";
        $sql = str_replace("@summaryId", $summaryId, $sql);
        $row = $db->query($sql);
        return $row->id;
    }

    public function save() {
        $q = Req('quiz');
        $name = $q['strTitle'];
        $this->title = $name;
        $this->username = $this->getUser();

        // find or create quiz
        $quizId = $this->getQuiz($name);
        if ($quizId < 0) {
            $quizId = $this->createQuiz();
        }
        $this->quizId = $quizId;
        $this->summaryId = $this->createSummary();
        $this->detailId = $this->createDetails();
        
        return true;
    }

    function createQuiz() {
        $db = $this->getDB();

        // create quiz record
        $q = Req("quiz");
        $this->title = $q['strTitle'];
        $sql = "INSERT IGNORE INTO quiz VALUES(NULL, '{$name}', 'final')";
        $quizId = $db->insert($sql) || $this->getQuiz($name);
        return $quizId;
    }

    function createSummary() {
        $q = Req('quiz');

        $db = $this->getDB();
        $sql = 'INSERT IGNORE INTO quiz_summary values (NULL, ' 
                . $this->quizId . 
                ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);';

        $id = $db->insert($sql);
        if ($id > 0) {
            $data = array();
            $segments = explode('\\', $this->username);
            if (count($segments)>1) {
                $data['domain'] = array_shift($segments);
                $data['network_id'] = array_shift($segments);
            }
            else
            {
                $data['domain'] = '';
                $data['network_id'] = $this->username;  
            }
            $data['status'] = $q['strResult'];
            $data['raw_score'] = $q['strScore'];
            $data['passing_score'] = $q['strPassingScore'];
            $data['max_score'] = $q['strMaxScore'];
            $data['min_score'] = $q['strMinScore'];
            $data['class'] = '';        
            $data['time'] = formatDateTime(now());
            $sql = "UPDATE quiz_summary SET
                    domain = :domain,
                    network_id = :network_id,
                    status = :status,
                    raw_score = :raw_score,
                    passing_score = :passing_score,
                    max_score = :max_score,
                    min_score = :min_score,
                    time = :time,
                    class = :class
                    WHERE id = $id";
            $db->executePrepared($sql, $data);
        }
        return $id;
    }
    
    /**
     * Save details of answer in DB
     * @see http://www.articulate.com/support/quizmaker-09/quiz-data-sent-to-an-lms-in-articulate-quizmaker-09
     * 
     * @return type
     */
    function createDetails() {
        $db = $this->getDB();

        $q = Req('quiz');
        $responses = Req('responses');

        // create user record with summary of results
        $sqlInsert =  'INSERT IGNORE INTO quiz_detail values (NULL, ' . 
                $this->summaryId . 
                ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)';

        foreach ($responses as $response) {
            $id = $db->insert($sqlInsert);
            if ($id > 0) {
                $data = array();
                $data['lastmodified'] = formatDateTime(now());
                $data['timestamp'] = formatDateTime(now());
                $data['score'] = '10'; // $response['nPoints'];
                $data['question'] = $response['strQuestion'];
                $data['interaction_id'] = $response['strInteractionId'];
                $data['interaction_type'] = $response['strType'];
                $data['objective_id'] = $response['strObjectiveId'];
                $data['question_num'] = $this->getQuestionNumber($data['objective_id'], $data['interaction_id']);
                $data['student_response'] = $response['strStudentResponse'];
                $data['result'] = $response['strResult'];
                $data['latency'] = $response['strLatency'];
                $sqlUpdate = "UPDATE quiz_detail SET    
                        lastmodified = :lastmodified,
                        timestamp = :timestamp,
                        score = :score,
                        question = :question,
                        interaction_id = :interaction_id,
                        interaction_type = :interaction_type,
                        objective_id = :objective_id,
                        question_num = :question_num,
                        student_response = :student_response,
                        result = :result,
                        latency = :latency
                        WHERE id = $id";
                $db->executePrepared($sqlUpdate, $data);
            }
        }
        return true;
    }

    /**
     * objective id can give the question id
     * objective id is in the form 'Question<N>_<A>'
     * <N> = question number
     * <A> = attempt number 
     * except when restarting a quiz and Flash can't recover the data
     * in that case a different format is used
     * 
     * @param string $objectiveId
     * @return int
     */
    function getQuestionNumber($objectiveId, $interactionId) {
        if ($objectiveId === $interactionId) {
            return null;
        }
        $qId1 = str_replace('Question', '', $objectiveId);
        $qId2 = substr($qId1, 0, -2);
        // $qId3 = int($qId2); // don't do this because of the alternate format
        return $qId2;
    }
    
}

?>
