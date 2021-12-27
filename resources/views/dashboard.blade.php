@extends('layout.main')
@section('content')
    <div class="row tile_count">
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-users"></i>
                </div>
                <div class="count">{{ $users }}</div>

                <h3>Total Users</h3>
            </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-tasks"></i>
                </div>
                <div class="count">{{$tasks}}</div>

                <h3>Total Todos</h3>
            </div>
        </div>
    </div>
@endsection
