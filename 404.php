<?php get_header(); ?>
<section class="section error-404-section">
    <div class="container error-404">
        <h1 class="error-404-code">404</h1>
        <p class="error-404-text">Cette page n'existe pas ou a été déplacée.</p>
        <a href="<?php echo esc_url(home_url()); ?>" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</section>
<?php get_footer(); ?>