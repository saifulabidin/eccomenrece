# Google OAuth Setup Guide

## 📋 Prerequisites

You need to set up Google OAuth credentials to enable the review system. Follow these steps:

## 🔑 Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the **Google+ API** (or Google People API)

## 🌐 Step 2: Create OAuth Credentials

1. Navigate to **APIs & Services** → **Credentials**
2. Click **Create Credentials** → **OAuth 2.0 Client ID**
3. Application type: **Web application**
4. Name: `Shop Catalog App`

## 🔗 Step 3: Configure Authorized Redirect URIs

Add these authorized redirect URIs:

```
http://localhost:8000/auth/google/callback
http://127.0.0.1:8000/auth/google/callback
```

## ⚙️ Step 4: Update Environment Variables

Edit your `.env` file and add the Google OAuth credentials:

```bash
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

Replace `your-client-id-here` and `your-client-secret-here` with the actual values from Google Cloud Console.

## 🔄 Step 5: Restart the Application

After updating the `.env` file:

```bash
# Clear configuration cache
./vendor/bin/sail artisan config:clear

# Restart the development server
./vendor/bin/sail artisan serve --host=0.0.0.0 --port=8000
```

## ✅ Step 6: Test the System

1. Visit any product detail page
2. Click on the "Ulasan" tab
3. Click "Login dengan Google"
4. You should be redirected to Google for authentication
5. After login, you can write reviews

## 🧪 Testing Without Google OAuth (Optional)

If you want to test the system without setting up Google OAuth, you can temporarily modify the authentication check:

Edit `app/Livewire/ProductReviews.php` and change the `submitReview` method:

```php
public function submitReview()
{
    $this->validate();

    // Temporarily skip Google authentication for testing
    // if (!$this->googleUser) {
    //     session()->flash('review_error', 'Silakan login dengan Google terlebih dahulu');
    //     return;
    // }

    // Test with mock user data
    $mockUser = [
        'id' => 'test_' . time(),
        'name' => 'Test User',
        'email' => 'test@example.com',
        'avatar' => 'https://ui-avatars.com/api/?name=Test+User&background=random',
    ];

    // Check if user already reviewed this product
    $existingReview = Review::where('product_id', $this->product->id)
        ->where('google_id', $mockUser['id'])
        ->first();

    if ($existingReview) {
        session()->flash('review_error', 'Anda sudah memberikan ulasan untuk produk ini');
        return;
    }

    Review::create([
        'product_id' => $this->product->id,
        'google_id' => $mockUser['id'],
        'user_name' => $mockUser['name'],
        'user_email' => $mockUser['email'],
        'user_avatar' => $mockUser['avatar'],
        'rating' => $this->rating,
        'review' => $this->review,
        'ip_address' => request()->ip(),
    ]);

    // Reset form
    $this->reset(['rating', 'review']);

    session()->flash('review_success', 'Ulasan Anda berhasil dikirim dan menunggu persetujuan admin');
}
```

## 🔧 Troubleshooting

### Error: "Missing required configuration keys"

This means Google OAuth credentials are missing from your `.env` file. Make sure you've added:

```bash
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=
```

### Error: "Invalid redirect URI"

Make sure the redirect URI in Google Cloud Console exactly matches your application URL.

### Error: "Access blocked"

If Google blocks the access, make sure your OAuth consent screen is properly configured and published.

## 🎯 Admin Panel Access

Access the admin panel at: `http://localhost:8000/admin`

Login credentials:
- Email: `admin@example.com`
- Password: `password123`

In the admin panel, you can:
- View all reviews (pending and approved)
- Approve or reject reviews
- Export review data
- View review statistics

## 🚀 Ready to Use!

Once Google OAuth is configured, your review system will be fully functional with:
- User authentication via Google
- Star rating system
- Review approval workflow
- Admin panel for management
- Responsive design for all devices