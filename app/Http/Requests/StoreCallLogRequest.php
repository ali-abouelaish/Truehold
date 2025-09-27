<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallLogRequest extends FormRequest
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
        $isNoAnswer = request('call_status') === 'no_answer';
        
        return [
            // Call Metadata
            'call_type' => ['required', 'in:outbound,inbound,follow_up,voicemail,sms_whatsapp'],
            'call_status' => ['required', 'in:connected,no_answer,wrong_number,voicemail,callback_requested'],
            'call_datetime' => ['nullable', 'date'],
            
            // Landlord Details
            'landlord_name' => [$isNoAnswer ? 'nullable' : 'required', 'string', 'max:255'],
            'landlord_phone' => ['nullable', 'string', 'max:20'],
            'landlord_email' => ['nullable', 'email', 'max:255'],
            'landlord_company' => ['nullable', 'string', 'max:255'],
            'contact_source' => [$isNoAnswer ? 'nullable' : 'required', 'in:gumtree,spareroom,zoopla,rightmove,referral,other'],
            
            // Property Details
            'property_address' => [$isNoAnswer ? 'nullable' : 'required', 'string', 'max:500'],
            'property_type' => [$isNoAnswer ? 'nullable' : 'required', 'in:studio,one_bed,two_bed,hmo,other'],
            'advertised_rent' => [$isNoAnswer ? 'nullable' : 'required', 'numeric', 'min:0', 'max:99999.99'],
            'availability_date' => ['nullable', 'date'],
            'vacant_keys' => ['boolean'],
            'furnished' => [$isNoAnswer ? 'nullable' : 'required', 'in:furnished,unfurnished,part_furnished,other'],
            
            // Discovery & Compliance
            'works_pending' => ['nullable', 'string'],
            'compliance_epc' => ['boolean'],
            'compliance_eicr' => ['boolean'],
            'compliance_gas' => ['boolean'],
            'compliance_licence' => ['boolean'],
            'landlord_priority' => [$isNoAnswer ? 'nullable' : 'required', 'in:speed,best_price,hands_off,other'],
            'discovery_notes' => ['nullable', 'string'],
            
            // Offer Presentation
            'packages_discussed' => ['nullable', 'array'],
            'landlord_preference' => [$isNoAnswer ? 'nullable' : 'required', 'in:full_management,top_up,let_only,undecided'],
            
            // Objection Handling
            'objections' => ['nullable', 'array'],
            
            // Outcome & Next Steps
            'viewing_booked' => ['boolean'],
            'viewing_datetime' => ['nullable', 'date'],
            'follow_up_needed' => ['boolean'],
            'follow_up_datetime' => ['nullable', 'date'],
            'next_step_status' => [$isNoAnswer ? 'nullable' : 'required', 'in:send_terms,send_compliance_docs,awaiting_response,collect_keys,tenant_reference_started,other'],
            'call_outcome' => ['required', 'in:instruction_won,pending,lost,not_interested'],
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
            'call_type.required' => 'Call type is required.',
            'call_type.in' => 'Invalid call type selected.',
            'call_status.required' => 'Call status is required.',
            'call_status.in' => 'Invalid call status selected.',
            'landlord_name.required' => 'Landlord name is required.',
            'contact_source.required' => 'Contact source is required.',
            'contact_source.in' => 'Invalid contact source selected.',
            'property_address.required' => 'Property address is required.',
            'property_type.required' => 'Property type is required.',
            'property_type.in' => 'Invalid property type selected.',
            'advertised_rent.required' => 'Advertised rent is required.',
            'advertised_rent.numeric' => 'Advertised rent must be a number.',
            'advertised_rent.min' => 'Advertised rent cannot be negative.',
            'furnished.required' => 'Furnished status is required.',
            'furnished.in' => 'Invalid furnished status selected.',
            'landlord_priority.required' => 'Landlord priority is required.',
            'landlord_priority.in' => 'Invalid landlord priority selected.',
            'landlord_preference.required' => 'Landlord preference is required.',
            'landlord_preference.in' => 'Invalid landlord preference selected.',
            'next_step_status.required' => 'Next step status is required.',
            'next_step_status.in' => 'Invalid next step status selected.',
            'call_outcome.required' => 'Call outcome is required.',
            'call_outcome.in' => 'Invalid call outcome selected.',
            'landlord_email.email' => 'Please provide a valid email address.',
        ];
    }

}
