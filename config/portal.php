<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Course Languages
    |--------------------------------------------------------------------------
    |
    | The codes a course can be "spoken" in. Used for the language badge on
    | course cards, the language switcher on detail / learn pages and as the
    | primary key of each course's `translations` JSON column.
    |
    */
    'supported_languages' => [
        'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr', 'flag' => '🇬🇧'],
        'ur' => ['name' => 'Urdu', 'native' => 'اردو', 'dir' => 'rtl', 'flag' => '🇵🇰'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'dir' => 'rtl', 'flag' => '🇸🇦'],
        'hi' => ['name' => 'Hindi', 'native' => 'हिन्दी', 'dir' => 'ltr', 'flag' => '🇮🇳'],
        'fr' => ['name' => 'French', 'native' => 'Français', 'dir' => 'ltr', 'flag' => '🇫🇷'],
        'es' => ['name' => 'Spanish', 'native' => 'Español', 'dir' => 'ltr', 'flag' => '🇪🇸'],
        'de' => ['name' => 'German', 'native' => 'Deutsch', 'dir' => 'ltr', 'flag' => '🇩🇪'],
    ],

];