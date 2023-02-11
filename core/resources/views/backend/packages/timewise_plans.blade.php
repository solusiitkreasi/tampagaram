@extends('backend.layout')

{{-- this style will be applied when the direction of language is right-to-left --}}
@includeIf('backend.partials.rtl_style')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Plans') }}</h4>
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
        <a href="#">{{ __('Packages Management') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Packages') }}</a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Plans') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-4">
              <div class="card-title d-inline-block">{{ __('Package\'s Timewise Plans') }}</div>
            </div>
            <div class="col-lg-3">
                @includeIf('backend.partials.languages')
            </div>

            <div class="col-lg-5 mt-2 mt-lg-0">
              <button
                class="btn btn-danger btn-sm float-right d-none bulk-delete"
                data-href="{{ route('admin.packages_management.bulk_delete_plan') }}"
              ><i class="flaticon-interface-5"></i> {{ __('Delete') }}</button>
              <a href="#" class="btn btn-primary btn-sm planBtn float-right" data-id="{{ $package->id }}" data-plan_type="{{ $package->plan_type }}">Add Plan</a>
            </div>
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              @if (count($plans) == 0)
                <h3 class="text-center">{{ __('NO PLAN FOUND FOR THIS PACKAGE!') }}</h3>
              @else
                <div class="table-responsive">
                  <table class="table table-striped mt-3">
                    <thead>
                      <tr>
                        <th scope="col">
                          <input type="checkbox" class="bulk-check" data-val="all">
                        </th>
                        <th scope="col">{{ __('Start Time') }}</th>
                        <th scope="col">{{ __('End Time') }}</th>
                        <th scope="col">{{ __('Title') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($plans as $plan)
                        <tr>
                          <td>
                            <input type="checkbox" class="bulk-check" data-val="{{ $plan->id }}">
                          </td>
                          <td>{{ $plan->start_time }}</td>
                          <td>{{ $plan->end_time }}</td>
                          <td>
                            @if ($plan->title != null)
                              {{ strlen($plan->title) > 100 ? convertUtf8(substr($plan->title, 0, 100)) . '...' : convertUtf8($plan->title) }}
                            @else
                              -
                            @endif
                          </td>
                          <td>
                            <a
                              class="btn btn-secondary btn-sm mr-1 editBtn"
                              href="#"
                              data-toggle="modal"
                              data-target="#editTimewisePlanModal"
                              data-id="{{ $plan->id }}"
                              data-start_time="{{ $plan->start_time }}"
                              data-end_time="{{ $plan->end_time }}"
                              data-title="{{ $plan->title }}"
                              data-plan="{{ replaceBaseUrl($plan->plan, 'summernote') }}"
                            >
                              <span class="btn-label">
                                <i class="fas fa-edit"></i>
                              </span>
                              {{ __('Edit') }}
                            </a>

                            <form
                              class="deleteForm d-inline-block"
                              action="{{ route('admin.packages_management.delete_plan') }}"
                              method="post"
                            >
                              @csrf
                              <input type="hidden" name="plan_id" value="{{ $plan->id }}">

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

        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{ $plans->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- edit modal --}}
  @include('backend.packages.edit_timewise_plan')

  {{-- add package plan modal (timewise) --}}
  @include('backend.packages.create_timewise_plan')
@endsection
