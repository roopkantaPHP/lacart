%if mode == 'definition':
\Balanced\Dispute->all()

% else:
<?php

require(__DIR__ . '/vendor/autoload.php');

Httpful\Bootstrap::init();
RESTful\Bootstrap::init();
Balanced\Bootstrap::init();

Balanced\Settings::$api_key = "ak-test-25ZY8HQwZPuQtDecrxb671LilUya5t5G0";

$marketplace = Balanced\Marketplace::mine();
$disputes = $marketplace->disputes->query()->all();

?>
%endif