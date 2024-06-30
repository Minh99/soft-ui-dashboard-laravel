@extends('layouts.user_type.auth')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between">
                            <div>
                                <h5 class="mb-0">Topics list</h5>
                            </div>
                            <button class="btn bg-gradient-primary btn-sm mb-0" type="button"
                                data-bs-toggle="modal" data-bs-target="#add">
                                +&nbsp; New Topic
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ID
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Name
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Description
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topics as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <p class="text-xs font-weight-bold mb-0">{{ $item->id }}</p>
                                            </td>
                                            <td class="">
                                                <p class="text-xs font-weight-bold mb-0">{{ $item->name }}</p>
                                            </td>
                                            <td class="">
                                                <p class="text-xs font-weight-bold mb-0">{{ $item->description }}</p>
                                            </td>
                                            <td class="align-middle ms-auto">
                                                <button class="btn bg-gradient-info btn-sm mb-2" type="button"
                                                    data-bs-toggle="modal" data-bs-target="#edit"
                                                    data-uid="{{ $item->id }}"
                                                    data-name="{{ $item->name }}"
                                                    data-description="{{ $item->description }}">
                                                    Edit
                                                </button>
                                                <form action="{{ route('deleteTopicsManagement') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="uid" value="{{ $item->id }}">
                                                    <button type="submit" class="btn bg-gradient-danger btn-sm mb-0" data-bs-dismiss="modal">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="editLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('createTopicsManagement') }}" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLabel">Edit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="uid" id="uid">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-closed" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-closed">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('createTopicsManagement') }}" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLabel">Add</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-closed" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-closed">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                var count = 0;
                var modal = '#edit';
                var modalTitle = '#editLabel';
                var modalBody = '.modal-body';
                var showModal = '#showModal';
                var btnClose = '.btn-closed';

                $(modal).on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget);
                    var uid = button.data('uid');
                    var name = button.data('name');
                    var description = button.data('description');
                    $(modal + ' ' + '#uid').val(uid);
                    $(modal + ' ' + '#name').val(name);
                    $(modal + ' ' + '#description').val(description);
                });
            });
        </script>
    @endpush
@endsection
