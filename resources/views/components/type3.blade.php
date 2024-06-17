@foreach ($data as $item)
<div class="row mb-2" id="sentence-{{ $item['id'] }}">
    <label for="question" class="form-label font-weight-bold" style="font-size: 15px">
        {{ $item['id'] }}. {{$item['sentences']}}
    </label>
    <p>📍 {{ implode(', ', $item['suggest_words']) }} </p>
    <div class="form-row align-items-center">
        <div class="col-auto">
            <div class="input-group mb-2">
                <input type="text" 
                    class="form-control input-item" 
                    placeholder="Enter answer" 
                    id="{{ $item['id'] }}-input"
                    data-sentences="{{ $item['sentences'] }}"
                    data-uid="{{ $item['id'] }}"
                    data-suggest_words="{{ implode(', ', $item['suggest_words']) }}"
                >
            </div>
        </div>
    </div>
</div>
<hr>
@endforeach
<button class="btn btn-primary mb-2 mt-2" id="submit">Submit</button>
<button class="btn btn-success mb-2 mt-2 d-none" id="submit-done">Mark as done</button>

<script>
    const data = @json($data);
    const userTest2Id = @json($userTest2Id);
    document.addEventListener('DOMContentLoaded', function() {
        $('#submit').click(function() {
            const texteds = $('input[type="text"]');
            var isContinue = true;
            for (let i = 0; i < texteds.length; i++) {
                const texted = texteds[i];
                const id = texted.id;
                const value = texted.value;
                if (value.trim() === '') {
                    isContinue = false;
                    $(texted).addClass('border-danger');
                }
            }

            if (isContinue) {
                var dataSubmit = [];
                var inputs = $('.input-item');
                inputs.each(function(index, item) {
                    const id = $(item).data('uid');
                    const sentences = $(item).data('sentences');
                    const suggest_words = $(item).data('suggest_words');
                    const user_answer = $(item).val();
                    dataSubmit.push({
                        id: id,
                        sentences: sentences,
                        suggest_words: suggest_words,
                        user_answer: user_answer
                    });
                });

                $.ajax({
                    url: "{{ route('test2-submit') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_test2_id: userTest2Id,
                        data: dataSubmit
                    },
                    beforeSend: function() {
                        $('.loader').show();
                    },
                    success: function(response) {
                        var $data = response?.data;
                        if (response.status === 200) {
                            var countCorrect = 0;
                            if ($data == null) {
                                alert('Please try again, something went wrong');
                                location.reload();
                            }
                            $data.forEach(function(value) {
                                let id = value.id;
                                let sentenceDom = $(`#sentence-${id}`);
                                let label = sentenceDom.find('label');
                                label.removeClass('text-danger');
                                label.find('span').remove();
                                sentenceDom.find('span').remove();
                            });
                            $data.forEach(function(value) {
                                let sentences = value.sentences;
                                let example = value.example ?? [];
                                let result = value.result ?? false;
                                let id = value.id;
                                let sentenceDom = $(`#sentence-${id}`);
                                if (result) {
                                    countCorrect++;
                                    var label = sentenceDom.find('label');
                                    label.append(`<span> ✅ </span>`);
                                } else {
                                    var label = sentenceDom.find('label');
                                    label.addClass('text-danger');
                                    label.append(`<span> ❌ </span>`);
                                    example.forEach(function(it) {
                                        sentenceDom.append(`<span class="text-danger text-sm"> 📝 ${it}</span><br>`)
                                    });
                                }
                            });



                            if ($data.length - countCorrect <= 2) {
                                $('#submit-done').removeClass('d-none');
                                $('#submit-done').click(function() {
                                    location.href = "{{ route('mark-done-test-2') }}";
                                });
                            }
                        } else {
                            alert(response.message);
                            location.reload();
                        }
                    },
                    complete: function() {
                        $('.loader').hide();
                    },
                    error: function() {
                        $('.loader').hide();
                    }
                });
            }
        });

        $('.input-item').keyup(function() {
            $(this).removeClass('border-danger');
        });
    });
</script>