<?php
return [
    
    /*
     * |--------------------------------------------------------------------------
     * | AWS SNS Configs
     * |--------------------------------------------------------------------------
     * |
     * | This file is for storing the configs for AWS SNS
     * | For more information, see:
     * | SMS: http://docs.aws.amazon.com/pt_br/sns/latest/api/API_SetSMSAttributes.html
     * |
     */
    
    'sms' => [
        'monthlySpendLimit' => env('SNS_SMS_MONTHLY_LIMIT'),
        'deliveryStatusIAMRole' => env('SNS_SMS_DELIVERY_STATUS_IAM_ROLE'),
        'deliveryStatusSuccessSamplingRate' => env('SNS_SMS_DELIVERY_STATUS'),
        'defaultSenderID' => env('SNS_SMS_SENDER'),
        'defaultSMSType' => env('SNS_SMS_TYPE'),
        'usageReportS3Bucket' => env('SNS_SMS_REPORT_S3')
    ]
];
