<!doctype html>
<html lang="en" class="app-page-404">
<head>
    <meta charset="UTF-8">
    <title>Page not found</title>
    <link rel="icon" href="<?php $app->base('/favicon.ico'); ?>" type="image/x-icon">
    <?php echo  $app->assets(['assets:app/css/style.css'], $app['yxorp/version']) ; ?>
</head>
<body class="uk-height-viewport uk-flex uk-flex-middle">

    <div class="uk-container uk-container-center uk-text-center uk-animation-slide-bottom">

        <img src="<?php $app->base('assets:app/media/icons/lighthouse.svg'); ?>" width="150" height="1050">

        <p class="uk-text-large uk-margin-large uk-text-bold">Uuuups, Page not found.</p>
        <p><a class="uk-button uk-button-link" href="<?php $app->route('/'); ?>"><?php echo $app("i18n")->get('Back to start'); ?></a></p>

    </div>

</body>
</html>
