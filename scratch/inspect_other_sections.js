import fs from 'fs';
const html = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/index.html', 'utf8');

const clientStart = html.indexOf('<section id="ideal-clients"');
const impactStart = html.indexOf('<section id="impact"');
const diffStart = html.indexOf('<section id="the-difference"');
const ctaStart = html.indexOf('<section id="cta"');

if (clientStart !== -1 && impactStart !== -1) {
  fs.writeFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/scratch/ideal_clients_section.txt', html.substring(clientStart, impactStart));
  console.log('Saved ideal-clients to ideal_clients_section.txt');
}
if (impactStart !== -1 && diffStart !== -1) {
  fs.writeFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/scratch/impact_section.txt', html.substring(impactStart, diffStart));
  console.log('Saved impact to impact_section.txt');
}
if (diffStart !== -1 && ctaStart !== -1) {
  fs.writeFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/scratch/difference_section.txt', html.substring(diffStart, ctaStart));
  console.log('Saved difference to difference_section.txt');
}
