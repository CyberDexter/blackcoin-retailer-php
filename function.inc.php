<?php

include_once 'config.inc.php';
include_once 'coinkite.api.php';

$debug = false;

function GenerateVoucher($amount) {
    global $debug;
    global $ACCOUNT_ID;

    $new_voucher_data["account"] = $ACCOUNT_ID;
    $new_voucher_data["amount"] = 0.05;
    $new_voucher = "/v1/new/voucher";
    $new_voucher_result = api_query($new_voucher, $new_voucher_data);

    if ($debug)
        print_r($new_voucher_result);

    // Free accounts have 2s limit between API queries.. remove when using paid account!
    sleep(3);

    $CK_refnum = $new_voucher_result["result"]["CK_refnum"];
    $sign_voucher = "/v1/update/$CK_refnum/auth_send";
    $sign_voucher_data["authcode"] = $new_voucher_result["result"]["send_authcode"];
    $sign_voucher_result = api_query($sign_voucher, $sign_voucher_data);

    if ($debug)
        print_r($sign_voucher_result);

    $data["redeem_page"] = $sign_voucher_result["result"]["voucher"]["detail_page"];
    $data["redeem_code"] = $sign_voucher_result["result"]["voucher"]["pin_code"];

    return $data;
}

// print_r(GenerateVoucher("0.05"));
?>
