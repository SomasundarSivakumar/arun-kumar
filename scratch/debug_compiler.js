import fs from 'fs';

const htmlPath = 'c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html';
let content = fs.readFileSync(htmlPath, 'utf8');

const steps = [
  {
    name: 'Header',
    fn: (c) => '<?php /* header */ ?>\n' + c
  },
  {
    name: 'Title/Meta',
    fn: (c) => {
      c = c.replace(/<title>[\s\S]*?<\/title>/i, '<title><?= h($metaTitle) ?></title>');
      c = c.replace(/<meta\s+name="description"\s+[\s\S]*?\/>/i, '<meta name="description" content="<?= h($metaDescription) ?>" />');
      return c;
    }
  },
  {
    name: 'Stylesheet',
    fn: (c) => c.replace(/<link\s+rel="stylesheet"\s+href="\/src\/style\.css"\s*\/?>/i, '<!-- css -->')
  },
  {
    name: 'Head End Style',
    fn: (c) => c.replace('</head>', '<style></style></head>')
  },
  {
    name: 'Hero Name',
    fn: (c) => c.replace(/<span class="block text-\[clamp\(3rem,7vw,6rem\)\]">Arun Kumar Jayakumar<\/span>/i, '<?= h($heroName) ?>')
  },
  {
    name: 'Hero Taglines',
    fn: (c) => c.replace(/<div class="hero-tagline flex flex-wrap justify-center items-center gap-3 mt-6 select-none">[\s\S]*?<\/div>/i, '<!-- taglines -->')
  },
  {
    name: 'Marquee',
    fn: (c) => c.replace(/<div class="marquee-track py-2">[\s\S]*?<!-- Marquee Group B[^>]*>[\s\S]*?<\/div>\s*<\/div>/i, '<!-- marquee -->')
  },
  {
    name: 'Opportunity Title',
    fn: (c) => c.replace(/Executive\s+Data\s+Leadership\s+—\s+Without\s+the\s+Full-Time\s+Overhead/i, '<?= h($oppTitle) ?>')
  },
  {
    name: 'Opportunity Friction Title',
    fn: (c) => c.replace(/Most\s+organizations\s+sit\s+on\s+<span[\s\S]*?>significant\s+untapped\s+data\s+potential<\/span>\./i, '<?= $oppFrictionTitle ?>')
  },
  {
    name: 'Opportunity Friction Text',
    fn: (c) => c.replace(/<div class="flex flex-col gap-4 text-gray-400 font-light text-sm md:text-base leading-relaxed z-10">[\s\S]*?Decisions are delayed[\s\S]*?What is missing[\s\S]*?<\/div>\s*<\/div>/i, '<!-- friction -->')
  },
  {
    name: 'Opportunity Solution Title',
    fn: (c) => c.replace(/Executive-level\s+data\s+leadership\s+<span[\s\S]*?>precisely\s+when\s+you\s+need\s+it<\/span>\./i, '<?= $oppSolutionTitle ?>')
  },
  {
    name: 'Opportunity Solution Text',
    fn: (c) => c.replace(/<p class="text-gray-300 font-light text-sm md:text-base leading-relaxed z-10">[\s\S]*?As a Fractional Chief Data Officer[\s\S]*?<\/p>/i, '<!-- solution text -->')
  },
  {
    name: 'Opportunity Pillars',
    fn: (c) => c.replace(/<!-- Key pillars of Fractional CDO -->\s*<div class="flex flex-wrap gap-2\.5 mt-2 z-10">[\s\S]*?<\/div>/i, '<!-- pillars -->')
  },
  {
    name: 'Opportunity Bottom Quote',
    fn: (c) => c.replace(/<p class="text-xl md:text-3xl font-serif font-semibold text-white\/95 leading-relaxed">[\s\S]*?<\/p>/i, '<!-- bottom quote -->')
  },
  {
    name: 'About Title',
    fn: (c) => c.replace(/Meet<br\s*\/?>\s*<span\s+class="text-white\s+text-6xl">Arun\s+Kumar\s+Jayakumar<\/span>/i, '<!-- about title -->')
  },
  {
    name: 'About Bio',
    fn: (c) => c.replace(/<!-- Left Column: Biography Content \(First 50%\) -->[\s\S]*?I am a <span class="text-\[#3b82f6\][\s\S]*?<\/div>\s*<\/div>/i, '<!-- bio -->')
  },
  {
    name: 'About Image',
    fn: (c) => c.replace(/src="\/assets\/images\/arun_kumar\.png"/i, 'src="<!-- img -->"')
  },
  {
    name: 'Capabilities',
    fn: (c) => c.replace(/<div\s+class="flex\s+flex-col\s+justify-between\s+py-2\s+h-\[600px\]\s+flex-grow"\s+id="timeline-content-container">[\s\S]*?Core\s+Disciplines[\s\S]*?<\/div>\s*<\/div>/i, '<!-- capabilities -->')
  },
  {
    name: 'Services',
    fn: (c) => c.replace(/<div\s+class="flex\s+flex-wrap\s+justify-center\s+gap-\[1\.5rem\]\s+lg:gap-\[2rem\]\s+px-\[1\.5rem\]\s+md:px-\[4rem\]\s+mt-\[3rem\]\s+lg:mt-\[4rem\]">[\s\S]*?Migration Planning & Risk Management[\s\S]*?<\/div>\s*<\/div>\s*<\/section>/i, '<!-- services -->')
  },
  {
    name: 'Ideal Clients Intro',
    fn: (c) => c.replace(/<p class="text-white\/60 text-base md:text-lg leading-relaxed font-inter">[\s\S]*?<\/p>/i, '<!-- clients intro -->')
  },
  {
    name: 'Ideal Clients Nav',
    fn: (c) => c.replace(/<div\s+class="client-nav\s+flex\s+flex-col\s+gap-6"\s+id="client-nav-list">[\s\S]*?<\/div>/i, '<!-- clients nav -->')
  },
  {
    name: 'Ideal Clients Panels',
    fn: (c) => c.replace(/<!-- 1\. CEOs & Founders Detail Panel -->[\s\S]*?<\/section>/i, '<!-- clients panels -->')
  },
  {
    name: 'Impact Swiper',
    fn: (c) => c.replace(/<!-- Slide 1: Enterprise Data Governance -->[\s\S]*?<!-- Swiper Navigation Arrows -->/i, '<!-- impact swiper -->')
  },
  {
    name: 'Experience highlights',
    fn: (c) => c.replace(/<!-- Timeline Item 1 -->[\s\S]*?<\/div>\s*<\/div>\s*<\/section>/i, '<!-- exp -->')
  },
  {
    name: 'Technology Stack',
    fn: (c) => c.replace(/<div\s+class="max-w-\[80%\]\s+mx-auto">[\s\S]*?<\/section>/i, '<!-- tech -->')
  },
  {
    name: 'Difference Headings',
    fn: (c) => {
      c = c.replace(/<h2 class="text-sm font-semibold uppercase tracking-widest text-\[#1d4ed8\] mb-2 font-inter">THE DIFFERENCE\s*<\/h2>/i, '<!-- diff h2 -->');
      c = c.replace(/<p class="text-3xl md:text-5xl font-serif font-bold text-white mb-6">Why Work With Me\s*<\/p>/i, '<!-- diff p -->');
      return c;
    }
  },
  {
    name: 'Difference Text 1 & 2',
    fn: (c) => {
      c = c.replace(/<p class="text-white\/85 text-base md:text-lg leading-relaxed font-sans mt-2">[\s\S]*?<\/p>/i, '<!-- diff t1 -->');
      c = c.replace(/<p class="text-white\/60 text-sm md:text-base leading-relaxed font-sans">[\s\S]*?<\/p>/i, '<!-- diff t2 -->');
      return c;
    }
  },
  {
    name: 'Difference Cards',
    fn: (c) => c.replace(/<div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">[\s\S]*?<\/section>/i, '<!-- diff cards -->')
  }
];

const checkIds = ['about', 'capabilities', 'services', 'ideal-clients'];

console.log('Initial check:');
for (const id of checkIds) {
  console.log(`  ${id}: ${content.includes(`id="${id}"`)}`);
}

for (const step of steps) {
  content = step.fn(content);
  console.log(`\nAfter step: ${step.name}`);
  for (const id of checkIds) {
    console.log(`  ${id}: ${content.includes(`id="${id}"`)}`);
  }
}
