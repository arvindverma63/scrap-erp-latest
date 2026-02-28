<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

    <head>
        <meta charset="utf-8" />
        <title>Login | Approx - Admin & Dashboard Template</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

       
        @include('layouts.common.style')

    </head>

    
    <!-- Top Bar Start -->
    <body>
        <div class="container-xxl">
            {{ $slot }}                    
        </div><!-- container -->
    </body>
    <!--end body-->
</html>