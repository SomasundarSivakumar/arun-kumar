import fs from 'fs';

const htmlPath = 'c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html';
const phpPath = 'c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.php';

let content = fs.readFileSync(htmlPath, 'utf8');

// 1. PHP logic block to insert at the very top
const phpHeader = `<?php
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
$bgColor      = $theme['bg_color']      ?? '#060913';
$textColor    = $theme['text_color']    ?? '#f3f4f6';
$sidebarBg    = $theme['sidebar_bg']    ?? '#03050a';

// ── Asset Loader ──────────────────────────────────────────────────────
$isDev = false;
$viteHost = 'http://localhost:5173';
$fp = @fsockopen('127.0.0.1', 5173, $errno, $errstr, 0.02);
if ($fp) {
    $isDev = true;
    fclose($fp);
}
$cssPath = '';
$jsPath = '';
if ($isDev) {
    $cssPath = $viteHost . '/src/style.css';
    $jsPath = $viteHost . '/src/main.js';
} else {
    $cssMatches = glob(__DIR__ . '/dist/assets/*.css');
    if (!empty($cssMatches)) { $cssPath = 'dist/assets/' . basename($cssMatches[0]); }
    $jsMatches = glob(__DIR__ . '/dist/assets/*.js');
    if (!empty($jsMatches)) { $jsPath = 'dist/assets/' . basename($jsMatches[0]); }
}

function h($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

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
`;

// 1. Prepend PHP header
content = phpHeader + '\n' + content;

// 2. Title & metas
content = content.replace(
  /<title>[\s\S]*?<\/title>/i,
  '<title><?= h($metaTitle) ?></title>'
);

content = content.replace(
  /<meta\s+name="description"\s+[\s\S]*?\/>/i,
  `  <meta name="description" content="<?= h($metaDescription) ?>" />
  <meta name="keywords" content="<?= h($metaKeywords) ?>" />`
);

// 3. Stylesheet links
content = content.replace(
  /<link\s+rel="stylesheet"\s+href="\/src\/style\.css"\s*\/?>/i,
  `<?php if ($isDev): ?>
    <script type="module" src="http://localhost:5173/@vite/client"></script>
  <?php endif; ?>
  <?php if (!empty($cssPath)): ?>
    <link rel="stylesheet" href="<?= h($cssPath) ?>" />
  <?php endif; ?>`
);

// Inject dynamic variables in head
content = content.replace(
  '</head>',
  `  <!-- ── Dynamic Theme CSS Variables from Admin ── -->
  <style>
    :root {
      --color-primary:  <?= h($primaryColor) ?>;
      --color-accent:   <?= h($accentColor) ?>;
      --color-bg:       <?= h($bgColor) ?>;
      --color-text:     <?= h($textColor) ?>;
      --color-sidebar:  <?= h($sidebarBg) ?>;
    }
    body {
      background-color: <?= h($bgColor) ?> !important;
      color: <?= h($textColor) ?> !important;
    }
    /* Override hardcoded values with theme variables */
    .text-\\[\\#1d4ed8\\], .text-\\[\\#3b82f6\\], .text-\\[\\#60a5fa\\], .text-violet-500 { color: var(--color-accent) !important; }
    .bg-\\[\\#060913\\], .bg-\\[\\#0a0a0a\\] { background-color: var(--color-bg) !important; }
    .bg-\\[\\#03050a\\] { background-color: var(--color-sidebar) !important; }
  </style>
</head>`
);

// 4. Hero Section
content = content.replace(
  /<span class="block text-\[clamp\(3rem,7vw,6rem\)\]">Arun Kumar Jayakumar<\/span>/i,
  '<span class="block text-[clamp(3rem,7vw,6rem)]"><?= h($heroName) ?></span>'
);

content = content.replace(
  /<div class="hero-tagline flex flex-wrap justify-center items-center gap-3 mt-6 select-none">[\s\S]*?<\/div>/i,
  `<div class="hero-tagline flex flex-wrap justify-center items-center gap-3 mt-6 select-none">
              <?php foreach ($heroTaglines as $tagline): ?>
              <span
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold tracking-widest uppercase font-inter text-gray-300 bg-white/[0.02] border border-white/5 hover:border-[#1d4ed8]/30 hover:bg-[#1d4ed8]/5 hover:text-[#3b82f6] transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_4px_20px_rgba(29,78,216,0.15)] cursor-default">
                <span class="w-1.5 h-1.5 rounded-full bg-[#1d4ed8]"></span>
                <?= h($tagline) ?>
              </span>
              <?php endforeach; ?>
            </div>`
);

// 5. Core Verticals Marquee
content = content.replace(
  /<div class="marquee-track py-2">[\s\S]*?<!-- Marquee Group B[^>]*>[\s\S]*?<\/div>\s*<\/div>/i,
  `<div class="marquee-track py-2">
          <!-- Marquee Group A -->
          <div class="marquee-group">
            <?php renderMarqueeGroup($expertiseItems); ?>
          </div>
          <!-- Marquee Group B (Duplicated for seamless infinite looping) -->
          <div class="marquee-group">
            <?php renderMarqueeGroup($expertiseItems); ?>
          </div>
        </div>`
);

// 6. Opportunity Section
content = content.replace(
  /Executive\s+Data\s+Leadership\s+—\s+Without\s+the\s+Full-Time\s+Overhead/i,
  '<?= h($oppTitle) ?>'
);

content = content.replace(
  /Most\s+organizations\s+sit\s+on\s+<span[\s\S]*?>significant\s+untapped\s+data\s+potential<\/span>\./i,
  '<?= $oppFrictionTitle ?>'
);

content = content.replace(
  /<div class="flex flex-col gap-4 text-gray-400 font-light text-sm md:text-base leading-relaxed z-10">[\s\S]*?Decisions are delayed[\s\S]*?What is missing[\s\S]*?<\/div>\s*<\/div>/i,
  `<div class="flex flex-col gap-4 text-gray-400 font-light text-sm md:text-base leading-relaxed z-10">
              <p><?= h($oppFrictionText) ?></p>
              <div class="pl-4 border-l-2 border-[#1d4ed8]/50 py-1 my-2">
                <p class="font-serif italic text-white/90 text-base md:text-lg">"<?= h($oppQuote) ?>"</p>
              </div>
            </div>`
);

content = content.replace(
  /Executive-level\s+data\s+leadership\s+<span[\s\S]*?>precisely\s+when\s+you\s+need\s+it<\/span>\./i,
  '<?= $oppSolutionTitle ?>'
);

content = content.replace(
  /<p class="text-gray-300 font-light text-sm md:text-base leading-relaxed z-10">[\s\S]*?As a Fractional Chief Data Officer[\s\S]*?<\/p>/i,
  `<p class="text-gray-300 font-light text-sm md:text-base leading-relaxed z-10">
              <?= h($oppSolutionText) ?>
            </p>`
);

content = content.replace(
  /<!-- Key pillars of Fractional CDO -->\s*<div class="flex flex-wrap gap-2\.5 mt-2 z-10">[\s\S]*?<\/div>/i,
  `<!-- Key pillars of Fractional CDO -->
            <div class="flex flex-wrap gap-2.5 mt-2 z-10">
              <?php foreach ($oppPillars as $pillar): ?>
              <span
                class="px-3.5 py-1.5 rounded-full text-xs font-medium bg-[#1d4ed8]/8 border border-[#1d4ed8]/25 text-[#3b82f6] backdrop-blur-sm shadow-sm transition-all duration-300 hover:bg-[#1d4ed8]/15 hover:border-[#1d4ed8]/50 cursor-default">
                <?= h($pillar) ?>
              </span>
              <?php endforeach; ?>
            </div>`
);

content = content.replace(
  /<p class="text-xl md:text-3xl font-serif font-semibold text-white\/95 leading-relaxed">[\s\S]*?<\/p>/i,
  `<p class="text-xl md:text-3xl font-serif font-semibold text-white/95 leading-relaxed">
            <?= h($oppBottomQuote) ?>
          </p>`
);

// 7. About Section
content = content.replace(
  /Meet<br\s*\/?>\s*<span\s+class="text-white\s+text-6xl">Arun\s+Kumar\s+Jayakumar<\/span>/i,
  'Meet<br /><span class="text-white text-6xl"><?= h($heroName) ?></span>'
);

content = content.replace(
  /<!-- Left Column: Biography Content \(First 50%\) -->[\s\S]*?I am a <span class="text-\[#3b82f6\][\s\S]*?<\/div>\s*<\/div>/i,
  `<!-- Left Column: Biography Content (First 50%) -->
            <div
              class="w-full lg:w-[calc(50%-1.25rem)] flex flex-col gap-6 text-gray-300 font-light text-base leading-relaxed">
              <p class="text-lg text-white font-medium">
                <?= nl2br(h($aboutBio)) ?>
              </p>
              <div class="mt-4 p-5 rounded-2xl bg-[#1d4ed8]/5 border border-[#1d4ed8]/15 relative overflow-hidden group">
                <div
                  class="absolute left-0 top-0 bottom-0 w-[3px] bg-[#1d4ed8]/60 group-hover:bg-[#3b82f6] transition-colors duration-300">
                </div>
                <p class="font-serif italic text-white/90 text-lg leading-relaxed relative z-10">
                  "<?= h($aboutQuote) ?>"
                </p>
              </div>
              
              <div class="grid grid-cols-2 gap-4 mt-4">
                <?php foreach ($aboutStats as $stat): ?>
                <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 text-center">
                  <div class="text-3xl font-bold text-white font-sans"><?= h($stat['value']) ?></div>
                  <div class="text-xs uppercase tracking-widest text-[#60a5fa] mt-1"><?= h($stat['label']) ?></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>`
);

content = content.replace(
  /src="\/assets\/images\/arun_kumar\.png"/i,
  'src="<?= h($aboutImage) ?>"'
);

// 8. Capabilities Section
content = content.replace(
  /<div\s+class="flex\s+flex-col\s+justify-between\s+py-2\s+h-\[600px\]\s+flex-grow"\s+id="timeline-content-container">[\s\S]*?Core\s+Disciplines[\s\S]*?<\/div>\s*<\/div>/i,
  `<div class="flex flex-col justify-between py-2 h-[600px] flex-grow" id="timeline-content-container">
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
            </div>`
);

// 9. Services Section
content = content.replace(
  /<div\s+class="flex\s+flex-wrap\s+justify-center\s+gap-\[1\.5rem\]\s+lg:gap-\[2rem\]\s+px-\[1\.5rem\]\s+md:px-\[4rem\]\s+mt-\[3rem\]\s+lg:mt-\[4rem\]">[\s\S]*?Migration Planning & Risk Management[\s\S]*?<\/div>\s*<\/div>\s*<\/section>/i,
  `<div class="flex flex-wrap justify-center gap-[1.5rem] lg:gap-[2rem] px-[1.5rem] md:px-[4rem] mt-[3rem] lg:mt-[4rem]">
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
    </section>`
);

// 10. Ideal Clients Section
content = content.replace(
  /<p class="text-white\/60 text-base md:text-lg leading-relaxed font-inter">[\s\S]*?<\/p>/i,
  `<p class="text-white/60 text-base md:text-lg leading-relaxed font-inter">
                <?= h($clientIntro) ?>
              </p>`
);

content = content.replace(
  /<div\s+class="client-nav\s+flex\s+flex-col\s+gap-6"\s+id="client-nav-list">[\s\S]*?<\/div>/i,
  `<div class="client-nav flex flex-col gap-6" id="client-nav-list">
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
          </div>`
);

content = content.replace(
  /<!-- 1\. CEOs & Founders Detail Panel -->[\s\S]*?<\/section>/i,
  `<?php
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
    </section>`
);

// 11. Impact Swiper Slides
content = content.replace(
  /<!-- Slide 1: Enterprise Data Governance -->[\s\S]*?<!-- Swiper Navigation Arrows -->/i,
  `<?php
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
              <div class="flex justify-between items-start">
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
            <!-- Swiper Navigation Arrows -->`
);

// 12. Experience highlights
content = content.replace(
  /<!-- Timeline Item 1 -->[\s\S]*?<\/div>\s*<\/div>\s*<\/section>/i,
  `<?php
          $jobSvgs = [
              0 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#bolt-shadow)"><path d="M17 4 L18.5 5.5 L17.5 14.5 L16 13 Z" fill="url(#bolt-side)" /><path d="M16 13 L17.5 14.5 L22.5 14.5 L21 13 Z" fill="url(#bolt-side)" /><path d="M21 13 L22.5 14.5 L15.5 25.5 L14 24 Z" fill="url(#bolt-side)" /><path d="M17 4 L11 13 H16 L14 24 L21 13 H16 Z" fill="url(#bolt-front)" /></g></svg>',
              1 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g filter="url(#shadow-3d)"><path d="M19 8 L24 5.5 L29 8 L24 10.5 Z" fill="url(#col-top)" /><path d="M19 8 L24 10.5 L24 21 L19 18.5 Z" fill="url(#col-left)" /><path d="M24 10.5 L29 8 L29 18.5 L24 21 Z" fill="url(#col-right)" /><path d="M12 14 L17 11.5 L22 14 L17 16.5 Z" fill="url(#col-top)" /><path d="M12 14 L17 16.5 L17 24 L12 21.5 Z" fill="url(#col-left)" /><path d="M17 16.5 L22 14 L22 21.5 L17 24 Z" fill="url(#col-right)" /><path d="M5 20 L10 17.5 L15 20 L10 22.5 Z" fill="url(#col-top)" /><path d="M5 20 L10 22.5 L10 27 L5 24.5 Z" fill="url(#col-left)" /><path d="M10 22.5 L15 20 L15 24.5 L10 27 Z" fill="url(#col-right)" /><path d="M4 22 L11 15 L17 16.5 L26 7.5" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0.125rem 0.25rem rgba(96, 165, 250,0.4));" /><path d="M21 7.5 H 26 V 12.5" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0.125rem 0.25rem rgba(96, 165, 250,0.4));" /><circle cx="26" cy="7.5" r="2" fill="#ffffff" class="pulse-node" /></g></svg>',
              2 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M 6 20 C 7 13, 23 10, 27 15" stroke="url(#ring-grad)" stroke-width="1.5" stroke-linecap="round" /><circle cx="16" cy="16" r="9.5" fill="url(#globe-sphere)" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));" /><path d="M 7.5 12 A 9.5 4.5 0 0 0 24.5 12" stroke="#ffffff" stroke-width="0.75" stroke-opacity="0.3" /><path d="M 6.5 16 A 9.5 5.5 0 0 0 25.5 16" stroke="#ffffff" stroke-width="0.75" stroke-opacity="0.4" /><path d="M 7.5 20 A 9.5 4.5 0 0 0 24.5 20" stroke="#ffffff" stroke-width="0.75" stroke-opacity="0.3" /><path d="M 12 6.5 A 5.5 9.5 0 0 0 12 25.5" stroke="#ffffff" stroke-width="0.75" stroke-opacity="0.3" /><path d="M 16 6.5 A 1 9.5 0 0 0 16 25.5" stroke="#ffffff" stroke-width="0.75" stroke-opacity="0.4" /><path d="M 20 6.5 A 5.5 9.5 0 0 0 20 25.5" stroke="#ffffff" stroke-width="0.75" stroke-opacity="0.3" /><path d="M 27 15 C 26 21, 10 23, 6 20" stroke="url(#ring-grad)" stroke-width="1.5" stroke-linecap="round" /><circle cx="12" cy="12" r="1.5" fill="#ffffff" class="pulse-node" /><circle cx="20" cy="16" r="1.5" fill="#ffffff" class="pulse-node" /><circle cx="16" cy="20" r="1.5" fill="#ffffff" class="pulse-node" /></svg>',
              3 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M 12.5 6 L 12.5 3.5 C 12.5 2.5, 19.5 2.5, 19.5 3.5 L 19.5 6" stroke="url(#case-handle)" stroke-width="1.5" stroke-linecap="round" fill="none" /><path d="M 8 7.5 L 16 11.2 L 16 22 L 8 18.2 Z" fill="url(#case-left)" /><path d="M 16 11.2 L 24 7.5 L 24 18.2 L 16 22 Z" fill="url(#case-right)" /><path d="M 16 11.2 L 24 7.5 L 16 3.8 L 8 7.5 Z" fill="url(#case-top)" /><path d="M 11.5 11 L 12.5 11.5 L 12.5 15.5 L 11.5 15 Z" fill="#ffffff" opacity="0.9" style="filter: drop-shadow(0 0.0625rem 0.125rem rgba(0,0,0,0.5));" /><circle cx="12" cy="14.5" r="0.75" fill="#1d4ed8" /><path d="M 19.5 11.5 L 20.5 11 L 20.5 15 L 19.5 15.5 Z" fill="#ffffff" opacity="0.9" style="filter: drop-shadow(0 0.0625rem 0.125rem rgba(0,0,0,0.5));" /><circle cx="20" cy="14.5" r="0.75" fill="#1d4ed8" /><path d="M 8 16 L 8 18.2 L 10 19.2 L 10 17 Z" fill="#60a5fa" opacity="0.8" /><path d="M 24 16 L 24 18.2 L 22 19.2 L 22 17 Z" fill="#60a5fa" opacity="0.8" /></svg>',
              4 => '<svg class="w-5 h-5" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M 8 22 A 8 3 0 0 0 24 22 L 24 27 A 8 3 0 0 1 8 27 Z" fill="url(#db-body)" /><ellipse cx="16" cy="22" rx="8" ry="3" fill="url(#db-top)" /><path d="M 10 24.5 H 14" stroke="#ffffff" stroke-width="0.5" opacity="0.7" /><circle cx="21" cy="24.5" r="0.75" fill="#ffffff" class="pulse-node" /><path d="M 8 15 A 8 3 0 0 0 24 15 L 24 20 A 8 3 0 0 1 8 20 Z" fill="url(#db-body)" /><ellipse cx="16" cy="15" rx="8" ry="3" fill="url(#db-top)" /><path d="M 10 17.5 H 14" stroke="#ffffff" stroke-width="0.5" opacity="0.7" /><circle cx="21" cy="17.5" r="0.75" fill="#ffffff" class="pulse-node" /><path d="M 8 8 A 8 3 0 0 0 24 8 L 24 13 A 8 3 0 0 1 8 13 Z" fill="url(#db-body)" /><ellipse cx="16" cy="8" rx="8" ry="3" fill="url(#db-top)" /><path d="M 10 10.5 H 14" stroke="#ffffff" stroke-width="0.5" opacity="0.7" /><circle cx="21" cy="10.5" r="0.75" fill="#ffffff" class="pulse-node" /></svg>'
          ];

          foreach ($expJobs as $idx => $job):
              $isLeft = ($idx % 2 !== 0);
              $alignClass = $isLeft ? 'md:text-right' : 'md:ml-[calc(50%+2rem)]';
              $cardAlign = $isLeft ? '' : 'md:ml-0 md:w-[calc(50%-2rem)]';
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
            <div class="ml-16 md:ml-0 <?= $cardAlign ?> <?= $alignClass ?>">
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
    </section>`
);

// 13. Technology Stack Grid
content = content.replace(
  /<div\s+class="max-w-\[80%\]\s+mx-auto">[\s\S]*?<\/section>/i,
  `<div class="max-w-[80%] mx-auto">
          <?php foreach ($techCategories as $category): ?>
          <div class="flex flex-col pt-20 w-full items-center justify-center">
            <div class="text-2xl font-semibold">
              <?= h($category['name']) ?>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 w-full gap-6 pt-10 justify-center">
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
    </section>`
);

// 14. The Difference Section
content = content.replace(
  /<h2 class="text-sm font-semibold uppercase tracking-widest text-\[#1d4ed8\] mb-2 font-inter">THE DIFFERENCE\s*<\/h2>/i,
  '<h2 class="text-sm font-semibold uppercase tracking-widest text-[#1d4ed8] mb-2 font-inter"><?= h($diffSubheading) ?></h2>'
);

content = content.replace(
  /<p class="text-3xl md:text-5xl font-serif font-bold text-white mb-6">Why Work With Me\s*<\/p>/i,
  '<p class="text-3xl md:text-5xl font-serif font-bold text-white mb-6"><?= h($diffTitle) ?></p>'
);

content = content.replace(
  /<p class="text-white\/85 text-base md:text-lg leading-relaxed font-sans mt-2">[\s\S]*?<\/p>/i,
  `<p class="text-white/85 text-base md:text-lg leading-relaxed font-sans mt-2">
            <?= h($diffText1) ?>
          </p>`
);

content = content.replace(
  /<p class="text-white\/60 text-sm md:text-base leading-relaxed font-sans">[\s\S]*?<\/p>/i,
  `<p class="text-white/60 text-sm md:text-base leading-relaxed font-sans">
            <?= h($diffText2) ?>
          </p>`
);

content = content.replace(
  /<div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">[\s\S]*?<\/section>/i,
  `<div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">
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
    </section>`
);

// 15. CTA Section
content = content.replace(
  /READY TO BUILD A DATA-DRIVEN ORGANIZATION\?[\s\S]*?<\/span>/i,
  '<?= h($contactSubheading) ?></span>'
);

content = content.replace(
  /Schedule a Strategy Consultation[\s\S]*?<\/h2>/i,
  '<?= h($contactHeadline) ?></h2>'
);

content = content.replace(
  /<p class="text-white\/60 max-w-2xl mx-auto text-base leading-relaxed font-inter">[\s\S]*?<\/p>/i,
  `<p class="text-white/60 max-w-2xl mx-auto text-base leading-relaxed font-inter">
              <?= h($contactText) ?>
            </p>`
);

content = content.replace(
  /<!-- Link 1: Consultation -->\s*<a\s+href="#"/i,
  '<!-- Link 1: Consultation -->\n          <a href="<?= h($contactCalendly) ?>"'
);

content = content.replace(
  /<!-- Link 2: LinkedIn -->\s*<a\s+href="#"/i,
  '<!-- Link 2: LinkedIn -->\n          <a href="<?= h($contactLinkedin) ?>"'
);

content = content.replace(
  /<!-- Link 3: Send Enquiry -->\s*<a\s+href="#"/i,
  '<!-- Link 3: Send Enquiry -->\n          <a href="mailto:<?= h($contactEmail) ?>"'
);

// 16. Dynamic JS Entrypoint Script (Also inject window.HERO_TITLE)
content = content.replace(
  '<script type="module" src="/src/main.js"></script>',
  `<script>
    window.HERO_TITLE = <?= json_encode($heroTitle) ?>;
  </script>
  <?php if (!empty($jsPath)): ?>
    <script type="module" src="<?= h($jsPath) ?>"></script>
  <?php endif; ?>`
);

fs.writeFileSync(phpPath, content, 'utf8');
console.log('✅ index.php generated successfully!');
