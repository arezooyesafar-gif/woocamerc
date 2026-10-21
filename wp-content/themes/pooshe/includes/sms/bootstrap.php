<?php
require_once __DIR__ . '/lib/nusoap.php';
require_once __DIR__ . '/class-pooshe-sms-gateway.php';
require_once __DIR__ . '/class-pooshe-gateways.php';
require_once __DIR__ . '/class-pooshe-sender.php';
require_once __DIR__ . '/class-pooshe-otf.php';
require_once __DIR__ . '/class-pooshe-smart-auth.php';

foreach ( glob( __DIR__ . '/gateways/class-pooshe-gateway-*.php' ) as $file ) {
    require_once $file;
}
