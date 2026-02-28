<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        $('#datatable_1').DataTable({
            dom: 'Bfrtip',
            order: [],
            buttons: [{
                    extend: 'pdfHtml5',
                    text: 'Export to PDF',
                    className: 'btn btn-primary btn-sm',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: function(idx, data, node) {
                            const header = $('#datatable_1 thead th').eq(idx).text().trim()
                                .toLowerCase();
                            return header !== 'active' && header !== 'action' && header !==
                                'image';
                        }
                    },

                    customize: function(doc) {
                        // === Basic styles ===
                        doc.defaultStyle.fontSize = 9;
                        doc.styles.tableHeader = {
                            fillColor: '#2d4154',
                            color: 'white',
                            bold: true,
                            alignment: 'center'
                        };
                        doc.styles.title = {
                            alignment: 'center',
                            fontSize: 14,
                            bold: true,
                            margin: [0, 0, 0, 10]
                        };

                        // === Proper margins for multiple pages ===
                        doc.pageMargins = [20, 40, 20, 40]; // L, T, R, B

                        // === Table formatting ===
                        var table = doc.content[1].table;
                        var colCount = table.body[0].length;

                        // Distribute columns evenly (full width)
                        table.widths = Array(colCount).fill('*');

                        // Center all cells
                        for (var i = 0; i < table.body.length; i++) {
                            for (var j = 0; j < table.body[i].length; j++) {
                                table.body[i][j].alignment = 'center';
                                table.body[i][j].margin = [0, 2, 0, 2];
                            }
                        }

                        // Table layout with borders and padding
                        doc.content[1].layout = {
                            hLineWidth: function(i, node) {
                                return 0.3;
                            },
                            vLineWidth: function(i, node) {
                                return 0.3;
                            },
                            hLineColor: function(i, node) {
                                return '#aaa';
                            },
                            vLineColor: function(i, node) {
                                return '#aaa';
                            },
                            paddingLeft: function() {
                                return 6;
                            },
                            paddingRight: function() {
                                return 6;
                            },
                            paddingTop: function() {
                                return 4;
                            },
                            paddingBottom: function() {
                                return 4;
                            }
                        };

                        // === Force table to respect page breaks ===
                        doc.content[1].table.dontBreakRows = false;
                        doc.content[1].table.headerRows = 1;
                        doc.content[1].alignment = 'center';

                        // === Optional: Add page numbers ===
                        doc['footer'] = function(currentPage, pageCount) {
                            return {
                                text: currentPage.toString() + ' / ' + pageCount,
                                alignment: 'center',
                                margin: [0, 10, 0, 0],
                                fontSize: 8
                            };
                        };
                    }
                },
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: function(idx, data, node) {
                            const header = $('#datatable_1 thead th').eq(idx).text().trim()
                                .toLowerCase();
                            return header !== 'active' && header !== 'action';
                        }
                    }
                }
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            responsive: true
        });
    });
</script>


