@extends('layouts.user_type.auth')

@section('content')
    {{-- quiz --}}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Quiz</h4>
                        <span>{{ count($quizTypeRandom) }} questions</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('quiz-first-submit') }}" method="POST" id="quiz-first-form">
                            @csrf
                            <div class="form-group">
                                @foreach ($quizTypeRandom as $key => $question)
                                    @if ($question['type'] == 'radio')
                                        <label for="question" class="form-check-label font-weight-bold">{{ $key + 1 }}.
                                            Do you know "<font style="color: rgb(85, 18, 18)">{{ $question['en'] }}</font>"
                                            mean? 
                                        </label>
                                        <div class="form-check" id="{{ $question['id'] }}">
                                            <div class="form-check form-check-block">
                                                <input class="form-check-input" type="radio"
                                                    name="{{ $question['id'] }}-en" id="{{ $question['id'] }}-answer1"
                                                    value="1">
                                                <label class="form-check-label"
                                                    for="{{ $question['id'] }}-answer1">Yes</label>
                                            </div>
                                            <div class="form-check form-check-block">
                                                <input class="form-check-input" type="radio"
                                                    name="{{ $question['id'] }}-en" id="{{ $question['id'] }}-answer2"
                                                    value="2">
                                                <label class="form-check-label"
                                                    for="{{ $question['id'] }}-answer2">No</label>
                                            </div>
                                            <div class="form-check form-check-block">
                                                <input class="form-check-input" type="radio"
                                                    name="{{ $question['id'] }}-en" id="{{ $question['id'] }}-answer3"
                                                    value="3">
                                                <label class="form-check-label" for="{{ $question['id'] }}-answer3">Not
                                                    sure</label>
                                            </div>
                                        </div>
                                    @else
                                        <label for="{{ $question['id'] }}-answer1"
                                            class="form-check-label font-weight-bold">{{ $key + 1 }}. What is the
                                            meaning of "<font style="color: rgb(85, 18, 18)">{{ $question['ko'] }}</font>"?
                                        </label>
                                        <div class="form-group">
                                            <input type="text" name="{{ $question['id'] }}-ko"
                                                class="form-control mb-2 mx-2" id="{{ $question['id'] }}-answer1"
                                                placeholder="Enter answer">
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-primary float-right"
                                id="submit-first-quiz">Submit
                            </button>
                            <button hidden type="button" id="showModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              ...
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-closed" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary btn-closed" data-bs-dismiss="modal">Understood</button>
            </div>
          </div>
        </div>
      </div>
    {{-- quiz --}}
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            var count = 0;
            var quizTypeRandom = @json($quizTypeRandom);
            var quizTypeRandomLength = quizTypeRandom.length;
            var modal = '#staticBackdrop';
            var modalTitle = '#staticBackdropLabel';
            var modalBody = '.modal-body';
            var showModal = '#showModal';
            var btnClose = '.btn-closed';
            var itemsDom = '.form-check-input';

            $(itemsDom).click(function() {
                var id = $(this).attr('name').split('-')[0];
                $('#' + id).removeClass('border border-danger');
            });

            $('#submit-first-quiz').click(function(e) {
                e.preventDefault();
                var unChecked = [];
                for (let i = 0; i < quizTypeRandomLength; i++) {
                    var id = quizTypeRandom[i]['id'];
                    var type = quizTypeRandom[i]['type'];
                    if (type == 'radio') {
                        var answer = $('input[name=' + id + '-en]:checked').val();
                        if (answer == undefined) {
                            $('#' + id).addClass('border border-danger');
                            unChecked.push(id);
                        }
                    } else {
                        var answer = $('#' + id + '-answer1').val();
                        if (answer == '') {
                            $('#' + id).addClass('border border-danger');
                            unChecked.push(id);
                        }
                    }
                }

                if (unChecked.length > 0) {
                    $(modalTitle).text('Warning');
                    $(modalBody).text('Please answer all questions, before submit');
                    $(showModal).click();
                    return;
                }

                $('#quiz-first-form').submit();
            });

        });
    </script>
@endsection
