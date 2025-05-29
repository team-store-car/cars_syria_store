<?php

return [
    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'api_url' => env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions'), // تأكد من الـ URL الصحيح
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'), // أو المودل الذي تستخدمه
    ],

    'recommendation_limit' => env('AI_RECOMMENDATION_LIMIT', 5), // عدد التوصيات المراد إظهارها
];