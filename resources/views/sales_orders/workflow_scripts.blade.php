@push('script-page')
    <script>
        var units = {!! json_encode($units) !!};
        var currencies = {!! json_encode($currencies) !!};

        function getUnitOptions(selected) {
            var options = '';
            $.each(units, function(k, v) {
                options += `<option value="${k}" ${k == selected ? 'selected' : ''}>${v}</option>`;
            });
            options += `<option value="ADD_NEW_UNIT" class="text-primary fw-bold">+ {{ __('Add New') }}</option>`;
            return options;
        }

        function getCurrencyOptions(selected) {
            var options = '';
            $.each(currencies, function(k, v) {
                options += `<option value="${k}" ${k == selected ? 'selected' : ''}>${v}</option>`;
            });
            options += `<option value="ADD_NEW_CURR" class="text-primary fw-bold">+ {{ __('Add New') }}</option>`;
            return options;
        }

        // PO Logic
        $(document).on('click', '.add-item', function() {
            var index = $('#po-items-table tbody tr').length;
            var html = `<tr>
                <td><input type="text" name="items[${index}][item]" class="form-control" required></td>
                <td><input type="text" name="items[${index}][description]" class="form-control"></td>
                <td><input type="number" name="items[${index}][qty]" class="form-control qty" required></td>
                <td>
                    <select name="items[${index}][unit]" class="form-control unit-select" required>
                        ${getUnitOptions('MT')}
                    </select>
                </td>
                <td><input type="number" step="0.01" name="items[${index}][price]" class="form-control price" required></td>
                <td>
                    <select name="items[${index}][currency]" class="form-control curr-select" required>
                        ${getCurrencyOptions('BDT')}
                    </select>
                </td>
                <td><input type="number" step="0.01" name="items[${index}][total]" class="form-control total" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item"><i class="ti ti-trash"></i></button></td>
            </tr>`;
            $('#po-items-table tbody').append(html);
        });

        // CI Logic
        $(document).on('click', '.add-tanker', function() {
            var index = $('#ci-tankers-table tbody tr').length;
            var html = `<tr>
                <td><input type="text" name="tankers[${index}][tanker_number]" class="form-control form-control-sm" required></td>
                <td><input type="number" step="0.001" name="tankers[${index}][qty_mt]" class="form-control form-control-sm t-qty" required></td>
                <td>
                    <select name="tankers[${index}][quantity_unit]" class="form-control form-control-sm unit-select" required>
                        ${getUnitOptions('MT')}
                    </select>
                </td>
                <td><input type="number" step="0.01" name="tankers[${index}][cpt_usd]" class="form-control form-control-sm t-cpt" required></td>
                <td>
                    <select name="tankers[${index}][currency]" class="form-control form-control-sm curr-select" required>
                        ${getCurrencyOptions('USD')}
                    </select>
                </td>
                <td><input type="number" step="0.01" name="tankers[${index}][total_amount]" class="form-control form-control-sm t-total" readonly></td>
                <td><button type="button" class="btn btn-danger btn-xs remove-tanker"><i class="ti ti-trash"></i></button></td>
            </tr>`;
            $('#ci-tankers-table tbody').append(html);
        });

        // Calculation logic
        $(document).on('click', '.remove-item', function() { $(this).closest('tr').remove(); calculateGrandTotal(); });
        $(document).on('keyup change', '.qty, .price', function() {
            var tr = $(this).closest('tr');
            var total = (tr.find('.qty').val() || 0) * (tr.find('.price').val() || 0);
            tr.find('.total').val(total.toFixed(2));
            calculateGrandTotal();
        });
        function calculateGrandTotal() {
            var grandTotal = 0;
            $('.total').each(function() { grandTotal += parseFloat($(this).val() || 0); });
            $('#grand_total').val(grandTotal.toFixed(2));
            $('#grand_total_display').text(grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        }

        $(document).on('click', '.remove-tanker', function() { $(this).closest('tr').remove(); calculateCITotals(); });
        $(document).on('keyup change', '.t-qty, .t-cpt', function() {
            var tr = $(this).closest('tr');
            var total = (tr.find('.t-qty').val() || 0) * (tr.find('.t-cpt').val() || 0);
            tr.find('.t-total').val(total.toFixed(2));
            calculateCITotals();
        });
        function calculateCITotals() {
            var tQty = 0; var tAmt = 0;
            $('.t-qty').each(function() { tQty += parseFloat($(this).val() || 0); });
            $('.t-total').each(function() { tAmt += parseFloat($(this).val() || 0); });
            $('#ci_total_qty').text(tQty.toFixed(3));
            $('#ci_total_amount').text(tAmt.toFixed(2));
        }
        
        if($('#ci-tankers-table').length) {
            calculateCITotals();
        }

        // AJAX for Add New
        $(document).on('change', '.unit-select', function() {
            var select = $(this);
            if (select.val() === 'ADD_NEW_UNIT') {
                var newName = prompt("{{ __('Enter new unit name (e.g. Box, Drum):') }}");
                if (newName) {
                    $.ajax({
                        url: '{{ route("sales-orders.add-unit") }}',
                        method: 'POST',
                        data: { name: newName, _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                units[res.data.name] = res.data.name;
                                $('.unit-select').each(function() {
                                    var s = $(this);
                                    var current = s.val();
                                    s.html(getUnitOptions(current === 'ADD_NEW_UNIT' ? res.data.name : current));
                                });
                                show_toastr('Success', 'Unit added successfully', 'success');
                            }
                        }
                    });
                } else { select.val('MT'); }
            }
        });

        $(document).on('change', '.curr-select', function() {
            var select = $(this);
            if (select.val() === 'ADD_NEW_CURR') {
                var newCode = prompt("{{ __('Enter new currency code (e.g. AED, INR):') }}");
                if (newCode) {
                    $.ajax({
                        url: '{{ route("sales-orders.add-currency") }}',
                        method: 'POST',
                        data: { code: newCode.toUpperCase(), _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                currencies[res.data.code] = res.data.code;
                                $('.curr-select').each(function() {
                                    var s = $(this);
                                    var current = s.val();
                                    s.html(getCurrencyOptions(current === 'ADD_NEW_CURR' ? res.data.code : current));
                                });
                                show_toastr('Success', 'Currency added successfully', 'success');
                            }
                        }
                    });
                } else { select.val('USD'); }
            }
        });

        // Datepicker Initialization
        if ($(".datepicker").length) {
            $(".datepicker").flatpickr({
                dateFormat: "m-d-Y",
                allowInput: true,
                onReady: function(selectedDates, dateStr, instance) {
                    // Force the placeholder to be MM-DD-YYYY
                    $(instance.element).attr('placeholder', 'MM-DD-YYYY');
                }
            });
        }

        // Date format validation (for manual typing if allowed)
        $(document).on('change', '.datepicker', function() {
            var dateVal = $(this).val();
            var regex = /^(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])-\d{4}$/;
            if (dateVal && !regex.test(dateVal)) {
                show_toastr('Error', 'Invalid date format. Please use MM-DD-YYYY', 'error');
                $(this).val('');
            }
        });

        // Mismatch Validation Logic
        function checkMismatch(inputId, piValueId, warningId, type = 'text') {
            var val = $('#' + inputId).val();
            var piVal = $('#' + piValueId).val();

            if (!val && !piVal) {
                $('#' + warningId).addClass('d-none');
                return;
            }

            var mismatch = false;
            if (type === 'number') {
                mismatch = Math.abs(parseFloat(val) - parseFloat(piVal)) > 0.001;
            } else if (type === 'date_validity') {
                var piDateStr = $('#pi_date').val();
                var validityDays = parseInt($('#pi_validity').val()) || 0;
                var lcDateStr = val;
                if (piDateStr && lcDateStr && validityDays > 0) {
                    var piDate = new Date(piDateStr);
                    var lcDate = new Date(lcDateStr);
                    var expiryDate = new Date(piDate);
                    expiryDate.setDate(expiryDate.getDate() + validityDays);
                    mismatch = lcDate > expiryDate;
                }
            } else if (type === 'date_match') {
                if (val && piVal) {
                    var d1 = new Date(val);
                    var d2 = new Date(piVal);
                    mismatch = d1.getTime() !== d2.getTime();
                }
            } else {
                mismatch = (val || '').trim().toLowerCase() !== (piVal || '').trim().toLowerCase();
            }

            if (mismatch) {
                $('#' + warningId).removeClass('d-none');
            } else {
                $('#' + warningId).addClass('d-none');
            }
        }

        $(document).on('keyup change', '#lc_qty', function () { checkMismatch('lc_qty', 'pi_qty', 'warning_qty', 'number'); });
        $(document).on('change', '#date_of_issue', function () { 
            checkMismatch('date_of_issue', 'pi_date', 'warning_date', 'date_validity'); 
        });
        $(document).on('change', '#lc_shipment_date', function () { checkMismatch('lc_shipment_date', 'pi_shipment_date', 'warning_shipment_date', 'date_match'); });

        ['seller_name', 'seller_address', 'seller_mobile', 'seller_email',
            'buyer_name', 'buyer_address', 'buyer_mobile', 'buyer_email',
            'lifting_time', 'country_of_origin', 'tolerance', 'port_of_loading', 'port_of_discharge'
        ].forEach(function (field) {
            var inputId = field === 'lifting_time' ? 'lc_lifting_time' : 'lc_' + field;
            var piValueId = field === 'lifting_time' ? 'pi_lifting_time_val' : 'pi_' + field;
            $(document).on('keyup change', '#' + inputId, function () {
                checkMismatch(inputId, piValueId, 'warning_' + field);
            });
        });

        // Trigger initial checks for LC if present
        if ($('#lc_qty').length) {
            $('#lc_qty').trigger('change');
            $('#date_of_issue').trigger('change');
            $('#lc_shipment_date').trigger('change');
            ['seller_name', 'seller_address', 'seller_mobile', 'seller_email',
                'buyer_name', 'buyer_address', 'buyer_mobile', 'buyer_email',
                'lifting_time', 'country_of_origin', 'tolerance', 'port_of_loading', 'port_of_discharge'
            ].forEach(function (field) {
                var inputId = field === 'lifting_time' ? 'lc_lifting_time' : 'lc_' + field;
                $('#' + inputId).trigger('change');
            });
        }
    </script>
@endpush
