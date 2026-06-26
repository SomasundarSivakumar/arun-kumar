-- ============================================================
-- Arun Kumar Portfolio - Database Setup Script
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS arun_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE arun_portfolio;

-- ─── Admin Users ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username=admin, password=admin123
INSERT INTO admin_users (username, password)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username = username;

-- ─── Site Content ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS site_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(100) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ─── Theme Settings ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS theme_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default theme (matches current site colors)
INSERT INTO theme_settings (setting_key, setting_value) VALUES
('primary_color', '#1d4ed8'),
('accent_color', '#60a5fa'),
('bg_color', '#060913'),
('text_color', '#f3f4f6'),
('sidebar_bg', '#03050a'),
('card_bg', 'rgba(255,255,255,0.02)'),
('border_color', 'rgba(255,255,255,0.05)')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ─── Seed Default Site Content ───────────────────────────────
INSERT INTO site_content (section, content) VALUES
('hero', '{"name":"Arun Kumar Jayakumar","title":"Fractional CDO & Enterprise Data Strategist","subtitle":"Transforming Data into Strategic Advantage","description":"14+ years leading high-stakes data and AI transformations across global enterprises","taglines":["Data Strategist","AI Advisor","Enterprise Transformation Leader"],"cta_text":"Book a Strategy Call","cta_url":"#contact"}'),

('about', '{"headline":"About Me","subheading":"Enterprise Data Leader","bio":"Arun Kumar Jayakumar is a seasoned Fractional Chief Data Officer with over 14 years of experience driving enterprise-level data and AI transformations across manufacturing, supply chain, logistics SaaS, and public sector organizations.","stats":[{"value":"14+","label":"Years Experience"},{"value":"50+","label":"Transformations Led"},{"value":"15+","label":"Countries"},{"value":"$2B+","label":"Value Delivered"}]}'),

('services', '{"headline":"Services","subheading":"What I Offer","items":[{"title":"Fractional CDO Leadership","description":"Executive-level data leadership on a flexible engagement model. I embed with your team to set strategy, build governance, and drive outcomes.","icon":"chart"},{"title":"Data Strategy & Roadmapping","description":"Comprehensive data strategy aligned to your business goals, with clear milestones and measurable KPIs.","icon":"map"},{"title":"AI & ML Advisory","description":"Practical guidance on building and scaling AI capabilities, from model selection to deployment and governance.","icon":"brain"},{"title":"Data Governance & Quality","description":"Frameworks, policies, and tooling to ensure your data is trustworthy, well-governed, and compliance-ready.","icon":"shield"},{"title":"ERP Data Architecture","description":"Design and implementation of data layers that unlock the full value of your SAP, Oracle, or Microsoft ERP investments.","icon":"database"},{"title":"Executive Coaching","description":"One-on-one coaching for CDOs, data leaders, and aspiring executives navigating complex data transformations.","icon":"user"}]}'),

('experience', '{"headline":"Experience","subheading":"Career Journey","jobs":[{"title":"Fractional Chief Data Officer","company":"Independent","period":"2021 – Present","location":"Global","bullets":["Led data strategy for 15+ organizations across manufacturing, retail, and public sector","Delivered $500M+ in measurable business value through data-driven transformation","Advised C-suite on AI adoption, data governance, and digital transformation roadmaps"]},{"title":"Global Head of Data & Analytics","company":"Fortune 500 Enterprise","period":"2017 – 2021","location":"Singapore / UAE","bullets":["Built and led a 60-person global data organization across 4 continents","Architected enterprise data platform serving 50,000+ users","Delivered real-time supply chain visibility saving $120M annually"]},{"title":"Senior Data Architect","company":"Tier-1 Consulting Firm","period":"2013 – 2017","location":"India / UK","bullets":["Designed data warehouse and BI solutions for top-tier clients","Led ERP data migration projects for SAP S/4HANA rollouts","Delivered analytics programs across FMCG, pharma, and financial services"]}]}'),

('technology', '{"headline":"Technology","subheading":"Tech Stack","categories":[{"name":"Data Platforms","items":["Snowflake","Databricks","Azure Synapse","Google BigQuery","AWS Redshift"]},{"name":"AI & ML","items":["Python","TensorFlow","Azure ML","AWS SageMaker","OpenAI API"]},{"name":"Visualization","items":["Power BI","Tableau","Looker","Apache Superset","Grafana"]},{"name":"ERP & Integration","items":["SAP S/4HANA","Oracle ERP","Microsoft Dynamics","MuleSoft","Azure Data Factory"]},{"name":"Governance","items":["Collibra","Alation","Microsoft Purview","Apache Atlas","dbt"]}]}'),

('expertise', '{"headline":"Core Verticals","subheading":"Strategic Domain Expertise","items":["Manufacturing","Supply Chain","Logistics SaaS","Public Sector","Enterprise Tech","Financial Services","Healthcare","Retail"]}'),

('contact', '{"headline":"Get In Touch","subheading":"Book a Strategy Call","email":"arun@example.com","linkedin":"https://linkedin.com/in/arunkumarjayakumar","calendly":"https://calendly.com/arunkumar","phone":"+65 XXXX XXXX","location":"Singapore / Global Remote"}'),

('meta', '{"title":"Arun Kumar Jayakumar | Fractional CDO & Enterprise Data Strategist","description":"Portfolio of Arun Kumar Jayakumar, a Fractional CDO and Enterprise Data Strategy Consultant with over 14 years of experience leading high-stakes data and AI transformations.","keywords":"Fractional CDO, Data Strategist, AI Advisor, Enterprise Data, Digital Transformation"}')

ON DUPLICATE KEY UPDATE content = VALUES(content);

SELECT 'Database setup complete!' AS Status;
