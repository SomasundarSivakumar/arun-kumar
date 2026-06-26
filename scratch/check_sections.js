import fs from 'fs';
const php = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.php', 'utf8');

const ids = [
  'hero',
  'expertise',
  'opportunity',
  'about',
  'capabilities',
  'services',
  'ideal-clients',
  'impact',
  'experience',
  'technology',
  'the-difference',
  'cta'
];

console.log('=== Section ID Check ===');
for (const id of ids) {
  console.log(`${id}: ${php.includes(`id="${id}"`)}`);
}
