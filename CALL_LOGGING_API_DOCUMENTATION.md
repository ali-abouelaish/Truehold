# Call Logging Web Documentation

## Overview
The Call Logging feature allows agents to track and manage their calls with landlords, including property details, compliance information, and follow-up actions.

## Database Schema

### Call Logs Table Structure
- **agent_id**: Foreign key to users table (automatically set to logged-in user)
- **call_type**: outbound, inbound, follow_up, voicemail, sms_whatsapp
- **call_status**: connected, no_answer, wrong_number, voicemail, callback_requested
- **call_datetime**: Timestamp (defaults to now)
- **landlord_name**: Required string
- **landlord_phone**: Optional string
- **landlord_email**: Optional email
- **landlord_company**: Optional string
- **contact_source**: gumtree, spareroom, zoopla, rightmove, referral, other
- **property_address**: Required string
- **property_type**: studio, one_bed, two_bed, hmo, other
- **advertised_rent**: Required decimal (10,2)
- **availability_date**: Optional date
- **vacant_keys**: Boolean (default false)
- **furnished**: furnished, unfurnished, part_furnished, other
- **works_pending**: Optional text
- **compliance_epc**: Boolean (default false)
- **compliance_eicr**: Boolean (default false)
- **compliance_gas**: Boolean (default false)
- **compliance_licence**: Boolean (default false)
- **landlord_priority**: speed, best_price, hands_off, other
- **discovery_notes**: Optional text
- **packages_discussed**: Optional JSON array
- **landlord_preference**: full_management, top_up, let_only, undecided
- **objections**: Optional JSON array
- **objection_response**: Optional text
- **viewing_booked**: Boolean (default false)
- **viewing_datetime**: Optional timestamp
- **follow_up_needed**: Boolean (default false)
- **follow_up_datetime**: Optional timestamp
- **next_step_status**: send_terms, send_compliance_docs, awaiting_response, collect_keys, tenant_reference_started, other
- **call_outcome**: instruction_won, pending, lost, not_interested
- **agent_notes**: Optional text
- **send_sms**: Boolean (default false)
- **send_email**: Boolean (default false)
- **send_whatsapp**: Boolean (default false)

## Web Routes (Admin Panel)
All routes are prefixed with `/admin` and require authentication.

- `GET /admin/call-logs` - List all call logs (with filtering)
- `GET /admin/call-logs/create` - Show create form
- `POST /admin/call-logs` - Store new call log
- `GET /admin/call-logs/{id}` - Show specific call log
- `GET /admin/call-logs/{id}/edit` - Show edit form
- `PUT /admin/call-logs/{id}` - Update call log
- `DELETE /admin/call-logs/{id}` - Delete call log
- `GET /admin/call-logs/stats` - Get statistics
- `GET /admin/call-logs/follow-ups` - Get follow-up needed calls
- `GET /admin/call-logs/recent` - Get recent calls for authenticated agent

## Usage Examples

### Creating a Call Log (POST /admin/call-logs)
Form submission with the following fields:
- `call_type`: outbound, inbound, follow_up, voicemail, sms_whatsapp
- `call_status`: connected, no_answer, wrong_number, voicemail, callback_requested
- `landlord_name`: Required string
- `landlord_phone`: Optional string
- `landlord_email`: Optional email
- `contact_source`: gumtree, spareroom, zoopla, rightmove, referral, other
- `property_address`: Required string
- `property_type`: studio, one_bed, two_bed, hmo, other
- `advertised_rent`: Required decimal
- `furnished`: furnished, unfurnished, part_furnished, other
- `landlord_priority`: speed, best_price, hands_off, other
- `landlord_preference`: full_management, top_up, let_only, undecided
- `next_step_status`: send_terms, send_compliance_docs, awaiting_response, collect_keys, tenant_reference_started, other
- `call_outcome`: instruction_won, pending, lost, not_interested

### Filtering Call Logs (GET /admin/call-logs)
Query parameters:
- `agent_id`: Filter by specific agent
- `call_status`: Filter by call status
- `call_type`: Filter by call type
- `landlord_name`: Search by landlord name (partial match)
- `property_address`: Search by property address (partial match)
- `date_from`: Filter from date
- `date_to`: Filter to date
- `call_outcome`: Filter by outcome
- `per_page`: Number of results per page (default: 15)

Example: `/admin/call-logs?call_status=connected&call_outcome=instruction_won&per_page=20`

### Getting Statistics (GET /admin/call-logs/stats)
Returns statistics including:
- Total calls count
- Calls by type (outbound, inbound, follow_up, etc.)
- Calls by status (connected, no_answer, voicemail, etc.)
- Calls by outcome (instruction_won, pending, lost, etc.)
- Follow-up needed count
- Viewing booked count

## Security Features

1. **Authentication Required**: All endpoints require user authentication
2. **Agent Isolation**: Agents can only access their own call logs (unless admin)
3. **Admin Override**: Admin users can access all call logs
4. **Input Validation**: Comprehensive validation on all inputs
5. **Mass Assignment Protection**: Only fillable fields can be updated

## Model Relationships

- `CallLog` belongs to `User` (agent)
- `User` has many `CallLog` (call logs)

## Query Scopes Available

- `forAgent($agentId)`: Filter by agent
- `byStatus($status)`: Filter by call status
- `byType($type)`: Filter by call type
- `byLandlord($name)`: Search by landlord name
- `byProperty($address)`: Search by property address
- `byDateRange($startDate, $endDate)`: Filter by date range

## Next Steps

1. ✅ Run `php artisan migrate` to create the database table (Already completed)
2. The system is ready to use immediately through web routes
3. Create frontend Blade views for better user experience
4. Implement automated follow-up reminders based on `follow_up_datetime`
5. Add email/SMS integration using the automation hooks
