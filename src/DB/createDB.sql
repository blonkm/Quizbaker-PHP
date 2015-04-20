-- * This Source Code Form is subject to the terms of the Mozilla Public
-- * License, v. 2.0. If a copy of the MPL was not distributed with this file,
-- * You can obtain one at http://mozilla.org/MPL/2.0/. */

-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2014 at 06:36 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: 'quizbaker'
--

-- --------------------------------------------------------

--
-- Table structure for table 'docenten'
--

CREATE TABLE IF NOT EXISTS docenten (
  ID varchar(50) NOT NULL,
  Afkorting varchar(255) DEFAULT NULL,
  Voornaam varchar(255) DEFAULT NULL,
  Achternaam varchar(255) DEFAULT NULL,
  PRIMARY KEY (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'log'
--

CREATE TABLE IF NOT EXISTS log (
  id int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  message longtext,
  student_id varchar(50) DEFAULT NULL,
  attachment longtext,
  class varchar(255) DEFAULT NULL,
  docent varchar(50) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY IX_Log (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Table structure for table 'quiz'
--

CREATE TABLE IF NOT EXISTS quiz (
  id int(11) NOT NULL AUTO_INCREMENT,
  quiz_name varchar(100) DEFAULT NULL,
  quiz_type varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=197 ;

-- --------------------------------------------------------

--
-- Table structure for table 'quiz_detail'
--

CREATE TABLE IF NOT EXISTS quiz_detail (
  id int(11) NOT NULL AUTO_INCREMENT,
  summary_id int(11) NOT NULL,
  lastmodified datetime DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  score varchar(50) DEFAULT NULL,
  interaction_id varchar(50) DEFAULT NULL,
  objective_id varchar(50) DEFAULT NULL,
  interaction_type varchar(50) DEFAULT NULL,
  student_response longtext,
  result varchar(50) DEFAULT NULL,
  weight varchar(50) DEFAULT NULL,
  latency varchar(50) DEFAULT NULL,
  question longtext,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48156 ;

-- --------------------------------------------------------

--
-- Table structure for table 'quiz_summary'
--

CREATE TABLE IF NOT EXISTS quiz_summary (
  id int(11) NOT NULL AUTO_INCREMENT,
  quiz_id int(11) DEFAULT NULL,
  lastmodified datetime DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  domain varchar(50) DEFAULT NULL,
  network_id varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  raw_score varchar(50) DEFAULT NULL,
  passing_score varchar(50) DEFAULT NULL,
  max_score varchar(50) DEFAULT NULL,
  min_score varchar(50) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL,
  class varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2051 ;

-- --------------------------------------------------------

--
-- Table structure for table 'students'
--

CREATE TABLE IF NOT EXISTS students (
  last_name varchar(255) DEFAULT NULL,
  first_name varchar(255) DEFAULT NULL,
  class varchar(255) DEFAULT NULL,
  id varchar(255) DEFAULT NULL,
  department varchar(255) DEFAULT NULL,
  photo varchar(255) DEFAULT NULL,
  UNIQUE KEY idxNummer (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'sysdiagrams'
--

CREATE TABLE IF NOT EXISTS sysdiagrams (
  `name` varchar(160) NOT NULL,
  principal_id int(11) NOT NULL,
  diagram_id int(11) NOT NULL AUTO_INCREMENT,
  version int(11) DEFAULT NULL,
  definition longblob,
  PRIMARY KEY (diagram_id),
  UNIQUE KEY UK_principal_name (principal_id,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view 'vwdetails'
--
CREATE TABLE IF NOT EXISTS `vwdetails` (
`quiz_id` int(11)
,`id` int(11)
,`summary_id` int(11)
,`lastmodified` datetime
,`timestamp` datetime
,`score` varchar(50)
,`interaction_id` varchar(50)
,`objective_id` varchar(50)
,`question_num` varchar(5)
,`interaction_type` varchar(50)
,`student_response` longtext
,`result` varchar(50)
,`weight` varchar(50)
,`latency` varchar(50)
,`question` longtext
,`raw_score` varchar(50)
,`quiz_name` varchar(100)
,`last_name` varchar(255)
,`first_name` varchar(255)
,`class` varchar(255)
,`student_id` varchar(255)
,`photo` varchar(255)
,`current_class` varchar(50)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view 'vwsummary'
--
CREATE TABLE IF NOT EXISTS `vwsummary` (
`id` int(11)
,`quiz_id` int(11)
,`lastmodified` timestamp
,`network_id` varchar(50)
,`status` varchar(50)
,`raw_score` varchar(50)
,`passing_score` varchar(50)
,`max_score` varchar(50)
,`min_score` varchar(50)
,`time` varchar(50)
,`last_name` varchar(255)
,`first_name` varchar(255)
,`student_id` varchar(255)
,`department` varchar(255)
,`photo` varchar(255)
,`quiz_name` varchar(100)
,`current_class` varchar(50)
,`class` varchar(255)
);
-- --------------------------------------------------------

--
-- Structure for view 'vwdetails'
--
DROP TABLE IF EXISTS `vwdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=root@localhost SQL SECURITY DEFINER VIEW quizbaker.vwdetails AS select quizbaker.quiz.id AS quiz_id,quizbaker.quiz_detail.id AS id,quizbaker.quiz_detail.summary_id AS summary_id,quizbaker.quiz_detail.lastmodified AS lastmodified,quizbaker.quiz_detail.`timestamp` AS `timestamp`,quizbaker.quiz_detail.score AS score,quizbaker.quiz_detail.interaction_id AS interaction_id,quizbaker.quiz_detail.objective_id AS objective_id, quiz_detail.question_num AS question_num,quizbaker.quiz_detail.interaction_type AS interaction_type,quizbaker.quiz_detail.student_response AS student_response,quizbaker.quiz_detail.result AS result,quizbaker.quiz_detail.latency AS latency,quizbaker.quiz_detail.question AS question,quizbaker.quiz_summary.raw_score AS raw_score,quizbaker.quiz.quiz_name AS quiz_name,quizbaker.students.last_name AS last_name,quizbaker.students.first_name AS first_name,quizbaker.students.class AS class,quizbaker.students.id AS student_id,quizbaker.students.photo AS photo,quizbaker.quiz_summary.class AS current_class from (((quizbaker.quiz join quizbaker.quiz_summary on((quizbaker.quiz.id = quizbaker.quiz_summary.quiz_id))) join quizbaker.quiz_detail on((quizbaker.quiz_summary.id = quizbaker.quiz_detail.summary_id))) join quizbaker.students on((quizbaker.quiz_summary.network_id = quizbaker.students.id)))


-- --------------------------------------------------------

--
-- Structure for view 'vwsummary'
--
DROP TABLE IF EXISTS `vwsummary`;

CREATE ALGORITHM=UNDEFINED DEFINER=root@localhost SQL SECURITY DEFINER VIEW quizbaker.vwsummary AS select quizbaker.quiz_summary.id AS id,quizbaker.quiz_summary.quiz_id AS quiz_id,quizbaker.quiz_summary.lastmodified AS lastmodified,quizbaker.quiz_summary.network_id AS network_id,quizbaker.quiz_summary.`status` AS `status`,quizbaker.quiz_summary.raw_score AS raw_score,quizbaker.quiz_summary.passing_score AS passing_score,quizbaker.quiz_summary.max_score AS max_score,quizbaker.quiz_summary.min_score AS min_score,quizbaker.quiz_summary.`time` AS `time`,quizbaker.students.last_name AS last_name,quizbaker.students.first_name AS first_name,quizbaker.students.id AS student_id,quizbaker.students.department AS department,quizbaker.students.photo AS photo,quizbaker.quiz.quiz_name AS quiz_name,quizbaker.quiz_summary.class AS current_class,quizbaker.students.class AS class from ((quizbaker.quiz_summary join quizbaker.students on((quizbaker.quiz_summary.network_id = quizbaker.students.id))) join quizbaker.quiz on((quizbaker.quiz_summary.quiz_id = quizbaker.quiz.id)));
