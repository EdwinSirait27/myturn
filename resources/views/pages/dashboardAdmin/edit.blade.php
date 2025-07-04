@extends('layouts.app')
@section('title', 'Update User')
@push('style')
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
                <h1>Update User : {{ $user->Employee->employee_name }}</h1>
                <div class="section-header-breadcrumb">
                    {{-- <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div> --}}
                    <div class="breadcrumb-item"><a href="{{ route('pages.dashboardAdmin') }}">Users</a></div>
                    <div class="breadcrumb-item">Update User : {{ $user->Employee->employee_name }}</div>
                </div>
            </div>

            <div class="section-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pb-0 px-3">
                                    <h6 class="mb-0">{{ __('Update User') }}</h6>
                                </div>
                                <div class="card-body pt-4 p-3">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
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
                                    <form id="user-edit" action="{{ route('dashboardAdmin.update', $hashedId) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="username" class="form-control-label">
                                                        <i class="fas fa-user"></i> {{ __('Username') }}
                                                    </label>
                                                    <div>
                                                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                                                            name="username" value="{{ old('username', $user->username) }}"
                                                            placeholder="edwinsirait27" required
                                                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')">
                                                        @error('username')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="password" class="form-control-label">
                                                        <i class="fas fa-lock"></i> {{ __('Password') }}
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                                                            name="password"
                                                            placeholder="Leave blank to keep current password"
                                                            aria-describedby="password-addon" maxlength="12"
                                                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9_-]/g, '');" />
                                                        <div class="input-group-append">
                                                            <span class="input-group-text" onclick="togglePassword()"
                                                                style="cursor: pointer;">
                                                                <i id="eyeIcon" class="fa fa-eye"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        Only letters, numbers, underscore, and dash allowed. Max 12
                                                        characters.
                                                    </small>
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            function togglePassword() {
                                                let passwordInput = document.getElementById('password');
                                                let eyeIcon = document.getElementById('eyeIcon');

                                                if (passwordInput.type === "password") {
                                                    passwordInput.type = "text";
                                                    eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
                                                } else {
                                                    passwordInput.type = "password";
                                                    eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
                                                }
                                            }
                                        </script>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="device_wifi_mac" class="form-control-label">
                                                        <i class="fas fa-id-card"></i> {{ __('Mac Wifi') }}
                                                    </label>
                                                    <div>
                                                        <input class="form-control @error('device_wifi_mac') is-invalid @enderror"
                                                            value="{{ old('device_wifi_mac', $user->Terms->device_wifi_mac ?? '') }}"
                                                            type="text" id="device_wifi_mac" name="device_wifi_mac"
                                                            aria-describedby="info-device_wifi_mac"
                                                            placeholder="xx-xx-xx-xx-xx" maxlength="255">
                                                        @error('device_wifi_mac')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="device_lan_mac" class="form-control-label">
                                                        <i class="fas fa-id-card"></i> {{ __('Mac Lan') }}
                                                    </label>
                                                    <div>
                                                        <input class="form-control @error('device_lan_mac') is-invalid @enderror"
                                                            value="{{ old('device_lan_mac', $user->Terms->device_lan_mac ?? '') }}"
                                                            type="text" id="device_lan_mac" name="device_lan_mac"
                                                            aria-describedby="info-device_lan_mac"
                                                            placeholder="xx-xx-xx-xx-xx" maxlength="255">
                                                        @error('device_lan_mac')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">


                                            {{-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="role" class="form-control-label">
                                                        <i class="fas fa-shield-alt"></i> {{ __('Role') }}
                                                    </label>
                                                    <div class="@error('role') border border-danger rounded-3 @enderror">
                                                        <select class="form-control @error('role') is-invalid @enderror" name="role" id="role"
                                                            required>

                                                            <option value="">Select Role</option>
                                                            @foreach ($roles as $name => $displayName)
                                                                <option value="{{ $name }}"
                                                                    {{ $selectedRole == $name ? 'selected' : '' }}>
                                                                    {{ $displayName }}
                                                                </option>
                                                            @endforeach
                                                        </select>


                                                        @error('role')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-6">
    <div class="form-group">
        <label class="form-control-label">
            <i class="fas fa-shield-alt"></i> {{ __('Role') }}
        </label>

        <div class="d-flex flex-column gap-1 @error('role') border border-danger rounded-3 p-2 @enderror">
            @foreach ($roles as $name => $displayName)
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="role[]"
                           id="role_{{ $name }}"
                           value="{{ $name }}"
                           {{ in_array($name, $selectedRole) ? 'checked' : '' }}>
                    <label class="form-check-label" for="role_{{ $name }}">
                        {{ $displayName }}
                    </label>
                </div>
            @endforeach
        </div>

        @error('role')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    </div>
</div>


                                        <div class="alert alert-secondary mt-4" role="alert">
                                            <span class="text-dark">
                                                <strong>Important Note:</strong> <br>
                                                - If a username is already registered, you cannot register it again.<br>
                                                - Leave the password field empty if you don't want to change it.
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <a href="{{ route('pages.dashboardAdmin') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> {{ __('Cancel') }}
                                            </a>
                                            <button type="submit" id="edit-btn"class="btn bg-primary">
                                                <i class="fas fa-save"></i> {{ __('Update') }}
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
    <!-- JS Libraies -->
    <script src="{{ asset('node_modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Page Specific JS File -->
    <script>
        document.getElementById('edit-btn').addEventListener('click', function(e) {
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
                    document.getElementById('user-edit').submit();
                }
            });
        });
    </script>
@endpush
