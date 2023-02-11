@extends('backend.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Offline Gateways') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{route('admin.dashboard')}}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Payment Gateways') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Offline Gateways') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title d-inline-block">{{ __('Offline Gateways') }}</div>
            </div>

            <div class="col-lg-4 offset-lg-4 mt-2 mt-lg-0">
              <a
                href="#"
                data-toggle="modal"
                data-target="#createModal"
                class="btn btn-primary btn-sm float-right"
              ><i class="fas fa-plus"></i> {{ __('Add Gateway') }}</a>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($offlineGateways) == 0)
                <h3 class="text-center">{{ __('NO OFFLINE PAYMENT GATEWAY FOUND!') }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3" id="basic-datatables">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Gateway Name') }}</th>
                        <th scope="col">{{ __('Room Booking Status') }}</th>
                        <th scope="col">{{ __('Serial Number') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($offlineGateways as $offlineGateway)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ convertUtf8($offlineGateway->name) }}</td>
                          <td>
                            <form
                              id="roomBookingForm{{ $offlineGateway->id }}"
                              class="d-inline-block"
                              action="{{ route('admin.payment_gateways.update_room_booking_status') }}"
                              method="post"
                            >
                              @csrf
                              <input type="hidden" name="offline_gateway_id" value="{{ $offlineGateway->id }}">
                              <select
                                class="form-control form-control-sm {{ $offlineGateway->room_booking_status == 1 ? 'bg-success' : 'bg-danger' }}"
                                name="room_booking_status"
                                onchange="document.getElementById('roomBookingForm{{ $offlineGateway->id }}').submit();"
                              >
                                <option
                                  value="1"
                                  {{ $offlineGateway->room_booking_status == 1 ? 'selected' : '' }}
                                >{{ __('Active') }}</option>
                                <option
                                  value="0"
                                  {{ $offlineGateway->room_booking_status == 0 ? 'selected' : '' }}
                                >{{ __('Deactive') }}</option>
                              </select>
                            </form>
                          </td>
                          <td>{{ $offlineGateway->serial_number }}</td>
                          <td>
                            <a
                              class="btn btn-secondary btn-sm editBtn mr-1"
                              href="#editModal"
                              data-toggle="modal"
                              data-id="{{ $offlineGateway->id }}"
                              data-name="{{ $offlineGateway->name }}"
                              data-short_description="{{ $offlineGateway->short_description }}"
                              data-instructions="{{ replaceBaseUrl($offlineGateway->instructions, 'summernote') }}"
                              data-attachment_status="{{ $offlineGateway->attachment_status }}"
                              data-serial_number="{{ $offlineGateway->serial_number }}"
                            >
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              {{ __('Edit') }}
                            </a>

                            <form
                              class="deleteForm d-inline-block"
                              action="{{ route('admin.payment_gateways.delete_offline_gateway') }}"
                              method="post"
                            >
                              @csrf
                              <input type="hidden" name="offline_gateway_id" value="{{ $offlineGateway->id }}">

                              <button type="submit" class="btn btn-danger btn-sm deleteBtn">
                                <span class="btn-label">
                                  <i class="fas fa-trash"></i>
                                </span>
                                {{ __('Delete') }}
                              </button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- create modal --}}
  @include('backend.payment_gateways.offline_gateways.create')

  {{-- edit modal --}}
  @include('backend.payment_gateways.offline_gateways.edit')
@endsection
