import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

const capabilitiesStart = html.indexOf('<section id="capabilities"');
const capabilitiesEnd = html.indexOf('<section id="services"');
if (capabilitiesStart !== -1 && capabilitiesEnd !== -1) {
  console.log(html.substring(capabilitiesStart, capabilitiesStart + 2000));
} else {
  console.log('Could not find capabilities or services sections');
}
