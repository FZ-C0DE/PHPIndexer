// Admin Dashboard JavaScript
$(document).ready(function() {
    // Initialize DataTables
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
        width: '100%',
        placeholder: 'Pilih...'
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Confirm delete actions
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            e.preventDefault();
        }
    });
    
    // Form validation and loading state
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
    });

    // Statistics cards animation
    $('.stats-number').each(function() {
        const $this = $(this);
        const finalValue = parseInt($this.text());
        
        $({ counter: 0 }).animate({ counter: finalValue }, {
            duration: 2000,
            easing: 'swing',
            step: function() {
                $this.text(Math.ceil(this.counter));
            }
        });
    });

    // Dashboard refresh button
    $('.btn-refresh').on('click', function() {
        location.reload();
    });

    // Export functionality
    $('.btn-export').on('click', function() {
        const format = $(this).data('format');
        const table = $('.datatable').DataTable();
        
        switch(format) {
            case 'excel':
                table.button('.buttons-excel').trigger();
                break;
            case 'pdf':
                table.button('.buttons-pdf').trigger();
                break;
            case 'csv':
                table.button('.buttons-csv').trigger();
                break;
        }
    });

    // Sidebar toggle
    $('.sidebar-toggle').on('click', function() {
        $('.sidebar').toggleClass('collapsed');
    });

    // Search functionality
    $('.global-search').on('keyup', function() {
        const searchTerm = $(this).val();
        $('.datatable').DataTable().search(searchTerm).draw();
    });
});

// Utility functions
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
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

function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}