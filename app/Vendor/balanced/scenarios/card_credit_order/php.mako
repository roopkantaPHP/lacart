%if mode == 'definition':
Balanced\Order->creditTo()

% else:
<?php

require(__DIR__ . '/vendor/autoload.php');

Httpful\Bootstrap::init();
RESTful\Bootstrap::init();
Balanced\Bootstrap::init();

Balanced\Settings::$api_key = "ak-test-25ZY8HQwZPuQtDecrxb671LilUya5t5G0";

$order = Balanced\Order::get("/orders/OR2UWXCNY2nKlqIQhQhWN3Jm");
$card = Balanced\Card::get("/cards/CC3IBNr3erYpVuuZDyWNFfet");
$order->creditTo(
    $card,
    "5000"
);

?>
%endif