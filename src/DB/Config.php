<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

function Svr($param, $default = null) {
    return (string) filter_input(INPUT_SERVER, $param);
}

function isLocal() {
    return PHP_SAPI or ( Svr('REMOTE_ADDR') == '127.0.0.1');
}

Class Config {

    public static $dbname = "quizbaker";
    public static $dbuser = "root";
    public static $dbpassword = "";
    public static $dbserver = "127.0.0.1";
    public static $dbtype = "MYSQL"; // MYSQL | MSSQL | MSACCESS
    public static $dbfile = ""; // not used for mysql
    public static $useDomain = false;
    public static $domain = "www.qm.local";
    public static $mail_ssl = true;
    public static $smtp_ssl = true;
    public static $smtp_port = 465;
    public static $smtp_server = "<smtp server name>";
    public static $smtp_user = "<email user name>";
    public static $smtp_pass = "<email user password>";
    public static $smtp_auth = 1; //true
    public static $smtp_from = "QuizBaker System <anonymous@example.com>";
    public static $smtp_copyto = "anonymous@example.com";
    public static $smtp_domain = "example.com";

    public static function root() {
        return Svr('DOCUMENT_ROOT');
    }

    public static function domain() {
        return Svr('HTTP_HOST') or Svr('SERVER_NAME');
    }

}

if (!isLocal()) {
    Config::$dbname = "rubiksc1_quizbaker";
    Config::$dbuser = "rubiksc1_cube";
    Config::$dbpassword = "P@ssw0rd";
    Config::$dbserver = "127.0.0.1";
}
?>
