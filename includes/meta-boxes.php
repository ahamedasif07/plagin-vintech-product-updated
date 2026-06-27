<?php
/**
 * Meta Boxes for ventech_product CPT
 * - Product Details (code, construction, finish, applications, mounting)
 * - Performance Table (dynamic rows, multi-column like Image 3)
 * - Options & Accessories
 * - PDF Upload
 */

add_action( 'add_meta_boxes', 'ventech_add_product_meta_boxes' );
function ventech_add_product_meta_boxes() {
    add_meta_box(
        'ventech_product_details',
        'Product Details',
        'ventech_product_details_cb',
        'ventech_product',
        'normal',
        'high'
    );
    add_meta_box(
        'ventech_product_options',
        'Product Options & Accessories',
        'ventech_product_options_cb',
        'ventech_product',
        'normal',
        'default'
    );
    add_meta_box(
        'ventech_performance_table',
        'Performance Table',
        'ventech_performance_table_cb',
        'ventech_product',
        'normal',
        'default'
    );
    add_meta_box(
        'ventech_product_pdfs',
        'Product Documents (PDF)',
        'ventech_product_pdfs_cb',
        'ventech_product',
        'side',
        'default'
    );
}

// ─── Product Details ─────────────────────────────────────────────────────────
function ventech_product_details_cb( $post ) {
    wp_nonce_field( 'ventech_save_meta', 'ventech_meta_nonce' );
    $fields = [
        'product_code'    => 'Product Code',
        'construction'    => 'Construction',
        'finish'          => 'Finish',
        'applications'    => 'Applications',
        'mounting'        => 'Mounting',
        'size_note'       => 'Size / Note (e.g. 600 × 90 mm)',
    ];
    echo '<table class="form-table ventech-meta-table">';
    foreach ( $fields as $key => $label ) {
        $val = get_post_meta( $post->ID, '_ventech_' . $key, true );
        echo '<tr>';
        echo '<th><label for="ventech_' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th>';
        echo '<td><input type="text" id="ventech_' . esc_attr( $key ) . '" name="ventech_' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" class="large-text" /></td>';
        echo '</tr>';
    }
    echo '</table>';
}

// ─── Options & Accessories ───────────────────────────────────────────────────
function ventech_product_options_cb( $post ) {
    $options     = get_post_meta( $post->ID, '_ventech_options', true );
    $accessories = get_post_meta( $post->ID, '_ventech_accessories', true );
    ?>
    <p>
        <label><strong>Options</strong> (one per line, e.g. <em>Design: Removable Core, Hinged Type</em>)</label><br>
        <textarea name="ventech_options" rows="5" style="width:100%"><?php echo esc_textarea( $options ); ?></textarea>
    </p>
    <p>
        <label><strong>Accessories</strong> (comma separated)</label><br>
        <textarea name="ventech_accessories" rows="3" style="width:100%"><?php echo esc_textarea( $accessories ); ?></textarea>
    </p>
    <?php
}

// ─── Performance Table ───────────────────────────────────────────────────────
function ventech_performance_table_cb( $post ) {
    $rows    = get_post_meta( $post->ID, '_ventech_perf_rows', true );
    $headers = get_post_meta( $post->ID, '_ventech_perf_headers', true );

    // Default headers matching Image 3
    if ( empty( $headers ) || ! is_array( $headers ) ) {
        $headers = [
            'Velocity (m/s)',
            'Flow (l/s)',
            'Pressure (Pa)',
            'Throw min-max (m)',
            'NC',
        ];
    }
    if ( empty( $rows ) || ! is_array( $rows ) ) {
        $rows = [];
    }

    $col_count = count( $headers );
    ?>
    <style>
        #ventech-perf-table { border-collapse: collapse; width: 100%; margin-top: 12px; }
        #ventech-perf-table th,
        #ventech-perf-table td { border: 1px solid #c3c4c7; padding: 6px 8px; text-align: left; white-space: nowrap; }
        #ventech-perf-table th { background: #1e3a5f; color: #fff; font-size: 13px; }
        #ventech-perf-table tbody tr:nth-child(even) { background: #f9f9f9; }
        #ventech-perf-table td input {
            width: 100%;
            min-width: 80px;
            box-sizing: border-box;
            border: 1px solid #8c8f94;
            border-radius: 3px;
            padding: 4px 6px;
            font-size: 13px;
        }
        #ventech-perf-table td input:focus { border-color: #2271b1; outline: 2px solid rgba(34,113,177,.2); }
        .ventech-remove-row { background: none !important; border: 1px solid #d63638 !important; color: #d63638 !important; padding: 2px 8px !important; cursor: pointer; border-radius: 3px; }
        .ventech-remove-row:hover { background: #d63638 !important; color: #fff !important; }
        #ventech-add-row { margin-top: 10px; }
        .ventech-perf-headers-wrap { margin-bottom: 16px; }
        .ventech-perf-headers-wrap label { font-weight: 600; display: block; margin-bottom: 4px; }
        .ventech-headers-hint { font-size: 12px; color: #646970; margin-top: 4px; }
    </style>

    <div class="ventech-perf-headers-wrap">
        <label for="ventech_perf_headers">Column Headers <span style="font-weight:400;color:#646970">(comma separated)</span></label>
        <input type="text"
               id="ventech_perf_headers"
               name="ventech_perf_headers"
               value="<?php echo esc_attr( implode( ', ', $headers ) ); ?>"
               class="large-text"
               placeholder="Velocity (m/s), Flow (l/s), Pressure (Pa), Throw min-max (m), NC" />
        <p class="ventech-headers-hint">Default: Velocity (m/s), Flow (l/s), Pressure (Pa), Throw min-max (m), NC &mdash; Edit to customise column names. Changing headers will re-index columns.</p>
    </div>

    <p><strong>Performance Rows</strong></p>
    <div style="overflow-x:auto;">
        <table id="ventech-perf-table">
            <thead>
                <tr id="ventech-perf-thead-row">
                    <?php foreach ( $headers as $h ) : ?>
                        <th><?php echo esc_html( trim( $h ) ); ?></th>
                    <?php endforeach; ?>
                    <th style="width:60px">Remove</th>
                </tr>
            </thead>
            <tbody id="ventech-perf-tbody">
                <?php foreach ( $rows as $ri => $row ) :
                    // Normalise row length to current column count
                    $row = array_pad( (array) $row, $col_count, '' );
                    ?>
                    <tr>
                        <?php for ( $ci = 0; $ci < $col_count; $ci++ ) : ?>
                            <td><input type="text"
                                       name="ventech_perf_rows[<?php echo $ri; ?>][<?php echo $ci; ?>]"
                                       value="<?php echo esc_attr( isset( $row[$ci] ) ? $row[$ci] : '' ); ?>"
                                /></td>
                        <?php endfor; ?>
                        <td style="text-align:center">
                            <button type="button" class="button ventech-remove-row" title="Remove row">✕</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <button type="button" class="button button-secondary" id="ventech-add-row">+ Add Row</button>
    <input type="hidden" name="ventech_perf_col_count" id="ventech_perf_col_count" value="<?php echo $col_count; ?>" />

    <script>
    (function(){
        var headerInput = document.getElementById('ventech_perf_headers');
        var tbody       = document.getElementById('ventech-perf-tbody');
        var theadRow    = document.getElementById('ventech-perf-thead-row');
        var colCountEl  = document.getElementById('ventech_perf_col_count');

        // Get current column count from hidden field
        function getColCount() {
            return parseInt( colCountEl.value, 10 ) || 1;
        }

        // Rebuild thead when headers change
        headerInput.addEventListener('change', function(){
            var cols = this.value.split(',').map(function(s){ return s.trim(); }).filter(Boolean);
            colCountEl.value = cols.length;

            // Update header row
            while ( theadRow.firstChild ) theadRow.removeChild( theadRow.firstChild );
            cols.forEach(function(col){
                var th = document.createElement('th');
                th.textContent = col;
                theadRow.appendChild(th);
            });
            var thRemove = document.createElement('th');
            thRemove.style.width = '60px';
            thRemove.textContent = 'Remove';
            theadRow.appendChild(thRemove);

            // Update existing rows
            var rows = tbody.querySelectorAll('tr');
            rows.forEach(function(tr, ri){
                var cells = tr.querySelectorAll('td:not(:last-child)');
                // Remove old cells (keep last = remove button)
                var lastCell = tr.lastElementChild;
                while ( tr.children.length > 1 ) tr.removeChild(tr.firstChild);

                // Rebuild cells
                var html = '';
                for ( var ci = 0; ci < cols.length; ci++ ) {
                    var oldVal = cells[ci] ? (cells[ci].querySelector('input') ? cells[ci].querySelector('input').value : '') : '';
                    html += '<td><input type="text" name="ventech_perf_rows['+ri+']['+ci+']" value="'+oldVal+'" /></td>';
                }
                tr.insertAdjacentHTML('afterbegin', html);
            });
        });

        // Add Row
        document.getElementById('ventech-add-row').addEventListener('click', function(){
            var count  = getColCount();
            var ri     = tbody.querySelectorAll('tr').length;
            var tr     = document.createElement('tr');
            var html   = '';
            for ( var i = 0; i < count; i++ ) {
                html += '<td><input type="text" name="ventech_perf_rows['+ri+']['+i+']" value="" /></td>';
            }
            html += '<td style="text-align:center"><button type="button" class="button ventech-remove-row" title="Remove row">✕</button></td>';
            tr.innerHTML = html;
            tbody.appendChild(tr);
        });

        // Remove Row (delegated)
        document.getElementById('ventech-perf-table').addEventListener('click', function(e){
            if ( e.target && e.target.classList.contains('ventech-remove-row') ) {
                if ( confirm('Remove this row?') ) {
                    e.target.closest('tr').remove();
                    // Re-index row names
                    tbody.querySelectorAll('tr').forEach(function(tr, ri){
                        tr.querySelectorAll('input').forEach(function(inp, ci){
                            inp.name = 'ventech_perf_rows['+ri+']['+ci+']';
                        });
                    });
                }
            }
        });
    })();
    </script>
    <?php
}

// ─── PDF Documents ───────────────────────────────────────────────────────────
function ventech_product_pdfs_cb( $post ) {
    $pdfs = get_post_meta( $post->ID, '_ventech_pdfs', true );
    if ( empty( $pdfs ) ) $pdfs = [];
    ?>
    <div id="ventech-pdf-list">
        <?php foreach ( $pdfs as $i => $pdf ) : ?>
        <div class="ventech-pdf-entry" style="margin-bottom:8px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <input type="text" name="ventech_pdf_labels[]" value="<?php echo esc_attr( $pdf['label'] ); ?>" placeholder="Label (e.g. Manual)" style="width:40%" />
            <input type="hidden" name="ventech_pdf_urls[]" value="<?php echo esc_attr( $pdf['url'] ); ?>" class="ventech-pdf-url-field" />
            <span class="ventech-pdf-filename"><?php echo esc_html( basename( $pdf['url'] ) ); ?></span>
            <button type="button" class="button ventech-upload-pdf">Upload PDF</button>
            <button type="button" class="button ventech-remove-pdf">✕</button>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="button button-secondary" id="ventech-add-pdf">+ Add Document</button>

    <script>
    (function($){
        $(document).ready(function(){
            var frame;
            $(document).on('click', '.ventech-upload-pdf', function(e){
                e.preventDefault();
                var btn = $(this);
                frame = wp.media({ title: 'Select PDF', button: { text: 'Use this file' }, multiple: false });
                frame.on('select', function(){
                    var att = frame.state().get('selection').first().toJSON();
                    btn.siblings('.ventech-pdf-url-field').val(att.url);
                    btn.siblings('.ventech-pdf-filename').text(att.filename || att.url.split('/').pop());
                });
                frame.open();
            });
            $(document).on('click', '.ventech-remove-pdf', function(){
                $(this).closest('.ventech-pdf-entry').remove();
            });
            $('#ventech-add-pdf').on('click', function(){
                $('#ventech-pdf-list').append(`
                    <div class="ventech-pdf-entry" style="margin-bottom:8px;display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <input type="text" name="ventech_pdf_labels[]" value="" placeholder="Label" style="width:40%" />
                        <input type="hidden" name="ventech_pdf_urls[]" value="" class="ventech-pdf-url-field" />
                        <span class="ventech-pdf-filename"></span>
                        <button type="button" class="button ventech-upload-pdf">Upload PDF</button>
                        <button type="button" class="button ventech-remove-pdf">✕</button>
                    </div>`);
            });
        });
    })(jQuery);
    </script>
    <?php
}

// ─── Save all meta ───────────────────────────────────────────────────────────
add_action( 'save_post_ventech_product', 'ventech_save_product_meta' );
function ventech_save_product_meta( $post_id ) {
    if ( ! isset( $_POST['ventech_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['ventech_meta_nonce'], 'ventech_save_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $text_fields = [ 'product_code', 'construction', 'finish', 'applications', 'mounting', 'size_note' ];
    foreach ( $text_fields as $field ) {
        if ( isset( $_POST[ 'ventech_' . $field ] ) ) {
            update_post_meta( $post_id, '_ventech_' . $field, sanitize_text_field( $_POST[ 'ventech_' . $field ] ) );
        }
    }

    if ( isset( $_POST['ventech_options'] ) ) {
        update_post_meta( $post_id, '_ventech_options', sanitize_textarea_field( $_POST['ventech_options'] ) );
    }
    if ( isset( $_POST['ventech_accessories'] ) ) {
        update_post_meta( $post_id, '_ventech_accessories', sanitize_textarea_field( $_POST['ventech_accessories'] ) );
    }

    // Performance headers
    if ( isset( $_POST['ventech_perf_headers'] ) ) {
        $raw_headers = sanitize_text_field( $_POST['ventech_perf_headers'] );
        $headers = array_filter( array_map( 'trim', explode( ',', $raw_headers ) ) );
        if ( empty( $headers ) ) {
            $headers = [ 'Velocity (m/s)', 'Flow (l/s)', 'Pressure (Pa)', 'Throw min-max (m)', 'NC' ];
        }
        update_post_meta( $post_id, '_ventech_perf_headers', array_values( $headers ) );
    }

    // Performance rows
    if ( isset( $_POST['ventech_perf_rows'] ) && is_array( $_POST['ventech_perf_rows'] ) ) {
        $clean_rows = [];
        foreach ( $_POST['ventech_perf_rows'] as $row ) {
            if ( ! is_array( $row ) ) continue;
            $clean_row = array_map( 'sanitize_text_field', $row );
            // Skip completely empty rows
            if ( array_filter( $clean_row ) ) {
                $clean_rows[] = array_values( $clean_row );
            }
        }
        update_post_meta( $post_id, '_ventech_perf_rows', $clean_rows );
    } else {
        update_post_meta( $post_id, '_ventech_perf_rows', [] );
    }

    // PDFs
    if ( isset( $_POST['ventech_pdf_labels'] ) && isset( $_POST['ventech_pdf_urls'] ) ) {
        $labels = array_map( 'sanitize_text_field', $_POST['ventech_pdf_labels'] );
        $urls   = array_map( 'esc_url_raw', $_POST['ventech_pdf_urls'] );
        $pdfs   = [];
        foreach ( $labels as $i => $label ) {
            if ( ! empty( $urls[ $i ] ) ) {
                $pdfs[] = [ 'label' => $label, 'url' => $urls[ $i ] ];
            }
        }
        update_post_meta( $post_id, '_ventech_pdfs', $pdfs );
    }
}
