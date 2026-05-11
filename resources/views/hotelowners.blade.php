@extends('templates.template')

@section('content')

<div class="container">

    <h3 class="mb-4">Hotel Owners</h3>

    @if($owners->isEmpty())
        <div class="alert alert-info text-center">
            No hotel owners found.
        </div>
    @else

        <div class="table-responsive">
            <table class="table table-hover bg-white shadow-sm rounded align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Picture</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>GCash</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($owners as $owner)
                        <tr>

                            <!-- PICTURE -->
                            <td>
                                <img src="{{ asset('storage/' . $owner->profile_picture) }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:50%;">
                            </td>

                            <!-- FULL NAME -->
                            <td>
                                {{ $owner->first_name }}
                                @if($owner->middle_name)
                                    {{ strtoupper(substr($owner->middle_name, 0, 1)) . '.' }}
                                @endif
                                {{ $owner->last_name }}
                            </td>

                            <!-- EMAIL -->
                            <td>{{ $owner->email }}</td>

                            <!-- PHONE -->
                            <td>{{ $owner->phone_number }}</td>

                            <!-- GCASH -->
                            <td>{{ $owner->gcash_number ?? 'N/A' }}</td>

                            <!-- ACTION -->
                            <td>
                                <button class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#hotelsModal{{ $owner->uid }}">
                                    Check Hotels
                                </button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    @endif

</div>

<!-- ================= MODALS OUTSIDE TABLE (IMPORTANT FIX) ================= -->

@foreach($owners as $owner)

<div class="modal fade" id="hotelsModal{{ $owner->uid }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    {{ $owner->first_name }}'s Hotels
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                @php
                    $hotels = DB::table('hotelsdb')
                        ->where('owner_id', $owner->uid)
                        ->orderBy('created_at', 'desc')
                        ->get();
                @endphp

                @if($hotels->isEmpty())
                    <div class="text-center text-muted">
                        No hotels found for this owner.
                    </div>
                @else

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>Hotel Name</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($hotels as $hotel)
                                    <tr>

                                        <td class="fw-bold">
                                            {{ $hotel->hotel_name }}
                                        </td>

                                        <td>
                                            {{ $hotel->hotel_address }}
                                        </td>

                                        <td>
                                            <span class="badge bg-secondary text-uppercase">
                                                {{ $hotel->status }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($hotel->created_at)->format('Y-m-d') }}
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @endif

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

@endforeach

@endsection