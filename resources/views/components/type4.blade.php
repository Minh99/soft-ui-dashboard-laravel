@foreach ($data as $item)
<div class="row mb-2" id="sentence-{{ $item['id'] }}">
    <label for="question" class="form-label font-weight-bold" style="font-size: 15px">
        {{ $item['id'] }}. {{ str_replace(['*' , '`'], '"', $item['question'])  }}
    </label>
    <div class="form-row align-items-center">
        <div class="col-auto">
            <div class="input-group mb-2">
                <input type="text" 
                    class="form-control input-item" 
                    placeholder="Enter answer" 
                    id="{{ $item['id'] }}-input"
                    data-uid="{{ $item['id'] }}"
                    data-question="{{ $item['question'] }}"
                >
            </div>
        </div>
    </div>
</div>
<hr>
@endforeach
<button class="btn btn-primary mb-2 mt-2" id="submit">Submit</button>
<br>
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
                    const user_answer = $(item).val();
                    const question = $(item).data('question');
                    dataSubmit.push({
                        id: id,
                        question: question,
                        user_answer: user_answer
                    });
                });

                $.ajax({
                    url: "{{ route('test2-submit') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        data: dataSubmit,
                        user_test2_id: userTest2Id
                    },
                    beforeSend: function() {
                        $('.loader').show();
                    },
                    success: function(response) {
                        var $data = response?.data;
                        if (response.status === 200 || $data == null) {
                            var countCorrect = 0;

                            for (let i = 0; i < $data.length; i++) {
                                const item = $data[i];
                                const id = item.id;
                                var sentenceDom = $(`#sentence-${id}`);
                                let label = sentenceDom.find('label');
                                label.removeClass('text-danger');
                                label.find('span').remove();
                                sentenceDom.find('span').remove();
                            }

                            for (let i = 0; i < $data.length; i++) {
                                const item = $data[i];
                                const id = item.id;
                                const user_answer = item.user_answer;
                                const correct = item.correct;
                                const comment = item.comment;
                                
                                if (correct) {
                                    countCorrect++;
                                    let label = $(`#sentence-${id}`).find('label');
                                    label.append(`<span> ✅ </span>`);
                                    $(`#sentence-${id}`).append(`<span class="text-success text-sm"> 👏 ${comment}</span>`);
                                } else {
                                    let label = $(`#sentence-${id}`).find('label');
                                    label.addClass('text-danger');
                                    label.append(`<span> ❌ </span>`);
                                    $(`#sentence-${id}`).append(`<span class="text-success text-sm"> ⚠️ ${item.comment}</span>`);
                                }
                            }

                            if (countCorrect >= $data.length - 2) {
                                $('#submit-done').removeClass('d-none');
                                $('#submit-done').click(function() {
                                    location.href = "{{ route('mark-done-test-2') }}";
                                });
                            }
                        } else {
                            alert('Error!, please try again');
                            location.reload();
                        }
                    },
                    error: function(error) {
                        alert('Error!, please try again');
                        location.reload();
                    },
                    complete: function() {
                        $('.loader').hide();
                    }
                });
            }
        });
    });
</script>