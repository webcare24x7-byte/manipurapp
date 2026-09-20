<?php

declare(strict_types=1);

namespace App\Modules\Packages\Data\Lookup;

final class LeadershipLookupDefaults
{
    /*
    |--------------------------------------------------------------------------
    | Leadership Lookup Package
    |--------------------------------------------------------------------------
    |
    | Package Code
    |
    | lookup.leadership
    |
    |--------------------------------------------------------------------------
    |
    | Lookup Types Included
    |
    | ✓ Appointment Types
    | ✓ Appointment Status
    | ✓ Term Types
    | ✓ Removal Reasons
    |
    |--------------------------------------------------------------------------
    */

    public const LOOKUPS = [

        /*
        |--------------------------------------------------------------------------
        | Appointment Types
        |--------------------------------------------------------------------------
        */

        'appointment_type' => [

            'name' => 'Appointment Types',

            'description' => 'Types of leadership appointments.',

            'values' => [

                [

                    'name' => 'Appointment',

                    'slug' => 'appointment',

                    'display_order' => 1,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Election',

                    'slug' => 'election',

                    'display_order' => 2,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Interim',

                    'slug' => 'interim',

                    'display_order' => 3,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Acting',

                    'slug' => 'acting',

                    'display_order' => 4,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Nomination',

                    'slug' => 'nomination',

                    'display_order' => 5,

                    'is_default' => 1,

                ],

            ],

        ],

        /*
        |--------------------------------------------------------------------------
        | Appointment Status
        |--------------------------------------------------------------------------
        */

        'appointment_status' => [

            'name' => 'Appointment Status',

            'description' => 'Current status of leadership appointments.',

            'values' => [

                [

                    'name' => 'Active',

                    'slug' => 'active',

                    'display_order' => 1,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Pending',

                    'slug' => 'pending',

                    'display_order' => 2,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Completed',

                    'slug' => 'completed',

                    'display_order' => 3,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Resigned',

                    'slug' => 'resigned',

                    'display_order' => 4,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Removed',

                    'slug' => 'removed',

                    'display_order' => 5,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Retired',

                    'slug' => 'retired',

                    'display_order' => 6,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Expired',

                    'slug' => 'expired',

                    'display_order' => 7,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Deceased',

                    'slug' => 'deceased',

                    'display_order' => 8,

                    'is_default' => 1,

                ],

            ],

        ],

                /*
        |--------------------------------------------------------------------------
        | Term Types
        |--------------------------------------------------------------------------
        */

        'term_type' => [

            'name' => 'Term Types',

            'description' => 'Leadership appointment term types.',

            'values' => [

                [

                    'name' => 'Permanent',

                    'slug' => 'permanent',

                    'display_order' => 1,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Fixed Term',

                    'slug' => 'fixed_term',

                    'display_order' => 2,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Temporary',

                    'slug' => 'temporary',

                    'display_order' => 3,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Interim',

                    'slug' => 'interim',

                    'display_order' => 4,

                    'is_default' => 1,

                ],

            ],

        ],

        /*
        |--------------------------------------------------------------------------
        | Removal Reasons
        |--------------------------------------------------------------------------
        */

        'removal_reason' => [

            'name' => 'Removal Reasons',

            'description' => 'Reasons for ending leadership appointments.',

            'values' => [

                [

                    'name' => 'Resigned',

                    'slug' => 'resigned',

                    'display_order' => 1,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Retired',

                    'slug' => 'retired',

                    'display_order' => 2,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Transferred',

                    'slug' => 'transferred',

                    'display_order' => 3,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Promoted',

                    'slug' => 'promoted',

                    'display_order' => 4,

                    'is_default' => 1,

                ],

                [

                    'name' => 'End of Term',

                    'slug' => 'end_of_term',

                    'display_order' => 5,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Removed',

                    'slug' => 'removed',

                    'display_order' => 6,

                    'is_default' => 1,

                ],

                [

                    'name' => 'Deceased',

                    'slug' => 'deceased',

                    'display_order' => 7,

                    'is_default' => 1,

                ],

            ],

        ],

    ];
}