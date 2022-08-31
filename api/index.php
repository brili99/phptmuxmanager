<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();
include('../vendor/autoload.php');

use phpseclib3\Net\SSH2;

$_SESSION["host"] = isset($_REQUEST['host']) ? $_REQUEST['host'] : "localhost";
$_SESSION["port"] = isset($_REQUEST['port']) ? $_REQUEST['port'] : 22;

$ret = [];
if (
    isset($_REQUEST['user']) &&
    isset($_REQUEST['pass'])
) {
    $_SESSION["user_tmux"] = $_REQUEST['user'];
    $_SESSION["password_tmux"] = $_REQUEST['pass'];
}

if (
    !isset($_SESSION["user_tmux"]) ||
    !isset($_SESSION["password_tmux"])
) {
    $ret['msg'] = "login_first";
} else if (isset($_REQUEST['action'])) {
    $ssh = new SSH2($_SESSION["host"], $_SESSION["port"]);
    // if ($expected != $ssh->getServerPublicHostKey()) {
    //     throw new \Exception('Host key verification failed');
    // }
    if (!$ssh->login($_SESSION["user_tmux"], $_SESSION["password_tmux"])) {
        $ret['msg'] = "login_failed";
    } else {
        if ($_REQUEST['action'] == "testLogin") {
            $ret['msg'] = "success";
            // $ret['debug'] = $ssh->getServerPublicHostKey();
        } else if ($_REQUEST['action'] == "tmuxLs") {
            $ret['msg'] = "success";
            $fb = $ssh->exec('tmux ls');
            if (strpos($fb, "no server running")  !== false) {
                // $ret['data'] = "";
                $ret['msg'] = "empty";
            } else {
                $ret['data'] = explode("\n", $ssh->exec('tmux ls'));
            }
        } else if ($_REQUEST['action'] == "newSession") {
            if (isset($_REQUEST['nama_session'])) {
                $ret['msg'] = "success";
                $nama_session = preg_replace("/[^a-zA-Z0-9_]+/", "", $_REQUEST['nama_session']);
                $ret['data'] = $ssh->exec('tmux new -d -s ' . $nama_session);
            } else {
                $ret['msg'] = "missing params";
            }
        } else if ($_REQUEST['action'] == "closeSession") {
            if (isset($_REQUEST['nama_session'])) {
                $ret['msg'] = "success";
                $nama_session = preg_replace("/[^a-zA-Z0-9_]+/", "", $_REQUEST['nama_session']);
                $command = 'tmux kill-ses -t ' . $nama_session;
                $ssh->exec($command);
                // $ret['debug'] = $command;
            } else {
                $ret['msg'] = "missing params";
            }
        } else if ($_REQUEST['action'] == "executeCommand") {
            if (
                isset($_REQUEST['nama_session']) &&
                isset($_REQUEST['command'])
            ) {
                $ret['msg'] = "success";
                $nama_session = preg_replace("/[^a-zA-Z0-9_]+/", "", $_REQUEST['nama_session']);
                $command = "tmux send-keys -t " . $nama_session . " \"" . $_REQUEST['command'] . "\" ENTER";
                $ssh->exec($command);
                // $ret['debug'] = $command;
            } else {
                $ret['msg'] = "missing params";
            }
        } else if ($_REQUEST['action'] == "getOutputSession") {
            if (
                isset($_REQUEST['nama_session'])
            ) {
                $ret['msg'] = "success";
                $nama_session = preg_replace("/[^a-zA-Z0-9_]+/", "", $_REQUEST['nama_session']);
                $command = "tmux capture-pane -t " . $nama_session . " -p -S- -E-|sed '/^$/d'|tail -2000";
                $ret['data'] = $ssh->exec($command);
            } else {
                $ret['msg'] = "missing params";
            }
        }
    }
} else {
    die("bad request");
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($ret);
