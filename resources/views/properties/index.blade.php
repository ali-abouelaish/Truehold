<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Find your dream home with TrueHold. Browse {{ $properties->total() }}+ premium properties in prime London locations.">
    <meta name="theme-color" content="#1e3a5f">
    <title>TrueHold - Premium Property Listings</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/truehold-logo.jpg') }}">
    
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    
    <style>
/* ==========================================
   TRUEHOLD - Premium Property Listings
   Color Palette: White, Navy Blue, Gold
   ========================================== */

/* CSS Variables */
:root {
    --primary-navy: #1e3a5f;
    --navy-dark: #152a45;
    --navy-light: #2d5280;
    --gold: #d4af37;
    --gold-light: #e8c55c;
    --gold-dark: #b8941f;
    --white: #ffffff;
    --off-white: #f8f9fa;
    --light-gray: #e9ecef;
    --gray: #6c757d;
    --text-dark: #212529;
    --shadow-sm: 0 2px 4px rgba(30, 58, 95, 0.08);
    --shadow-md: 0 4px 12px rgba(30, 58, 95, 0.12);
    --shadow-lg: 0 8px 24px rgba(30, 58, 95, 0.15);
    --transition: all 0.3s ease;
}

/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: var(--text-dark);
    background-color: var(--off-white);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

a {
    text-decoration: none;
    color: inherit;
    transition: var(--transition);
    -webkit-tap-highlight-color: transparent;
}

button {
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: var(--transition);
    -webkit-tap-highlight-color: transparent;
}

/* Smooth Scrolling */
        html {
    scroll-behavior: smooth;
}

/* Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ==========================================
   NAVIGATION - MODERN DESIGN
   ========================================== */

.navbar {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    box-shadow: 0 4px 30px rgba(30, 58, 95, 0.08);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(30, 58, 95, 0.06);
}

.nav-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
}

.logo {
    display: flex;
    align-items: center;
    gap: 14px;
    font-weight: 700;
    font-size: 22px;
    color: var(--primary-navy);
    cursor: pointer;
    transition: var(--transition);
}

.logo:hover {
    transform: translateX(2px);
}

.logo-icon {
    width: 46px;
    height: 46px;
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(30, 58, 95, 0.2);
    transition: var(--transition);
}

.logo-icon svg {
    stroke: var(--gold);
    stroke-width: 2.5;
}

.logo:hover .logo-icon {
    transform: rotate(-5deg) scale(1.05);
    box-shadow: 0 6px 20px rgba(30, 58, 95, 0.3);
}

.logo-text {
    letter-spacing: 1px;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 8px;
    align-items: center;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--gray);
    font-weight: 500;
    font-size: 15px;
    padding: 12px 20px;
    border-radius: 10px;
            position: relative;
    transition: var(--transition);
}

.nav-link svg {
    stroke-width: 2;
    transition: var(--transition);
}

.nav-link:hover {
    color: var(--primary-navy);
    background-color: rgba(30, 58, 95, 0.05);
}

.nav-link:hover svg {
    transform: translateY(-2px);
}

.nav-link.active {
    color: var(--primary-navy);
    background-color: rgba(30, 58, 95, 0.08);
    font-weight: 600;
}

.nav-link.active svg {
    stroke: var(--gold);
}

.btn-agent {
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
    margin-left: 8px;
}

.btn-agent:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(212, 175, 55, 0.4);
}

/* ==========================================
   HERO HEADER - WITH BACKGROUND IMAGE
   ========================================== */

.hero-header {
    position: relative;
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
            overflow: hidden;
        }
        
.hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
    z-index: 0;
}

.hero-bg-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(30, 58, 95, 0.85) 0%,
        rgba(21, 42, 69, 0.75) 50%,
        rgba(30, 58, 95, 0.85) 100%
    );
    backdrop-filter: blur(2px);
}

.hero-content {
            position: relative;
            z-index: 2;
    text-align: center;
    color: var(--white);
    padding: 60px 0;
    width: 100%;
}

.hero-title {
    font-size: 56px;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.2;
    letter-spacing: -1px;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
    font-size: 20px;
    color: rgba(255, 255, 255, 0.95);
    margin-bottom: 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.hero-stats {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 48px;
    max-width: 900px;
    margin: 0 auto;
    padding: 32px 48px;
    background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 42px;
    font-weight: 700;
    color: var(--gold);
    margin-bottom: 8px;
    line-height: 1;
}

.stat-label {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
}

.stat-divider {
    width: 1px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
}

/* ==========================================
   FILTERS SECTION — Premium (navy + gold)
   ========================================== */

.filters-section {
    padding: 28px 0 32px;
    background: linear-gradient(180deg, #fafbfc 0%, var(--white) 40%, var(--off-white) 100%);
    border-bottom: 1px solid rgba(30, 58, 95, 0.08);
    position: relative;
}

.filters-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--gold), var(--gold-light), var(--gold), transparent);
    opacity: 0.85;
}

.filters-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.filters-toggle {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 14px 26px;
    background: var(--white);
    border: 1px solid rgba(30, 58, 95, 0.12);
    border-radius: 12px;
    color: var(--primary-navy);
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.02em;
    transition: var(--transition);
    width: fit-content;
    cursor: pointer;
    box-shadow: 0 2px 12px rgba(30, 58, 95, 0.06), 0 1px 0 rgba(255, 255, 255, 0.8) inset;
}

.filters-toggle:hover {
    border-color: rgba(212, 175, 55, 0.45);
    box-shadow: 0 4px 20px rgba(30, 58, 95, 0.08), 0 0 0 1px rgba(212, 175, 55, 0.15);
}

.filters-toggle-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(30, 58, 95, 0.08), rgba(30, 58, 95, 0.04));
    color: var(--gold);
    flex-shrink: 0;
}

.filters-toggle-icon svg {
    width: 20px;
    height: 20px;
}

.filters-toggle .chevron {
    margin-left: 4px;
    color: var(--primary-navy);
    opacity: 0.55;
    transition: transform 0.3s ease;
}

.filters-active-pill {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    background: linear-gradient(135deg, rgba(30, 58, 95, 0.06), rgba(212, 175, 55, 0.08));
    border: 1px solid rgba(212, 175, 55, 0.35);
    border-radius: 999px;
    box-shadow: 0 2px 12px rgba(212, 175, 55, 0.12);
}

.filters-active-pill svg {
    color: var(--gold);
    flex-shrink: 0;
}

.filters-active-pill span {
    color: var(--primary-navy);
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.filters-active-pill button {
    background: none;
    border: none;
    color: var(--gold-dark);
    cursor: pointer;
    font-weight: 700;
    font-size: 12px;
    text-decoration: underline;
    text-underline-offset: 3px;
    padding: 0 0 0 4px;
}

.filters-active-pill button:hover {
    color: var(--primary-navy);
}

.filters-content {
    display: none;
    margin-top: 24px;
    padding: 0;
    background: linear-gradient(155deg, #1a3354 0%, var(--primary-navy) 42%, var(--navy-dark) 100%);
    border-radius: 20px;
    border: 1px solid rgba(212, 175, 55, 0.22);
    box-shadow:
        0 4px 6px rgba(0, 0, 0, 0.04),
        0 24px 48px rgba(21, 42, 69, 0.35),
        0 0 0 1px rgba(255, 255, 255, 0.04) inset;
    overflow: hidden;
}

.filters-content.active {
    display: block;
}

.filters-panel-inner {
    padding: 28px 32px 32px;
}

.filters-header {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 28px;
    padding-bottom: 22px;
    border-bottom: 1px solid rgba(212, 175, 55, 0.18);
}

.filters-header-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid rgba(212, 175, 55, 0.25);
    color: var(--gold);
}

.filters-header-icon svg {
    width: 24px;
    height: 24px;
}

.filters-header-text h3 {
    color: var(--white);
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 6px 0;
    letter-spacing: -0.02em;
}

.filters-header-text p {
    margin: 0;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.55);
    font-weight: 500;
    line-height: 1.45;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px 24px;
    margin-bottom: 8px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.filter-label {
    color: rgba(232, 197, 120, 0.95);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
}

.filter-input {
    padding: 14px 16px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: var(--white);
    background: rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(8px);
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
}

.filter-input::placeholder {
    color: rgba(255, 255, 255, 0.38);
}

.filter-input:focus {
    outline: none;
    border-color: rgba(212, 175, 55, 0.55);
    background: rgba(0, 0, 0, 0.28);
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.12);
}

select.filter-input {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%23d4af37' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-color: rgba(0, 0, 0, 0.2);
    background-position: right 14px center;
    padding-right: 40px;
}

select.filter-input:focus {
    background-color: rgba(0, 0, 0, 0.28);
}

select.filter-input option {
    background-color: #152a45;
    color: var(--white);
}

.filter-check-stack {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-check-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.15);
    cursor: pointer;
    transition: border-color 0.2s ease, background 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.92);
}

.filter-check-row:hover {
    border-color: rgba(212, 175, 55, 0.3);
    background: rgba(0, 0, 0, 0.22);
}

.filter-check-row input[type="checkbox"] {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    accent-color: var(--gold);
    cursor: pointer;
    border-radius: 4px;
}

.filter-notice {
    padding: 16px 18px;
    border-radius: 12px;
    border: 1px solid rgba(212, 175, 55, 0.2);
    background: rgba(0, 0, 0, 0.2);
}

.filter-notice p {
    color: rgba(255, 255, 255, 0.88);
    font-size: 13px;
    margin: 0 0 12px 0;
    line-height: 1.5;
}

.filter-notice a {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    background: linear-gradient(135deg, var(--gold-dark), var(--gold));
    color: var(--primary-navy);
    transition: var(--transition);
}

.filter-notice a:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
}

.filter-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: flex-end;
    align-items: center;
    padding: 24px 32px 28px;
    margin: 8px -32px -32px;
    background: rgba(0, 0, 0, 0.18);
    border-top: 1px solid rgba(212, 175, 55, 0.12);
}

.filter-btn-apply {
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--primary-navy);
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.02em;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 12px rgba(212, 175, 55, 0.25);
}

.filter-btn-apply:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(212, 175, 55, 0.35);
}

.filter-btn-apply i {
    color: inherit;
}

.filter-btn-clear {
    padding: 14px 22px;
    background: transparent;
    color: rgba(255, 255, 255, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.filter-btn-clear:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.35);
}

.filter-btn-share {
    padding: 14px 22px;
    background: rgba(212, 175, 55, 0.08);
    color: var(--gold-light);
    border: 1px solid rgba(212, 175, 55, 0.4);
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.filter-btn-share:hover {
    background: rgba(212, 175, 55, 0.16);
    border-color: rgba(212, 175, 55, 0.55);
    color: #f0d78c;
}

.filter-btn-share.copied {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.5);
    color: #6ee7b7;
}

.filter-btn-share svg {
    stroke: currentColor;
}

/* ==========================================
   PROPERTIES GRID
   ========================================== */

.properties-section {
    padding: 48px 0;
}

.properties-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 32px;
    margin-bottom: 48px;
}

.property-card {
    background-color: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    position: relative;
}

.property-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

/* Property Flag Ribbons */
.property-flag {
    position: absolute;
    top: 16px;
    right: -8px;
    z-index: 10;
    padding: 8px 20px 8px 16px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--white);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    clip-path: polygon(0 0, 100% 0, 100% 100%, 8px 100%, 0 calc(100% - 8px));
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
}

.card-image {
    position: relative;
    overflow: hidden;
    background-color: var(--light-gray);
}

.card-img {
    width: 100%;
    height: 240px;
    object-fit: cover;
    display: block;
    transition: var(--transition);
}

.property-card:hover .card-img {
    transform: scale(1.05);
}

.photo-count {
    position: absolute;
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, 0.95);
    color: var(--primary-navy);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    z-index: 2;
}

.card-content {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex-grow: 1;
}

.property-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--primary-navy);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.property-location {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--gray);
    font-size: 14px;
}

.property-location svg {
    color: var(--gold);
    flex-shrink: 0;
}

.property-description {
    color: var(--gray);
    font-size: 14px;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-footer {
    margin-top: auto;
    padding-top: 16px;
    border-top: 1px solid var(--light-gray);
}

.property-price {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary-navy);
    margin-bottom: 12px;
}

.property-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.badge {
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.badge-room {
    background-color: rgba(30, 58, 95, 0.1);
    color: var(--primary-navy);
}

.badge-available {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(232, 197, 92, 0.2));
    color: var(--gold-dark);
}

.badge-date {
    background-color: var(--light-gray);
    color: var(--gray);
}

/* ==========================================
   PAGINATION
   ========================================== */

.pagination {
    display: flex;
    justify-content: space-between;
            align-items: center;
    padding: 32px 0;
    border-top: 1px solid var(--light-gray);
}

.pagination-info {
    color: var(--gray);
    font-size: 15px;
}

.pagination-controls {
    display: flex;
    gap: 8px;
    align-items: center;
}

.page-btn {
    padding: 10px 16px;
    background-color: var(--white);
    border: 2px solid var(--light-gray);
    color: var(--text-dark);
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: var(--transition);
}

.page-btn:hover:not(:disabled) {
    border-color: var(--primary-navy);
    color: var(--primary-navy);
}

.page-btn.active {
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    color: var(--white);
    border-color: var(--primary-navy);
}

.page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* Success message */
.success-message {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 2px solid #10b981;
    color: #065f46;
    padding: 16px 24px;
            border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
}

/* ==========================================
   RESPONSIVE DESIGN
   ========================================== */

@media (max-width: 768px) {
    .container {
        padding: 0 16px;
    }
    
    /* Navigation */
    .navbar {
        padding: 12px 0;
    }
    
    .logo-text {
        font-size: 18px;
    }
    
    .logo-icon svg {
        width: 20px;
        height: 20px;
    }
    
    .nav-links {
        gap: 4px;
        flex-wrap: wrap;
    }
    
    .nav-link {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    .nav-link svg {
        width: 14px;
        height: 14px;
    }
    
    .btn-agent {
        padding: 8px 12px;
        font-size: 12px;
    }
    
    .btn-agent svg {
        width: 14px;
        height: 14px;
    }
    
    /* Hero Section */
    .hero-header {
        padding: 48px 0 32px 0;
        min-height: auto;
    }
    
    .hero-title {
        font-size: 32px;
        line-height: 1.2;
        margin-bottom: 12px;
    }
    
    .hero-subtitle {
        font-size: 15px;
        margin-bottom: 24px;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 16px;
        padding: 20px;
    }
    
    .stat-divider {
        display: none;
    }
    
    .stat-number {
        font-size: 28px;
    }
    
    .stat-label {
        font-size: 13px;
    }
    
    /* Filters */
    .filters-section {
        padding: 16px 0;
    }
    
    .filters-toggle {
        width: 100%;
        justify-content: center;
        padding: 14px 20px;
        font-size: 15px;
    }
    
    .filters-content {
        margin-top: 12px;
        border-radius: 16px;
    }

    .filters-panel-inner {
        padding: 20px 16px 8px;
    }
    
    .filters-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 18px;
    }

    .filters-header-icon {
        width: 44px;
        height: 44px;
    }

    .filters-header-text h3 {
        font-size: 18px;
    }

    .filters-header-text p {
        font-size: 13px;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .filter-label {
        font-size: 13px;
    }
    
    .filter-input {
        padding: 12px 14px;
        font-size: 14px;
    }
    
    .filter-actions {
        flex-direction: column;
        gap: 10px;
        margin: 12px -16px -20px;
        padding: 20px 16px 22px;
    }
    
    .filter-btn-apply,
    .filter-btn-clear,
    .filter-btn-share {
        width: 100%;
        justify-content: center;
        padding: 14px 20px;
        font-size: 15px;
    }
    
    /* Properties Grid */
    .properties-section {
        padding: 32px 0;
    }
    
    .properties-grid {
        grid-template-columns: 1fr;
        gap: 20px;
            }
            
            .property-card {
        max-width: 100%;
    }
    
    .card-img {
        height: 220px;
    }
    
    .property-flag {
        font-size: 11px;
        padding: 6px 16px 6px 12px;
    }
    
    .card-content {
        padding: 16px;
    }
    
    .card-title {
        font-size: 17px;
        margin-bottom: 8px;
    }
    
    .card-location {
        font-size: 13px;
        margin-bottom: 12px;
    }
    
    .card-price {
        font-size: 22px;
    }
    
    .card-price-label {
        font-size: 13px;
    }
    
    .property-badges {
        gap: 6px;
    }
    
    .badge {
        padding: 5px 10px;
        font-size: 11px;
    }
    
    /* Pagination */
    .pagination {
                flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 20px 16px;
    }
    
    .page-info {
        text-align: center;
        font-size: 13px;
    }
    
    .page-links {
                justify-content: center;
        flex-wrap: wrap;
    }
    
    .page-link {
        padding: 8px 12px;
        font-size: 13px;
        min-width: 36px;
    }
    
    /* Footer */
    .footer {
        padding: 32px 0;
    }
    
    .footer-logo {
        margin-bottom: 12px;
    }
    
    .logo-icon {
        width: 32px;
        height: 32px;
        font-size: 20px;
    }
    
    .logo-text {
        font-size: 16px;
    }
    
    .footer-text {
        font-size: 13px;
    }
    
    .footer-copyright {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 28px;
    }
    
    .hero-subtitle {
        font-size: 14px;
    }
    
    .stat-number {
        font-size: 24px;
    }
    
    .card-title {
        font-size: 16px;
    }
    
    .card-price {
        font-size: 20px;
    }
    
    .nav-links {
        justify-content: center;
    }
    
    .properties-grid {
        gap: 16px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="{{ route('properties.index') }}" class="logo">
                    <div class="logo-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2"/>
                            <path d="M9 22V12h6v10" stroke-width="2"/>
                        </svg>
                        </div>
                    <span class="logo-text">TRUEHOLD</span>
                </a>
                <ul class="nav-links">
                    <li><a href="{{ route('properties.index') }}" class="nav-link active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2"/>
                        </svg>
                        Properties
                    </a></li>
                    <li><a href="{{ route('properties.map') }}" class="nav-link" id="trueholdNavMapLink">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z" stroke-width="2"/>
                        </svg>
                        Map View
                    </a></li>
                        @auth
                    <li><a href="{{ route('admin.dashboard') }}" class="btn-agent">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Dashboard
                    </a></li>
                        @else
                    <li><a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn-agent">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Agent Login
                    </a></li>
                        @endauth
                </ul>
                    </div>
                </div>
    </nav>

    <!-- Hero Header -->
    <header class="hero-header">
        <div class="hero-background">
            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1920&q=80" alt="Luxury Property" class="hero-bg-image">
            <div class="hero-overlay"></div>
            </div>
        <div class="hero-content">
            <div class="container">
                <h1 class="hero-title">Find Your Dream Home</h1>
                <p class="hero-subtitle">Discover exceptional properties in prime locations across London</p>
                
                <!-- Stats -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">{{ $properties->total() }}</div>
                        <div class="stat-label">Properties Available</div>
        </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $locations->count() }}+</div>
                        <div class="stat-label">Prime Locations</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

        <!-- Success Messages -->
        @if(session('success'))
        <div class="container" style="padding-top: 24px;">
            <div class="success-message">
                <i class="fas fa-check-circle" style="font-size: 24px;"></i>
                <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-toolbar">
                <button type="button" class="filters-toggle" onclick="toggleFilters()">
                    <span class="filters-toggle-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                        </svg>
                    </span>
                    <span id="filterToggleText">Refine search</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="chevron" id="filterChevron">
                        <path d="M6 9l6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                
                @if(request()->hasAny(['location', 'property_type', 'min_price', 'max_price', 'couples_allowed', 'ensuite', 'agent_name', 'paying_only', 'room_count']))
                    <div class="filters-active-pill">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                        </svg>
                        <span>Filters active</span>
                        <button type="button" onclick="clearFilters()">Clear all</button>
                    </div>
                @endif
            </div>
            
            <form method="GET" action="{{ route('properties.index') }}" class="filters-content" id="filtersContent">
                <div class="filters-panel-inner">
                <div class="filters-header">
                    <div class="filters-header-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                        </svg>
                    </div>
                    <div class="filters-header-text">
                        <h3>Search criteria</h3>
                        <p>Adjust filters to match your ideal property. Results update as you change options.</p>
                    </div>
                </div>
                    
                <div class="filter-grid">
                    @auth
                    <div class="filter-group">
                        <span class="filter-label">Paying agents</span>
                        <label class="filter-check-row">
                            <input type="checkbox" name="paying_only" value="1" {{ request('paying_only') ? 'checked' : '' }}>
                            <span>Show only paying agents ⚡</span>
                        </label>
                    </div>
                    @endauth
                    <div class="filter-group">
                        <label class="filter-label">Location</label>
                        <select name="location" class="filter-input">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                    <div class="filter-group">
                        <label class="filter-label">Property Type</label>
                        <select name="property_type" class="filter-input">
                                    <option value="">All Types</option>
                                    @foreach($propertyTypes as $type)
                                        <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @auth
                    <div class="filter-group">
                        <label class="filter-label">Agent Name</label>
                        <select name="agent_name" class="filter-input">
                                    <option value="">All Agents</option>
                                    @foreach($agentNames as $agent)
                                        <option value="{{ $agent }}" {{ request('agent_name') == $agent ? 'selected' : '' }}>
                                            {{ $agent }}@if(isset($agentsWithPaying) && (is_array($agentsWithPaying) ? in_array($agent, $agentsWithPaying) : ($agentsWithPaying->has($agent) ? $agentsWithPaying->get($agent) : $agentsWithPaying->contains($agent)))) ⚡@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    @else
                        @if(request('agent_name'))
                            <div class="filter-group">
                                <span class="filter-label">Agent <i class="fas fa-lock" style="opacity:0.7;font-size:10px;"></i></span>
                                <div class="filter-notice">
                                    <p><i class="fas fa-user-lock" style="color: var(--gold); margin-right: 6px;"></i>Agent filtering is available to registered users.</p>
                                    <a href="{{ route('login', ['redirect' => url()->current()]) }}">Sign in to unlock</a>
                                </div>
                            </div>
                        @endif
                            @endauth
                            
                    <div class="filter-group">
                        <label class="filter-label">Min Price</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="£0" class="filter-input">
                        </div>
                        
                    <div class="filter-group">
                        <label class="filter-label">Max Price</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="£5000" class="filter-input">
                            </div>
                            
                    <div class="filter-group">
                        <span class="filter-label">Couple allowed</span>
                        <div class="filter-check-stack">
                            <label class="filter-check-row">
                                <input type="checkbox" id="listingCouplesYes" name="couples_allowed" value="yes" {{ request('couples_allowed') == 'yes' ? 'checked' : '' }}>
                                <span>Couples welcome only</span>
                            </label>
                            <label class="filter-check-row">
                                <input type="checkbox" id="listingCouplesNo" name="couples_allowed" value="no" {{ request('couples_allowed') == 'no' ? 'checked' : '' }}>
                                <span>Singles only</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <span class="filter-label">Ensuite</span>
                        <label class="filter-check-row">
                            <input type="checkbox" name="ensuite" value="yes" {{ request('ensuite') == 'yes' ? 'checked' : '' }}>
                            <span>Ensuite only</span>
                        </label>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Room count</label>
                        <select name="room_count" class="filter-input">
                            <option value="">All</option>
                            @foreach($roomCounts ?? [] as $count)
                                @if($count !== null && $count !== '')
                                    <option value="{{ $count }}" {{ request('room_count') == $count ? 'selected' : '' }}>{{ $count }} {{ $count == 1 ? 'room' : 'rooms' }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                </div>
                            
                <div class="filter-actions">
                    <button type="button" onclick="clearFilters()" class="filter-btn-clear">
                        <i class="fas fa-times"></i>
                        Clear
                                </button>
                    <button type="button" onclick="shareFilters()" class="filter-btn-share" title="Copy link with current filters">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8h-3m0 0a2 2 0 11-4 0 2 2 0 014 0zM3 21h18M3 10h18M13 13l5-5m0 0v4m0-4h-4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span id="shareButtonText">Share Link</span>
                    </button>
                    <button type="submit" class="filter-btn-apply">
                        <i class="fas fa-search"></i>
                        Apply Filters
                                </button>
                        </div>
                    </form>
                </div>
    </section>
    
    <!-- Properties Grid (only this container updates when filters change) -->
    <section class="properties-section">
        <div class="container" id="propertiesResultsContainer">
            @if($properties->count() > 0)
                <div class="properties-grid">
                    @foreach($properties as $property)
                        <a href="{{ route('properties.show', $property->id) }}" class="property-card">
                            @if($property->flag)
                                <span class="property-flag" style="{{ $property->flag_color ? 'background: ' . $property->flag_color : '' }}">
                                    {{ $property->flag }}
                            </span>
            @endif

                            <div class="card-image">
                                    @if($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0)
                                    <img src="{{ $property->high_quality_photos_array[0] }}" alt="{{ $property->title }}" class="card-img">
                                    @elseif($property->first_photo_url && $property->first_photo_url !== 'N/A')
                                    <img src="{{ $property->first_photo_url }}" alt="{{ $property->title }}" class="card-img">
                                    @else
                                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80" alt="{{ $property->title }}" class="card-img">
                                    @endif
                                    
                                    @if(($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0) || $property->photo_count > 0)
                                    <span class="photo-count">
                                            @if($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0)
                                            {{ count($property->high_quality_photos_array) }} photos
                                            @else
                                            {{ $property->photo_count }} photos
                                            @endif
                                            </span>
                                        @endif
                                    </div>
                            <div class="card-content">
                                <h3 class="property-title">{{ Str::limit($property->title, 60) }}</h3>
                                <div class="property-location">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                    {{ $property->location ?: 'Location not specified' }}
                                </div>
                                    @if($property->description && $property->description !== 'N/A')
                                    <p class="property-description">{{ Str::limit($property->description, 100) }}</p>
                                    @endif
                                <div class="card-footer">
                                    <div class="property-price">{{ $property->formatted_price }}</div>
                                    <div class="property-badges">
                                        @if($property->property_type)
                                            <span class="badge badge-room">{{ $property->property_type }}</span>
                                        @endif
                                        @if($property->available_date && $property->available_date !== 'N/A')
                                            <span class="badge badge-date">{{ $property->available_date }}</span>
                                        @else
                                            <span class="badge badge-available">Available now</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                    @if($properties->hasPages())
                    <div class="pagination">
                        <div class="pagination-info">
                            Showing {{ $properties->firstItem() ?? 0 }}-{{ $properties->lastItem() ?? 0 }} of {{ $properties->total() }}
                            </div>
                        <div class="pagination-controls">
                                @if($properties->onFirstPage())
                                <button class="page-btn" disabled>Previous</button>
                                @else
                                <a href="{{ $properties->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="page-btn">Previous</a>
                                @endif
                                
                                @foreach($properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)) as $page => $url)
                                    @if($page == $properties->currentPage())
                                    <button class="page-btn active">{{ $page }}</button>
                                    @else
                                    <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" class="page-btn">{{ $page }}</a>
                                    @endif
                                @endforeach
                                
                                @if($properties->hasMorePages())
                                <a href="{{ $properties->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="page-btn">Next</a>
                                @else
                                <button class="page-btn" disabled>Next</button>
                                @endif
                            </div>
                        </div>
                    @endif
            @else
                <div style="text-align: center; padding: 60px 20px; background: var(--white); border-radius: 16px; box-shadow: var(--shadow-md);">
                    <i class="fas fa-home" style="font-size: 64px; color: var(--gray); margin-bottom: 24px;"></i>
                    <h3 style="font-size: 24px; font-weight: 700; color: var(--primary-navy); margin-bottom: 12px;">No Properties Found</h3>
                    <p style="color: var(--gray); margin-bottom: 24px;">Try adjusting your filters to see more results.</p>
                    <a href="{{ route('properties.index') }}" class="filter-btn-apply">View All Properties</a>
                </div>
            @endif
    </div>
    </section>

    <script src="{{ asset('js/property-filters-sync.js') }}"></script>
    <script>
        (function initCouplesCheckboxes() {
            const yesEl = document.getElementById('listingCouplesYes');
            const noEl = document.getElementById('listingCouplesNo');
            if (yesEl && noEl) {
                yesEl.addEventListener('change', function() { if (this.checked) noEl.checked = false; });
                noEl.addEventListener('change', function() { if (this.checked) yesEl.checked = false; });
            }
        })();
        function toggleFilters() {
            const content = document.getElementById('filtersContent');
            const text = document.getElementById('filterToggleText');
            const chevron = document.getElementById('filterChevron');
            
            if (content.classList.contains('active')) {
                content.classList.remove('active');
                text.textContent = 'Refine search';
                chevron.style.transform = 'rotate(0deg)';
                } else {
                content.classList.add('active');
                text.textContent = 'Hide search';
                chevron.style.transform = 'rotate(180deg)';
            }
        }
        
        function clearFilters() {
            if (typeof TrueholdPropertyFilters !== 'undefined') {
                TrueholdPropertyFilters.clearStored();
            }
            window.location.href = '{{ route("properties.index") }}';
        }

        function shareFilters() {
            // Get current URL with all query parameters (filters)
            const currentUrl = window.location.href;
            
            // Copy to clipboard
            navigator.clipboard.writeText(currentUrl).then(() => {
                // Visual feedback
                const shareButton = document.querySelector('.filter-btn-share');
                const shareButtonText = document.getElementById('shareButtonText');
                const originalText = shareButtonText.textContent;
                
                shareButton.classList.add('copied');
                shareButtonText.innerHTML = '<i class="fas fa-check"></i> Link Copied!';
                
                // Reset after 2 seconds
                setTimeout(() => {
                    shareButton.classList.remove('copied');
                    shareButtonText.textContent = originalText;
                }, 2000);
            }).catch(err => {
                // Fallback for browsers that don't support clipboard API
                alert('Link copied to clipboard:\n' + currentUrl);
                console.error('Failed to copy:', err);
            });
        }
        
        // Auto-apply filters on change: update only the results container (no full page reload)
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filtersContent');
            const container = document.getElementById('propertiesResultsContainer');
            if (!form || !container) return;

            function getFilterQueryString() {
                if (typeof TrueholdPropertyFilters !== 'undefined') {
                    return TrueholdPropertyFilters.listingFormQueryString(form);
                }
                const formData = new FormData(form);
                const params = new URLSearchParams();
                formData.forEach((value, key) => {
                    if (value != null && value !== '') params.set(key, value);
                });
                return params.toString();
            }

            function loadResults() {
                const qs = getFilterQueryString();
                const url = '{{ route("properties.index") }}' + (qs ? '?' + qs : '');
                container.style.opacity = '0.6';
                container.style.pointerEvents = 'none';
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContainer = doc.getElementById('propertiesResultsContainer');
                        if (newContainer) {
                            container.innerHTML = newContainer.innerHTML;
                        }
                        history.pushState({}, '', url);
                        if (typeof TrueholdPropertyFilters !== 'undefined') {
                            TrueholdPropertyFilters.saveFromLocationSearch();
                        }
                    })
                    .catch(() => { window.location.href = url; })
                    .finally(() => {
                        container.style.opacity = '';
                        container.style.pointerEvents = '';
                    });
            }

            if (typeof TrueholdPropertyFilters !== 'undefined') {
                if (!TrueholdPropertyFilters.hasFilterParamsInSearch()) {
                    const stored = TrueholdPropertyFilters.getStoredQueryString();
                    if (stored) {
                        TrueholdPropertyFilters.applyQueryStringToListingForm(form, stored);
                        const u = '{{ route("properties.index") }}' + '?' + stored;
                        history.replaceState({}, '', u);
                        loadResults();
                    }
                } else {
                    TrueholdPropertyFilters.saveFromLocationSearch();
                }
            }

            const mapNav = document.getElementById('trueholdNavMapLink');
            if (mapNav && typeof TrueholdPropertyFilters !== 'undefined') {
                mapNav.addEventListener('click', function (e) {
                    e.preventDefault();
                    const qs = TrueholdPropertyFilters.listingFormQueryString(form) || TrueholdPropertyFilters.getStoredQueryString();
                    TrueholdPropertyFilters.saveFromQueryString(qs);
                    window.location.href = '{{ route("properties.map") }}' + (qs ? '?' + qs : '');
                });
            }

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                loadResults();
            });
            let priceDebounce;
            form.querySelectorAll('select.filter-input').forEach(sel => {
                sel.addEventListener('change', loadResults);
            });
            form.querySelectorAll('input.filter-input[type="number"]').forEach(inp => {
                inp.addEventListener('input', () => {
                    clearTimeout(priceDebounce);
                    priceDebounce = setTimeout(loadResults, 400);
                });
                inp.addEventListener('change', loadResults);
            });
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', loadResults);
            });
            // Store current URL when clicking on property cards (delegation so AJAX-loaded cards work)
            document.addEventListener('click', (e) => {
                if (e.target.closest('.property-card')) {
                    sessionStorage.setItem('propertyListingUrl', window.location.href);
                }
            });
        });
    </script>
</body>
</html>
