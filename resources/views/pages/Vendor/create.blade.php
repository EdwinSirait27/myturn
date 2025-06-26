@extends('layouts.app')
@section('title', 'Create Vendor')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <style>
        .avatar {
            position: relative;
        }

        .iframe-container {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%;
            /* Aspect ratio 16:9 */
        }

        .iframe-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Additional CSS for improved styling */
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            border: 1px solid #d1d1d1;
        }

        .form-control:focus {
            border-color: #6777ef;
            box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
        }

        .form-control-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #34395e;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control-label i {
            color: #6777ef;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f9f9f9;
            padding: 20px;
        }

        .card-header h6 {
            font-weight: 700;
            font-size: 16px;
            color: #34395e;
        }

        .card-body {
            padding: 30px;
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .btn-secondary {
            background-color: #cdd3d8;
            border-color: #cdd3d8;
            color: #34395e;
        }

        .btn-secondary:hover {
            background-color: #b9bfc4;
            border-color: #b9bfc4;
        }

        .bg-gradient-dark {
            background: linear-gradient(310deg, #2dce89, #2dcec7);
            border: none;
        }

        .bg-gradient-dark:hover {
            background: linear-gradient(310deg, #26b179, #26b1a9);
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-secondary {
            background-color: #f8f9fa;
            border-color: #f1f2f3;
        }

        .alert-secondary .text-white {
            color: #6c757d !important;
        }

        .form-check {
            padding-left: 30px;
            margin-bottom: 10px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 3px;
            margin-left: -30px;
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
        }

        .invalid-feedback {
            display: block;
            margin-top: 5px;
            font-size: 13px;
            color: #fc544b;
        }

        .alert-danger {
            background-color: #ffdede;
            border-color: #ffd0d0;
            color: #dc3545;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        select.form-control {
            height: 42px;
        }
    </style>
@endpush
@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Create Vendor</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="{{ route('pages.Vendorgroup') }}"> Vendor Group</a></div>
                    <div class="breadcrumb-item">Create Vendor</div>
                </div>
            </div>

            <div class="section-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pb-0 px-3">
                                    <h6 class="mb-0">{{ __('Create Vendor') }}</h6>
                                </div>
                                <div class="card-body pt-4 p-3">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" id="alert-success"
                                            role="alert">
                                            <span class="alert-text">
                                                {{ session('success') }}
                                            </span>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close">
                                                <i class="fa fa-close" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    @endif

                                    <form id="vendor-create" action="{{ route('Vendor.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="vendor_group_id" value="{{ $vendorgroup->id }}">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Name *') }}
                                                    </label>
                                                    <div>
                                                        <input type="text"
                                                            class="form-control text-uppercase @error('name') is-invalid @enderror"
                                                            id="name" name="name" value="{{ old('name') }}"
                                                            required placeholder="Fill Vendor Name" style="text-transform: uppercase;">
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="type" class="form-control-label">
                                                        <i class="fas fa-id-card"></i> {{ __('Type *') }}
                                                    </label> 
                                                    <div class="@error('type') border border-danger rounded-3 @enderror">

                                                        <select name="type" id="type" class="form-control select2" required>
                                                            <option value="">Choose Type</option>
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                                                     {{ $type }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('type')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                     <label for="consignment" class="form-control-label">
                                                        <i class="fas fa-id-card"></i> {{ __('Consignment *') }}
                                                    </label>
                                                    <div class="@error('consignment') border border-danger rounded-3 @enderror">
                                                        <select name="consignment" id="consignment" class="form-control select2" required>
                                                            <option value="">Choose Consignment</option>
                                                            @foreach ($consis as $consi)
                                                                {{-- <option value="{{ $consi }}">{{ $consi }} --}}
                                                                    <option value="{{ $consi }}" {{ old('consignment') == $consi ? 'selected' : '' }}>
                                                                        {{ $consi }}
                                                                    </option>


                                                            @endforeach
                                                        </select>
                                                        @error('consignment')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="bank_id" class="form-control-label">
                                                            <i class="fas fa-shield-alt"></i> {{ __('Bank *') }}
                                                        </label>
                                                        <div class="@error('bank_id') border border-danger rounded-3 @enderror">
                                                            <select class="form-control select2 @error('bank_id') is-invalid @enderror" name="bank_id" id="bank_id" required>
                                                                <option value="" disabled selected>Choose Banks</option>
                                                                @foreach($banks as $bank)
                                                            <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                                                {{ $vendor->bank->name ?? $bank->name ?? 'Tanpa Nama' }}
                                                            </option>
                                                        @endforeach
                                                            </select>
                                                            @error('bank_id')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="row">
                                              <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="country_id" class="form-control-label">
                                                            <i class="fas fa-shield-alt"></i> {{ __('Country *') }}
                                                        </label>
                                                        <div class="@error('country_id') border border-danger rounded-3 @enderror">
                                                            <select class="form-control select2 @error('country_id') is-invalid @enderror" name="country_id" id="country_id" required>
                                                                <option value="" disabled selected>Choose Country</option>
                                                                @foreach($countrys as $country)
                                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                                {{ $vendor->country->country_name ?? $country->country_name ?? 'Tanpa Nama' }}
                                                            </option>
                                                        @endforeach
                                                            </select>
                                                            @error('country_id')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                  <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="address" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Vendor Address *') }}
                                                    </label>
                                                    <div>
                                                        <input type="text"
                                                            class="form-control @error('address') is-invalid @enderror"
                                                            id="address" name="address" value="{{ old('address') }}"
                                                            required placeholder="Fill Vendor Address">
                                                        @error('address')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        <div class="row">
                                               <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="city" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Vendor City *') }}
                                                    </label>
                                                    <div>
                                                        <input type="text"
                                                            class="form-control @error('city') is-invalid @enderror"
                                                            id="city" name="city" value="{{ old('city') }}"
                                                            required placeholder="Fill Vendor City">
                                                        @error('city')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                               <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Vendor Mail *') }}
                                                    </label>
                                                    <div>
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            id="email" name="email" value="{{ old('email') }}"
                                                            required placeholder="Fill Vendor Email">
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="row">
                                               <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phonenumber" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Vendor Phone Number *') }}
                                                    </label>
                                                    <div>
                                                        <input type="number"
                                                            class="form-control @error('phonenumber') is-invalid @enderror"
                                                            id="phonenumber" name="phonenumber" max="13" value="{{ old('phonenumber') }}"
                                                            required placeholder="Fill Vendor Phone Number">
                                                        @error('city')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                     <label for="vendorpkp" class="form-control-label">
                                                        <i class="fas fa-id-card"></i> {{ __('Vendor PKP *') }}
                                                    </label>
                                                    <div class="@error('vendorpkp') border border-danger rounded-3 @enderror">
                                                    
                                                        <select name="vendorpkp" id="vendorpkp" class="form-control select2" required>
                                                            <option value="">Choose Vendor PKP</option>
                                                            @foreach ($vendors as $vendor)
                                                                {{-- <option value="{{ $vendor }}">{{ $vendor }}
                                                                </option> --}}

                                                                  <option value="{{ $vendor }}" {{ old('vendorpkp') == $vendor ? 'selected' : '' }}>
                                                                        {{ $vendor }}
                                                                    </option>

                                                            @endforeach
                                                        </select>
                                                        @error('vendorpkp')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                             <div class="row">
                                               <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="salesname" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Vendor Sales Name *') }}
                                                    </label>
                                                    <div>
                                                        <input type="text"
                                                            class="form-control @error('salesname') is-invalid @enderror"
                                                            id="salesname" name="salesname" max="255" value="{{ old('salesname') }}"
                                                            required placeholder="Fill Vendor Sales Name">
                                                        @error('salesname')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                               <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="salescp" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Vendor Sales CP *') }}
                                                    </label>
                                                    <div>
                                                        <input type="number"
                                                            class="form-control @error('salescp') is-invalid @enderror"
                                                            id="salescp" name="salescp" max="13" value="{{ old('salescp') }}"
                                                            required placeholder="Fill Vendor Sales CP">
                                                        @error('salescp')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
 <div class="row">
                                               <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="npwpname" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Npwp Name') }}
                                                    </label>
                                                    <div>
                                                        <input type="text"
                                                            class="form-control @error('npwpname') is-invalid @enderror"
                                                            id="npwpname" name="npwpname" max="255" value="{{ old('npwpname') }}"
                                                             placeholder="Fill Npwp Name ">
                                                        @error('npwpname')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
  <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="npwpaddress" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('NPWP Address ') }}
                                                    </label>
                                                    <div>
                                                        <input type="text"
                                                            class="form-control @error('npwpaddress') is-invalid @enderror"
                                                            id="npwpaddress" name="npwpaddress" max="255" value="{{ old('npwpaddress') }}"
                                                            placeholder="Fill Vendor NPWP Address">
                                                        @error('npwpaddress')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
<div class="row">
                                               <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="npwpnumber" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Npwp Number ') }}
                                                    </label>
                                                    <div>
                                                        <input type="number"
                                                            class="form-control @error('npwpnumber') is-invalid @enderror"
                                                            id="npwpnumber" name="npwpnumber" max="13" value="{{ old('npwpnumber') }}"
                                                            placeholder="Fill Npwp Number ">
                                                        @error('npwpnumber')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                               <div class="col-md-6">

                                              <div class="form-group">
                                                    <label for="description" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Description') }}
                                                    </label>
                                                    <div>
                                                     <textarea
                                                class="form-control" placeholder="description"
                                                id="description" name="description"value="{{ old('description') }}" aria-describedby="info-description"
                                                 
                                                style="resize: both; overflow: auto;"></textarea>
                                            @error('description')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                            @enderror
                                                      
                                                    </div>
                                                </div>
                                                </div>
                                            </div>

                                              
                                        {{-- <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="manager_id" class="form-control-label">
                                                            <i class="fas fa-shield-alt"></i> {{ __('Manager Department') }}
                                                        </label>
                                                        <div class="@error('manager_id') border border-danger rounded-3 @enderror">
                                                            <select class="form-control @error('manager_id') is-invalid @enderror" name="manager_id" id="manager_id" required>
                                                                <option value="" disabled selected>Choose Manager</option>
                                                                @foreach ($managers as $manager)
                                                            <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                                                {{ $manager->Employee->employee_name ?? $manager->name ?? 'Tanpa Nama' }}
                                                            </option>
                                                        @endforeach
                                                            </select>
                                                            @error('manager_id')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}

                                        <div class="alert alert-secondary mt-4" role="alert">
                                            <span class="text-dark">
                                                <strong>Important Note:</strong> <br>
                                            - If Vendor group already exist, you cant input the same vendor group again, for exm: PT. Dancow already registered then you can't input the same data okay.<br>

                                                - * please fill the input.<br>
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="{{ route('Vendorgroup.detail', ['hashedId' => $hashedId]) }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Back
                                            </a>

                                            <button type="submit" id="create-btn" class="btn bg-primary">
                                                <i class="fas fa-save"></i> {{ __('Create') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#type').select2({
                placeholder: 'Choose Type',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script src="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('create-btn').addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah pengiriman form langsung
            Swal.fire({
                title: 'Are You Sure?',
                text: "Make sure the data you entered is correct!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Assign!',
                cancelButtonText: 'Abort'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna mengkonfirmasi, submit form
                    document.getElementById('vendor-create').submit();
                }
            });
        });
    </script>
    <script>
        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endpush
