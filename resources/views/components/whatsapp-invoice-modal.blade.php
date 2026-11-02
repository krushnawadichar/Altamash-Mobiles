<!-- Reusable WhatsApp Invoice Sender Modal -->
<div class="modal fade" id="whatsappInvoiceModal" tabindex="-1" aria-labelledby="whatsappInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center" id="whatsappInvoiceModalLabel">
                    <i class="fa-brands fa-whatsapp fs-3 me-2"></i> Send Invoice on WhatsApp
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Customer & Invoice Summary Card -->
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light border">
                    <div>
                        <span class="text-muted small d-block">Customer</span>
                        <strong class="text-dark fs-6" id="waCustomerNameDisplay">Customer</strong>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small d-block">Invoice No</span>
                        <span class="badge bg-primary fs-6" id="waInvoiceNumberDisplay">INV-0000</span>
                    </div>
                </div>

                <!-- Customer Phone Input -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small mb-1">
                        <i class="fa-solid fa-phone text-success me-1"></i> Customer WhatsApp Mobile Number <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-success-subtle text-success fw-bold border-success">
                            <i class="fa-brands fa-whatsapp me-1 fs-5"></i> +91
                        </span>
                        <input type="tel" 
                               id="waCustomerPhoneInput" 
                               class="form-control form-control-lg fw-bold number-only border-success" 
                               maxlength="15" 
                               placeholder="10-digit mobile number" 
                               autocomplete="off">
                    </div>
                    <div class="form-text text-muted small">
                        Enter 10-digit customer number without 0 or +91 (e.g. 9876543210).
                    </div>
                </div>

                <!-- Mini Financial Pill Summary -->
                <div class="row g-2 mb-3 text-center">
                    <div class="col-4">
                        <div class="p-2 border rounded bg-white">
                            <span class="small text-muted d-block" style="font-size: 0.75rem;">Total</span>
                            <span class="fw-bold text-primary" id="waGrandTotalDisplay">₹0.00</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded bg-white">
                            <span class="small text-muted d-block" style="font-size: 0.75rem;">Paid</span>
                            <span class="fw-bold text-success" id="waPaidAmountDisplay">₹0.00</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 border rounded bg-white">
                            <span class="small text-muted d-block" style="font-size: 0.75rem;">Due Balance</span>
                            <span class="fw-bold" id="waDueAmountDisplay">₹0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Live Message Preview (Editable) -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small text-muted mb-0 fw-semibold">
                            <i class="fa-solid fa-comment-dots me-1 text-primary"></i> WhatsApp Message Preview
                        </label>
                        <span class="badge bg-light text-secondary border">Editable</span>
                    </div>
                    <textarea id="waMessageText" class="form-control font-monospace" rows="6" style="font-size: 0.82rem; line-height: 1.45; background-color: #f8fafc;"></textarea>
                </div>

                <!-- Direct Invoice Link Bar -->
                <div class="p-2 bg-light rounded border d-flex justify-content-between align-items-center">
                    <div class="small text-truncate me-2">
                        <i class="fa-solid fa-link text-primary me-1"></i>
                        <span id="waInvoiceLinkDisplay" class="text-primary text-decoration-underline fw-semibold small"></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap py-0 px-2" id="btnCopyWaLink" title="Copy Invoice Link">
                        <i class="fa-solid fa-copy me-1"></i> Copy Link
                    </button>
                </div>
                
                <div id="waNoticeAlert" class="alert alert-success py-1 px-2 small mt-2 d-none">
                    <i class="fa-solid fa-check-circle me-1"></i> Copied to clipboard!
                </div>
            </div>
            
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-primary" id="btnCopyWaMessage">
                    <i class="fa-solid fa-copy me-1"></i> Copy Message
                </button>
                <button type="button" class="btn btn-success fw-bold px-3" id="btnSubmitWhatsApp">
                    <i class="fa-brands fa-whatsapp me-1"></i> Send on WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentWaInvoiceUrl = '';

    /**
     * Global helper to open WhatsApp Invoice Modal from any page/table
     */
    function openWhatsAppModal(invoiceNumber, customerName, customerPhone, grandTotal, paidAmount, dueAmount, invoiceUrl) {
        customerName = customerName || 'Customer';
        customerPhone = customerPhone || '';
        grandTotal = grandTotal || '0.00';
        paidAmount = paidAmount || '0.00';
        dueAmount = dueAmount || '0.00';

        // Resolve absolute URL
        let fullUrl = invoiceUrl || '';
        if (fullUrl && !fullUrl.startsWith('http://') && !fullUrl.startsWith('https://')) {
            fullUrl = window.location.origin + (fullUrl.startsWith('/') ? '' : '/') + fullUrl;
        }
        currentWaInvoiceUrl = fullUrl;

        // Clean customer phone (strip country code prefix for 10-digit input box)
        let cleanPhone = customerPhone.toString().replace(/[^0-9]/g, '');
        if (cleanPhone.startsWith('91') && cleanPhone.length > 10) {
            cleanPhone = cleanPhone.substring(2);
        } else if (cleanPhone.startsWith('0') && cleanPhone.length > 10) {
            cleanPhone = cleanPhone.substring(1);
        }

        // Set UI values
        $('#waCustomerNameDisplay').text(customerName);
        $('#waInvoiceNumberDisplay').text(invoiceNumber);
        $('#waCustomerPhoneInput').val(cleanPhone);
        $('#waGrandTotalDisplay').text('₹' + grandTotal);
        $('#waPaidAmountDisplay').text('₹' + paidAmount);
        
        let dueNum = parseFloat(dueAmount.replace(/,/g, '')) || 0;
        if (dueNum > 0) {
            $('#waDueAmountDisplay').text('₹' + dueAmount).removeClass('text-success text-muted').addClass('text-danger');
        } else {
            $('#waDueAmountDisplay').text('₹0.00 (Nil)').removeClass('text-danger text-muted').addClass('text-success');
        }

        $('#waInvoiceLinkDisplay').text(fullUrl);

        // Build WhatsApp formatted message
        let msg = `Hello ${customerName},\n\n`;
        msg += `Thank you for shopping at *Altamash Mobiles*! 📱\n`;
        msg += `Here are the details of your invoice:\n\n`;
        msg += `📄 *Invoice No:* ${invoiceNumber}\n`;
        msg += `💰 *Grand Total:* ₹${grandTotal}\n`;
        msg += `✅ *Amount Paid:* ₹${paidAmount}\n`;
        if (dueNum > 0) {
            msg += `⏳ *Balance Due:* ₹${dueAmount}\n`;
        } else {
            msg += `🎉 *Status:* Paid in Full\n`;
        }
        msg += `\n📥 *View & Download Invoice:* \n${fullUrl}\n\n`;
        msg += `📍 *Altamash Mobiles*\n`;
        msg += `New Narsala Road, Opp. Dhanashree Apartment, Nagpur\n`;
        msg += `📞 +91 9226087986`;

        $('#waMessageText').val(msg);

        // Show modal
        $('#whatsappInvoiceModal').modal('show');

        // Focus phone input if blank
        setTimeout(function() {
            if (!cleanPhone) {
                $('#waCustomerPhoneInput').focus();
            }
        }, 500);
    }

    $(document).ready(function() {
        // Submit to WhatsApp
        $('#btnSubmitWhatsApp').on('click', function() {
            let phone = $('#waCustomerPhoneInput').val().trim();
            let digits = phone.replace(/[^0-9]/g, '');

            if (digits.length < 10) {
                alert('Please enter a valid 10-digit mobile number to send the invoice on WhatsApp.');
                $('#waCustomerPhoneInput').focus();
                return;
            }

            // Ensure 91 country code prefix for India
            if (digits.length === 10) {
                digits = '91' + digits;
            } else if (digits.startsWith('0') && digits.length === 11) {
                digits = '91' + digits.substring(1);
            }

            let msg = $('#waMessageText').val();
            let waUrl = 'https://api.whatsapp.com/send?phone=' + digits + '&text=' + encodeURIComponent(msg);

            // Open WhatsApp in new tab
            window.open(waUrl, '_blank');

            // Hide modal
            $('#whatsappInvoiceModal').modal('hide');
        });

        // Copy Invoice Link
        $('#btnCopyWaLink').on('click', function() {
            if (currentWaInvoiceUrl) {
                navigator.clipboard.writeText(currentWaInvoiceUrl).then(function() {
                    $('#waNoticeAlert').removeClass('d-none').text('Invoice link copied to clipboard!');
                    setTimeout(function() {
                        $('#waNoticeAlert').addClass('d-none');
                    }, 3000);
                });
            }
        });

        // Copy Message Text
        $('#btnCopyWaMessage').on('click', function() {
            let msg = $('#waMessageText').val();
            if (msg) {
                navigator.clipboard.writeText(msg).then(function() {
                    $('#waNoticeAlert').removeClass('d-none').text('Message text copied to clipboard!');
                    setTimeout(function() {
                        $('#waNoticeAlert').addClass('d-none');
                    }, 3000);
                });
            }
        });
    });
</script>
