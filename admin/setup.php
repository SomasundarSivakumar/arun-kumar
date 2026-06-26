<?php
/**
 * Database Setup Script
 * Run this ONCE at: http://localhost/arun-kumar/admin/setup.php
 * DELETE this file after setup is complete for security!
 */

// Only allow local access
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost'])) {
    http_response_code(403);
    die('Access denied');
}

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect without DB first to create it
    $conn = new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');
    $results = [];

    // Create database
    $conn->query('CREATE DATABASE IF NOT EXISTS arun_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $results[] = ['✅', 'Database created: arun_portfolio'];

    // Select database
    $conn->select_db('arun_portfolio');

    // Create admin_users table
    $conn->query('CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )');
    $results[] = ['✅', 'Table created: admin_users'];

    // Insert default admin (admin/admin123)
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $conn->prepare('INSERT IGNORE INTO admin_users (username, password) VALUES (?, ?)');
    $stmt->bind_param('ss', $u, $p);
    $u = 'admin'; $p = $hashedPassword;
    $stmt->execute();
    $stmt->close();
    $results[] = ['✅', 'Admin user created: admin / admin123'];

    // Create site_content table
    $conn->query('CREATE TABLE IF NOT EXISTS site_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        section VARCHAR(100) NOT NULL UNIQUE,
        content LONGTEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )');
    $results[] = ['✅', 'Table created: site_content'];

    // Create theme_settings table
    $conn->query('CREATE TABLE IF NOT EXISTS theme_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value VARCHAR(255) NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )');
    $results[] = ['✅', 'Table created: theme_settings'];

    // Seed theme settings
    $themeDefaults = [
        ['primary_color', '#1d4ed8'],
        ['accent_color', '#60a5fa'],
        ['bg_color', '#060913'],
        ['text_color', '#f3f4f6'],
        ['sidebar_bg', '#03050a'],
        ['card_bg', 'rgba(255,255,255,0.02)'],
        ['border_color', 'rgba(255,255,255,0.05)'],
    ];
    $stmt = $conn->prepare('INSERT INTO theme_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->bind_param('ss', $k, $v);
    foreach ($themeDefaults as [$k, $v]) { $stmt->execute(); }
    $stmt->close();
    $results[] = ['✅', 'Theme settings seeded (7 defaults)'];

    // Seed site content
    $sections = [
        'hero' => [
            'name' => 'Arun Kumar Jayakumar',
            'title' => 'Fractional CDO & Enterprise Data Strategist',
            'subtitle' => 'Transforming Data into Strategic Advantage',
            'description' => '14+ years leading high-stakes data and AI transformations across global enterprises',
            'taglines' => ['Data Strategist', 'AI Advisor', 'Enterprise Transformation Leader'],
            'cta_text' => 'Book a Strategy Call',
            'cta_url' => '#contact'
        ],
        'about' => [
            'headline' => 'About Me',
            'subheading' => 'Enterprise Data Leader',
            'bio' => 'Arun Kumar Jayakumar is a seasoned Fractional Chief Data Officer with over 14 years of experience driving enterprise-level data and AI transformations across manufacturing, supply chain, logistics SaaS, and public sector organizations.',
            'quote' => 'I do not sell technology. I build the strategic clarity and operational confidence that allows the right technology to succeed.',
            'image' => '/assets/images/arun_kumar.png',
            'stats' => [
                ['value' => '14+', 'label' => 'Years Experience'],
                ['value' => '50+', 'label' => 'Transformations Led'],
                ['value' => '15+', 'label' => 'Countries'],
                ['value' => '$2B+', 'label' => 'Value Delivered']
            ]
        ],
        'opportunity' => [
            'title' => 'Executive Data Leadership — Without the Full-Time Overhead',
            'friction_title' => 'Most organizations sit on <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400 font-extrabold">significant untapped data potential</span>.',
            'friction_text' => 'Decisions are delayed by fragmented reporting, AI initiatives stall without clear governance, and ERP investments underdeliver because the data layer was never properly designed.',
            'quote' => 'What is missing is not more technology — it is senior leadership with the experience to connect strategy to execution.',
            'solution_title' => 'Executive-level data leadership <span class="text-[#3b82f6] font-extrabold">precisely when you need it</span>.',
            'solution_text' => 'As a Fractional Chief Data Officer, I provide the senior leadership your organization needs — with the cross-industry expertise, global delivery experience, and business-outcome orientation that full-time hiring rarely secures.',
            'pillars' => ['Cross-Industry Expertise', 'Global Delivery Experience', 'Business-Outcome Orientation'],
            'bottom_quote' => 'Data should not exist in reports. It should influence decisions. It should accelerate growth. It should create competitive advantage.'
        ],
        'capabilities' => [
            'headline' => 'Capabilities',
            'subheading' => 'Professional Footprint',
            'items' => [
                ['category' => 'Experience', 'description' => '14+ years across data strategy, ERP, AI, and enterprise delivery'],
                ['category' => 'Industries Served', 'description' => 'Manufacturing, Supply Chain, Logistics SaaS, Public Sector, Enterprise Tech'],
                ['category' => 'Global Footprint', 'type' => 'pills', 'items' => ['UAE', 'Netherlands', 'Germany', 'Canada', 'India']],
                ['category' => 'Engagement Model', 'type' => 'pills', 'items' => ['Fractional CDO', 'Advisory Retainer', 'Project-Based Consulting']],
                ['category' => 'Core Disciplines', 'description' => 'Data Strategy • AI Advisory • Business Intelligence • ERP Governance • Cloud Platforms']
            ]
        ],
        'services' => [
            'headline' => 'Services',
            'subheading' => 'How I Help Organizations Succeed',
            'items' => [
                [
                    'title' => 'Fractional Chief Data Officer',
                    'subtitle' => 'Executive Leadership',
                    'description' => 'Part-time executive leadership designed to establish data capabilities, coordinate business strategies, and guide core analytics implementations.',
                    'bullets' => [
                        'Data Strategy Development & Roadmapping',
                        'Enterprise Data Governance Frameworks',
                        'Executive KPI & Reporting Architecture',
                        'AI Readiness & Opportunity Assessment',
                        'Board-Level Stakeholder Alignment',
                        'Analytics Operating Model Design'
                    ]
                ],
                [
                    'title' => 'Data Strategy & Governance',
                    'subtitle' => 'Strategic Alignment',
                    'description' => 'Establishing clear structures, ownership models, metadata catalogs, and quality parameters that drive corporate value and cross-functional trust.',
                    'bullets' => [
                        'Enterprise Data Strategy Documentation',
                        'Data Governance Operating Model',
                        'Data Quality & Integrity Frameworks',
                        'Master Data Management Programs',
                        'Data Ownership & Stewardship Models',
                        'Policies, Standards & Compliance'
                    ]
                ],
                [
                    'title' => 'AI Strategy & Transformation',
                    'subtitle' => 'Innovation & Scale',
                    'description' => 'Evaluating data assets for AI-readiness, prioritizing operational use cases, and introducing governance parameters for Generative AI tooling.',
                    'bullets' => [
                        'AI Opportunity & Maturity Assessment',
                        'Use Case Identification & Prioritization',
                        'Generative AI Strategy & Implementation',
                        'AI Governance & Responsible AI Frameworks',
                        'Proof of Concept Design & Oversight',
                        'Enterprise AI Adoption & Change Management'
                    ]
                ],
                [
                    'title' => 'ERP Data Excellence',
                    'subtitle' => 'System Optimization',
                    'description' => 'Protecting transactional data integrity. Advising on ERP data governance, reporting structures, migration validation, and system integrations.',
                    'bullets' => [
                        'ERP Data Governance Design',
                        'Reporting Strategy & Architecture',
                        'Data Migration Planning & Validation',
                        'KPI Definition & Measurement Frameworks',
                        'Analytics Integration with ERP Systems',
                        'Post-Go-Live Data Quality Programs'
                    ]
                ],
                [
                    'title' => 'Business Intelligence & Analytics',
                    'subtitle' => 'Insight Enablement',
                    'description' => 'Structuring corporate dashboards and analytics platforms that deliver actionable insight, unified metric structures, and dashboard systems.',
                    'bullets' => [
                        'Executive Dashboard Design & Delivery',
                        'KPI Framework Architecture',
                        'Power BI & Tableau Implementation',
                        'Self-Service Analytics Enablement',
                        'Data Visualization Standards',
                        'Analytics Adoption Programs'
                    ]
                ],
                [
                    'title' => 'Digital Transformation Advisory',
                    'subtitle' => 'Transformation Delivery',
                    'description' => 'End-to-end advisory for organizations navigating complex digital transformation programs with data at the core.',
                    'bullets' => [
                        'Transformation Roadmap Development',
                        'Change Management & Communication',
                        'Technology Selection & Vendor Management',
                        'Digital Operating Model Design',
                        'Innovation Framework Development',
                        'Executive Alignment & Governance'
                    ]
                ]
            ]
        ],
        'clients' => [
            'intro' => 'My engagements deliver the greatest impact when leadership teams have strategic ambition, the organizational readiness to act, and a genuine desire to build lasting data capabilities — not just commission a report.',
            'tabs' => [
                ['id' => 'ceos-founders', 'num' => '01 //', 'title' => 'CEOs & Founders', 'text' => 'Build a data-driven growth strategy. Gain the executive data leadership needed to compete, scale, and attract investment.'],
                ['id' => 'cios-ctos', 'num' => '02 //', 'title' => 'CIOs & CTOs', 'text' => 'Align technology investments with business outcomes. Translate data and AI ambitions into practical, governed delivery programs.'],
                ['id' => 'erp-leaders', 'num' => '03 //', 'title' => 'ERP Program Leaders', 'text' => 'Improve data quality, reporting effectiveness, and post-implementation analytics maturity across your ERP estate.'],
                ['id' => 'pe-firms', 'num' => '04 //', 'title' => 'PE & Investment Firms', 'text' => 'Create executive-level visibility into portfolio company performance through unified data frameworks and KPI reporting.'],
                ['id' => 'enterprises', 'num' => '05 //', 'title' => 'Enterprise Organizations', 'text' => 'Modernize analytics, strengthen data governance, and accelerate AI adoption with senior advisory leadership.']
            ]
        ],
        'impact' => [
            'headline' => 'Impact',
            'subheading' => 'Delivered Results — Across Industries',
            'intro' => 'The measure of successful data leadership is not the sophistication of the technology deployed. It is the quality of decisions made, the speed of business response, and the competitive advantage unlocked.',
            'slides' => [
                [
                    'num' => '01',
                    'title' => 'Enterprise Data Governance',
                    'description' => 'Designed and implemented governance frameworks that materially improved data consistency, regulatory compliance, and reporting reliability — enabling leadership teams to act with confidence.',
                    'svg' => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /><circle cx="12" cy="12" r="3" /></svg>'
                ],
                [
                    'num' => '02',
                    'title' => 'Analytics Transformation',
                    'description' => 'Rebuilt reporting and analytics ecosystems for executive audiences, reducing decision-making latency and establishing single sources of truth for operational and strategic KPIs.',
                    'svg' => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18M18 9l-5 5-3-3-4 4" stroke-linecap="round" stroke-linejoin="round" /></svg>'
                ],
                [
                    'num' => '03',
                    'title' => 'AI Strategy Development',
                    'description' => 'Partnered with business leaders to assess AI maturity, identify high-value use cases, and build the governance and delivery structures required to move from proof-of-concept to production.',
                    'svg' => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="5" width="14" height="14" rx="2" /><path d="M9 5V2M15 5V2M9 19v3M15 19v3M5 9H2M5 15H2M19 9h3M19 15h3" /></svg>'
                ],
                [
                    'num' => '04',
                    'title' => 'Cloud Data Platform Architecture',
                    'description' => 'Designed modern, scalable cloud data environments on AWS and Azure — replacing fragmented legacy architectures with governed, analytics-ready data ecosystems.',
                    'svg' => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 10h-.7a7 7 0 00-13.8 2.1 4 4 0 00.5 7.9H18a5 5 0 000-10z" /></svg>'
                ],
                [
                    'num' => '05',
                    'title' => 'ERP Data Optimization',
                    'description' => 'Enabled organizations to extract strategic intelligence from their ERP investments — improving visibility across supply chain, procurement, inventory, and financial operations.',
                    'svg' => '<svg class="w-[3.5rem] h-[3.5rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><ellipse cx="12" cy="5" rx="9" ry="3" /><path d="M3 5v6c0 1.66 4 3 9 3s9-1.34 9-3V5M3 11v6c0 1.66 4 3 9 3s9-1.34 9-3v-6" /></svg>'
                ]
            ]
        ],
        'experience' => [
            'headline' => 'Experience',
            'subheading' => 'Career Highlights',
            'jobs' => [
                [
                    'title' => 'Technical Project Manager',
                    'company' => 'Estrel AI',
                    'period' => 'Current',
                    'location' => 'Dubai, UAE',
                    'bullets' => [
                        'Leading AI-driven public transportation initiatives and enterprise delivery programs.',
                        'Overseeing end-to-end programme governance for intelligent transit solutions deployed at scale.'
                    ]
                ],
                [
                    'title' => 'Director — Strategy & Business Development',
                    'company' => 'Iterative Research',
                    'period' => 'Strategy',
                    'location' => 'Dubai, UAE',
                    'bullets' => [
                        'Drove AI adoption strategy, data capability development, and SaaS growth initiatives.',
                        'Responsible for client advisory across data strategy and enterprise digital transformation.'
                    ]
                ],
                [
                    'title' => 'Senior Consultant',
                    'company' => 'Blue Brain Strategy',
                    'period' => 'Consulting',
                    'location' => 'International',
                    'bullets' => [
                        'Delivered data governance programmes, analytics transformation projects, and cloud-based business intelligence solutions for enterprise clients across multiple sectors.'
                    ]
                ],
                [
                    'title' => 'Oracle Techno-Functional Lead Consultant',
                    'company' => 'Tata Consultancy Services',
                    'period' => 'Enterprise',
                    'location' => 'EMEA',
                    'bullets' => [
                        'Led enterprise ERP implementations across the EMEA region, with responsibility for functional design, data migration strategy, and analytics integration on Oracle platforms.'
                    ]
                ],
                [
                    'title' => 'Oracle ERP Functional Analyst',
                    'company' => 'Accenture',
                    'period' => 'Foundation',
                    'location' => 'Global',
                    'bullets' => [
                        'Delivered global ERP transformation engagements with a focus on business process optimization, operational data design, and cross-functional stakeholder alignment.'
                    ]
                ]
            ]
        ],
        'technology' => [
            'headline' => 'TECHNOLOGY EXPERTISE',
            'subheading' => 'Platforms & Technologies',
            'categories' => [
                [
                    'name' => 'Strategy & Governance',
                    'items' => [
                        ['name' => 'Data Strategy', 'image' => '/assets/images/strat_3d_icon.png'],
                        ['name' => 'Data Governance', 'image' => '/assets/images/gov_3d_icon.png'],
                        ['name' => 'Master Data Management', 'image' => '/assets/images/mdm_3d_icon.png'],
                        ['name' => 'AI Strategy', 'image' => '/assets/images/ai_3d_icon.png'],
                        ['name' => 'AI Governance', 'image' => '/assets/images/aig_3d_icon.png']
                    ]
                ],
                [
                    'name' => 'Analytics & BI',
                    'items' => [
                        ['name' => 'Power BI', 'image' => '/assets/images/powerbi_3d_icon.png'],
                        ['name' => 'Tableau', 'image' => '/assets/images/tableau_3d_icon.png'],
                        ['name' => 'Executive Dashboards', 'image' => '/assets/images/dashboard_3d_icon.png'],
                        ['name' => 'KPI Frameworks', 'image' => '/assets/images/kpi_3d_icon.png'],
                        ['name' => 'Enterprise Reporting', 'image' => '/assets/images/reporting_3d_icon.png']
                    ]
                ],
                [
                    'name' => 'Cloud Platforms',
                    'items' => [
                        ['name' => 'Amazon Web Services (AWS)', 'image' => '/assets/images/aws_3d_icon.png'],
                        ['name' => 'Microsoft Azure', 'image' => '/assets/images/azure_3d_icon.png'],
                        ['name' => 'Snowflake', 'image' => '/assets/images/snowflake_3d_icon.png'],
                        ['name' => 'Data Lakes', 'image' => '/assets/images/datalake_3d_icon.png'],
                        ['name' => 'Data Warehouses', 'image' => '/assets/images/datawarehouse_3d_icon.png']
                    ]
                ],
                [
                    'name' => 'ERP Systems',
                    'items' => [
                        ['name' => 'Oracle ERP', 'image' => '/assets/images/oracle_erp_3d_icon.png'],
                        ['name' => 'Oracle Fusion', 'image' => '/assets/images/oracle_fusion_3d_icon.png']
                    ]
                ]
            ]
        ],
        'expertise' => [
            'headline' => 'Core Verticals',
            'subheading' => 'Strategic Domain Expertise',
            'items' => ['Manufacturing', 'Supply Chain', 'Logistics SaaS', 'Public Sector', 'Enterprise Tech', 'Financial Services', 'Healthcare', 'Retail']
        ],
        'difference' => [
            'headline' => 'THE DIFFERENCE',
            'subheading' => 'Why Work With Me',
            'text1' => 'Many consultants focus on technology. <span class="text-[#3b82f6] font-semibold">I focus on business outcomes.</span>',
            'text2' => 'I bridge the gap between executive strategy, business operations, data platforms, and AI innovation — delivering practical roadmaps that create measurable, lasting value.',
            'cards' => [
                ['title' => 'Decision Quality & Speed', 'text' => 'Replace intuition with trusted, timely intelligence at every level of the organization.'],
                ['title' => 'Operational Efficiency', 'text' => 'Surface the data insights that drive process improvement, cost reduction, and performance gains.'],
                ['title' => 'AI Transformation', 'text' => 'Move from strategy to delivery with governance, prioritization, and programme leadership in place.'],
                ['title' => 'Data Foundations', 'text' => 'Build the quality, governance, and ownership structures that make data an organizational asset.'],
                ['title' => 'Responsible AI Scale', 'text' => 'Design the frameworks, controls, and use-case roadmaps that turn AI potential into business value.'],
                ['title' => 'Technology ROI', 'text' => 'Ensure your ERP, cloud, and analytics investments deliver the intelligence they were designed to provide.']
            ]
        ],
        'contact' => [
            'headline' => 'Get In Touch',
            'subheading' => 'Book a Strategy Call',
            'text' => 'Whether you are exploring AI, modernizing analytics, implementing an ERP system, or establishing enterprise data governance, let us design a practical roadmap that delivers measurable business value.',
            'email' => 'arun@example.com',
            'linkedin' => 'https://linkedin.com/in/arunkumarjayakumar',
            'calendly' => 'https://calendly.com/arunkumar',
            'phone' => '+65 XXXX XXXX',
            'location' => 'Singapore / Global Remote'
        ],
        'meta' => [
            'title' => 'Arun Kumar Jayakumar | Fractional CDO & Enterprise Data Strategist',
            'description' => 'Portfolio of Arun Kumar Jayakumar, a Fractional CDO and Enterprise Data Strategy Consultant with over 14 years of experience leading high-stakes data and AI transformations.',
            'keywords' => 'Fractional CDO, Data Strategist, AI Advisor, Enterprise Data, Digital Transformation'
        ]
    ];

    $stmt = $conn->prepare('INSERT INTO site_content (section, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = CURRENT_TIMESTAMP');
    $stmt->bind_param('ss', $sec, $cnt);
    foreach ($sections as $sec => $data) {
        $cnt = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $stmt->execute();
    }
    $stmt->close();
    $results[] = ['✅', count($sections) . ' content sections seeded'];

    $conn->close();
    $success = true;

} catch (Exception $e) {
    $results[] = ['❌', 'Error: ' . $e->getMessage()];
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup — Arun Kumar Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; background: #060913; color: #f3f4f6; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #0d1117; border: 1px solid rgba(255,255,255,0.06); border-radius: 20px; padding: 40px; max-width: 600px; width: 100%; }
        h1 { font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .sub { font-size: 13px; color: #6b7280; margin-bottom: 28px; }
        .result-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); margin-bottom: 8px; font-size: 13.5px; }
        .icon { font-size: 16px; }
        .divider { height: 1px; background: rgba(255,255,255,0.06); margin: 24px 0; }
        .success-box { background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px; padding: 20px; color: #34d399; font-size: 14px; }
        .error-box { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); border-radius: 12px; padding: 20px; color: #f87171; font-size: 14px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #1d4ed8; color: white; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; margin-top: 20px; transition: all 0.2s; }
        .btn:hover { background: #1e40af; }
        .warning { background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 10px; padding: 14px 16px; font-size: 12px; color: #fbbf24; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🗄️ Database Setup</h1>
        <p class="sub">Setting up MySQL database for Arun Kumar Portfolio CMS</p>

        <?php foreach ($results as [$icon, $msg]): ?>
        <div class="result-item">
            <span class="icon"><?= $icon ?></span>
            <span><?= htmlspecialchars($msg) ?></span>
        </div>
        <?php endforeach; ?>

        <div class="divider"></div>

        <?php if ($success): ?>
        <div class="success-box">
            ✅ <strong>Database setup complete!</strong><br>
            All tables created and seeded with default content.<br><br>
            <strong>Admin credentials:</strong><br>
            Username: <code>admin</code> | Password: <code>admin123</code>
        </div>
        <a href="index.php" class="btn">→ Go to Admin Panel</a>
        <?php else: ?>
        <div class="error-box">
            ❌ Setup encountered errors. Check your MySQL connection settings in <code>admin/api/db.php</code>.
        </div>
        <?php endif; ?>

        <div class="warning">
            ⚠️ <strong>Security:</strong> Delete this file after setup! <code>/admin/setup.php</code>
        </div>
    </div>
</body>
</html>
