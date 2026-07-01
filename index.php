<?php
// ─────────────────────────────────────────────────────────────────────
// Fetch all content from MySQL — NO hardcoded data on this page
// ─────────────────────────────────────────────────────────────────────
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'arun_portfolio';

$siteContent = [];
$theme = [];

try {
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if (!$conn->connect_error) {
        $conn->set_charset('utf8mb4');

        // Fetch content
        $res = $conn->query('SELECT section, content FROM site_content');
        while ($row = $res->fetch_assoc()) {
            $siteContent[$row['section']] = json_decode($row['content'], true);
        }

        // Fetch theme
        $res2 = $conn->query('SELECT setting_key, setting_value FROM theme_settings');
        while ($row = $res2->fetch_assoc()) {
            $theme[$row['setting_key']] = $row['setting_value'];
        }

        $conn->close();
    }
} catch (Exception $e) {
    // DB not available
}

// ── Defaults (used only when DB is empty / not yet set up) ──────────
$hero        = $siteContent['hero']        ?? [];
$about       = $siteContent['about']       ?? [];
$services    = $siteContent['services']    ?? [];
$experience  = $siteContent['experience']  ?? [];
$technology  = $siteContent['technology']  ?? [];
$expertise   = $siteContent['expertise']   ?? [];
$contact     = $siteContent['contact']     ?? [];
$meta        = $siteContent['meta']        ?? [];
$opportunity = $siteContent['opportunity'] ?? [];
$clients     = $siteContent['clients']     ?? [];
$difference  = $siteContent['difference']  ?? [];
$capabilities = $siteContent['capabilities'] ?? [];

// Hero defaults
$heroName        = $hero['name']        ?? 'Arun Kumar Jayakumar';
$heroTitle       = $hero['title']       ?? 'Fractional CDO & Enterprise Data Strategist';
$heroSubtitle    = $hero['subtitle']    ?? 'Transforming Data into Strategic Advantage';
$heroDescription = $hero['description'] ?? '14+ years leading high-stakes data and AI transformations across global enterprises';
$heroCtaText     = $hero['cta_text']    ?? 'Book a Strategy Call';
$heroCtaUrl      = $hero['cta_url']     ?? '#contact';
$heroTaglines    = $hero['taglines']    ?? ['Data Strategist', 'AI Advisor', 'Enterprise Transformation Leader'];
$heroBgImage     = $hero['bg_image']    ?? '/assets/images/hero_bg.jpg';
if (empty($heroBgImage) || !file_exists(__DIR__ . '/' . ltrim($heroBgImage, '/'))) {
    $heroBgImage = '/assets/images/hero_bg.jpg';
}

// About defaults
$aboutHeadline   = $about['headline']   ?? 'About Me';
$aboutSubheading = $about['subheading'] ?? 'Enterprise Data Leader';
$aboutBio        = $about['bio']        ?? '';
$aboutQuote      = $about['quote']      ?? '';
$aboutImage      = $about['image']      ?? '/assets/images/arun_kumar.png';
$aboutStats      = $about['stats']      ?? [];

// Services defaults
$serviceItems = $services['items'] ?? [];

// Experience defaults
$expJobs = $experience['jobs'] ?? [];

// Technology defaults
$techCategories = $technology['categories'] ?? [];

// Expertise defaults
$expertiseItems = $expertise['items'] ?? [];

// Opportunity defaults
$oppTitle         = $opportunity['title']         ?? '';
$oppFrictionTitle = $opportunity['friction_title'] ?? '';
$oppFrictionText  = $opportunity['friction_text']  ?? '';
$oppQuote         = $opportunity['quote']         ?? '';
$oppSolutionTitle = $opportunity['solution_title'] ?? '';
$oppSolutionText  = $opportunity['solution_text']  ?? '';
$oppPillars       = $opportunity['pillars']       ?? [];
$oppBottomQuote   = $opportunity['bottom_quote']   ?? '';

// Ideal Clients defaults
$clientIntro = $clients['intro'] ?? '';
$clientTabs = $clients['tabs'] ?? [];

// Difference defaults
$diffTitle      = $difference['title']      ?? '';
$diffSubheading = $difference['subheading'] ?? '';
$diffText1      = $difference['text1']      ?? '';
$diffText2      = $difference['text2']      ?? '';
$diffCards      = $difference['cards']      ?? [];

// Contact defaults
$contactHeadline = $contact['headline'] ?? 'Get In Touch';
$contactSubheading = $contact['subheading'] ?? 'Book a Strategy Call';
$contactText     = $contact['text']     ?? '';
$contactEmail    = $contact['email']    ?? '';
$contactPhone    = $contact['phone']    ?? '';
$contactLinkedin = $contact['linkedin'] ?? '';
$contactCalendly = $contact['calendly'] ?? '';
$contactLocation = $contact['location'] ?? '';

// Meta defaults
$metaTitle       = $meta['title']       ?? 'Arun Kumar Jayakumar | Fractional CDO & Enterprise Data Strategist';
$metaDescription = $meta['description'] ?? '';
$metaKeywords    = $meta['keywords']    ?? '';

// Theme defaults
$primaryColor = $theme['primary_color'] ?? '#1d4ed8';
$accentColor  = $theme['accent_color']  ?? '#60a5fa';
$bgColor      = $theme['bg_color']      ?? '#061022';
$textColor    = $theme['text_color']    ?? '#f3f4f6';
$sidebarBg    = $theme['sidebar_bg']    ?? '#03050a';

// ── Asset Paths ──────────────────────────────────────────────────────
$cssPath = 'src/style.css?v=' . filemtime(__DIR__ . '/src/style.css');
$jsPath = 'src/main.js?v=' . filemtime(__DIR__ . '/src/main.js');

function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

function hexToRgb($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) == 6) {
        $r = $hex[0].$hex[1];
        $g = $hex[2].$hex[3];
        $b = $hex[4].$hex[5];
    } elseif (strlen($hex) == 3) {
        $r = $hex[0].$hex[0];
        $g = $hex[1].$hex[1];
        $b = $hex[2].$hex[2];
    } else {
        return '6, 9, 19';
    }
    return hexdec($r) . ', ' . hexdec($g) . ', ' . hexdec($b);
}

function getVerticalIcon($name) {
    $l = strtolower($name);
    if (str_contains($l, 'manufacturing')) {
        return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#60a5fa">
            <path d="M2 21V13L6 10V13L10 10V21Z" fill="rgba(96, 165, 250,0.1)" stroke-width="1.2" stroke-linejoin="round" />
            <rect x="10" y="13" width="12" height="8" rx="0.5" fill="rgba(96, 165, 250,0.07)" stroke-width="1.2" />
            <rect x="15" y="7" width="3.5" height="6" fill="rgba(96,165,250,0.15)" stroke-width="1" />
            <rect x="7" y="17" width="2.5" height="4" fill="rgba(96, 165, 250,0.25)" stroke-width="0.8" />
            <g>
              <animateTransform attributeName="transform" type="rotate" from="0 18 17.5" to="360 18 17.5" dur="4s" repeatCount="indefinite" />
              <circle cx="18" cy="17.5" r="2.8" stroke-width="1.2" fill="rgba(96, 165, 250,0.08)" />
              <circle cx="18" cy="17.5" r="0.9" fill="#60a5fa" stroke="none" />
              <line x1="18" y1="14.7" x2="18" y2="20.3" stroke-width="0.8" />
              <line x1="15.2" y1="17.5" x2="20.8" y2="17.5" stroke-width="0.8" />
            </g>
            <circle cx="16.5" cy="5" r="2" fill="#60a5fa" stroke="none" opacity="0.2">
              <animate attributeName="cy" values="5;2;5" dur="3s" repeatCount="indefinite" />
              <animate attributeName="opacity" values="0.2;0;0.2" dur="3s" repeatCount="indefinite" />
              <animate attributeName="r" values="2;3.5;2" dur="3s" repeatCount="indefinite" />
            </circle>
        </svg>';
    } else if (str_contains($l, 'supply chain') || str_contains($l, 'supplychain')) {
        return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="3" fill="rgba(96, 165, 250,0.3)" stroke="#60a5fa" stroke-width="1.5" />
            <line x1="12" y1="9" x2="12" y2="3" stroke="#60a5fa" stroke-width="1.1" />
            <line x1="14.6" y1="10.5" x2="20" y2="6.5" stroke="#60a5fa" stroke-width="1.1" />
            <line x1="14.6" y1="13.5" x2="20" y2="17.5" stroke="#60a5fa" stroke-width="1.1" />
            <line x1="12" y1="15" x2="12" y2="21" stroke="#60a5fa" stroke-width="1.1" />
            <line x1="9.4" y1="13.5" x2="4" y2="17.5" stroke="#60a5fa" stroke-width="1.1" />
            <line x1="9.4" y1="10.5" x2="4" y2="6.5" stroke="#60a5fa" stroke-width="1.1" />
            <circle cx="12" cy="2" r="2" fill="rgba(96, 165, 250,0.25)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="21" cy="6" r="2" fill="rgba(96, 165, 250,0.2)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="21" cy="18" r="2" fill="rgba(96, 165, 250,0.2)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="12" cy="22" r="2" fill="rgba(96, 165, 250,0.25)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="3" cy="18" r="2" fill="rgba(96, 165, 250,0.2)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="3" cy="6" r="2" fill="rgba(96, 165, 250,0.2)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="12" cy="12" r="5" fill="none" stroke="#60a5fa" stroke-width="0.5" opacity="0.35">
              <animate attributeName="r" values="3;7;3" dur="2.5s" repeatCount="indefinite" />
              <animate attributeName="opacity" values="0.5;0;0.5" dur="2.5s" repeatCount="indefinite" />
            </circle>
            <circle cx="12" cy="6" r="1.2" fill="#60a5fa">
              <animate attributeName="cy" values="9;2;9" dur="2s" repeatCount="indefinite" />
              <animate attributeName="opacity" values="1;0;1" dur="2s" repeatCount="indefinite" />
            </circle>
        </svg>';
    } else if (str_contains($l, 'logistics')) {
        return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
            <rect x="1" y="9" width="13" height="8" rx="1" fill="rgba(96,165,250,0.15)" stroke="#60a5fa" stroke-width="1.3" />
            <line x1="7" y1="9" x2="7" y2="17" stroke="#60a5fa" stroke-width="0.7" opacity="0.5" />
            <line x1="4" y1="12" x2="10" y2="12" stroke="#60a5fa" stroke-width="0.6" opacity="0.4" />
            <path d="M14 12 L14 17 L22 17 L22 13.5 L20 9 L14 9 Z" fill="rgba(96, 165, 250,0.2)" stroke="#60a5fa" stroke-width="1.3" stroke-linejoin="round" />
            <path d="M15.5 10.5 L20 10.5 L21.5 13.5 L15.5 13.5 Z" fill="rgba(96, 165, 250,0.35)" stroke="#60a5fa" stroke-width="0.8" />
            <circle cx="5" cy="19.5" r="2.5" fill="rgba(10,10,20,0.9)" stroke="#60a5fa" stroke-width="1.3" />
            <circle cx="5" cy="19.5" r="0.8" fill="#60a5fa" />
            <circle cx="11.5" cy="19.5" r="2.5" fill="rgba(10,10,20,0.9)" stroke="#60a5fa" stroke-width="1.3" />
            <circle cx="11.5" cy="19.5" r="0.8" fill="#60a5fa" />
            <circle cx="19" cy="19.5" r="2.5" fill="rgba(10,10,20,0.9)" stroke="#60a5fa" stroke-width="1.3" />
            <circle cx="19" cy="19.5" r="0.8" fill="#60a5fa" />
            <circle cx="22" cy="15" r="1" fill="#60a5fa">
              <animate attributeName="opacity" values="0.5;1;0.5" dur="1.5s" repeatCount="indefinite" />
            </circle>
        </svg>';
    } else if (str_contains($l, 'public sector') || str_contains($l, 'public')) {
        return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
            <polygon points="12,1 22,7 2,7" fill="rgba(96, 165, 250,0.2)" stroke="#60a5fa" stroke-width="1.3" stroke-linejoin="round" />
            <rect x="2" y="7" width="20" height="2.5" fill="rgba(96,165,250,0.15)" stroke="#60a5fa" stroke-width="1" />
            <rect x="3" y="9.5" width="3" height="11" fill="rgba(96, 165, 250,0.12)" stroke="#60a5fa" stroke-width="1" />
            <rect x="8" y="9.5" width="3" height="11" fill="rgba(96, 165, 250,0.08)" stroke="#60a5fa" stroke-width="1" />
            <rect x="13" y="9.5" width="3" height="11" fill="rgba(96, 165, 250,0.08)" stroke="#60a5fa" stroke-width="1" />
            <rect x="18" y="9.5" width="3" height="11" fill="rgba(96, 165, 250,0.12)" stroke="#60a5fa" stroke-width="1" />
            <rect x="1.5" y="20.5" width="21" height="2" rx="0.3" fill="rgba(96, 165, 250,0.18)" stroke="#60a5fa" stroke-width="1" />
            <rect x="1" y="22.5" width="22" height="1" rx="0.3" fill="rgba(96, 165, 250,0.1)" stroke="#60a5fa" stroke-width="0.8" />
            <circle cx="12" cy="4" r="1.8" fill="#60a5fa">
              <animate attributeName="opacity" values="0.5;1;0.5" dur="2.5s" repeatCount="indefinite" />
            </circle>
        </svg>';
    } else {
        return '<svg width="30" height="30" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="2" width="20" height="5.5" rx="1.2" fill="rgba(96,165,250,0.15)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="19.5" cy="4.75" r="1.3" fill="#60a5fa" />
            <rect x="2" y="9.25" width="20" height="5.5" rx="1.2" fill="rgba(96, 165, 250,0.12)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="19.5" cy="12" r="1.3" fill="#60a5fa" />
            <rect x="2" y="16.5" width="20" height="5.5" rx="1.2" fill="rgba(96, 165, 250,0.15)" stroke="#60a5fa" stroke-width="1.2" />
            <circle cx="19.5" cy="19.25" r="1.3" fill="#60a5fa" />
        </svg>';
    }
}

function renderMarqueeGroup($items) {
    foreach ($items as $item) {
        $iconSvg = getVerticalIcon($item);
        ?>
        <div class="marquee-item">
          <div class="icon-badge">
            <?= $iconSvg ?>
          </div>
          <span class="text-xl font-bold text-white group-hover:text-[#3b82f6] transition-colors font-serif"><?= h($item) ?></span>
        </div>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="lenis text-[4.2vw] md:text-[1.5vw] lg:text-[1vw]">

<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/svg+xml" href="/vite.svg" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= h($metaTitle) ?></title>
    <meta name="description" content="<?= h($metaDescription) ?>" />
  <meta name="keywords" content="<?= h($metaKeywords) ?>" />

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700;800&family=Syne:wght@700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <link rel="stylesheet" href="<?= h($cssPath) ?>" />
  <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
  <style type="tailwindcss">
    @theme {
      --font-sans: 'Montserrat', sans-serif;
      --font-serif: 'Syne', sans-serif;
      --font-inter: 'Inter', sans-serif;

      --color-bg-deep: <?= h($sidebarBg) ?>;
      --color-bg-base: <?= h($bgColor) ?>;
      --color-bg-surface: <?= h($sidebarBg) ?>;
      --color-primary: <?= h($accentColor) ?>;
      --color-accent: <?= h($accentColor) ?>;
      --color-dark-accent: <?= h($primaryColor) ?>;
    }
  </style>
  <!-- ── Dynamic Theme CSS Variables from Admin ── -->
  <style>
    /* ── Font Family Overrides (ensures Google Fonts load correctly) ── */
    :root {
      --font-sans:  'Montserrat', sans-serif;
      --font-serif: 'Syne', sans-serif;
      --font-inter: 'Inter', sans-serif;
    }

    html, body {
      font-family: 'Montserrat', sans-serif !important;
    }

    /* Tailwind font utility class overrides */
    .font-sans  { font-family: 'Montserrat', sans-serif !important; }
    .font-serif { font-family: 'Syne', sans-serif !important; }
    .font-inter { font-family: 'Inter', sans-serif !important; }
    .font-mono  { font-family: ui-monospace, 'Courier New', monospace !important; }

    /* ── Dynamic Theme CSS Variables from Admin ── */
    :root {
      --color-primary:  <?= h($primaryColor) ?>;
      --color-accent:   <?= h($accentColor) ?>;
      --color-bg:       <?= h($bgColor) ?>;
      --color-text:     <?= h($textColor) ?>;
      --color-sidebar:  <?= h($sidebarBg) ?>;
      --hero-bg-url:    url('<?= h($heroBgImage) ?>');
    }
    #hero::before {
      display: none !important;
      content: none !important;
    }
    body {
      background-color: <?= h($bgColor) ?> !important;
      color: <?= h($textColor) ?> !important;
    }
    /* Override hardcoded values with theme variables */
    .text-\[\#1d4ed8\], .text-\[\#3b82f6\], .text-\[\#60a5fa\], .text-violet-500 { color: var(--color-accent) !important; }
    .bg-\[\#060913\], .bg-\[\#0a0a0a\] { background-color: var(--color-bg) !important; }
    .bg-\[\#03050a\] { background-color: var(--color-sidebar) !important; }

    /* ── Dynamic Hover Neon Border & Glow Overrides ── */
    .marquee-item:hover {
      border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4), 
                  0 0 20px color-mix(in srgb, var(--color-accent) 20%, transparent) !important;
    }
    .service-card:hover {
      border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 
                  0 0 25px color-mix(in srgb, var(--color-accent) 25%, transparent), 
                  inset 0 0 15px color-mix(in srgb, var(--color-accent) 8%, transparent) !important;
    }
    .premium-card:hover {
      border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.55), 
                  0 0 25px color-mix(in srgb, var(--color-accent) 25%, transparent), 
                  inset 0 0 15px color-mix(in srgb, var(--color-accent) 8%, transparent) !important;
    }
    .exp-card:hover {
      border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 
                  0 0 25px color-mix(in srgb, var(--color-accent) 20%, transparent), 
                  inset 0 0 15px color-mix(in srgb, var(--color-accent) 8%, transparent) !important;
    }
    .opp-card:hover {
      border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 
                  0 0 25px color-mix(in srgb, var(--color-accent) 20%, transparent), 
                  inset 0 0 15px color-mix(in srgb, var(--color-accent) 8%, transparent) !important;
    }
    .nav-icon-box:hover, .nav-item:hover .nav-icon-box {
      border-color: color-mix(in srgb, var(--color-accent) 35%, transparent) !important;
      box-shadow: 0 0 15px color-mix(in srgb, var(--color-accent) 15%, transparent) !important;
    }
    .hover\:border-\[\#1d4ed8\]\/30:hover { border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important; }
    .hover\:border-\[\#1d4ed8\]\/45:hover { border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important; }
    .hover\:border-\[\#1d4ed8\]\/50:hover { border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important; }
    .hover\:border-\[\#60a5fa\]\/40:hover { border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important; }
    .group-hover\:border-\[\#1d4ed8\]\/35:hover, .group:hover .group-hover\:border-\[\#1d4ed8\]\/35 { border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important; }
    .group-hover\:border-\[\#3b82f6\]\/50:hover, .group:hover .group-hover\:border-\[\#3b82f6\]\/50 { border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important; }

    /* Swiper slide / Impact card overrides */
    .swiper-slide {
      border-color: rgba(255, 255, 255, 0.07) !important;
      transition: transform 0.5s ease, border-color 0.5s ease, box-shadow 0.5s ease, filter 0.5s ease, opacity 0.5s ease !important;
    }
    .swiper-slide-active {
      border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.55), 
                  0 0 30px color-mix(in srgb, var(--color-accent) 25%, transparent), 
                  inset 0 0 15px color-mix(in srgb, var(--color-accent) 8%, transparent) !important;
    }
    .swiper-slide:hover {
      border-color: color-mix(in srgb, var(--color-accent) 55%, transparent) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 
                  0 0 35px color-mix(in srgb, var(--color-accent) 30%, transparent), 
                  inset 0 0 15px color-mix(in srgb, var(--color-accent) 10%, transparent) !important;
    }
  </style>
</head>

<body class="bg-[var(--color-bg)] text-[#f3f4f6] font-sans antialiased selection:bg-violet-500 selection:text-white">

  <!-- Premium Preloader/Loading Screen -->
  <div id="preloader"
    class="fixed inset-0 bg-[#03050a] z-[9999] flex flex-col items-center justify-center select-none transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)]">
    <div class="relative flex flex-col items-center gap-6">
      <!-- Glow ambient background -->
      <div class="absolute w-36 h-36 bg-[#1d4ed8]/10 rounded-full blur-2xl animate-pulse"></div>

      <!-- Premium Spinner Indicator -->
      <div class="w-16 h-16 rounded-full border-2 border-white/5 border-t-[#60a5fa] animate-spin"></div>

      <!-- Brand Text -->
      <div class="text-xs font-bold tracking-[0.3em] text-[#60a5fa]/80 uppercase font-sans animate-pulse text-center">
        Welcome to my portfolio
      </div>
    </div>
  </div> <!-- Left Vertical Navigation Header (Fixed) -->
  <aside id="main-sidebar"
    class="hidden md:flex fixed top-0 left-0 h-screen w-[100px] z-50 bg-[#03050a] border-r border-white/5 flex-col items-center justify-between py-10 select-none">
    <!-- Logo stylized as 'Arun Kumar Jayakumar' -->
    <!-- User Initials / Profile avatar (Moved to Top) -->
    <div
      class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#3b82f6] to-[#1d4ed8] flex items-center justify-center border border-white/10 shadow-[0_0_10px_rgba(29,78,216,0.15)] text-black text-xs font-semibold cursor-pointer">
      JAK
    </div>

    <!-- Navigation Items -->
    <div class="flex flex-col gap-8 w-full items-center">
      <!-- Home Link -->
      <a href="#hero"
        class="nav-item group flex flex-col items-center gap-1.5 text-gray-500 hover:text-white transition-all duration-300 relative py-1">
        <div
          class="nav-icon-box w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:border-[#1d4ed8]/35 group-hover:bg-[#1d4ed8]/10 group-hover:text-[#60a5fa] transition-all duration-300 relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
            </path>
          </svg>
        </div>
        <span
          class="text-[9.5px] font-bold tracking-widest uppercase opacity-75 group-hover:opacity-100 transition-opacity">Home</span>
        <span
          class="active-dot absolute -right-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#60a5fa] opacity-0 scale-0 transition-all duration-300 shadow-[0_0_8px_#60a5fa]"></span>
      </a>

      <!-- About Link -->
      <a href="#about"
        class="nav-item group flex flex-col items-center gap-1.5 text-gray-500 hover:text-white transition-all duration-300 relative py-1">
        <div
          class="nav-icon-box w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:border-[#1d4ed8]/35 group-hover:bg-[#1d4ed8]/10 group-hover:text-[#60a5fa] transition-all duration-300 relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
            </path>
          </svg>
        </div>
        <span
          class="text-[9.5px] font-bold tracking-widest uppercase opacity-75 group-hover:opacity-100 transition-opacity">About</span>
        <span
          class="active-dot absolute -right-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#60a5fa] opacity-0 scale-0 transition-all duration-300 shadow-[0_0_8px_#60a5fa]"></span>
      </a>

      <!-- Services Link -->
      <a href="#services"
        class="nav-item group flex flex-col items-center gap-1.5 text-gray-500 hover:text-white transition-all duration-300 relative py-1">
        <div
          class="nav-icon-box w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:border-[#1d4ed8]/35 group-hover:bg-[#1d4ed8]/10 group-hover:text-[#60a5fa] transition-all duration-300 relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
            </path>
          </svg>
        </div>
        <span
          class="text-[9.5px] font-bold tracking-widest uppercase opacity-75 group-hover:opacity-100 transition-opacity">Services</span>
        <span
          class="active-dot absolute -right-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#60a5fa] opacity-0 scale-0 transition-all duration-300 shadow-[0_0_8px_#60a5fa]"></span>
      </a>

      <!-- Experience Link -->
      <a href="#experience"
        class="nav-item group flex flex-col items-center gap-1.5 text-gray-500 hover:text-white transition-all duration-300 relative py-1">
        <div
          class="nav-icon-box w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:border-[#1d4ed8]/35 group-hover:bg-[#1d4ed8]/10 group-hover:text-[#60a5fa] transition-all duration-300 relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222">
            </path>
          </svg>
        </div>
        <span
          class="text-[9.5px] font-bold tracking-widest uppercase opacity-75 group-hover:opacity-100 transition-opacity">Experience</span>
        <span
          class="active-dot absolute -right-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#60a5fa] opacity-0 scale-0 transition-all duration-300 shadow-[0_0_8px_#60a5fa]"></span>
      </a>

      <!-- Technology Link -->
      <a href="#technology"
        class="nav-item group flex flex-col items-center gap-1.5 text-gray-500 hover:text-white transition-all duration-300 relative py-1">
        <div
          class="nav-icon-box w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center border border-white/5 group-hover:border-[#1d4ed8]/35 group-hover:bg-[#1d4ed8]/10 group-hover:text-[#60a5fa] transition-all duration-300 relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z">
            </path>
          </svg>
        </div>
        <span
          class="text-[9.5px] font-bold tracking-widest uppercase opacity-75 group-hover:opacity-100 transition-opacity">Technology</span>
        <span
          class="active-dot absolute -right-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 rounded-full bg-[#60a5fa] opacity-0 scale-0 transition-all duration-300 shadow-[0_0_8px_#60a5fa]"></span>
      </a>
    </div>

    <!-- Spacer placeholder to maintain centered navigation items alignment -->
    <div class="w-10 h-10"></div>
  </aside>

  <!-- Mobile Sticky Header (Fixed) -->
  <header id="mobile-header"
    class="md:hidden fixed top-0 left-0 right-0 h-20 bg-[#03050a]/85 backdrop-blur-md border-b border-white/5 z-50 flex items-center justify-between px-6 select-none">
    <!-- Brand Avatar Logo -->
    <a href="#hero"
      class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#3b82f6] to-[#1d4ed8] flex items-center justify-center border border-white/10 shadow-[0_0_10px_rgba(29,78,216,0.15)] text-black text-xs font-bold cursor-pointer transition-transform hover:scale-105 duration-300">
      JAK
    </a>

    <!-- Hamburger Button -->
    <button id="mobile-menu-toggle"
      class="w-11 h-11 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 hover:border-white/20 transition-all duration-300 focus:outline-none cursor-pointer"
      aria-label="Toggle menu">
      <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2"
        viewBox="0 0 24 24">
        <path id="hamburger-path" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>
  </header>

  <!-- Mobile Menu Drawer (Overlay) -->
  <div id="mobile-menu"
    class="md:hidden fixed inset-0 bg-[#03050a]/98 backdrop-blur-xl z-[49] flex flex-col justify-center px-10 pt-16">
    <nav class="flex flex-col gap-8">
      <a href="#hero"
        class="mobile-nav-item flex items-center gap-4 text-2xl font-semibold text-gray-400 hover:text-[#60a5fa] transition-colors py-2 relative">
        <svg class="w-6 h-6 text-[#1d4ed8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
          </path>
        </svg>
        <span>Home</span>
      </a>
      <a href="#about"
        class="mobile-nav-item flex items-center gap-4 text-2xl font-semibold text-gray-400 hover:text-[#60a5fa] transition-colors py-2 relative">
        <svg class="w-6 h-6 text-[#1d4ed8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
          </path>
        </svg>
        <span>About</span>
      </a>
      <a href="#services"
        class="mobile-nav-item flex items-center gap-4 text-2xl font-semibold text-gray-400 hover:text-[#60a5fa] transition-colors py-2 relative">
        <svg class="w-6 h-6 text-[#1d4ed8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
          </path>
        </svg>
        <span>Services</span>
      </a>
      <a href="#experience"
        class="mobile-nav-item flex items-center gap-4 text-2xl font-semibold text-gray-400 hover:text-[#60a5fa] transition-colors py-2 relative">
        <svg class="w-6 h-6 text-[#1d4ed8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222">
          </path>
        </svg>
        <span>Experience</span>
      </a>
      <a href="#technology"
        class="mobile-nav-item flex items-center gap-4 text-2xl font-semibold text-gray-400 hover:text-[#60a5fa] transition-colors py-2 relative">
        <svg class="w-6 h-6 text-[#1d4ed8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z">
          </path>
        </svg>
        <span>Technology</span>
      </a>
    </nav>
  </div>

  <!-- Main Container -->
  <main class="w-full md:pl-[100px] pt-20 md:pt-0">

    <section id="hero" class="relative min-h-screen w-full overflow-hidden flex items-center">
      <!-- Hero Background Image Layer -->
      <div class="absolute inset-0 z-0 pointer-events-none"
        style="background-image: linear-gradient(to bottom, transparent 65%, var(--color-bg) 100%), url('<?= h($heroBgImage) ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.35;">
      </div>

      <div class="absolute top-1/3 left-1/4 w-[500px] h-[500px] rounded-full pointer-events-none z-[1]"
        style="background: radial-gradient(circle, rgba(29,78,216,0.08) 0%, transparent 70%); filter: blur(60px);">
      </div>
      <div class="absolute bottom-1/4 right-1/3 w-[400px] h-[400px] rounded-full pointer-events-none z-[1]"
        style="background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%); filter: blur(80px);">
      </div>


      <div
        class="hero-content relative z-10 w-full max-w-7xl mx-auto px-12 md:px-20 flex flex-col justify-center items-center text-center select-none">

        <!-- Top: Role Pill -->
        <div class="hero-badge flex items-center justify-center gap-3 mb-8">
        </div>

        <!-- Name + Decorative Rule -->
        <div class="flex items-start justify-center gap-6 mb-6">

          <!-- Name Block -->
          <div>
            <h1 class="hero-name font-sans font-extrabold leading-[1] tracking-wide text-white">
              <span class="block text-[clamp(3rem,7vw,6rem)]"><?= h($heroName) ?></span>
            </h1>
            <div id="hero-role"
              class="text-lg mt-8 uppercase font-sans tracking-widest opacity-90 min-h-[1.5em] inline-block"></div>
            <div class="hero-tagline flex flex-wrap justify-center items-center gap-3 mt-6 select-none">
              <?php foreach ($heroTaglines as $tagline): ?>
              <span
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold tracking-widest uppercase font-inter text-gray-300 bg-white/[0.02] border border-white/5 hover:border-[#1d4ed8]/30 hover:bg-[#1d4ed8]/5 hover:text-[#3b82f6] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_20px_rgba(29,78,216,0.15)] cursor-default">
                <span class="w-1.5 h-1.5 rounded-full bg-[#1d4ed8]"></span>
                <?= h($tagline) ?>
              </span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div>


    </section>

    <section id="expertise" class="py-10 lg:py-24 overflow-hidden w-full select-none">
      <div class="max-w-[90%] mx-auto  md:px-16 mb-12">
        <h2 class="text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">Core Verticals</h2>
        <p class="text-3xl md:text-5xl font-serif font-bold text-white">Strategic Domain Expertise</p>
      </div>

      <style>
        /* Marquee Specific Styles */
        .marquee-track {
          display: flex;
          width: max-content;
          gap: 2rem;
        }

        .marquee-group {
          display: flex;
          flex-shrink: 0;
          gap: 2rem;
          animation: scrollMarquee 30s linear infinite;
        }


        @keyframes scrollMarquee {
          0% {
            transform: translateX(0);
          }

          100% {
            transform: translateX(calc(-100% - 2rem));
          }
        }

        .marquee-item {
          display: flex;
          align-items: center;
          gap: 1.5rem;
          flex-shrink: 0;
          padding: 1rem 2rem;
          background: rgba(255, 255, 255, 0.02);
          border: 1px solid rgba(255, 255, 255, 0.05);
          border-radius: 1.5rem;
          backdrop-filter: blur(8px);
          transition: all 0.3s ease;
          cursor: default;
          perspective: 800px;
          transform-style: preserve-3d;
        }

        .marquee-item:hover {
          border-color: color-mix(in srgb, var(--color-accent) 40%, transparent) !important;
          background: rgba(29,78,216,0.05);
          transform: translateY(-2px);
          box-shadow: 0 4px 20px rgba(29,78,216,0.15);
        }

        .icon-badge {
          width: 52px;
          height: 52px;
          border-radius: 14px;
          background: linear-gradient(135deg, rgba(96, 165, 250, 0.08) 0%, rgba(96, 165, 250, 0.03) 100%);
          border: 1px solid rgba(96, 165, 250, 0.22);
          display: flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
          box-shadow: 0 0 16px rgba(96, 165, 250, 0.06), inset 0 1px 0 rgba(96, 165, 250, 0.12);
          transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
          transform-style: preserve-3d;
          transform: translateZ(0) scale(1);
        }

        .icon-badge svg {
          transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), filter 0.4s ease;
          transform: translateZ(0) scale(1);
        }

        .marquee-item:hover .icon-badge {
          background: linear-gradient(135deg, rgba(96, 165, 250, 0.14) 0%, rgba(96, 165, 250, 0.06) 100%);
          border-color: color-mix(in srgb, var(--color-accent) 45%, transparent) !important;
          box-shadow: 0 0 28px rgba(96, 165, 250, 0.18), inset 0 1px 0 rgba(96, 165, 250, 0.2);
          transform: translateZ(25px) scale(1.15);
        }

        .marquee-item:hover .icon-badge svg {
          transform: translateZ(40px) scale(1.12);
          filter: drop-shadow(0 6px 12px rgba(96, 165, 250, 0.25));
        }
      </style>

      <div class="relative w-full overflow-hidden py-4 flex select-none">
        <!-- Edge masks for premium fade effect -->
        <div
          class="absolute left-0 top-0 bottom-0 w-24 z-10 pointer-events-none"
          style="background: linear-gradient(to right, var(--color-bg), transparent);">
        </div>
        <div
          class="absolute right-0 top-0 bottom-0 w-24 z-10 pointer-events-none"
          style="background: linear-gradient(to left, var(--color-bg), transparent);">
        </div>

        <div class="marquee-track py-2">
          <!-- Marquee Group A -->
          <div class="marquee-group">
            <?php renderMarqueeGroup($expertiseItems); ?>
          </div>
          <!-- Marquee Group B (Duplicated for seamless infinite looping) -->
          <div class="marquee-group">
            <?php renderMarqueeGroup($expertiseItems); ?>
          </div>
        </div>
        </div>
      </div>
    </section>

    <!-- THE OPPORTUNITY   -->
    <section id="opportunity" class="relative py-10 lg:py-30 overflow-hidden w-full select-none">
      <!-- Plexus Data Network Canvas Background -->
      <canvas id="opportunity-network-canvas"
        class="absolute inset-0 w-full h-full pointer-events-none opacity-40 z-0"></canvas>

      <div class="mx-auto max-w-[90%] pb-10 lg:pb-30 relative z-10">
        <div class=" md:px-16 mb-12">
          <h2 class="text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">THE OPPORTUNITY</h2>
          <p class="text-3xl md:text-5xl lg:leading-14 font-serif font-bold text-white"><?= h($oppTitle) ?> </p>
        </div>
        <div class="flex flex-wrap lg:flex-nowrap items-stretch w-full gap-8 md:gap-10  md:px-16">

          <!-- Left Column: The Friction / The Challenge -->
          <div
            class="opp-card w-full lg:w-[calc(50%-1.25rem)] flex flex-col gap-6 p-8 md:p-10 rounded-3xl bg-white/[0.01] border border-white/5 backdrop-blur-md relative overflow-hidden group hover:border-white/10 transition-all duration-500">
            <!-- Left gold background glow -->
            <div
              class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-[#1d4ed8]/5 blur-3xl pointer-events-none group-hover:bg-[#1d4ed8]/8 transition-all duration-500">
            </div>

            <div class="flex items-center gap-2 z-10">
              <span class="w-2 h-2 lg:w-1.5 lg:h-1.5 rounded-full bg-red-400 animate-pulse"></span>
              <span class="text-[#1d4ed8] text-[3.2vw] md:text-xs font-bold tracking-[0.2em] uppercase font-inter">The Friction</span>
            </div>

            <h3 class="text-[5vw] md:text-xl lg:text-2xl font-bold font-sans text-white/90 leading-snug z-10">
              <?= $oppFrictionTitle ?>
            </h3>

            <div class="flex flex-col gap-4 text-gray-400 font-light text-[3.8vw] md:text-sm lg:text-base leading-relaxed z-10">
              <p><?= h($oppFrictionText) ?></p>
              <div class="pl-4 border-l-2 border-[#1d4ed8]/50 py-1 my-2">
                <p class="font-serif italic text-white/90 text-[4vw] md:text-base lg:text-lg">"<?= h($oppQuote) ?>"</p>
              </div>
            </div>
          </div>

          <!-- Right Column: The CDO Solution -->
          <div
            class="opp-card w-full lg:w-[calc(50%-1.25rem)] flex flex-col gap-6 p-8 md:p-10 rounded-3xl bg-gradient-to-br from-white/[0.03] to-white/[0.01] border border-[#1d4ed8]/25 backdrop-blur-md relative overflow-hidden group hover:border-[#1d4ed8]/45 transition-all duration-500 shadow-[0_0_30px_rgba(29,78,216,0.02)] hover:shadow-[0_0_35px_rgba(29,78,216,0.06)]">
            <!-- Top-right gold glow -->
            <div
              class="absolute -bottom-10 -right-10 w-40 h-40 rounded-full bg-[#1d4ed8]/8 blur-3xl pointer-events-none group-hover:bg-[#1d4ed8]/12 transition-all duration-500">
            </div>

            <div class="flex items-center gap-2 z-10">
              <span class="w-1.5 h-1.5 rounded-full bg-[#1d4ed8] animate-pulse"></span>
              <span class="text-[#1d4ed8] text-[3.2vw] md:text-xs font-bold tracking-[0.2em] uppercase font-inter">The CDO Advantage</span>
            </div>

            <h3 class="text-[5vw] md:text-xl lg:text-2xl font-bold font-sans text-white leading-snug z-10">
              <?= $oppSolutionTitle ?>
            </h3>

            <p class="text-gray-300 font-light text-[3.8vw] md:text-sm lg:text-base leading-relaxed z-10">
              <?= h($oppSolutionText) ?>
            </p>

            <!-- Key pillars of Fractional CDO -->
            <div class="flex flex-wrap gap-2.5 mt-2 z-10">
              <?php foreach ($oppPillars as $pillar): ?>
              <span
                class="px-3.5 py-1.5 rounded-full text-xs font-medium bg-[#1d4ed8]/8 border border-[#1d4ed8]/25 text-[#3b82f6] backdrop-blur-sm shadow-sm transition-all duration-300 hover:bg-[#1d4ed8]/15 hover:border-[#1d4ed8]/50 cursor-default">
                <?= h($pillar) ?>
              </span>
              <?php endforeach; ?>
            </div>
          </div>

        </div>

        <!-- Bottom Quote Sign-off -->
        <div
          class=" md:mt-20 pt-12 border-t border-white/5 flex flex-col items-center text-center relative max-w-4xl mx-auto px-6">
          <span class="text-[#1d4ed8]/25 text-5xl font-serif leading-none mb-2">“</span>
          <p class="text-[5.5vw] md:text-2xl lg:text-3xl font-serif font-semibold text-white/95 leading-relaxed">
            <?= h($oppBottomQuote) ?>
          </p>
          <div class="w-10 h-[1.5px] bg-[#1d4ed8]/30 mt-6"></div>
        </div>

      </div>
    </section>

    <!-- About section  -->
    <section id="about">
      <div class="pb-10 lg:pb-30 mx-auto max-w-[90%]">
        <div class=" md:px-16">
          <h2 class="text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">ABOUT
          </h2>
          <p class="text-3xl md:text-5xl lg:leading-14 font-serif font-bold text-white">Meet<br /><span class="text-white text-4xl lg:text-6xl"><?= h($heroName) ?></span>
          </p>
        </div>
        <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center gap-10  md:px-16 mt-6 lg:mt-12">
          <!-- Left Column: Biography Content (First 50%) -->
            <div
              class="w-full lg:w-[calc(50%-1.25rem)] flex flex-col gap-6 text-gray-300 font-light text-base leading-relaxed">
              <p class="text-lg text-white font-medium">
                <?= nl2br(h($aboutBio)) ?>
              </p>
              <div class="mt-2 lg:mt-4 p-5 rounded-2xl bg-[#1d4ed8]/5 border border-[#1d4ed8]/15 relative overflow-hidden group">
                <div
                  class="absolute left-0 top-0 bottom-0 w-[3px] bg-[#1d4ed8]/60 group-hover:bg-[#3b82f6] transition-colors duration-300">
                </div>
                <p class="font-serif italic text-white/90 text-lg leading-relaxed relative z-10">
                  "<?= h($aboutQuote) ?>"
                </p>
              </div>
              
              <div class="flex flex-wrap gap-4 mt-4 lg:mt-8 w-full">
                <?php foreach ($aboutStats as $stat): ?>
                <div class="flex-grow flex-shrink-0 w-[calc(50%-0.5rem)] min-w-[130px] p-6 rounded-2xl bg-[#0a0e1c]/40 backdrop-blur-md border border-[#1d4ed8]/15 hover:border-[#60a5fa]/40 hover:-translate-y-1 transition-all duration-300 text-center shadow-[0_4px_20px_rgba(0,0,0,0.3)] relative overflow-hidden group">
                  <!-- Glow Effect inside card -->
                  <div class="absolute -top-10 -right-10 w-20 h-20 bg-[#1d4ed8]/10 rounded-full blur-xl group-hover:bg-[#3b82f6]/20 transition-all duration-500"></div>
                  <!-- Stat Value -->
                  <div class="text-4xl font-extrabold text-white font-serif tracking-tight mb-1 bg-clip-text text-transparent bg-gradient-to-r from-white via-white to-white/70 group-hover:scale-105 transition-transform duration-300">
                    <?= h($stat['value']) ?>
                  </div>
                  <!-- Stat Label -->
                  <div class="text-[10px] md:text-xs font-bold font-sans uppercase tracking-widest text-[#3b82f6] group-hover:text-[#60a5fa] transition-colors duration-300">
                    <?= h($stat['label']) ?>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

          <div class="w-full lg:w-[calc(50%-1.25rem)] relative min-h-[400px] overflow-hidden rounded-3xl">
            <div
              class="absolute z-10 h-[25rem] w-[31.375rem] lg:w-[34.375rem] xl:w-[45.625rem] rotate-[-12deg] bottom-[3.4rem] -right-[13rem] rounded-[0.6rem]"
              style="background: linear-gradient(90deg, #1d4ed8 0%, #1d4ed8 35%, rgba(0, 0, 0, 0.98) 60%, rgba(0, 0, 0, 0.98) 100%);">
            </div>
            <div class="relative  lg:w-110 z-20 lg:-right-7">
              <img src="<?= h($aboutImage) ?>" class="w-full object-contain h-full" alt="">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CAPABILITIES & EXPERIENCE SECTION -->
    <section id="capabilities" class="relative pb-10 lg:pb-30 pt-10 overflow-hidden w-full select-none">
      <div class="mx-auto max-w-[90%] relative z-10">

        <div class=" md:px-16 mb-20">
          <h2 class="text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">Capabilities</h2>
          <p class="text-3xl md:text-5xl font-serif font-bold text-white">Professional Footprint</p>
        </div>

        <div class="relative w-full  flex flex-col lg:flex-row gap-12 lg:gap-16 items-center">

          <!-- Left side: Timeline Track + Content Column -->
          <div class="flex flex-row gap-8 md:gap-12 flex-grow w-full lg:max-w-[55%]">
            <!-- SVG Line Column (Vertical track) -->
            <div id="timeline-track-container" class="relative flex justify-center w-12 flex-shrink-0">
              <!-- Background track line -->
              <div class="absolute top-0 bottom-0 w-[2px] bg-white/5"></div>
              <!-- Drawing line (controlled by GSAP) -->
              <div id="timeline-scroll-line"
                class="absolute top-0 w-[2px] bg-gradient-to-b from-[#3b82f6] to-[#1d4ed8] origin-top h-0"></div>

              <!-- Dots aligned on the line -->
              <div class="absolute top-0 bottom-0 w-full flex flex-col justify-between items-center py-6 h-[600px]"
                id="timeline-dots-container">
                <!-- Dot 1 -->
                <div
                  class="timeline-dot w-4.5 h-4.5 rounded-full bg-[#121216] border-2 border-white/10 flex items-center justify-center transition-all duration-300 z-10"
                  data-index="0">
                  <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-300 inner-dot"></div>
                </div>
                <!-- Dot 2 -->
                <div
                  class="timeline-dot w-4.5 h-4.5 rounded-full bg-[#121216] border-2 border-white/10 flex items-center justify-center transition-all duration-300 z-10"
                  data-index="1">
                  <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-300 inner-dot"></div>
                </div>
                <!-- Dot 3 -->
                <div
                  class="timeline-dot w-4.5 h-4.5 rounded-full bg-[#121216] border-2 border-white/10 flex items-center justify-center transition-all duration-300 z-10"
                  data-index="2">
                  <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-300 inner-dot"></div>
                </div>
                <!-- Dot 4 -->
                <div
                  class="timeline-dot w-4.5 h-4.5 rounded-full bg-[#121216] border-2 border-white/10 flex items-center justify-center transition-all duration-300 z-10"
                  data-index="3">
                  <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-300 inner-dot"></div>
                </div>
                <!-- Dot 5 -->
                <div
                  class="timeline-dot w-4.5 h-4.5 rounded-full bg-[#121216] border-2 border-white/10 flex items-center justify-center transition-all duration-300 z-10"
                  data-index="4">
                  <div class="w-1.5 h-1.5 rounded-full bg-white/20 transition-all duration-300 inner-dot"></div>
                </div>
              </div>
            </div>

            <!-- Content Column -->
            <div class="flex flex-col justify-between gap-12 py-2 lg:h-[600px] flex-grow" id="timeline-content-container">
              <?php foreach ($capabilities['items'] as $idx => $ci): ?>
              <div class="timeline-item opacity-25 translate-x-4 transition-all duration-500" data-index="<?= $idx ?>">
                <h3 class="text-[#3b82f6] font-sans font-bold text-sm uppercase tracking-wider mb-2"><?= h($ci['category']) ?></h3>
                <?php if (isset($ci['type']) && $ci['type'] === 'pills'): ?>
                  <div class="flex flex-wrap gap-2.5 mt-1">
                    <?php foreach ($ci['items'] as $pill): ?>
                    <span class="px-4 py-1.5 rounded-full text-xs md:text-sm bg-white/5 border border-white/10 text-white font-medium"><?= h($pill) ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <p class="text-white text-lg md:text-2xl font-serif font-medium leading-snug"><?= h($ci['description'] ?? $ci['text']) ?></p>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Right side: Visual Column (Sticky/pinned on desktop) -->
          <div id="timeline-visual-container"
            class="w-full lg:w-[25rem] xl:w-[28.125rem] h-[23.75rem] lg:h-[31.25rem] flex-shrink-0 relative overflow-hidden rounded-3xl border border-white/5 bg-[#0d0d12]/45 backdrop-blur-md p-6 flex items-center justify-center shadow-[0_1.25rem_3.125rem_rgba(0,0,0,0.5)]">
            <!-- Grid Background inside the visual container for tech aesthetic -->
            <div
              class="absolute inset-0 bg-[radial-gradient(#1d4ed8_0.0625rem,transparent_0.0625rem)] [background-size:1rem_1rem] opacity-15 pointer-events-none">
            </div>
            <!-- Glow background overlays -->
            <div
              class="absolute -top-12 -left-12 w-48 h-48 rounded-full bg-[#1d4ed8]/10 blur-[3.75rem] pointer-events-none">
            </div>
            <div
              class="absolute -bottom-12 -right-12 w-48 h-48 rounded-full bg-[#3b82f6]/5 blur-[3.75rem] pointer-events-none">
            </div>

            <!-- SVG 1: Experience (Gauge/Radar Pointer) -->
            <div class="timeline-visual active" data-index="0">
              <svg class="w-[85%] h-[85%] overflow-visible" viewBox="0 0 200 200" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <!-- Circular shadow ring -->
                <circle cx="100" cy="100" r="85" stroke="#ffffff" stroke-opacity="0.03" stroke-width="8" />
                <circle cx="100" cy="100" r="75" stroke="url(#experience-grad-1)" stroke-opacity="0.1" stroke-width="2"
                  stroke-dasharray="4 6" class="animate-spin-slow" />
                <circle cx="100" cy="100" r="65" stroke="url(#experience-grad-2)" stroke-opacity="0.2"
                  stroke-width="1" />

                <!-- Dial ticks -->
                <g class="opacity-40">
                  <line x1="100" y1="20" x2="100" y2="28" stroke="#3b82f6" stroke-width="2" />
                  <line x1="100" y1="172" x2="100" y2="180" stroke="#3b82f6" stroke-width="2" />
                  <line x1="20" y1="100" x2="28" y2="100" stroke="#3b82f6" stroke-width="2" />
                  <line x1="172" y1="100" x2="180" y2="100" stroke="#3b82f6" stroke-width="2" />
                  <line x1="43.4" y1="43.4" x2="49.1" y2="49.1" stroke="#3b82f6" stroke-width="1.5" />
                  <line x1="156.6" y1="43.4" x2="150.9" y2="49.1" stroke="#3b82f6" stroke-width="1.5" />
                  <line x1="43.4" y1="156.6" x2="49.1" y2="150.9" stroke="#3b82f6" stroke-width="1.5" />
                  <line x1="156.6" y1="156.6" x2="150.9" y2="150.9" stroke="#3b82f6" stroke-width="1.5" />
                </g>

                <!-- Gauge Progress Arc -->
                <path d="M 43.4 156.6 A 80 80 0 1 1 156.6 156.6" stroke="url(#experience-grad-active)" stroke-width="4"
                  stroke-linecap="round" stroke-dasharray="300" stroke-dashoffset="80" />

                <!-- Rotating Pointer Needle -->
                <g class="animate-spin-slow" style="transform-origin: 100px 100px; animation-duration: 8s;">
                  <line x1="100" y1="100" x2="100" y2="35" stroke="url(#needle-grad)" stroke-width="3"
                    stroke-linecap="round" />
                  <polygon points="100,28 96,38 104,38" fill="#3b82f6" />
                  <circle cx="100" cy="100" r="8" fill="#121216" stroke="#3b82f6" stroke-width="2" />
                  <circle cx="100" cy="100" r="3" fill="#3b82f6" />
                </g>

                <!-- Center Text Readout -->
                <text x="100" y="125" text-anchor="middle" fill="#ffffff" font-family="Montserrat" font-size="24"
                  font-weight="800">14+</text>
                <text x="100" y="142" text-anchor="middle" fill="#1d4ed8" font-family="Inter" font-size="10"
                  font-weight="600" letter-spacing="1.5">YEARS XP</text>

                <!-- Gradients definitions -->
                <defs>
                  <linearGradient id="experience-grad-1" x1="0" y1="0" x2="200" y2="200" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#1d4ed8" />
                    <stop offset="100%" stop-color="#3b82f6" />
                  </linearGradient>
                  <linearGradient id="experience-grad-2" x1="200" y1="0" x2="0" y2="200" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.8" />
                    <stop offset="100%" stop-color="#000000" stop-opacity="0" />
                  </linearGradient>
                  <linearGradient id="experience-grad-active" x1="0" y1="200" x2="200" y2="0"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#1d4ed8" />
                    <stop offset="60%" stop-color="#3b82f6" />
                    <stop offset="100%" stop-color="#ffffff" />
                  </linearGradient>
                  <linearGradient id="needle-grad" x1="100" y1="100" x2="100" y2="35" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#1d4ed8" />
                    <stop offset="100%" stop-color="#ffffff" />
                  </linearGradient>
                </defs>
              </svg>
            </div>

            <!-- SVG 2: Industries Served (Network Mesh/Prism) -->
            <div class="timeline-visual" data-index="1">
              <svg class="w-[85%] h-[85%] overflow-visible animate-globe-float" viewBox="0 0 200 200" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <!-- Inner rotating node cluster -->
                <g class="animate-spin-slow" style="transform-origin: 100px 100px; animation-duration: 25s;">
                  <!-- Outer bounding circle -->
                  <circle cx="100" cy="100" r="70" stroke="url(#ind-grad-1)" stroke-opacity="0.1"
                    stroke-dasharray="3 7" />

                  <!-- Connective Edges / Web -->
                  <!-- Core Pentagon structure -->
                  <polygon points="100,40 160,80 140,150 60,150 40,80" stroke="#1d4ed8" stroke-opacity="0.3"
                    stroke-width="1" />
                  <polygon points="100,55 145,85 130,135 70,135 55,85" stroke="#3b82f6" stroke-opacity="0.25"
                    stroke-width="1.5" />

                  <!-- Cross linkages -->
                  <line x1="100" y1="40" x2="140" y2="150" stroke="#1d4ed8" stroke-opacity="0.2" stroke-width="1"
                    class="animate-dash" />
                  <line x1="160" y1="80" x2="60" y2="150" stroke="#1d4ed8" stroke-opacity="0.2" stroke-width="1"
                    class="animate-dash" />
                  <line x1="40" y1="80" x2="140" y2="150" stroke="#1d4ed8" stroke-opacity="0.2" stroke-width="1"
                    class="animate-dash" />
                  <line x1="100" y1="40" x2="60" y2="150" stroke="#1d4ed8" stroke-opacity="0.2" stroke-width="1" />
                  <line x1="40" y1="80" x2="160" y2="80" stroke="#1d4ed8" stroke-opacity="0.15" stroke-width="1" />

                  <!-- Nodes (Vertices) -->
                  <circle cx="100" cy="40" r="6" fill="#3b82f6" stroke="#121216" stroke-width="2"
                    class="animate-pulse-glow" />
                  <circle cx="160" cy="80" r="6" fill="#1d4ed8" stroke="#121216" stroke-width="2" />
                  <circle cx="140" cy="150" r="6" fill="#3b82f6" stroke="#121216" stroke-width="2" />
                  <circle cx="60" cy="150" r="6" fill="#1d4ed8" stroke="#121216" stroke-width="2" />
                  <circle cx="40" cy="80" r="6" fill="#3b82f6" stroke="#121216" stroke-width="2" />

                  <circle cx="100" cy="55" r="4" fill="#ffffff" />
                  <circle cx="145" cy="85" r="4" fill="#ffffff" />
                  <circle cx="130" cy="135" r="4" fill="#ffffff" />
                  <circle cx="70" cy="135" r="4" fill="#ffffff" />
                  <circle cx="55" cy="85" r="4" fill="#ffffff" />

                  <!-- Center Node -->
                  <circle cx="100" cy="100" r="10" fill="url(#ind-grad-2)" stroke="#60a5fa" stroke-opacity="0.5"
                    stroke-width="1.5" class="animate-pulse-core" />
                </g>

                <defs>
                  <linearGradient id="ind-grad-1" x1="0" y1="0" x2="200" y2="200" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#60a5fa" />
                    <stop offset="100%" stop-color="#1d4ed8" />
                  </linearGradient>
                  <linearGradient id="ind-grad-2" x1="90" y1="90" x2="110" y2="110" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#ffffff" />
                    <stop offset="100%" stop-color="#1d4ed8" />
                  </linearGradient>
                </defs>
              </svg>
            </div>

            <!-- SVG 3: Global Footprint (High-tech Globe) -->
            <div class="timeline-visual" data-index="2">
              <svg class="w-[85%] h-[85%] overflow-visible animate-globe-float" viewBox="0 0 200 200" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <!-- Base Grid Ring -->
                <circle cx="100" cy="100" r="75" stroke="#ffffff" stroke-opacity="0.04" stroke-width="1.5" />
                <circle cx="100" cy="100" r="65" stroke="#1d4ed8" stroke-opacity="0.1" stroke-width="1" />

                <!-- Globe meridians (ellipses to simulate 3D spheres) -->
                <ellipse cx="100" cy="100" rx="75" ry="30" stroke="#1d4ed8" stroke-opacity="0.15" stroke-width="1"
                  class="animate-spin-slow" style="animation-duration: 24s;" />
                <ellipse cx="100" cy="100" rx="30" ry="75" stroke="#1d4ed8" stroke-opacity="0.15" stroke-width="1"
                  class="animate-spin-slow" style="animation-duration: 24s;" />
                <ellipse cx="100" cy="100" rx="75" ry="50" stroke="#3b82f6" stroke-opacity="0.08" stroke-width="1" />

                <!-- Rotating Dotted World Grid Overlay -->
                <g class="animate-spin-slow" style="transform-origin: 100px 100px; animation-duration: 35s;">
                  <!-- Mocking world dot mesh clusters -->
                  <!-- NA/EU/Asia/ME markers -->
                  <circle cx="60" cy="80" r="2.5" fill="#3b82f6" opacity="0.75" />
                  <circle cx="65" cy="78" r="1.5" fill="#3b82f6" opacity="0.6" />
                  <circle cx="70" cy="82" r="2" fill="#3b82f6" opacity="0.6" />

                  <circle cx="100" cy="70" r="3" fill="#3b82f6" opacity="0.9" class="animate-pulse-glow" />
                  <circle cx="95" cy="68" r="2" fill="#3b82f6" opacity="0.6" />
                  <circle cx="105" cy="72" r="1.5" fill="#3b82f6" opacity="0.6" />

                  <circle cx="135" cy="85" r="3" fill="#3b82f6" opacity="0.9" />
                  <circle cx="140" cy="88" r="2" fill="#3b82f6" opacity="0.6" />
                  <circle cx="130" cy="82" r="1.5" fill="#3b82f6" opacity="0.6" />

                  <circle cx="125" cy="95" r="3.5" fill="#ffffff" class="animate-pulse-glow" />
                  <circle cx="120" cy="98" r="2" fill="#3b82f6" opacity="0.7" />
                  <circle cx="130" cy="92" r="1.5" fill="#3b82f6" opacity="0.5" />
                </g>

                <!-- Curved Connection Arcs (Data highways) -->
                <!-- Arc from India to Germany -->
                <path d="M 125 95 Q 112 60 100 70" stroke="url(#arc-grad-1)" stroke-width="1.5" stroke-linecap="round"
                  class="animate-dash" />
                <!-- Arc from India to UAE -->
                <path d="M 125 95 Q 120 115 135 85" stroke="url(#arc-grad-2)" stroke-width="1.5" stroke-linecap="round"
                  class="animate-dash" />
                <!-- Arc from Germany to Canada -->
                <path d="M 100 70 Q 75 55 60 80" stroke="url(#arc-grad-1)" stroke-width="1.5" stroke-linecap="round"
                  class="animate-dash-slow" />
                <!-- Arc from India to Netherlands -->
                <path d="M 125 95 Q 100 50 105 72" stroke="url(#arc-grad-2)" stroke-width="1" stroke-linecap="round" />

                <!-- Location radar rings on major footprint hubs -->
                <circle cx="125" cy="95" r="6" stroke="#ffffff" stroke-opacity="0.5" stroke-width="1"
                  class="animate-pulse-core" />
                <circle cx="100" cy="70" r="5" stroke="#3b82f6" stroke-opacity="0.5" stroke-width="1"
                  class="animate-pulse-core" style="animation-delay: 0.8s;" />
                <circle cx="60" cy="80" r="5" stroke="#3b82f6" stroke-opacity="0.5" stroke-width="1"
                  class="animate-pulse-core" style="animation-delay: 1.5s;" />

                <defs>
                  <linearGradient id="arc-grad-1" x1="125" y1="95" x2="60" y2="80" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#3b82f6" />
                    <stop offset="50%" stop-color="#ffffff" />
                    <stop offset="100%" stop-color="#1d4ed8" />
                  </linearGradient>
                  <linearGradient id="arc-grad-2" x1="125" y1="95" x2="135" y2="85" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#60a5fa" />
                    <stop offset="100%" stop-color="#1d4ed8" />
                  </linearGradient>
                </defs>
              </svg>
            </div>

            <!-- SVG 4: Engagement Model (Isometric Stacked Slabs) -->
            <div class="timeline-visual" data-index="3">
              <svg class="w-[85%] h-[85%] overflow-visible" viewBox="0 0 200 200" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <!-- Drop shadow projection on bottom -->
                <ellipse cx="100" cy="175" rx="55" ry="12" fill="url(#slab-shadow)" opacity="0.5" />

                <!-- Isometric Stack Layer 3: Bottom Slab (Project-Based Consulting) -->
                <g class="animate-float-slab-3" style="transform-origin: 100px 140px;">
                  <!-- Isometric rhombus (width: 120, height: 60) centered -->
                  <polygon points="100,110 160,140 100,170 40,140" fill="url(#slab-grad-bottom)"
                    stroke="url(#slab-border-blue)" stroke-width="1.5" />
                  <!-- Side extrusions for 3D slab thickness -->
                  <polygon points="40,140 100,170 100,174 40,144" fill="#0f172a" />
                  <polygon points="160,140 100,170 100,174 160,144" fill="#1e293b" />
                  <text x="100" y="145" text-anchor="middle" fill="#ffffff" font-family="Montserrat" font-size="8"
                    font-weight="700" letter-spacing="0.5">PROJECT-BASED</text>
                </g>

                <!-- Isometric Stack Layer 2: Middle Slab (Advisory Retainer) -->
                <g class="animate-float-slab-2" style="transform-origin: 100px 100px;">
                  <polygon points="100,70 160,100 100,130 40,100" fill="url(#slab-grad-mid)"
                    stroke="url(#slab-border-blue)" stroke-width="1.5" />
                  <polygon points="40,100 100,130 100,134 40,104" fill="#1e293b" />
                  <polygon points="160,100 100,130 100,134 160,104" fill="#334155" />
                  <text x="100" y="105" text-anchor="middle" fill="#3b82f6" font-family="Montserrat" font-size="8"
                    font-weight="700" letter-spacing="0.5">ADVISORY RETAINER</text>
                </g>

                <!-- Isometric Stack Layer 1: Top Slab (Fractional CDO) -->
                <g class="animate-float-slab-1" style="transform-origin: 100px 60px;">
                  <polygon points="100,30 160,60 100,90 40,60" fill="url(#slab-grad-top)"
                    stroke="url(#slab-border-white)" stroke-width="2" />
                  <polygon points="40,60 100,90 100,94 40,64" fill="#1d4ed8" />
                  <polygon points="160,60 100,90 100,94 160,64" fill="#3b82f6" />
                  <text x="100" y="65" text-anchor="middle" fill="#ffffff" font-family="Montserrat" font-size="9"
                    font-weight="800" letter-spacing="0.5" class="animate-pulse-glow">FRACTIONAL CDO</text>
                  <!-- Glowing core indicator on top slab -->
                  <circle cx="100" cy="50" r="4" fill="#60a5fa" class="animate-pulse-core" />
                </g>

                <defs>
                  <radialGradient id="slab-shadow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="#1d4ed8" stop-opacity="0.6" />
                    <stop offset="100%" stop-color="#000000" stop-opacity="0" />
                  </radialGradient>
                  <linearGradient id="slab-grad-top" x1="40" y1="60" x2="160" y2="60" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="rgba(59,130,246,0.45)" />
                    <stop offset="100%" stop-color="rgba(29,78,216,0.15)" />
                  </linearGradient>
                  <linearGradient id="slab-grad-mid" x1="40" y1="100" x2="160" y2="100" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="rgba(29, 78, 216, 0.4)" />
                    <stop offset="100%" stop-color="rgba(20, 20, 25, 0.7)" />
                  </linearGradient>
                  <linearGradient id="slab-grad-bottom" x1="40" y1="140" x2="160" y2="140"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="rgba(12, 12, 16, 0.9)" />
                    <stop offset="100%" stop-color="rgba(29, 78, 216, 0.25)" />
                  </linearGradient>
                  <linearGradient id="slab-border-blue" x1="40" y1="140" x2="160" y2="140"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#1d4ed8" stop-opacity="0.6" />
                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.2" />
                  </linearGradient>
                  <linearGradient id="slab-border-white" x1="40" y1="60" x2="160" y2="60"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#ffffff" stop-opacity="0.8" />
                    <stop offset="50%" stop-color="#3b82f6" stop-opacity="0.5" />
                    <stop offset="100%" stop-color="#1d4ed8" stop-opacity="0.2" />
                  </linearGradient>
                </defs>
              </svg>
            </div>

            <!-- SVG 5: Core Disciplines (Pulsing CPU Core) -->
            <div class="timeline-visual" data-index="4">
              <svg class="w-[85%] h-[85%] overflow-visible" viewBox="0 0 200 200" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <!-- Concentric Outer Tech Rings -->
                <circle cx="100" cy="100" r="82" stroke="#1d4ed8" stroke-opacity="0.1" stroke-width="1.5" />
                <circle cx="100" cy="100" r="72" stroke="#3b82f6" stroke-opacity="0.15" stroke-width="2"
                  stroke-dasharray="12 8 4 8" class="animate-spin-slow" />
                <circle cx="100" cy="100" r="62" stroke="#1d4ed8" stroke-opacity="0.25" stroke-width="1.5"
                  stroke-dasharray="2 4" class="animate-spin-counter" />

                <!-- Circuit Connection Lines running from core -->
                <g stroke="#1d4ed8" stroke-opacity="0.4" stroke-width="1.5" fill="none">
                  <!-- North line -->
                  <path d="M 100 58 L 100 35 M 100 35 L 85 20" />
                  <!-- East line -->
                  <path d="M 142 100 L 165 100 M 165 100 L 180 85" class="animate-dash" />
                  <!-- South line -->
                  <path d="M 100 142 L 100 165 M 100 165 L 115 180" />
                  <!-- West line -->
                  <path d="M 58 100 L 35 100 M 35 100 L 20 115" class="animate-dash" />

                  <!-- Diagonal lines -->
                  <path d="M 70 70 L 48 48" />
                  <path d="M 130 70 L 152 48" />
                  <path d="M 130 130 L 152 152" />
                  <path d="M 70 130 L 48 152" />
                </g>

                <!-- Terminating circuit node circles -->
                <circle cx="85" cy="20" r="3" fill="#3b82f6" />
                <circle cx="180" cy="85" r="3.5" fill="#ffffff" class="animate-pulse-glow" />
                <circle cx="115" cy="180" r="3" fill="#3b82f6" />
                <circle cx="20" cy="115" r="3.5" fill="#ffffff" class="animate-pulse-glow" />

                <circle cx="48" cy="48" r="3" fill="#1d4ed8" />
                <circle cx="152" cy="48" r="3" fill="#3b82f6" />
                <circle cx="152" cy="152" r="3" fill="#1d4ed8" />
                <circle cx="48" cy="152" r="3" fill="#3b82f6" />

                <!-- Central CPU Chip Body -->
                <!-- Shadow backdrop -->
                <rect x="62" y="62" width="76" height="76" rx="10" fill="#08080c" stroke="url(#cpu-border)"
                  stroke-width="1.5" class="animate-pulse-glow" />
                <rect x="70" y="70" width="60" height="60" rx="8" fill="url(#cpu-body)" stroke="#1d4ed8"
                  stroke-opacity="0.3" stroke-width="1" />

                <!-- CPU Core (Central glow crystal) -->
                <circle cx="100" cy="100" r="14" fill="url(#cpu-core-glow)" class="animate-pulse-core" />
                <rect x="94" y="94" width="12" height="12" rx="2" fill="#ffffff" opacity="0.9" />

                <!-- Gold Pins / Contact Pads on CPU edge -->
                <g fill="#3b82f6" opacity="0.8">
                  <!-- Top pins -->
                  <rect x="76" y="65" width="4" height="2" />
                  <rect x="86" y="65" width="4" height="2" />
                  <rect x="96" y="65" width="4" height="2" />
                  <rect x="106" y="65" width="4" height="2" />
                  <rect x="116" y="65" width="4" height="2" />
                  <!-- Bottom pins -->
                  <rect x="76" y="133" width="4" height="2" />
                  <rect x="86" y="133" width="4" height="2" />
                  <rect x="96" y="133" width="4" height="2" />
                  <rect x="106" y="133" width="4" height="2" />
                  <rect x="116" y="133" width="4" height="2" />
                  <!-- Left pins -->
                  <rect x="65" y="76" width="2" height="4" />
                  <rect x="65" y="86" width="2" height="4" />
                  <rect x="65" y="96" width="2" height="4" />
                  <rect x="65" y="106" width="2" height="4" />
                  <rect x="65" y="116" width="2" height="4" />
                  <!-- Right pins -->
                  <rect x="133" y="76" width="2" height="4" />
                  <rect x="133" y="86" width="2" height="4" />
                  <rect x="133" y="96" width="2" height="4" />
                  <rect x="133" y="106" width="2" height="4" />
                  <rect x="133" y="116" width="2" height="4" />
                </g>

                <defs>
                  <linearGradient id="cpu-border" x1="62" y1="62" x2="138" y2="138" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#ffffff" />
                    <stop offset="50%" stop-color="#3b82f6" />
                    <stop offset="100%" stop-color="#1d4ed8" />
                  </linearGradient>
                  <linearGradient id="cpu-body" x1="70" y1="70" x2="130" y2="130" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#121216" />
                    <stop offset="100%" stop-color="#050508" />
                  </linearGradient>
                  <radialGradient id="cpu-core-glow" cx="50%" cy="50%" r="50%">
                    <stop offset="0%" stop-color="#ffffff" />
                    <stop offset="40%" stop-color="#3b82f6" />
                    <stop offset="100%" stop-color="#1d4ed8" stop-opacity="0" />
                  </radialGradient>
                </defs>
              </svg>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="relative pb-10 lg:pb-30 pt-10 overflow-hidden">
      <!-- Glow effects behind services -->
      <div
        class="absolute top-1/4 left-1/4 w-[31.25rem] h-[31.25rem] rounded-full bg-[#1d4ed8]/5 blur-[7.5rem] pointer-events-none">
      </div>
      <div
        class="absolute bottom-1/4 right-1/4 w-[31.25rem] h-[31.25rem] rounded-full bg-[#3b82f6]/5 blur-[7.5rem] pointer-events-none">
      </div>

      <div class="mx-auto max-w-[90%] relative z-10">

        <div class=" md:px-[4rem] mb-[2rem] lg:mb-[4rem]">
          <h2 class="text-xs md:text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-[0.5rem] font-inter">
            Services</h2>
          <p class="text-3xl md:text-5xl font-serif font-bold text-white leading-tight">How I Help Organizations Succeed
          </p>
        </div>

        <div class="flex flex-wrap justify-center gap-[1.5rem] lg:gap-[2rem]  md:px-[4rem] mt-[1.5rem] lg:mt-[4rem]">
          <?php
          $serviceSvgs = [
              0 => '<svg class="w-[1.75rem] h-[1.75rem]" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /><path d="M7 10l3-2 2 4 2-4 3 2v4H7v-4z" fill="#3b82f6" fill-opacity="0.2" /></svg>',
              1 => '<svg class="w-[1.75rem] h-[1.75rem]" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5"><circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="4" stroke-dasharray="2 2" /><path d="M12 2v20M2 12h22" stroke-opacity="0.4" /><path d="M12 12l4-4" stroke-linecap="round" /><circle cx="16" cy="8" r="1" fill="#3b82f6" /></svg>',
              2 => '<svg class="w-[1.75rem] h-[1.75rem]" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" /><path d="M9 3v18M15 3v18M3 9h18M3 15h18" /><circle cx="12" cy="12" r="2.5" fill="#3b82f6" /><path d="M12 6.5A5.5 5.5 0 0 0 6.5 12A5.5 5.5 0 0 0 12 17.5A5.5 5.5 0 0 0 17.5 12A5.5 5.5 0 0 0 12 6.5Z" stroke-dasharray="2 3" /></svg>',
              3 => '<svg class="w-[1.75rem] h-[1.75rem]" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5"><path d="M4 22V4c0-.5.5-1 1-1h14c.5 0 1 .5 1 1v18M4 7h16M4 12h16M4 17h16" /><path d="M8 3v19M16 3v19" stroke-opacity="0.3" /></svg>',
              4 => '<svg class="w-[1.75rem] h-[1.75rem]" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5"><path d="M3 3v18h18M18 9l-5 5-3-3-4 4" stroke-linecap="round" stroke-linejoin="round" /><circle cx="18" cy="9" r="2" fill="#3b82f6" /><circle cx="13" cy="14" r="1.5" fill="#3b82f6" /></svg>',
              5 => '<svg class="w-[1.75rem] h-[1.75rem]" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.5"><ellipse cx="12" cy="5" rx="9" ry="3" /><path d="M3 5v6c0 1.66 4 3 9 3s9-1.34 9-3V5M3 11v6c0 1.66 4 3 9 3s9-1.34 9-3v-6" /><path d="M12 2v6" stroke-dasharray="2 2" /></svg>'
          ];
          foreach ($serviceItems as $idx => $svc):
              $svg = $serviceSvgs[$idx % count($serviceSvgs)];
          ?>
          <div class="service-card w-full md:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1.333rem)]" id="service-card-<?= $idx + 1 ?>">
            <div class="w-[3.5rem] h-[3.5rem] rounded-2xl bg-[#1d4ed8]/10 flex items-center justify-center border border-[#1d4ed8]/30 mb-[1.5rem]">
              <?= $svg ?>
            </div>
            <h3 class="text-white text-xl font-bold font-sans mb-[0.25rem]"><?= h($svc['title']) ?></h3>
            <p class="text-white/60 text-sm leading-relaxed font-inter mb-[1.5rem]">
              <?= h($svc['description']) ?>
            </p>
            <?php if (!empty($svc['bullets'])): ?>
            <div class="h-[1px] bg-white/5 w-full mb-[1.5rem]"></div>
            <ul class="space-y-[0.875rem] mt-auto">
              <?php foreach ($svc['bullets'] as $bullet): ?>
              <li class="flex items-start gap-[0.75rem] text-sm text-white/80 font-inter">
                <svg class="w-[1rem] h-[1rem] text-[#3b82f6] mt-[0.125rem] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span><?= h($bullet) ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Ideal Clients Section -->
    <section id="ideal-clients" class="relative py-10 lg:py-32 overflow-hidden">
      <!-- Glow effects -->
      <div
        class="absolute -top-[10%] -left-[10%] w-96 h-96 rounded-full bg-[#1d4ed8]/5 blur-[120px] pointer-events-none">
      </div>
      <div
        class="absolute -bottom-[10%] -right-[10%] w-96 h-96 rounded-full bg-[#1d4ed8]/5 blur-[120px] pointer-events-none">
      </div>

      <div class="max-w-7xl mx-auto px-6 md:px-12 flex flex-col lg:flex-row gap-16 lg:gap-20">
        <!-- Left Column: Navigation Index -->
        <div class="w-full lg:w-[55%] flex flex-col justify-center">
          <h2 class="text-[3.2vw] md:text-xs lg:text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">Ideal Clients</h2>
          <h3 class="text-[5.5vw] md:text-[6vw] lg:text-5xl font-serif font-extrabold tracking-tight text-white mb-8 whitespace-nowrap">
            Who I Work With</h3>

          <!-- Interactive List -->
          <div class="client-nav flex flex-col gap-6" id="client-nav-list">
            <?php foreach ($clientTabs as $idx => $tab):
                $activeClass = ($idx === 0) ? 'active-item' : '';
            ?>
            <div class="client-nav-item group cursor-pointer py-4 border-b border-white/5 relative <?= $activeClass ?>"
              data-target="<?= h($tab['id']) ?>">
              <span class="font-serif text-xs text-[#1d4ed8]/50 mr-4"><?= h($tab['num'] ?? sprintf("%02d //", $idx+1)) ?></span>
              <span
                class="text-xl md:text-2xl font-bold font-sans text-white/50 group-hover:text-white transition-colors duration-300"><?= h($tab['title']) ?></span>
              <div class="nav-line absolute bottom-0 left-0 w-0 h-[2px] bg-[#3b82f6] transition-all duration-300"></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Right Column: Glassmorphic Visual Showcase -->
        <div class="w-full lg:w-[45%] relative flex items-center justify-center min-h-[420px]">
          <div
            class="w-full h-full glassmorphism rounded-3xl p-8 md:p-12 border border-[#1d4ed8]/15 flex flex-col justify-between min-h-[420px] relative overflow-hidden shadow-[0_0_30px_rgba(29,78,216,0.02)]">
            <!-- Background accent glow -->
            <div
              class="absolute -top-[10%] -right-[10%] w-48 h-48 rounded-full bg-[#1d4ed8]/5 blur-3xl pointer-events-none">
            </div>

            <!-- 0. Static Default Intro (Initially visible) -->
            <div id="panel-default"
              class="showcase-panel flex flex-col justify-center h-full gap-4 transition-all duration-500">
              <div class="mb-6 opacity-40">
                <svg class="w-[5rem] h-[5rem] text-[#3b82f6]" viewBox="0 0 100 100" fill="none" stroke="currentColor"
                  stroke-width="1">
                  <circle cx="50" cy="50" r="10" stroke-dasharray="2 2" />
                  <circle cx="50" cy="50" r="25" stroke-dasharray="3 3" />
                  <circle cx="20" cy="30" r="3" fill="#3b82f6" />
                  <circle cx="80" cy="30" r="3" fill="#3b82f6" />
                  <circle cx="50" cy="85" r="3" fill="#3b82f6" />
                  <line x1="20" y1="30" x2="50" y2="50" stroke-width="0.8" />
                  <line x1="80" y1="30" x2="50" y2="50" stroke-width="0.8" />
                  <line x1="50" y1="85" x2="50" y2="50" stroke-width="0.8" />
                </svg>
              </div>
              <p class="text-white/60 text-base md:text-lg leading-relaxed font-inter">
                <?= h($clientIntro) ?>
              </p>
            </div>

            <?php
            $panelSvgs = [
                'ceos-founders' => '<svg class="w-[5rem] h-[5rem] text-[#3b82f6]" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="60" cy="35" r="8" fill="#3b82f6" fill-opacity="0.1" stroke-width="1.5" /><line x1="60" y1="43" x2="60" y2="80" /><line x1="60" y1="55" x2="25" y2="55" /><line x1="60" y1="55" x2="95" y2="55" /><line x1="25" y1="55" x2="25" y2="80" /><line x1="95" y1="55" x2="95" y2="80" /><circle cx="25" cy="84" r="4" fill="#3b82f6" /><circle cx="60" cy="84" r="4" fill="#3b82f6" /><circle cx="95" cy="84" r="4" fill="#3b82f6" /><path d="M15 100 L 45 85 L 75 90 L 105 45" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" class="draw-path" /><circle cx="105" cy="45" r="3.5" fill="#60a5fa" class="pulse-node" /></svg>',
                'cios-ctos' => '<svg class="w-[5rem] h-[5rem] text-[#3b82f6]" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="20" y="25" width="22" height="14" rx="3" fill="#3b82f6" fill-opacity="0.1" /><rect x="20" y="53" width="22" height="14" rx="3" fill="#3b82f6" fill-opacity="0.1" /><rect x="20" y="81" width="22" height="14" rx="3" fill="#3b82f6" fill-opacity="0.1" /><path d="M42 32 L 75 32 L 75 60" class="draw-path" /><path d="M42 60 L 75 60" class="draw-path" /><path d="M42 88 L 75 88 L 75 60" class="draw-path" /><line x1="75" y1="60" x2="98" y2="60" stroke-width="1.5" /><circle cx="100" cy="60" r="6" fill="#60a5fa" class="pulse-node" /><circle cx="100" cy="60" r="12" stroke-dasharray="3 3" /></svg>',
                'erp-leaders' => '<svg class="w-[5rem] h-[5rem] text-[#3b82f6]" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.2"><ellipse cx="60" cy="30" rx="18" ry="6" fill="#3b82f6" fill-opacity="0.1" /><path d="M42 30 V 45 A 18 6 0 0 0 78 45 V 30" /><path d="M42 45 V 60 A 18 6 0 0 0 78 60 V 45" /><circle cx="20" cy="45" r="4" /><circle cx="100" cy="45" r="4" /><circle cx="60" cy="95" r="4" /><line x1="38" y1="45" x2="24" y2="45" stroke-dasharray="2 2" /><line x1="82" y1="45" x2="96" y2="45" stroke-dasharray="2 2" /><line x1="60" y1="68" x2="60" y2="91" stroke-dasharray="2 2" /></svg>',
                'pe-firms' => '<svg class="w-[5rem] h-[5rem] text-[#3b82f6]" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="15" y="25" width="90" height="70" rx="6" stroke-dasharray="4 2" /><line x1="30" y1="45" x2="70" y2="45" stroke-width="2" class="draw-path" /><line x1="30" y1="60" x2="85" y2="60" stroke-width="2" class="draw-path" /><line x1="30" y1="75" x2="60" y2="75" stroke-width="2" stroke="#60a5fa" class="draw-path" /><circle cx="70" cy="45" r="3" fill="#3b82f6" /><circle cx="85" cy="60" r="3" fill="#3b82f6" /><circle cx="60" cy="75" r="3" fill="#60a5fa" class="pulse-node" /></svg>',
                'enterprises' => '<svg class="w-[5rem] h-[5rem] text-[#3b82f6]" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="60" cy="60" r="35" stroke-width="1.5" /><path d="M25 60 H 95" /><path d="M60 25 V 95" /><path d="M30 40 Q 60 70 90 40" stroke-opacity="0.5" /><path d="M30 80 Q 60 50 90 80" stroke-opacity="0.5" /><circle cx="45" cy="45" r="2.5" fill="#60a5fa" class="pulse-node" /><circle cx="75" cy="75" r="2.5" fill="#60a5fa" class="pulse-node" /></svg>'
            ];

            foreach ($clientTabs as $idx => $tab):
                $pId = $tab['id'];
                $svg = $panelSvgs[$pId] ?? '<svg class="w-[5rem] h-[5rem] text-[#3b82f6]" viewBox="0 0 100 100" fill="none" stroke="currentColor"><circle cx="50" cy="50" r="10" /></svg>';
            ?>
            <div id="panel-<?= h($pId) ?>"
              class="showcase-panel absolute inset-0 p-8 md:p-12 opacity-0 pointer-events-none flex flex-col justify-between transition-all duration-500 translate-y-4">
              <div class="flex justify-between items-start mb-6">
                <?= $svg ?>
                <span class="text-5xl font-serif font-black text-white/5"><?= sprintf("%02d", $idx + 1) ?></span>
              </div>
              <div>
                <h4 class="text-2xl font-bold font-sans text-white mb-4"><?= h($tab['title']) ?></h4>
                <p class="text-white/80 text-sm md:text-base leading-relaxed font-inter">
                  <?= h($tab['text']) ?>
                </p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Impact Section -->
    <section id="impact" class="relative py-10 lg:py-32 border-t border-white/5 overflow-hidden w-full select-none">
      <!-- Glow ambient background -->
      <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full bg-[#1d4ed8]/5 blur-[150px] pointer-events-none">
      </div>

      <div class="max-w-7xl mx-auto px-6 md:px-12 relative z-10">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="text-xs md:text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">Impact</h2>
          <h3 class="text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-white mb-6">Delivered Results —
            Across Industries</h3>
          <p class="text-white/60 text-sm md:text-base leading-relaxed font-inter">
            The measure of successful data leadership is not the sophistication of the technology deployed. It is the
            quality of decisions made, the speed of business response, and the competitive advantage unlocked.
          </p>
        </div>

        <!-- Swiper Container -->
        <div class="swiper impact-swiper relative py-12">
          <div class="swiper-wrapper">

            <?php
            $slideSvgs = [
                0 => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /><circle cx="12" cy="12" r="3" /></svg>',
                1 => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18M18 9l-5 5-3-3-4 4" stroke-linecap="round" stroke-linejoin="round" /></svg>',
                2 => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="5" width="14" height="14" rx="2" /><path d="M9 5V2M15 5V2M9 19v3M15 19v3M5 9H2M5 15H2M19 9h3M19 15h3" /></svg>',
                3 => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 10h-.7a7 7 0 00-13.8 2.1 4 4 0 00.5 7.9H18a5 5 0 000-10z" /></svg>',
                4 => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><ellipse cx="12" cy="5" rx="9" ry="3" /><path d="M3 5v6c0 1.66 4 3 9 3s9-1.34 9-3V5M3 11v6c0 1.66 4 3 9 3s9-1.34 9-3v-6" /></svg>'
            ];

            $slides = $siteContent['impact']['slides'] ?? [];
            foreach ($slides as $idx => $slide):
                $svg = $slideSvgs[$idx % count($slideSvgs)];
            ?>
            <div class="swiper-slide w-[28rem] h-[22rem] flex-shrink-0 glassmorphism rounded-3xl p-8 border border-[#1d4ed8]/15 flex flex-col justify-between relative shadow-xl transform-gpu">
              <div class="flex justify-between items-start mb-10">
                <div class="w-[3.5rem] h-[3.5rem] rounded-2xl bg-[#1d4ed8]/10 flex items-center justify-center border border-[#1d4ed8]/30 text-[#3b82f6]">
                  <?= $svg ?>
                </div>
                <span class="font-serif text-5xl font-extrabold text-white/5"><?= h($slide['num']) ?></span>
              </div>
              <div>
                <h4 class="text-xl font-bold font-sans text-white mb-3"><?= h($slide['title']) ?></h4>
                <p class="text-white/60 text-sm leading-relaxed font-inter">
                  <?= h($slide['description']) ?>
                </p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <!-- Swiper Navigation Arrows -->
          <div
            class="!hidden md:!flex swiper-button-prev !text-[#3b82f6] !w-12 !h-12 !rounded-full !bg-white/5 !border !border-white/10 hover:!border-[#60a5fa] hover:!bg-[#1d4ed8]/10 !left-4 md:!left-12 flex items-center justify-center after:!text-sm transition-all">
          </div>
          <div
            class="!hidden md:!flex swiper-button-next !text-[#3b82f6] !w-12 !h-12 !rounded-full !bg-white/5 !border !border-white/10 hover:!border-[#60a5fa] hover:!bg-[#1d4ed8]/10 !right-4 md:!right-12 flex items-center justify-center after:!text-sm transition-all">
          </div>

          <!-- Swiper Pagination dots -->
          <div class="swiper-pagination !bottom-[-2rem]"></div>
        </div>
      </div>
    </section>

    <!-- Experience Section -->
    <section id="experience" class="relative py-10 lg:py-32  overflow-hidden w-full select-none">
      <!-- Ambient glow -->
      <div
        class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-[#1d4ed8]/5 blur-[180px] pointer-events-none">
      </div>

      <div class="max-w-5xl mx-auto px-6 md:px-12 relative z-10">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto mb-20 exp-header">
          <h2 class="text-xs md:text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">Experience</h2>
          <h3 class="text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-white mb-6">Career Highlights</h3>
        </div>

        <!-- Timeline Container -->
        <div class="exp-timeline relative">
          <!-- Vertical timeline line (animated fill) -->
          <div class="absolute left-[1.75rem] md:left-1/2 top-0 bottom-0 w-[2px] bg-white/5 -translate-x-1/2">
            <div
              class="exp-line-fill absolute top-0 left-0 w-full bg-gradient-to-b from-[#3b82f6] via-[#1d4ed8] to-[#3b82f6]/20"
              style="height: 0%;"></div>
          </div>

          <?php
          $jobSvgs = [
              0 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <defs>
                        <linearGradient id="bolt-front" x1="16" y1="4" x2="16" y2="24" gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#ffffff" />
                          <stop offset="30%" stop-color="#60a5fa" />
                          <stop offset="100%" stop-color="#1d4ed8" />
                        </linearGradient>
                        <linearGradient id="bolt-side" x1="16" y1="4" x2="20" y2="25" gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#a87500" />
                          <stop offset="100%" stop-color="#473000" />
                        </linearGradient>
                        <filter id="bolt-shadow" x="-20%" y="-20%" width="140%" height="140%">
                          <feDropShadow dx="1" dy="2" stdDeviation="1.5" flood-color="#60a5fa" flood-opacity="0.3" />
                        </filter>
                      </defs>
                      <g filter="url(#bolt-shadow)">
                        <!-- Top slant side -->
                        <path d="M17 4 L18.5 5.5 L17.5 14.5 L16 13 Z" fill="url(#bolt-side)" />
                        <!-- Horizontal middle side -->
                        <path d="M16 13 L17.5 14.5 L22.5 14.5 L21 13 Z" fill="url(#bolt-side)" />
                        <!-- Lower slant side -->
                        <path d="M21 13 L22.5 14.5 L15.5 25.5 L14 24 Z" fill="url(#bolt-side)" />
                        <!-- Front Face -->
                        <path d="M17 4 L11 13 H16 L14 24 L21 13 H16 Z" fill="url(#bolt-front)" />
                      </g>
                    </svg>',
              1 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <defs>
                        <linearGradient id="col-top" x1="0" y1="0" x2="0" y2="1">
                          <stop offset="0%" stop-color="#ffffff" />
                          <stop offset="100%" stop-color="#60a5fa" />
                        </linearGradient>
                        <linearGradient id="col-right" x1="0" y1="0" x2="1" y2="0">
                          <stop offset="0%" stop-color="#3b82f6" />
                          <stop offset="100%" stop-color="#1d4ed8" />
                        </linearGradient>
                        <linearGradient id="col-left" x1="0" y1="0" x2="1" y2="0">
                          <stop offset="0%" stop-color="#1d4ed8" />
                          <stop offset="100%" stop-color="#543c00" />
                        </linearGradient>
                        <filter id="shadow-3d" x="-20%" y="-20%" width="140%" height="140%">
                          <feDropShadow dx="1" dy="2" stdDeviation="1" flood-color="#000000" flood-opacity="0.5" />
                        </filter>
                      </defs>
                      <g filter="url(#shadow-3d)">
                        <!-- Column 3 (Back) -->
                        <path d="M19 8 L24 5.5 L29 8 L24 10.5 Z" fill="url(#col-top)" />
                        <path d="M19 8 L24 10.5 L24 21 L19 18.5 Z" fill="url(#col-left)" />
                        <path d="M24 10.5 L29 8 L29 18.5 L24 21 Z" fill="url(#col-right)" />

                        <!-- Column 2 (Middle) -->
                        <path d="M12 14 L17 11.5 L22 14 L17 16.5 Z" fill="url(#col-top)" />
                        <path d="M12 14 L17 16.5 L17 24 L12 21.5 Z" fill="url(#col-left)" />
                        <path d="M17 16.5 L22 14 L22 21.5 L17 24 Z" fill="url(#col-right)" />

                        <!-- Column 1 (Front) -->
                        <path d="M5 20 L10 17.5 L15 20 L10 22.5 Z" fill="url(#col-top)" />
                        <path d="M5 20 L10 22.5 L10 27 L5 24.5 Z" fill="url(#col-left)" />
                        <path d="M10 22.5 L15 20 L15 24.5 L10 27 Z" fill="url(#col-right)" />

                        <!-- Floating Trend Arrow (3D styled) -->
                        <path d="M4 22 L11 15 L17 16.5 L26 7.5" stroke="#ffffff" stroke-width="1.5"
                          stroke-linecap="round" stroke-linejoin="round"
                          style="filter: drop-shadow(0 0.125rem 0.25rem rgba(96, 165, 250,0.4));" />
                        <path d="M21 7.5 H 26 V 12.5" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round"
                          style="filter: drop-shadow(0 0.125rem 0.25rem rgba(96, 165, 250,0.4));" />
                        <circle cx="26" cy="7.5" r="2" fill="#ffffff" class="pulse-node" />
                      </g>
                    </svg>',
              2 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <defs>
                        <radialGradient id="globe-sphere" cx="35%" cy="35%" r="65%">
                          <stop offset="0%" stop-color="#ffffff" />
                          <stop offset="40%" stop-color="#60a5fa" />
                          <stop offset="85%" stop-color="#1d4ed8" />
                          <stop offset="100%" stop-color="#422e00" />
                        </radialGradient>
                        <linearGradient id="ring-grad" x1="0" y1="0" x2="1" y2="1">
                          <stop offset="0%" stop-color="#ffffff" stop-opacity="0.8" />
                          <stop offset="50%" stop-color="#60a5fa" stop-opacity="0.5" />
                          <stop offset="100%" stop-color="#1d4ed8" stop-opacity="0.1" />
                        </linearGradient>
                      </defs>
                      <!-- Back half of the ring -->
                      <path d="M 6 20 C 7 13, 23 10, 27 15" stroke="url(#ring-grad)" stroke-width="1.5"
                        stroke-linecap="round" />

                      <!-- Globe Sphere -->
                      <circle cx="16" cy="16" r="9.5" fill="url(#globe-sphere)"
                        style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));" />

                      <!-- Grid Lines -->
                      <path d="M 7.5 12 A 9.5 4.5 0 0 0 24.5 12" stroke="#ffffff" stroke-width="0.75"
                        stroke-opacity="0.3" />
                      <path d="M 6.5 16 A 9.5 5.5 0 0 0 25.5 16" stroke="#ffffff" stroke-width="0.75"
                        stroke-opacity="0.4" />
                      <path d="M 7.5 20 A 9.5 4.5 0 0 0 24.5 20" stroke="#ffffff" stroke-width="0.75"
                        stroke-opacity="0.3" />

                      <path d="M 12 6.5 A 5.5 9.5 0 0 0 12 25.5" stroke="#ffffff" stroke-width="0.75"
                        stroke-opacity="0.3" />
                      <path d="M 16 6.5 A 1 9.5 0 0 0 16 25.5" stroke="#ffffff" stroke-width="0.75"
                        stroke-opacity="0.4" />
                      <path d="M 20 6.5 A 5.5 9.5 0 0 0 20 25.5" stroke="#ffffff" stroke-width="0.75"
                        stroke-opacity="0.3" />

                      <!-- Front half of the ring -->
                      <path d="M 27 15 C 26 21, 10 23, 6 20" stroke="url(#ring-grad)" stroke-width="1.5"
                        stroke-linecap="round" />

                      <!-- Pulse nodes -->
                      <circle cx="12" cy="12" r="1.5" fill="#ffffff" class="pulse-node" />
                      <circle cx="20" cy="16" r="1.5" fill="#ffffff" class="pulse-node" />
                      <circle cx="16" cy="20" r="1.5" fill="#ffffff" class="pulse-node" />
                    </svg>',
              3 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <defs>
                        <linearGradient id="case-top" x1="16" y1="4" x2="16" y2="11" gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#ffffff" />
                          <stop offset="100%" stop-color="#60a5fa" />
                        </linearGradient>
                        <linearGradient id="case-right" x1="16" y1="11" x2="24" y2="18.5"
                          gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#60a5fa" />
                          <stop offset="100%" stop-color="#1d4ed8" />
                        </linearGradient>
                        <linearGradient id="case-left" x1="8" y1="7.5" x2="16" y2="22" gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#1d4ed8" />
                          <stop offset="100%" stop-color="#3d2b00" />
                        </linearGradient>
                        <linearGradient id="case-handle" x1="13" y1="2" x2="19" y2="6.5" gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#ffffff" />
                          <stop offset="100%" stop-color="#1d4ed8" />
                        </linearGradient>
                      </defs>
                      <path d="M 12.5 6 L 12.5 3.5 C 12.5 2.5, 19.5 2.5, 19.5 3.5 L 19.5 6" stroke="url(#case-handle)"
                        stroke-width="1.5" stroke-linecap="round" fill="none" />
                      <path d="M 8 7.5 L 16 11.2 L 16 22 L 8 18.2 Z" fill="url(#case-left)" />
                      <path d="M 16 11.2 L 24 7.5 L 24 18.2 L 16 22 Z" fill="url(#case-right)" />
                      <path d="M 16 11.2 L 24 7.5 L 16 3.8 L 8 7.5 Z" fill="url(#case-top)" />
                      <path d="M 11.5 11 L 12.5 11.5 L 12.5 15.5 L 11.5 15 Z" fill="#ffffff" opacity="0.9"
                        style="filter: drop-shadow(0 0.0625rem 0.125rem rgba(0,0,0,0.5));" />
                      <circle cx="12" cy="14.5" r="0.75" fill="#1d4ed8" />
                      <path d="M 19.5 11.5 L 20.5 11 L 20.5 15 L 19.5 15.5 Z" fill="#ffffff" opacity="0.9"
                        style="filter: drop-shadow(0 0.0625rem 0.125rem rgba(0,0,0,0.5));" />
                      <circle cx="20" cy="14.5" r="0.75" fill="#1d4ed8" />
                      <path d="M 8 16 L 8 18.2 L 10 19.2 L 10 17 Z" fill="#60a5fa" opacity="0.8" />
                      <path d="M 24 16 L 24 18.2 L 22 19.2 L 22 17 Z" fill="#60a5fa" opacity="0.8" />
                    </svg>',
              4 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <defs>
                        <linearGradient id="db-top" x1="8" y1="5" x2="24" y2="11" gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#ffffff" />
                          <stop offset="100%" stop-color="#60a5fa" />
                        </linearGradient>
                        <linearGradient id="db-body" x1="8" y1="0" x2="24" y2="0" gradientUnits="userSpaceOnUse">
                          <stop offset="0%" stop-color="#543a00" />
                          <stop offset="25%" stop-color="#60a5fa" />
                          <stop offset="65%" stop-color="#1d4ed8" />
                          <stop offset="100%" stop-color="#302000" />
                        </linearGradient>
                      </defs>
                      <path d="M 8 22 A 8 3 0 0 0 24 22 L 24 27 A 8 3 0 0 1 8 27 Z" fill="url(#db-body)" />
                      <ellipse cx="16" cy="22" rx="8" ry="3" fill="url(#db-top)" />
                      <path d="M 10 24.5 H 14" stroke="#ffffff" stroke-width="0.5" opacity="0.7" />
                      <circle cx="21" cy="24.5" r="0.75" fill="#ffffff" class="pulse-node" />

                      <path d="M 8 15 A 8 3 0 0 0 24 15 L 24 20 A 8 3 0 0 1 8 20 Z" fill="url(#db-body)" />
                      <ellipse cx="16" cy="15" rx="8" ry="3" fill="url(#db-top)" />
                      <path d="M 10 17.5 H 14" stroke="#ffffff" stroke-width="0.5" opacity="0.7" />
                      <circle cx="21" cy="17.5" r="0.75" fill="#ffffff" class="pulse-node" />

                      <path d="M 8 8 A 8 3 0 0 0 24 8 L 24 13 A 8 3 0 0 1 8 13 Z" fill="url(#db-body)" />
                      <ellipse cx="16" cy="8" rx="8" ry="3" fill="url(#db-top)" />
                      <path d="M 10 10.5 H 14" stroke="#ffffff" stroke-width="0.5" opacity="0.7" />
                      <circle cx="21" cy="10.5" r="0.75" fill="#ffffff" class="pulse-node" />
                    </svg>'
          ];

          foreach ($expJobs as $idx => $job):
              $isLeft = ($idx % 2 !== 0);
              // Right side: card starts at 50%+gap; Left side: card sits at left (ml-0) with 50%-gap width
              $alignClass = $isLeft ? 'md:w-[calc(50%-2rem)] md:ml-0' : 'md:w-[calc(50%-2rem)] md:ml-[calc(50%+2rem)]';
              $textAlign  = $isLeft ? 'md:text-right' : '';
              $headerAlign = $isLeft ? 'md:flex-row-reverse' : '';
              $svg = $jobSvgs[$idx % count($jobSvgs)];
              $period = $job['period'] ?? '';
          ?>
          <!-- Timeline Item -->
          <div class="exp-item relative flex flex-col md:flex-row md:items-start mb-16 md:mb-20 group">
            <!-- Dot -->
            <div
              class="exp-dot absolute left-[1.75rem] md:left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-[#0a0a0a] border-2 border-white/15 z-10 transition-all duration-500 group-[.is-active]:border-[#3b82f6] group-[.is-active]:bg-[#3b82f6] group-[.is-active]:shadow-[0_0_12px_rgba(59,130,246,0.6)]">
              <div
                class="absolute inset-0 rounded-full bg-[#3b82f6]/30 scale-0 group-[.is-active]:scale-[2.5] transition-transform duration-700">
              </div>
            </div>
            <!-- Content Card -->
            <div class="ml-16 md:ml-0 <?= $alignClass ?> <?= $textAlign ?>">
              <div
                class="exp-card glassmorphism rounded-2xl p-6 md:p-8 border border-white/8 hover:border-[#1d4ed8]/30 transition-all duration-500">
                <div class="flex items-center gap-3 mb-4 <?= $headerAlign ?>">
                  <div class="w-10 h-10 rounded-xl bg-[#1d4ed8]/10 flex items-center justify-center border border-[#1d4ed8]/25 text-[#3b82f6]">
                    <?= $svg ?>
                  </div>
                  <span class="text-[#3b82f6]/60 text-xs font-semibold font-inter uppercase tracking-widest"><?= h($period) ?></span>
                </div>
                <h4 class="text-lg md:text-xl font-bold text-white mb-1 font-sans"><?= h($job['title']) ?></h4>
                <p class="text-[#3b82f6] text-sm font-semibold mb-3 font-inter"><?= h($job['company']) ?> <span
                    class="text-white/30 mx-2">•</span> <span class="text-white/40 font-normal"><?= h($job['location']) ?></span></p>
                <div class="text-white/55 text-sm leading-relaxed font-inter space-y-2">
                  <?php foreach (($job['bullets'] ?? []) as $bullet): ?>
                    <p>• <?= h($bullet) ?></p>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Technology Expertise Section -->
    <section id="technology" class="relative pb-10 lg:pb-30 pt-10 overflow-hidden w-full">


      <div class="max-w-[90%] mx-auto  md:px-12 relative z-10">
        <!-- Section Header -->
        <div class="text-center mx-auto">
          <h2 class="text-xs md:text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter">TECHNOLOGY EXPERTISE</h2>
          <h3 class="text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-white mb-6">Platforms & Technologies</h3>
          <p class="text-white/60 w-full lg:w-1/2 mx-auto text-base md:text-lg leading-relaxed font-inter">
            I bring hands-on proficiency across the full data and analytics stack — from strategy and governance through
            to platform architecture and delivery execution.
          </p>
        </div>

        <div class="w-full">
          <?php foreach ($techCategories as $category): 
              $itemCount = count($category['items'] ?? []);
              if ($itemCount === 5) {
                  $gridClass = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 w-full gap-6 pt-10 justify-center';
              } elseif ($itemCount === 4) {
                  $gridClass = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 lg:max-w-[80%] mx-auto w-full gap-6 pt-10 justify-center';
              } elseif ($itemCount === 3) {
                  $gridClass = 'grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 lg:max-w-[60%] mx-auto w-full gap-6 pt-10 justify-center';
              } elseif ($itemCount === 2) {
                  $gridClass = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 lg:max-w-[40%] mx-auto w-full gap-6 pt-10 justify-center';
              } else {
                  $gridClass = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 w-full gap-6 pt-10 justify-center';
              }
          ?>
          <div class="flex flex-col pt-20 w-full items-center justify-center">
            <div class="text-2xl font-semibold">
              <?= h($category['name']) ?>
            </div>
            <div class="<?= $gridClass ?>">
              <?php foreach (($category['items'] ?? []) as $item): 
                  $itemImg = $item['image'] ?? '/assets/images/strat_3d_icon.png';
                  $itemName = $item['name'] ?? '';
              ?>
              <div class="premium-card group">
                <div class="icon-wrapper">
                  <img src="<?= h($itemImg) ?>" class="w-full h-full object-contain" alt="<?= h($itemName) ?>">
                </div>
                <h4
                  class="text-xs font-bold text-gray-300 group-hover:text-[#3b82f6] transition-colors duration-300 font-sans tracking-widest uppercase mt-2 text-center">
                  <?= h($itemName) ?></h4>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Why Work With Me / The Difference Section -->
    <section id="the-difference" class="relative py-10 md:py-32 overflow-hidden w-full">
      <!-- Background subtle glow -->
      <div class="absolute top-1/4 right-1/4 w-[400px] h-[400px] rounded-full pointer-events-none z-0"
        style="background: radial-gradient(circle, rgba(29, 78, 216,0.04) 0%, transparent 70%); filter: blur(80px);">
      </div>

      <div class="max-w-[90%] mx-auto md:px-12 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">

          <!-- Left Column: Header & Context -->
          <div class="lg:col-span-5 difference-header flex flex-col gap-6">
            <h2 class="text-xs md:text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] font-inter">
              THE DIFFERENCE
            </h2>
            <h3 class="text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-white leading-tight">
              Why Work With Me
            </h3>
            <p class="text-white/85 text-base md:text-lg leading-relaxed font-sans mt-2">
            <?= $diffText1 ?>
          </p>
            <p class="text-white/60 text-sm md:text-base leading-relaxed font-sans">
            <?= $diffText2 ?>
          </p>
          </div>

          <!-- Right Column: Staggered Value Cards -->
          <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($diffCards as $dc): ?>
            <div class="difference-item">
              <div class="premium-card group w-full h-full">
                <div class="flex items-start gap-4 w-full">
                  <div
                    class="flex-shrink-0 w-8 h-8 rounded-lg bg-[#1d4ed8]/10 border border-[#1d4ed8]/30 flex items-center justify-center text-[#3b82f6] group-hover:bg-[#1d4ed8]/20 group-hover:border-[#3b82f6]/50 transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                  <div class="flex flex-col gap-2 text-left">
                    <h4
                      class="text-sm font-bold text-white font-sans tracking-wide transition-colors duration-300 group-hover:text-[#3b82f6]">
                      <?= h($dc['title']) ?>
                    </h4>
                    <p class="text-xs text-white/50 leading-relaxed font-sans">
                      <?= h($dc['text'] ?? '') ?>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action Section -->
    <section id="cta" class="relative py-10 md:py-36 overflow-hidden w-full ">
      <!-- Background subtle glow -->
      <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full pointer-events-none z-0"
        style="background: radial-gradient(circle, rgba(29,78,216,0.05) 0%, transparent 70%); filter: blur(100px);">
      </div>

      <div class="max-w-[90%] mx-auto  lg:px-12 relative z-10 text-center flex flex-col items-center">
        <div class="cta-container max-w-3xl flex flex-col items-center">
          <span class="text-xs md:text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] font-inter">
            <?= h($contactSubheading) ?></span>
          <h2 class="text-3xl md:text-5xl font-serif font-extrabold tracking-tight text-white leading-tight mt-4">
            <?= h($contactHeadline) ?></h2>
          <p class="text-white/70 text-base md:text-lg leading-relaxed font-sans mt-6">
            Whether you are exploring AI, modernizing analytics, implementing an ERP system, or establishing enterprise
            data governance, let us design a practical roadmap that delivers measurable business value.
          </p>
        </div>

        <!-- Elegant Inline Link Bar with Premium Gold SVGs and No Subtext -->
        <div
          class="cta-links-container flex flex-wrap items-center justify-center gap-6 md:gap-12 mt-16 text-xs md:text-sm font-sans tracking-widest uppercase font-bold text-[#3b82f6]">
          <span class="text-[#1d4ed8]/40 text-lg md:text-xl font-light select-none">&raquo;</span>

          <!-- Link 1: Book Consultation -->
          <a href="https://wa.me/971557154748" target="_blank" rel="noopener noreferrer"
            class="cta-link-item flex items-center gap-3.5 group relative py-2 px-1 transition-colors duration-300 hover:text-white">
            <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-115" viewBox="0 0 24 24" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="gold-grad-1" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#60a5fa" />
                  <stop offset="50%" stop-color="#3b82f6" />
                  <stop offset="100%" stop-color="#1d4ed8" />
                </linearGradient>
              </defs>
              <rect x="3" y="5" width="18" height="15" rx="2" stroke="url(#gold-grad-1)" stroke-width="2" />
              <path d="M7 3v4M17 3v4" stroke="url(#gold-grad-1)" stroke-width="2" stroke-linecap="round" />
              <path d="M7 10h10M7 14h6M7 17h10" stroke="url(#gold-grad-1)" stroke-width="1.5" stroke-linecap="round"
                opacity="0.8" />
            </svg>
            <span>Book a Consultation</span>
            <span
              class="absolute bottom-0 left-0 w-0 h-[2px] bg-[#3b82f6] transition-all duration-300 group-hover:w-full"></span>
          </a>

          <span class="text-white/20 select-none">&bull;</span>

          <!-- Link 2: LinkedIn -->
          <a href="<?= h($contactLinkedin) ?>" target="_blank" rel="noopener noreferrer"
            class="cta-link-item flex items-center gap-3.5 group relative py-2 px-1 transition-colors duration-300 hover:text-white">
            <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-115" viewBox="0 0 24 24" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="gold-grad-2" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#60a5fa" />
                  <stop offset="50%" stop-color="#3b82f6" />
                  <stop offset="100%" stop-color="#1d4ed8" />
                </linearGradient>
              </defs>
              <rect x="3" y="3" width="18" height="18" rx="3.5" stroke="url(#gold-grad-2)" stroke-width="2" />
              <path d="M7 10h2.5v7H7v-7zm1.25-2.25a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z" fill="url(#gold-grad-2)" />
              <path
                d="M12.5 10H15c1.38 0 2.5 1.12 2.5 2.5V17h-2.5v-4.5c0-.28-.22-.5-.5-.5h-.5c-.28 0-.5.22-.5.5V17h-2.5v-7z"
                fill="url(#gold-grad-2)" />
            </svg>
            <span>Connect on LinkedIn</span>
            <span
              class="absolute bottom-0 left-0 w-0 h-[2px] bg-[#3b82f6] transition-all duration-300 group-hover:w-full"></span>
          </a>

          <span class="text-white/20 select-none">&bull;</span>

          <!-- Link 3: Send Enquiry -->
          <a href="mailto:<?= h($contactEmail) ?>"
            class="cta-link-item flex items-center gap-3.5 group relative py-2 px-1 transition-colors duration-300 hover:text-white">
            <svg class="w-6 h-6 transition-transform duration-300 group-hover:scale-115" viewBox="0 0 24 24" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="gold-grad-3" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#60a5fa" />
                  <stop offset="50%" stop-color="#3b82f6" />
                  <stop offset="100%" stop-color="#1d4ed8" />
                </linearGradient>
              </defs>
              <rect x="3" y="5" width="18" height="14" rx="2" stroke="url(#gold-grad-3)" stroke-width="2" />
              <path d="M4 6.5l8 5.5 8-5.5M4 17.5l6.5-5M20 17.5l-6.5-5" stroke="url(#gold-grad-3)" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>Send an Enquiry</span>
            <span
              class="absolute bottom-0 left-0 w-0 h-[2px] bg-[#3b82f6] transition-all duration-300 group-hover:w-full"></span>
          </a>

          <span class="text-[#1d4ed8]/40 text-lg md:text-xl font-light select-none">&laquo;</span>
        </div>
      </div>
    </section>

  </main>

  <!-- Opportunity Section Plexus Network Canvas Script -->
  <script type="module">
    const canvas = document.getElementById('opportunity-network-canvas');
    if (canvas) {
      const ctx = canvas.getContext('2d');
      const section = document.getElementById('opportunity');

      let width = canvas.width = section.offsetWidth;
      let height = canvas.height = section.offsetHeight;

      const particles = [];
      const particleCount = 75;
      const connectionDistance = 120;

      const colors = ['rgba(29, 78, 216, 0.35)', 'rgba(59,130,246,0.25)', 'rgba(96,165,250,0.15)'];

      class Particle {
        constructor() {
          this.x = Math.random() * width;
          this.y = Math.random() * height;
          this.vx = (Math.random() - 0.5) * 0.45;
          this.vy = (Math.random() - 0.5) * 0.45;
          this.radius = Math.random() * 2 + 0.8;
          this.color = colors[Math.floor(Math.random() * colors.length)];
        }

        update() {
          this.x += this.vx;
          this.y += this.vy;

          if (this.x < 0 || this.x > width) this.vx *= -1;
          if (this.y < 0 || this.y > height) this.vy *= -1;
        }

        draw() {
          ctx.beginPath();
          ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
          ctx.fillStyle = this.color;
          ctx.fill();
        }
      }

      // Initialize
      for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
      }

      let mouse = { x: null, y: null };

      section.addEventListener('mousemove', (e) => {
        const rect = section.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
      });

      section.addEventListener('mouseleave', () => {
        mouse.x = null;
        mouse.y = null;
      });

      function animate() {
        ctx.clearRect(0, 0, width, height);

        // Draw connections
        for (let i = 0; i < particles.length; i++) {
          particles[i].update();
          particles[i].draw();

          // Connect to other particles
          for (let j = i + 1; j < particles.length; j++) {
            const dx = particles[i].x - particles[j].x;
            const dy = particles[i].y - particles[j].y;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < connectionDistance) {
              const alpha = (1 - dist / connectionDistance) * 0.15;
              ctx.strokeStyle = `rgba(59, 130, 246, ${alpha})`;
              ctx.lineWidth = 0.6;
              ctx.beginPath();
              ctx.moveTo(particles[i].x, particles[i].y);
              ctx.lineTo(particles[j].x, particles[j].y);
              ctx.stroke();
            }
          }

          // Connect to mouse
          if (mouse.x !== null && mouse.y !== null) {
            const dx = particles[i].x - mouse.x;
            const dy = particles[i].y - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < 135) {
              const alpha = (1 - dist / 135) * 0.25;
              ctx.strokeStyle = `rgba(96, 165, 250, ${alpha})`;
              ctx.lineWidth = 0.8;
              ctx.beginPath();
              ctx.moveTo(particles[i].x, particles[i].y);
              ctx.lineTo(mouse.x, mouse.y);
              ctx.stroke();
            }
          }
        }

        requestAnimationFrame(animate);
      }

      animate();

      // Resize event
      window.addEventListener('resize', () => {
        width = canvas.width = section.offsetWidth;
        height = canvas.height = section.offsetHeight;
      });
    }
  </script>

  <!-- Project Script Entry Point -->
  <script>
    window.HERO_TITLE = <?= json_encode($heroTitle) ?>;
  </script>
  <!-- CDNs for GSAP, ScrollTrigger, and Lenis -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script src="https://unpkg.com/lenis@1.1.20/dist/lenis.min.js"></script>

  <?php if (!empty($jsPath)): ?>
    <script type="module" src="<?= h($jsPath) ?>"></script>
  <?php endif; ?>

  <!-- ── Late Font Override: ensures fonts win after Tailwind CDN async injection ── -->
  <style id="font-override">
    html, body { font-family: 'Montserrat', sans-serif !important; }
    .font-sans  { font-family: 'Montserrat', sans-serif !important; }
    .font-serif { font-family: 'Syne', sans-serif !important; }
    .font-inter { font-family: 'Inter', sans-serif !important; }
    .font-mono  { font-family: ui-monospace, 'Courier New', monospace !important; }
    .hero-name  { font-family: 'Montserrat', sans-serif !important; }
  </style>
  <script>
    // Watch for Tailwind CDN async style injection and re-apply font overrides afterward
    (function() {
      var CSS = [
        "html, body { font-family: 'Montserrat', sans-serif !important; }",
        ".font-sans  { font-family: 'Montserrat', sans-serif !important; }",
        ".font-serif { font-family: 'Syne', sans-serif !important; }",
        ".font-inter { font-family: 'Inter', sans-serif !important; }",
        ".font-mono  { font-family: ui-monospace, 'Courier New', monospace !important; }",
        ".hero-name  { font-family: 'Montserrat', sans-serif !important; }"
      ].join('\n');

      function applyFonts() {
        var existing = document.getElementById('font-override-late');
        if (existing) existing.remove();
        var s = document.createElement('style');
        s.id = 'font-override-late';
        s.textContent = CSS;
        document.head.appendChild(s);
      }

      // MutationObserver: watches for Tailwind CDN injecting <style> tags
      var observer = new MutationObserver(function(mutations) {
        for (var m of mutations) {
          for (var n of m.addedNodes) {
            if (n.tagName === 'STYLE' && n.id !== 'font-override' && n.id !== 'font-override-late') {
              applyFonts();
            }
          }
        }
      });
      observer.observe(document.head, { childList: true });

      // Disconnect after 5 seconds — Tailwind should be done by then
      setTimeout(function() { observer.disconnect(); applyFonts(); }, 5000);

      // Also apply on DOMContentLoaded immediately
      document.addEventListener('DOMContentLoaded', applyFonts);
    })();
  </script>

</body>

</html>