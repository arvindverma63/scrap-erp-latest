<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $description ?? 'Default description here' }}">
    <meta name="author" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    @include('layouts.common.style')
</head>
