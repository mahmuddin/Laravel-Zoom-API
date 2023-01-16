<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.9/dist/sweetalert2.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datetimepicker/jquery.datetimepicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatable/jquery.dataTables.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <title>Zoom API</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        body {
            color: #566787;
            background: #f5f5f5;
            font-family: 'Roboto', sans-serif;
        }

        .table-responsive {
            margin: 30px 0;
        }

        .table-wrapper {
            min-width: 1000px;
            background: #fff;
            padding: 20px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        }

        .table-title {
            font-size: 15px;
            padding-bottom: 10px;
            margin: 0 0 10px;
            min-height: 45px;
        }

        .table-title h2 {
            margin: 5px 0 0;
            font-size: 24px;
        }

        .table-title select {
            border-color: #ddd;
            border-width: 0 0 1px 0;
            padding: 3px 10px 3px 5px;
            margin: 0 5px;
        }

        .table-title .show-entries {
            margin-top: 7px;
        }

        .search-box {
            position: relative;
            float: right;
        }

        .search-box .input-group {
            min-width: 200px;
            position: absolute;
            right: 0;
        }

        .search-box .input-group-addon,
        .search-box input {
            border-color: #ddd;
            border-radius: 0;
        }

        .search-box .input-group-addon {
            border: none;
            border: none;
            background: transparent;
            position: absolute;
            z-index: 9;
        }

        .search-box input {
            height: 34px;
            padding-left: 28px;
            box-shadow: none !important;
            border-width: 0 0 1px 0;
        }

        .search-box input:focus {
            border-color: #3FBAE4;
        }

        .search-box i {
            color: #a0a5b1;
            font-size: 19px;
            position: relative;
            top: 8px;
        }

        table.table tr th,
        table.table tr td {
            border-color: #e9e9e9;
        }

        table.table th i {
            font-size: 13px;
            margin: 0 5px;
            cursor: pointer;
        }

        table.table td:last-child {
            width: 130px;
        }

        table.table td a {
            color: #a0a5b1;
            display: inline-block;
            margin: 0 5px;
        }

        table.table td a.view {
            color: #03A9F4;
        }

        table.table td a.edit {
            color: #FFC107;
        }

        table.table td a.delete {
            color: #E34724;
        }

        table.table td i {
            font-size: 19px;
        }

        .pagination {
            float: right;
            margin: 0 0 5px;
        }

        .pagination li a {
            border: none;
            font-size: 13px;
            min-width: 30px;
            min-height: 30px;
            padding: 0 10px;
            color: #999;
            margin: 0 2px;
            line-height: 30px;
            border-radius: 30px !important;
            text-align: center;
        }

        .pagination li a:hover {
            color: #666;
        }

        .pagination li.active a {
            background: #03A9F4;
        }

        .pagination li.active a:hover {
            background: #0397d6;
        }

        .pagination li.disabled i {
            color: #ccc;
        }

        .pagination li i {
            font-size: 16px;
            padding-top: 6px
        }

        .hint-text {
            float: left;
            margin-top: 10px;
            font-size: 13px;
        }

        #meeting-table_length {
            padding-bottom: 20px;
        }

        /* Loader */
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            display: block;
            margin: 30px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

    </style>
</head>

<body>
    <?php
    $url = "https://zoom.us/oauth/authorize?response_type=code&client_id=$ZOOM_CLIENT_ID&redirect_uri=$ZOOM_REDIRECT_URI";
    ?>

    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="pb-2">
                    @if ($is_login == true)
                        <div class="row">

                            <div class="col-lg-6">
                                <div class="form-group row mb-2">
                                    <label for="inputMeetingTopic" class="col-sm-2 col-form-label">Topic</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control topic required" id="inputMeetingTopic"
                                            placeholder="Input meeting topic" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="inputMeetingDuration" class="col-sm-2 col-form-label">Duration</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control minute required"
                                            id="inputMeetingDuration" placeholder="Input meeting duration (in Minute)"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="passwordZoom" class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control topic required" id="passwordZoom"
                                            placeholder="Password" required>
                                        <a href="javascript:void(0)" style="text-decoration: none;"
                                            onMouseOver="this.style.cursor='pointer'" onclick="generateRandPass()"><i
                                                class="fa fa-cog"></i>&nbsp;Generate Random Password</a>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="offset-lg-2 col-lg-5">
                                        <button type="button" class="btn btn-success btn-submit"><i
                                                class="fa fa-save"></i> Create Meeting</button>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group row mb-2">
                                    <label for="inputStartTime" class="col-sm-2 col-form-label">Start Time</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control topic required" id="inputStartTime"
                                            placeholder="" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-2">
                                    <label for="inputMeetingQuota" class="col-sm-2 col-form-label">Quota</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control quota required" id="inputMeetingQuota"
                                            placeholder="Input meeting Quoata" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ $url }}" type="submit" class="btn btn-success">Login Zoom</a>
                    @endif
                </div>
                @if (Session::has('createMeeting') == true)
                    {{-- <div class="alert alert-primary" role="alert">
                        Success add meeting
                    </div> --}}
                @endif
                <div class="table-title">
                    <div class="row">

                    </div>
                </div>
                <table class="table table-bordered " id="meeting-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Meeting Id</th>
                            <th>Password</th>
                            <th>Topic</th>
                            <th>Start Time</th>
                            <th>Duration (minute)</th>
                            <th>Quota</th>
                            <th>Join Url</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($data['meetings'] as $d)
                            <tr style="background-color: {{ $d['is_passed'] == true ? '#707070' : 'white' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d['id'] }}</td>
                                <td>{{ $d['password'] }}</td>
                                <td>{{ $d['topic'] }}</td>
                                <td>
                                    <?php date_default_timezone_set('Asia/Jakarta'); ?>
                                    {{ date('d F Y H:i:s', strtotime($d['start_time'] . ' UTC')) }} WIB
                                </td>
                                <td style="text-align: center; vertical-align: middle;">{{ $d['duration'] }}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $d['quota'] }}</td>
                                <td>
                                    <a target="_blank" href="{{ $d['join_url'] }}"
                                        style="text-decoration: none; color:#3FBAE4">{{ $d['join_url'] }}</a>
                                </td>
                                <td style="text-align: center; vertical-align: middle; width: 180px;">
                                    <a href="javascript:void(0)" data-value="delete_meeting/{{ $d['id'] }}"
                                        class="btn btn-danger btn-delete-meeting" onclick="removeMeeting(this);"
                                        style="color: white !important;" data-toggle="tooltip" data-placement="top"
                                        title="Delete meeting"><i class="fa fa-trash"></i></a>

                                    <span data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        class="btn-invite-meeting" data-value="{{ $d['id'] }}">
                                        <a class="btn btn-success" style="color: white !important;"
                                            data-toggle="tooltip" data-placement="top" title="Invite user"><i
                                                class="fa fa-user-plus"></i></a>
                                    </span>
                                    @if (count($d['invited']) > 0)
                                        <span data-bs-toggle="modal" data-bs-target="#exampleModalUserInvited"
                                            class="btn-user-invited" data-value="{{ $d['id'] }}">
                                            <a class="btn btn-primary" style="color: white !important;"
                                                data-toggle="tooltip" data-placement="top" title="User Invited"><i
                                                    class="fa fa-eye"></i></a>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Invitation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select id="myInvite" class="js-example-basic-multiple form-control" name="states[]"
                        multiple="multiple" style="width: 100%">

                    </select>
                    <input id="zoom_room_id" type="hidden" name="zoom_room_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-close-invitation"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-save-invitation"><i class="fa fa-save"></i>
                        Save</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModalUserInvited" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">User Invited</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped" id="invited-user-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Optional JavaScript; choose one of the two! -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="{{ asset('vendor/datetimepicker/moment.js') }}"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('vendor/datetimepicker/build/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('vendor/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#meeting-table').DataTable({
                responsive: true,
                "scrollX": true
            });

            var tableUser = $('#invited-user-table').DataTable();

            $('[data-toggle="tooltip"]').tooltip();
            // Animate select box length
            var searchInput = $(".search-box input");
            var inputGroup = $(".search-box .input-group");
            var boxWidth = inputGroup.width();
            searchInput.focus(function() {
                inputGroup.animate({
                    width: "300"
                });
            }).blur(function() {
                inputGroup.animate({
                    width: boxWidth
                });
            });

            $('.btn-submit').click(function(e) {
                let quota = $('.quota').val();
                let minute = $('.minute').val();
                let topic = $('.topic').val();
                let start_time = $('#inputStartTime').val();
                let password = $('#passwordZoom').val();

                if (minute == '' || quota == '' || topic == '' || start_time == '' || password == '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'All fields can not be empty',
                    })
                } else {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want create meeting",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Please Wait...',
                                html: '<div class="loader"></div>',
                                allowOutsideClick: false,
                                showConfirmButton: false
                            });
                            window.location.href =
                                "{{ $ZOOM_OWN_URL }}/create_meeting/?duration=" + minute +
                                "&quota=" + quota + "&topic=" + topic + "&start_time=" +
                                start_time + "&password=" +
                                password;

                        }
                    })

                }

            });

            $('#inputStartTime').datetimepicker({
                mask: '39-19-1999 29:59',
                format: 'd-m-Y H:i',
                formatDate: 'd-m-Y',
                formatTime: 'H:i',
            })

            // Modal dialag
            $('#exampleModal').on('shown.bs.modal', function() {
                $('#myInvite').val(null).trigger('change');
                $('#myInvite').trigger('focus')
            })

            // Select2
            $('.js-example-basic-multiple').select2({
                multiple: true,
                dropdownParent: $('#exampleModal'),
                placeholder: 'Select users',
                allowClear: true,
                ajax: {
                    url: '/autocomplete-ajax-user',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        var results = [];
                        $.each(data, function(index, d) {
                            results.push({
                                id: d.id,
                                text: d.email
                            });
                        });

                        return {
                            results: results
                        };
                    },
                    cache: true
                }
            });

            $('.btn-invite-meeting').click(function() {
                $('#zoom_room_id').val($(this).attr('data-value'));
            });

            $('.btn-user-invited').click(function() {
                $('#zoom_room_id').val($(this).attr('data-value'));
            });

            $(".btn-save-invitation").click(function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Want to add this participant",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Please Wait...',
                            html: '<div class="loader"></div>',
                            allowOutsideClick: false,
                            showConfirmButton: false
                        });

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $.ajax({
                            url: "/add_invitation",
                            method: "POST",
                            data: {
                                zoom_room_id: $('#zoom_room_id').val(),
                                user_id: $("#myInvite").val()
                            },
                        }).done(function(response) {
                            Swal.close();
                            if (response.status == 200) {
                                Swal.fire(
                                    'Success!',
                                    'Data saved!',
                                    'success'
                                )
                                $('#exampleModal').modal('toggle');
                                location.reload();
                            } else if (response.status == 201) {
                                Swal.fire(
                                    'Error!',
                                    'Quota Exceeded!',
                                    'error'
                                )
                            } else if (response.status == 500) {
                                Swal.fire(
                                    'Error!',
                                    'Data not saved!',
                                    'error'
                                )
                            }
                        }).fail(function(jqXHR, textStatus) {
                            Swal.close();
                            $('#exampleModal').modal('toggle');
                        })

                    }
                })
            })

            $('.btn-user-invited').click(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "/view_invitation",
                    method: "POST",
                    data: {
                        zoom_room_id: $('#zoom_room_id').val()
                    },
                }).done(function(response) {
                    console.log(response)
                    if (response.status == 200) {
                        tableUser.clear();
                        $.each(response.data, function(row) {
                            tableUser.row.add([
                                this.no,
                                this.email,
                                "<a class='btn btn-danger delete-user-invited' href='#' data-value=" +
                                this.id + " data-room-id=" + this.zoom_room_id +
                                "><i class='fa fa-trash ' style='color:white'></i></a>"
                            ]);
                        });
                        tableUser.draw();
                    }
                }).fail(function(jqXHR, textStatus) {
                    $('#exampleModalUserInvited').modal('toggle');
                })
            });

            // Delete action
            $('table#invited-user-table').on('click', '.delete-user-invited', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want delete this user",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Please Wait...',
                            html: '<div class="loader"></div>',
                            allowOutsideClick: false,
                            showConfirmButton: false
                        });

                        let user_id = $(this).attr('data-value');
                        let zoom_room_id = $(this).attr('data-room-id');

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                    'meta[name="csrf-token"]'
                                ).attr('content')
                            }
                        });

                        $.ajax({
                            url: "/delete_invitation",
                            method: "DELETE",
                            data: {
                                zoom_room_id: zoom_room_id,
                                user_id: user_id,
                            },
                        }).done(function(response) {
                            console.log(response)
                            if (response.status == 200) {
                                tableUser.clear();
                                $.each(response.data, function(
                                    row) {
                                    tableUser.row.add([
                                        this.no,
                                        this
                                        .email,
                                        "<a class='btn btn-danger delete-user-invited' href='#' data-value=" +
                                        this
                                        .id +
                                        " data-room-id=" +
                                        this
                                        .zoom_room_id +
                                        "><i class='fa fa-trash' style='color:white'></i></a>"
                                    ]);
                                });
                                tableUser.draw();
                                Swal.close()
                            }

                        }).fail(function(jqXHR, textStatus) {
                            $('#exampleModalUserInvited').modal(
                                'toggle');
                        })

                    }
                })
            })
            // Delete action

        })

        function removeMeeting(d) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This process can not be reverted",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Please Wait...',
                        html: '<div class="loader"></div>',
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });
                    window.location.href = "{{ $ZOOM_OWN_URL }}/" + d.getAttribute("data-value");
                }
            })

        }

        function generateRandPass() {
            $("#passwordZoom").val('');
            $("#passwordZoom").val(Math.random().toString(36).slice(-8));
        }
    </script>
</body>

</html>
