@if(session('success'))
    <!-- Success alert -->
    <div class="alert alert-success d-flex" role="alert">
        <div class="alert-icon me-2">
            <i class="ci-check-circle"></i>
        </div>
        <div>Uspjeh..! {{ session('success') }}</div>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger d-flex" role="alert">
        <div class="alert-icon me-2">
            <i class="ci-close-circle"></i>
        </div>
        <div>Greška..! {{ session('error') }}</div>
    </div>

@endif
@if(session('warning'))
    <div class="alert alert-warning d-flex" role="alert">
        <div class="alert-icon">
            <i class="ci-security-announcement me-2"></i>
        </div>
        <div>Upozorenje..! {{ session('warning') }}</div>
    </div>
@endif



@if ($errors->any())


    <div class="alert alert-danger d-flex" role="alert">
        <div class="alert-icon">
            <i class="ci-close-circle me-2"></i>
        </div>
            @foreach ($errors->all() as $error)
            <div>  {{ $error }}</div>
            @endforeach

    </div>
@endif
