<!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                pageLength: 25,
                responsive: true,
                order: [[0, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
            
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Statistics cards animation
            $('.stats-number').each(function() {
                const $this = $(this);
                const finalValue = parseInt($this.text()) || 0;
                
                $({ counter: 0 }).animate({ counter: finalValue }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.ceil(this.counter));
                    }
                });
            });

            // Fix sidebar active states
            $('.sidebar .nav-link').on('click', function() {
                $('.sidebar .nav-link').removeClass('active');
                $(this).addClass('active');
            });
        });
        
        // Show success/error messages
        function showAlert(message, type = 'success') {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            if ($('.alert-container').length) {
                $('.alert-container').html(alertHtml);
            } else {
                $('main .container-fluid').prepend('<div class="alert-container">' + alertHtml + '</div>');
            }
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        }

        // Fix layout issues
        $(window).on('load resize', function() {
            const sidebarWidth = $('.sidebar').outerWidth();
            const headerHeight = $('.header-main').outerHeight();
            
            $('main').css({
                'margin-left': sidebarWidth + 'px',
                'margin-top': headerHeight + 'px',
                'min-height': 'calc(100vh - ' + headerHeight + 'px)'
            });
        });
    </script>
</body>
</html>