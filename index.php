<?php
require __DIR__ . '/db.php';

// ---------- پردازش عملیات‌ها ----------

// افزودن قلم جدید
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $qty  = trim($_POST['quantity'] ?? '');
    $cat  = trim($_POST['category'] ?? 'سایر');

    if ($name !== '' && array_key_exists($cat, $categories)) {
        $stmt = $pdo->prepare("INSERT INTO items (name, quantity, category, purchased, created_at) VALUES (?, ?, ?, 0, ?)");
        $stmt->execute([$name, $qty, $cat, date('Y-m-d H:i:s')]);
    }
    header('Location: index.php');
    exit;
}

// تغییر وضعیت خریداری‌شده / نشده
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("UPDATE items SET purchased = 1 - purchased WHERE id = ?")->execute([$id]);
    header('Location: index.php');
    exit;
}

// حذف یک قلم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("DELETE FROM items WHERE id = ?")->execute([$id]);
    header('Location: index.php');
    exit;
}

// پاک کردن همه‌ی خریداری‌شده‌ها
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_purchased') {
    $pdo->exec("DELETE FROM items WHERE purchased = 1");
    header('Location: index.php');
    exit;
}

// ---------- خواندن اطلاعات برای نمایش ----------

$allItems = $pdo->query("SELECT * FROM items ORDER BY purchased ASC, created_at ASC")->fetchAll(PDO::FETCH_ASSOC);

$grouped = [];
foreach ($categories as $catName => $meta) {
    $grouped[$catName] = [];
}
foreach ($allItems as $item) {
    $grouped[$item['category']][] = $item;
}

$remainingCount = count(array_filter($allItems, fn($i) => !$i['purchased']));
$purchasedCount = count($allItems) - $remainingCount;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لیست خرید و آشپزخانه</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Lalezar&family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="jar-header">
    <div class="jar-lid"></div>
    <h1>لیست خرید و آشپزخانه</h1>
    <p class="subtitle">هرچی لازم داری اینجا یادداشت کن، هیچی یادت نمی‌ره</p>
    <div class="counter-row">
        <span class="counter-pill remain">🛒 <?= $remainingCount ?> مانده</span>
        <span class="counter-pill done">✅ <?= $purchasedCount ?> خریداری‌شده</span>
    </div>
</div>

<form class="add-form" method="post" action="index.php">
    <input type="hidden" name="action" value="add">
    <div class="add-form-row">
        <input type="text" name="name" placeholder="مثلاً: پیاز" required autofocus>
        <input type="text" name="quantity" placeholder="مقدار (مثلاً ۲ کیلو)">
    </div>
    <div class="add-form-row">
        <select name="category">
            <?php foreach ($categories as $catName => $meta): ?>
                <option value="<?= htmlspecialchars($catName) ?>"><?= $meta['icon'] ?> <?= htmlspecialchars($catName) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-add">افزودن به لیست</button>
    </div>
</form>

<div class="list-wrap">
<?php $hasAnyItem = false; ?>
<?php foreach ($categories as $catName => $meta): ?>
    <?php if (empty($grouped[$catName])) continue; ?>
    <?php $hasAnyItem = true; ?>
    <section class="category-block">
        <div class="category-tab" style="background: <?= $meta['color'] ?>;">
            <span class="cat-icon"><?= $meta['icon'] ?></span>
            <span class="cat-name"><?= htmlspecialchars($catName) ?></span>
            <span class="cat-count"><?= count($grouped[$catName]) ?></span>
        </div>
        <ul class="item-list">
            <?php foreach ($grouped[$catName] as $item): ?>
                <li class="item-row <?= $item['purchased'] ? 'is-purchased' : '' ?>">
                    <form method="post" action="index.php" class="toggle-form">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="check-circle" aria-label="خریداری شد"></button>
                    </form>
                    <div class="item-text">
                        <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                        <?php if (!empty($item['quantity'])): ?>
                            <span class="item-qty"><?= htmlspecialchars($item['quantity']) ?></span>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="index.php" class="delete-form">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn-delete" aria-label="حذف">✕</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endforeach; ?>

<?php if (!$hasAnyItem): ?>
    <div class="empty-state">
        <p>🧺 لیست خالیه</p>
        <p class="empty-sub">از فرم بالا اولین چیزی که لازم داری رو اضافه کن</p>
    </div>
<?php endif; ?>
</div>

<?php if ($purchasedCount > 0): ?>
<form method="post" action="index.php" class="clear-form">
    <input type="hidden" name="action" value="clear_purchased">
    <button type="submit" class="btn-clear">پاک کردن موارد خریداری‌شده (<?= $purchasedCount ?>)</button>
</form>
<?php endif; ?>

</body>
</html>
