<!-- Javascript  -->
<!-- vendor js -->

<script src="{{ asset('assets/js/app.js')}}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ asset('assets/js/DynamicSelect.js')}}"></script>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/js/select2.min.js"></script>

<script src="{{ asset('assets/toastr/toastr.min.js') }}"></script>
<script>
    document.getElementById('closeSidebar').addEventListener('click', function () {
        document.getElementById('startbar').style.display = 'none';
    });
    document.getElementById('togglemenu').addEventListener('click', function () {
        document.getElementById('startbar').style.display = 'block';
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sweet-action-btn').forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const url = this.getAttribute('href');
                const action = this.getAttribute('data-action');

                let title, text, icon, confirmText;

                if (action === 'complete') {
                    title = 'Complete Order?';
                    text = 'Are you sure you want to mark this order as completed?';
                    icon = 'success';
                    confirmText = 'Yes, complete it!';
                } else if (action === 'void') {
                    title = 'Void Order?';
                    text = 'This will cancel the order. Are you sure you want to continue?';
                    icon = 'warning';
                    confirmText = 'Yes, void it!';
                }

                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmText
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        $(document).find('.select2').select2({
            allowClear: true,
            width: '100%'
        });
        $(document).find('.select2-supplier').select2({
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script> console.log(@json(session()->all()));</script>

@if (session('success'))
    <script>
        console.log('success log');
        toastr.success("{{ Session::get('success') }}");
    </script>
@endif

@if (session('error'))
    <script>
        console.log('error log');
        toastr.error("{{ Session::get('error') }}");
    </script>
@endif

@if (session('warning'))
    <script>
        console.log('warning log');
        toastr.warning("{{ Session::get('warning') }}");
    </script>
@endif

@if (session('info'))
    <script>
        console.log('info log');
        toastr.info("{{ Session::get('info') }}");
    </script>
@endif


{{-- @include('layouts.common.notification') --}}