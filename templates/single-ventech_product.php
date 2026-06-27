<?php
/**
 * Template: Single Product Detail
 * Shows full product info like Image 3 — specs table + PDF downloads
 */

get_header();

while ( have_posts() ) : the_post();

$post_id      = get_the_ID();
$code         = get_post_meta( $post_id, '_ventech_product_code', true );
$construction = get_post_meta( $post_id, '_ventech_construction', true );
$finish       = get_post_meta( $post_id, '_ventech_finish', true );
$applications = get_post_meta( $post_id, '_ventech_applications', true );
$mounting     = get_post_meta( $post_id, '_ventech_mounting', true );
$size_note    = get_post_meta( $post_id, '_ventech_size_note', true );
$options_raw  = get_post_meta( $post_id, '_ventech_options', true );
$accessories  = get_post_meta( $post_id, '_ventech_accessories', true );
$perf_headers = get_post_meta( $post_id, '_ventech_perf_headers', true );
$perf_rows    = get_post_meta( $post_id, '_ventech_perf_rows', true );
$pdfs         = get_post_meta( $post_id, '_ventech_pdfs', true );

// Get category
$cats    = get_the_terms( $post_id, 'product_category' );
$cat     = $cats && ! is_wp_error( $cats ) ? $cats[0] : null;
$cat_url = $cat ? get_term_link( $cat ) : home_url( '/products' );
$cat_name = $cat ? $cat->name : 'Products';
?>

<main class="ventech-single-product-main">
    <div class="ventech-container">

        <!-- Breadcrumb -->
        <nav class="ventech-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span class="ventech-bc-sep">›</span>
            <a href="<?php echo home_url('/products'); ?>">Products</a>
            <span class="ventech-bc-sep">›</span>
            <a href="<?php echo esc_url( $cat_url ); ?>"><?php echo esc_html( $cat_name ); ?></a>
            <span class="ventech-bc-sep">›</span>
            <span><?php the_title(); ?></span>
        </nav>

        <!-- Product Detail Card -->
        <div class="ventech-product-detail-card">

            <!-- Image Column -->
            <div class="ventech-detail-image-col">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="ventech-detail-image-wrap">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php else : ?>
                    <div class="ventech-no-image ventech-no-image-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 160" fill="none" stroke="#d0d5dd" stroke-width="2"><rect x="10" y="10" width="180" height="140" rx="6"/><circle cx="66" cy="60" r="22"/><path d="M10 130 L54 96 L90 120 L126 88 L190 130"/></svg>
                    </div>
                <?php endif; ?>

                <!-- PDF Downloads -->
                <?php if ( ! empty( $pdfs ) ) : ?>
                <div class="ventech-pdf-downloads">
                    <h4 class="ventech-pdf-title">Documents</h4>
                    <?php foreach ( $pdfs as $pdf ) :
                        if ( empty( $pdf['url'] ) ) continue;
                        $filename = ! empty( $pdf['label'] ) ? $pdf['label'] : basename( $pdf['url'] );
                    ?>
                    <a href="<?php echo esc_url( $pdf['url'] ); ?>" class="ventech-pdf-link" target="_blank" download>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        <?php echo esc_html( $filename ); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Info Column -->
            <div class="ventech-detail-info-col">

                <!-- Title + Code -->
                <div class="ventech-detail-header">
                    <?php if ( $code ) : ?>
                        <span class="ventech-detail-code"><?php echo esc_html( $code ); ?></span>
                    <?php endif; ?>
                    <h1 class="ventech-detail-title"><?php the_title(); ?></h1>
                </div>

                <!-- Description -->
                <?php if ( get_the_content() ) : ?>
                <div class="ventech-detail-description">
                    <?php the_content(); ?>
                </div>
                <?php endif; ?>

                <!-- Specs Table -->
                <?php if ( $code || $construction || $finish || $applications || $mounting ) : ?>
                <table class="ventech-specs-table">
                    <?php if ( $code ) : ?>
                    <tr><th>Product code</th><td><?php echo esc_html( $code ); ?></td></tr>
                    <?php endif; ?>
                    <?php if ( $construction ) : ?>
                    <tr><th>Construction</th><td><?php echo esc_html( $construction ); ?></td></tr>
                    <?php endif; ?>
                    <?php if ( $finish ) : ?>
                    <tr><th>Finish</th><td><?php echo esc_html( $finish ); ?></td></tr>
                    <?php endif; ?>
                    <?php if ( $applications ) : ?>
                    <tr><th>Applications</th><td><?php echo esc_html( $applications ); ?></td></tr>
                    <?php endif; ?>
                    <?php if ( $mounting ) : ?>
                    <tr><th>Mounting</th><td><?php echo esc_html( $mounting ); ?></td></tr>
                    <?php endif; ?>
                </table>
                <?php endif; ?>

                <!-- Options -->
                <?php if ( ! empty( $options_raw ) ) :
                    $option_lines = array_filter( array_map( 'trim', explode( "\n", $options_raw ) ) );
                ?>
                <div class="ventech-detail-section">
                    <h4 class="ventech-section-label">Options</h4>
                    <ul class="ventech-option-list">
                        <?php foreach ( $option_lines as $line ) : ?>
                            <li><?php echo esc_html( $line ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Accessories -->
                <?php if ( ! empty( $accessories ) ) : ?>
                <div class="ventech-detail-section">
                    <h4 class="ventech-section-label">Accessories</h4>
                    <p class="ventech-accessories-text"><?php echo esc_html( $accessories ); ?></p>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Performance Table -->
        <?php if ( ! empty( $perf_rows ) && ! empty( $perf_headers ) ) : ?>
        <div class="ventech-perf-section">
            <h3 class="ventech-perf-title">
                Full Performance
                <?php if ( $size_note ) echo '— ' . esc_html( strtoupper( $size_note ) ); ?>
            </h3>
            <div class="ventech-table-wrap">
                <table class="ventech-perf-table">
                    <thead>
                        <tr>
                            <?php foreach ( $perf_headers as $h ) : ?>
                                <th><?php echo esc_html( trim( $h ) ); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $perf_rows as $row ) : ?>
                        <tr>
                            <?php foreach ( $row as $cell ) : ?>
                                <td><?php echo esc_html( $cell ); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="ventech-perf-notes">
                <p>* All performance data is based on isothermal conditions.</p>
                <?php if ( $size_note ) : ?>
                <p>* Data shown for <?php echo esc_html( $size_note ); ?>.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Back Button -->
        <div class="ventech-back-wrap">
            <a href="<?php echo esc_url( $cat_url ); ?>" class="ventech-btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
                Back to <?php echo esc_html( $cat_name ); ?>
            </a>
        </div>

    </div>
</main>

<?php
endwhile;
get_footer();
?>
