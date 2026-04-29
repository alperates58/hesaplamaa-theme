<?php get_header(); ?>
<div class="not-found-wrap">
    <div class="not-found-code">404</div>
    <h1 style="font-size:1.6rem;margin-bottom:10px">Sayfa Bulunamadı</h1>
    <p style="color:var(--color-text-3);margin-bottom:32px">Aradığınız sayfa taşınmış ya da kaldırılmış olabilir.</p>
    <a href="<?php echo home_url('/'); ?>" class="btn-primary">← Ana Sayfaya Dön</a>
</div>
<?php get_footer(); ?>
