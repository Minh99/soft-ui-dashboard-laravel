@extends('layouts.user_type.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Quiz</h4>
                        <span>
                            Fill in the blank with the correct word

                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <label for="question" class="form-label font-weight-bold" style="font-size: 15px">
                                1. 저는 장학금을 받기 위해 열심히 공부하고 있습니다.
                            </label>
                            <p>➡️ Scholarship, Scholarship ⬅️</p>
                            <div class="form-row align-items-center">
                                <div class="col-auto">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" placeholder="Enter answer">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mb-2 mt-2">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
