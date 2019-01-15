<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/Logger.php';
require __DIR__ . '/autoload.php';

use PAMI\Client\Impl\ClientImpl as PamiClient;
use PAMI\Message\Event\EventMessage;

while (true) {

    $options = array(
        'host'            => 'localhost',
        'scheme'          => 'tcp://',
        'port'            => 5038,
        'username'        => 'magnus',
        'secret'          => 'magnussolution',
        'connect_timeout' => 10,
        'read_timeout'    => 10,
    );

    try {
        $pamiClient = new PamiClient($options);
        // Open the connection
        $pamiClient->open();
    } catch (Exception $e) {
        Logger::write($e->getMessage() . ' at line ' . $e->getLine());
        sleep(1);
        continue;
    }

    $pamiClient->registerEventListener(function (EventMessage $event) {
        /*
        QUEUE events
        QueueCallerAbandon
        QueueMemberAdded
        QueueMemberPaused
        QueueMemberPenalty
        QueueMemberRemoved
        QueueMemberRinginuse
        QueueMemberStatus
         */

        $eventType = $event->getKeys()['event'];

        $ignoreEvents = array(
            'RTCPReceived',
            'VarSet',
            'RTCPSent',
            'Newexten',
        );
        if (in_array($eventType, $ignoreEvents)) {
            return;
        }

        //print_r($event->getKeys());
        try {
            switch ($eventType) {
                case 'DialEnd':
                    checkPredictiveCallStatus($event);
                    break;
                case 'QueueMemberPause':
                    setQueueMemberStatus($event);
                    break;
                case 'QueueMemberStatus':
                    setQueueMemberStatus($event);
                    break;
                case 'QueueCallerJoin':
                    queueJoin($event);
                    break;
                case 'QueueCallerLeave':
                    queueLeave($event);
                    break;
                case 'AgentConnect':
                    agentConnect($event);
                    break;
                case 'PeerStatus':
                    peerStatus($event);
                    break;
                case 'QueueMemberAdded':
                    setQueueMemberStatus($event);
                    break;
                case 'DeviceStateChange':
                    setMemberStatus($event);
                    break;
            }
        } catch (Exception $e) {
            Logger::write($e->getMessage() . " at line " . $e->getLine());
        }
    });
    $running = true;
    // Main loop
    while ($running) {

        try {
            $pamiClient->process();
            usleep(1000);
        } catch (Exception $e) {
            Logger::write($e->getMessage() . ' at line ' . __LINE__);
            continue;
        }
    }
    // Close the connection
    $pamiClient->close();
}

function checkPredictiveCallStatus($event)
{
    //

    if (preg_match('/predictive/', $event->getKeys()['destaccountcode'])) {

        if (preg_match('/CONGESTION|NOANSWER|BUSY/', $event->getKeys()['dialstatus'])) {

            $data = explode('|', $event->getKeys()['destaccountcode']);

            if (file_exists("/var/spool/asterisk/outgoing_done/" . $data[1] . ".call")) {
                $fp = fopen("/var/spool/asterisk/outgoing_done/" . $data[1] . ".call", "r") or die("Unable to open file!");
            } else {
                sleep(2);

                if (file_exists("/var/spool/asterisk/outgoing_done/" . $data[1] . ".call")) {
                    $fp = fopen("/var/spool/asterisk/outgoing_done/" . $data[1] . ".call", "r") or die("Unable to open file!");
                } else {
                    return;
                }

            }

            $old_file = fread($fp, filesize("/var/spool/asterisk/outgoing_done/" . $data[1] . ".call"));
            fclose($fp);
            $old_file_array = preg_split('/\r\n|\r|\n/', $old_file);

            if (strlen($old_file_array[$data[2] + 14]) > 10) {
                $newNumber = explode('=', $old_file_array[$data[2] + 14]);
                if (!isset($newNumber[1]) || !is_numeric($newNumber[1])) {
                    $data[2]++;
                    $newNumber = explode('=', $old_file_array[$data[2] + 14]);
                    if (!isset($newNumber[1]) || !is_numeric($newNumber[1])) {
                        $data[2]++;
                        $newNumber = explode('=', $old_file_array[$data[2] + 14]);
                        if (!isset($newNumber[1]) || !is_numeric($newNumber[1])) {
                            $data[2]++;
                            $newNumber = explode('=', $old_file_array[$data[2] + 14]);
                            if (!isset($newNumber[1]) || !is_numeric($newNumber[1])) {
                                $data[2]++;
                                $newNumber = explode('=', $old_file_array[$data[2] + 14]);
                            }
                        }
                    }
                }
            }
            if (!isset($newNumber[1])) {
                echo "There not number to dial\n";
                $con = connectDB();
                $sql = "UPDATE pkg_phonenumber SET status = 1, id_category = 1, try = try + 1  WHERE id = " . $data[3];
                echo $sql . "\n";
                $commad = $con->prepare($sql);
                $commad->execute();
                return;
            }

            $trunk             = explode('@', $old_file_array[1]);
            $old_file_array[1] = 'Channel:SIP/' . $newNumber[1] . '@' . $trunk[1];
            $old_file_array[2] = 'CallerID:' . $newNumber[1];
            $old_file_array[3] = 'Account: predictive|' . $data[1] . '|' . ($data[2] + 1) . '|' . $data[3];
            $old_file_array[5] = 'Extension: ' . $newNumber[1];
            $old_file_array[7] = 'Set:CALLERID=' . $newNumber[1];
            $old_file_array[8] = 'Set:CALLED=' . $newNumber[1];

            unset($old_file_array[22]);
            unset($old_file_array[23]);
            unset($old_file_array[24]);

            $new_file = implode("\n", $old_file_array);

            exec("rm -rf /var/spool/asterisk/outgoing_done/" . $data[1] . ".call");

            $arquivo_call = "/tmp/" . $data[1] . ".call";

            $fp = fopen("$arquivo_call", "a+");
            fwrite($fp, $new_file);
            fclose($fp);

            system("mv $arquivo_call /var/spool/asterisk/outgoing/" . $data[1] . ".call");

            return;
        }

    }

}

function peerStatus($event)
{
    $con = connectDB();
    $sql = "UPDATE pkg_operator_status SET
            peer_status = '" . $event->getKeys()['peerstatus'] . "'
            WHERE id_user = (
                SELECT id_user FROM pkg_sip WHERE name ='" . substr($event->getKeys()['peer'], 4) . "'
                )";
    //echo $sql . "\n";
    $commad = $con->prepare($sql);
    $commad->execute();

}

function agentConnect($event)
{
    $con = connectDB();
    $sql = "UPDATE pkg_operator_status SET
            last_call_channel = '" . $event->getKeys()['channel'] . "',
            last_call_ringtime = '" . $event->getKeys()['ringtime'] . "',
            in_call = 1
            WHERE id_user = (
                SELECT id_user FROM pkg_sip WHERE name ='" . substr($event->getKeys()['membername'], 4) . "'
                )";
    $commad = $con->prepare($sql);

    $commad->execute();
}

function queueJoin($event)
{
    $con = connectDB();
    $sql = "INSERT pkg_queue_call_waiting (channel) VALUE
                (
                    '" . $event->getKeys()['channel'] . "'
                )";
    //echo $sql;
    $commad = $con->prepare($sql);
    $commad->execute();

}

function queueLeave($event)
{
    $con = connectDB();
    $sql = "DELETE FROM pkg_queue_call_waiting WHERE channel = '" . $event->getKeys()['channel'] . "'";
    //echo $sql;
    $commad = $con->prepare($sql);
    $commad->execute();

    if (isset($event->getKeys()['membername'])) {
        $sql = "UPDATE pkg_operator_status SET
            in_call = 0
            WHERE id_user = (
                SELECT id_user FROM pkg_sip WHERE name ='" . substr($event->getKeys()['membername'], 4) . "'
                )";
        $commad = $con->prepare($sql);

        $commad->execute();
    }
}

function connectDB()
{

    $configFile      = '/etc/asterisk/res_config_mysql.conf';
    $array           = parse_ini_file($configFile);
    $array['dbname'] = 'callcenter';
    try {
        $con = new PDO('mysql:host=localhost;dbname=' . $array['dbname'], $array['dbuser'], $array['dbpass']);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        Logger::write($e->getMessage() . ' at line ' . __LINE__);
        echo 'error DB connect';
        return;
    }
    return $con;
}

function setMemberStatus($event)
{
    $con = connectDB();
    $sql = "UPDATE pkg_operator_status SET time_free = '" . time() . "'
            WHERE id_user = (SELECT id_user FROM pkg_sip WHERE
            name = '" . substr($event->getKeys()['device'], 4) . "')";
    //echo $sql . "\n";
    $commad = $con->prepare($sql);
    try {
        $commad->execute();
    } catch (Exception $e) {
        //
    }
}

function setQueueMemberStatus($event)
{
    $con = connectDB();
    $sql = "UPDATE pkg_operator_status SET queue_status = '" . $event->getKeys()['status'] . "',
            queue_paused = '" . $event->getKeys()['paused'] . "' ,
            last_call = '" . $event->getKeys()['lastcall'] . "' ,
            calls_taken = '" . $event->getKeys()['callstaken'] . "',
            in_call = '" . $event->getKeys()['incall'] . "'
            WHERE id_user = (SELECT id_user FROM pkg_sip WHERE
            name = '" . substr($event->getKeys()['membername'], 4) . "')";
    //echo $sql . "\n";
    $commad = $con->prepare($sql);
    try {
        $commad->execute();
    } catch (Exception $e) {
        $sql = "INSERT pkg_operator_status (id_user, queue_status,queue_paused, last_call,calls_taken, in_call ) VALUE
                (
                    (SELECT id_user FROM pkg_sip WHERE name = '" . substr($event->getKeys()['membername'], 4) . "'),
                    " . $event->getKeys()['status'] . ",
                    " . $event->getKeys()['paused'] . ",
                    " . $event->getKeys()['lastcall'] . ",
                    " . $event->getKeys()['callstaken'] . ",
                    " . $event->getKeys()['incall'] . "
                )";
        //echo $sql;
        $commad = $con->prepare($sql);
        $commad->execute();
    }
}
