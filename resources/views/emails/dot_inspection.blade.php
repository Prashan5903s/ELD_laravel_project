<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $data['subject'] }}</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            color: #333333;
        }
        table, td {
            border-collapse: collapse;
            mso-table-lspace: 0;
            mso-table-rspace: 0;
        }
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        p {
            display: block;
            margin: 13px 0;
        }
        .email-content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-header {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }
        .email-body {
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }

        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-content {
                padding: 10px;
            }
            .email-header {
                font-size: 20px;
            }
            .email-body {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div>
        {!! $text !!}
    </div>
</body>
</html>
