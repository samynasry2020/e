"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { Button } from "@/components/ui/Button";
import { Upload } from "lucide-react";

const schema = yup.object({
  agency: yup.string().required("Agency name is required"),
  deadline: yup.date().required("Deadline is required").min(new Date(), "Deadline must be in the future"),
  projectTitle: yup.string().required("Project title is required"),
  description: yup.string().required("Project description is required"),
  budget: yup.string(),
  requirements: yup.string().required("Technical requirements are required"),
  contactName: yup.string().required("Contact name is required"),
  contactEmail: yup.string().email("Invalid email").required("Contact email is required"),
  contactPhone: yup.string(),
  consent: yup.boolean().isTrue("You must consent to our privacy policy"),
});

type FormData = yup.InferType<typeof schema>;

export function RFPForm() {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [attachments, setAttachments] = useState<File[]>([]);

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm<FormData>({
    resolver: yupResolver(schema),
  });

  const handleFileUpload = (event: React.ChangeEvent<HTMLInputElement>) => {
    const files = event.target.files;
    if (files) {
      setAttachments(Array.from(files));
    }
  };

  const onSubmit = async (data: FormData) => {
    setIsSubmitting(true);

    try {
      // Here you would typically send the data to your backend
      // For now, we'll just simulate an API call
      await new Promise((resolve) => setTimeout(resolve, 1000));

      console.log("RFP submitted:", data, "Attachments:", attachments);
      setIsSubmitted(true);
      reset();
      setAttachments([]);
    } catch (error) {
      console.error("RFP submission error:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  if (isSubmitted) {
    return (
      <div className="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
        <div className="text-green-800">
          <h3 className="text-lg font-semibold mb-2">RFP Submitted Successfully!</h3>
          <p>Your RFP has been received and is under review. Our team will contact you within 48 hours to discuss next steps.</p>
          <Button
            variant="outline"
            className="mt-4"
            onClick={() => setIsSubmitted(false)}
          >
            Submit Another RFP
          </Button>
        </div>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label htmlFor="agency" className="block text-sm font-medium text-gray-700 mb-2">
            Agency/Department *
          </label>
          <input
            type="text"
            id="agency"
            {...register("agency")}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="e.g., Department of Defense"
          />
          {errors.agency && (
            <p className="mt-1 text-sm text-red-600">{errors.agency.message}</p>
          )}
        </div>

        <div>
          <label htmlFor="deadline" className="block text-sm font-medium text-gray-700 mb-2">
            RFP Deadline *
          </label>
          <input
            type="date"
            id="deadline"
            {...register("deadline")}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          />
          {errors.deadline && (
            <p className="mt-1 text-sm text-red-600">{errors.deadline.message}</p>
          )}
        </div>
      </div>

      <div>
        <label htmlFor="projectTitle" className="block text-sm font-medium text-gray-700 mb-2">
          Project Title *
        </label>
        <input
          type="text"
          id="projectTitle"
          {...register("projectTitle")}
          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          placeholder="Brief project title or RFP number"
        />
        {errors.projectTitle && (
          <p className="mt-1 text-sm text-red-600">{errors.projectTitle.message}</p>
        )}
      </div>

      <div>
        <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-2">
          Project Description *
        </label>
        <textarea
          id="description"
          rows={4}
          {...register("description")}
          className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          placeholder="Describe the project scope, objectives, and requirements..."
        />
        {errors.description && (
          <p className="mt-1 text-sm text-red-600">{errors.description.message}</p>
        )}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label htmlFor="budget" className="block text-sm font-medium text-gray-700 mb-2">
            Budget Range
          </label>
          <select
            id="budget"
            {...register("budget")}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          >
            <option value="">Select budget range</option>
            <option value="under-50k">Under $50,000</option>
            <option value="50k-100k">$50,000 - $100,000</option>
            <option value="100k-500k">$100,000 - $500,000</option>
            <option value="500k-1m">$500,000 - $1,000,000</option>
            <option value="over-1m">Over $1,000,000</option>
          </select>
        </div>

        <div>
          <label htmlFor="requirements" className="block text-sm font-medium text-gray-700 mb-2">
            Technical Requirements *
          </label>
          <textarea
            id="requirements"
            rows={3}
            {...register("requirements")}
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Specific technical requirements, quantities, etc."
          />
          {errors.requirements && (
            <p className="mt-1 text-sm text-red-600">{errors.requirements.message}</p>
          )}
        </div>
      </div>

      {/* File Upload */}
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-2">
          RFP Documents & Attachments
        </label>
        <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
          <Upload className="mx-auto h-12 w-12 text-gray-400" />
          <div className="mt-4">
            <label htmlFor="file-upload" className="cursor-pointer">
              <span className="mt-2 block text-sm font-medium text-primary">
                Upload RFP documents
              </span>
              <input
                id="file-upload"
                name="file-upload"
                type="file"
                multiple
                className="sr-only"
                onChange={handleFileUpload}
                accept=".pdf,.doc,.docx,.txt"
              />
            </label>
            <p className="mt-1 text-xs text-gray-500">
              PDF, DOC, DOCX up to 10MB each
            </p>
          </div>
          {attachments.length > 0 && (
            <div className="mt-4">
              <p className="text-sm text-gray-600">
                {attachments.length} file{attachments.length > 1 ? 's' : ''} selected
              </p>
            </div>
          )}
        </div>
      </div>

      <div className="border-t pt-6">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label htmlFor="contactName" className="block text-sm font-medium text-gray-700 mb-2">
              Contact Name *
            </label>
            <input
              type="text"
              id="contactName"
              {...register("contactName")}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            />
            {errors.contactName && (
              <p className="mt-1 text-sm text-red-600">{errors.contactName.message}</p>
            )}
          </div>

          <div>
            <label htmlFor="contactEmail" className="block text-sm font-medium text-gray-700 mb-2">
              Contact Email *
            </label>
            <input
              type="email"
              id="contactEmail"
              {...register("contactEmail")}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            />
            {errors.contactEmail && (
              <p className="mt-1 text-sm text-red-600">{errors.contactEmail.message}</p>
            )}
          </div>

          <div>
            <label htmlFor="contactPhone" className="block text-sm font-medium text-gray-700 mb-2">
              Contact Phone
            </label>
            <input
              type="tel"
              id="contactPhone"
              {...register("contactPhone")}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <div className="flex items-start space-x-2">
        <input
          type="checkbox"
          id="consent"
          {...register("consent")}
          className="mt-1 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
        />
        <label htmlFor="consent" className="text-sm text-gray-700">
          I consent to the collection and processing of my personal data as described in the{" "}
          <a href="/legal/privacy" className="text-primary hover:underline">
            Privacy Policy
          </a>
          . *
        </label>
      </div>
      {errors.consent && (
        <p className="text-sm text-red-600">{errors.consent.message}</p>
      )}

      <div>
        <Button
          type="submit"
          className="w-full"
          disabled={isSubmitting}
        >
          {isSubmitting ? "Submitting RFP..." : "Submit RFP for Review"}
        </Button>
      </div>

      <p className="text-xs text-gray-500">
        We'll review your RFP and contact you within 48 hours to discuss next steps.
        All information is kept strictly confidential.
      </p>
    </form>
  );
}