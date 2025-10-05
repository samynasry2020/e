'use client';

import { useState, FormEvent } from 'react';
import { RFPFormData } from '@/lib/types';

export default function RFPForm() {
  const [formData, setFormData] = useState<Omit<RFPFormData, 'attachments'>>({
    name: '',
    email: '',
    phone: '',
    agency: '',
    deadline: '',
    projectDescription: '',
    budgetRange: '',
    specifications: '',
    consentPrivacy: false,
    honeypot: '',
  });

  const [errors, setErrors] = useState<Partial<Record<keyof RFPFormData, string>>>({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState<'idle' | 'success' | 'error'>('idle');

  const budgetRanges = [
    'Under $25,000',
    '$25,000 - $100,000',
    '$100,000 - $500,000',
    '$500,000 - $1,000,000',
    'Over $1,000,000',
    'Undisclosed / TBD',
  ];

  const validateForm = (): boolean => {
    const newErrors: Partial<Record<keyof RFPFormData, string>> = {};

    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
    }

    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = 'Please enter a valid email address';
    }

    if (!formData.phone.trim()) {
      newErrors.phone = 'Phone number is required for RFP submissions';
    }

    if (!formData.agency.trim()) {
      newErrors.agency = 'Agency/organization name is required';
    }

    if (!formData.deadline) {
      newErrors.deadline = 'Bid deadline is required';
    }

    if (!formData.projectDescription.trim()) {
      newErrors.projectDescription = 'Project description is required';
    } else if (formData.projectDescription.trim().length < 20) {
      newErrors.projectDescription = 'Please provide more details (minimum 20 characters)';
    }

    if (!formData.specifications.trim()) {
      newErrors.specifications = 'Technical specifications are required';
    }

    if (!formData.consentPrivacy) {
      newErrors.consentPrivacy = 'You must agree to the privacy policy';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    // Check honeypot
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
      const response = await fetch('/api/rfp', {
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
          agency: '',
          deadline: '',
          projectDescription: '',
          budgetRange: '',
          specifications: '',
          consentPrivacy: false,
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
      {/* Honeypot */}
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

      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 className="font-semibold text-blue-900 mb-2">📋 RFP / Bid Support Request</h3>
        <p className="text-sm text-blue-800">
          Complete this form to request a proposal for your government or enterprise IT procurement. 
          We respond to RFP inquiries within 4 hours during business days.
        </p>
      </div>

      {/* Contact Information */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="form-group">
          <label htmlFor="rfp-name" className="form-label">
            Full Name <span className="text-error">*</span>
          </label>
          <input
            type="text"
            id="rfp-name"
            value={formData.name}
            onChange={(e) => setFormData({ ...formData, name: e.target.value })}
            className={`form-input ${errors.name ? 'border-error' : ''}`}
            aria-required="true"
            aria-invalid={!!errors.name}
          />
          {errors.name && <div className="form-error">{errors.name}</div>}
        </div>

        <div className="form-group">
          <label htmlFor="rfp-email" className="form-label">
            Email Address <span className="text-error">*</span>
          </label>
          <input
            type="email"
            id="rfp-email"
            value={formData.email}
            onChange={(e) => setFormData({ ...formData, email: e.target.value })}
            className={`form-input ${errors.email ? 'border-error' : ''}`}
            aria-required="true"
            aria-invalid={!!errors.email}
          />
          {errors.email && <div className="form-error">{errors.email}</div>}
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="form-group">
          <label htmlFor="rfp-phone" className="form-label">
            Phone Number <span className="text-error">*</span>
          </label>
          <input
            type="tel"
            id="rfp-phone"
            value={formData.phone}
            onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
            className={`form-input ${errors.phone ? 'border-error' : ''}`}
            aria-required="true"
            aria-invalid={!!errors.phone}
          />
          {errors.phone && <div className="form-error">{errors.phone}</div>}
        </div>

        <div className="form-group">
          <label htmlFor="rfp-agency" className="form-label">
            Agency / Organization <span className="text-error">*</span>
          </label>
          <input
            type="text"
            id="rfp-agency"
            value={formData.agency}
            onChange={(e) => setFormData({ ...formData, agency: e.target.value })}
            className={`form-input ${errors.agency ? 'border-error' : ''}`}
            placeholder="e.g., Department of Defense, State of California"
            aria-required="true"
            aria-invalid={!!errors.agency}
          />
          {errors.agency && <div className="form-error">{errors.agency}</div>}
        </div>
      </div>

      {/* Project Details */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="form-group">
          <label htmlFor="rfp-deadline" className="form-label">
            Bid Deadline <span className="text-error">*</span>
          </label>
          <input
            type="date"
            id="rfp-deadline"
            value={formData.deadline}
            onChange={(e) => setFormData({ ...formData, deadline: e.target.value })}
            className={`form-input ${errors.deadline ? 'border-error' : ''}`}
            aria-required="true"
            aria-invalid={!!errors.deadline}
          />
          {errors.deadline && <div className="form-error">{errors.deadline}</div>}
        </div>

        <div className="form-group">
          <label htmlFor="rfp-budget" className="form-label">
            Budget Range <span className="text-gray-500">(Optional)</span>
          </label>
          <select
            id="rfp-budget"
            value={formData.budgetRange}
            onChange={(e) => setFormData({ ...formData, budgetRange: e.target.value })}
            className="form-select"
          >
            <option value="">Select budget range</option>
            {budgetRanges.map((range) => (
              <option key={range} value={range}>
                {range}
              </option>
            ))}
          </select>
        </div>
      </div>

      <div className="form-group">
        <label htmlFor="rfp-description" className="form-label">
          Project Description <span className="text-error">*</span>
        </label>
        <textarea
          id="rfp-description"
          value={formData.projectDescription}
          onChange={(e) => setFormData({ ...formData, projectDescription: e.target.value })}
          className={`form-textarea ${errors.projectDescription ? 'border-error' : ''}`}
          rows={4}
          placeholder="Describe the project scope, objectives, and key requirements..."
          aria-required="true"
          aria-invalid={!!errors.projectDescription}
        />
        {errors.projectDescription && <div className="form-error">{errors.projectDescription}</div>}
      </div>

      <div className="form-group">
        <label htmlFor="rfp-specs" className="form-label">
          Technical Specifications <span className="text-error">*</span>
        </label>
        <textarea
          id="rfp-specs"
          value={formData.specifications}
          onChange={(e) => setFormData({ ...formData, specifications: e.target.value })}
          className={`form-textarea ${errors.specifications ? 'border-error' : ''}`}
          rows={6}
          placeholder="List specific technical requirements: quantities, models, performance specs, compatibility needs, etc."
          aria-required="true"
          aria-invalid={!!errors.specifications}
        />
        {errors.specifications && <div className="form-error">{errors.specifications}</div>}
        <p className="text-sm text-gray-600 mt-2">
          💡 Tip: Include part numbers, NAICS codes, or reference comparable systems for faster response.
        </p>
      </div>

      {/* Privacy Consent */}
      <div className="form-group">
        <div className="flex items-start">
          <input
            type="checkbox"
            id="rfp-consent"
            checked={formData.consentPrivacy}
            onChange={(e) => setFormData({ ...formData, consentPrivacy: e.target.checked })}
            className={`form-checkbox mt-1 ${errors.consentPrivacy ? 'border-error' : ''}`}
            aria-required="true"
            aria-invalid={!!errors.consentPrivacy}
          />
          <label htmlFor="rfp-consent" className="ml-3 text-sm text-gray-700">
            I agree to the{' '}
            <a href="/privacy" className="text-primary hover:underline" target="_blank" rel="noopener noreferrer">
              Privacy Policy
            </a>{' '}
            and consent to PU Build contacting me regarding this RFP. <span className="text-error">*</span>
          </label>
        </div>
        {errors.consentPrivacy && <div className="form-error ml-8">{errors.consentPrivacy}</div>}
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
              <span className="loading mr-2"></span>
              Submitting...
            </>
          ) : (
            'Submit RFP Request'
          )}
        </button>
      </div>

      {/* Status Messages */}
      {submitStatus === 'success' && (
        <div className="p-4 bg-green-50 border border-green-200 rounded-lg text-green-800" role="alert">
          <strong>✅ RFP Request Submitted!</strong>
          <p className="mt-2">
            Thank you for your submission. Our team will review your requirements and respond within 4 business hours.
            You'll receive a confirmation email at {formData.email}.
          </p>
        </div>
      )}

      {submitStatus === 'error' && (
        <div className="p-4 bg-red-50 border border-red-200 rounded-lg text-red-800" role="alert">
          <strong>❌ Submission Error</strong>
          <p className="mt-2">
            We encountered an issue submitting your RFP request. Please email us directly at{' '}
            <a href="mailto:sales@pubuild.com" className="underline">
              sales@pubuild.com
            </a>{' '}
            or call <a href="tel:8004741388" className="underline">(800) 474-1388</a>.
          </p>
        </div>
      )}

      <div className="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-6">
        <p className="text-sm text-gray-700">
          <strong>📞 Urgent RFP?</strong> For time-sensitive procurement requests, call us directly at{' '}
          <a href="tel:8004741388" className="text-primary hover:underline font-semibold">
            (800) 474-1388
          </a>{' '}
          during business hours (Mon-Fri, 8 AM - 6 PM PT).
        </p>
      </div>
    </form>
  );
}
