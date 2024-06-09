@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header pb-0 px-3">
            <h6 class="mb-0">General questions and explanations with the chatbot</h6>
            <div class="row">
                <div class="col-md-6 form-group">
                    <select class="form-select mt-4" id="know" name="know">
                        @foreach ($vocabularies as $voc)
                            <option value="{{ $voc['en'] }}">{{ $voc['is_known'] ? '✅' : '' }} {{ $voc['en'] }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <button class="btn bg-gradient-dark btn-md mt-4" id="start">Start</button>
                </div>
            </div>
        </div>
        
    </div>

    <div class="card mt-4 mb-10">
        <div class="pt-4 p-3 mx-2" id="chat-container" data-id-chat="-1">
            {{-- bot gen --}}
            <div class="row mb-2 bg-chat rounded">
                <img src="{{ asset('assets/img/bot.png') }}" alt="robot" width="94" height="94" class="col-md-1 bot-img">
                <div class="col-md-10">
                    <div class="text-sm p-2">
                        Hello, I'm a chatbot. I can help you with general questions and explanations. Let's get started!
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card pt-2 p-3 mt-4 fixed-bottom area-chat-enter">
        <div class="spinner-grow text-primary col-md-2 mb-2 d-none" id="icon-loading" role="status"></div>
        <div class="form-group">
            <textarea class="form-control" id="question" rows="3" placeholder="Make a question?" disabled name="about_me"></textarea>
        </div>
        <div class="d-flex justify-content-start">
            <button type="submit" class="btn bg-gradient-dark btn-md" id="submit" disabled>Submit</button>
        </div>
    </div>
</div>

<script>
    
    window.addEventListener('DOMContentLoaded', (event) => {
        $('#start').click(function () {
            $('.loader').show();
            var know = $('#know').val();
            var chatContainer = $('#chat-container');
            chatContainer.html('');

            $.ajax({
                url: "{{ route('exchange-start-word', ['word' => '99']) }}".replace('99', know),
                type: 'GET',
                success: function (response) {
                    var data = JSON.parse(response);
                    var data = JSON.parse(data.data);
                    var histories = data?.history ? JSON.parse(data.history) : null;
                    $('#chat-container').attr('data-id-chat', data.id);
                    
                    if (histories) {
                        histories.shift();
                        histories.forEach(history => {
                            var user = history.role === 'user';
                            var bot = history.role === 'model';

                            if (user) {
                                chatContainer.append(`
                                    <div class="row mb-2 bg-chat rounded text-end justify-content-end">
                                        <div class="col-md-10">
                                            <div class="text-sm p-2">
                                                ${history.part}
                                            </div>
                                        </div>
                                        <img src="{{ asset('assets/img/user.png') }}" alt="robot" width="94" height="94" class="col-md-1 user-img text-end">
                                    </div>
                                `);
                            } else if (bot) {
                                chatContainer.append(`
                                    <div class="row mb-2 bg-chat rounded">
                                        <img src="{{ asset('assets/img/bot.png') }}" alt="robot" width="94" height="94" class="col-md-1 bot-img">
                                        <div class="col-md-10">
                                            <div class="text-sm p-2">
                                                ${history.part}
                                            </div>
                                        </div>
                                    </div>
                                `);
                            }
                        });
                    }
                },
                error: function (error) {
                    console.log(error);
                    alert('Something went wrong');
                },
                complete: function () {
                    $('.loader').hide();
                    $('#submit').removeAttr('disabled');
                    $('#question').removeAttr('disabled');
                }
            });
        });

        $('#submit').click(function () {
            $('#submit').attr('disabled', 'disabled');
            $('#question').attr('disabled', 'disabled');
            $('#icon-loading').removeClass('d-none');
            var question = $('#question').val();
            var chatContainer = $('#chat-container');
            var id = chatContainer.attr('data-id-chat');
            chatContainer.append(`
                <div class="row mb-2 bg-chat rounded text-end justify-content-end">
                    <div class="col-md-10">
                        <div class="text-sm p-2">
                            ${question}
                        </div>
                    </div>
                    <img src="{{ asset('assets/img/user.png') }}" alt="robot" width="94" height="94" class="col-md-1 user-img text-end">
                </div>
            `);

            $.ajax({
                url: "{{ route('exchange-question', ['id' => '-99']) }}".replace('-99', id),
                type: 'GET',
                data: {
                    question: question
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    var text = data.text;
                    chatContainer.append(`
                        <div class="row mb-2 bg-chat rounded">
                            <img src="{{ asset('assets/img/bot.png') }}" alt="robot" width="94" height="94" class="col-md-1 bot-img">
                            <div class="col-md-10">
                                <div class="text-sm p-2">
                                    ${text}
                                </div>
                            </div>
                        </div>
                    `);

                    // scroll to bottom
                    chatContainer.scrollTop(chatContainer[0].scrollHeight);
                },
                error: function (error) {
                    console.log(error);
                    alert('Something went wrong');
                },
                complete: function () {
                    $('#submit').removeAttr('disabled');
                    $('#icon-loading').addClass('d-none');
                    $('#question').removeAttr('disabled');
                    $('#question').val('');
                }
            });
        });
    });

</script>

@endsection