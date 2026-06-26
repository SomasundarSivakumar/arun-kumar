import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

const start = html.indexOf('<section id="expertise"');
const end = html.indexOf('<section id="opportunity"');
if (start !== -1 && end !== -1) {
  console.log(html.substring(start, start + 3000));
} else {
  console.log('Could not find expertise or opportunity sections');
}
