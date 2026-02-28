<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>

    <meta charset="utf-8" />

    <title>{{ $title ?? 'Scrap CMS' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    @include('layouts.common.style')

</head>

<body>

    @include('layouts.common.topbar')


    @include('layouts.common.sidebar')



    <div class="page-wrapper">

        {{ $slot }}

    </div>
    <!-- end page-wrapper -->

    @include('layouts.common.scripts')

</body>
<!--end body-->

</html>