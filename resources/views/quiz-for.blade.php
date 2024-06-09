@extends('layouts.user_type.auth')

@section('content')
    {{-- quiz --}}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Quiz</h4>
                        <span>{{ count($quizTypeRandom) }}  questions</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('quiz-for-submit') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                @foreach ($quizTypeRandom as $key => $question)
                                @if ($question['type'] == 'radio')
                                    <label for="question" class="form-check-label font-weight-bold">{{ $key + 1 }}. Do you know "<font style="color: rgb(85, 18, 18)">{{ $question['en'] }}</font>" mean? </label>
                                    <div class="form-check">
                                        <div class="form-check form-check-block">
                                            <input class="form-check-input" type="radio" name="{{ $question['id'] }}-en" id="{{ $question['id'] }}-answer1" value="1">
                                            <label class="form-check-label" for="{{ $question['id'] }}-answer1">Yes</label>
                                        </div>
                                        <div class="form-check form-check-block">
                                            <input class="form-check-input" type="radio" name="{{ $question['id'] }}-en" id="{{ $question['id'] }}-answer2" value="2">
                                            <label class="form-check-label" for="{{ $question['id'] }}-answer2">No</label>
                                        </div>
                                        <div class="form-check form-check-block">
                                            <input class="form-check-input" type="radio" name="{{ $question['id'] }}-en" id="{{ $question['id'] }}-answer3" value="3">
                                            <label class="form-check-label" for="{{ $question['id'] }}-answer3">Not sure</label>
                                        </div>
                                    </div>
                                @else
                                    <label for="{{ $question['id'] }}-answer1" class="form-check-label font-weight-bold">{{ $key + 1 }}. What is the meaning of "<font style="color: rgb(85, 18, 18)">{{ $question['ko'] }}</font>"? </label>
                                    <div class="form-group">
                                        <input type="text" name="{{ $question['id'] }}-ko" class="form-control mb-2 mx-2" id="{{ $question['id'] }}-answer1" placeholder="Enter answer">
                                    </div>
                                @endif
                                @endforeach
                            </div>
                           
                            <button type="submit" class="btn btn-primary float-right">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- quiz --}}

@endsection
