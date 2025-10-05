import { NextRequest, NextResponse } from 'next/server';
import { COMPANY } from '@/lib/constants';

// Rate limiting (simple in-memory implementation)
const rateLimitMap = new Map<string, { count: number; resetTime: number }>();
const RATE_LIMIT = 3; // 3 requests (RFPs are more limited)
const RATE_LIMIT_WINDOW = 60 * 60 * 1000; // 1 hour

function checkRateLimit(ip: string): boolean {
  const now = Date.now();
  const record = rateLimitMap.get(ip);

  if (!record || now > record.resetTime) {
    rateLimitMap.set(ip, { count: 1, resetTime: now + RATE_LIMIT_WINDOW });
    return true;
  }

  if (record.count >= RATE_LIMIT) {
    return false;
  }

  record.count++;
  return true;
}

export async function POST(request: NextRequest) {
  try {
    // Get IP address for rate limiting
    const ip = request.headers.get('x-forwarded-for') || request.headers.get('x-real-ip') || 'unknown';

    // Check rate limit
    if (!checkRateLimit(ip)) {
      return NextResponse.json(
        { error: 'Too many RFP submissions. Please contact us directly at ' + COMPANY.phone },
        { status: 429 }
      );
    }

    const body = await request.json();
    const {
      name,
      email,
      phone,
      agency,
      deadline,
      projectDescription,
      budgetRange,
      specifications,
      consentPrivacy,
      honeypot,
    } = body;

    // Check honeypot (spam protection)
    if (honeypot) {
      console.log('Spam detected via honeypot');
      return NextResponse.json({ success: true }); // Return success to avoid revealing spam detection
    }

    // Validate required fields
    if (!name || !email || !phone || !agency || !deadline || !projectDescription || !specifications || !consentPrivacy) {
      return NextResponse.json(
        { error: 'Missing required fields' },
        { status: 400 }
      );
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return NextResponse.json(
        { error: 'Invalid email address' },
        { status: 400 }
      );
    }

    // Log the RFP submission (high priority)
    console.log('🚨 RFP SUBMISSION (HIGH PRIORITY):', {
      name,
      email,
      phone,
      agency,
      deadline,
      budgetRange: budgetRange || 'Not specified',
      timestamp: new Date().toISOString(),
    });

    // Email template for RFP
    const emailBody = `
🚨 HIGH PRIORITY: New RFP Request from ${COMPANY.domain}

CONTACT INFORMATION:
Name: ${name}
Email: ${email}
Phone: ${phone}
Agency/Organization: ${agency}

BID DETAILS:
Deadline: ${deadline}
Budget Range: ${budgetRange || 'Not specified'}

PROJECT DESCRIPTION:
${projectDescription}

TECHNICAL SPECIFICATIONS:
${specifications}

---
Submitted at: ${new Date().toLocaleString()}
IP Address: ${ip}
Response SLA: 4 hours
    `.trim();

    // TODO: Send email to ${COMPANY.email} with HIGH PRIORITY flag
    console.log('Email to send:', emailBody);

    // TODO: Send SMS/Slack notification for urgent RFP
    // Example: Notify team via Slack webhook

    // Send auto-reply confirmation to user
    // TODO: Implement auto-reply with RFP confirmation

    return NextResponse.json(
      {
        success: true,
        message: 'Your RFP request has been received. Our team will respond within 4 business hours.',
      },
      { status: 200 }
    );
  } catch (error) {
    console.error('RFP form error:', error);
    return NextResponse.json(
      { error: 'An error occurred. Please call us directly at ' + COMPANY.phone },
      { status: 500 }
    );
  }
}
