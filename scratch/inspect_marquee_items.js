import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

const start = html.indexOf('<div class="marquee-track py-2">');
const end = html.indexOf('<!-- Marquee Group B');
if (start !== -1 && end !== -1) {
  console.log(html.substring(start, end));
} else {
  console.log('Could not find marquee track or Group B');
}
