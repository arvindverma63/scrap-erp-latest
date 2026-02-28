    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            const searchInput = document.getElementById('search').value.trim();

            if (searchInput === '') {
                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Search!',
                    text: 'Please enter a search term before submitting.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
