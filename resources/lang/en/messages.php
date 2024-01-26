<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'user' => [
        'login' => [
            'success' => 'Login Success',
            'failed' => 'Login Failed'
        ],
        'logout' => [
            'success' => 'Logout Success',
            'failed' => 'Logout Failed'
        ],
        'update' => [
            'success' => 'Update Success',
            'failed' => 'Update Failed'
        ],
        'delete' => [
            'success' => 'Delete Success',
        ],
        'sms' => [
            'messages' => [
                'logged' => 'You are logged out, Login again.',
                'number' => 'Your Number is Verified.',
                'otp' => 'OTP does not match.',
                'send-otp' => 'Send OTP'
            ]
        ],
        'gender' => [
            'male' => 'Male',
            'female' => 'Female'
        ],
    ],
    'menu' => [
        'delete' => [
            'success' => 'Delete Menu Success'
        ]
    ],
    'product' => [
        'delete' => [
            'success' => 'Delete Product Success',
        ]
    ],
    'slider' => [
        'delete' => [
            'success' => 'Delete Slider Success',
        ]
    ],
    'mail' => [
        'send' => [
            'success' => 'Send Success',
            'failed' => 'Send Failed',
            'email' => 'Please check your email'
        ]
    ],
    'import' => [
        'menu' => 'Import Menu Success',
        'success' => 'Import Success'
    ],
    'reset-password' => [
        'token' => 'This password reset token is invalid.'
    ],
    'upload-file' => [
        'success' => 'Upload File Success',
        'failed' => 'Upload File Failed'
    ],
    'cart' => [
        'delete' => [
            'success' => 'Delete Cart Success'
        ],
        'payment' => [
            'currency_code' => 'USD',
            'failed' => "Can't Payment",
            'complete' => "COMPLETED",
            'success' => 'Payment Success'
        ]
    ],
    'validation' => [
        'user' => [
            'email' => [
                'required' => 'Please enter your email',
                'unique' => 'Email already exist',
                'email' => 'The attribute must be a valid email address',
                'exists' => 'Email is not exist'
            ],
            'password' => [
                'required' => 'Please enter your password',
                'regex' => 'Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters',
                'confirm' => 'Password confirm does not match',
            ],
            'name' => [
                'required' => 'Please enter your name'
            ]
        ],
        'mail' => [
            'subject' => [
                'required' => 'Subject is required'
            ],
            'content' => [
                'required' => 'Content is required'
            ]
        ],
        'file' => [
            'required' => 'File is required',
            '*' => [
                'file' => 'File is File',
                'mimes' => 'Type File must be CSV or XLSX',
                'max' => 'Max is 50MB'
            ]
        ],
        'cart' => [
            'exists' => 'You already have it in your cart',
        ],
        'import' => [
            'header-title' => 'Header Title Incorrect',
            'header' => 'Header Incorrect'
        ]
    ],
];
