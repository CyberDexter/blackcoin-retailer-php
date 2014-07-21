<?php

#############################
#                           #
#   COINKITE API WRAPPER    # 
#                           #
#############################

/* Include configuration file */
include_once 'config.inc.php';

/*
 * Coinkite API Query
 */

function api_query($method, $parameters = null) {
    global $API_KEY;
    $sign = sign($method);
    // generate the extra headers
    $headers = array(
        'X-CK-Key: ' . $API_KEY,
        'X-CK-Sign: ' . $sign[0],
        'X-CK-Timestamp: ' . $sign[1]
    );
    // our curl handle (initialize if required)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.coinkite.com$method");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($parameters != null) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
    }
    // run the query
    $res = curl_exec($ch);
    $dec = json_decode($res, true);
    return $dec;
}

/*
 * Sign Coinkite request
 */

function sign($endpoint, $force_ts = false) {
    global $API_SECRET;
    if ($force_ts) {
        $ts = $force_ts;
    } else {
        $now = new DateTime();
        $ts = $now->format(DateTime::ISO8601);
    }
    $data = $endpoint . '|' . $ts;
    $hm = hash_hmac('sha256', $data, $API_SECRET);
    return array($hm, $ts);
}

?>
