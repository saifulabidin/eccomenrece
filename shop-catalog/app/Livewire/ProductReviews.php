<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Controllers\Auth\GoogleController;

class ProductReviews extends Component
{
    use WithPagination;

    public $product;
    public $rating = 5;
    public $review = '';
    public $googleUser = null;
    public $sortBy = 'latest'; // latest, highest
    public $isSubmitting = false;
    public $hasSubmittedReview = false;
    public $recaptchaToken = null;

    protected $queryString = [
        'sortBy' => ['except' => 'latest'],
    ];

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'required|string|min:10|max:1000',
        'recaptchaToken' => 'required',
    ];

    protected $messages = [
        'rating.required' => 'Rating wajib dipilih',
        'rating.min' => 'Rating minimal 1 bintang',
        'rating.max' => 'Rating maksimal 5 bintang',
        'review.required' => 'Ulasan wajib diisi',
        'review.min' => 'Ulasan minimal 10 karakter',
        'review.max' => 'Ulasan maksimal 1000 karakter',
        'recaptchaToken.required' => 'Mohon verifikasi reCAPTCHA',
    ];

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $this->googleUser = GoogleController::getCurrentGoogleUser(request());
    }

    public function submitReview()
    {
        // Prevent double submission
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            $this->validate();

            if (!$this->googleUser) {
                session()->flash('review_error', 'Silakan login dengan Google terlebih dahulu');
                $this->isSubmitting = false;
                return;
            }

            // Verify reCAPTCHA
            $recaptcha = new \ReCaptcha\ReCaptcha(config('services.recaptcha.secret_key'));
            $resp = $recaptcha->verify($this->recaptchaToken, request()->ip());

            if (!$resp->isSuccess()) {
                session()->flash('review_error', 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
                $this->isSubmitting = false;
                $this->recaptchaToken = null; // Reset token
                $this->dispatch('resetRecaptcha'); // Trigger reset on frontend
                return;
            }

            // Check if user already reviewed this product
            $existingReview = Review::where('product_id', $this->product->id)
                ->where('google_id', $this->googleUser['id'])
                ->first();

            if ($existingReview) {
                session()->flash('review_error', 'Anda sudah memberikan ulasan untuk produk ini');
                $this->isSubmitting = false;
                return;
            }

        Review::create([
                'product_id' => $this->product->id,
                'google_id' => $this->googleUser['id'],
                'user_name' => $this->googleUser['name'],
                'user_email' => $this->googleUser['email'],
                'user_avatar' => $this->googleUser['avatar'],
                'rating' => $this->rating,
                'review' => $this->review,
                'ip_address' => request()->ip(),
            ]);

            // Reset form and hide form
            $this->reset(['rating', 'review', 'recaptchaToken']);
            $this->hasSubmittedReview = true;

            session()->flash('review_success', 'Ulasan Anda berhasil dikirim dan menunggu persetujuan admin');

        } catch (\Exception $e) {
            session()->flash('review_error', 'Terjadi kesalahan: ' . $e->getMessage());
            $this->recaptchaToken = null;
            $this->dispatch('resetRecaptcha');
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function setSortBy($sort)
    {
        $this->sortBy = $sort;
        $this->resetPage();
    }

    public function getReviewsProperty()
    {
        $query = $this->product->approvedReviews();

        if ($this->sortBy === 'latest') {
            $query->latest();
        } elseif ($this->sortBy === 'highest') {
            $query->highestRated();
        }

        return $query->paginate(5);
    }

    public function loginWithGoogle()
    {
        session()->put('intended_url', route('product.detail', $this->product->slug));
        return redirect()->route('auth.google');
    }

    public function logoutFromGoogle()
    {
        return redirect()->route('auth.google.logout');
    }

    public function resetForm()
    {
        $this->hasSubmittedReview = false;
        $this->rating = 5;
        $this->review = '';
    }

    public function render()
    {
        return view('livewire.product-reviews', [
            'reviews' => $this->reviews,
        ]);
    }
}
