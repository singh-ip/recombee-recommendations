<?php

return [
    'name' => 'Organisation',

    // enabling this flag will make SetOrganisationContext middleware run every time `auth` middleware is used
    'replace_auth_middleware' => false,

    // enabling this flag will make SetOrganisationContext middleware check if Organisation exists
    'validate_org_context' => true,
];
