<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap + jQuery -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <h2 class="text-center">Todo List</h2>
    <hr>

    <!-- Add Todo Button -->
    <button type="button" class="btn btn-info btn-lg" id="add_todo">
        Add Todo
    </button>

    <br><br>

    <!-- Todo Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Sr.no</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="list_todo">
        @foreach($todos as $todo)
            <tr id="list_todo_id{{$todo->id}}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $todo->name }}</td>
                <td>
                    <button class="btn btn-sm btn-warning edit_todo" data-id="{{ $todo->id }}">Edit</button>
                    <button class="btn btn-sm btn-danger delete_todo" data-id="{{ $todo->id }}">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="modal_todo" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal_title">Add Todo</h4>
                </div>

                <div class="modal-body">
                    <form id="form_todo">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        <div class="form-group">
                            <label for="name">Todo Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <button type="submit" class="btn btn-success">Save</button>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){

        // ✅ Setup CSRF token for AJAX
        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ✅ Helper function: refresh serial numbers
        function refreshSerialNumbers() {
            $("#list_todo tr").each(function(index){
                $(this).find("td:first").text(index + 1);
            });
        }

        // ✅ Open modal for adding new todo
        $("#add_todo").on('click', function () {
            $("#form_todo").trigger('reset');
            $("#modal_title").html('Add Todo');
            $("#id").val('');
            $("#modal_todo").modal('show');
        });

        // ✅ Save or Update Todo
        $("#form_todo").on('submit', function(e){
            e.preventDefault();

            var id = $("#id").val();
            var url = id ? "/api/todos/" + id : "/api/todos";
            var type = id ? "PUT" : "POST";

            $.ajax({
                url: url,
                type: type,
                data: { name: $("#name").val(), id: id },
                success: function(res){
                    if(id){
                        // Update existing row
                        $("#list_todo_id"+id+" td:nth-child(2)").html(res.name);
                    } else {
                        // Add new row at the end (FIFO order)
                        $("#list_todo").append(
                            "<tr id='list_todo_id"+res.id+"'>"+
                            "<td></td>"+ // will be updated by refreshSerialNumbers()
                            "<td>"+res.name+"</td>"+
                            "<td>"+
                            "<button class='btn btn-sm btn-warning edit_todo' data-id='"+res.id+"'>Edit</button> "+
                            "<button class='btn btn-sm btn-danger delete_todo' data-id='"+res.id+"'>Delete</button>"+
                            "</td>"+
                            "</tr>"
                        );
                    }
                    refreshSerialNumbers(); // ✅ update numbering
                    $("#modal_todo").modal('hide');
                }
            });
        });

        // ✅ Edit Todo
        $("body").on('click','.edit_todo',function(){
            var id = $(this).data('id');
            $.get("/api/todos/" + id, function(res){
                $("#modal_title").html('Edit Todo');
                $("#id").val(res.id);
                $("#name").val(res.name);
                $("#modal_todo").modal('show');
            });
        });

        // ✅ Delete Todo
        $("body").on('click','.delete_todo',function(){
            var id = $(this).data('id');
            if(confirm("Are you sure you want to delete this?")){
                $.ajax({
                    url: "/api/todos/" + id,
                    type: "DELETE",
                    success: function(){
                        $("#list_todo_id"+id).remove();
                        refreshSerialNumbers(); // ✅ update numbering
                    }
                });
            }
        });

    });
</script>

</body>
</html>

