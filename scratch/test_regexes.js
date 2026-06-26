import fs from 'fs';

const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

const regexes = {
  metaDescription: /<meta\s+name="description"\s+[\s\S]*?\/>/i,
  stylesheet: /<link\s+rel="stylesheet"\s+href="\/src\/style\.css"\s*\/?>/i,
  heroName: />Arun Kumar Jayakumar<\/span>/i,
  typewriterText: /const HERO_ROLE_TEXT\s*=\s*'[^']+';/i,
  heroTaglines: /<div class="hero-tagline flex flex-wrap justify-center items-center gap-3 mt-6 select-none">[\s\S]*?<\/div>/i,
  marquee: /<div class="marquee-track py-2">[\s\S]*?<!-- Marquee Group B[^>]*>[\s\S]*?<\/div>\s*<\/div>/i,
  oppTitle: /Executive Data Leadership — Without the Full-Time Overhead/i,
  oppFrictionTitle: /Most organizations sit on <span class="text-transparent[^>]*>significant untapped data potential<\/span>\./i,
  oppFrictionText: /<p>Decisions are delayed by fragmented reporting[\s\S]*?<\/p>/i,
  oppQuote: /"What is missing is not more technology — it is senior leadership with the experience to connect strategy to execution\."/i,
  oppSolutionTitle: /Executive-level data leadership <span class="text-\[#3b82f6\][^>]*>precisely when you need it<\/span>\./i,
  oppSolutionText: /<p class="text-gray-300 font-light text-sm md:text-base leading-relaxed z-10">As a Fractional Chief Data Officer[\s\S]*?<\/p>/i,
  oppPillars: /<div class="flex flex-wrap gap-2.5 mt-2 z-10">[\s\S]*?<\/div>/i,
  oppBottomQuote: /<p class="text-xl md:text-3xl font-serif font-semibold text-white\/95 leading-relaxed">[\s\S]*?<\/p>/i,
  aboutTitle: /Meet<br \/><span class="text-white text-6xl">Arun Kumar Jayakumar<\/span>/i,
  aboutImage: /src="\/assets\/images\/arun_kumar\.png"/i
};

for (const [name, regex] of Object.entries(regexes)) {
  const match = html.match(regex);
  if (match) {
    console.log(`✅ MATCHED: ${name} (length: ${match[0].length})`);
  } else {
    console.log(`❌ FAILED: ${name}`);
  }
}
