<div class="accordion bg-body-tertiary rounded-5 p-4">
    <div class="accordion-item border-0">
        <h3 class="accordion-header" id="promoCodeHeading">
            <button type="button" class="accordion-button animate-underline collapsed py-0 ps-sm-2 ps-lg-0 ps-xl-2" data-bs-toggle="collapse" data-bs-target="#promoCode" aria-expanded="false" aria-controls="promoCode">
                <i class="ci-percent fs-xl me-2"></i>
                <span class="animate-target me-2">Kod za popust</span>
            </button>
        </h3>
        <div class="accordion-collapse collapse" id="promoCode" aria-labelledby="promoCodeHeading">
            <div class="accordion-body pt-3 pb-2 ps-sm-2 px-lg-0 px-xl-2">
                <form method="POST" action="{{ route('checkout.coupon') }}" class="needs-validation d-flex gap-2" novalidate>
                    @csrf
                    <div class="position-relative w-100">
                        <input type="text" class="form-control" name="coupon" value="{{ session(config('session.cart') . '_coupon') }}" placeholder="Unesite promo kod" required>
                        <div class="invalid-tooltip bg-transparent py-0">Unesite ispravan promo kod!</div>
                    </div>
                    <button type="submit" class="btn btn-dark">Primjeni</button>
                </form>
            </div>
        </div>
    </div>
</div>