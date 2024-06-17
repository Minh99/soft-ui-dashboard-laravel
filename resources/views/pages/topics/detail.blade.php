@extends('layouts.user_type.auth')

@section('content')
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12 col-xl-6 mb-2">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="row">
                                <div class="col-md-8 d-flex align-items-center">
                                    <h6 class="mb-0">Story about {{ $topicUser->topic_name }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <p class="text-sm">
                                {{ $content }}
                            </p>
                            <hr class="horizontal gray-light my-4">
                            <ul class="list-group">
                                <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Read and understand the story above:</strong> &nbsp; about 10 minutes</li>
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Get familiar with vocabulary:</strong> &nbsp; understand to take the test</li>
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Answer:</strong> &nbsp; questions about the story</li>
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Take the test:</strong> &nbsp; evaluate capacity</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6 mb-2">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">Words to used in this topic</h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group">
                                @foreach ($words as $k => $word)
                                <li class="list-group-item border-0 d-flex align-items-center px-0 mb-2">
                                    <div class="avatar me-3">
                                        <h2 class="text-dark text-gradient m-0"> {{ $k + 1}}</h2>
                                    </div>
                                    <div class="d-flex align-items-start flex-column justify-content-center px-2">
                                        <h6 class="mb-0 text-sm">{{ $word['word']}} <i onclick="speak('{{ $word['word'] }}')" id="vocabulary-one-listen" class="fas fa-headphones text-secondary text-sm mx-2"></i> </h6>
                                        <p class="mb-0 text-xs">{{ $word['explanation']['en'] ?? $word['explanation'] }}</p>
                                        <p class="mb-0 text-xs">{{ $word['explanation']['ko'] ?? null }}</p>
                                    </div>
                                    <a target="_blank" class="btn btn-link pe-3 ps-0 mb-0 ms-auto" href="https://papago.naver.com/?sk=en&tk=ko&hn=1&st={{ $word['word'] }}">
                                        <i class="fas fa-external-link-alt text-secondary text-sm"></i>
                                    </a>
                                </li>
                                @endforeach
                                <script>
                                    function speak(text) {
                                      const utterance = new SpeechSynthesisUtterance(text);
                                      const voices = speechSynthesis.getVoices();
                                      utterance.voice = voices[0];
                                      speechSynthesis.speak(utterance);
                                    }
                                </script>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0"> Questions and Answers (Complete at least {{ $typeQuiz == 2 ?  count($questions) : count($words) }} questions) </h6>
                        </div>
                        {{-- quiz type 1, tu luan --}}
                        <div class="card-body {{ $typeQuiz == 2 ? 'd-none' : '' }}" id="quiz" data-count-quetion="0" data-done="{{ count($words) }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="spinner-grow text-info col-md-2" id="icon-loading" role="status"></div>
                                        <p id="question" class="col-md-10">
                                            {{-- bot gen --}}
                                            <i id="question-icon" class="fas fa-question-circle"></i> &nbsp; Getting question...
                                        </p>
                                    </div>
                                    <div class="row" id="area-quiz">
                                        {{-- bot gen --}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <textarea class="form-control" id="answer" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="d-flex mt-4">
                                <button class="btn btn-primary" id="submit" data-type-btn="submit">
                                    Submit Answer
                                </button>
                            </div>
                        </div>

                        {{-- quiz type 2, trac nghiem --}}
                        <div class="card-body {{ $typeQuiz == 1 ? 'd-none' : '' }}" id="quiz-tn">
                            <i id="question-icon" class="fas fa-question-circle"></i> 
                            @foreach ($questions as $key => $question)
                                <div class="form-group">
                                    <label for="question" class="form-check-label font-weight-bold" id="label-question-{{ $key }}">
                                        {{ $key + 1 }}. {{ $question['question'] }}
                                    </label>
                                    @php
                                        $mapKeys = ['A', 'B', 'C', 'D'];
                                    @endphp
                                    <div class="form-group" id="{{ $key }}-key">
                                        @foreach ($question['answers'] as $k => $answer)
                                            <div class="form-check">
                                                <div class="form-check form-check-block">
                                                    <input class="form-check-input" type="radio"
                                                        name="{{ $key }}-value" data-key="{{$mapKeys[$k]}}" value="{{ $answer[$mapKeys[$k]] }}" id="{{ $key }}-answer{{ $k }}">
                                                    <label class="form-check-label"
                                                        for="{{ $key }}-answer{{ $k }}">{{ $answer[$mapKeys[$k]] }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="d-flex mt-4">
                                <button class="btn btn-primary" id="submit-tn" data-type-btn="submit">
                                    Submit Answer
                                </button>
                            </div>
                        </div>

                        <div id="mark-done" class="d-flex justify-content-start mx-4 d-none">
                            {{-- <a href="{{ route('quiz-for', ['topicUserId' => $topicUser->id ]) }}" class="btn btn-primary" id="submit" data-type-btn="submit">
                                The test is ready
                            </a> --}}
                            <form action="{{ route('mark-done', ['topicUserId' => $topicUser->id ]) }}" method="POST">
                                @csrf
                                <button class="btn btn-success" type="submit">Mark as done</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var typeQuiz = @json($typeQuiz);
        var questions = @json($questions);

        if (typeQuiz == 1) {
            setTimeout(() => {
                getQuestion(0);
            }, 3000);
        }

        function getQuestion(countQuetion) {
            $('#icon-loading').show();
            $('#area-quiz').html('');
            $('#question').html(`<i id="question-icon" class="fas fa-question-circle"></i> &nbsp; Getting question...`);

            let countDone = $('#quiz').data('done');

            if (parseInt(countQuetion) >= parseInt(countDone)) {
                $('#mark-done').removeClass('d-none');
            }

            $.ajax({
                url: "{{ route('generate-question', ['topicUserId' => $topicUser->id ]) }}",
                type: 'GET',
                success: function(data) {
                    var data = JSON.parse(data);
                    let status = data.status;
                    let question = data?.data?.question || '';
                    
                    if (status = 200 && question) {
                        $('#question').html(`<i id="question-icon" class="fas fa-question-circle"></i> &nbsp; ${question}`);
                    } else {
                        setTimeout(function() {
                            getQuestion(countQuetion);
                        }, 3000);
                    }
                },
                error: function() {
                    alert('Something went wrong! Please try again later.');
                },
                complete: function() {
                    $('#quiz').attr('data-count-quetion', parseInt(countQuetion) + 1);
                    $('#answer').attr('disabled', false);
                    $('#answer').val('');
                    $('#submit').data('type-btn', 'submit');
                    $('#submit').text('Submit Answer');

                    setTimeout(function() {
                        $('#submit').attr('disabled', false);
                    }, 2000);

                    $('#icon-loading').hide();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            $( document ).ready(function() {
                $('#submit').click(function() {
                    // show loading
                    $('#icon-loading').show();
                    $('#submit').attr('disabled', true);

                    var typeBtn = $('#submit').data('type-btn');

                    if (typeBtn == 'submit') {
                        if ($('#answer').val() == '') {
                            alert('Please enter your answer!');
                            $('#icon-loading').hide();
                            $('#submit').attr('disabled', false);
                            return;
                        }

                        $('#answer').attr('disabled', true);
                        let answer = $('#answer').val();
                        let question = $('#question').text();
                        $.ajax({
                            url: '{{ route("generate-answer", ["topicUserId" => $topicUser->id ]) }}' + `?answer=${answer}&question=${question}`,
                            type: 'GET',
                            success: function(data) {
                                var data = JSON.parse(data);
                                let status = data.status;
                                let is_correct = data?.data?.is_correct || false;
                                let comment = data?.data?.comment || null;
                                let correct_answer = data?.data?.correct_answer || undefined;
                                
                                if (status = 200) {
                                    let html = '';

                                    if (is_correct) {
                                        html = `
                                            <div class="row">
                                                <p class="col-md-1"> <i class="fas fa-check text-success"></i></p>
                                                <p class="col-md-11 text-success"> ${comment} </p>
                                            </div>
                                        `;
                                    } else {
                                        html = `
                                            <div class="row" id="fail">
                                                <p class="col-md-1"> <i class="fas fa-times text-danger"></i></p>
                                                <p class="col-md-11">
                                                    <span class="text-danger"> ${comment} </span>
                                                    <br>
                                                    <span class="text-success">
                                                        <i class="fas fa-check text-success"></i> ${correct_answer}
                                                    </span>
                                                </p>
                                            </div>
                                        `;
                                    }

                                    $('#area-quiz').append(html);
                                } else {
                                    alert('Something went wrong! Please try again later.');
                                }
                            },
                            error: function() {
                                alert('Something went wrong! Please try again later.');
                            },
                            complete: function() {
                                $('#submit').data('type-btn', 'next');
                                $('#submit').text('Next Question');
                                setTimeout(function() {
                                    $('#submit').attr('disabled', false);
                                }, 2000);
                                $('#icon-loading').hide();
                            }
                        });
                    } else {
                        let countQuetion = $('#quiz').attr('data-count-quetion');
                        getQuestion(countQuetion);
                    }
                });

                $('#submit-tn').click(function() {
                    var questions = @json($questions);
                    var count = 0;

                    for (let i = 0; i < questions.length; i++) {
                        var names = i + '-value';
                        var dataKey = $('input[name=' + names + ']:checked').data('key');
                        var correctAnswer = questions[i]['answer_correct'];

                        $('#label-question-' + i).find('span').remove();
                        if (dataKey === correctAnswer) {
                            $('#label-question-' + i).append('<span class="text-success text-sx">✅</span>');
                            count++;
                        } else {
                            $('#label-question-' + i).append('<span class="text-danger text-sx">❌</span>');
                        } 
                    }

                    $('#submit-tn').text('Check again');

                    if (count >= questions.length - 3) {
                        $('#mark-done').removeClass('d-none');
                    }
                });
            });
        });
    </script>
@endsection
