<?php foreach ($news as $news_item): ?>

    <h2><?php echo $news_item['TITLE'] ?></h2>
    <div class="main">
        <?php echo $news_item['TEXT'] ?>
    </div>
    <p><a href="news/<?php echo $news_item['SLUG'] ?>">View article</a></p>

<?php endforeach ?>