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
    public $hasApprovedReview = false;
    public $isEditMode = false;
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

        // Check if user has already reviewed THIS specific product
        if ($this->googleUser) {
            $existingReview = Review::where('product_id', $this->product->id)
                ->where('google_id', $this->googleUser['id'])
                ->first();

            $this->hasSubmittedReview = $existingReview && !$existingReview->approved; // Waiting approval
            $this->hasApprovedReview = $existingReview && $existingReview->approved; // Already approved

            // Pre-fill form if user has an existing review (for editing)
            if ($existingReview) {
                $this->rating = $existingReview->rating;
                $this->review = $existingReview->review;
            }
        }
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

            $reviewData = [
                'product_id' => $this->product->id,
                'google_id' => $this->googleUser['id'],
                'user_name' => $this->googleUser['name'],
                'user_email' => $this->googleUser['email'],
                'user_avatar' => $this->googleUser['avatar'],
                'rating' => $this->rating,
                'review' => $this->review,
                'ip_address' => request()->ip(),
            ];

            if ($existingReview) {
                // Update existing review for this product
                $existingReview->update($reviewData);
                $message = 'Ulasan Anda berhasil diperbarui';
            } else {
                // Create new review for this product
                Review::create($reviewData);
                $message = 'Ulasan Anda berhasil dikirim dan menunggu persetujuan admin';
            }

            // Update review status flags
            $existingReview = Review::where('product_id', $this->product->id)
                ->where('google_id', $this->googleUser['id'])
                ->first();

            $this->hasSubmittedReview = $existingReview && !$existingReview->approved; // Waiting approval
            $this->hasApprovedReview = $existingReview && $existingReview->approved; // Already approved

            // Reset reCAPTCHA token but keep rating and review for editing
            $this->recaptchaToken = null;
            $this->isEditMode = false;

            session()->flash('review_success', $message);

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

    public function editReview()
    {
        if ($this->googleUser) {
            $existingReview = Review::where('product_id', $this->product->id)
                ->where('google_id', $this->googleUser['id'])
                ->first();

            if ($existingReview) {
                $this->rating = $existingReview->rating;
                $this->review = $existingReview->review;
                $this->isEditMode = true;
                $this->dispatch('focusReviewForm');
                $this->dispatch('renderRecaptcha');
            }
        }
    }

    public function resetForm()
    {
        // Reset review status flags based on current product
        if ($this->googleUser) {
            $existingReview = Review::where('product_id', $this->product->id)
                ->where('google_id', $this->googleUser['id'])
                ->first();

            $this->hasSubmittedReview = $existingReview && !$existingReview->approved; // Waiting approval
            $this->hasApprovedReview = $existingReview && $existingReview->approved; // Already approved

            // If user has an existing review, pre-fill the form for editing
            if ($existingReview) {
                $this->rating = $existingReview->rating;
                $this->review = $existingReview->review;
            } else {
                // Reset to defaults if no review exists
                $this->rating = 5;
                $this->review = '';
            }
        } else {
            $this->hasSubmittedReview = false;
            $this->hasApprovedReview = false;
            $this->rating = 5;
            $this->review = '';
        }

        $this->recaptchaToken = null;
        $this->isEditMode = false;
    }

    public function render()
    {
        // Clear success message if user's review is now approved and visible
        if ($this->googleUser && session('review_success')) {
            $userReviewVisible = $this->product->approvedReviews()
                ->where('google_id', $this->googleUser['id'])
                ->exists();

            if ($userReviewVisible) {
                session()->forget('review_success');
            }
        }

        // Update hasApprovedReview status in real-time
        if ($this->googleUser) {
            $this->hasApprovedReview = $this->product->approvedReviews()
                ->where('google_id', $this->googleUser['id'])
                ->exists();
        }

        return view('livewire.product-reviews', [
            'reviews' => $this->reviews,
        ]);
    }
}
