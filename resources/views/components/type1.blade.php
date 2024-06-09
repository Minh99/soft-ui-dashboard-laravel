@foreach ($data as $item)
    <div class="row">
        <div class="form-group sentence-item" id="sentence-{{ $item['id'] }}" data-uid="{{ $item['id'] }}" data-sentence="{{ $item['sentence'] }}">
            <p class="form-check-label font-weight-bold">
                {{ $item['id'] }}. {{ $item['sentence'] }}
            </p>
            <div class="form-check" id="sentence-options">
                @foreach ($item['options'] as $k => $value)
                    <div class="form-check form-check-block">
                        <input class="form-check-input" type="radio"
                            id="{{$item['id'] .$k}}-answer" name="{{$item['id']}}-name"
                            value="{{ $value }}">
                        <label class="form-check-label"
                            for="{{$item['id'] .$k}}-answer">{{$value}}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <hr>
    </div>
@endforeach
<button type="button" class="btn btn-primary" id="submit">Submit</button>
<button type="button" class="btn btn-success d-none" id="submit-done"> Mark as done </button>

<script>
    const data = @json($data);
    const userTest2Id = @json($userTest2Id);
    console.log(data);
    console.log(userTest2Id);
    document.addEventListener('DOMContentLoaded', function() {
        $('#submit').click(function() {
            const selected = $(`input[type='radio']:checked`);
            if (selected === undefined) {
                alert('Please select an answer');
                return;
            }

            if (selected.length < data.length) {
                alert('Please select an answer');
                return;
            }

            var dataSubmit = [];
            var sentences = $('.sentence-item');
            console.log(sentences);
            sentences.each(function(index, item) {
                console.log(item);
                const id = $(item).data('uid');
                const sentence = $(item).data('sentence');
                const answer = $(item).find('input[type="radio"]:checked').val();
                dataSubmit.push({
                    id: id,
                    sentence: sentence,
                    user_answer: answer
                });
            });

            console.log(dataSubmit);
            $('.loader').show();

            $.ajax({
                url: "{{ route('test2-submit') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_test2_id: userTest2Id,
                    data: dataSubmit
                },
                success: function(response) {
                    var $data = response?.data;
                    console.log($data);
                    if (response.status === 200) {
                        var countCorrect = 0;
                        $data.forEach(function(item) {
                            const id = item.id;
                            var sentenceDom = $(`#sentence-${id}`);
                            var p = sentenceDom.find('p');
                            p.removeClass('text-danger');
                            p.find('span').remove();
                            sentenceDom.find('span').remove();
                            sentenceDom.find('br').remove();
                        });
                        $data.forEach(function(item) {
                            const sentence = item.sentence;
                            const userAnswer = item.user_answer;
                            const correctAnswer = item.correct_answer;
                            const correct = item.correct ?? false;
                            const comment = item.comment;
                            const id = item.id;
                            var sentenceDom = $(`#sentence-${id}`);
                            if (correct) {
                                countCorrect++;
                                var p = sentenceDom.find('p');
                                // p.addClass('text-success');
                                p.append(`<span> ✅ </span>`);
                                sentenceDom.append(`<span class="text-success text-sm"> 👏 ${comment}</span>`)
                            } else {
                                var p = sentenceDom.find('p');
                                p.addClass('text-danger');
                                // get text p + 'value'
                                p.append(`<span> ❌ </span>`);
                                sentenceDom.append(`<span class="text-success text-sm"> ✳️ ${correctAnswer}</span>`);
                                sentenceDom.append(`<br><span class="text-success text-sm"> 📌 ${comment}</span>`)
                            }
                        });

                        $('#submit').text('Submit Again');
                        if ($data.length - countCorrect <= 2) {
                           $('#submit-done').removeClass('d-none');
                           $('#submit-done').click(function() {
                               location.href = "{{ route('mark-done-test-2') }}";
                           });
                        } else {
                            $('#submit-done').addClass('d-none');
                        }
                    } else {
                        alert(response.message);
                        location.reload();
                    }
                },
                complete: function() {
                    $('.loader').hide();
                }
            });
        });

    });
</script>