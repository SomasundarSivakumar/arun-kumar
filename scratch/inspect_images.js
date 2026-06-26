import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

// Find all img tags
const imgRegex = /<img\s+[^>]*src="([^"]+)"/g;
const images = [];
let match;
while ((match = imgRegex.exec(html)) !== null) {
  images.push(match[1]);
}
console.log('Images in index.html:', images);
