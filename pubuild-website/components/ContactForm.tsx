'use client';

import { useState, FormEvent } from 'react';
import { ContactFormData } from '@/lib/types';

export default function ContactForm() {
  const [formData, setFormData] = useState<ContactFormData>({
    name: '',
    email: '',
    phone: '',
    company: '',
    message: '',
    consentPrivacy: false,
    newsletter: false,
    honeypot: '', // Anti-spam honeypot field
  });

  const [errors, setErrors] = useState<Partial<Record<keyof ContactFormData, string>>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState<'idle' | 'success' | 'error'>('idle');

  const validateForm = (): boolean => {
    const newErrors: Partial<Record<keyof ContactFormData, string>> = {};

    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
    }

    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = 'Please enter a valid email address';
    }

    if (!formData.message.trim()) {
      newErrors.message = 'Message is required';
    } else if (formData.message.trim().length < 10) {
      newErrors.message = 'Message must be at least 10 characters';
    }

    if (!formData.consentPrivacy) {
      newErrors.consentPrivacy = 'You must agree to the privacy policy';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    // Check honeypot (if filled, it's likely a bot)
    if (formData.honeypot) {
      console.log('Spam detected');
      return;
    }

    if (!validateForm()) {
      return;
    }

    setIsSubmitting(true);
    setSubmitStatus('idle');

    try {
      const response = await fetch('/api/contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData),
      });

      if (response.ok) {
        setSubmitStatus('success');
        setFormData({
          name: '',
          email: '',
          phone: '',
          company: '',
          message: '',
          consentPrivacy: false,
          newsletter: false,
          honeypot: '',
        });
      } else {
        setSubmitStatus('error');
      }
    } catch (error) {
      console.error('Form submission error:', error);
      setSubmitStatus('error');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6" noValidate>
      {/* Honeypot field - hidden from users */}
      <input
        type="text"
        name="website"
        value={formData.honeypot}
        onChange={(e) => setFormData({ ...formData, honeypot: e.target.value })}
        className="sr-only"
        tabIndex={-1}
        autoComplete="off"
        aria-hidden="true"
      />

      {/* Name */}
      <div className="form-group">
        <label htmlFor="contact-name" className="form-label">
          Full Name <span className="text-error" aria-label="required">*</span>
        </label>
        <input
          type="text"
          id="contact-name"
          name="name"
          value={formData.name}
          onChange={(e) => setFormData({ ...formData, name: e.target.value })}
          className={`form-input ${errors.name ? 'border-error' : ''}`}
          aria-required="true"
          aria-invalid={!!errors.name}
          aria-describedby={errors.name ? 'name-error' : undefined}
        />
        {errors.name && (
          <div id="name-error" className="form-error" role="alert">
            {errors.name}
          </div>
        )}
      </div>

      {/* Email */}
      <div className="form-group">
        <label htmlFor="contact-email" className="form-label">
          Email Address <span className="text-error" aria-label="required">*</span>
        </label>
        <input
          type="email"
          id="contact-email"
          name="email"
          value={formData.email}
          onChange={(e) => setFormData({ ...formData, email: e.target.value })}
          className={`form-input ${errors.email ? 'border-error' : ''}`}
          aria-required="true"
          aria-invalid={!!errors.email}
          aria-describedby={errors.email ? 'email-error' : undefined}
        />
        {errors.email && (
          <div id="email-error" className="form-error" role="alert">
            {errors.email}
          </div>
        )}
      </div>

      {/* Phone (optional) */}
      <div className="form-group">
        <label htmlFor="contact-phone" className="form-label">
          Phone Number <span className="text-gray-500">(Optional)</span>
        </label>
        <input
          type="tel"
          id="contact-phone"
          name="phone"
          value={formData.phone}
          onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
          className="form-input"
        />
      </div>

      {/* Company (optional) */}
      <div className="form-group">
        <label htmlFor="contact-company" className="form-label">
          Company / Organization <span className="text-gray-500">(Optional)</span>
        </label>
        <input
          type="text"
          id="contact-company"
          name="company"
          value={formData.company}
          onChange={(e) => setFormData({ ...formData, company: e.target.value })}
          className="form-input"
        />
      </div>

      {/* Message */}
      <div className="form-group">
        <label htmlFor="contact-message" className="form-label">
          Message <span className="text-error" aria-label="required">*</span>
        </label>
        <textarea
          id="contact-message"
          name="message"
          value={formData.message}
          onChange={(e) => setFormData({ ...formData, message: e.target.value })}
          className={`form-textarea ${errors.message ? 'border-error' : ''}`}
          rows={6}
          aria-required="true"
          aria-invalid={!!errors.message}
          aria-describedby={errors.message ? 'message-error' : undefined}
        />
        {errors.message && (
          <div id="message-error" className="form-error" role="alert">
            {errors.message}
          </div>
        )}
      </div>

      {/* Privacy Consent */}
      <div className="form-group">
        <div className="flex items-start">
          <input
            type="checkbox"
            id="contact-consent"
            name="consentPrivacy"
            checked={formData.consentPrivacy}
            onChange={(e) => setFormData({ ...formData, consentPrivacy: e.target.checked })}
            className={`form-checkbox mt-1 ${errors.consentPrivacy ? 'border-error' : ''}`}
            aria-required="true"
            aria-invalid={!!errors.consentPrivacy}
            aria-describedby={errors.consentPrivacy ? 'consent-error' : undefined}
          />
          <label htmlFor="contact-consent" className="ml-3 text-sm text-gray-700">
            I agree to the{' '}
            <a href="/privacy" className="text-primary hover:underline" target="_blank" rel="noopener noreferrer">
              Privacy Policy
            </a>{' '}
            and consent to PU Build contacting me regarding my inquiry. <span className="text-error">*</span>
          </label>
        </div>
        {errors.consentPrivacy && (
          <div id="consent-error" className="form-error ml-8" role="alert">
            {errors.consentPrivacy}
          </div>
        )}
      </div>

      {/* Newsletter (optional) */}
      <div className="form-group">
        <div className="flex items-start">
          <input
            type="checkbox"
            id="contact-newsletter"
            name="newsletter"
            checked={formData.newsletter}
            onChange={(e) => setFormData({ ...formData, newsletter: e.target.checked })}
            className="form-checkbox mt-1"
          />
          <label htmlFor="contact-newsletter" className="ml-3 text-sm text-gray-700">
            Subscribe to our newsletter for product updates and industry insights (optional)
          </label>
        </div>
      </div>

      {/* Submit Button */}
      <div>
        <button
          type="submit"
          disabled={isSubmitting}
          className="btn btn-primary btn-large w-full disabled:opacity-50 disabled:cursor-not-allowed"
          aria-busy={isSubmitting}
        >
          {isSubmitting ? (
            <>
              <span className="loading mr-2" aria-hidden="true"></span>
              Sending...
            </>
          ) : (
            'Send Message'
          )}
        </button>
      </div>

      {/* Status Messages */}
      {submitStatus === 'success' && (
        <div
          className="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800"
          role="alert"
          aria-live="polite"
        >
          <strong>Thank you!</strong> Your message has been sent successfully. We'll respond within 24 hours.
        </div>
      )}

      {submitStatus === 'error' && (
        <div
          className="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800"
          role="alert"
          aria-live="assertive"
        >
          <strong>Error:</strong> There was a problem sending your message. Please try again or email us directly at{' '}
          <a href="mailto:sales@pubuild.com" className="underline">
            sales@pubuild.com
          </a>
          .
        </div>
      )}

      <p className="text-sm text-gray-600 mt-4">
        <strong>Response Time:</strong> We typically respond to inquiries within 24 hours during business days.
        For urgent matters, please call us at <a href="tel:8004741388" className="text-primary hover:underline">(800) 474-1388</a>.
      </p>
    </form>
  );
}
