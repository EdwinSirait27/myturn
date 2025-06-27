{{-- 
@extends('layouts.app') 
@section('title', 'Import Employee')

@push('style')

@endpush

@section('main')
<div class="main-content p-6">
    <section class="section bg-white rounded-lg shadow p-6">
        <div class="section-header mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Import Employee</h1>
        </div>

        <div class="section-body">
            <form action="{{ route('Import.employee') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700">Choose Excel File</label>
                    <input type="file" name="file" id="file" required 
                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition duration-150">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </section>
    <div class="mt-6 p-4 bg-gray-100 border-l-4 border-gray-400 rounded">
        <p class="text-sm text-gray-800">
            <strong>Important Note:</strong><br>
            - Use Excel file (.xlsx).<br>
            - CSV format may not be supported.
        </p>
    </div>
</div>
@endsection
@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

 @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
            });
        @endif
    <!-- JS Libraies -->

    <!-- Page Specific JS File -->
    </script>
@endpush --}}
@extends('layouts.app')
@section('title', 'Import Employees')
@push('styles')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
        .form-container {
            max-width: 500px;
            margin: 20px auto;
            padding: 25px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .form-actions button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-actions button:hover {
            background-color: #0056b3;
        }
    </style>
    @endpush

@section('main')<div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Import Employees</h1>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- <div class="section-body">
                <form id="import-vendor" action="{{ route('Importvendorgroup.vendorgroup') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" required>
                    <button id="create-btn" type="submit">Import</button>
                </form>
            </div> --}}
            <div class="section-body">
    <div class="form-container">
        <h2>Import Employees</h2>
        <form id="import-create" action="{{ route('Import.employee') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Choose your File Excel:</label>
                <input type="file" name="file" id="file" required>
            </div>
            <div class="form-actions"
                            style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                            <a href="{{ route('pages.Employeeall') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('Cancel') }}
                            </a>
                            <button id="create-btn" type="submit" class="btn btn-primary">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                        </div>
        </form>
    </div>
</div>
        </section>
        <div class="alert alert-secondary mt-4" role="alert">
            <span class="text-dark">
                <strong>Important Note:</strong> <br>
                - for the file use excel xlsx type, csv may not work.<br>
            </span>
        </div>
    </div>
@endsection
@push('scripts')
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
                    document.getElementById('import-create').submit();
                }
            });
        });
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ session('success') }}',
            });
        </script>
    @endif

    @if (session('errorr'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('errorr') }}',
            });
        </script>
    @endif
@endpush
