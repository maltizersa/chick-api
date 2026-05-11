@extends('templates.template')

@section('content')

<div class="container">

    <h3 class="mb-4">Pending Hotels</h3>

    @if($hotels->isEmpty())
        <div class="alert alert-info text-center">
            No pending hotels at the moment.
        </div>
    @else

        <div class="table-responsive">
            <table class="table table-hover bg-white shadow-sm rounded">

                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Owner</th>
                        <th>Hotel Name</th>
                        <th>Address</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>PDF</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($hotels as $hotel)
                        <tr>

                            <!-- IMAGE -->
                            <td>
                                <img src="{{ asset('storage/' . $hotel->hotel_image_loc) }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                            </td>

                            <!-- OWNER -->
                            <td>
                                {{ $hotel->first_name }}
                                @if($hotel->middle_name)
                                    {{ strtoupper(substr($hotel->middle_name, 0, 1)) . '.' }}
                                @endif
                                {{ $hotel->last_name }}
                            </td>

                            <!-- HOTEL NAME -->
                            <td class="fw-bold">
                                {{ $hotel->hotel_name }}
                            </td>

                            <!-- ADDRESS -->
                            <td>
                                {{ $hotel->hotel_address }}
                            </td>

                            <!-- CONTACT -->
                            <td>
                                {{ $hotel->hotel_contact }}
                            </td>

                            <!-- STATUS -->
                            <td>
                                <span class="badge bg-secondary text-uppercase">
                                    {{ $hotel->status }}
                                </span>
                            </td>

                            <!-- DATE -->
                            <td>
                                {{ \Carbon\Carbon::parse($hotel->created_at)->format('Y-m-d') }}
                            </td>

                            <!-- PDF -->
                            <td>
                                @if($hotel->hotel_pdf_loc)

                                    <a href="{{ asset('storage/' . $hotel->hotel_pdf_loc) }}"
                                       target="_blank"
                                       class="btn btn-info btn-sm mb-1 w-100">
                                        View PDF
                                    </a>

                                    <a href="{{ asset('storage/' . $hotel->hotel_pdf_loc) }}"
                                       download="{{ $hotel->hotel_name }}_{{ \Carbon\Carbon::parse($hotel->created_at)->format('Y-m-d') }}.pdf"
                                       class="btn btn-success btn-sm w-100">
                                        Download
                                    </a>

                                @else
                                    <span class="text-muted">No PDF</span>
                                @endif
                            </td>

                            <!-- ACTION -->
                            <td>

                                <!-- APPROVE -->
                                <button class="btn btn-success btn-sm w-100 mb-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal{{ $hotel->id }}">
                                    Approve
                                </button>

                                <!-- DENY -->
                                <button class="btn btn-danger btn-sm w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#denyModal{{ $hotel->id }}">
                                    Deny
                                </button>

                            </td>

                        </tr>

                        <!-- APPROVE MODAL -->
                        <div class="modal fade" id="approveModal{{ $hotel->id }}" tabindex="-1">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                              <div class="modal-header">
                                <h5 class="modal-title">Confirm Approve</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body">
                                Approve <b>{{ $hotel->hotel_name }}</b>?
                              </div>

                              <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="/hotel/approve/{{ $hotel->id }}" class="btn btn-success">
                                    Yes, Approve
                                </a>
                              </div>

                            </div>
                          </div>
                        </div>

                        <!-- DENY MODAL -->
                        <div class="modal fade" id="denyModal{{ $hotel->id }}" tabindex="-1">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                              <div class="modal-header">
                                <h5 class="modal-title">Confirm Deny</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body">
                                Deny <b>{{ $hotel->hotel_name }}</b>?
                              </div>

                              <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <a href="/hotel/deny/{{ $hotel->id }}" class="btn btn-danger">
                                    Yes, Deny
                                </a>
                              </div>

                            </div>
                          </div>
                        </div>

                    @endforeach
                </tbody>

            </table>
        </div>

    @endif

</div>

@endsection