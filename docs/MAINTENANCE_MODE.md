# Maintenance Mode Documentation

## Overview
The Maintenance Mode feature allows administrators to toggle the public availability of the website directly from the Filament Admin Panel. This is a "Soft Maintenance" implementation, meaning the application server remains running, but a middleware intercepts public requests.

## How It Works

### 1. Settings Storage
Configuration is stored in the `settings` table using `spatie/laravel-settings`.
- **Class**: `App\Settings\GeneralSettings`
- **Fields**:
    - `site_active` (boolean): Determines if the site is accessible to the public.
    - `maintenance_message` (string): The message displayed on the maintenance page.
    - `maintenance_image` (string, nullable): Path to custom illustration image for maintenance page.

### 2. Middleware
A middleware `App\Http\Middleware\CheckMaintenanceMode` intercepts all web requests.
- **Logic**:
    1.  Checks if `site_active` is `true`. If yes, allows request.
    2.  Checks if the user is logged in (Admin). If yes, allows request.
    3.  Checks if the route is an Admin Panel route (`admin*`). If yes, allows request (to enable login).
    4.  If none of the above, returns a **503 Service Unavailable** response with the `errors.503` view.

### 3. Admin Interface
A Filament page `App\Filament\Pages\ManageGeneralSettings` provides the UI to change these settings.

## How to Use

1.  Login to the Admin Panel.
2.  Navigate to **Pengaturan** > **Pengaturan Sistem**.
3.  **To Enable Maintenance Mode**:
    - Toggle "Status Website Aktif" to **OFF**.
    - (Optional) Update the "Pesan Maintenance".
    - (Optional) Upload a custom illustration image (max 2MB, JPG/PNG/SVG).
    - Click "Save Changes".
4.  **To Disable Maintenance Mode**:
    - Toggle "Status Website Aktif" to **ON**.
    - Click "Save Changes".

## Customization

### Changing the View
The maintenance page is located at `resources/views/errors/503.blade.php`. You can modify the HTML/Tailwind classes to match your branding.

### Custom Illustration Image
- Upload an image via the "Gambar Ilustrasi" field in the admin panel.
- Supported formats: JPG, PNG, SVG
- Maximum size: 2MB
- Images are stored in `storage/app/public/maintenance/`
- If no image is uploaded, a default icon will be displayed.

### Adding Whitelisted Routes
To allow other routes (e.g., API) during maintenance, modify `App\Http\Middleware\CheckMaintenanceMode.php` and add conditions to the check logic.

```php
if ($request->is('api*')) {
    return $next($request);
}
```
