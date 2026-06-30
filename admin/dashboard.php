<?php
session_start();

// Auth gate
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/api/db.php';

// Fetch theme settings
$db = getDB();
$themeResult = $db->query('SELECT setting_key, setting_value FROM theme_settings');
$theme = [];
while ($row = $themeResult->fetch_assoc()) {
    $theme[$row['setting_key']] = $row['setting_value'];
}

// Fetch all content
$contentResult = $db->query('SELECT section, content, updated_at FROM site_content');
$siteContent = [];
while ($row = $contentResult->fetch_assoc()) {
    $siteContent[$row['section']] = json_decode($row['content'], true);
}
$db->close();

$primary = $theme['primary_color'] ?? '#1d4ed8';
$accent = $theme['accent_color'] ?? '#60a5fa';
$bgColor = $theme['bg_color'] ?? '#060913';
$textColor = $theme['text_color'] ?? '#f3f4f6';
$adminUser = $_SESSION['admin_user'] ?? 'Admin';

function resolve_preview($path, $default = '') {
    if (empty($path)) return $default;
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) return $path;
    $clean = ltrim($path, '/');
    if (strpos($clean, '../') === 0) return $clean;
    
    // If path starts with assets/, it's in public/ or dist/
    if (strpos($clean, 'assets/') === 0) {
        if (is_dir(__DIR__ . '/../dist')) {
            return '../dist/' . $clean;
        } else {
            return '../public/' . $clean;
        }
    }
    return '../' . $clean;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Arun Kumar Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: <?= htmlspecialchars($primary) ?>;
            --accent: <?= htmlspecialchars($accent) ?>;
            --bg: #0a0e1a;
            --sidebar-bg: #060913;
            --surface: #0d1117;
            --surface2: #131928;
            --surface3: #1a2236;
            --border: rgba(255,255,255,0.06);
            --border-hover: rgba(255,255,255,0.12);
            --text: #f3f4f6;
            --muted: #6b7280;
            --muted2: #9ca3af;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --sidebar-width: 240px;
            --header-height: 64px;
        }

        html, body { height: 100%; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            display: flex;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ── SIDEBAR ─────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 11px;
        }
        .sidebar-logo {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 0 14px rgba(29,78,216,0.3);
        }
        .sidebar-brand-text { line-height: 1.2; }
        .sidebar-brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
        }
        .sidebar-brand-sub {
            font-size: 10px;
            color: var(--accent);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
            min-height: 0;
        }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
            padding: 8px 10px 6px;
            margin-top: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.18s ease;
            color: var(--muted2);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            position: relative;
            border: 1px solid transparent;
            margin-bottom: 2px;
            user-select: none;
        }
        .nav-item:hover {
            color: #fff;
            background: rgba(255,255,255,0.04);
            border-color: var(--border);
        }
        .nav-item.active {
            color: var(--accent);
            background: rgba(29,78,216,0.1);
            border-color: rgba(29,78,216,0.2);
        }
        .nav-item.active .nav-icon { color: var(--accent); }

        .nav-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            transition: all 0.18s;
        }
        .nav-item:hover .nav-icon, .nav-item.active .nav-icon {
            background: rgba(29,78,216,0.12);
            border-color: rgba(29,78,216,0.25);
        }
        .nav-icon svg { width: 16px; height: 16px; }

        .nav-badge {
            margin-left: auto;
            background: var(--primary);
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid var(--border);
        }

        /* ── MAIN AREA ───────────────────────────────── */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── HEADER ──────────────────────────────────── */
        .header {
            height: var(--header-height);
            background: rgba(6,9,19,0.85);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(12px);
        }
        .header-title {
            flex: 1;
        }
        .header-title h2 {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
        }
        .header-title p {
            font-size: 12px;
            color: var(--muted);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
            border: 1px solid transparent;
            font-family: 'Inter', sans-serif;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: #1e40af;
            box-shadow: 0 4px 16px rgba(29,78,216,0.3);
        }
        .btn-outline {
            background: transparent;
            color: var(--muted2);
            border-color: var(--border);
        }
        .btn-outline:hover {
            color: #fff;
            border-color: var(--border-hover);
            background: rgba(255,255,255,0.04);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: white;
            cursor: pointer;
            border: 2px solid rgba(255,255,255,0.1);
        }

        /* ── CONTENT AREA ────────────────────────────── */
        .content-area {
            flex: 1;
            padding: 28px;
        }

        /* ── PANELS ──────────────────────────────────── */
        .panel {
            display: none;
            animation: panelFadeIn 0.25s ease;
        }
        .panel.active { display: block; }

        @keyframes panelFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── CARDS ───────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            transition: border-color 0.2s;
        }
        .card:hover { border-color: var(--border-hover); }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
        }
        .card-subtitle {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        /* ── FORM ELEMENTS ───────────────────────────── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .form-row.single { grid-template-columns: 1fr; }
        .form-row.triple { grid-template-columns: 1fr 1fr 1fr; }

        .form-group { margin-bottom: 16px; }

        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 7px;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 10px 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.18s;
            resize: vertical;
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            border-color: rgba(29,78,216,0.5);
            background: rgba(29,78,216,0.04);
            box-shadow: 0 0 0 3px rgba(29,78,216,0.1);
        }
        .form-textarea { min-height: 100px; }

        /* ── COLOR PICKERS ───────────────────────────── */
        .color-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }
        .color-card {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .color-card:hover { border-color: var(--border-hover); }

        .color-preview {
            width: 100%;
            height: 60px;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .color-preview input[type="color"] {
            position: absolute;
            inset: -5px;
            width: calc(100% + 10px);
            height: calc(100% + 10px);
            border: none;
            cursor: pointer;
            opacity: 0;
        }
        .color-preview-swatch {
            width: 100%;
            height: 100%;
            border-radius: 8px;
            pointer-events: none;
        }

        .color-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin-bottom: 4px;
        }
        .color-value {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            font-family: monospace;
        }

        /* ── LIST EDITOR ─────────────────────────────── */
        .list-item {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            position: relative;
            transition: border-color 0.2s;
        }
        .list-item:hover { border-color: var(--border-hover); }

        .list-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .list-item-num {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--accent);
        }

        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.18s;
            font-family: 'Inter', sans-serif;
        }
        .btn-icon:hover {
            color: #ef4444;
            border-color: rgba(239,68,68,0.3);
            background: rgba(239,68,68,0.08);
        }
        .btn-icon svg { width: 14px; height: 14px; }

        .btn-add {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px dashed rgba(29,78,216,0.4);
            background: rgba(29,78,216,0.05);
            color: var(--accent);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
            font-family: 'Inter', sans-serif;
            width: 100%;
            justify-content: center;
            margin-top: 8px;
        }
        .btn-add:hover {
            border-color: rgba(29,78,216,0.6);
            background: rgba(29,78,216,0.1);
        }
        .btn-add svg { width: 15px; height: 15px; }

        /* ── TOAST ───────────────────────────────────── */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 260px;
            animation: toastIn 0.3s ease;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .toast.success {
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.25);
            color: #34d399;
        }
        .toast.error {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.25);
            color: #f87171;
        }
        .toast.out { animation: toastOut 0.3s ease forwards; }
        @keyframes toastIn { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
        @keyframes toastOut { to{opacity:0;transform:translateX(20px)} }

        /* ── STATS GRID (Dashboard) ──────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            transition: all 0.2s;
        }
        .stat-card:hover { border-color: var(--border-hover); transform: translateY(-2px); }
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-value { font-size: 24px; font-weight: 700; color: #fff; }
        .stat-label { font-size: 12px; color: var(--muted); margin-top: 3px; }

        /* ── SAVE BAR ────────────────────────────────── */
        .save-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 20px;
        }
        .save-bar-info { font-size: 13px; color: var(--muted); }
        .save-bar-info strong { color: var(--accent); }

        /* ── PREVIEW LINK ────────────────────────────── */
        .view-site-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        .view-site-link:hover { color: var(--accent); }

        /* ── THEME PREVIEW ───────────────────────────── */
        .theme-preview-bar {
            height: 8px;
            border-radius: 8px;
            background: linear-gradient(to right,
                var(--preview-primary, #1d4ed8) 0%,
                var(--preview-accent, #60a5fa) 50%,
                var(--preview-bg, #060913) 100%
            );
            margin-top: 12px;
            border: 1px solid var(--border);
        }

        .mobile-toggle-btn {
            display: none;
            background: none;
            border: none;
            color: var(--text);
            cursor: pointer;
            padding: 8px;
            margin-right: 12px;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        .mobile-toggle-btn:hover {
            background: var(--surface2);
            color: var(--accent);
        }

        /* ── RESPONSIVE ──────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); z-index: 999; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; padding: 16px; }
            .header { display: flex; align-items: center; }
            .mobile-toggle-btn { display: flex; }
            .form-row { grid-template-columns: 1fr !important; }
            .form-row.triple { grid-template-columns: 1fr !important; }
        }

        /* scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 5px; }
    </style>
</head>
<body>

    <!-- ── SIDEBAR ─────────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-logo">JAK</div>
                <div class="sidebar-brand-text">
                    <div class="sidebar-brand-name">Portfolio CMS</div>
                    <div class="sidebar-brand-sub">Admin Panel</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Overview</div>

            <div class="nav-item active" data-panel="dashboard" onclick="switchPanel('dashboard', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                Dashboard
            </div>

            <div class="nav-section-label">Website Content</div>

            <div class="nav-item" data-panel="hero" onclick="switchPanel('hero', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3l14 9-14 9V3z"/></svg>
                </div>
                Hero Section
            </div>

            <div class="nav-item" data-panel="expertise" onclick="switchPanel('expertise', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                Core Verticals
            </div>

            <div class="nav-item" data-panel="opportunity" onclick="switchPanel('opportunity', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                Opportunity
            </div>

            <div class="nav-item" data-panel="about" onclick="switchPanel('about', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                About Bio
            </div>

            <div class="nav-item" data-panel="capabilities" onclick="switchPanel('capabilities', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                Capabilities Timeline
            </div>

            <div class="nav-item" data-panel="services" onclick="switchPanel('services', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                Services
            </div>

            <div class="nav-item" data-panel="clients" onclick="switchPanel('clients', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                Ideal Clients
            </div>

            <div class="nav-item" data-panel="impact" onclick="switchPanel('impact', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                Delivered Impact
            </div>

            <div class="nav-item" data-panel="experience" onclick="switchPanel('experience', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                Experience
            </div>

            <div class="nav-item" data-panel="technology" onclick="switchPanel('technology', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z"/></svg>
                </div>
                Technology
            </div>

            <div class="nav-item" data-panel="difference" onclick="switchPanel('difference', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                The Difference
            </div>

            <div class="nav-item" data-panel="contact" onclick="switchPanel('contact', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                Contact
            </div>

            <div class="nav-section-label">Customization</div>

            <div class="nav-item" data-panel="theme" onclick="switchPanel('theme', this)" style="display: none;">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                </div>
                Theme Colors
            </div>

            <div class="nav-item" data-panel="meta" onclick="switchPanel('meta', this)">
                <div class="nav-icon">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                Site Settings
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php" class="nav-item" style="color:#ef4444; border-color:rgba(239,68,68,0.15)">
                <div class="nav-icon" style="background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.2)">
                    <svg fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                Logout
            </a>
        </div>
    </aside>

    <!-- ── MAIN ─────────────────────────────────────── -->
    <div class="main-wrapper">

        <!-- Header -->
        <header class="header">
            <!-- Mobile Toggle Menu Button -->
            <button class="mobile-toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Navigation">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="header-title">
                <h2 id="pageTitle">Dashboard</h2>
                <p id="pageSub">Welcome back, <strong><?= htmlspecialchars($adminUser) ?></strong></p>
            </div>
            <div class="header-actions">
                <a href="../index.php" target="_blank" class="header-btn btn-outline">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Site
                </a>
                <button class="header-btn btn-primary" onclick="saveCurrentSection()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Save Changes
                </button>
                <div class="user-avatar" title="<?= htmlspecialchars($adminUser) ?>"><?= strtoupper(substr($adminUser, 0, 2)) ?></div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-area">

            <!-- ── DASHBOARD PANEL ─────────────────────── -->
            <div class="panel active" id="panel-dashboard">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(29,78,216,0.1); border:1px solid rgba(29,78,216,0.2)">
                            <svg fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="stat-value">8</div>
                        <div class="stat-label">Content Sections</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2)">
                            <svg fill="none" stroke="#34d399" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        </div>
                        <div class="stat-value">7</div>
                        <div class="stat-label">Theme Settings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2)">
                            <svg fill="none" stroke="#fbbf24" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div class="stat-value">Live</div>
                        <div class="stat-label">Site Status</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(139,92,246,0.1); border:1px solid rgba(139,92,246,0.2)">
                            <svg fill="none" stroke="#a78bfa" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div class="stat-value">Secure</div>
                        <div class="stat-label">Auth Status</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Quick Actions</div>
                            <div class="card-subtitle">Click any section to edit</div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px;">
                        <?php
                        $quickLinks = [
                            ['panel'=>'hero','label'=>'Hero','icon'=>'play','color'=>'#60a5fa'],
                            ['panel'=>'about','label'=>'About','icon'=>'user','color'=>'#34d399'],
                            ['panel'=>'services','label'=>'Services','icon'=>'brief','color'=>'#fbbf24'],
                            ['panel'=>'experience','label'=>'Experience','icon'=>'school','color'=>'#a78bfa'],
                            ['panel'=>'technology','label'=>'Technology','icon'=>'chip','color'=>'#f472b6'],
                            // ['panel'=>'theme','label'=>'Theme','icon'=>'palette','color'=>'#fb923c'],
                        ];
                        foreach ($quickLinks as $link): ?>
                        <button onclick="switchPanel('<?= $link['panel'] ?>', null)" style="
                            background:var(--surface2);
                            border:1px solid var(--border);
                            border-radius:12px;
                            padding:16px;
                            cursor:pointer;
                            transition:all 0.18s;
                            text-align:left;
                            color:var(--text);
                            font-family:'Inter',sans-serif;
                        " onmouseover="this.style.borderColor='var(--border-hover)'" onmouseout="this.style.borderColor='var(--border)'">
                            <div style="font-size:22px;margin-bottom:8px;"><?php
                                $emojis=['play'=>'▶','user'=>'👤','brief'=>'💼','school'=>'🎓','chip'=>'⚙️','palette'=>'🎨'];
                                echo $emojis[$link['icon']] ?? '📄';
                            ?></div>
                            <div style="font-size:13px; font-weight:600; color:#fff;"><?= $link['label'] ?></div>
                            <div style="font-size:11px; color:var(--muted); margin-top:2px;">Edit content →</div>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Current Theme Preview</div>
                    </div>
                    <div style="display:flex; gap:16px; flex-wrap:wrap; align-items:center;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:32px;height:32px;border-radius:8px;background:<?= htmlspecialchars($primary) ?>;border:1px solid rgba(255,255,255,0.1)"></div>
                            <div>
                                <div style="font-size:11px;color:var(--muted)">Primary</div>
                                <div style="font-size:13px;font-weight:600;font-family:monospace"><?= htmlspecialchars($primary) ?></div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:32px;height:32px;border-radius:8px;background:<?= htmlspecialchars($accent) ?>;border:1px solid rgba(255,255,255,0.1)"></div>
                            <div>
                                <div style="font-size:11px;color:var(--muted)">Accent</div>
                                <div style="font-size:13px;font-weight:600;font-family:monospace"><?= htmlspecialchars($accent) ?></div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:32px;height:32px;border-radius:8px;background:<?= htmlspecialchars($bgColor) ?>;border:1px solid rgba(255,255,255,0.1)"></div>
                            <div>
                                <div style="font-size:11px;color:var(--muted)">Background</div>
                                <div style="font-size:13px;font-weight:600;font-family:monospace"><?= htmlspecialchars($bgColor) ?></div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:32px;height:32px;border-radius:8px;background:<?= htmlspecialchars($textColor) ?>;border:1px solid rgba(255,255,255,0.1)"></div>
                            <div>
                                <div style="font-size:11px;color:var(--muted)">Text</div>
                                <div style="font-size:13px;font-weight:600;font-family:monospace"><?= htmlspecialchars($textColor) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── HERO PANEL ──────────────────────────── -->
            <div class="panel" id="panel-hero">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Hero Section</strong> — Changes will update the main landing section</div>
                </div>
                <?php $hero = $siteContent['hero'] ?? []; ?>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Hero Content</div>
                            <div class="card-subtitle">Main landing section</div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input class="form-input" id="hero_name" type="text" value="<?= htmlspecialchars($hero['name'] ?? 'Arun Kumar Jayakumar') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Professional Title</label>
                            <input class="form-input" id="hero_title" type="text" value="<?= htmlspecialchars($hero['title'] ?? 'Fractional CDO & Enterprise Data Strategist') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subtitle / Tagline</label>
                        <input class="form-input" id="hero_subtitle" type="text" value="<?= htmlspecialchars($hero['subtitle'] ?? 'Transforming Data into Strategic Advantage') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-textarea" id="hero_description"><?= htmlspecialchars($hero['description'] ?? '14+ years leading high-stakes data and AI transformations') ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">CTA Button Text</label>
                            <input class="form-input" id="hero_cta_text" type="text" value="<?= htmlspecialchars($hero['cta_text'] ?? 'Book a Strategy Call') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CTA Button URL</label>
                            <input class="form-input" id="hero_cta_url" type="text" value="<?= htmlspecialchars($hero['cta_url'] ?? '#contact') ?>">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Hero Background Image</div></div>
                    <div class="form-group">
                        <label class="form-label">Background Image (optional)</label>
                        <div style="display:flex; align-items:center; gap:16px;">
                            <img id="hero_bg_preview" src="<?= htmlspecialchars(resolve_preview($hero['bg_image'] ?? '')) ?>" style="width:120px; height:60px; object-fit:cover; border-radius:8px; border:1px solid var(--border); <?= empty($hero['bg_image']) ? 'display:none' : '' ?>" onerror="this.style.display='none'">
                            <div style="display:flex; flex-direction:column; gap:8px; flex: 1;">
                                <input type="file" id="hero_bg_file" style="display:none" accept="image/*" onchange="uploadImage(this, 'hero_bg_preview', 'hero_bg_image')">
                                <button type="button" class="header-btn btn-outline" style="align-self: flex-start;" onclick="document.getElementById('hero_bg_file').click()">Upload Image</button>
                                <input class="form-input" id="hero_bg_image" type="text" value="<?= htmlspecialchars($hero['bg_image'] ?? '') ?>" placeholder="/assets/images/uploads/hero_bg.jpg">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Role / Tagline Pills</div>
                            <div class="card-subtitle">Displayed below the name</div>
                        </div>
                    </div>
                    <div id="hero_taglines">
                        <?php foreach (($hero['taglines'] ?? ['Data Strategist','AI Advisor','Enterprise Transformation Leader']) as $i => $tag): ?>
                        <div class="list-item" data-idx="<?= $i ?>">
                            <div class="list-item-header">
                                <span class="list-item-num">Pill <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="removeTagline(this)">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <input class="form-input" type="text" value="<?= htmlspecialchars($tag) ?>" placeholder="Tagline text">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addTagline()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Pill
                    </button>
                </div>
            </div>

            <!-- ── ABOUT PANEL ─────────────────────────── -->
            <div class="panel" id="panel-about">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>About Section</strong></div>
                </div>
                <?php $about = $siteContent['about'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">About Content</div></div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Section Headline</label>
                            <input class="form-input" id="about_headline" type="text" value="<?= htmlspecialchars($about['headline'] ?? 'About Me') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subheading</label>
                            <input class="form-input" id="about_subheading" type="text" value="<?= htmlspecialchars($about['subheading'] ?? 'Enterprise Data Leader') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bio / Description</label>
                        <textarea class="form-textarea" id="about_bio" style="min-height:140px"><?= htmlspecialchars($about['bio'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Biography Quote</label>
                        <input class="form-input" id="about_quote" type="text" value="<?= htmlspecialchars($about['quote'] ?? '') ?>" placeholder="Quote text">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Image</label>
                        <div style="display:flex; align-items:center; gap:16px;">
                            <img id="about_image_preview" src="<?= htmlspecialchars(resolve_preview($about['image'] ?? '', '/assets/images/arun_kumar.png')) ?>" style="width:80px; height:80px; object-fit:cover; border-radius:12px; border:1px solid var(--border)">
                            <div style="display:flex; flex-direction:column; gap:8px; flex: 1;">
                                <input type="file" id="about_image_file" style="display:none" onchange="uploadAboutImage(this)">
                                <button type="button" class="header-btn btn-outline" style="align-self: flex-start;" onclick="document.getElementById('about_image_file').click()">Upload Image</button>
                                <input class="form-input" id="about_image" type="text" value="<?= htmlspecialchars($about['image'] ?? '/assets/images/arun_kumar.png') ?>" placeholder="/assets/images/arun_kumar.png">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Stats / Numbers</div>
                    </div>
                    <div id="about_stats">
                        <?php foreach (($about['stats'] ?? [['value'=>'14+','label'=>'Years Experience'],['value'=>'50+','label'=>'Transformations Led']]) as $i => $stat): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Stat <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-row">
                                <div class="form-group" style="margin:0">
                                    <label class="form-label">Value</label>
                                    <input class="form-input stat-value-input" type="text" value="<?= htmlspecialchars($stat['value'] ?? '') ?>" placeholder="14+">
                                </div>
                                <div class="form-group" style="margin:0">
                                    <label class="form-label">Label</label>
                                    <input class="form-input stat-label-input" type="text" value="<?= htmlspecialchars($stat['label'] ?? '') ?>" placeholder="Years Experience">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addStat()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Stat
                    </button>
                </div>
            </div>

            <!-- ── SERVICES PANEL ──────────────────────── -->
            <div class="panel" id="panel-services">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Services Section</strong></div>
                </div>
                <?php $services = $siteContent['services'] ?? []; ?>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Section Header</div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Headline</label>
                            <input class="form-input" id="services_headline" type="text" value="<?= htmlspecialchars($services['headline'] ?? 'Services') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subheading</label>
                            <input class="form-input" id="services_subheading" type="text" value="<?= htmlspecialchars($services['subheading'] ?? 'What I Offer') ?>">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Service Cards</div>
                    </div>
                    <div id="services_items">
                        <?php foreach (($services['items'] ?? []) as $i => $item): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Service <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Title</label>
                                <input class="form-input service-title" type="text" value="<?= htmlspecialchars($item['title'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Subtitle</label>
                                <input class="form-input service-subtitle" type="text" value="<?= htmlspecialchars($item['subtitle'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea class="form-textarea service-desc"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Bullets (one per line)</label>
                                <textarea class="form-textarea service-bullets"><?= htmlspecialchars(implode("\n", $item['bullets'] ?? [])) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Custom Icon Image (optional — overrides default SVG)</label>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <img class="svc-icon-preview" src="<?= htmlspecialchars(resolve_preview($item['icon_image'] ?? '')) ?>" style="width:48px; height:48px; object-fit:contain; border-radius:8px; border:1px solid var(--border); <?= empty($item['icon_image']) ? 'display:none' : '' ?>" onerror="this.style.display='none'">
                                    <div style="display:flex; flex-direction:column; gap:6px; flex:1;">
                                        <input type="file" class="svc-icon-file" style="display:none" accept="image/*" onchange="uploadImageInline(this)">
                                        <button type="button" class="header-btn btn-outline" style="align-self:flex-start; font-size:11px; padding:4px 12px;" onclick="this.closest('.form-group').querySelector('.svc-icon-file').click()">Upload Icon</button>
                                        <input class="form-input service-icon-image" type="text" value="<?= htmlspecialchars($item['icon_image'] ?? '') ?>" placeholder="Leave empty for default SVG icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addServiceItem()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Service
                    </button>
                </div>
            </div>

            <!-- ── EXPERIENCE PANEL ────────────────────── -->
            <div class="panel" id="panel-experience">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Experience Section</strong></div>
                </div>
                <?php $exp = $siteContent['experience'] ?? []; ?>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Section Header</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Headline</label>
                            <input class="form-input" id="exp_headline" type="text" value="<?= htmlspecialchars($exp['headline'] ?? 'Experience') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subheading</label>
                            <input class="form-input" id="exp_subheading" type="text" value="<?= htmlspecialchars($exp['subheading'] ?? 'Career Journey') ?>">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Jobs / Roles</div></div>
                    <div id="experience_jobs">
                        <?php foreach (($exp['jobs'] ?? []) as $i => $job): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Role <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Job Title</label>
                                    <input class="form-input job-title" type="text" value="<?= htmlspecialchars($job['title'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Company</label>
                                    <input class="form-input job-company" type="text" value="<?= htmlspecialchars($job['company'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Period</label>
                                    <input class="form-input job-period" type="text" value="<?= htmlspecialchars($job['period'] ?? '') ?>" placeholder="2021 – Present">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Location</label>
                                    <input class="form-input job-location" type="text" value="<?= htmlspecialchars($job['location'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Key Points (one per line)</label>
                                <textarea class="form-textarea job-bullets"><?= htmlspecialchars(implode("\n", $job['bullets'] ?? [])) ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addJobItem()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Role
                    </button>
                </div>
            </div>

            <!-- ── TECHNOLOGY PANEL ────────────────────── -->
            <div class="panel" id="panel-technology">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Technology Section</strong></div>
                </div>
                <?php $tech = $siteContent['technology'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Section Header</div></div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Headline</label>
                            <input class="form-input" id="tech_headline" type="text" value="<?= htmlspecialchars($tech['headline'] ?? 'Technology') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subheading</label>
                            <input class="form-input" id="tech_subheading" type="text" value="<?= htmlspecialchars($tech['subheading'] ?? 'Tech Stack') ?>">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Tech Categories</div></div>
                    <div id="tech_categories">
                        <?php foreach (($tech['categories'] ?? []) as $i => $cat): ?>
                        <div class="list-item tech-category-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Category <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Category Name</label>
                                <input class="form-input cat-name" type="text" value="<?= htmlspecialchars($cat['name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="margin-bottom:8px">Technology Items</label>
                                <div class="tech-items-list" style="display:flex; flex-direction:column; gap:10px;">
                                    <?php foreach (($cat['items'] ?? []) as $j => $it):
                                        $itName = is_array($it) ? ($it['name'] ?? '') : $it;
                                        $itImage = is_array($it) ? ($it['image'] ?? '') : '';
                                    ?>
                                    <div class="tech-item-row" style="display:flex; align-items:center; gap:10px; padding:8px; border:1px solid var(--border); border-radius:8px; background:rgba(255,255,255,0.02);">
                                        <img class="tech-item-preview" src="<?= htmlspecialchars(resolve_preview($itImage)) ?>" style="width:40px; height:40px; object-fit:contain; border-radius:6px; border:1px solid var(--border); flex-shrink:0; <?= empty($itImage) ? 'display:none' : '' ?>" onerror="this.style.display='none'">
                                        <input class="form-input tech-item-name" type="text" value="<?= htmlspecialchars($itName) ?>" placeholder="Technology name" style="flex:1;">
                                        <input class="form-input tech-item-image" type="text" value="<?= htmlspecialchars($itImage) ?>" placeholder="Image path" style="flex:1; font-size:11px;">
                                        <input type="file" class="tech-item-file" style="display:none" accept="image/*" onchange="uploadImageInline(this)">
                                        <button type="button" class="header-btn btn-outline" style="font-size:10px; padding:4px 10px; flex-shrink:0;" onclick="this.closest('.tech-item-row').querySelector('.tech-item-file').click()" title="Upload icon">
                                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </button>
                                        <button type="button" class="btn-icon" style="flex-shrink:0" onclick="this.closest('.tech-item-row').remove()">
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn-add" style="margin-top:8px;" onclick="addTechItem(this.closest('.form-group').querySelector('.tech-items-list'))">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Add Item
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addTechCategory()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Category
                    </button>
                </div>
            </div>

            <!-- ── EXPERTISE PANEL ─────────────────────── -->
            <div class="panel" id="panel-expertise">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Expertise / Core Verticals Section</strong></div>
                </div>
                <?php $exp2 = $siteContent['expertise'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Section Header</div></div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Headline</label>
                            <input class="form-input" id="expertise_headline" type="text" value="<?= htmlspecialchars($exp2['headline'] ?? 'Core Verticals') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Subheading</label>
                            <input class="form-input" id="expertise_subheading" type="text" value="<?= htmlspecialchars($exp2['subheading'] ?? 'Strategic Domain Expertise') ?>">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Marquee Items</div></div>
                    <div id="expertise_items">
                        <?php foreach (($exp2['items'] ?? ['Manufacturing','Supply Chain','Logistics SaaS','Public Sector','Enterprise Tech']) as $i => $item): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Item <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <input class="form-input expertise-item" type="text" value="<?= htmlspecialchars($item) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addExpertiseItem()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Item
                    </button>
                </div>
            </div>

            <!-- ── CONTACT PANEL ───────────────────────── -->
            <div class="panel" id="panel-contact">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Contact Section</strong></div>
                </div>
                <?php $contact = $siteContent['contact'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Contact Information</div></div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Section Headline</label>
                            <input class="form-input" id="contact_headline" type="text" value="<?= htmlspecialchars($contact['headline'] ?? 'Get In Touch') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input class="form-input" id="contact_email" type="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">LinkedIn URL</label>
                        <input class="form-input" id="contact_linkedin" type="url" value="<?= htmlspecialchars($contact['linkedin'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- ── THEME PANEL ─────────────────────────── -->
            <div class="panel" id="panel-theme">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Theme Colors</strong> — Changes apply site-wide instantly</div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">🎨 Color Palette</div>
                            <div class="card-subtitle">Click any color block to open the color picker</div>
                        </div>
                    </div>
                    <div class="color-grid">
                        <?php
                        $colorSettings = [
                            ['key'=>'primary_color','label'=>'Primary Color','desc'=>'Buttons, links, highlights'],
                            ['key'=>'accent_color','label'=>'Accent Color','desc'=>'Glow, hover effects'],
                            ['key'=>'bg_color','label'=>'Background Color','desc'=>'Site background'],
                            ['key'=>'text_color','label'=>'Text Color','desc'=>'Body text color'],
                            ['key'=>'sidebar_bg','label'=>'Sidebar Background','desc'=>'Navigation sidebar'],
                            ['key'=>'surface_color','label'=>'Surface Color','desc'=>'Card & panel backgrounds'],
                            ['key'=>'hover_color','label'=>'Hover Color','desc'=>'Hover state backgrounds'],
                            ['key'=>'border_color','label'=>'Border Color','desc'=>'Borders & dividers'],
                        ];
                        foreach ($colorSettings as $cs):
                            $val = $theme[$cs['key']] ?? '#ffffff';
                            // Only show color picker for hex colors
                            $isHex = preg_match('/^#[0-9a-fA-F]{3,8}$/', $val);
                        ?>
                        <div class="color-card">
                            <div class="color-preview" onclick="document.getElementById('cp_<?= $cs['key'] ?>').click()">
                                <?php if ($isHex): ?>
                                <div class="color-preview-swatch" id="swatch_<?= $cs['key'] ?>" style="background:<?= htmlspecialchars($val) ?>"></div>
                                <input type="color" id="cp_<?= $cs['key'] ?>" value="<?= htmlspecialchars($val) ?>"
                                    oninput="updateColorFromPicker(this,'<?= $cs['key'] ?>')"
                                    onchange="updateColorFromPicker(this,'<?= $cs['key'] ?>')">
                                <?php else: ?>
                                <div class="color-preview-swatch" style="background:<?= htmlspecialchars($val) ?>; display:flex;align-items:center;justify-content:center;font-size:10px;color:#888">rgba</div>
                                <?php endif; ?>
                            </div>
                            <div class="color-label"><?= $cs['label'] ?></div>
                            <div style="margin-top: 4px; display: flex; gap: 6px; align-items: center; margin-bottom: 6px;">
                                <input type="text" class="form-input" id="ci_<?= $cs['key'] ?>" value="<?= htmlspecialchars($val) ?>"
                                    style="font-family: monospace; font-size: 13px; padding: 6px 8px; height: 32px; width: 100%;"
                                    oninput="updateColorFromInput(this,'<?= $cs['key'] ?>')">
                            </div>
                            <div style="font-size:10px;color:var(--muted);margin-top:3px"><?= $cs['desc'] ?></div>
                            <input type="hidden" id="theme_<?= $cs['key'] ?>" value="<?= htmlspecialchars($val) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card" id="theme-preview-card">
                    <div class="card-header"><div class="card-title">Live Preview</div></div>
                    <div id="theme-live-preview" style="
                        padding: 20px;
                        border-radius: 12px;
                        background: <?= htmlspecialchars($bgColor) ?>;
                        border: 1px solid rgba(255,255,255,0.1);
                        font-family: 'Inter', sans-serif;
                    ">
                        <div style="font-size: 24px; font-weight: 700; color: <?= htmlspecialchars($textColor) ?>; margin-bottom: 8px;">Arun Kumar Jayakumar</div>
                        <div style="font-size: 14px; color: <?= htmlspecialchars($accent) ?>; margin-bottom: 16px;">Fractional CDO & Enterprise Data Strategist</div>
                        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
                            <span style="padding:6px 14px;border-radius:20px;background:<?= htmlspecialchars($primary) ?>;color:white;font-size:12px;font-weight:600;">Book a Call</span>
                            <span style="padding:6px 14px;border-radius:20px;background:transparent;border:1px solid <?= htmlspecialchars($primary) ?>;color:<?= htmlspecialchars($accent) ?>;font-size:12px;">View Work</span>
                        </div>
                        <div style="height:1px;background:rgba(255,255,255,0.08);margin-bottom:16px;"></div>
                        <div style="font-size:13px;color:rgba(255,255,255,0.5);">Data Strategist · AI Advisor · Enterprise Transformation Leader</div>
                    </div>
                </div>
            </div>

            <!-- ── OPPORTUNITY PANEL ───────────────────── -->
            <div class="panel" id="panel-opportunity">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Opportunity Section</strong></div>
                </div>
                <?php $opp = $siteContent['opportunity'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Opportunity Headings</div></div>
                    <div class="form-group">
                        <label class="form-label">Section Title</label>
                        <input class="form-input" id="opp_title" type="text" value="<?= htmlspecialchars($opp['title'] ?? '') ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Friction Title</label>
                            <input class="form-input" id="opp_friction_title" type="text" value="<?= htmlspecialchars($opp['friction_title'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CDO Solution Title</label>
                            <input class="form-input" id="opp_solution_title" type="text" value="<?= htmlspecialchars($opp['solution_title'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Friction Description</label>
                        <textarea class="form-textarea" id="opp_friction_text"><?= htmlspecialchars($opp['friction_text'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Friction Quote</label>
                        <input class="form-input" id="opp_quote" type="text" value="<?= htmlspecialchars($opp['quote'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CDO Solution Description</label>
                        <textarea class="form-textarea" id="opp_solution_text"><?= htmlspecialchars($opp['solution_text'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bottom Quote</label>
                        <textarea class="form-textarea" id="opp_bottom_quote"><?= htmlspecialchars($opp['bottom_quote'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Solution Pillars</div></div>
                    <div id="opp_pillars">
                        <?php foreach (($opp['pillars'] ?? []) as $i => $pillar): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Pillar <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <input class="form-input opp-pillar-input" type="text" value="<?= htmlspecialchars($pillar) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addOppPillar()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Pillar
                    </button>
                </div>
            </div>

            <!-- ── CAPABILITIES PANEL ──────────────────── -->
            <div class="panel" id="panel-capabilities">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Capabilities Timeline</strong></div>
                </div>
                <?php $caps = $siteContent['capabilities'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Capabilities Timeline Items</div></div>
                    <div id="capabilities_items">
                        <?php foreach (($caps['items'] ?? []) as $i => $item): ?>
                        <div class="list-item" data-type="<?= htmlspecialchars($item['type'] ?? 'text') ?>">
                            <div class="list-item-header">
                                <span class="list-item-num">Timeline Item <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Category Name</label>
                                    <input class="form-input cap-category" type="text" value="<?= htmlspecialchars($item['category'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Item Type</label>
                                    <select class="form-select cap-type" onchange="toggleCapType(this)">
                                        <option value="text" <?= (!isset($item['type']) || $item['type'] === 'text') ? 'selected' : '' ?>>Description Text</option>
                                        <option value="pills" <?= (isset($item['type']) && $item['type'] === 'pills') ? 'selected' : '' ?>>Pill List (Comma Separated)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group cap-text-group" style="display: <?= (!isset($item['type']) || $item['type'] === 'text') ? 'block' : 'none' ?>">
                                <label class="form-label">Description Text</label>
                                <textarea class="form-textarea cap-description"><?= htmlspecialchars($item['description'] ?? $item['text'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group cap-pills-group" style="display: <?= (isset($item['type']) && $item['type'] === 'pills') ? 'block' : 'none' ?>">
                                <label class="form-label">Pills (comma-separated)</label>
                                <input class="form-input cap-items" type="text" value="<?= htmlspecialchars(implode(', ', $item['items'] ?? [])) ?>">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addCapabilityItem()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Timeline Item
                    </button>
                </div>
            </div>

            <!-- ── IDEAL CLIENTS PANEL ─────────────────── -->
            <div class="panel" id="panel-clients">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Ideal Clients Section</strong></div>
                </div>
                <?php $cl = $siteContent['clients'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Intro Paragraph</div></div>
                    <div class="form-group">
                        <label class="form-label">Introduction Text</label>
                        <textarea class="form-textarea" id="clients_intro"><?= htmlspecialchars($cl['intro'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Client Tabs & Panels</div></div>
                    <div id="clients_tabs">
                        <?php foreach (($cl['tabs'] ?? []) as $i => $tab): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Tab <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Tab Identifier (e.g. ceos-founders)</label>
                                    <input class="form-input client-tab-id" type="text" value="<?= htmlspecialchars($tab['id'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Tab Number Prefixed (e.g. 01 //)</label>
                                    <input class="form-input client-tab-num" type="text" value="<?= htmlspecialchars($tab['num'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tab / Panel Title</label>
                                <input class="form-input client-tab-title" type="text" value="<?= htmlspecialchars($tab['title'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Showcase Content Description</label>
                                <textarea class="form-textarea client-tab-text"><?= htmlspecialchars($tab['text'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addClientTab()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Client Tab
                    </button>
                </div>
            </div>

            <!-- ── DELIVERED IMPACT PANEL ──────────────── -->
            <div class="panel" id="panel-impact">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Delivered Impact / Results</strong></div>
                </div>
                <?php $imp = $siteContent['impact'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Impact Swiper Cards</div></div>
                    <div id="impact_slides">
                        <?php foreach (($imp['slides'] ?? []) as $i => $slide): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Slide <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Card Number (e.g. 01)</label>
                                    <input class="form-input impact-num" type="text" value="<?= htmlspecialchars($slide['num'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Card Title</label>
                                    <input class="form-input impact-title" type="text" value="<?= htmlspecialchars($slide['title'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Card Description</label>
                                <textarea class="form-textarea impact-description"><?= htmlspecialchars($slide['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addImpactSlide()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Impact Slide
                    </button>
                </div>
            </div>

            <!-- ── THE DIFFERENCE PANEL ────────────────── -->
            <div class="panel" id="panel-difference">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>The Difference Section</strong></div>
                </div>
                <?php $diff = $siteContent['difference'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">Difference Headings</div></div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Subheading (e.g. THE DIFFERENCE)</label>
                            <input class="form-input" id="diff_subheading" type="text" value="<?= htmlspecialchars($diff['subheading'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Headline (e.g. Why Work With Me)</label>
                            <input class="form-input" id="diff_title" type="text" value="<?= htmlspecialchars($diff['title'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Introduction Text Line 1</label>
                        <input class="form-input" id="diff_text1" type="text" value="<?= htmlspecialchars($diff['text1'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Introduction Text Line 2</label>
                        <input class="form-input" id="diff_text2" type="text" value="<?= htmlspecialchars($diff['text2'] ?? '') ?>">
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><div class="card-title">Value Cards</div></div>
                    <div id="diff_cards">
                        <?php foreach (($diff['cards'] ?? []) as $i => $card): ?>
                        <div class="list-item">
                            <div class="list-item-header">
                                <span class="list-item-num">Card <?= $i+1 ?></span>
                                <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Card Title</label>
                                <input class="form-input diff-card-title" type="text" value="<?= htmlspecialchars($card['title'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Card Description</label>
                                <textarea class="form-textarea diff-card-text"><?= htmlspecialchars($card['text'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="btn-add" onclick="addDiffCard()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Value Card
                    </button>
                </div>
            </div>

            <!-- ── META / SETTINGS PANEL ───────────────── -->
            <div class="panel" id="panel-meta">
                <div class="save-bar">
                    <div class="save-bar-info">Editing: <strong>Site Settings & SEO</strong></div>
                </div>
                <?php $meta = $siteContent['meta'] ?? []; ?>
                <div class="card">
                    <div class="card-header"><div class="card-title">SEO & Meta Tags</div></div>
                    <div class="form-group">
                        <label class="form-label">Page Title</label>
                        <input class="form-input" id="meta_title" type="text" value="<?= htmlspecialchars($meta['title'] ?? 'Arun Kumar Jayakumar | Fractional CDO & Enterprise Data Strategist') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-textarea" id="meta_description"><?= htmlspecialchars($meta['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keywords (comma-separated)</label>
                        <input class="form-input" id="meta_keywords" type="text" value="<?= htmlspecialchars($meta['keywords'] ?? '') ?>">
                    </div>
                </div>
            </div>

        </div><!-- /.content-area -->
    </div><!-- /.main-wrapper -->

    <!-- Toast container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ── Embedded PHP data ─────────────────────────────
        const SITE_CONTENT = <?= json_encode($siteContent, JSON_HEX_TAG | JSON_HEX_QUOT) ?>;
        const THEME = <?= json_encode($theme, JSON_HEX_TAG | JSON_HEX_QUOT) ?>;

        // ── Panel Switcher ────────────────────────────────
        const pageTitles = {
            dashboard: {title:'Dashboard', sub:'Overview of your portfolio CMS'},
            hero: {title:'Hero Section', sub:'Edit the main landing banner'},
            about: {title:'About', sub:'Edit your about section'},
            services: {title:'Services', sub:'Edit your service offerings'},
            experience: {title:'Experience', sub:'Edit your career history'},
            technology: {title:'Technology', sub:'Edit your tech stack'},
            expertise: {title:'Expertise', sub:'Edit your core verticals'},
            contact: {title:'Contact', sub:'Edit contact information'},
            theme: {title:'Theme Colors', sub:'Customize site colors and branding'},
            meta: {title:'Site Settings', sub:'SEO and meta configuration'},
            opportunity: {title:'Opportunity', sub:'Edit opportunity data strategy content'},
            capabilities: {title:'Capabilities Timeline', sub:'Edit capability timeline stages'},
            clients: {title:'Ideal Clients', sub:'Edit client profile tabs'},
            impact: {title:'Delivered Impact', sub:'Edit results slider content'},
            difference: {title:'The Difference', sub:'Edit what sets you apart'},
        };

        let currentPanel = 'dashboard';

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) sidebar.classList.toggle('open');
        }

        function switchPanel(panelId, navEl) {
            // Hide all panels
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            // Show target
            const target = document.getElementById('panel-' + panelId);
            if (target) target.classList.add('active');
            currentPanel = panelId;

            // Update nav items
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            if (navEl) {
                navEl.classList.add('active');
            } else {
                // Find by data-panel
                const found = document.querySelector(`.nav-item[data-panel="${panelId}"]`);
                if (found) found.classList.add('active');
            }

            // Update header
            const info = pageTitles[panelId] || {title: panelId, sub: ''};
            document.getElementById('pageTitle').textContent = info.title;
            document.getElementById('pageSub').textContent = info.sub;

            // Auto-close sidebar on mobile
            const sidebar = document.getElementById('sidebar');
            if (sidebar && window.innerWidth <= 768) {
                sidebar.classList.remove('open');
            }
        }

        // ── Color Picker ──────────────────────────────────
        function updateColorFromPicker(input, key) {
            const val = input.value;
            const txtInput = document.getElementById('ci_' + key);
            if (txtInput) txtInput.value = val;
            
            document.getElementById('theme_' + key).value = val;
            
            const swatch = document.getElementById('swatch_' + key);
            if (swatch) swatch.style.background = val;
            
            updateThemePreview();
        }

        function updateColorFromInput(input, key) {
            let val = input.value.trim();
            if (val === '') return;
            
            // Auto prepend # if typed raw hex digits
            if (/^[0-9a-fA-F]{3,8}$/.test(val)) {
                val = '#' + val;
                input.value = val;
            }
            
            if (/^#[0-9a-fA-F]{3,8}$/.test(val)) {
                document.getElementById('theme_' + key).value = val;
                
                const swatch = document.getElementById('swatch_' + key);
                if (swatch) swatch.style.background = val;
                
                let pickerVal = val;
                if (val.length === 4) {
                    pickerVal = '#' + val[1] + val[1] + val[2] + val[2] + val[3] + val[3];
                }
                if (pickerVal.length === 7) {
                    const picker = document.getElementById('cp_' + key);
                    if (picker) picker.value = pickerVal.toLowerCase();
                }
                
                updateThemePreview();
            }
        }

        // Compatibility function
        function updateColorCard(input, key) {
            updateColorFromPicker(input, key);
        }

        function updateThemePreview() {
            const primary = document.getElementById('theme_primary_color')?.value || '#1d4ed8';
            const accent = document.getElementById('theme_accent_color')?.value || '#60a5fa';
            const bg = document.getElementById('theme_bg_color')?.value || '#060913';
            const text = document.getElementById('theme_text_color')?.value || '#f3f4f6';

            const preview = document.getElementById('theme-live-preview');
            if (!preview) return;
            preview.style.background = bg;
            preview.children[0].style.color = text;
            preview.children[1].style.color = accent;
            preview.children[2].children[0].style.background = primary;
            preview.children[2].children[1].style.borderColor = primary;
            preview.children[2].children[1].style.color = accent;
        }

        // ── Toast Notifications ───────────────────────────
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const icon = type === 'success'
                ? '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
                : '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
            toast.innerHTML = icon + message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('out');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ── Save Functions ────────────────────────────────
        async function saveSection(section, data, type = 'content') {
            try {
                const response = await fetch('api/save_content.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ section, data, type })
                });
                const result = await response.json();
                if (result.success) {
                    showToast(`${section} saved successfully!`, 'success');
                } else {
                    showToast(result.error || 'Save failed', 'error');
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
            }
        }

        function saveCurrentSection() {
            switch (currentPanel) {
                case 'dashboard': showToast('Select a section to edit and save', 'error'); break;
                case 'hero': saveHero(); break;
                case 'about': saveAbout(); break;
                case 'services': saveServices(); break;
                case 'experience': saveExperience(); break;
                case 'technology': saveTechnology(); break;
                case 'expertise': saveExpertise(); break;
                case 'contact': saveContact(); break;
                case 'theme': saveTheme(); break;
                case 'meta': saveMeta(); break;
                case 'opportunity': saveOpportunity(); break;
                case 'capabilities': saveCapabilities(); break;
                case 'clients': saveClients(); break;
                case 'impact': saveImpact(); break;
                case 'difference': saveDifference(); break;
            }
        }

        function saveHero() {
            const taglines = [];
            document.querySelectorAll('#hero_taglines .list-item input').forEach(i => {
                if (i.value.trim()) taglines.push(i.value.trim());
            });
            const data = {
                name: document.getElementById('hero_name').value,
                title: document.getElementById('hero_title').value,
                subtitle: document.getElementById('hero_subtitle').value,
                description: document.getElementById('hero_description').value,
                cta_text: document.getElementById('hero_cta_text').value,
                cta_url: document.getElementById('hero_cta_url').value,
                bg_image: document.getElementById('hero_bg_image').value,
                taglines
            };
            saveSection('hero', data);
        }

        function saveAbout() {
            const stats = [];
            document.querySelectorAll('#about_stats .list-item').forEach(item => {
                stats.push({
                    value: item.querySelector('.stat-value-input').value,
                    label: item.querySelector('.stat-label-input').value
                });
            });
            saveSection('about', {
                headline: document.getElementById('about_headline').value,
                subheading: document.getElementById('about_subheading').value,
                bio: document.getElementById('about_bio').value,
                quote: document.getElementById('about_quote').value,
                image: document.getElementById('about_image').value,
                stats
            });
        }

        function saveServices() {
            const items = [];
            document.querySelectorAll('#services_items .list-item').forEach(item => {
                const bulletsRaw = item.querySelector('.service-bullets').value;
                const bullets = bulletsRaw.split('\n').map(b => b.trim()).filter(b => b);
                items.push({
                    title: item.querySelector('.service-title').value,
                    subtitle: item.querySelector('.service-subtitle').value,
                    description: item.querySelector('.service-desc').value,
                    icon_image: item.querySelector('.service-icon-image') ? item.querySelector('.service-icon-image').value : '',
                    bullets
                });
            });
            saveSection('services', {
                headline: document.getElementById('services_headline').value,
                subheading: document.getElementById('services_subheading').value,
                items
            });
        }

        function saveExperience() {
            const jobs = [];
            document.querySelectorAll('#experience_jobs .list-item').forEach(item => {
                const bulletsRaw = item.querySelector('.job-bullets').value;
                const bullets = bulletsRaw.split('\n').map(b => b.trim()).filter(b => b);
                jobs.push({
                    title: item.querySelector('.job-title').value,
                    company: item.querySelector('.job-company').value,
                    period: item.querySelector('.job-period').value,
                    location: item.querySelector('.job-location').value,
                    bullets
                });
            });
            saveSection('experience', {
                headline: document.getElementById('exp_headline').value,
                subheading: document.getElementById('exp_subheading').value,
                jobs
            });
        }

        function saveTechnology() {
            const categories = [];
            document.querySelectorAll('#tech_categories .tech-category-item').forEach(catItem => {
                const items = [];
                catItem.querySelectorAll('.tech-item-row').forEach(row => {
                    const name = row.querySelector('.tech-item-name').value.trim();
                    const image = row.querySelector('.tech-item-image').value.trim();
                    if (name) items.push({ name, image });
                });
                categories.push({
                    name: catItem.querySelector('.cat-name').value,
                    items
                });
            });
            saveSection('technology', {
                headline: document.getElementById('tech_headline').value,
                subheading: document.getElementById('tech_subheading').value,
                categories
            });
        }

        function saveExpertise() {
            const items = [];
            document.querySelectorAll('#expertise_items .expertise-item').forEach(i => {
                if (i.value.trim()) items.push(i.value.trim());
            });
            saveSection('expertise', {
                headline: document.getElementById('expertise_headline').value,
                subheading: document.getElementById('expertise_subheading').value,
                items
            });
        }

        function saveContact() {
            saveSection('contact', {
                headline: document.getElementById('contact_headline').value,
                email: document.getElementById('contact_email').value,
                linkedin: document.getElementById('contact_linkedin').value
            });
        }

        function saveTheme() {
            const themeData = {};
            document.querySelectorAll('[id^="theme_"]').forEach(input => {
                const key = input.id.replace('theme_', '');
                themeData[key] = input.value;
            });
            saveSection('theme', themeData, 'theme');
        }

        function saveMeta() {
            saveSection('meta', {
                title: document.getElementById('meta_title').value,
                description: document.getElementById('meta_description').value,
                keywords: document.getElementById('meta_keywords').value
            });
        }

        // ── Dynamic List Adders ───────────────────────────
        function addTagline() {
            const container = document.getElementById('hero_taglines');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Pill ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <input class="form-input" type="text" value="" placeholder="Tagline text">
            `;
            container.appendChild(div);
        }

        function removeTagline(btn) { btn.closest('.list-item').remove(); }

        function addStat() {
            const container = document.getElementById('about_stats');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Stat ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group" style="margin:0"><label class="form-label">Value</label><input class="form-input stat-value-input" type="text" placeholder="14+"></div>
                    <div class="form-group" style="margin:0"><label class="form-label">Label</label><input class="form-input stat-label-input" type="text" placeholder="Years Experience"></div>
                </div>
            `;
            container.appendChild(div);
        }

        function addServiceItem() {
            const container = document.getElementById('services_items');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Service ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-group"><label class="form-label">Title</label><input class="form-input service-title" type="text" placeholder="Service title"></div>
                <div class="form-group"><label class="form-label">Subtitle</label><input class="form-input service-subtitle" type="text" placeholder="Service subtitle"></div>
                <div class="form-group"><label class="form-label">Description</label><textarea class="form-textarea service-desc" placeholder="Service description"></textarea></div>
                <div class="form-group"><label class="form-label">Bullets (one per line)</label><textarea class="form-textarea service-bullets" placeholder="Bullet points"></textarea></div>
                <div class="form-group">
                    <label class="form-label">Custom Icon Image (optional)</label>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img class="svc-icon-preview" style="width:48px; height:48px; object-fit:contain; border-radius:8px; border:1px solid var(--border); display:none" onerror="this.style.display='none'">
                        <div style="display:flex; flex-direction:column; gap:6px; flex:1;">
                            <input type="file" class="svc-icon-file" style="display:none" accept="image/*" onchange="uploadImageInline(this)">
                            <button type="button" class="header-btn btn-outline" style="align-self:flex-start; font-size:11px; padding:4px 12px;" onclick="this.closest('.form-group').querySelector('.svc-icon-file').click()">Upload Icon</button>
                            <input class="form-input service-icon-image" type="text" placeholder="Leave empty for default SVG icon">
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        function addJobItem() {
            const container = document.getElementById('experience_jobs');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Role ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Job Title</label><input class="form-input job-title" type="text"></div>
                    <div class="form-group"><label class="form-label">Company</label><input class="form-input job-company" type="text"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Period</label><input class="form-input job-period" type="text" placeholder="2021 – Present"></div>
                    <div class="form-group"><label class="form-label">Location</label><input class="form-input job-location" type="text"></div>
                </div>
                <div class="form-group"><label class="form-label">Key Points (one per line)</label><textarea class="form-textarea job-bullets"></textarea></div>
            `;
            container.appendChild(div);
        }

        function addTechCategory() {
            const container = document.getElementById('tech_categories');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.className = 'list-item tech-category-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Category ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-group"><label class="form-label">Category Name</label><input class="form-input cat-name" type="text" placeholder="Data Platforms"></div>
                <div class="form-group">
                    <label class="form-label" style="margin-bottom:8px">Technology Items</label>
                    <div class="tech-items-list" style="display:flex; flex-direction:column; gap:10px;"></div>
                    <button type="button" class="btn-add" style="margin-top:8px;" onclick="addTechItem(this.closest('.form-group').querySelector('.tech-items-list'))">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Item
                    </button>
                </div>
            `;
            container.appendChild(div);
        }

        function addExpertiseItem() {
            const container = document.getElementById('expertise_items');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Item ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <input class="form-input expertise-item" type="text" placeholder="Industry name">
            `;
            container.appendChild(div);
        }

        // ── Generic Image Upload Helper ──────────────────
        async function uploadImage(input, previewId, pathInputId) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch('api/upload_image.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    const previewEl = document.getElementById(previewId);
                    const pathEl = document.getElementById(pathInputId);
                    if (previewEl) { previewEl.src = '..' + result.filePath; previewEl.style.display = ''; }
                    if (pathEl) pathEl.value = result.filePath;
                    showToast('Image uploaded successfully!', 'success');
                } else {
                    showToast(result.error || 'Upload failed', 'error');
                }
            } catch (err) {
                showToast('Upload error. Please try again.', 'error');
            }
        }

        // Inline image upload for dynamically created rows (services icons, tech items)
        async function uploadImageInline(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch('api/upload_image.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    const row = input.closest('.tech-item-row') || input.closest('.form-group');
                    if (row) {
                        const preview = row.querySelector('.tech-item-preview, .svc-icon-preview');
                        const pathInput = row.querySelector('.tech-item-image, .service-icon-image');
                        if (preview) { preview.src = '..' + result.filePath; preview.style.display = ''; }
                        if (pathInput) pathInput.value = result.filePath;
                    }
                    showToast('Image uploaded successfully!', 'success');
                } else {
                    showToast(result.error || 'Upload failed', 'error');
                }
            } catch (err) {
                showToast('Upload error. Please try again.', 'error');
            }
        }

        // About Image Upload (uses generic helper)
        function uploadAboutImage(input) {
            uploadImage(input, 'about_image_preview', 'about_image');
        }

        // Add a tech item row inside a category
        function addTechItem(container) {
            const div = document.createElement('div');
            div.className = 'tech-item-row';
            div.style.cssText = 'display:flex; align-items:center; gap:10px; padding:8px; border:1px solid var(--border); border-radius:8px; background:rgba(255,255,255,0.02);';
            div.innerHTML = `
                <img class="tech-item-preview" style="width:40px; height:40px; object-fit:contain; border-radius:6px; border:1px solid var(--border); flex-shrink:0; display:none" onerror="this.style.display='none'">
                <input class="form-input tech-item-name" type="text" placeholder="Technology name" style="flex:1;">
                <input class="form-input tech-item-image" type="text" placeholder="Image path" style="flex:1; font-size:11px;">
                <input type="file" class="tech-item-file" style="display:none" accept="image/*" onchange="uploadImageInline(this)">
                <button type="button" class="header-btn btn-outline" style="font-size:10px; padding:4px 10px; flex-shrink:0;" onclick="this.closest('.tech-item-row').querySelector('.tech-item-file').click()" title="Upload icon">
                    <svg style="width:14px;height:14px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </button>
                <button type="button" class="btn-icon" style="flex-shrink:0" onclick="this.closest('.tech-item-row').remove()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;
            container.appendChild(div);
        }

        // Opportunity Save & Pillars Add
        function saveOpportunity() {
            const pillars = [];
            document.querySelectorAll('#opp_pillars .list-item input').forEach(i => {
                if (i.value.trim()) pillars.push(i.value.trim());
            });
            saveSection('opportunity', {
                title: document.getElementById('opp_title').value,
                friction_title: document.getElementById('opp_friction_title').value,
                solution_title: document.getElementById('opp_solution_title').value,
                friction_text: document.getElementById('opp_friction_text').value,
                quote: document.getElementById('opp_quote').value,
                solution_text: document.getElementById('opp_solution_text').value,
                bottom_quote: document.getElementById('opp_bottom_quote').value,
                pillars
            });
        }

        function addOppPillar() {
            const container = document.getElementById('opp_pillars');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Pillar ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <input class="form-input opp-pillar-input" type="text" placeholder="Pillar text">
            `;
            container.appendChild(div);
        }

        // Capabilities Save & Timeline Items Add
        function saveCapabilities() {
            const items = [];
            document.querySelectorAll('#capabilities_items .list-item').forEach(item => {
                const type = item.getAttribute('data-type') || 'text';
                const category = item.querySelector('.cap-category').value;
                if (type === 'pills') {
                    const rawItems = item.querySelector('.cap-items').value;
                    items.push({
                        category,
                        type,
                        items: rawItems.split(',').map(s => s.trim()).filter(s => s)
                    });
                } else {
                    items.push({
                        category,
                        type,
                        description: item.querySelector('.cap-description').value
                    });
                }
            });
            saveSection('capabilities', {
                headline: 'Capabilities',
                subheading: 'Professional Footprint',
                items
            });
        }

        function addCapabilityItem() {
            const container = document.getElementById('capabilities_items');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.setAttribute('data-type', 'text');
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Timeline Item ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input class="form-input cap-category" type="text" placeholder="e.g. Global Footprint">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Item Type</label>
                        <select class="form-select cap-type" onchange="toggleCapType(this)">
                            <option value="text" selected>Description Text</option>
                            <option value="pills">Pill List (Comma Separated)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group cap-text-group" style="display: block">
                    <label class="form-label">Description Text</label>
                    <textarea class="form-textarea cap-description" placeholder="Description text"></textarea>
                </div>
                <div class="form-group cap-pills-group" style="display: none">
                    <label class="form-label">Pills (comma-separated)</label>
                    <input class="form-input cap-items" type="text" placeholder="UAE, Netherlands, Germany">
                </div>
            `;
            container.appendChild(div);
        }

        function toggleCapType(select) {
            const listItem = select.closest('.list-item');
            const type = select.value;
            listItem.setAttribute('data-type', type);
            listItem.querySelector('.cap-text-group').style.display = type === 'text' ? 'block' : 'none';
            listItem.querySelector('.cap-pills-group').style.display = type === 'pills' ? 'block' : 'none';
        }

        // Ideal Clients Save & Tab Add
        function saveClients() {
            const tabs = [];
            document.querySelectorAll('#clients_tabs .list-item').forEach(item => {
                tabs.push({
                    id: item.querySelector('.client-tab-id').value,
                    num: item.querySelector('.client-tab-num').value,
                    title: item.querySelector('.client-tab-title').value,
                    text: item.querySelector('.client-tab-text').value
                });
            });
            saveSection('clients', {
                intro: document.getElementById('clients_intro').value,
                tabs
            });
        }

        function addClientTab() {
            const container = document.getElementById('clients_tabs');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Tab ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tab Identifier (e.g. ceos-founders)</label>
                        <input class="form-input client-tab-id" type="text" placeholder="ceos-founders">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tab Number Prefixed (e.g. 01 //)</label>
                        <input class="form-input client-tab-num" type="text" placeholder="01 //">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tab / Panel Title</label>
                    <input class="form-input client-tab-title" type="text" placeholder="CEOs & Founders">
                </div>
                <div class="form-group">
                    <label class="form-label">Showcase Content Description</label>
                    <textarea class="form-textarea client-tab-text" placeholder="Tab description text"></textarea>
                </div>
            `;
            container.appendChild(div);
        }

        // Delivered Impact Save & Slide Add
        function saveImpact() {
            const slides = [];
            document.querySelectorAll('#impact_slides .list-item').forEach(item => {
                slides.push({
                    num: item.querySelector('.impact-num').value,
                    title: item.querySelector('.impact-title').value,
                    description: item.querySelector('.impact-description').value
                });
            });
            saveSection('impact', { slides });
        }

        function addImpactSlide() {
            const container = document.getElementById('impact_slides');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Slide ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Card Number (e.g. 01)</label>
                        <input class="form-input impact-num" type="text" placeholder="01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Card Title</label>
                        <input class="form-input impact-title" type="text" placeholder="Result Title">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Card Description</label>
                    <textarea class="form-textarea impact-description" placeholder="Description text"></textarea>
                </div>
            `;
            container.appendChild(div);
        }

        // The Difference Save & Card Add
        function saveDifference() {
            const cards = [];
            document.querySelectorAll('#diff_cards .list-item').forEach(item => {
                cards.push({
                    title: item.querySelector('.diff-card-title').value,
                    text: item.querySelector('.diff-card-text').value
                });
            });
            saveSection('difference', {
                subheading: document.getElementById('diff_subheading').value,
                title: document.getElementById('diff_title').value,
                text1: document.getElementById('diff_text1').value,
                text2: document.getElementById('diff_text2').value,
                cards
            });
        }

        function addDiffCard() {
            const container = document.getElementById('diff_cards');
            const count = container.querySelectorAll('.list-item').length + 1;
            const div = document.createElement('div');
            div.className = 'list-item';
            div.innerHTML = `
                <div class="list-item-header">
                    <span class="list-item-num">Card ${count}</span>
                    <button class="btn-icon" onclick="this.closest('.list-item').remove()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="form-group">
                    <label class="form-label">Card Title</label>
                    <input class="form-input diff-card-title" type="text" placeholder="Value card title">
                </div>
                <div class="form-group">
                    <label class="form-label">Card Description</label>
                    <textarea class="form-textarea diff-card-text" placeholder="Value card description"></textarea>
                </div>
            `;
            container.appendChild(div);
        }
    </script>
</body>
</html>
