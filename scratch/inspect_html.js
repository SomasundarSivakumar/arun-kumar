import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

// Find all HTML tags that start sections or have IDs
const matches = [];
const regex = /<([a-zA-Z0-9]+)\s+[^>]*id="([^"]+)"/g;
let match;
while ((match = regex.exec(html)) !== null) {
  matches.push({ tag: match[1], id: match[2] });
}

console.log('Found elements with IDs:', matches);

// Also look for sections/headings to see the general sections
const sections = [];
const secRegex = /<!--\s*──\s*([^-]+?)\s*──\s*-->/g;
while ((match = secRegex.exec(html)) !== null) {
  sections.push(match[1].trim());
}
console.log('Found sections markers:', sections);
