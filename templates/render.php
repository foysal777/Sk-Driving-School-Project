<?php
declare(strict_types=1);

/**
 * SK Driving School - Page Metadata & Template Rendering Engine
 */

if (!function_exists('getPagesConfig')) {
    function getPagesConfig(): array {
        return [
            'home' => [
                'view'        => 'pages/home.php',
                'title'       => 'Drive Master | Certified Professional Driving School',
                'description' => 'Drive Safe & Smart With Our School. Top certified driving school with 98.4% first-time pass rate, modern dual-control automatic and manual fleet.',
                'active'      => 'home',
            ],
            'about' => [
                'view'        => 'pages/about.php',
                'title'       => 'About Us | DriveMaster Professional Driving Academy',
                'description' => 'Learn about DriveMaster Driving Academy. 16+ years of DVSA certified excellence, our patient Grade-A instructors, modern dual-control hybrid cars, and our 98.4% first-time pass mission.',
                'active'      => 'about',
            ],
            'courses' => [
                'view'        => 'pages/courses.php',
                'title'       => 'Driving Courses & Pricing Packages | DriveMaster Academy',
                'description' => 'Explore DVSA certified driving courses at DriveMaster Academy. Beginner starter packs, intensive pass courses, automatic and manual lessons, and advanced motorway training.',
                'active'      => 'courses',
            ],
            'gallery' => [
                'view'        => 'pages/gallery.php',
                'title'       => 'Photo Gallery | Drive Master Professional Driving School',
                'description' => 'Explore real student test pass moments, behind-the-wheel lessons, modern dual-control training cars, and instructor guidance at Drive Master Driving School.',
                'active'      => 'gallery',
            ],
            'contact' => [
                'view'        => 'pages/contact.php',
                'title'       => 'Contact Us | DriveMaster Certified Professional Driving School',
                'description' => 'Get in touch with DriveMaster Driving School. Send us an email inquiry for driving lessons, intensive courses, test dates, and prices. Quick response guaranteed.',
                'active'      => 'contact',
            ],
            'product' => [
                'view'        => 'pages/product.php',
                'title'       => 'Course Package Details | DriveMaster Driving Academy',
                'description' => 'Explore lesson syllabus, dual-control hybrid vehicle specs, transmission options and customize your driving package with DriveMaster.',
                'active'      => 'courses',
            ],
            'cart' => [
                'view'        => 'pages/cart.php',
                'title'       => 'Shopping Cart & Booking Summary | DriveMaster Driving Academy',
                'description' => 'Review your selected driving lesson packages, customize lessons, apply promo codes, and proceed to secure Square payment.',
                'active'      => 'cart',
            ],
            'checkout' => [
                'view'        => 'pages/checkout.php',
                'title'       => 'Square Sandbox Checkout & Enrollment | DriveMaster Driving Academy',
                'description' => 'Complete your driving lesson package enrollment securely with our official Square test sandbox payment gateway and real-time administrator approval workflow.',
                'active'      => 'courses',
            ],
        ];
    }
}

if (!function_exists('renderPage')) {
    function renderPage(string $pageKey, array $overrideData = []): void {
        $pages = getPagesConfig();
        $normalizedKey = strtolower(trim($pageKey));

        // Alias index or root to home
        if ($normalizedKey === '' || $normalizedKey === 'index') {
            $normalizedKey = 'home';
        }

        if (!isset($pages[$normalizedKey])) {
            http_response_code(404);
            $normalizedKey = 'home';
        }

        $config = array_merge($pages[$normalizedKey] ?? [], $overrideData);

        $pageTitle       = $config['title'] ?? 'Drive Master | Certified Professional Driving School';
        $pageDescription = $config['description'] ?? '';
        $activePage      = $config['active'] ?? $normalizedKey;
        $extraCss        = $config['extra_css'] ?? '';
        $extraJs         = $config['extra_js'] ?? '';

        $baseDir  = dirname(__DIR__);
        $viewFile = $baseDir . '/' . ($config['view'] ?? "pages/{$normalizedKey}.php");

        // Buffer the view content
        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo '<div class="container py-5 text-center my-5"><h2>Page Not Found</h2><p class="text-muted">The requested page content is unavailable.</p><a href="home" class="btn btn-primary">Return Home</a></div>';
        }
        $content = ob_get_clean();

        // Render via master layout
        include __DIR__ . '/layout.php';
    }
}
