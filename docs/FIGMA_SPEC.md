# Figma Design Spec — Toko Kayu Kontan Jaya

## File Structure (Pages)

```
📁 00_Tokens
  ├── Color Styles
  ├── Typography Styles
  ├── Spacing & Sizing
  └── Token Export (JSON + CSS vars)

📁 01_Assets
  ├── Logo / Brand (SVG variants: full, icon, dark, light)
  ├── Product Placeholder Images
  ├── Icon Library (Heroicons subset)
  └── Illustration set

📁 02_Components
  ├── Atoms: Button, Input, Badge, Avatar, Tooltip
  ├── Molecules: SearchBar, CartItem, ProductCard, StatCard
  ├── Organisms: Navbar, Sidebar, DataTable, Modal, Toast
  └── Patterns: Form Layout, Page Header, Empty State

📁 03_Pages
  ├── Login (Desktop 1440px + Mobile 390px)
  ├── Dashboard (Desktop + Tablet 768px)
  ├── Products / List (Desktop)
  ├── Product Detail & Form (Desktop)
  ├── Inventory (Desktop)
  ├── POS / Kasir (Desktop + Tablet)
  ├── Reports (Desktop)
  └── Settings (Desktop)

📁 04_Prototype
  ├── Flow 1: Login → Dashboard
  ├── Flow 2: Dashboard → POS → Checkout → Invoice
  ├── Flow 3: Products CRUD → Import CSV
  ├── Flow 4: Stock In / Stock Out
  └── Flow 5: Reports → Export
```

---

## Frame Sizes

| Page | Desktop | Tablet | Mobile |
|---|---|---|---|
| Login | 1440×900 | 768×1024 | 390×844 |
| Dashboard | 1440×900 | 768×1024 | — |
| Products | 1440×900 | 768×1024 | — |
| POS | 1440×900 | 768×1024 | — |
| Inventory | 1440×900 | — | — |
| Reports | 1440×900 | — | — |
| Settings | 1440×900 | — | — |

---

## Design Tokens

### Color Palette

```json
{
  "colors": {
    "brand": {
      "wood-900":  { "value": "#78350f" },
      "wood-800":  { "value": "#92400e" },
      "wood-700":  { "value": "#b45309" },
      "gold-600":  { "value": "#d97706" },
      "gold-500":  { "value": "#f59e0b" },
      "gold-100":  { "value": "#fef3c7" },
      "surface":   { "value": "#fefce8" }
    },
    "semantic": {
      "success": { "value": "#059669" },
      "warning": { "value": "#d97706" },
      "danger":  { "value": "#dc2626" },
      "info":    { "value": "#2563eb" }
    },
    "neutral": {
      "900": { "value": "#111827" },
      "700": { "value": "#374151" },
      "500": { "value": "#6b7280" },
      "200": { "value": "#e5e7eb" },
      "50":  { "value": "#f9fafb" }
    }
  }
}
```

### CSS Custom Properties

```css
:root {
  /* Brand */
  --color-wood-900:   #78350f;
  --color-wood-800:   #92400e;
  --color-wood-700:   #b45309;
  --color-gold-600:   #d97706;
  --color-gold-500:   #f59e0b;
  --color-gold-100:   #fef3c7;
  --color-surface:    #fefce8;

  /* Semantic */
  --color-success:    #059669;
  --color-warning:    #d97706;
  --color-danger:     #dc2626;
  --color-info:       #2563eb;

  /* Typography */
  --font-family:      'Inter', -apple-system, sans-serif;
  --font-size-xs:     0.75rem;    /* 12px */
  --font-size-sm:     0.875rem;   /* 14px */
  --font-size-base:   1rem;       /* 16px */
  --font-size-lg:     1.125rem;   /* 18px */
  --font-size-xl:     1.25rem;    /* 20px */
  --font-size-2xl:    1.5rem;     /* 24px */
  --font-size-3xl:    1.875rem;   /* 30px */

  /* Spacing (8px grid) */
  --space-1:  0.25rem;  /* 4px  */
  --space-2:  0.5rem;   /* 8px  */
  --space-3:  0.75rem;  /* 12px */
  --space-4:  1rem;     /* 16px */
  --space-5:  1.25rem;  /* 20px */
  --space-6:  1.5rem;   /* 24px */
  --space-8:  2rem;     /* 32px */
  --space-10: 2.5rem;   /* 40px */

  /* Border Radius */
  --radius-sm:   0.5rem;   /* 8px  */
  --radius-md:   0.75rem;  /* 12px */
  --radius-lg:   1rem;     /* 16px */
  --radius-xl:   1.5rem;   /* 24px */
  --radius-full: 9999px;

  /* Shadows */
  --shadow-sm:  0 1px 2px 0 rgba(0,0,0,0.05);
  --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.07);
  --shadow-lg:  0 10px 15px -3px rgba(0,0,0,0.08);
  --shadow-xl:  0 20px 25px -5px rgba(0,0,0,0.10);
}
```

### Tailwind Config Snippet

```javascript
// tailwind.config.js
module.exports = {
  content: ['./app/Views/**/*.{html,php}', './public/assets/js/**/*.js'],
  theme: {
    extend: {
      colors: {
        wood: {
          50:  '#fdf8f6',
          100: '#fef3c7',
          900: '#78350f',
          800: '#92400e',
          700: '#b45309',
        },
        gold: {
          500: '#f59e0b',
          600: '#d97706',
        },
      },
      fontFamily: {
        sans: ['Inter', '-apple-system', 'sans-serif'],
      },
      borderRadius: {
        '2xl': '1rem',
        '3xl': '1.5rem',
      },
      boxShadow: {
        'card': '0 4px 6px -1px rgba(120,53,15,0.07), 0 2px 4px -1px rgba(120,53,15,0.05)',
      },
    },
  },
  plugins: [],
};
```

---

## Component Naming Convention

```
[Layer]/[Component]/[Variant]/[State]

Examples:
  Atoms/Button/Primary/Default
  Atoms/Button/Primary/Hover
  Atoms/Button/Primary/Disabled
  Atoms/Button/Secondary/Default
  Molecules/ProductCard/Default
  Molecules/ProductCard/LowStock
  Organisms/Navbar/Desktop
  Organisms/Navbar/Mobile
  Organisms/DataTable/WithPagination
  Organisms/Modal/Confirm
  Organisms/Modal/Form
```

---

## Auto-Layout Rules

### Cards
- Padding: 20px all sides
- Gap between elements: 12px
- Border radius: 16px
- Fill: white; Stroke: var(--color-gold-100)

### Forms
- Label + Input gap: 8px
- Input height: 48px (desktop), 44px (mobile)
- Input padding: 12px 16px
- Border radius: 12px
- Focus ring: 2px solid var(--color-gold-600)

### Tables
- Row height: 56px
- Cell padding: 16px 24px
- Header bg: var(--color-surface)
- Hover row bg: #fef9ee

### Buttons
- Height: 44px (default), 40px (sm), 52px (lg)
- Padding: 12px 20px
- Border radius: 12px
- Font weight: 600

---

## Prototype Flows (5 flows minimum)

### Flow 1: Login → Dashboard
```
[Login Page]
  → [Input email + password]
  → [Click "Masuk"]
  → [Dashboard Page]
  → [Overview metrics visible]
```

### Flow 2: POS Checkout
```
[POS Page]
  → [Type in search bar]
  → [Typeahead dropdown appears]
  → [Click product → added to cart]
  → [Adjust quantity]
  → [Fill customer name]
  → [Select payment method: Cash]
  → [Input payment amount]
  → [Click Checkout]
  → [Success modal with invoice link]
  → [Click Print Invoice → Invoice page]
```

### Flow 3: Product CRUD
```
[Products List]
  → [Click "Tambah Produk"]
  → [Fill form (SKU, nama, kategori, harga, stok)]
  → [Upload gambar]
  → [Click Simpan]
  → [Redirect to Products List with toast success]
  → [Click Edit on row]
  → [Edit form pre-filled]
  → [Click Perbarui]
```

### Flow 4: Stock In
```
[Inventory Page]
  → [See low stock alert badge]
  → [Click "Stock In"]
  → [Select product from dropdown]
  → [Fill quantity + reference PO]
  → [Submit]
  → [Redirect Inventory → stock updated]
  → [Stock movement history shows new IN row]
```

### Flow 5: Reports Export
```
[Reports Page]
  → [Select date range picker]
  → [View table: per produk]
  → [View chart: daily trend]
  → [Click "Export CSV"]
  → [File download triggered]
  → [Click "Export PDF"]
  → [Print-ready page opens]
```
