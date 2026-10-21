@extends('admin.layout.master')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table td,
        .table th {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    @include('admin.layout.response_message')

    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Affiliates</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Affiliates</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <!-- <div class="card-header justify-content-between">
                    <div class="card-title">
                        Users
                    </div>
                    <div class="prism-toggle">
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne6">
                            Search
                        </a>
                        @can('create_user')
                            <a href="{{ route('admin-admin_users.create') }}" class="btn btn-primary"
                                style="margin-right: 10px;">
                                Add User
                            </a>
                        @endcan
                        @can('view_user')
                            <a href="{{ route('admin-admin_users.export-users') }}" class="btn btn-success"
                                style="margin-right: 10px;">
                                Export
                            </a>
                        @endcan
                        <a href="{{ route('admin-admin_users.import-users') }}" class="btn btn-warning"
                            style="margin-right: 10px;">
                            Import
                        </a>
                    </div>
                </div> -->
                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                    <div id="collapseOne6" class="collapse m-3 {{ request('show')? 'show' : ''  }} ?>" data-parent="#accordionExample6">
                        <div>
                            <!-- <form id="listSearchForm" class="row mb-6"> -->
                            <form id="listSearchForm" class="row mb-6" method="GET" action="{{ route('admin-admin_users.index') }}">
                                <div class="col-lg-3  mb-lg-5 mb-6">
                                    <label>Status</label>
                                    <select name="is_active" class="form-control select2init">
                                        <option value="" {{ (request('is_active') ?? '') == '' ? 'selected' : '' }}>All</option>
                                        <option value="1" {{ (request('is_active') ?? '') == '1' ? 'selected' : '' }}>Activate</option>
                                        <option value="0" {{ (request('is_active') ?? '') == '0' ? 'selected' : '' }}>Deactivate</option>
                                    </select>
                                </div>

                                <div class="col-lg-3  mb-lg-5 mb-6">
                                    <label>Role</label>
                                    <select name="role" class="form-control select2init">
                                        <option value="" {{ (request('role') ?? '') == '' ? 'selected' : '' }}>All</option>
                                        @if (!empty($roles))
                                            @foreach ($roles as $id=>$name)
                                                <option value="{{ $id }}" {{ (request('role') ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <input type="hidden"  name="show" value="show">

                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" placeholder=" Name"
                                        value="{{request('name') ?? '' }}">
                                </div>


                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="email" placeholder="Email"
                                        value="{{request('email') ?? '' }}">
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" name="phone_number"
                                        placeholder="Phone Number"  value="{{request('phone_number') ?? '' }}">
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_from" class="form-label">Date
                                        From</label>
                                    <input type="date" class="form-control" value="{{ request('date_from') }}" id="date_from" name="date_from" placeholder="Date From">
                                </div>
                                <div class="col-lg-3 mb-lg-5 mb-6">
                                    <label for="date_to" class="form-label">Date
                                        To</label>
                                    <input type="date" class="form-control" value="{{ request('date_to') }}" id="date_to" name="date_to" placeholder="Date To">
                                </div>

                                <div class="row mt-8">
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary btn-primary--icon" id="kt_search_btn">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Search</span>
                                            </span>
                                        </button>
                                        &nbsp;&nbsp;
                                        <a href='{{ route('admin-' . "$model.index") }}'
                                            class="btn btn-secondary btn-secondary--icon">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Clear Search</span>
                                            </span>
                                        </a>
                                    </div>
                               </div>
                               <hr>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="container mt-4">
                    <button type="button" class="btn btn-outline-primary my-1 me-2" fdprocessedid="g9dg58f"> Total Affiliates:
                        <span class="badge ms-2 totalDataCount">{{ $totalResults }}</span> </button>

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
                <div class="table-responsive">
                    <table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap"
                        style="width:100%">
                        <thead>
                            <tr id="tableHeaders">
                                <th class="sortable">Registration Date</th>
                                <th class="sortable" data-column="name">User Name <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <!-- <th class="sortable" data-column="email">Email Subscription<i class="sort-icon ri-sort-asc"></i></th> -->
                                <th class="sortable" data-column="phone_number">Location<i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="gender">Orders<i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="gender">Payable Amount<i class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="is_active">Role <i class="sort-icon ri-sort-asc"></i></th>

                                <!-- <th class="sortable" data-column="is_active">Total Refunded <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th class="sortable" data-column="is_active">Total Wallet <i
                                        class="sort-icon ri-sort-asc"></i></th> -->
                                <th class="sortable" data-column="is_active">Status <i
                                        class="sort-icon ri-sort-asc"></i></th>
                                <th>Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="loader-row" style="display: none;">
                                <td colspan="9" style="text-align: center;">
                                    <button class="btn btn-light" type="button" disabled="">
                                        <span class="spinner-grow spinner-grow-sm align-middle" role="status"
                                            aria-hidden="true"></span> Loading...
                                    </button>
                                </td>
                            </tr>
                            @if ($results->isNotEmpty())
                                @include('admin.admin_users.load_more_data_affiliates', ['results' => $results])
                            @else
                                <tr>
                                    <td colspan="9" style="text-align: center;">No results found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($results->isNotEmpty() && $totalResults > Config('Reading.records_per_page'))
                    <div class="my-3" style="display: flex; justify-content: center;">
                        <button class="btn btn-primary-light btn-border-down" fdprocessedid="l5zhli" id="load-more"
                            data-offset="{{ Config('Reading.records_per_page') }}" data-default-offset="0"
                            data-limit="{{ Config('Reading.records_per_page') }}"
                            data-default-limit="{{ Config('Reading.records_per_page') }}">
                            <span class="loadMoreText me-2">Load More</span>
                            <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                        </button>
                    </div>
                @else
                    <div class="my-3" style="display: flex; justify-content: center;">
                        <button class="btn btn-primary-light btn-border-down" style="display:none;"
                            fdprocessedid="l5zhli" id="load-more" data-offset="{{ Config('Reading.records_per_page') }}"
                            data-default-offset="0" data-limit="{{ Config('Reading.records_per_page') }}"
                            data-default-limit="{{ Config('Reading.records_per_page') }}">
                            <span class="loadMoreText me-2">Load More</span>
                            <span class="loading"><i class="ri-refresh-line fs-16"></i></span>
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>
    </div>

    <!-- User Login HistroyLog Popup -->
<div class="modal fade modal-light" id="userHistoryPopup" tabindex="-1" aria-labelledby="exampleModalLabel"  aria-hidden="true" style="width:90% !important; --bs-modal-width: 1280px;">
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
		<i class="fa-solid fa-xmark"></i>
	</button>
	<!--<div class="modal-dialog modal-dialog-centered">-->
		<div class="modal-dialog" style="width:90% !important">
		<div class="modal-content" style="--bs-modal-width: 1600px;">
			<h3>User Login Histroy</h3>
			<table id="datatable-basic" data-sorting="" data-order="" class="table table-bordered text-nowrap" style="width:100%">
                <thead>
                    <tr id="tableHeaders">
						<th>Sr. No.</th>
                        <th>User</th>
                        <th>IP</th>
                        <th>Login Time</th>
                    </tr>
				</thead>
				<tbody id="userHistoryContent">
				</tbody>
			</table>
		</div>
	</div>
</div>
<!-- User Login Histroy Log Popup -->

<!-- Affliate Payment Popup -->
<div class="modal fade modal-light" id="paymentPopup" tabindex="-1" aria-labelledby="exampleModalLabel"  aria-hidden="true" style="width:90% !important; --bs-modal-width: 1280px;">
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
		<i class="fa-solid fa-xmark"></i>
	</button>
	<!--<div class="modal-dialog modal-dialog-centered">-->
    <div class="modal-dialog" style="width:90% !important">
		<div class="modal-content" style="--bs-modal-width: 1600px;">
            <div class="modal-header">
                <h5>Affliate Payment</h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- product details inner end -->
                <form id="affiliatePaymentFrom" action="{{ route('admin-admin_users.save-affiliate-payment') }}" method="POST" enctype="multipart/form-data" autocomplete="off" >
                @csrf
                <div class="edit-account">
                    <div class="row">
                        <input type="hidden" name="affiliate_id" id="affiliate_id" value=''/>
                        <div class="col-lg-12 col-md-6">
                            <div class="form-group">
                                <label>Affiliate Name</label>
                                <input type="text" name="affiliate" id="affiliate" class="form-control affiliate"  value='' readonly/>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-6">
                            <div class="form-group">
                                <label>Payable Amount</label>
                                <input type="text" name="payable_amount" id="payable_amount" class="form-control payable_amount" value='' readonly required/>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label>Custom Amount</label>
                                <input type="text" name="paid_amount" id="paid_amount" class="form-control login edit_name" autocomplete="off" required/>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label>Remark</label>
                                <textarea name="description" id="description" class="form-control" row='10' required></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label>Transaction Id</label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control" autocomplete="off" required/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label>Payment Type</label>
                                <select class="form-control" name="payment_type" id="payment_type" autocomplete="off" required>
                                    <option value="">Select payment Type</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Online">Online</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label>Any Reciept Image/PDF file</label>
                                <input type="file" name="image" id="file" accept="image/*" class="form-control" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <span class="add-adddress-error" style="color:red;font-size:16px"></span>
                    <span class="add-adddress-success" style="color:green;font-size:16px"></span>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <button type="submit" class="sqr-btn mt-3 d-block paymentPopupSubmitBtn">Payment
                                <svg width="30" height="8" viewBox="0 0 30 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 3.5C0.723858 3.5 0.5 3.72386 0.5 4C0.5 4.27614 0.723858 4.5 1 4.5V3.5ZM29.3536 4.35355C29.5488 4.15829 29.5488 3.84171 29.3536 3.64645L26.1716 0.464466C25.9763 0.269204 25.6597 0.269204 25.4645 0.464466C25.2692 0.659728 25.2692 0.976311 25.4645 1.17157L28.2929 4L25.4645 6.82843C25.2692 7.02369 25.2692 7.34027 25.4645 7.53553C25.6597 7.7308 25.9763 7.7308 26.1716 7.53553L29.3536 4.35355ZM1 4.5H29V3.5H1V4.5Z" fill="black" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            <!-- product details inner end -->
            </div>
        </div>
    </div>
</div>
<!-- Affliate Payment Popup -->
@endsection



@push('scripts')
    <!-- Datatables Cdn -->
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script>
        var routeName = '{{ route($listRouteName) }}';
        // Your DataTables initialization or other JavaScript logic here
    </script>
    <!-- Internal Datatables JS -->
    <script src="{{ asset('assets/js/datatables.js') }}"></script>

    <!-- Sweetalerts JS -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>

    <script>
        $(document).on('click', '.userHistoryPopupBtn', function () {
            var user_id = $(this).attr('data-id');
        
            if (user_id) {
                $.ajax({
                    type : 'POST',
                    url : "<?php echo route('admin-admin_users.loginhistory'); ?>",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        user_id: user_id,
                    },
                    success: function (response) {
                        $('#userHistoryContent').html(response);
                    },
                    error: function (e) {
                        Swal.fire(
                            "Server error",
                            'Something is wrong',
                            "error"
                        );
                    }
                });
            } 
        });


        /* get affilaite detail by id */
        var affiliateDetailUrl = @json(route('admin-admin_users.get-affiliate', ['id' => '__AFFILIATE_ID__']));

        $(document).on('click', '.get-affiliate-detail', function () {
            // Show the modal first
            $('#paymentPopup').modal('show');

            $('#affiliate_id').val('');
            $('.affiliate').val('');
            $('.affiliate').prop('readonly',false);
            $('.payable_amount').val('');
            $('.payable_amount').prop('readonly',false);

            var affiliate_id = $(this).attr('data-id');
            if (affiliate_id) {
                $.ajax({
                    url: affiliateDetailUrl.replace('__AFFILIATE_ID__', encodeURIComponent(affiliate_id)),
                    type: 'GET',
                    success: function (response) {
                        if (response && response.success && response.data) {
                            $('#affiliate_id').val(response.data.id).trigger('change');;
                            $('.affiliate').val(response.data.name).trigger('change');;
                            $('.affiliate').prop('readonly',true).trigger('change');;
                            $('.payable_amount').val(response.data.payable_amount);
                            $('.payable_amount').prop('readonly',true);
                        } else {
                            $('.payable_amount').val('');
                            Swal.fire('Unable to load affiliate', response.message || 'Please try again.', 'error');
                        } 
                    },
                    error: function (xhr) {
                        $('.payable_amount').val('');
                        Swal.fire(
                            'Unable to load affiliate',
                            xhr.responseJSON && xhr.responseJSON.message
                                ? xhr.responseJSON.message
                                : 'Please try again.',
                            'error'
                        );
                    }
                });
            } 
        });
        /* get affilaite detail by id */
    </script>
        
@endpush
