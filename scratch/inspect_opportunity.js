import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

const regex = /Right Column/gi;
let match;
while ((match = regex.exec(html)) !== null) {
  console.log(`Match at index ${match.index}: "${html.substring(match.index - 50, match.index + 50)}"`);
}
