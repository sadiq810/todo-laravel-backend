@extends('layout.main')
@section('content')
   {{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQhaezqwq3wXWnWl7nk2RD7QCIYpHCVZA&v=3.exp&libraries=places"></script>--}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBQhaezqwq3wXWnWl7nk2RD7QCIYpHCVZA&callback=initMap&libraries=&v=weekly"
        async
    ></script>
    <style>
        #map {
            height: 400px;
            /* The height is 400 pixels */
            width: 100%;
            /* The width is the width of the web page */
        }
    </style>
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Manage Users <small>{{ __('all.list') }}</small></h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <ul class="nav navbar-right panel_toolbox">
                            <li>
                                <button class="btn btn-info btn-sm btn-round addNew"><i class="fa fa-plus"></i>
                                    {{ __('all.add_new') }}
                                </button>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('all.name') }}</th>
                                <th>{{ __('all.email') }}</th>
                                <th style="text-align: center;">{{ __('all.action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="x_content">
                        <div id="map" class="mine"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="addEditModal" class="modal fade bs-example-modal-xl lang-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form id="mainForm" method="post" action="{{ route('customers.save', app()->getLocale()) }}" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalLabel">{{ __('all.add_edit_user') }}</h4>
                        </div>
                        <div class="modal-body ">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">{{ __('all.name') }}</label>
                                    <input type="text" id="name" class="form-control" name="name" required="required"/>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="email">{{ __('all.email') }}</label>
                                    <input type="email" id="email" class="form-control" name="email" required="required"/>
                                </div>

                                <div class="form-group col-md-6 password__container" style="display: none;">
                                    <label for="password">{{ __('all.password') }}</label>
                                    <input type="password" name="password" id="password" class="form-control" />
                                </div>
                                <input type="hidden" id="hidden_id" name="id"/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('all.close') }}</button>
                            <input type="submit" id="saveBtn" class="btn btn-primary" value="{{ __('all.save') }}">
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <script>
            var mainForm = null;
            var addEditModal = null;
            var saveBtn = null;
            var hidden_id = null;
            var userName = null;
            var email = null;
            var password = null;
            var table = null;
            var password__container = null;

            var selectedRecordOfSubTable = null;

            $(document).ready(function () {
                mainForm = $('#mainForm');
                addEditModal = $('#addEditModal');
                saveBtn = $('#saveBtn');
                hidden_id = $('#hidden_id');
                userName = $('#name');
                email = $('#email');
                password = $('#password');
                password__container = $('.password__container');

                table = $('#datatable-responsive').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    ajax: "{{ route('customers.list', app()->getLocale()) }}",
                    columns: [
                        { "className": 'details-control', "orderable": false, "data": null, "defaultContent": '', searchable: false },
                        { data: 'name', name: 'name', render: function (data, t, row) {
                                return `<a href="#" class="editable__field" data-name="name" data-title="{{ __('all.enter_title') }}" data-type="text" data-pk="${row.id}">${data}</a>`
                            }
                        },
                        { data: 'email', name: 'email', render: function (data, t, row) {
                                if(row.action == '')
                                    return data;

                                return `<a href="#" class="editable__field" data-title="{{ __('all.sort_order') }}" data-name="email" data-type="text" data-pk="${row.id}">${data}</a>`
                            }
                        },
                        {data: 'action', name: 'action', orderable: false, searchable: false, className: "center-text"}
                    ],
                    "drawCallback": function (setting) {
                        initializeEditable();
                    },
                });

                /*---------------show Modal=========================================*/
                $('body').on('click', '.addNew', function () {
                    hidden_id.val('');
                    password__container.css('display', 'block');
                    mainForm.trigger("reset");
                    addEditModal.modal('show');
                });

                /*---------------add and update records=========================================*/
                $('#mainForm').on('submit', function (e) {
                    e.preventDefault();
                    saveBtn.prop('disabled', true);
                    $(this).ajaxSubmit({
                        success: function (data) {
                            if (data.status) {
                                showNotification(data.message);
                                mainForm.trigger("reset");
                                password__container.css('display', 'none');
                                addEditModal.modal('hide');
                                table.ajax.reload(null, false);
                            } else showNotification(data.message, 'error', 'Error');

                            saveBtn.prop('disabled', false);
                        },
                        error: function (data) {
                            showNotification("{{ __('all.internal_server_error') }}", 'error', 'Error');
                            saveBtn.prop('disabled', false);
                        }
                    });
                });

                /*---------------edit records=========================================*/
                $(document).on('click', '.edit', function () {
                    var data = table.row($(this).parents('tr')).data();
                    hidden_id.val(data.id);
                    password__container.css('display', 'none');
                    userName.val(data.name);
                    email.val(data.email);
                    password.val('');
                    addEditModal.modal('show');
                });

                /*---------------delete records=========================================*/
                $('body').on('click', '.delete', function () {
                    var data = table.row($(this).parents('tr')).data();
                    var url = "{{ route('customers.destroy', [app()->getLocale(), '***']) }}";
                    url = url.replace('***', data.id);
                    if (confirm("Are You sure want to delete !"))
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            success: function (data) {
                                showNotification(data.message);
                                table.ajax.reload(null, false);
                            }, error: function (data) {
                                showNotification("{{ __('all.internal_server_error') }}", 'error', 'Error');
                            }
                        });
                });

                $('#datatable-responsive tbody').on('click', 'td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = table.row(tr);

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.removeClass('shown');
                    } else {
                        if (table.row('.shown').length)
                            $('.details-control', table.row('.shown').node()).click();

                        row.child(formatTable(row.data())).show();
                        var bid = row.data().id;
                        childTable = $('#in' + bid).DataTable({
                            ajax: {
                                url: "{{ route('customer.checkins.grid', app()->getLocale()) }}?id=" + bid,
                                "type": "GET"
                            },
                            columns: [
                                { data: 'location', name: 'location'},
                                { data: 'latitude', name: 'latitude'},
                                { data: 'longitude', name: 'longitude'},
                                { data: 'date', name: 'created_at'},
                                { data: 'action', name: 'action', orderable: false, searchable: false, className: "center-text" }
                            ],
                            "destroy": true,
                            "info": false,
                            select: false,
                            "bProcessing": true,
                            "filter": false,
                            "paging": false,
                            stateSave: true,
                        });
                        tr.addClass('shown');
                    }
                });


                $('body').on('click', '.viewOnMap', function() {
                    let table = $('#in' + $(this).data('id')).DataTable();
                    var data = table.row($(this).parents('tr')).data();
                    const uluru = { lat: Number(data.latitude), lng: Number(data.longitude) };
                    initMap(uluru)
                })
            });
            //========================================================================================//
            function initMap(userLatLng = null) {
                // The location of Uluru
                const uluru = { lat: -25.344, lng: 131.036 };
                // The map, centered at Uluru
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 4,
                    center: userLatLng ? userLatLng : uluru,
                });

                if (userLatLng)
                   new google.maps.Marker({
                        position: userLatLng ? userLatLng : uluru,
                        map: map,
                    });
            }
            //========================================================================================//
            function formatTable(rowData) {
                var childTable = '<table id="in' + rowData.id + '" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" width="100%">' +
                    '<thead><th>Location</th><th>Lat</th><th>long</th><th>Date</th><th>{{ __("all.action") }}</th></thead> ' +
                    '</table>';
                return $(childTable).toArray();
            }

            function initializeEditable() {
                $('.editable__field').editable({
                    mode: 'inline',
                    url: "{{route('update.customers.field',app()->getLocale())}}",
                    success: function (response, newvalue) {
                        showNotification(response.message, response.status ? 'success': 'error');
                        table.ajax.reload(null, false);
                    }
                });
            }//..... end of initializeEditable() ....//
        </script>
    </div>
@endsection
