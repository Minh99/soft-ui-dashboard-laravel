@extends('layouts.user_type.auth')

@section('content')
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12 mt-4">
                    <div class="card mb-4">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-1">Topics</h6>
                            <p class="text-sm">Choose your desired topic and let's get started</p>
                        </div>
                        {{-- <div class="card-body">
                            <div class="card h-100 card-plain border">
                                <div class="card-body d-flex flex-column justify-content-center text-center">
                                    <a href="javascript:;">
                                        <i class="fa fa-plus text-secondary mb-3"></i>
                                        <h5 class=" text-secondary"> New topic </h5>
                                    </a>
                                </div>
                            </div>
                        </div> --}}
                        <div class="card-body p-3">
                            <div class="row">
                                <style>
                                    .topic-image:hover {
                                        transform: scale(1.1);
                                        transition: transform 1.5s;
                                    }
                                    .topic-image:hover::after {
                                        content: "";
                                        position: absolute;
                                        top: 0;
                                        left: 0;
                                        right: 0;
                                        bottom: 0;
                                        background-color: rgba(46, 45, 46, 0.5);
                                        border-radius: 10px;
                                    }
                                </style>
                                @foreach ($topics as $topic)
                                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card card-blog pb-2 px-2 rounded">
                                        <div class="position-relative topic-image"
                                            style="background-image: url('{{ $topic->image }}'); background-size: cover; background-position: center; height: 150px; border-radius: 10px; overflow: hidden;">
                                        </div>
                                        <div class="card-body px-1 pb-0">
                                            <a href="javascript:;">
                                                <p class="text-gradient text-dark mb-2 text-sm">{{ $topic->name }}</p>
                                            </a>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <a data-topic-id="{{ $topic->id }}"  class="btn btn-outline-primary btn-sm mb-0 btn-go-to-topic-detail font-weight-bold">
                                                    Start
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
