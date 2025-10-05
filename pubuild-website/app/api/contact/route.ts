import { NextRequest, NextResponse } from 'next/server';
import { COMPANY } from '@/lib/constants';

// Rate limiting (simple in-memory implementation)
const rateLimitMap = new Map<string, { count: number; resetTime: number }>();
const RATE_LIMIT = 5; // 5 requests
const RATE_LIMIT_WINDOW = 15 * 60 * 1000; // 15 minutes

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
        { error: 'Too many requests. Please try again later.' },
        { status: 429 }
      );
    }

    const body = await request.json();
    const { name, email, phone, company, message, consentPrivacy, newsletter, honeypot } = body;

    // Check honeypot (spam protection)
    if (honeypot) {
      console.log('Spam detected via honeypot');
      return NextResponse.json({ success: true }); // Return success to avoid revealing spam detection
    }

    // Validate required fields
    if (!name || !email || !message || !consentPrivacy) {
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

    // In production, send email here using a service like:
    // - SendGrid
    // - AWS SES
    // - Mailgun
    // - Nodemailer with SMTP

    // For now, log the submission
    console.log('Contact form submission:', {
      name,
      email,
      phone: phone || 'Not provided',
      company: company || 'Not provided',
      message,
      newsletter: newsletter || false,
      timestamp: new Date().toISOString(),
    });

    // Email template (example)
    const emailBody = `
New Contact Form Submission from ${COMPANY.domain}

Name: ${name}
Email: ${email}
Phone: ${phone || 'Not provided'}
Company: ${company || 'Not provided'}
Newsletter Signup: ${newsletter ? 'Yes' : 'No'}

Message:
${message}

---
Submitted at: ${new Date().toLocaleString()}
IP Address: ${ip}
    `.trim();

    // TODO: Send email to ${COMPANY.email}
    console.log('Email to send:', emailBody);

    // Send auto-reply to user (optional)
    // TODO: Implement auto-reply

    return NextResponse.json(
      { success: true, message: 'Your message has been received. We will respond within 24 hours.' },
      { status: 200 }
    );
  } catch (error) {
    console.error('Contact form error:', error);
    return NextResponse.json(
      { error: 'An error occurred. Please try again or email us directly.' },
      { status: 500 }
    );
  }
}
