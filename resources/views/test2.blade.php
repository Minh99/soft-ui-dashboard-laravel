@extends('layouts.user_type.auth')

@section('content')
    {{-- quiz --}}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Quiz</h4>
                        <span>
                            @if ($type == 1)
                                Fill in the blank with the correct word Simple Sentences
                            @elseif ($type == 2)
                                Fill in the blank with the correct word Short Paragraph
                            @elseif ($type == 3)
                                Translated from Korean to English
                            @elseif ($type == 4)
                                Make a sentence with the following word
                            @endif
                        </span>
                    </div>
                    <div class="card-body">
                        @if ($type == 1)
                            @include('components.type1', ['data' => $data, 'type' => $type, 'userTest2Id' => $userTest2Id])
                        @elseif ($type == 2)
                            @include('components.type2', ['data' => $data, 'type' => $type, 'userTest2Id' => $userTest2Id])
                        @elseif ($type == 3)
                            @include('components.type3', ['data' => $data, 'type' => $type, 'userTest2Id' => $userTest2Id])
                        @elseif ($type == 4)
                            @include('components.type4', ['data' => $data, 'type' => $type, 'userTest2Id' => $userTest2Id])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- quiz --}}

@endsection
