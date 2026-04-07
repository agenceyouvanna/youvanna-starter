<?php get_header(); ?>
<section class="section" style="min-height:60vh;display:flex;align-items:center;margin-top:72px;">
    <div class="container" style="text-align:center;">
        <h1 style="font-size:6rem;color:var(--color-primary);">404</h1>
        <p style="font-size:1.2rem;color:var(--color-text-light);margin:16px 0 32px;">Cette page n'existe pas ou a été déplacée.</p>
        <a href="<?php echo esc_url(home_url()); ?>" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</section>
<?php get_footer(); ?>
