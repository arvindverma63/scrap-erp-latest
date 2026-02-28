<!-- App favicon -->
<link rel="shortcut icon" href="{{App\Models\Setting::where('key','admin_logo')->first()->value}}">

<!-- App css -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}?v={{time()}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/icons.min.css') }}?v={{time()}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/app.min.css') }}?v={{time()}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/css/select2.min.css" rel="stylesheet" />


<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<!-- Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<style>
    .table-light{
   --bs-table-bg: {{auth()?->user()?->roles?->first()?->color ??'white'}}
}
    .table-responsive.card.shadow-sm.rounded-3 {
        overflow: visible;
    }
    </style>