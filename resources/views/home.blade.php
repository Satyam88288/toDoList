<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">\
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        #loader {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        .delete-task{
            font-weight: 900;
        }
        .update-task{
            font-weight: 900;
            background-color: rgb(108, 223, 108);
            color:white;
        }
        .add-input{
            width: 25rem;
        }
        .text-align-center{
            text-align: -webkit-center !important;
        }
        .list-group{
            display: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    
    <div id="error-message" class="alert alert-danger" style="display: none;"></div>
    
    <div id="success-message" class="alert alert-success" style="display: none;"></div>
    
    <h1 class="mb-4 text-center">Simple To Do List</h1>
    <div class="add-input text-align-center w-100 my-3">
        <button type="" id="show-task" class="btn btn-primary show-task">Show Task</button>
        <button type="" id="show-all-task" class="btn btn-primary show-all-task">Show all Task</button>
    </div>
    <form id="task-form" class="text-align-center mb-4">
        @csrf
        <div class="input-group add-input">
            <input type="text" name="title" id="task-title" class="form-control" placeholder="Add a new task" required>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </div>
    </form>
    
    <div class="list-group" id="">
        <table class="table text-center">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Task</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody id="task-list">
            </tbody>
          </table>
    </div>
</div>

<!-- Loader -->
<div id="loader">
    <img src="https://i.gifer.com/ZZ5H.gif" alt="Loading..." width="100">
</div>

<script>
    $(document).ready(function() {
        $(document).on('keypress',function(e) {
            if(e.which == 13) {
                // console.log(e.which);
                alert('You pressed enter!');
                $.ajax({
                url: '{{route('tasks')}}',
                method: 'GET',
                data: {
                    _token: token,
                    title: title
                },
                success: function(response) {
                    $('#task-title').val('');
                    $('#error-message').hide();
                    $('#success-message').html('Task added successfully!').show();
                    $('#task-list').append(response);
                    $('#loader').hide();
                },
                error: function(response) {
                    $('#loader').hide();
                    let errors = response.responseJSON.errors;
                    let errorList = '';
                    $.each(errors, function(key, value) {
                        errorList += `<li>${value}</li>`;
                    });
                    $('#error-message').html(errorList).show();
                }
            });
            }
        });
        // Handle form submission with AJAX
        $('#task-form').submit(function(event) {
            // console.log('asxas');
            $('#success-message').hide();
            $('#error-message').hide();
            event.preventDefault();
            $('#loader').show();
            let title = $('#task-title').val();
            let token = $('input[name=_token]').val();

            $.ajax({
                url: '{{route('tasks-store')}}',
                method: 'POST',
                data: {
                    _token: token,
                    title: title
                },
                success: function(response) {
                    console.log(response);
                    addData(response.data);
                    updateSerialNumbers();

                    $('#error-message').hide();
                    $('#success-message').html('Task added successfully!').show();
                    $('#loader').hide();
                },
                error: function(response) {
                    $('#loader').hide();
                    let errors = response.responseJSON.errors;
                    let errorList = '';
                    $.each(errors, function(key, value) {
                        errorList += `<li>${value}</li>`;
                    });
                    $('#error-message').html(errorList).show();
                }
            });
        });

        // Handle task update (checkbox toggle) with AJAX
        $(document).on('click', '.task-checkbox', function() {
            $('#loader').show();
            let listItem = $(this).closest('tr');
            let taskId = listItem.data('id');
            let token = $('input[name=_token]').val();
            $('#success-message').hide();
            $('#error-message').hide();
            $.ajax({
                
                url: '{{route('tasks-update')}}',
                method: 'POST',
                data: {
                    _token: token,
                    taskId:taskId
                },
                success: function(response) {
                    listItem.remove();
                    $('#success-message').html('Task Updated successfully!').show();
                    updateSerialNumbers();
                    $('#loader').hide();
                }.bind(this)
            });
        });

        function updateSerialNumbers() {
            $('#task-list tr').each(function(index) {
                // console.log('ssss');
                // console.log($(this).find('.task-serial'));
                $(this).find('.task-serial').text((index+1));
            });
        }

        $(document).on('click', '.delete-task', function() {
            let listItem = $(this).closest('tr');
            let taskId = listItem.data('id');
            let token = $('input[name=_token]').val();
            $('#success-message').hide();
            $('#error-message').hide();
            if (confirm('Are you sure you want to delete this task?')) {
                $('#loader').show();
                $.ajax({
                    url: '{{route('tasks-delete')}}',
                    method: 'DELETE',
                    data: {
                        _token: token,
                        taskId:taskId
                    },
                    success: function(response) {
                        listItem.remove();
                        $('#success-message').html('Task deleted successfully!').show();
                        updateSerialNumbers();
                        $('#loader').hide();
                    }
                });
            }
        });
        $(document).on('click', '#show-all-task', function() {
            $('#loader').show();
            
            $.ajax({
                url: '{{route('all-task')}}',
                method: 'GET',
                success: function(response) {
                    tasks = response['data'];
                    $(".list-group").show();
                    loadData(tasks);
                        // console.log(response['data']);
                        $('#success-message').html('ALL task fetched successfully!').show();
                        $('#loader').hide();
                    }
                });
        });

        $(document).on('click', '#show-task', function() {
            $('#loader').show();
            
            $.ajax({
                url: '{{route('tasks')}}',
                method: 'GET',
                success: function(response) {
                    tasks = response['data'];
                    $(".list-group").show();
                        loadData(tasks);
                        // console.log(response['data']);
                        $('#success-message').html('Task fetched successfully!').show();
                        $('#loader').hide();
                    }
                });
        });

        function loadData(tasks){
            $('#task-list').empty();
            tasks.forEach(function(task, index) {
                // console.log(index);
                        var taskRow = `
                            <tr class="" data-id="${task.id}">
                                <th class="task-serial" scope="row">${index + 1}</th>
                                <td>
                                    <span class="task-title">
                                        ${task.title}
                                    </span>
                                </td>
                                <td>${task.is_completed ? 'Done' : ''}</td>
                                <td>
                                    <div>${!task.is_completed ?
                                        '<button class="btn btn-success btn-sm update-task task-checkbox" type="checkbox"> &#10003;</button>' : ''}
                                        <button class="btn btn-danger btn-sm delete-task"> &#x2715;</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $('#task-list').append(taskRow);
                    });
        }

        function addData(task){
            var taskRow = `
                <tr class="" data-id="${task.id}">
                    <th class="task-serial" scope="row">${0}</th>
                    <td>
                        <span class="task-title">
                            ${task.title}
                        </span>
                    </td>
                    <td>${task.is_completed ? 'Done' : ''}</td>
                    <td>
                        <div>${!task.is_completed ?
                            '<button class="btn btn-success btn-sm update-task task-checkbox" type="checkbox"> &#10003;</button>' : ''}
                            <button class="btn btn-danger btn-sm delete-task"> &#x2715;</button>
                        </div>
                    </td>
                </tr>
            `;
            $('#task-list').append(taskRow);
        }
    });
</script>
</body>
</html>
