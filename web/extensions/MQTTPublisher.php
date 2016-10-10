<?php
    require("Credentials.php");
    require("../phpMQTT/phpMQTT.php");

    function exitWithErrorNewline($text) {
        exit("<b>ERROR:</b> " . $text . "<br />");
    }

    parse_str($_SERVER['QUERY_STRING']);

    if (empty($topic)) {
        exitWithErrorNewline("No topic provided!\n");
        exit(1);
    }

    if (empty($message)) {
        exitWithErrorNewline("No message provided!\n");
        exit(1);
    }

    $mqtt = new phpMQTT($MQTT_HOST, $MQTT_PORT, "ClientID".rand()); 

    if ($mqtt->connect(true, null, $MQTT_USERNAME, $MQTT_PASSWORD)){
        $mqtt->publish($topic, $message, 0);
        $mqtt->close();
    } else {
        exitWithErrorNewline("Fail or time out");
    }
?>