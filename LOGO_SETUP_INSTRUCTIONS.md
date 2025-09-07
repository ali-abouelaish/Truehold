# Logo Setup Instructions

## Adding the TRUEHOLD GROUP LTD Logo to Invoices

To add the actual logo image to your invoices, follow these steps:

### 1. Prepare the Logo Image
- Save your TRUEHOLD GROUP LTD logo as `truehold-logo.png`
- Recommended size: 200x80 pixels or similar aspect ratio
- Format: PNG with transparent background (preferred) or white background
- Place the file in: `public/images/truehold-logo.png`

### 2. Update the PDF Template
The PDF template is already configured to use the logo. If you want to use an image instead of the text-based logo, update this line in `resources/views/admin/invoices/pdf.blade.php`:

**Current (text-based logo):**
```html
<div class="company-logo">T</div>
<div class="company-logo-text">TRUEHOLD</div>
<div class="company-logo-subtitle">GROUP LTD</div>
```

**Replace with (image-based logo):**
```html
<img src="{{ public_path('images/truehold-logo.png') }}" alt="TRUEHOLD GROUP LTD" class="company-logo">
```

### 3. Update CSS for Image Logo
If using an image, update the CSS in the same file:

**Replace:**
```css
.company-logo {
    font-size: 48px;
    font-weight: bold;
    color: #d4af37;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    letter-spacing: 2px;
}
```

**With:**
```css
.company-logo {
    max-width: 200px;
    height: auto;
    margin-bottom: 10px;
}
```

### 4. Test the Invoice
1. Navigate to `http://127.0.0.1:8000/admin/invoices`
2. Create or view an existing invoice
3. Click "Download PDF" to see the updated design

## Current Design Features

The invoice now features:
- ✅ **Charcoal Black Theme**: Simple, professional charcoal (#2c2c2c) color scheme
- ✅ **No Shadows**: Clean, flat design without drop shadows
- ✅ **Text-Based Logo**: Stylized "T" with "TRUEHOLD GROUP LTD" text
- ✅ **Professional Layout**: Clean, business-appropriate design
- ✅ **Print-Ready**: Optimized for both screen and print

## Color Scheme
- **Primary**: Charcoal Black (#2c2c2c)
- **Accent**: Gold (#d4af37) for logo elements
- **Background**: Light Gray (#f8f8f8) for sections
- **Text**: Dark Gray (#666) for secondary text
- **Borders**: Light Gray (#e0e0e0) for subtle separation

The invoice system is now ready with a professional, simple charcoal theme that matches your business branding!
