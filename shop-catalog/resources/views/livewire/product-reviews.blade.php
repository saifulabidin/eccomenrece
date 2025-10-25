<div class="reviews-section">
    <!-- Reviews Header -->
    <div class="row g-3 mb-4 align-items-center">
        <div class="col-12 col-md-8">
            <h3 class="text-light mb-3 mb-md-2">
                <i class="bi bi-star-fill text-warning me-2"></i>Ulasan Produk
            </h3>
            <div class="rating-summary-wrapper">
                <div class="rating-summary d-flex align-items-center gap-3">
                    <h2 class="text-warning mb-0 fw-bold">{{ $product->formatted_average_rating }}</h2>
                    <div>
                        <div class="rating-display mb-1">
                            {!! $product->starsAttribute !!}
                        </div>
                        <small class="text-muted">{{ $product->total_reviews }} ulasan</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <label class="text-muted small mb-2 d-block d-md-none">
                <i class="bi bi-funnel me-1"></i>Urutkan Ulasan
            </label>
            <div class="btn-group w-100 shadow-sm" role="group">
                <button type="button" 
                        class="btn btn-sm {{ $sortBy === 'latest' ? 'btn-primary' : 'btn-outline-secondary' }}"
                        wire:click="setSortBy('latest')">
                    <i class="bi bi-clock me-1 me-sm-2"></i>
                    <span class="d-none d-sm-inline">Terbaru</span>
                    <span class="d-inline d-sm-none">Baru</span>
                </button>
                <button type="button" 
                        class="btn btn-sm {{ $sortBy === 'highest' ? 'btn-primary' : 'btn-outline-secondary' }}"
                        wire:click="setSortBy('highest')">
                    <i class="bi bi-star me-1 me-sm-2"></i>
                    <span class="d-none d-sm-inline">Rating Tertinggi</span>
                    <span class="d-inline d-sm-none">Tertinggi</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="reviews-list">
        @if($reviews->count() > 0)
            @foreach($reviews as $review)
                <div class="review-item card bg-dark border-secondary mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                @if($review->user_avatar)
                                    <img src="{{ $review->user_avatar }}" alt="{{ $review->display_name }}"
                                         class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-person text-light fs-4"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-light mb-0">{{ $review->display_name }}</h6>
                                        <small class="text-muted">{{ $review->formatted_date }}</small>
                                        @if($review->is_verified)
                                            <span class="badge bg-success ms-2">
                                                <i class="bi bi-check-circle me-1"></i>Verified
                                            </span>
                                        @endif
                                    </div>
                                    <div class="rating-display">
                                        {!! $review->stars !!}
                                    </div>
                                </div>
                                <div class="review-content text-light">
                                    {{ nl2br(e($review->review)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $reviews->links('vendor.pagination.custom') }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <div class="bg-secondary bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center"
                         style="width: 80px; height: 80px;">
                        <i class="bi bi-star text-muted fs-1"></i>
                    </div>
                </div>
                <h4 class="text-light mb-2">Belum Ada Ulasan</h4>
                <p class="text-muted">Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
            </div>
        @endif
    </div>

    <!-- Success/Error Messages -->
    @if(session('google_auth_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('google_auth_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('google_auth_error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('google_auth_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('google_logout_success'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('google_logout_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Review Form Messages -->
    @if(session('review_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('review_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('review_error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('review_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Review Form -->
    @if(!$hasSubmittedReview)
        <div class="review-form-section mb-5">
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary">
                    <h5 class="text-light mb-0">
                        @if($googleUser)
                            <i class="bi bi-pencil-square me-2"></i>Tulis Ulasan Anda
                        @else
                            <i class="bi bi-google me-2"></i>Login untuk Review
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($googleUser)
                    <!-- User Info with Logout -->
                    <div class="user-info mb-4 p-3 bg-secondary bg-opacity-10 rounded-3 border border-secondary">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                <img src="{{ $googleUser['avatar'] }}" 
                                     alt="{{ $googleUser['name'] }}" 
                                     class="rounded-circle shadow-sm" 
                                     style="width: 48px; height: 48px; object-fit: cover;">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="text-light fw-semibold text-truncate">{{ $googleUser['name'] }}</div>
                                    <small class="text-muted d-block text-truncate">{{ $googleUser['email'] }}</small>
                                </div>
                            </div>
                            <form action="{{ route('auth.google.logout') }}" method="POST" class="w-100 w-sm-auto">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-box-arrow-right me-1"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Review Form -->
                    <form wire:submit.prevent="submitReview">

                        <!-- Rating Input -->
                        <div class="mb-3">
                            <label class="form-label text-light">Rating <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            class="star-btn {{ $rating >= $i ? 'active' : '' }}"
                                            wire:click="$set('rating', {{ $i }})"
                                            title="{{ $i }} bintang">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                @endfor
                            </div>
                            @error('rating')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Review Textarea -->
                        <div class="mb-3">
                            <label for="review" class="form-label text-light">Ulasan <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-dark border-secondary text-light @error('review') is-invalid @enderror"
                                      id="review"
                                      wire:model="review"
                                      rows="4"
                                      placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                            @error('review')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary {{ $isSubmitting ? 'disabled' : '' }}" {{ $isSubmitting ? 'disabled' : '' }}>
                            @if($isSubmitting)
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Mengirim...
                            @else
                                <i class="bi bi-send me-2"></i>Kirim Ulasan
                            @endif
                        </button>
                    </form>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" style="width: 80px; opacity: 0.8;">
                        </div>
                        <p class="text-light mb-3">Login dengan Google untuk menulis ulasan produk</p>
                        <button wire:click="loginWithGoogle" class="btn btn-danger">
                            <i class="bi bi-google me-2"></i>Login dengan Google
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @else
        <!-- Success Message -->
        <div class="review-form-section mb-5">
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary">
                    <h5 class="text-light mb-0">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>Ulasan Terkirim
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-light mb-2">Terima Kasih!</h4>
                        <p class="text-muted">Ulasan Anda berhasil dikirim dan sedang menunggu persetujuan admin.</p>
                        <button wire:click="resetForm" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square me-2"></i>Tulis Ulasan Lain
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>