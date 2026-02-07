=== Woo Advanced Ajax Filter ===
Contributors: Hamta
Tags: woocommerce, filter, ajax, product filter
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

فیلتر پیشرفته ووکامرس با قابلیت فیلتر بر اساس دسته‌بندی، قیمت و ویژگی‌ها

== Description ==

این افزونه یک فیلتر پیشرفته Ajax برای ووکامرس ارائه می‌دهد که امکانات زیر را دارد:

* فیلتر بر اساس دسته‌بندی محصولات
* فیلتر قیمت با اسلایدر
* بارگذاری سریع با Ajax
* سازگار با تم پورتو
* نمایش کامل labels (تخفیف، جدید، ویژه، موجودی)

== Installation ==

1. فایل zip افزونه را آپلود کنید
2. افزونه را فعال کنید
3. شورت‌کد `[woo_advanced_filter]` را در صفحه فروشگاه قرار دهید

== ساختار فایل‌ها ==

woo-advanced-ajax-filter/
├── woo-advanced-ajax-filter.php (فایل اصلی)
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── filter.js
└── README.txt

== استفاده ==

بعد از نصب و فعال‌سازی، شورت‌کد زیر را در صفحه فروشگاه خود قرار دهید:

[woo_advanced_filter]

== تنظیمات ==

* برای تغییر حداکثر قیمت: در فایل assets/js/filter.js
* برای تغییر تعداد محصولات: در فایل اصلی افزونه

== Changelog ==

= 1.1 =
* رفع خطای Undefined offset
* بهبود سرعت بارگذاری
* اضافه شدن نمایش زیردسته‌ها
* بهینه‌سازی Ajax requests
* اضافه شدن debounce برای اسلایدر قیمت

= 1.0 =
* نسخه اولیه
