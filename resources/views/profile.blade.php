@extends('layouts.user_type.auth')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>
                New User
            </h5>
        </div>
        <div class="card-body">
            @php
                $route = route('user-management-store');
                if (!empty($user)) {
                    $route = route('user-management-store', ['id' => $user->id]);
                }
            @endphp
            <form action="{{ $route  }}" method="POST">
                @csrf
                <div class="row mt-2">
                    <div class="fom-group col-md-6">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="@if(!empty($user)){{ $user->name }}@else{{ old('name') }}@endif">
						@if ($errors->has('name'))
                            <span class="text-danger text-xs">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="fom-group col-md-6">
                        <label for="name">Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email" @if(!empty($user)) disabled @endif value="@if(!empty($user)){{ $user->email }}@else{{ old('email') }}@endif">
                        @if ($errors->has('email'))
                            <span class="text-danger text-xs">
                                {{ $errors->first('email') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="fom-group col-md-6">
                        <label for="name"> {{ !empty($user) ? 'Edit ' : '' }}Password <span class="text-danger"></span></label>
                        <input type="text" class="form-control" id="password" name="password" value="{{ old('password') }}">
						@if ($errors->has('password'))
                            <span class="text-danger text-xs">
                                {{ $errors->first('password') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="fom-group col-md-6">
                        <label for="name">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="@if(!empty($user)){{ $user->phone }}@else{{ old('phone') }}@endif">
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="fom-group col-md-6">
                        <button class="btn btn-primary col-md-3" type="submit">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
