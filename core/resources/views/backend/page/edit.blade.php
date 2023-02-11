@extends('backend.layout')
@section('content')
<div class="page-header">
   <h4 class="page-title">Pages</h4>
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
         <a href="#">Edit Page</a>
      </li>
      <li class="separator">
         <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
         <a href="#">Pages</a>
      </li>
   </ul>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header">
            <div class="card-title">Edit Page</div>
         </div>
         <div class="card-body pt-5 pb-4">
            <div class="row">
               <div class="col-lg-10 offset-lg-1">
                <div class="alert alert-danger" id="pageErrors" style="display: none;">
                    <ul>

                    </ul>
                </div>

                <form id="pageForm" action="{{route('admin.page.update')}}" method="post">
                    @csrf
                    <input type="hidden" name="pageid" value="{{$page->id}}">
                    <div class="row">
                        <div class="col-lg-6">
                             <div class="form-group">
                                 <label for="">Status **</label>
                                 <select class="form-control ltr" name="status">
                                     <option value="1" {{$page->status == 1 ? 'selected' : ''}}>Active</option>
                                     <option value="0" {{$page->status == 0 ? 'selected' : ''}}>Deactive</option>
                                 </select>
                             </div>
                        </div>
                       <div class="col-lg-6">
                           <div class="form-group">
                               <label for="">Serial Number **</label>
                               <input type="number" class="form-control ltr" name="serial_number" value="{{$page->serial_number}}" placeholder="Enter Serial Number">
                               <p class="text-warning mb-0"><small>The higher the serial number is, the later the page will be shown.</small></p>
                            </div>
                       </div>
                    </div>



                    <div id="accordion" class="mt-5">
                        @foreach ($languages as $language)
                            @php
                                $pc = $language->page_contents()->where('page_id', $page->id)->first();
                                $name = !empty($pc) ? $pc->name : '';
                                $body = !empty($pc) ? $pc->body : '';
                                $meta_keywords = !empty($pc) ? $pc->meta_keywords : '';
                                $meta_description = !empty($pc) ? $pc->meta_description : '';
                            @endphp

                           <div class="version">
                               <div class="version-header" id="heading{{$language->id}}">
                                   <h5 class="mb-0">
                                       <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$language->id}}" aria-expanded="{{$language->is_default == 1 ? 'true' : 'false'}}" aria-controls="collapse{{$language->id}}">
                                       {{$language->name}} Language {{$language->is_default == 1 ? "(Default)" : ""}}
                                       </button>
                                   </h5>
                               </div>


                               <div id="collapse{{$language->id}}" class="collapse {{$language->is_default == 1 ? 'show' : ''}}" aria-labelledby="heading{{$language->id}}" data-parent="#accordion">
                                   <div class="version-body">
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <div class="form-group">
                                                   <label for="">Name **</label>
                                                   <input type="text" class="form-control {{$language->direction == 1 ? 'rtl' : ''}}" name="{{$language->code}}_name" value="{{$name}}" placeholder="Enter name">
                                               </div>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <div class="form-group {{$language->direction == 1 ? 'rtl text-right' : ''}}">
                                                   <label for="" class="{{$language->direction == 1 ? 'text-left d-block ltr' : ''}}">Body **</label>
                                                   <textarea class="form-control summernote" name="{{$language->code}}_body" placeholder="Enter body" data-height="300">{{replaceBaseUrl($body, 'summernote')}}</textarea>
                                               </div>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <div class="form-group {{$language->direction == 1 ? 'rtl text-right' : ''}}">
                                                   <label class="{{$language->direction == 1 ? 'ltr text-left d-block' : ''}}">Meta Keywords</label>
                                                   <input class="form-control" name="{{$language->code}}_meta_keywords" value="{{$meta_keywords}}" placeholder="Enter meta keywords" data-role="tagsinput">
                                               </div>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <div class="form-group">
                                                   <label>Meta Description</label>
                                                   <textarea class="form-control {{$language->direction == 1 ? 'rtl text-right' : ''}}" name="{{$language->code}}_meta_description" rows="5" placeholder="Enter meta description">{{$meta_description}}</textarea>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                               </div>
                           </div>
                        @endforeach
                    </div>
                 </form>
               </div>
            </div>
         </div>
         <div class="card-footer">
            <div class="form">
               <div class="form-group from-show-notify row">
                  <div class="col-12 text-center">
                     <button type="submit" form="pageForm" class="btn btn-success">Submit</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/js/admin-page.js')}}"></script>
@endsection
