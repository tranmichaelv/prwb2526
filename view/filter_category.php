<div class="category-filter">
    <select id="category-filter" name="category_id">
        <option value="0">All categories</option>

        <?php foreach ($categories as $category): ?>
        <option value="<?= $category->get_id() ?>">
            <?= $category->get_name() ?>
        </option>
        <?php endforeach; ?>
    </select>
</div>