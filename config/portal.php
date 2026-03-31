<?php

return [
    'contact_form_recipients' => array_filter(array_map('trim', explode(',', (string) env('PORTAL_CONTACT_RECIPIENTS', '')))),
    'send_customer_credentials_email' => (bool) env('PORTAL_SEND_CUSTOMER_CREDENTIALS_EMAIL', false),
];
