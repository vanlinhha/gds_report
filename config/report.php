<?php

return [
    'download_report' => [
        'unique_fields' => ['date'],  // các trường riêng biệt của 1 bản ghi
        'api' => '/api/v1/report/date/download?range=d', // api endpoint
        'attribute_mapping' => [ // xác định các trường và kiểu dữ liệu của mỗi trường
            'date' => 'date',
            'count' => 'unsignedInteger'
        ],
        'data_field' => 'items' // trường dữ liệu chứa danh sách các bản ghi từ API, mặc định là `items`
    ],
    
    'upload_report' => [
        'unique_fields' => ['uploaded_at'],
        'api' => '/api/v1/upload_report?range=d',
        'attribute_mapping' => [
            "uploaded_at" => "date",
            "user_uploaded_count_sum" => "unsignedInteger",
            "bot_uploaded_count_sum" => "unsignedInteger",
            "user_approved_inday_count_sum" => "unsignedInteger",
            "bot_approved_inday_count_sum" => "unsignedInteger",
            "user_approved_count_sum" => "unsignedInteger",
            "bot_approved_count_sum" => "unsignedInteger",
        ],
    ],
    
    'robot_counter_report' => [
        'unique_fields' => ['bot', 'report_date'],
        'api' => '/api/v1/robots/counter?type=day',
        'attribute_mapping' => [
            "report_date" => "date",
            "bot" => "string",
            "min_execution_time" => "unsignedInteger",
            "max_execution_time" => "unsignedInteger",
            "average_execution_time" => "unsignedInteger",
            "visited_times" => "unsignedInteger",
        ],
    ],
    
    'seo_keyword' => [
        'unique_fields' => ['date'],
        'api' => '/api/v1/seo_keyword/report',
        'attribute_mapping' => [
            "date" => "date",
            "public_count" => "unsignedInteger",
            "total" => "unsignedInteger",
        ],
    ],
    
    'social_report' => [
        'unique_fields' => ['date'],
        'api' => '/social_report',
        'attribute_mapping' => [
            "date" => "date",
            "posted_urls" => "unsignedInteger",
            "twitter" => "unsignedInteger",
            "pinterest" => "unsignedInteger",
            "facebook" => "unsignedInteger",
            "trello" => "unsignedInteger",
            "medium" => "unsignedInteger",
            "wordpress" => "unsignedInteger",
            "linkedin" => "unsignedInteger",
            "tumblr" => "unsignedInteger",
        ],
        'data_field' => 'data'
    
    ]
];
