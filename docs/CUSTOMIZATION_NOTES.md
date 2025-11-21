# Customization Notes - Portal Data AN-TKA Disdik Sulteng

## 🎨 Customizations Applied

### 1. Logo Provinsi Sulawesi Tengah di Navbar ✅

**File**: `resources/views/public/layout.blade.php`

**Changes**:

-   Added logo image before title in navbar
-   Logo path: `storage/logo-sulteng.png`
-   Logo size: `h-12 w-auto` (height 48px, auto width)

**Logo Placement**:

```blade
<img src="{{ asset('storage/logo-sulteng.png') }}" alt="Logo Sulawesi Tengah" class="h-12 w-auto">
```

**To Add Logo**:

1. Upload logo file to: `storage/app/public/logo-sulteng.png`
2. Create symlink (if not exists): `php artisan storage:link`
3. Logo will appear automatically

---

### 2. Removed Tagline/Subtitle ✅

**Files Modified**:

-   `resources/views/public/landing.blade.php` - Removed system description
-   `resources/views/public/dashboard.blade.php` - Removed subtitle

**Before**:

```
Sistem Informasi Data Pelaksanaan Asesmen Nasional - Tingkat Kompetensi Asesmen
```

**After**:

```
(Removed completely)
```

---

### 3. Redesigned Hero Section ✅

**File**: `resources/views/public/landing.blade.php`

**Changes**:

-   Split hero into 2 columns (left: text, right: illustration)
-   Shortened main heading: "Selamat Datang di Portal"
-   Subheading below: "AN-TKA Disdik Sulteng"
-   Added space for illustration image

**Layout**:

```
┌─────────────────────────────────────────────┐
│  Left Column          │  Right Column       │
│                       │                     │
│  Selamat Datang       │                     │
│  di Portal            │   [Illustration]    │
│                       │                     │
│  AN-TKA Disdik        │                     │
│  Sulteng              │                     │
│                       │                     │
│  [Button]             │                     │
└─────────────────────────────────────────────┘
```

**Illustration Path**: `storage/illustration-hero.png`

---

## 📁 Required Assets

### 1. Logo Sulteng

-   **Path**: `storage/app/public/logo-sulteng.png`
-   **Recommended Size**: 200x200px (will be displayed at 48px height)
-   **Format**: PNG with transparent background
-   **Usage**: Navbar logo

### 2. Hero Illustration

-   **Path**: `storage/app/public/illustration-hero.png`
-   **Recommended Size**: 800x600px or similar
-   **Format**: PNG or SVG
-   **Usage**: Landing page hero section (right side)
-   **Note**: Hidden on mobile, visible only on desktop (lg breakpoint)

---

## 🚀 How to Add Images

### Method 1: Upload via Admin Panel

1. Go to `/admin`
2. Upload images to appropriate location
3. Note the path

### Method 2: Manual Upload

1. Place images in: `storage/app/public/`
2. Run: `php artisan storage:link`
3. Images accessible via: `storage/your-image.png`

### Method 3: Public Directory

1. Place images in: `public/images/`
2. Update blade files to use: `{{ asset('images/your-image.png') }}`

---

## 🎨 Design Specifications

### Hero Section

-   **Background**: Gradient blue (from-blue-600 to-blue-800)
-   **Text Color**: White
-   **Main Heading**: 5xl (48px) font-bold
-   **Subheading**: 3xl (30px) font-semibold
-   **Button**: White background, blue text
-   **Layout**: 2 columns on desktop, 1 column on mobile

### Navbar

-   **Background**: White with shadow
-   **Height**: 64px (h-16)
-   **Logo Height**: 48px (h-12)
-   **Text**: 2xl (24px) font-bold
-   **Link Hover**: Gray-900

---

## 📝 Future Customization Ideas

### Suggested Enhancements

1. **Color Scheme**: Customize gradient colors to match Sulteng branding
2. **Typography**: Use custom fonts (e.g., Poppins, Inter)
3. **Animations**: Add smooth transitions and animations
4. **Dark Mode**: Add toggle for dark/light theme
5. **More Illustrations**: Add illustrations to features section

### Color Palette Suggestion

Based on Sulawesi Tengah branding:

```css
Primary: #1E40AF (Blue)
Secondary: #059669 (Green - untuk highlight)
Accent: #F59E0B (Orange/Gold - untuk CTA)
```

---

## 🖼️ Placeholder Images

If you don't have images yet, you can use placeholders:

**Logo Placeholder**:

```blade
<img src="https://via.placeholder.com/200x200/1E40AF/FFFFFF?text=LOGO"
     alt="Logo Sulawesi Tengah" class="h-12 w-auto">
```

**Hero Illustration Placeholder**:

```blade
<img src="https://via.placeholder.com/800x600/3B82F6/FFFFFF?text=Dashboard+Illustration"
     alt="Ilustrasi Dashboard" class="w-full max-w-md">
```

---

## ✅ Checklist

After adding images:

-   [ ] Logo Sulteng uploaded to `storage/app/public/logo-sulteng.png`
-   [ ] Storage link created: `php artisan storage:link`
-   [ ] Hero illustration uploaded to `storage/app/public/illustration-hero.png`
-   [ ] Test on desktop view (logo and illustration visible)
-   [ ] Test on mobile view (illustration hidden, layout responsive)
-   [ ] Clear cache: `php artisan view:clear`
-   [ ] Browser hard refresh (Ctrl+Shift+R)

---

## 🔧 Technical Notes

### Responsive Behavior

-   **Logo**: Always visible on all devices
-   **Hero Illustration**: Hidden on mobile (`hidden lg:flex`)
-   **Grid Layout**: 1 column on mobile, 2 columns on desktop (`grid-cols-1 lg:grid-cols-2`)

### Performance

-   Images loaded via Laravel's `asset()` helper
-   Automatic caching by browser
-   Consider using WebP format for better performance
-   Add lazy loading if needed: `loading="lazy"`

---

**Last Updated**: 2025-11-21  
**Customized For**: Dinas Pendidikan Provinsi Sulawesi Tengah  
**Version**: 1.0
