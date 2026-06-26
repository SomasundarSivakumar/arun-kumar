import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

const capabilitiesStart = html.indexOf('<section id="capabilities"');
const capabilitiesEnd = html.indexOf('<section id="services"');
if (capabilitiesStart !== -1 && capabilitiesEnd !== -1) {
  fs.writeFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/scratch/capabilities_section.txt', html.substring(capabilitiesStart, capabilitiesEnd));
  console.log('Saved capabilities section to capabilities_section.txt. Size:', html.substring(capabilitiesStart, capabilitiesEnd).length);
} else {
  console.log('Could not find capabilities or services sections');
}
