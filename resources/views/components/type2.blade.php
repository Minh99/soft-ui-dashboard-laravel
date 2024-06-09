@foreach ($data as $key => $item)
    <form class="mb-4">
        <p class="form-label font-weight-bold text-lg"> Paragraph {{ $key + 1}} </p>
        <label class="form-label font-weight-bold" style="font-size: 15px">
            🅿️ {{ $item['graph']}}
        </label>
        <label class="form-label font-weight-bold" style="font-size: 15px;">
            @php
                shuffle($item['options']);
            @endphp
            🔷 (<i> {{ implode(', ', $item['options']) }} </i>)
        </label>
        @foreach ($item['correct_answer'] as $k => $answer)
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend input-group-text" id="pos-{{$key}}-{{$k}}">
                            Postion {{ $k + 1 }}
                        </div>
                        <input type="text" class="form-control" placeholder="Enter answer" id="{{$key}}-{{$k}}">
                    </div>
                </div>
            </div>
        @endforeach
    </form>
    <hr>
@endforeach

<button type="button" class="btn btn-primary mb-2 mt-2" id="submit">Submit</button>
<button type="button" class="btn btn-success mb-2 mt-2 d-none" id="submit-done">Mark as done</button>

<script>
    const data = @json($data);
    const userTest2Id = @json($userTest2Id);
    console.log(data);
    console.log(userTest2Id);
    document.addEventListener('DOMContentLoaded', function() {
        $('#submit').click(function() {
            const texteds = $('input[type="text"]');
            for (let i = 0; i < texteds.length; i++) {
                const texted = texteds[i];
                const id = texted.id;
                const value = texted.value;
               
                const [pos, index] = id.split('-');

                $(`#pos-${pos}-${index}`).removeClass('text-success');
                $(`#pos-${pos}-${index}`).removeClass('text-danger');
                $(`#pos-${pos}-${index}`).text('Postion ' + (parseInt(index) + 1));
            }

            var countPassed = 0;
            for (let i = 0; i < texteds.length; i++) {
                const texted = texteds[i];
                const id = texted.id;
                const value = texted.value;
               
                const [pos, index] = id.split('-');
                const correctAnswer = data[pos].correct_answer[index];
                if (value.trim().toLowerCase() === correctAnswer.trim().toLowerCase()) {
                    countPassed++;
                    $(`#pos-${pos}-${index}`).addClass('text-success');
                    $(`#pos-${pos}-${index}`).append(`<span> ✅ </span>`);
                } else {
                    $(`#pos-${pos}-${index}`).addClass('text-danger');
                    $(`#pos-${pos}-${index}`).append(`<span> ❌ </span>`);
                }
            }

            if (countPassed == texteds.length) {
                $('#submit').addClass('d-none');
                $('#submit-done').removeClass('d-none');
                $('#submit-done').click(function() {
                    location.href = "{{ route('mark-done-test-2') }}";
                });
            }
        });
    });
</script>