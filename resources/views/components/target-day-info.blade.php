<div class="row">
    <div class="col-md-6 card mb-4">
        <div class="card-header pb-0">
            <h6>Daily tasks</h6>
            <p class="text-sm">
                <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                <span class="font-weight-bold">6</span> days
            </p>
        </div>
        <div class="card-body p-3">
            <div class="timeline timeline-one-side">
                <div class="timeline-block mb-3">
                    <span class="timeline-step">
                        <i class="fa fa-leanpub text-success text-gradient"></i>
                    </span>
                    <div class="timeline-content">
                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                            First Test, 2 Story, 7 Words  {{ ($userDay->day_number > 1 || ($userDay->day_number == 1 && $userDay->is_passed_test_2 == 1)) ? '✅' : '' }}
                            {{ ($userDay->day_number == 1 && $userDay->is_passed_test_2 == 0) ? '🔥' : '' }}
                            {{ $userDay->day_number < 1 ? '🔒' : '' }}
                        </h6>
                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                            @php
                                $percent = ' 100%✔️';
                                if ($userDay->day_number == 1) {
                                    if ($userDay->is_passed_quiz_story_1 == 1 && $userDay->is_passed_quiz_story_2 == 1) {
                                        $percent = ' 98%';
                                        if ($userDay->is_passed_test_2 == 1) {
                                            $percent = ' 100%✔️';
                                        }
                                    } elseif ($userDay->is_passed_quiz_story_1 == 1) {
                                        $percent = ' 50%';
                                    } elseif ($userDay->is_passed_first_quiz == 1) {
                                        $percent = ' 25%';
                                    } else {
                                        $percent = ' 0%';
                                    }
                                }
                            @endphp
                            Day 1: {{ $percent }}
                            @if ($userDay->day_number == 1)
                                @if ($userDay->is_passed_test_2 == 1)
                                    <a href="{{ route('topics') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; 💪Story </a>
                                        &nbsp; &nbsp; 
                                    <a href="{{ route('test2') }}" class="text-primary text-sm font-weight-bold blink_me2">
                                        Test✏️ </a>
                                @else
                                    <a href="{{ $percent !== ' 98%' ? route('topics') : route('test2') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; {{ $percent !== ' 98%' ? "💪Let's GO" : "Sumary test✏️"  }}</a>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
                <div class="timeline-block mb-3">
                    <span class="timeline-step">
                        <i class="fa fa-leanpub text-danger text-gradient"></i>
                    </span>
                    <div class="timeline-content">
                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                            2 Story, 5 Words / 1 Story  {{ ($userDay->day_number > 2 || ($userDay->day_number == 2 && $userDay->is_passed_test_2 == 1)) ? '✅' : '' }}
                            {{ ($userDay->day_number == 2 && $userDay->is_passed_test_2 == 0) ? '🔥' : '' }}
                            {{ $userDay->day_number < 2 ? '🔒' : '' }}
                        </h6>
                        @php
                            $percent = $userDay->day_number < 2 ? ' 0%' : ' 100%✔️';
                            if ($userDay->day_number == 2 ) {
                                if ($userDay->is_passed_quiz_story_1 == 1 && $userDay->is_passed_quiz_story_2 == 1) {
                                    $percent = ' 98%';
                                    if ($userDay->is_passed_test_2 == 1) {
                                        $percent = ' 100%✔️';
                                    }
                                } elseif ($userDay->is_passed_quiz_story_1 == 1) {
                                    $percent = ' 50%';
                                } else {
                                    $percent = ' 0%';
                                }
                            }
                        @endphp
                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                            Day 2: {{ $percent }}
                            @if ($userDay->day_number == 2)
                                @if ($userDay->is_passed_test_2 == 1)
                                    <a href="{{ route('topics') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; 💪Story </a>
                                        &nbsp; &nbsp; 
                                    <a href="{{ route('test2') }}" class="text-primary text-sm font-weight-bold blink_me2">
                                        Test✏️ </a>
                                @else
                                    <a href="{{ $percent !== ' 98%' ? route('topics') : route('test2') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; {{ $percent !== ' 98%' ? "💪Let's GO" : "Sumary test✏️"  }}</a>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
                <div class="timeline-block mb-3">
                    <span class="timeline-step">
                        <i class="fa fa-leanpub text-info text-gradient"></i>
                    </span>
                    <div class="timeline-content">
                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                            2 Story, 5 Words / 1 Story {{ ($userDay->day_number > 3 || ($userDay->day_number == 3 && $userDay->is_passed_test_2 == 1)) ? '✅' : '' }}
                            {{ ($userDay->day_number == 3 && $userDay->is_passed_test_2 == 0) ? '🔥' : '' }}
                            {{ $userDay->day_number < 3 ? '🔒' : '' }}
                        </h6>
                        @php
                            $percent = $userDay->day_number < 3 ? ' 0%' : ' 100%✔️';
                            if ($userDay->day_number == 3 ) {
                                if ($userDay->is_passed_quiz_story_1 == 1 && $userDay->is_passed_quiz_story_2 == 1) {
                                    $percent = ' 98%';
                                    if ($userDay->is_passed_test_2 == 1) {
                                        $percent = ' 100%✔️';
                                    }
                                } elseif ($userDay->is_passed_quiz_story_1 == 1) {
                                    $percent = ' 50%';
                                } else {
                                    $percent = ' 0%';
                                }
                            }
                        @endphp
                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                            Day 3: {{ $percent }}
                            @if ($userDay->day_number == 3)
                                @if ($userDay->is_passed_test_2 == 1)
                                    <a href="{{ route('topics') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; 💪Story </a>
                                        &nbsp; &nbsp; 
                                    <a href="{{ route('test2') }}" class="text-primary text-sm font-weight-bold blink_me2">
                                        Test✏️ </a>
                                @else
                                    <a href="{{ $percent !== ' 98%' ? route('topics') : route('test2') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; {{ $percent !== ' 98%' ? "💪Let's GO" : "Sumary test✏️"  }}</a>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
                <div class="timeline-block mb-3">
                    <span class="timeline-step">
                        <i class="fa fa-comments-o text-warning text-gradient"></i>
                    </span>
                    <div class="timeline-content">
                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                            Checking daily
                            {{ $userDay->day_number > 4 ? '✅' : '' }}
                            {{ $userDay->day_number == 4 ? '🔥' : '' }}
                            {{ $userDay->day_number < 4 ? '🔒' : '' }}
                        </h6>
                        @php
                            $percent = $userDay->day_number < 4 ? ' 0%' : ' 100%✔️';
                            if ($userDay->day_number == 4 ) {
                                if ($userDay->is_passed_first_quiz == 1) {
                                    $percent = ' 98%';
                                    if ($userDay->is_passed_test_2 == 1) {
                                        $percent = ' 100%✔️';
                                    }
                                } else {
                                    $percent = ' 0%';
                                }
                            }
                        @endphp
                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                            Day 4: {{ $percent }}
                            @if ($userDay->day_number == 4)
                                <a href="{{ route('quiz') }}"
                                    class="text-primary text-sm font-weight-bold blink_me">&nbsp; &nbsp; Test daily &nbsp; &nbsp; 
                                </a>
                            @endif

                            @if ($userDay->day_number == 4)
                                <a href="{{ route('test2') }}"
                                    class="text-primary text-sm font-weight-bold blink_me2">Practice
                                </a>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="timeline-block mb-3">
                    <span class="timeline-step">
                        <i class="fa fa-leanpub text-primary text-gradient"></i>
                    </span>
                    <div class="timeline-content">
                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                            2 Story, 5 Words / 1 Story  {{ ($userDay->day_number > 5 || ($userDay->day_number == 5 && $userDay->is_passed_test_2 == 1)) ? '✅' : '' }}
                            {{ ($userDay->day_number == 5 && $userDay->is_passed_test_2 == 0) ? '🔥' : '' }}
                            {{ $userDay->day_number < 5 ? '🔒' : '' }}
                        </h6>
                        @php
                            $percent = $userDay->day_number < 5 ? ' 0%' : ' 100%✔️';
                            if ($userDay->day_number == 5) {
                                if ($userDay->is_passed_quiz_story_1 == 1 && $userDay->is_passed_quiz_story_2 == 1) {
                                    $percent = ' 98%';
                                    if ($userDay->is_passed_test_2 == 1) {
                                        $percent = ' 100%✔️';
                                    }
                                } elseif ($userDay->is_passed_quiz_story_1 == 1) {
                                    $percent = ' 50%';
                                } else {
                                    $percent = ' 0%';
                                }
                            }
                        @endphp
                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                            Day 5: {{ $percent }}
                            @if ($userDay->day_number == 5)
                                @if ($userDay->is_passed_test_2 == 1)
                                    <a href="{{ route('topics') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; 💪Story </a>
                                        &nbsp; &nbsp; 
                                    <a href="{{ route('test2') }}" class="text-primary text-sm font-weight-bold blink_me2">
                                        Test✏️ </a>
                                @else
                                    <a href="{{ $percent !== ' 98%' ? route('topics') : route('test2') }}" class="text-primary text-sm font-weight-bold blink_me">
                                        &nbsp; &nbsp; {{ $percent !== ' 98%' ? "💪Let's GO" : "Sumary test✏️"  }}</a>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
                <div class="timeline-block">
                    <span class="timeline-step">
                        <i class="fa fa-graduation-cap text-dark text-gradient"></i>
                    </span>
                    <div class="timeline-content">
                        <h6 class="text-dark text-sm font-weight-bold mb-0">
                            Test final {{ $userDay->day_number > 6 ? '✅' : '' }}
                            {{ $userDay->day_number == 6 ? ($userDay->is_completed == 0 ? '🔥' : '✅') : '' }}
                            {{ $userDay->day_number < 6 ? '🔒' : '' }}
                        </h6>
                        @php
                            $percent = $userDay->day_number < 6 ? ' 0%' : ' 100%✔️';
                            if ($userDay->day_number == 6 ) {
                                if ($userDay->is_passed_first_quiz == 1) {
                                    $percent = ' 98%';
                                    if ($userDay->is_passed_test_2 == 1) {
                                        $percent = ' 100%✔️';
                                    }
                                } else {
                                    $percent = ' 0%';
                                }
                            }
                        @endphp
                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                            Day 6: {{ $percent }}
                            @if ($userDay->day_number == 6 && $userDay->is_completed == 0)
                                <a href="{{ route('quiz') }}"
                                    class="text-primary text-sm font-weight-bold blink_me">&nbsp; &nbsp;📋Test final&nbsp; &nbsp;
                                </a>
                            @endif
                            @if ($userDay->day_number == 6)
                                <a href="{{ route('test2') }}"
                                    class="text-primary text-sm font-weight-bold blink_me2">Practice✏️
                                </a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex flex-column h-100">
                        <p class="mb-1 pt-2 text-bold"> Continue completing challenges </p> 
                        <h5 class="font-weight-bolder">With Chat Bot</h5>
                        <p class="mb-5">Story around the words you don't know</p>
                        <a class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto"
                            href="{{ route('topics') }}">
                            Let's Do It
                            <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-5 ms-auto text-center mt-5 mt-lg-0">
                    <div class="bg-gradient-primary border-radius-lg h-100 overflow-hidden">
                        <div class="position-relative d-flex align-items-center justify-content-center h-100">
                            <img class="w-100 position-relative z-index-2 pt-4"
                                src="{{ asset('assets/img/illustrations/rocket-white.png') }}" alt="rocket">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
