# Professional B2B Invoice Generator System

## Overview
A comprehensive invoice management system built with Laravel that allows you to create, manage, and generate professional PDF invoices for your business. The system is designed to match the style and functionality of professional B2B invoicing systems.

## Features

### 🧾 Invoice Management
- **Create Invoices**: Generate professional invoices with customizable client information
- **Edit Invoices**: Modify existing invoices before sending
- **View Invoices**: Detailed invoice view with all information
- **Duplicate Invoices**: Quickly create copies of existing invoices
- **Status Management**: Track invoice status (Draft, Sent, Paid, Overdue, Cancelled)

### 📄 Professional PDF Generation
- **High-Quality PDFs**: Generate professional PDF invoices using DomPDF
- **Customizable Templates**: Professional invoice template with company branding
- **Print-Ready**: Optimized for both screen viewing and printing
- **Responsive Design**: Clean, professional layout

### 💼 Business Features
- **Client Management**: Store and manage client information
- **Item Management**: Add multiple line items with quantities and rates
- **Tax Calculation**: Automatic tax calculation with customizable rates
- **Payment Tracking**: Track payments and outstanding balances
- **Banking Details**: Include banking information for payments
- **Terms & Conditions**: Customizable terms and notes

### 📊 Dashboard & Analytics
- **Invoice Overview**: Quick stats on total, paid, pending, and overdue invoices
- **Status Tracking**: Visual status indicators for all invoices
- **Search & Filter**: Easy navigation through invoice lists

## Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 10+
- MySQL/SQLite database

### Installation Steps

1. **Install Dependencies**
   ```bash
   composer install
   composer require barryvdh/laravel-dompdf
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```

3. **Seed Sample Data (Optional)**
   ```bash
   php artisan db:seed --class=InvoiceSeeder
   ```

4. **Start Development Server**
   ```bash
   php artisan serve
   ```

## Usage

### Accessing the Invoice System
1. Navigate to your Laravel application
2. Log in to the admin panel
3. Click on "Invoicing" in the sidebar
4. Select "Manage Invoices" or "Create Invoice"

### Creating an Invoice

1. **Basic Information**
   - Invoice date and due date
   - Payment terms (30 days, 15 days, etc.)
   - PO number (optional)

2. **Client Details**
   - Client name and address
   - Contact information (email, phone)

3. **Invoice Items**
   - Add multiple line items
   - Set quantities and rates
   - Automatic amount calculation

4. **Additional Information**
   - Tax rate (if applicable)
   - Notes and terms & conditions

### Managing Invoices

- **View**: Click on any invoice to see full details
- **Edit**: Modify invoice information
- **PDF**: Download professional PDF version
- **Status Updates**: Mark as sent, paid, etc.
- **Duplicate**: Create copies for recurring invoices

## File Structure

```
app/
├── Http/Controllers/
│   └── InvoiceController.php          # Main invoice controller
├── Models/
│   └── Invoice.php                    # Invoice model with relationships
database/
├── migrations/
│   └── 2025_09_07_190425_create_invoices_table.php
└── seeders/
    └── InvoiceSeeder.php              # Sample invoice data
resources/views/admin/invoices/
├── index.blade.php                    # Invoice list view
├── create.blade.php                   # Create invoice form
├── edit.blade.php                     # Edit invoice form
├── show.blade.php                     # Invoice detail view
└── pdf.blade.php                      # PDF template
```

## Database Schema

### Invoices Table
- `id` - Primary key
- `invoice_number` - Unique invoice identifier
- `invoice_date` - Date invoice was created
- `due_date` - Payment due date
- `payment_terms` - Payment terms (30 days, etc.)
- `po_number` - Purchase order number
- `company_name` - Your company name
- `company_address` - Company address
- `company_phone` - Company phone
- `company_email` - Company email
- `company_website` - Company website
- `account_holder_name` - Bank account holder
- `account_number` - Bank account number
- `sort_code` - Bank sort code
- `bank_name` - Bank name
- `client_name` - Client company name
- `client_address` - Client address
- `client_email` - Client email
- `client_phone` - Client phone
- `items` - JSON array of invoice items
- `subtotal` - Subtotal amount
- `tax_rate` - Tax rate percentage
- `tax_amount` - Tax amount
- `total_amount` - Total invoice amount
- `balance_due` - Outstanding balance
- `amount_paid` - Amount paid
- `status` - Invoice status
- `notes` - Additional notes
- `terms` - Terms and conditions

## Customization

### Company Information
Update the company details in the `InvoiceController.php` store method:

```php
$invoice->company_name = 'Your Company Name';
$invoice->company_address = 'Your Address';
$invoice->account_holder_name = 'YOUR COMPANY LTD';
$invoice->account_number = '12345678';
$invoice->sort_code = '12-34-56';
```

### Invoice Template
Modify the PDF template in `resources/views/admin/invoices/pdf.blade.php` to match your branding.

### Styling
The system uses Tailwind CSS for styling. Modify the classes in the Blade templates to match your design preferences.

## API Endpoints

### Invoice Management
- `GET /admin/invoices` - List all invoices
- `GET /admin/invoices/create` - Show create form
- `POST /admin/invoices` - Store new invoice
- `GET /admin/invoices/{id}` - Show invoice details
- `GET /admin/invoices/{id}/edit` - Show edit form
- `PUT /admin/invoices/{id}` - Update invoice
- `DELETE /admin/invoices/{id}` - Delete invoice

### Invoice Actions
- `GET /admin/invoices/{id}/pdf` - Download PDF
- `POST /admin/invoices/{id}/mark-sent` - Mark as sent
- `POST /admin/invoices/{id}/mark-paid` - Mark as paid
- `POST /admin/invoices/{id}/duplicate` - Duplicate invoice

## Security Features

- **Authentication Required**: All invoice routes require authentication
- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: Comprehensive validation on all inputs
- **SQL Injection Protection**: Uses Eloquent ORM for database queries

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Troubleshooting

### Common Issues

1. **PDF Generation Fails**
   - Ensure DomPDF is properly installed
   - Check file permissions for storage directory

2. **Migration Errors**
   - Check database connection
   - Ensure all previous migrations are run

3. **Styling Issues**
   - Clear view cache: `php artisan view:clear`
   - Rebuild assets: `npm run build`

## Support

For technical support or feature requests, please contact your development team or create an issue in the project repository.

## License

This invoice system is part of your property management application and follows the same licensing terms.
