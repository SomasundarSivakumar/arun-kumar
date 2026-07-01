import fs from 'fs';

const content = fs.readFileSync('c:/Users/SUNDAR/Documents/GitHub/arun-kumar/admin/dashboard.php', 'utf8');
const lines = content.split('\n');

lines.forEach((line, idx) => {
    const lineNum = idx + 1;
    if (lineNum > 870) { // Only HTML body
        if (line.includes('style=') || line.includes('style =')) {
            if (line.includes('#fff') || line.includes('white') || line.includes('#ffffff') || line.includes('rgba(255,255,255')) {
                console.log(`Line ${lineNum}: ${line.trim()}`);
            }
        }
    }
});
