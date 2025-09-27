<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCallLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Call Metadata
            'call_type' => ['sometimes', 'in:outbound,inbound,follow_up,voicemail,sms_whatsapp'],
            'call_status' => ['sometimes', 'in:connected,no_answer,wrong_number,voicemail,callback_requested'],
            'call_datetime' => ['nullable', 'date'],
            
            // Landlord Details
            'landlord_name' => ['sometimes', 'string', 'max:255'],
            'landlord_phone' => ['nullable', 'string', 'max:20'],
            'landlord_email' => ['nullable', 'email', 'max:255'],
            'landlord_company' => ['nullable', 'string', 'max:255'],
            'contact_source' => ['sometimes', 'in:gumtree,spareroom,zoopla,rightmove,referral,other'],
            
            // Property Details
            'property_address' => ['sometimes', 'string', 'max:500'],
            'property_type' => ['sometimes', 'in:studio,one_bed,two_bed,hmo,other'],
            'advertised_rent' => ['sometimes', 'numeric', 'min:0', 'max:99999.99'],
            'availability_date' => ['nullable', 'date'],
            'vacant_keys' => ['boolean'],
            'furnished' => ['sometimes', 'in:furnished,unfurnished,part_furnished,other'],
            
            // Discovery & Compliance
            'works_pending' => ['nullable', 'string'],
            'compliance_epc' => ['boolean'],
            'compliance_eicr' => ['boolean'],
            'compliance_gas' => ['boolean'],
            'compliance_licence' => ['boolean'],
            'landlord_priority' => ['sometimes', 'in:speed,best_price,hands_off,other'],
            'discovery_notes' => ['nullable', 'string'],
            
            // Offer Presentation
            'packages_discussed' => ['nullable', 'array'],
            'landlord_preference' => ['sometimes', 'in:full_management,top_up,let_only,undecided'],
            
            // Objection Handling
            'objections' => ['nullable', 'array'],
            'objection_response' => ['nullable', 'string'],
            
            // Outcome & Next Steps
            'viewing_booked' => ['boolean'],
            'viewing_datetime' => ['nullable', 'date'],
            'follow_up_needed' => ['boolean'],
            'follow_up_datetime' => ['nullable', 'date'],
            'next_step_status' => ['sometimes', 'in:send_terms,send_compliance_docs,awaiting_response,collect_keys,tenant_reference_started,other'],
            'call_outcome' => ['sometimes', 'in:instruction_won,pending,lost,not_interested'],
            'agent_notes' => ['nullable', 'string'],
            
            // Automation Hooks
            'send_sms' => ['boolean'],
            'send_email' => ['boolean'],
            'send_whatsapp' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'call_type.in' => 'Invalid call type selected.',
            'call_status.in' => 'Invalid call status selected.',
            'landlord_name.string' => 'Landlord name must be a string.',
            'contact_source.in' => 'Invalid contact source selected.',
            'property_address.string' => 'Property address must be a string.',
            'property_type.in' => 'Invalid property type selected.',
            'advertised_rent.numeric' => 'Advertised rent must be a number.',
            'advertised_rent.min' => 'Advertised rent cannot be negative.',
            'furnished.in' => 'Invalid furnished status selected.',
            'landlord_priority.in' => 'Invalid landlord priority selected.',
            'landlord_preference.in' => 'Invalid landlord preference selected.',
            'next_step_status.in' => 'Invalid next step status selected.',
            'call_outcome.in' => 'Invalid call outcome selected.',
            'landlord_email.email' => 'Please provide a valid email address.',
        ];
    }
}
