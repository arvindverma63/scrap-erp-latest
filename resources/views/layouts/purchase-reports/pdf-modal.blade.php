<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Invoice Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="invoiceFrame" src="" style="width: 100%; height: 600px; border: none;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Send</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle modal iframe source -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const invoiceLinks = document.querySelectorAll('.view-invoice');
        const invoiceFrame = document.getElementById('invoiceFrame');

        invoiceLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const invoiceUrl = this.getAttribute('data-invoice-url');
                invoiceFrame.src = invoiceUrl;
            });
        });

        // Reset iframe src when modal is closed to prevent loading old content
        const invoiceModal = document.getElementById('invoiceModal');
        invoiceModal.addEventListener('hidden.bs.modal', function () {
            invoiceFrame.src = '';
        });

        // Handle iframe load errors
        invoiceFrame.addEventListener('error', function () {
            alert('Failed to load invoice. Please try downloading instead or contact support.');
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>