<?php
require_once '../lib/BootstrapCssSprite.php';

//$sprite = new CssSprite\BootstrapCssSprite(array(
//    'imgSourcePath' => __DIR__ . '/images/source',
//    'imgSourceExt'  => 'jpg,jpeg,gif,png',
//    'imgDestPath'   => __DIR__ . '/images/sprite.png',
//    'cssPath'       => __DIR__ . '/css/sprite.css',
//    'cssNamespace'  => 'img',
//    'cssImgUrl'     => '../images/sprite.png',
//));
$sprite = new CssSprite\BootstrapCssSprite();
$sprite->setImgSourcePath('./images/source')
    ->setImgSourceExt('jpg,jpeg,gif,png')
    ->setImgDestPath('./images/sprite.png')
    ->setCssPath('./css/sprite.css')
    ->setCssImgUrl('../images/sprite.png');

$sprite->setCheckIfImageIsCreated(false)
    ->setInitialCssStyleCompact(true)
    ->setInitialCssSelectors('.icon-category')
    ->setCompactImage(true);

$sprite->generate();

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Bootstrap CSS Sprite Demo Page</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css" />
        <link rel="stylesheet" type="text/css" href="css/sprite.css" />
    </head>
    <body>
        <?php if (count($sprite->getErrors()) > 0): ?>
            <h2>Errors occured:</h2>
            <ul>
                <?php foreach ($sprite->getErrors() as $error): ?>
                <li>
                    <?=$error['type']?>
                    <?php if (!empty($error['message'])): ?>
                        <br />
                        <i><?=$error['message']?></i>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <h2>Success!</h2>
            <?php foreach ($sprite->getTagList() as $tag): ?>
                <div class="hover-img">
                    <?php echo $tag; ?>
                    <?php echo htmlentities($tag); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </body>
</html>