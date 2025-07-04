@extends('layouts.app')
@section('title', 'Vendor Groups Details')
@push('styles')
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
<style>
    /* Card Styles */
    .card {
        border: none;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background-color: #fff;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        padding: 1.25rem 1.5rem;
    }

    .card-header h6 {
        margin: 0;
        font-weight: 600;
        color: #4a5568;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .card-header h6 i {
        margin-right: 0.75rem;
        color: #5e72e4;
        transition: color 0.3s ease;
    }

    /* Table Styles */
    .table-responsive {
        padding: 0 1.5rem;
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background-color: #f8fafc;
        color: #4a5568;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem 0.75rem;
        position: sticky;
        top: 0;
        z-index: 10;
        transition: all 0.3s ease;
    }

    .table tbody tr {
        transition: all 0.25s ease;
        position: relative;
    }

    .table tbody tr:not(:last-child)::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: rgba(0, 0, 0, 0.05);
    }

    .table tbody tr:hover {
        background-color: rgba(94, 114, 228, 0.03);
        transform: scale(1.002);
    }

    .table tbody td {
        padding: 1.1rem 0.75rem;
        vertical-align: middle;
        color: #4a5568;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        border: none;
        background: #fff;
    }

    .table tbody tr:hover td {
        color: #2d3748;
    }

    /* Text alignment for specific columns */
    .text-center {
        text-align: center;
    }

    /* Action Buttons */
    .action-buttons {
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: flex-end;
    }

    .btn-primary {
        background-color: #5e72e4;
        border-color: #5e72e4;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #4a5bd1;
        border-color: #4a5bd1;
        transform: translateY(-1px);
    }

    /* Section Header */
    .section-header h1 {
        font-weight: 600;
        color: #2d3748;
        font-size: 1.5rem;
    }

    /* Smooth scroll for table */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            padding: 0 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-header {
            padding: 1rem;
        }

        .table thead th {
            font-size: 0.65rem;
            padding: 0.75rem 0.5rem;
        }

        .table tbody td {
            padding: 0.85rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>


@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Vendor Groups {{ $vendorgroup->name }}</h1>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filter-type">Filter Type</label>
                    <select id="filter-type" class="form-control select2">
                        <option value="">All Types</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-consignment">Filter is Consignment</label>
                    <select id="filter-consignment" class="form-control select2">
                        <option value="">All</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-vendorpkp">Filter is Vendor PKP</label>
                    <select id="filter-vendorpkp" class="form-control select2">
                        <option value="">All</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-bank">Filter Banks</label>
                    <select id="filter-bank" class="form-control select2">
                        <option value="">All Banks</option>
                    </select>
                </div>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-user-shield"></i>List Vendor Groups {{ $vendorgroup->name }}</h6>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="users-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No.</th>
                                                {{-- <th class="text-center">Vendor Group</th> --}}
                                                <th class="text-center">Name</th>
                                                <th class="text-center">Type</th>
                                                <th class="text-center">Code</th>
                                                <th class="text-center">Address</th>
                                                <th class="text-center">City</th>
                                                <th class="text-center">Country</th>
                                                <th class="text-center">Email</th>
                                                <th class="text-center">Phone Number</th>
                                                <th class="text-center">Consigment</th>
                                                <th class="text-center">Vendor PKP</th>
                                                <th class="text-center">Sales Name</th>
                                                <th class="text-center">Sales CP</th>
                                                <th class="text-center">NPWP Name</th>
                                                <th class="text-center">NPWP Number</th>
                                                <th class="text-center">NPWP Address</th>
                                                <th class="text-center">Vendor Fee</th>
                                                <th class="text-center">Bank Account</th>
                                                <th class="text-center">Description</th>

                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="action-buttons d-flex gap-10">
                                    <a href="{{ route('pages.Vendorgroup') }}" class="btn btn-secondary "  style="margin-right: 10px;">
                                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                                    </a>
                                    <button type="button"
                                        onclick="window.location='{{ route('Vendor.create', ['hashedId' => $hashedId]) }}'"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle"></i> Create Vendor on {{ $vendorgroup->name }}
                                    </button>
                                </div>
                                   {{-- <div class="d-flex justify-content-end mt-4">
                                            <a href="{{ route('pages.Vendorgroup') }}"
                                                class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Back
                                            </a>

                                            <button type="button" onclick="window.location='{{ route('Vendor.create', ['hashedId' => $hashedId]) }}'" id="create-btn" class="btn bg-primary">
                                                <i class="fas fa-save"></i> {{ __('Create Vendor') }}
                                            </button>
                                        </div> --}}



                                <div class="alert alert-secondary mt-4" role="alert">
                                    <span class="text-dark">
                                        <strong>Important Note:</strong> <br>
                                        - If Vendor already exist, you cant input the same vendor again, for exm: PT. Dancow
                                        already registered then you can't input the same data okay.<br>
                                        <br>

                                    </span>
                                </div>
                            </div>
                        </div>
                        <h1>List Vendor Logs</h1>
                    </div>

                    <table id="logTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Time</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">User</th>
                                <th class="text-center">Details</th>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>

@endsection
@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#type').select2({
                placeholder: 'Choose Type',
                allowClear: true,
                width: '100%'
            });
            $('#consignment').select2({
                placeholder: 'Choose Consignment',
                allowClear: true,
                width: '100%'
            });
            $('#vendorpkp').select2({
                placeholder: 'Choose Vendorpkp',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#logTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('vendor.vendor') }}',
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                columns: [{
                        data: 'waktu',
                        name: 'created_at',
                        className: 'text-center'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        className: 'text-center'
                    },
                    {
                        data: 'user',
                        name: 'causer_id',
                        className: 'text-center'
                    },
                    {
                        data: 'detail',
                        name: 'properties',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
    <script>
        jQuery(document).ready(function($) {
            const hashedId = "{{ $hashedId }}"; // pastikan variabel ini dikirim dari controller

            // Load filter options
            $.get(`/vendor/filters/${hashedId}`, function(data) {
                // Populate type options
                data.types.forEach(type => {
                    $('#filter-type').append(`<option value="${type}">${type}</option>`);
                });
                data.consignments.forEach(consignment => {
                    $('#filter-consignment').append(
                        `<option value="${consignment}">${consignment}</option>`);
                });
                data.vendorpkps.forEach(vendorpkp => {
                    $('#filter-vendorpkp').append(
                        `<option value="${vendorpkp}">${vendorpkp}</option>`);
                });
                data.banks.forEach(bank => {
                    $('#filter-bank').append(`<option value="${bank}">${bank}</option>`);
                });
            });
            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: '{{ route('vendors.vendors', $vendorgroup->id_hashed) }}',
                    type: 'GET',
                    data: function(d) {
                        d.type = $('#filter-type').val();
                        d.consignment = $('#filter-consignment').val();
                        d.vendorpkp = $('#filter-vendorpkp').val();
                        d.name = $('#filter-bank').val();

                    }
                },
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                },
                columns: [{
                        data: null,
                        name: 'id',
                        className: 'text-center align-middle',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },

                    {
                        data: 'name',
                        name: 'name',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'code',
                        name: 'code',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'address',
                        name: 'address',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'city',
                        name: 'city',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'country.country_name',
                        name: 'country.country_name',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'email',
                        name: 'email',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'phonenumber',
                        name: 'phonenumber',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'consignment',
                        name: 'consignment',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'vendorpkp',
                        name: 'vendorpkp',
                        className: 'text-center',
                        defaultContent: '-'
                    },




                    {
                        data: 'salesname',
                        name: 'salesname',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'salescp',
                        name: 'salescp',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'npwpname',
                        name: 'npwpname',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'npwpnumber',
                        name: 'npwpnumber',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'npwpaddress',
                        name: 'npwpaddress',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'vendorfee',
                        name: 'vendorfee',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'banks.name',
                        name: 'banks.name',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                  
                    {
                        data: 'description',
                        name: 'description',
                        className: 'text-center',
                        defaultContent: '-'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-control');
                }
            });
            $('#filter-type, #filter-consignment, #filter-vendorpkp, #filter-bank').change(function() {
                table.ajax.reload();
            });

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                });
            @endif

        });
    </script>
@endpush
