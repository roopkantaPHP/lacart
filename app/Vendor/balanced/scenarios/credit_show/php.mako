%if mode == 'definition':
Balanced\Credit::get()

% else:
<?php

require(__DIR__ . '/vendor/autoload.php');

Httpful\Bootstrap::init();
RESTful\Bootstrap::init();
Balanced\Bootstrap::init();

Balanced\Settings::$api_key = "ak-test-25ZY8HQwZPuQtDecrxb671LilUya5t5G0";

$credit = Balanced\Credit::get("/credits/CR3yvp1R6162kK7MozoHmSkg");

?>
%endif