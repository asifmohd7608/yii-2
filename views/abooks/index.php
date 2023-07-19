<?php

use yii\helpers\Html;

?>
<h2 class="books_header">Books</h2>
<table class="books_table">
    <th>Name</th>
    <th>Cover</th>
    <th>Category Type</th>
    <th>ISBN</th>
    <th>Author</th>
    <th>Language</th>
    <th>Published On</th>
    <th>Total Copies</th>
    <th>Available Copies</th>
    <th>Available Status</th>
    <th>Price</th>
    <?php foreach ($books as $book) : ?>
        <tr>
            <td><?= Html::encode(ucfirst($book['Book_Title'])) ?></td>
            <td><img class="table_book_cover" src=<?= Html::encode($book['File_Path']) ?> alt=""></td>
            <td><?= Html::encode(ucfirst($book['Category_Type'])) ?></td>
            <td><?= Html::encode($book['ISBN']) ?></td>
            <td><?= Html::encode(ucfirst($book['Author'])) ?></td>
            <td><?= Html::encode(ucfirst($book['Language'])) ?></td>
            <td><?= Html::encode($book['Publication_Year']) ?></td>
            <td><?= Html::encode($book['No_Of_Copies_Actual']) ?></td>
            <td><?= Html::encode($book['No_Of_Copies_Current']) ?></td>
            <td><?php
                if ($book['Available'] == 1) {
                    echo "In Stock";
                } else {
                    echo "Out Of Stock";
                };
                ?>
            </td>
            <td><?= Html::encode($book['Price']) ?></td>
        </tr>
    <?php endforeach ?>
</table>