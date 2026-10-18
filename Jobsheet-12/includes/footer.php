</main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Sistem Pemesanan UMKM &mdash; by Ferdy</p>
    </footer>
    <script src="/PunyaFerdy/Jobsheet-12/assets/js/app.js"></script>
    <?php if (!empty($extra_scripts)): foreach ($extra_scripts as $src): ?>
        <script src="<?php echo e($src); ?>"></script>
    <?php endforeach;
    endif; ?>
</body>
</html>