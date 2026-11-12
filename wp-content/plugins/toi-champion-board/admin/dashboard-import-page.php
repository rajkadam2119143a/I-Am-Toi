<?php
defined('ABSPATH') || exit;

function tcb_admin_import_page() {
    ?>
    <div class="wrap">
        <h1>📥 Import Excel Data</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="tcb_excel_file" required />
            <input type="submit" name="tcb_import_excel" class="button button-primary" value="Import Excel" />
        </form>
        <p>Upload Excel files to update dashboard data.</p>
    </div>
    <?php
}
