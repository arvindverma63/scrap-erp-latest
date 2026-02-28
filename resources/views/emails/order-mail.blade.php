<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Receipt from CM Recycling</title>
    <style>
        /* Basic responsive email styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f5f7;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #111827;
            -webkit-text-size-adjust: 100%;
        }
        .email-wrapper {
            width: 100%;
            padding: 24px 12px;
            box-sizing: border-box;
        }
        .email-body {
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(16,24,40,0.06);
        }
        .header {
            background: #111827;
            color: #ffffff;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo {
            width: 44px;
            height: 44px;
            border-radius: 6px;
            background-color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #111827;
            font-weight: 700;
            font-size: 18px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            letter-spacing: 0.2px;
        }
        .content {
            padding: 24px;
            line-height: 1.5;
            color: #374151;
        }
        .preheader {
            display:none !important;
            visibility:hidden;
            opacity:0;
            color:transparent;
            height:0;
            width:0;
            overflow:hidden;
            mso-hide:all;
        }
        .button {
            display: inline-block;
            background: #ff9900;
            color: #111827;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 14px;
        }
        .footer {
            background: #f9fafb;
            padding: 16px 24px;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #eef2f7;
        }
        .small {
            font-size: 13px;
            color: #6b7280;
        }
        @media (max-width:480px){
            .header, .content, .footer { padding-left:16px; padding-right:16px; }
        }
    </style>
</head>
<body>
<!-- Subject: Receipt from CM Recycling -->
<div class="preheader">Your receipt from CM Recycling is now available.</div>

<div class="email-wrapper">
    <div class="email-body" role="article" aria-roledescription="email" aria-label="Receipt from CM Recycling">
        <!-- Header -->
        <div class="header">
            <div class="logo" aria-hidden="true">CM</div>
            <h1>Receipt from CM Recycling</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Hello <strong>{{$data['name']}}</strong>,</p>

            <p>Your receipt is now available. Please find the attached invoice for your recent order/service.</p>

            <!-- CTA (link to view receipt, replace href with actual URL if available) -->
{{--            <p>--}}
{{--                <a href="#" class="button" target="_blank" rel="noopener noreferrer">View Receipt</a>--}}
{{--            </p>--}}

            <p>If you have any questions, you can reply to this email and we’ll be happy to help.</p>

            <p>Thank you for choosing us.</p>

            <p style="margin-top:20px;">Regards,<br/><strong>CM Recycling</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="small">
                CM Recycling<br/>
                Support: <a href="{{ App\Models\Setting::where('key', 'website_email')->first()->value }}">
                    {{ App\Models\Setting::where('key', 'website_email')->first()->value }}
                </a> | Phone: {{ App\Models\Setting::where('key', 'phone_number')->first()->value }}
            </div>
        </div>
    </div>
</div>
</body>
</html>