<?php

return [
    'name' => 'common',
    'modules' => [
        'enabled' => [
            'Profile',
            'Team',
            'UserInvitation',
        ],
        'classes' => [
            'Organisation' => 'Modules\Organisation\Providers\OrganisationServiceProvider',
            'OrganisationUserPermission' => 'Modules\OrganisationUserPermission\Providers\OrganisationUserPermissionServiceProvider',
            'Profile' => 'Modules\Profile\Providers\ProfileServiceProvider',
            'Team' => 'Modules\Team\Providers\TeamServiceProvider',
            'UserInvitation' => 'Modules\UserInvitation\Providers\UserInvitationServiceProvider',
        ]
    ]
];
