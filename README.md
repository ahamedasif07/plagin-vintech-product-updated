# Vintech Products Plugin

A WordPress plugin for managing and displaying HVAC/ventilation products with detailed specifications and multi-column performance tables.

---

## Features

- Custom Post Type: **ventech_product**
- Product taxonomy: **product_category**
- Admin meta boxes for all product data
- **Full multi-column Performance Table** (admin + frontend)
- PDF document uploads per product
- Frontend product listing with category filter
- Responsive single product detail page
- Custom navigation walker with dropdown categories

---

## Installation

1. Upload the `plagin-vintech-product-main` folder to `/wp-content/plugins/`
2. Activate the plugin from **Plugins → Installed Plugins**
3. Go to **Products** in the WordPress admin sidebar to start adding products

---

## File Structure

```
plagin-vintech-product-main/
├── ventech-products.php          # Main plugin file (registers hooks)
├── assets/
│   ├── css/
│   │   ├── ventech-admin.css     # Admin styles
│   │   └── ventech-products.css  # Frontend styles
│   └── js/
│       └── ventech-products.js   # Frontend JS (nav dropdowns, card hover)
├── includes/
│   ├── ajax-handlers.php         # AJAX callbacks
│   ├── cpt-register.php          # CPT + taxonomy registration
│   ├── enqueue.php               # Script/style enqueuing
│   ├── meta-boxes.php            # All admin meta boxes + save logic
│   ├── nav-walker.php            # Custom nav walker for dropdowns
│   └── shortcodes.php            # Shortcodes for product listing
└── templates/
    ├── single-ventech_product.php      # Single product detail page
    └── taxonomy-product_category.php   # Category listing page
```

---

## Admin Meta Boxes

When editing a product you will see four meta boxes:

### 1. Product Details
| Field | Description |
|---|---|
| Product Code | SKU / model number shown on detail page |
| Construction | e.g. Aluminium extrusion |
| Finish | e.g. Powder coated white |
| Applications | e.g. Supply air, comfort cooling |
| Mounting | e.g. Surface, recessed |
| Size / Note | e.g. 600 × 90 mm — shown in the performance table heading |

### 2. Product Options & Accessories
- **Options** — one per line, e.g. `Design: Removable Core`
- **Accessories** — comma separated list

### 3. Performance Table *(updated)*
This meta box now matches the full table layout shown in the frontend (Image 3):

**Column Headers** — comma separated, editable. Default:
```
Velocity (m/s), Flow (l/s), Pressure (Pa), Throw min-max (m), NC
```

Changing the header field and clicking away will **live-update** the table columns and re-index all existing rows.

**Performance Rows**
- Click **+ Add Row** to append a new row with one input per column
- Fill in values for each column (e.g. `1.25`, `60`, `2`, `2.1 – 3.85`, `–`)
- Click **✕** on any row to remove it (with confirmation)
- Completely empty rows are automatically skipped on save

### 4. Product Documents (PDF)
- Click **+ Add Document**, then **Upload PDF** to select a file from the Media Library
- Add a label (e.g. `Installation Manual`)
- Multiple PDFs supported
- Shown as download links on the frontend detail page

---

## Frontend Display

### Single Product Page (`single-ventech_product.php`)
- Breadcrumb navigation
- Product image (featured image)
- PDF download links
- Specs table (code, construction, finish, applications, mounting)
- Options list
- Accessories
- **Full Performance Table** — all columns from admin, alternating row colours, blue header
- Back button to category

### Category Listing Page (`taxonomy-product_category.php`)
- Grid of product cards
- Thumbnail, title, code, category badge

### Shortcode
Use `[ventech_products]` on any page to show the product listing with category tabs.

---

## Performance Table — Data Format

Rows are stored as an indexed array of arrays in `_ventech_perf_rows` post meta.
Headers are stored as an indexed array in `_ventech_perf_headers`.

Example stored value for a 5-column table:
```php
// _ventech_perf_headers
['Velocity (m/s)', 'Flow (l/s)', 'Pressure (Pa)', 'Throw min-max (m)', 'NC']

// _ventech_perf_rows
[
    ['1.25', '60',    '2',     '2.1 – 3.85',  '–'],
    ['1.35', '66',    '2',     '2.4 – 4.3',   '–'],
    ['1.5',  '75',    '3',     '2.7 – 5',     '–'],
    ['1.75', '84',    '5',     '2.7 – 5.4',   '–'],
    ['1.9',  '96',    '6',     '3.3 – 6',     '15'],
    ['2',    '99.6',  '6.4',   '3.44 – 6.08', '15.8'],
    ...
]
```

---

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Theme must call `get_header()` / `get_footer()` for templates to render correctly

---

## Changelog

### v1.1.0
- **Performance Table:** Admin meta box now shows all configured columns with individual inputs per cell (matches Image 3 multi-column layout)
- **Performance Table:** Live column header editing — changing headers rebuilds the table without a page reload
- **Performance Table:** Row removal now re-indexes names correctly to prevent gaps on save
- **Performance Table:** Completely empty rows are skipped on save
- **Performance Table:** Added confirmation dialog before row removal
- **Save:** Added `current_user_can` permission check on save

### v1.0.0
- Initial release
