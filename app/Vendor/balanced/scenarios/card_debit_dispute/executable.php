<?php

require(__DIR__ . '/vendor/autoload.php');

Httpful\Bootstrap::init();
RESTful\Bootstrap::init();
Balanced\Bootstrap::init();

Balanced\Settings::$api_key = "ak-test-25ZY8HQwZPuQtDecrxb671LilUya5t5G0";

$card = Balanced\Card::get("/cards/CC4wj9Lfvka6iodY7jzyqSHe");
$card->debits->create(array(
    "amount" => "5000",
    "appears_on_statement_as" => "Statement text",
    "description" => "Some descriptive text for the debit in the dashboard",
));

?>