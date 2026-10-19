</main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Sistem Pemesanan UMKM &mdash; by Ferdy</p>
    </footer>
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
    <?php if (!empty($extra_scripts)): foreach ($extra_scripts as $src): ?>
        <script src="<?php echo $src; ?>"></script>
    <?php endforeach;
    endif; ?>
</body>
</html>