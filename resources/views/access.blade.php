@extends('layouts.app')

@section('styles')
<style media="screen">
    .table td,
    .table th {
        text-align: center;
    }
</style>
@endsection

@section('script')
<script type="text/javascript">
$(function() {

    $(document).on('click', '.qr_button', function() {
        //console.log('.qr_button click');
        bootbox.alert('<img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&choe=UTF-8&chl=' + $(this).data('code') +  '\">');
    });

    $(document).on('click', '.notify-user', function() {

        var id = $(this).data('access-id');

        bootbox.confirm('This will send the QR Code to the user, do you wish to do so?', function(ret) {
            if( ret ) {
                $.get('/access/notify/' + id, {} , function(ret) {
                    console.log(ret);
                }).fail(function(ret) {
                    bootbox.alert('Error in sending mail');
                    console.error(ret);
                });
            }
        });
    });

    $(document).on('click' , '.btn-delete' , function(){
        var id = $(this).data('id');
        console.log('btn-delete click, id = ' + id);

        bootbox.confirm('Do you really want to delete access #' + id + '?', function(ret) {
            if (ret) {
                $.ajax({
                    url: '/access/' + id,
                    method: 'DELETE',
                    data: {
                        _token: window.Laravel.csrfToken
                    }
                }).done(function (resp) {
                    console.log(resp);
                    location.href = '/access';
                }).fail(function (resp) {
                    bootbox.alert('Delete access fail!');
                    console.error(resp);
                });
            }
        });
    } );

});
</script>
@endsection


@section('content')
<div class="container">

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Access List</div>
                <div class="panel-body">
                    <table class="table table-hover table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Access ID</th>
                                <th>Belong To</th>
                                <th>Expire Day</th>
                                <th>Actual Time</th>
                                <th>Created At</th>
                                <th>Last Update</th>
                                <th>View QR</th>
                                <th>Notify User</th>
                                <th>Update</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $access_array as $access )
                                <tr>
                                    <td>{{ $access->id }}</td>
                                    <td>{{ $access->user_id }}</td>
                                    <td>
                                        @if( $access->expire_day == 0 )
                                            Never
                                        @else
                                            {{ $access->expire_day }}
                                        @endif
                                    </td>
                                    <td>
                                        @if( $access->expire_day == 0 )
                                            -
                                        @else
                                            {{ $access->getActualExpireTime() }}
                                        @endif
                                    </td>
                                    <td>{{ $access->created_at }}</td>
                                    <td>{{ $access->updated_at }}</td>
                                    <td>
                                        <button data-code="{{ $access->qr_code }}" class="btn btn-default btn-xs qr_button">QR</button>
                                    </td>
                                    <td>
                                        <button data-access-id="{{ $access->id }}" class="btn btn-default btn-xs notify-user"><span class="glyphicon glyphicon-inbox"></span></button>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span></button>
                                    </td>
                                    <td>
                                        <button data-id="{{ $access->id }}"  class="btn btn-danger btn-xs btn-delete"><span class="glyphicon glyphicon-remove"></span></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Grant Access To User</div>
                <div class="panel-body">

                    @if( isset($errors) && count($errors) > 0 )
                        <div class="alert alert-danger" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="post" action="/access">
                        {{ csrf_field() }}

                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="user_id">Belong To</label>
                                  <select name="user_id" id="user_id" class="form-control">
                                      <option value="null"></option>
                                      @foreach( $user_array as $user )
                                          <option value="{{$user->id}}"> {{$user->name}} </option>
                                      @endforeach
                                  </select>
                              </div>
                              <div class="form-group">
                                <label for="expire_day">Expire Day</label>
                                <input type="text" class="form-control" name="expire_day" id="expire_day" placeholder="Expire Day(0 is no expire time)" value="{{ Request::old('email') }}">
                              </div>
                          </div>
                      </div>
                      <hr>
                      <button type="submit" class="btn btn-default">Create</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
