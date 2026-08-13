<?php
// اتصال به دیتابیس MySQL
// این تنظیمات مخصوص XAMPP هست (کاربر پیش‌فرض root بدون رمز عبور)

$dbHost = 'localhost';
$dbName = 'khaneh_shopping';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(
        'خطا در اتصال به دیتابیس: ' . htmlspecialchars($e->getMessage()) .
        '<br><br>مطمئن شو که: ۱) توی XAMPP سرویس MySQL روشنه، ۲) دیتابیس khaneh_shopping رو از روی فایل schema.sql ساختی (توضیحش توی راهنما.txt هست).'
    );
}

// دسته‌بندی‌های ثابت به همراه رنگ و آیکون هر کدو (مثل برچسب شیشه‌های ادویه)
$categories = [
    'میوه و سبزیجات'   => ['color' => '#5C7A5E', 'icon' => '🥬'],
    'لبنیات و تخم‌مرغ'  => ['color' => '#D9A441', 'icon' => '🥚'],
    'گوشت و مرغ و ماهی' => ['color' => '#7A4B3A', 'icon' => '🍖'],
    'نان و غلات'        => ['color' => '#C97064', 'icon' => '🍞'],
    'ادویه و خشکبار'    => ['color' => '#B08968', 'icon' => '🌶️'],
    'نوشیدنی'           => ['color' => '#3E7C8C', 'icon' => '🥤'],
    'بهداشتی و شوینده'  => ['color' => '#8E7CC3', 'icon' => '🧴'],
    'سایر'              => ['color' => '#7C7566', 'icon' => '🛒'],
];
