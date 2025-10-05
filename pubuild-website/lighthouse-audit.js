#!/usr/bin/env node

const { execSync } = require('child_process');
const fs = require('fs');

const URL = process.argv[2] || 'http://localhost:3001';

console.log(`🧪 Running Lighthouse audit on: ${URL}`);
console.log('⏳ This may take a few minutes...\n');

// Run Lighthouse audit
try {
  const output = execSync(`npx lighthouse ${URL} --output=json --output-path=lighthouse-report.json --chrome-flags="--headless"`, {
    encoding: 'utf8',
    stdio: ['pipe', 'pipe', 'pipe']
  });

  console.log('✅ Lighthouse audit completed successfully!');

  // Read and parse the report
  const report = JSON.parse(fs.readFileSync('lighthouse-report.json', 'utf8'));

  // Extract scores
  const scores = {
    performance: Math.round(report.categories.performance.score * 100),
    accessibility: Math.round(report.categories.accessibility.score * 100),
    'best-practices': Math.round(report.categories['best-practices'].score * 100),
    seo: Math.round(report.categories.seo.score * 100)
  };

  console.log('\n📊 Lighthouse Scores:');
  console.log(`🎯 Performance: ${scores.performance}/100`);
  console.log(`♿ Accessibility: ${scores.accessibility}/100`);
  console.log(`✅ Best Practices: ${scores['best-practices']}/100`);
  console.log(`🔍 SEO: ${scores.seo}/100`);

  // Check if all scores meet the target
  const target = 90;
  const allAboveTarget = Object.values(scores).every(score => score >= target);

  if (allAboveTarget) {
    console.log(`\n🎉 All scores are above ${target}! Website meets performance targets.`);
  } else {
    console.log(`\n⚠️  Some scores are below ${target}. Review the detailed report for improvements.`);
  }

  console.log('\n📋 Detailed report saved to: lighthouse-report.json');
  console.log('🔗 View the HTML report by running: npx lighthouse ${URL} --view');

} catch (error) {
  console.error('❌ Lighthouse audit failed:', error.message);
  process.exit(1);
}